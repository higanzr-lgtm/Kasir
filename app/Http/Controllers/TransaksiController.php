<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\MenuBundle;
// PERBAIKAN 1: Mengimpor Facade DB resmi Laravel dengan benar
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // Menampilkan halaman kasir utama
    public function index()
    {
        $produks = Produk::with('diskon')->get();
        $bundles = MenuBundle::with('items.produk')->where('aktif', true)->get();

        // Prepare bundles data for JavaScript
        $bundlesData = $bundles->map(function ($b) {
            return [
                'id' => $b->id,
                'nama_bundle' => $b->nama_bundle,
                'harga_bundle' => (float)$b->harga_bundle,
                'items' => $b->items->map(function ($item) {
                    $produk = $item->produk;
                    $hargaNet = $produk ? $produk->getHargaNet() : 0;
                    return [
                        'id_produk' => $item->id_produk,
                        'nama_produk' => $produk ? $produk->nama_produk : 'N/A',
                        'harga' => $hargaNet,
                        'qty' => $item->qty,
                    ];
                })
            ];
        });

        // Generate nomor pesanan otomatis berdasarkan transaksi hari ini
        $today = date('Y-m-d');
        $countToday = Transaksi::whereDate('created_at', $today)->count();
        $nomorPesananOtomatis = '#' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

        // Riwayat transaksi untuk kasir
        $riwayatTransaksi = Transaksi::with('detailTransaksis.produk', 'pembayaran')
            ->orderBy('created_at', 'DESC')
            ->take(20)
            ->get();

        // Serialize riwayat untuk JavaScript
        $riwayatTransaksiJson = $riwayatTransaksi->map(function ($rt) {
            return [
                'id_transaksi' => $rt->id_transaksi,
                'nomor_pesanan' => $rt->nomor_pesanan,
                'nama_pembeli' => $rt->nama_pembeli,
                'nomor_hp' => $rt->nomor_hp,
                'total_bayar' => (float)$rt->total_bayar,
                'status_pembayaran' => $rt->status_pembayaran,
                'created_at' => $rt->created_at ? $rt->created_at->toISOString() : null,
                'metode_bayar' => $rt->pembayaran ? $rt->pembayaran->metode_bayar : '-',
                'items' => $rt->detailTransaksis->map(function ($dt) {
                    return [
                        'nama_produk' => $dt->produk ? $dt->produk->nama_produk : 'N/A',
                        'qty' => $dt->qty,
                        'subtotal' => (float)$dt->subtotal,
                        'harga_satuan' => $dt->qty > 0 ? (float)$dt->subtotal / $dt->qty : 0,
                    ];
                })
            ];
        });

        return view('kasir.dashboard', compact('produks', 'bundles', 'bundlesData', 'nomorPesananOtomatis', 'riwayatTransaksi', 'riwayatTransaksiJson'));
    }

    // Memproses transaksi masuk dari kasir
    public function simpanTransaksi(Request $request)
    {
        // Validasi input data keranjang belanja
        $request->validate([
            'id_user' => 'required',
            'items' => 'required|array',
            'nama_pembeli' => 'nullable|string|max:100',
            'nomor_pesanan' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'detail_alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:20',
            'latitude' => 'nullable|string|max:30',
            'longitude' => 'nullable|string|max:30',
        ]);

        $id_transaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());

        DB::beginTransaction();
        try {
            $totalGrand = 0;

            // 1. Buat Induk Transaksi
            $transaksi = Transaksi::create([
                'id_transaksi' => $id_transaksi,
                'id_user' => $request->id_user,
                'nama_pembeli' => $request->nama_pembeli,
                'nomor_pesanan' => $request->nomor_pesanan,
                'alamat' => $request->alamat,
                'detail_alamat' => $request->detail_alamat,
                'nomor_hp' => $request->nomor_hp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'total_bayar' => 0,
                'status_pembayaran' => 'Belum Bayar'
            ]);

            // 2. Loop semua item belanjaan
            foreach ($request->items as $item) {
                $produk = Produk::with('diskon')->find($item['id_produk']);

                if (!$produk || $produk->stok < $item['qty']) {
                    return response()->json(['status' => 'error', 'message' => 'Stok produk tidak cukup!']);
                }

                $hargaNormal = $produk->getHargaNet();
                $subtotal = $hargaNormal * $item['qty'];

                // Hitung diskon per item
                $diskonPersenItem = (float) ($item['diskon_persen'] ?? 0);
                $diskonNominalItem = 0;
                $subtotalSetelahDiskon = $subtotal;

                if ($diskonPersenItem > 0) {
                    $diskonNominalItem = $subtotal * ($diskonPersenItem / 100);
                    $subtotalSetelahDiskon = $subtotal - $diskonNominalItem;
                }

                $totalGrand += $subtotalSetelahDiskon;

                // Simpan ke tabel detail_transaksi
                DetailTransaksi::create([
                    'id_transaksi' => $id_transaksi,
                    'id_produk' => $produk->id_produk,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                    'diskon_persen' => $diskonPersenItem,
                    'diskon_nominal' => $diskonNominalItem,
                    'subtotal_setelah_diskon' => $subtotalSetelahDiskon,
                ]);

                $produk->stok = $produk->stok - $item['qty'];
                $produk->save();
            }

            // 3. Hitung total diskon keseluruhan
            $totalDiskonNominal = $totalGrand > 0 ? 0 : 0;
            $totalSebelumDiskon = 0;
            foreach ($request->items as $item) {
                $hargaNormal = 0;
                $produkCek = Produk::find($item['id_produk']);
                if ($produkCek) {
                    $hargaNormal = $produkCek->getHargaNet() * $item['qty'];
                    $totalSebelumDiskon += $hargaNormal;
                }
            }

            // 4. Update total bayar
            $transaksi->update([
                'total_bayar' => $totalGrand,
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan!',
                'id_transaksi' => $id_transaksi,
                'total_tagihan' => $totalGrand
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\MenuBundle;

class CustomerController extends Controller
{
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

        // Generate nomor pesanan otomatis
        $today = date('Y-m-d');
        $countToday = Transaksi::whereDate('created_at', $today)->count();
        $nomorPesananOtomatis = '#' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

        // Ambil riwayat pesanan customer yang login
        $idUser = session('id_user');
        $riwayatPesanan = Transaksi::with('detailTransaksis.produk')
            ->where('id_user', $idUser)
            ->whereNotNull('alamat')
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();

        // Serialize riwayat pesanan untuk JavaScript (detail popup)
        $riwayatJson = $riwayatPesanan->load('pembayaran')->map(function ($rp) {
            return [
                'id_transaksi' => $rp->id_transaksi,
                'nomor_pesanan' => $rp->nomor_pesanan,
                'nama_pembeli' => $rp->nama_pembeli,
                'nomor_hp' => $rp->nomor_hp,
                'total_bayar' => (float)$rp->total_bayar,
                'alamat' => $rp->alamat,
                'detail_alamat' => $rp->detail_alamat,
                'latitude' => $rp->latitude,
                'longitude' => $rp->longitude,
                'status_pengiriman' => $rp->status_pengiriman,
                'status_pembayaran' => $rp->status_pembayaran,
                'kurir_latitude' => $rp->kurir_latitude,
                'kurir_longitude' => $rp->kurir_longitude,
                'kurir_updated_at' => $rp->kurir_updated_at,
                'created_at' => $rp->created_at ? $rp->created_at->toISOString() : null,
                'metode_bayar' => $rp->pembayaran ? $rp->pembayaran->metode_bayar : '-',
                'items' => $rp->detailTransaksis->map(function ($dt) {
                    return [
                        'nama_produk' => $dt->produk ? $dt->produk->nama_produk : 'N/A',
                        'qty' => $dt->qty,
                        'subtotal' => (float)$dt->subtotal,
                        'harga_satuan' => $dt->qty > 0 ? (float)$dt->subtotal / $dt->qty : 0,
                    ];
                })
            ];
        });

        return view('customer.dashboard', compact('produks', 'bundles', 'bundlesData', 'nomorPesananOtomatis', 'riwayatPesanan', 'riwayatJson'));
    }
}
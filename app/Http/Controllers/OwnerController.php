<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Diskon;
use App\Models\Transaksi;
use App\Models\UserKaryawan;
use App\Models\MenuBundle;
use App\Models\MenuBundleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    public function index()
    {
        $produks = Produk::with('diskon')->get();
        $diskons = Diskon::all();
        $semuaUser = UserKaryawan::orderBy('id_user', 'ASC')->get();
        $semuaBundle = MenuBundle::with('items.produk')->orderBy('created_at', 'DESC')->get();

        $omsetHarian = Transaksi::select(
            DB::raw("DATE_FORMAT(created_at, '%d %b') as tanggal"),
            DB::raw("SUM(total_bayar) as total")
        )
            ->groupBy('tanggal')
            ->orderBy(DB::raw("MIN(created_at)"), 'ASC')
            ->take(7)
            ->get();

        $grafikLabel = $omsetHarian->pluck('tanggal')->toArray();
        $grafikData = $omsetHarian->pluck('total')->toArray();

        $totalOmsetSistem = Transaksi::sum('total_bayar');
        $totalTransaksiSistem = Transaksi::count();

        $semuaTransaksi = Transaksi::with('pembayaran', 'detailTransaksis.produk')
            ->orderBy('created_at', 'DESC')
            ->get();

        $pesananCustomer = Transaksi::with('pembayaran', 'detailTransaksis.produk')
            ->where('status_pembayaran', 'Lunas')
            ->whereNotNull('alamat')
            ->whereIn('status_pengiriman', ['menunggu', 'dikirim'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('owner.dashboard', compact(
            'produks',
            'diskons',
            'semuaUser',
            'semuaBundle',
            'grafikLabel',
            'grafikData',
            'totalOmsetSistem',
            'totalTransaksiSistem',
            'semuaTransaksi',
            'pesananCustomer'
        ));
    }

    public function konfirmasiSampai($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return back()->with('error', 'Transaksi tidak ditemukan!');
        }
        $transaksi->update(['status_pengiriman' => 'sudah_sampai']);
        return back()->with('success', 'Pesanan ' . $transaksi->nomor_pesanan . ' dikonfirmasi sampai!');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Owner,Kasir,Customer,Kurir',
        ]);

        $user = UserKaryawan::find($id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan!');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user ' . $user->nama . ' berhasil diubah menjadi ' . $request->role);
    }

    public function getTransaksi($id)
    {
        $transaksi = Transaksi::with('detailTransaksis.produk', 'pembayaran')->find($id);

        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaksi
        ]);
    }

    // Fungsi Tambah Produk dengan perbaikan path
    public function tambahProduk(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|unique:produks',
            'nama_produk' => 'required',
            'harga_normal' => 'required|numeric',
            'stok' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();

            // Simpan ke storage/app/public/menu (bisa diakses via /storage/menu/)
            $file->storeAs('public/menu', $namaFile);

            // Juga simpan ke public/images/menu (backup untuk akses langsung)
            $file->move(public_path('images/menu'), $namaFile);

            $data['foto'] = $namaFile;
        }

        try {
            Produk::create($data);
            return back()->with('success', 'Produk berhasil ditambah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function hapusProduk($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return back()->with('error', 'Produk tidak ditemukan!');
        }

        $dipakai = \App\Models\DetailTransaksi::where('id_produk', $id)->count();
        if ($dipakai > 0) {
            return back()->with('error', 'Produk ' . $produk->nama_produk . ' tidak bisa dihapus karena sudah digunakan di ' . $dipakai . ' transaksi. Nonaktifkan saja stok menjadi 0.');
        }

        // Hapus file foto jika ada
        if ($produk->foto) {
            $publicPath = public_path('images/menu/' . $produk->foto);
            $storagePath = storage_path('app/public/menu/' . $produk->foto);
            if (File::exists($publicPath)) File::delete($publicPath);
            if (File::exists($storagePath)) File::delete($storagePath);
        }

        $produk->delete();
        return back()->with('success', 'Produk ' . $produk->nama_produk . ' berhasil dihapus!');
    }

    public function updateBarang(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga_normal' => 'required|numeric|min:0',
            'stok' => 'required|numeric|min:0',
            'id_diskon' => 'nullable',
        ]);

        $produk = Produk::find($id);
        if (!$produk) {
            return back()->with('error', 'Produk tidak ditemukan!');
        }

        $data = [
            'nama_produk' => $request->nama_produk,
            'harga_normal' => $request->harga_normal,
            'stok' => $request->stok,
            'id_diskon' => $request->id_diskon ?: null,
        ];

        if ($request->hasFile('foto')) {
            $request->validate([
                'foto' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Hapus foto lama
            if ($produk->foto) {
                $publicPath = public_path('images/menu/' . $produk->foto);
                $storagePath = storage_path('app/public/menu/' . $produk->foto);
                if (File::exists($publicPath)) File::delete($publicPath);
                if (File::exists($storagePath)) File::delete($storagePath);
            }

            $file = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/menu', $namaFile);
            $file->move(public_path('images/menu'), $namaFile);
            $data['foto'] = $namaFile;
        }

        $produk->update($data);
        return back()->with('success', 'Barang ' . $produk->nama_produk . ' berhasil diperbarui!');
    }

    public function tambahBundle(Request $request)
    {
        $request->validate([
            'nama_bundle' => 'required',
            'harga_bundle' => 'required|numeric|min:0',
            'deskripsi' => 'nullable',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        try {
            $data = $request->only('nama_bundle', 'harga_bundle', 'deskripsi');
            $data['aktif'] = $request->has('aktif') ? true : false;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $namaFile = time() . '_bundle_' . $file->getClientOriginalName();
                $file->storeAs('public/menu', $namaFile);
                $file->move(public_path('images/menu'), $namaFile);
                $data['foto'] = $namaFile;
            }

            $bundle = MenuBundle::create($data);

            foreach ($request->items as $item) {
                MenuBundleItem::create([
                    'bundle_id' => $bundle->id,
                    'id_produk' => $item['id_produk'],
                    'qty' => $item['qty'],
                ]);
            }

            return back()->with('success', 'Bundle ' . $request->nama_bundle . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah bundle: ' . $e->getMessage());
        }
    }

    public function hapusBundle($id)
    {
        $bundle = MenuBundle::find($id);
        if (!$bundle) {
            return back()->with('error', 'Bundle tidak ditemukan!');
        }

        if ($bundle->foto) {
            $publicPath = public_path('images/menu/' . $bundle->foto);
            $storagePath = storage_path('app/public/menu/' . $bundle->foto);
            if (File::exists($publicPath)) File::delete($publicPath);
            if (File::exists($storagePath)) File::delete($storagePath);
        }

        $bundle->delete();
        return back()->with('success', 'Bundle ' . $bundle->nama_bundle . ' berhasil dihapus!');
    }

    public function toggleBundle($id)
    {
        $bundle = MenuBundle::find($id);
        if (!$bundle) {
            return back()->with('error', 'Bundle tidak ditemukan!');
        }

        $bundle->update(['aktif' => !$bundle->aktif]);
        $status = $bundle->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', 'Bundle ' . $bundle->nama_bundle . ' berhasil ' . $status . '!');
    }

    public function tambahPromo(Request $request)
    {
        $request->validate([
            'id_diskon' => 'required|unique:diskons',
            'nama_diskon' => 'required',
            'tipe_diskon' => 'required|in:Persen,Nominal',
            'nilai' => 'required|numeric|min:1',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        try {
            Diskon::create($request->all());
            return back()->with('success', 'Promo ' . $request->nama_diskon . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah promo: ' . $e->getMessage());
        }
    }
}
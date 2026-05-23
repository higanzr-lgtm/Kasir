<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Diskon;
use App\Models\Transaksi;
use App\Models\UserKaryawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index()
    {
        $produks = Produk::with('diskon')->get();
        $diskons = Diskon::all();
        $semuaUser = UserKaryawan::orderBy('id_user', 'ASC')->get();

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

        // Ambil semua transaksi untuk tabel data transaksi
        $semuaTransaksi = Transaksi::with('pembayaran', 'detailTransaksis.produk')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('owner.dashboard', compact(
            'produks',
            'diskons',
            'semuaUser',
            'grafikLabel',
            'grafikData',
            'totalOmsetSistem',
            'totalTransaksiSistem',
            'semuaTransaksi'
        ));
    }

    // Update role user oleh Owner
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Owner,Kasir,Customer',
        ]);

        $user = UserKaryawan::find($id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan!');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user ' . $user->nama . ' berhasil diubah menjadi ' . $request->role);
    }

    // API: Ambil data transaksi lengkap by ID (untuk cetak struk)
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

    // Fungsi Tambah Produk (Gunakan versi yang lengkap ini)
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
            $file->move(public_path('images/menu'), $namaFile);
            $data['foto'] = $namaFile;
        }
        try {
            Produk::create($data);
            return back()->with('success', 'Produk berhasil ditambah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Database Error: ' . $e->getMessage());
        }

        Produk::create($data);
        return back()->with('success', 'Produk berhasil ditambah!');
    }
}
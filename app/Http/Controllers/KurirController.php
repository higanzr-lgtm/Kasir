<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class KurirController extends Controller
{
    public function index()
    {
        // Ambil order yang sudah dibayar dan belum "sudah_sampai"
        $pesanan = Transaksi::with('pembayaran', 'detailTransaksis.produk')
            ->where('status_pembayaran', 'Lunas')
            ->whereIn('status_pengiriman', ['menunggu', 'dikirim'])
            ->whereNotNull('alamat')
            ->whereNotNull('nomor_hp')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('kurir.dashboard', compact('pesanan'));
    }

    // Ambil pesanan untuk diantar
    public function kirim($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }
        if ($transaksi->status_pengiriman !== 'menunggu') {
            return response()->json(['status' => 'error', 'message' => 'Status pesanan tidak valid!']);
        }

        $transaksi->update(['status_pengiriman' => 'dikirim']);
        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan ' . $transaksi->nomor_pesanan . ' sedang dalam pengiriman!'
        ]);
    }

    // Update lokasi kurir secara realtime
    public function updateLokasi(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $transaksi->update([
            'kurir_latitude' => $request->lat,
            'kurir_longitude' => $request->lng,
            'kurir_updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi kurir diperbarui!'
        ]);
    }

    // Konfirmasi pesanan sudah diantar
    public function konfirmasiSampai($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }

        $transaksi->update(['status_pengiriman' => 'sudah_sampai']);
        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan ' . $transaksi->nomor_pesanan . ' telah dikonfirmasi sampai!'
        ]);
    }
}
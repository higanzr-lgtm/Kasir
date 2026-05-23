<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function prosesPembayaran(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|string|exists:transaksis,id_transaksi',
            'metode_bayar' => 'required|string',
            'nominal_bayar' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::findOrFail($request->id_transaksi);

            if ($transaksi->status_pembayaran === 'Lunas') {
                return response()->json(['status' => 'error', 'message' => 'Transaksi ini sudah lunas.'], 400);
            }

            $kembalian = 0;
            if ($request->metode_bayar === 'Tunai') {
                if ($request->nominal_bayar < $transaksi->total_bayar) {
                    return response()->json(['status' => 'error', 'message' => 'Nominal pembayaran kurang.'], 400);
                }
                $kembalian = $request->nominal_bayar - $transaksi->total_bayar;
            }

            Pembayaran::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'metode_bayar' => $request->metode_bayar,
                'nominal_bayar' => $request->nominal_bayar,
                'kembalian' => $kembalian,
            ]);

            $transaksi->update(['status_pembayaran' => 'Lunas']);

            DB::commit();

            $transaksi->load('detailTransaksis.produk', 'pembayaran');

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil diproses!',
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }
}
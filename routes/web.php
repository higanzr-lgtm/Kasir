<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\AuthController;

// ========== HALAMAN PUBLIK (Tanpa Login) ==========
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Jalur Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/proses', [AuthController::class, 'login'])->name('login.proses');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register/proses', [AuthController::class, 'register'])->name('register.proses');
Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp/verifikasi', [AuthController::class, 'verifikasiOtp'])->name('otp.verifikasi');
Route::get('/lupa-password', [AuthController::class, 'showLupaPassword'])->name('lupa.password');
Route::post('/lupa-password/kirim', [AuthController::class, 'kirimOtpLupaPassword'])->name('lupa.password.kirim');
Route::get('/lupa-password/verifikasi', [AuthController::class, 'showVerifikasiLupaPassword'])->name('lupa.password.verifikasi');
Route::post('/lupa-password/verifikasi-otp', [AuthController::class, 'verifikasiOtpLupaPassword'])->name('lupa.password.verifikasi.otp');
Route::get('/lupa-password/reset', [AuthController::class, 'showResetPassword'])->name('lupa.password.reset');
Route::post('/lupa-password/reset/proses', [AuthController::class, 'resetPassword'])->name('lupa.password.reset.proses');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ========== HALAMAN KHUSUS LOGIN (Session isLogin) ==========
Route::middleware(['auth.kasir'])->group(function () {
    Route::get('/kasir', [TransaksiController::class, 'index'])->name('kasir.dashboard');
    Route::post('/transaksi/simpan', [TransaksiController::class, 'simpanTransaksi'])->name('transaksi.simpan');
    Route::post('/pembayaran/proses', [PembayaranController::class, 'prosesPembayaran'])->name('pembayaran.proses');
});

Route::middleware(['auth.customer'])->group(function () {
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.dashboard');
    Route::post('/customer/transaksi/simpan', [TransaksiController::class, 'simpanTransaksi'])->name('customer.transaksi.simpan');
    Route::post('/customer/pembayaran/proses', [PembayaranController::class, 'prosesPembayaran'])->name('customer.pembayaran.proses');
});

// Halaman Owner (hanya role Owner)
Route::middleware(['auth.owner'])->group(function () {
    Route::get('/owner', [OwnerController::class, 'index'])->name('owner.dashboard');
    Route::post('/owner/produk/tambah', [OwnerController::class, 'tambahProduk'])->name('owner.produk.tambah');
    Route::delete('/owner/produk/hapus/{id}', [OwnerController::class, 'hapusProduk'])->name('owner.produk.hapus');
    Route::get('/owner/transaksi/{id}', [OwnerController::class, 'getTransaksi'])->name('owner.transaksi.get');
    Route::put('/owner/user/update-role/{id}', [OwnerController::class, 'updateRole'])->name('owner.user.update-role');
});
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserKaryawan;
use App\Models\Diskon;
use App\Models\Produk;
use Illuminate\Support\Facades\Hash;

class DatabaseKasirSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data User Karyawan (Kasir & Owner)
        UserKaryawan::create([
            'id_user' => 1,
            'nama' => 'Higan',
            'email' => 'owner@kopikita.com',
            'role' => 'Owner',
            'username' => 'higan',
            'password' => Hash::make('123'),
        ]);

        UserKaryawan::create([
            'id_user' => 2,
            'nama' => 'Hilal',
            'email' => 'kasir@kopikita.com',
            'role' => 'Kasir',
            'username' => 'hilal',
            'password' => Hash::make('123'),
        ]);

        // Akun demo Customer
        UserKaryawan::create([
            'id_user' => 3,
            'nama' => 'Budi Customer',
            'email' => 'customer@kopikita.com',
            'role' => 'Customer',
            'username' => 'budi',
            'password' => Hash::make('123'),
        ]);

        // Akun Head Admin (bisa akses Owner panel & kelola user)
        UserKaryawan::create([
            'id_user' => 4,
            'nama' => 'Admin Utama',
            'email' => 'admin@kopikita.com',
            'role' => 'Owner',
            'username' => 'admin',
            'password' => Hash::make('123'),
        ]);

        // 2. Buat Data Master Diskon (Diskon berlaku dari hari ini sampai bulan depan)
        Diskon::create([
            'id_diskon' => 'DISC10',
            'nama_diskon' => 'Promo Kopi Hemat 10%',
            'tipe_diskon' => 'Persen',
            'nilai' => 10,
            'tgl_mulai' => date('Y-m-d'),
            'tgl_selesai' => date('Y-m-d', strtotime('+1 month')),
        ]);

        // 3. Buat Data Produk Jualan
        Produk::create([
            'id_produk' => 'P001',
            'id_diskon' => 'DISC10', // Terhubung ke diskon
            'nama_produk' => 'Kopi Susu Gula Aren',
            'harga_normal' => 18000,
            'stok' => 50,
        ]);

        Produk::create([
            'id_produk' => 'P002',
            'id_diskon' => null, // Harga normal tanpa diskon
            'nama_produk' => 'Roti Bakar Cokelat',
            'harga_normal' => 15000,
            'stok' => 30,
        ]);

        Produk::create([
            'id_produk' => 'P003',
            'id_diskon' => null,
            'nama_produk' => 'Air Mineral 600ml',
            'harga_normal' => 5000,
            'stok' => 100,
        ]);
    }
}
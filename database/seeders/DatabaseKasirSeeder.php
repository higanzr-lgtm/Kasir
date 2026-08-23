<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserKaryawan;
use App\Models\Diskon;
use App\Models\Produk;
use App\Models\MenuBundle;
use App\Models\MenuBundleItem;
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

        // 2. Buat Data Master Diskon
        Diskon::create([
            'id_diskon' => 'DISC10',
            'nama_diskon' => 'Promo Kopi Hemat 10%',
            'tipe_diskon' => 'Persen',
            'nilai' => 10,
            'tgl_mulai' => date('Y-m-d'),
            'tgl_selesai' => date('Y-m-d', strtotime('+1 month')),
        ]);

        Diskon::create([
            'id_diskon' => 'DISC15',
            'nama_diskon' => 'Diskon Makanan 15%',
            'tipe_diskon' => 'Persen',
            'nilai' => 15,
            'tgl_mulai' => date('Y-m-d'),
            'tgl_selesai' => date('Y-m-d', strtotime('+1 month')),
        ]);

        Diskon::create([
            'id_diskon' => 'DISC5',
            'nama_diskon' => 'Promo Minuman Segar 5%',
            'tipe_diskon' => 'Persen',
            'nilai' => 5,
            'tgl_mulai' => date('Y-m-d'),
            'tgl_selesai' => date('Y-m-d', strtotime('+1 month')),
        ]);

        // 3. Buat Data Produk Jualan (20 Menu) dengan foto
        $produks = [
            ['P001', 'DISC10', 'Kopi Susu Gula Aren', 18000, 50],
            ['P002', 'DISC15', 'Roti Bakar Cokelat', 15000, 30],
            ['P003', null, 'Air Mineral 600ml', 5000, 100],
            ['P004', 'DISC15', 'Nasi Goreng Spesial', 25000, 25],
            ['P005', null, 'Mie Ayam Bakso', 20000, 30],
            ['P006', 'DISC5', 'Es Teh Manis', 7000, 80],
            ['P007', 'DISC5', 'Jus Alpukat', 12000, 40],
            ['P008', 'DISC15', 'Kentang Goreng', 15000, 35],
            ['P009', 'DISC10', 'Pisang Goreng', 10000, 40],
            ['P010', 'DISC10', 'Cappuccino', 20000, 30],
            ['P011', null, 'Matcha Latte', 22000, 25],
            ['P012', 'DISC15', 'Croissant', 18000, 20],
            ['P013', 'DISC5', 'Salad Buah', 15000, 25],
            ['P014', null, 'French Fries', 18000, 35],
            ['P015', 'DISC15', 'Sosis Bakar', 12000, 40],
            ['P016', 'DISC10', 'Donat Glazed', 8000, 50],
            ['P017', null, 'Milkshake Strawberry', 25000, 20],
            ['P018', 'DISC10', 'Teh Tarik', 12000, 30],
            ['P019', null, 'Mochi Ice Cream', 15000, 25],
            ['P020', null, 'Espresso Doppio', 25000, 20],
        ];

        foreach ($produks as [$id, $diskon, $nama, $harga, $stok]) {
            $foto = strtolower(str_replace(' ', '_', $nama)) . '.jpg';
            Produk::create([
                'id_produk' => $id,
                'id_diskon' => $diskon,
                'nama_produk' => $nama,
                'harga_normal' => $harga,
                'stok' => $stok,
                'foto' => $foto,
            ]);
        }

        // 4. Buat Bundle / Paket Menu
        $bundle1 = MenuBundle::create([
            'nama_bundle' => 'Paket Kopi Nikmat',
            'harga_bundle' => 25000,
            'deskripsi' => '1 Kopi Susu Gula Aren + 1 Roti Bakar Cokelat',
            'aktif' => true,
        ]);
        MenuBundleItem::create(['bundle_id' => $bundle1->id, 'id_produk' => 'P001', 'qty' => 1]);
        MenuBundleItem::create(['bundle_id' => $bundle1->id, 'id_produk' => 'P002', 'qty' => 1]);

        $bundle2 = MenuBundle::create([
            'nama_bundle' => 'Paket Sarapan',
            'harga_bundle' => 30000,
            'deskripsi' => '1 Nasi Goreng + 1 Es Teh Manis',
            'aktif' => true,
        ]);
        MenuBundleItem::create(['bundle_id' => $bundle2->id, 'id_produk' => 'P004', 'qty' => 1]);
        MenuBundleItem::create(['bundle_id' => $bundle2->id, 'id_produk' => 'P006', 'qty' => 1]);

        $bundle3 = MenuBundle::create([
            'nama_bundle' => 'Paket Ngemil',
            'harga_bundle' => 22000,
            'deskripsi' => '1 Kentang Goreng + 1 Pisang Goreng',
            'aktif' => true,
        ]);
        MenuBundleItem::create(['bundle_id' => $bundle3->id, 'id_produk' => 'P008', 'qty' => 1]);
        MenuBundleItem::create(['bundle_id' => $bundle3->id, 'id_produk' => 'P009', 'qty' => 1]);
    }
}
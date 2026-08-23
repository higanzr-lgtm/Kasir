<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_transaksi', 'id_user', 'nama_pembeli', 'nomor_pesanan', 'total_bayar', 'status_pembayaran', 'diskon_persen', 'diskon_nominal', 'total_setelah_diskon', 'alamat', 'nomor_hp', 'status_pengiriman', 'detail_alamat', 'latitude', 'longitude', 'kurir_latitude', 'kurir_longitude', 'kurir_updated_at'];

    // Relasi ke detail item belanja (One-to-Many)
    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // Relasi ke data pembayaran nota (One-to-One)
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksis';
    protected $primaryKey = 'id_detail';

    protected $fillable = ['id_transaksi', 'id_produk', 'qty', 'subtotal', 'diskon_persen', 'diskon_nominal', 'subtotal_setelah_diskon'];

    // Relasi ke data produk induknya
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
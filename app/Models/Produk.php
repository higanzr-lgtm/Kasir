<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'id_produk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_produk', 'id_diskon', 'nama_produk', 'harga_normal', 'stok', 'foto'];

    // Relasi Class Diagram: Produk memiliki satu diskon (Many-to-One)
    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon', 'id_diskon');
    }

    // Fungsi Logika Bisnis: Mengambil harga bersih setelah dipotong diskon aktif
    public function getHargaNet()
    {
        // Cek apakah produk punya diskon dan diskonnya masih dalam masa berlaku (valid)
        if ($this->diskon && Carbon::now()->between($this->diskon->tgl_mulai, $this->diskon->tgl_selesai)) {
            $potongan = $this->diskon->hitungPotongan($this->harga_normal);
            return $this->harga_normal - $potongan;
        }
        return $this->harga_normal;
    }
}
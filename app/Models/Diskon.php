<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = 'diskons';
    protected $primaryKey = 'id_diskon';
    public $incrementing = false; // Karena ID berupa String (contoh: 'DISC10')
    protected $keyType = 'string';

    protected $fillable = ['id_diskon', 'nama_diskon', 'tipe_diskon', 'nilai', 'tgl_mulai', 'tgl_selesai'];

    // Fungsi Logika Bisnis: Menghitung nilai potongan harga
    public function hitungPotongan($hargaNormal)
    {
        if ($this->tipe_diskon === 'Persen') {
            return $hargaNormal * ($this->nilai / 100);
        }
        return $this->nilai; // Jika tipe nominal langsung kembalikan angkanya
    }
}
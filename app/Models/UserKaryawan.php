<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKaryawan extends Model
{

    protected $table = 'user_karyawans';
    protected $primaryKey = 'id_user';

    // Atribut yang boleh diisi (Public)
    protected $fillable = ['nama', 'email', 'role', 'username', 'password', 'email_verified_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Enkapsulasi: Menyembunyikan password agar bersifat Private saat data ditarik
    protected $hidden = ['password'];

    // Relasi Class Diagram: One-to-Many (Satu user bisa memproses banyak transaksi)
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_user', 'id_user');
    }
}
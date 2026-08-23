<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBundleItem extends Model
{
    protected $table = 'menu_bundle_items';

    protected $fillable = [
        'bundle_id',
        'id_produk',
        'qty',
    ];

    public function bundle()
    {
        return $this->belongsTo(MenuBundle::class, 'bundle_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
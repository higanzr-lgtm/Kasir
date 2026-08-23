<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBundle extends Model
{
    protected $table = 'menu_bundles';

    protected $fillable = [
        'nama_bundle',
        'harga_bundle',
        'deskripsi',
        'foto',
        'aktif',
    ];

    public function items()
    {
        return $this->hasMany(MenuBundleItem::class, 'bundle_id');
    }

    public function getTotalHargaNormalAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->produk) {
                $total += $item->produk->harga_normal * $item->qty;
            }
        }
        return $total;
    }

    public function getHematAttribute()
    {
        $normal = $this->total_harga_normal;
        $bundle = $this->harga_bundle;
        return $normal - $bundle;
    }
}
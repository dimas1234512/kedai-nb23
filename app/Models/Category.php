<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Izinkan semua kolom diisi
    protected $guarded = [];

    // Satu kategori bisa punya banyak produk
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
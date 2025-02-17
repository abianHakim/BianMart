<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    use HasFactory;

    protected $table = 'stok_barang';

    protected $fillable = [
        'produk_id',
        'stok_gudang',
        'stok_toko'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

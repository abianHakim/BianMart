<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'harga_beli',
        'harga_jual',
        'deskripsi',
        'satuan',
        'gambar',
    ];



    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function stokBarang()
    {
        return $this->hasOne(StokBarang::class);
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaanBarang::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}

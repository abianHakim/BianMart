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
        'supplier_id',
        'harga_beli',
        'persentase_keuntungan',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaanBarang::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function getHargaJualAttribute()
    {
        return $this->harga_beli + ($this->harga_beli * ($this->persentase_keuntungan / 100));
    }
}

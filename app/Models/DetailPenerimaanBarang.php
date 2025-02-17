<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenerimaanBarang extends Model
{
    use HasFactory;

    protected $table = 'detail_penerimaan_barang';

    protected $fillable = [
        'penerimaan_id',
        'produk_id',
        'jumlah',
        'harga_beli',
        'sub_total'
    ];

    public function penerimaanBarang()
    {
        return $this->belongsTo(PenerimaanBarang::class, 'penerimaan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

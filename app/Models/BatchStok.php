<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchStok extends Model
{
    use HasFactory;
    protected $table = 'batch_stok';

    protected $fillable = [
        'produk_id',
        'kode_batch',
        'expired_date',
        'stok_gudang',
        'stok_toko'
    ];


    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

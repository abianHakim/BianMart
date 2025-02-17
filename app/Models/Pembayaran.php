<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'penjualan_id',
        'jumlah_bayar',
        'metode_pembayaran'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}

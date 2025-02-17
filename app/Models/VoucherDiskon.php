<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherDiskon extends Model
{
    use HasFactory;

    protected $table = 'voucher_diskon';

    protected $fillable = [
        'kode_voucher',
        'jenis',
        'nilai',
        'min_belanja',
        'berlaku_hingga'
    ];
}

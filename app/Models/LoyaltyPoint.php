<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $table = 'loyalty_points';

    protected $fillable = [
        'member_id',
        'penjualan_id',
        'point_didapat'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}

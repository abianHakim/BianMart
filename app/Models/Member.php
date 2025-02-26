<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';

    protected $fillable = [
        'no_telp',
        'nama',
        'alamat',
        'email',
        'loyalty_points',
        'tgl_bergabung'
    ];
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }
}

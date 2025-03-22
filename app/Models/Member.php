<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Member extends Authenticatable
{
    use HasFactory;

    protected $table = 'member';

    protected $fillable = [
        'nama',
        'no_telp',
        'email',
        'alamat',
        'password',
        'loyalty_points',
        'tgl_bergabung',
    ];


    protected $hidden = [
        'password',

    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'member_id');
    }
}

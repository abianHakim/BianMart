<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenKerja extends Model
{

    use HasFactory;
    protected $table = "tbl_absen_kerja";

    protected $fillable = [
        'user_id',
        'tanggal_masuk',
        'waktu_masuk',
        'status_masuk',
        'waktu_selesai_kerja'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
        'waktu_selesai_kerja' => 'datetime:H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

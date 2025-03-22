<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $fillable = ['member_id', 'nama_barang', 'tanggal_pengajuan', 'qty', 'terpenuhi'];

    protected $casts = [
        'terpenuhi' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

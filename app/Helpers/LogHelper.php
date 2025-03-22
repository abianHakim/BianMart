<?php

namespace App\Helpers;

// use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function create($aktivitas, $deskripsi)
    {
        $userId = null;
        $memberId = null;

        if (Auth::guard('web')->check()) {
            $userId = Auth::id(); // Admin/Kasir
        } elseif (Auth::guard('member')->check()) {
            $memberId = Auth::guard('member')->id(); // Member
        }

        // Simpan log ke database
        \App\Models\Log::create([
            'user_id' => $userId,
            'member_id' => $memberId,
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'tanggal_aktivitas' => now(),
        ]);
    }
}

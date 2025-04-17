<?php

namespace App\Http\Controllers;

// use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Menampilkan daftar log terbaru dengan paginasi.
     *
     * Fungsi ini mengambil log terbaru dari model `Log`, kemudian melakukan paginasi
     * untuk menampilkan 10 entri per halaman. Daftar log ini kemudian diteruskan ke 
     * tampilan `admin.logs.logging` untuk ditampilkan kepada pengguna.
     *
     * @return \Illuminate\View\View Tampilan halaman log dengan daftar log yang dipaginasi
     */
    public function index()
    {
        $logs = \App\Models\Log::latest()->paginate(10);
        return view('admin.logs.logging', compact('logs'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('admin.transaksi.kasir');
    }

    public function riwayat()
    {
        return view('admin.transaksi.riwayat');
    }
}

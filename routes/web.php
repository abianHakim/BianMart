<?php

use App\Http\Controllers\AbsensiKerjaController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\authController;
use App\Http\Controllers\barangDisplayController;
use App\Http\Controllers\BatchStokController;
use App\Http\Controllers\ExportPengajuanController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\memberController;
use App\Http\Controllers\penerimaanBarangController;
use App\Http\Controllers\produkController;
use App\Http\Controllers\stockBarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\PengajuanController;


Route::get('/', function () {
    return view('login');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('cek-login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('member/login', [MemberAuthController::class, 'showLoginForm'])->name('member.login');
Route::post('member/login', [MemberAuthController::class, 'login'])->name('member.login.submit');
Route::post('member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');


Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        switch (Auth::user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kasir':
                return redirect()->route('transaksi.index');
            default:
                return view('dashboard');
        }
    } elseif (Auth::guard('member')->check()) {
        return redirect()->route('pengajuan.index');
    }

    return redirect()->route('login');
})->middleware(['auth:web,member', 'verified'])->name('dashboard');



Route::middleware(['auth', 'role:admin'])->group(function () {
    //admin dashboard
    Route::get('adminhome', [AdminHomeController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/live-transactions', [AdminHomeController::class, 'liveTransactions']);

    // kategori
    Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
    Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    //produk
    Route::get('produk', [produkController::class, 'index'])->name('produk.index');
    Route::post('produk', [produkController::class, 'store'])->name('produk.store');
    Route::patch('produk/{id}', [produkController::class, 'update'])->name('produk.update');
    Route::delete('produk/{id}', [produkController::class, 'destroy'])->name('produk.destroy');
    Route::post('/produk/import', [produkController::class, 'import'])->name('produk.import');


    //member
    Route::get('member', [MemberController::class, 'index'])->name('member.index');
    Route::post('member', [MemberController::class, 'store'])->name('member.store');
    Route::patch('member/{id}', [MemberController::class, 'update'])->name('member.update');
    Route::delete('member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');

    //supplier
    Route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::patch('supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    //penerimaan Barang
    Route::get('penerimaan', [PenerimaanBarangController::class, 'index'])->name('penerimaan.index');
    Route::get('penerimaan/create', [PenerimaanBarangController::class, 'create'])->name('penerimaan.create');
    Route::get('/get-harga-beli', [PenerimaanBarangController::class, 'getHargaBeli'])->name('getHargaBeli');
    Route::get('/getProdukBySupplier', [PenerimaanBarangController::class, 'getProdukBySupplier'])->name('getProdukBySupplier');
    Route::get('penerimaan/{id}/invoice', [PenerimaanBarangController::class, 'invoice'])->name('penerimaan.invoice');
    Route::post('penerimaanBarang', [PenerimaanBarangController::class, 'store'])->name('penerimaanBarang.store');
    Route::patch('penerimaan/{id}', [PenerimaanBarangController::class, 'update'])->name('penerimaan.update');
    Route::delete('penerimaan/{id}', [PenerimaanBarangController::class, 'destroy'])->name('penerimaan.destroy');

    //stok barang
    Route::get('stokbarang', [stockBarangController::class, 'index'])->name('stokbarang.index');
    Route::post('stokbarang', [stockBarangController::class, 'store'])->name('stokbarang.store');
    Route::patch('stokbarang/{id}', [stockBarangController::class, 'update'])->name('stokbarang.update');
    Route::delete('stokbarang/{id}', [stockBarangController::class, 'destroy'])->name('stokbarang.destroy');

    //batch stok
    Route::get('batchstok', [BatchStokController::class, 'index'])->name('batchstok.index');
    Route::post('batchstok', [BatchStokController::class, 'store'])->name('batchstok.store');
    Route::patch('batchstok/{id}', [BatchStokController::class, 'update'])->name('batchstok.update');
    Route::delete('batchstok/{id}', [BatchStokController::class, 'destroy'])->name('batchstok.destroy');

    //laporan
    Route::get('/laporan/pembelian', [LaporanController::class, 'pembelian'])->name('laporan.pembelian');
    Route::get('/laporan/pembelian/pdf', [LaporanController::class, 'exportPembelianPdf'])->name('laporan.pembelian.pdf');
    Route::get('/laporan/pembelian/excel', [LaporanController::class, 'exportPembelianExcel'])->name('laporan.pembelian.excel');


    //barang Display
    Route::get('/mutasiStok', [barangDisplayController::class, 'mutasiIndex'])->name('mutasiStok.index');
    Route::post('/mutasiStok/proses', [barangDisplayController::class, 'prosesMutasi'])->name('mutasiStok.proses');

    //Transaks
    Route::get('kasir-admin', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('api/barang/{kode}', [TransaksiController::class, 'getBarang']);
    Route::get('/api/cari-member', [TransaksiController::class, 'cariMember'])->name('api.cari-member');
    Route::post('api/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');

    Route::get('riwayat-transaksi', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
    Route::get('transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');



    // logs
    Route::get('/admin/logs', [LogController::class, 'index'])->name('logs.index')->middleware('auth');

    //absensi
    Route::get('absensi', [AbsensiKerjaController::class, 'index'])->name('absensi.index');
    Route::post('absensi/store', [AbsensiKerjaController::class, 'store'])->name('absensi.store');
    Route::patch('absensi/{id}', [AbsensiKerjaController::class, 'update'])->name('absensi.update');
    Route::patch('/absensi/{id}/update-status', [absensiKerjaController::class, 'updateStatus'])->name('absensi.updateStatus');
    Route::delete('absensi/{id}', [AbsensiKerjaController::class, 'destroy'])->name('absensi.destroy');
    Route::post('selesai/{id}', [AbsensiKerjaController::class, 'selesaiKerja'])->name('absensi.selesai');
    Route::get('export/excel', [AbsensiKerjaController::class, 'exportExcel'])->name('absensi.export.excel');
    Route::get('export/pdf', [AbsensiKerjaController::class, 'exportPDF'])->name('absensi.export.pdf');
    Route::post('import', [AbsensiKerjaController::class, 'importExcel'])->name('absensi.import');
    Route::get('format-import', [AbsensiKerjaController::class, 'downloadFormat'])->name('absensi.format');
});



Route::middleware(['auth', 'role:kasir'])->group(function () {
    // Route::get('kasirHome', [KasirController::class, 'index'])->name('kasir.dashboard');
});


// GROUP: Transaksi (Admin + Kasir)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {

    //kasir
    Route::get('kasir', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('api/barang/{kode}', [TransaksiController::class, 'getBarang']);
    Route::get('/api/cari-member', [TransaksiController::class, 'cariMember'])->name('api.cari-member');
    Route::post('api/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');

    //riwayat penjualan
    Route::get('riwayat-transaksi', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
    Route::get('transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/terakhir', [TransaksiController::class, 'getTransaksiTerakhir']);

    //barang display
    Route::get('display-barang', [barangDisplayController::class, 'index'])->name('displayBarang.index');

    // laporan
    Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/penjualan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.penjualan.pdf');
    Route::get('/laporan/penjualan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.penjualan.excel');


    //pengajuan
    Route::get('/pengajuan/all', [PengajuanController::class, 'pengajuanAll'])->name('pengajuan.all');
    Route::post('/pengajuan/update/status/{id}', [PengajuanController::class, 'updateStatus'])->name('pengajuan.updateStatus');
    Route::get('/pengajuan/export-pdf', [PengajuanController::class, 'exportPDF'])->name('pengajuan.exportPDF');
    Route::get('/pengajuan/export-excel', [PengajuanController::class, 'exportExcel'])->name('pengajuan.exportExcel');
});

Route::middleware(['member'])->group(function () {
    Route::get('member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');

    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::post('/pengajuan/update/{id}', [PengajuanController::class, 'update'])->name('pengajuan.update');
    Route::delete('/pengajuan/delete/{id}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
});

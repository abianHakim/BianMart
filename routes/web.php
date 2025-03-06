<?php

use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\authController;
use App\Http\Controllers\BatchStokController;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\memberController;
use App\Http\Controllers\penerimaanBarangController;
use App\Http\Controllers\produkController;
use App\Http\Controllers\stockBarangController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {

        switch (Auth::user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kasir':
                return redirect()->route('kasir.dashboard');
            default:
                return view('dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('cek-login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'role:admin'])->group(function () {
    //admin dashboard
    Route::get('adminhome', [AdminHomeController::class, 'index'])->name('admin.dashboard');

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
    Route::get('penerimaan', [penerimaanBarangController::class, 'index'])->name('penerimaan.index');
    Route::post('penerimaan', [penerimaanBarangController::class, 'store'])->name('penerimaan.store');
    Route::patch('penerimaan/{id}', [penerimaanBarangController::class, 'update'])->name('penerimaan.update');
    Route::delete('penerimaan/{id}', [penerimaanBarangController::class, 'destroy'])->name('penerimaan.destroy');

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
});



Route::middleware(['auth', 'role:kasir'])->group(function () {
    // Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
});

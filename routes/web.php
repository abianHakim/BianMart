<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\kategoriController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {
        // Cek role user
        if (Auth::user()->role === 'admin') {
            return redirect()->route('kategori.index');
        }
        if (Auth::user()->role === 'kasir') {
            return redirect()->route('kategori.index');
        }
        return view('dashboard');
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('login', [authController::class, 'showLoginForm'])->name('login');
Route::post('login', [authController::class, 'login'])->name('cek-login');
Route::post('logout', [authController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'role:admin'])->group(function () {
    // kategori
    Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
    Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});


Route::middleware(['role:kasir'])->group(function () {});

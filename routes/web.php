<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\kategoriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('login', [authController::class, 'showLoginForm'])->name('login');
Route::post('login', [authController::class, 'login'])->name('cek-login');
Route::post('logout', [authController::class, 'logout'])->name('logout');

Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
Route::patch('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

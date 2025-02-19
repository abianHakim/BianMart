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

        switch (Auth::user()->role) {
            case 'admin':
                return redirect()->route('kategori.index');
            case 'kasir':
                return redirect()->route('kategori.index');
            default:
                return view('dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified', 'prevent-back'])->name('dashboard');


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('prevent-back');
Route::post('login', [AuthController::class, 'login'])->name('cek-login')->middleware('prevent-back');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('prevent-back');



Route::middleware(['auth', 'role:admin'])->group(function () {
    // kategori
    Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');
    Route::post('kategori', [kategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});


Route::middleware(['role:kasir'])->group(function () {
    // Route::get('kategori', [kategoriController::class, 'index'])->name('kategori.index');

});

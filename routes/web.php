<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;

Auth::routes(['register' => false]);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
    Route::resource('buku', BukuController::class)->except(['index', 'show']);
});
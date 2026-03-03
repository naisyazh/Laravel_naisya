<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtpController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/otp-verification', [OtpController::class, 'showVerify'])
        ->name('otp.verify.form');

    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])
        ->name('otp.verify');

    Route::post('/otp-resend', [OtpController::class, 'sendOtp'])
        ->name('otp.send');


    Route::get('/dashboard', [OtpController::class, 'showDashboard'])
        ->name('otp.dashboard');

    Route::get('/sertifikat', [OtpController::class, 'showSertifikat'])
        ->name('otp.sertifikat');

    Route::get('/undangan', [OtpController::class, 'showUndangan'])
        ->name('otp.undangan');


    Route::get('/kategori', [KategoriController::class, 'index'])
        ->name('kategori.index');

    Route::get('/buku', [BukuController::class, 'index'])
        ->name('buku.index');

    Route::get('/barang', [BarangController::class, 'index'])
        ->name('barang.index');


    Route::post('/cetak-label-barang', [BarangController::class, 'cetakLabel'])
        ->name('barang.cetak');


    Route::middleware(['admin'])->group(function () {

        Route::resource('barang', BarangController::class)
            ->except(['index']);

        Route::resource('kategori', KategoriController::class)
            ->except(['index', 'show']);


        Route::resource('buku', BukuController::class)
            ->except(['index', 'show']);
            
        Route::resource('documents', DocumentController::class);
    });
});
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;

// 1. Tampilan Utama: Langsung ke halaman Login bawaan Laravel
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

// 3. Alur OTP (Hanya bisa diakses JIKA sudah Login Password)
// Pastikan ini ada di web.php
Auth::routes(['register' => false]);

// Pastikan rute verifikasi namanya sesuai dengan yang dipanggil di form action verify.blade.php
Route::middleware(['auth'])->group(function () {
    Route::get('/otp-verification', [OtpController::class, 'showVerify'])->name('otp.verify.form');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp-resend', [OtpController::class, 'sendOtp'])->name('otp.send');

    // 4. Grup Terproteksi Ganda (Sudah Login Password + Lolos OTP)
    Route::middleware(['web'])->group(function () {
        
        // Fitur Dashboard & Konten Modul 2
        Route::get('/dashboard', [OtpController::class, 'showDashboard'])->name('otp.dashboard');
        Route::get('/sertifikat', [OtpController::class, 'showSertifikat'])->name('otp.sertifikat');
        Route::get('/undangan', [OtpController::class, 'showUndangan'])->name('otp.undangan');

        // Fitur Koleksi Buku (Project Lama)
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');

        // Fitur CRUD untuk Admin
        Route::middleware(['admin'])->group(function () {
            Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
            Route::resource('buku', BukuController::class)->except(['index', 'show']);
        });
    });
});
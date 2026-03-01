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
    
    // 1. Alur Verifikasi OTP (Hanya butuh Login Password)
    Route::get('/otp-verification', [OtpController::class, 'showVerify'])->name('otp.verify.form');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp-resend', [OtpController::class, 'sendOtp'])->name('otp.send');

    // 2. Akses Setelah Lolos OTP (Bisa diakses Admin & User Biasa)
    Route::get('/dashboard', [OtpController::class, 'showDashboard'])->name('otp.dashboard');

    // Menu Sertifikat & Undangan (User Side - Read Only)
    Route::get('/sertifikat', [OtpController::class, 'showSertifikat'])->name('otp.sertifikat');
    Route::get('/undangan', [OtpController::class, 'showUndangan'])->name('otp.undangan');

    // Menu Master Data & Tag Harga (Akses Lihat)
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang/cetak', [BarangController::class, 'cetakLabel'])->name('barang.cetak');

    // 3. Akses Khusus Admin (Akses CRUD)
    Route::middleware(['admin'])->group(function () {
        Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
        Route::resource('buku', BukuController::class)->except(['index', 'show']);
        Route::resource('barang', BarangController::class)->except(['index']);
        
        // Manajemen Sertifikat & Undangan (Admin Side - CRUD)
        Route::resource('documents', DocumentController::class);
    });
});
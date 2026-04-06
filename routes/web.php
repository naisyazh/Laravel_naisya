<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

use App\Http\Controllers\OtpController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\VendorOrderController;

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/payments/midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('payments.midtrans.notification');

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

    Route::get('/tugas-js', [AssignmentController::class, 'index'])
        ->name('assignment');

    Route::prefix('tugas-js/api')->name('assignment.')->group(function () {
        Route::get('/regions/provinces', [AssignmentController::class, 'provinces'])
            ->name('regions.provinces');

        Route::get('/regions/regencies', [AssignmentController::class, 'regencies'])
            ->name('regions.regencies');

        Route::get('/regions/districts', [AssignmentController::class, 'districts'])
            ->name('regions.districts');

        Route::get('/regions/villages', [AssignmentController::class, 'villages'])
            ->name('regions.villages');

        Route::get('/barang', [AssignmentController::class, 'lookupBarang'])
            ->name('barang.lookup');

        Route::post('/checkout', [AssignmentController::class, 'checkout'])
            ->name('checkout');
    });


    Route::get('/kategori', [KategoriController::class, 'index'])
        ->name('kategori.index');

    Route::get('/buku', [BukuController::class, 'index'])
        ->name('buku.index');

    Route::middleware(['user'])->group(function () {
        Route::get('/toko-buku', [TokoController::class, 'index'])
            ->name('toko-buku.index');

        Route::get('/toko-buku/api/buku', [TokoController::class, 'lookup'])
            ->name('toko-buku.lookup');

        Route::post('/toko-buku/checkout', [TokoController::class, 'checkout'])
            ->name('toko-buku.checkout');

        Route::get('/toko-buku/orders/{penjualan:nomor_transaksi}', [TokoController::class, 'show'])
            ->name('toko-buku.orders.show');

        Route::post('/toko-buku/orders/{penjualan:nomor_transaksi}/confirm-demo-payment', [TokoController::class, 'confirmDemoPayment'])
            ->name('toko-buku.orders.confirm-demo-payment');

        Route::post('/toko-buku/orders/{penjualan:nomor_transaksi}/refresh-status', [TokoController::class, 'refreshStatus'])
            ->name('toko-buku.orders.refresh');
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/barang', [BarangController::class, 'index'])
            ->name('barang.index');

        Route::post('/cetak-label-barang', [BarangController::class, 'cetakLabel'])
            ->name('barang.cetak');

        Route::get('/vendor/orders', [VendorOrderController::class, 'index'])
            ->name('vendor.orders.index');

        Route::get('/vendor/orders/{penjualan:nomor_transaksi}', [VendorOrderController::class, 'show'])
            ->name('vendor.orders.show');

        Route::post('/vendor/orders/{penjualan:nomor_transaksi}/refresh-status', [VendorOrderController::class, 'refreshStatus'])
            ->name('vendor.orders.refresh');

        Route::post('/vendor/orders/{penjualan:nomor_transaksi}/mark-paid', [VendorOrderController::class, 'markPaid'])
            ->name('vendor.orders.mark-paid');


        Route::resource('barang', BarangController::class)
            ->except(['index', 'create', 'show']);

        Route::resource('kategori', KategoriController::class)
            ->except(['index', 'show']);


        Route::resource('buku', BukuController::class)
            ->except(['index', 'show']);

        Route::resource('documents', DocumentController::class)
            ->only(['index', 'create', 'store', 'destroy']);
    });
});

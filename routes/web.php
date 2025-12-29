<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;

// --- ROUTE PUBLIC (Bisa diakses tanpa login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROUTE PROTECTED (Harus Login dulu)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); // Ubah root langsung ke dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']); // Alias

    // Transaksi Routes
    Route::get('/transaksi/riwayat', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/baru', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/simpan', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::put('/transaksi/{id}/complete', [TransaksiController::class, 'complete'])->name('transaksi.complete');

    // Pelanggan Route
    Route::resource('pelanggan', PelangganController::class);

    // Ruangan Routes
    Route::resource('ruangan', RuanganController::class);

    // User Route
    Route::resource('user', UserController::class);
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
// Pastikan Anda membuat UserController jika belum ada
// use App\Http\Controllers\UserController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan rute untuk aplikasi web Anda.
|
*/

// Rute untuk Halaman Depan (bisa diakses siapa saja)
Route::get('/', function () {
    return view('welcome');
});

// Grup Rute yang MEMBUTUHKAN LOGIN
// Semua rute operasional apotek kita letakkan di sini
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard (Bisa diakses semua role yang login)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- RUTE UNTUK SEMUA ROLE (ADMIN, KEPALA APOTEK, APOTEKER) ---
    Route::get('/penjualan', [ObatController::class, 'index'])->name('penjualan.index');
    Route::post('/checkout', [ObatController::class, 'checkout'])->name('checkout');
    Route::get('/perhitungan', [ObatController::class, 'showRekapStok'])->name('obat.rekap');
    Route::get('/peramalan', [ObatController::class, 'hitungPeramalan'])->name('obat.peramalan');
    Route::get('/cek', [ObatController::class, 'showStok'])->name('obat.stok');

Route::middleware(['role:admin'])->group(function () {
    // Route ini akan otomatis membuat route untuk index, create, store, edit, update, destroy
    Route::resource('users', App\Http\Controllers\UserController::class);
    // Ganti route karyawan lama Anda dengan ini
});

    // --- RUTE KHUSUS UNTUK ADMIN & KEPALA APOTEK ---
    // Apoteker tidak akan bisa mengakses rute di dalam grup ini
    Route::middleware(['role:admin|kepala apotek'])->group(function () {
        Route::get('/master_data', [ObatController::class, 'masterIndex'])->name('obat.master.index');
        Route::post('/master_data', [ObatController::class, 'masterStore'])->name('obat.master.store');
        Route::put('/master_data/{id}', [ObatController::class, 'masterUpdate'])->name('obat.master.update');
        Route::delete('/master_data/{id}', [ObatController::class, 'masterDestroy'])->name('obat.master.destroy');
    });


    // --- RUTE KHUSUS UNTUK ADMIN ---
    // Hanya admin yang bisa mengelola pengguna/karyawan
        // --- RUTE KHUSUS UNTUK ADMIN ---
// Hanya admin yang bisa mengelola pengguna/karyawan
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
});

});


// Rute-rute lama yang tidak terpakai atau sudah dipindahkan bisa dihapus.
// Contohnya seperti Route::get('/data-obat', ...) dan Route::post('/tambah-obat', ...)
// karena fungsionalitasnya sudah di-handle oleh /master_data.
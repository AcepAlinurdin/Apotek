<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PembelianController;
USE App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\UserController;
// Pastikan ini ada di atas
use App\Http\Controllers\PaketObatController; // Dan ini juga

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
Route::get("/", function () {
    return view("welcome");
});

// Grup Rute yang MEMBUTUHKAN LOGIN
// Semua rute operasional apotek kita letakkan di sini
Route::middleware([
    "auth:sanctum",
    config("jetstream.auth_session"),
    "verified",
])->group(function () {
    // Dashboard (Bisa diakses semua role yang login)

    

    // --- RUTE UNTUK SEMUA ROLE (ADMIN, KEPALA APOTEK, APOTEKER) ---
    Route::get("/penjualan", [ObatController::class, "index"])->name(
        "penjualan.index"
    );
    Route::post("/checkout", [ObatController::class, "checkout"])->name(
        "checkout"
    );
    // routes/web.php

    // Rute untuk MENAMPILKAN halaman (method GET)
    Route::get("/perhitungan", [ObatController::class, "showRekapStok"])->name(
        "obat.rekap"
    );

    // Rute untuk MENYIMPAN data (method POST)
    Route::post("/perhitungan/simpan", [
        ObatController::class,
        "simpanPerhitungan",
    ])->name("perhitungan.simpan");
    Route::get("/peramalan", [ObatController::class, "hitungPeramalan"])->name(
        "obat.peramalan"
    );
    Route::get("/cek", [ObatController::class, "showStok"])->name("obat.stok");

    Route::middleware(["role:admin"])->group(function () {
        // Route ini akan otomatis membuat route untuk index, create, store, edit, update, destroy
        Route::resource("users", App\Http\Controllers\UserController::class);
        // Ganti route karyawan lama Anda dengan ini
    });

    // Route untuk menampilkan halaman laporan
    Route::get("/laporan-rekomendasi", [
        PembelianController::class,
        "rekap",
    ])->name("obat.rekap");

    // Route untuk menyimpan data pembelian
    Route::post("/pembelian/simpan", [
        PembelianController::class,
        "simpan",
    ])->name("pembelian.simpan");
    // Route::get('/pembelian/{pembelian}/cetak', [PembelianController::class, 'cetak'])->name('pembelian.cetak');
    // --- RUTE KHUSUS UNTUK ADMIN & KEPALA APOTEK ---
    // Apoteker tidak akan bisa mengakses rute di dalam grup ini
    Route::middleware(["role:admin|kepala apotek"])->group(function () {
        Route::get("/master_data", [
            ObatController::class,
            "masterIndex",
        ])->name("obat.master.index");
        Route::post("/master_data", [
            ObatController::class,
            "masterStore",
        ])->name("obat.master.store");
        Route::post("/master_data/transaksi_sementara", [
            ObatController::class,
            "simpanTransaksiSementara",
        ])->name("obat.transaksi.sementara");
        Route::put("/master_data/{id}", [
            ObatController::class,
            "masterUpdate",
        ])->name("obat.master.update");
        Route::delete("/master_data/{id}", [
            ObatController::class,
            "masterDestroy",
        ])->name("obat.master.destroy");
    });
    Route::resource(
        "suppliers",
        App\Http\Controllers\SupplierController::class
    );
    Route::get("/suppliers", [SupplierController::class, "index"])->name(
        "suppliers.index"
    );

    // Menampilkan form untuk membuat supplier baru
    Route::get("/suppliers/create", [
        SupplierController::class,
        "create",
    ])->name("suppliers.create");

    // Menyimpan data supplier baru
    Route::post("/suppliers", [SupplierController::class, "store"])->name(
        "suppliers.store"
    );

    // Menampilkan form untuk mengedit supplier
    Route::get("/suppliers/{id}/edit", [
        SupplierController::class,
        "edit",
    ])->name("suppliers.edit");

    // Memperbarui data supplier
    Route::put("/suppliers/{id}", [SupplierController::class, "update"])->name(
        "suppliers.update"
    );

    // Menghapus data supplier
    Route::delete("/suppliers/{id}", [
        SupplierController::class,
        "destroy",
    ])->name("suppliers.destroy");

    // --- RUTE KHUSUS UNTUK ADMIN ---
    // Hanya admin yang bisa mengelola pengguna/karyawan
    // --- RUTE KHUSUS UNTUK ADMIN ---
    // Hanya admin yang bisa mengelola pengguna/karyawan
    Route::middleware(["role:admin|kepala apotek"])->group(function () {
        Route::resource("users", App\Http\Controllers\UserController::class);
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/pembelian/{pembelian}/lunas', [PembelianController::class, 'updateStatusLunas'])->name('pembelian.update.lunas')->middleware('auth');


// === KELOMPOK ROUTE UNTUK API (PENCARIAN & RIWAYAT) ===
Route::middleware(['auth', 'role:apoteker'])->prefix('api')->name('api.')->group(function () {
    Route::get('/pasien/search', [PasienController::class, 'search'])->name('pasien.search');
    Route::get('/pasien/{pasien}/riwayat', [PasienController::class, 'getHistory'])->name('pasien.history');
});

// === KELOMPOK ROUTE UNTUK MANAJEMEN PASIEN (CRUD WEB) ===
Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::resource('pasien', PasienController::class);
});
Route::resource('paket-obat', PaketObatController::class)->middleware('auth');
Route::get('/api/paket-obat/{paket}', [ObatController::class, 'getPaketDetail'])
    ->middleware('auth')
    ->name('api.paket.detail');
Route::delete('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

// Route untuk mengaktifkan kembali pengguna
Route::post('/users/{id}/reactivate', [UserController::class, 'reactivate'])->name('users.reactivate');
}); // <-- Tambahkan penutup kurung kurawal dan tanda kurung tutup di sini

// Rute-rute lama yang tidak terpakai atau sudah dipindahkan bisa dihapus.
// Contohnya seperti Route::get('/data-obat', ...) dan Route::post('/tambah-obat', ...)
// karena fungsionalitasnya sudah di-handle oleh /master_data.

<?php

use Illuminate\Support\Facades\Route;
use App\Models\DataObat;
use Illuminate\Http\Request;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\master_dataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/perhitungan', function () {
//     return view('perhitungan');
// });

// Route untuk menampilkan halaman master data obat
Route::get('/master_data', [ObatController::class, 'masterIndex'])->name('obat.master.index');
Route::post('/master_data', [ObatController::class, 'masterStore'])->name('obat.master.store');
Route::put('/master_data/{id}', [ObatController::class, 'masterUpdate'])->name('obat.master.update');
Route::delete('/master_data/{id}', [ObatController::class, 'masterDestroy'])->name('obat.master.destroy');



Route::get('/cek', [ObatController::class, 'showStok'])->name('obat.rekap');

// Rute untuk menampilkan halaman daftar obat (indeks)
Route::get('/penjualan', [ObatController::class, 'index']);
Route::post('/checkout', [ObatController::class, 'checkout'])->name('checkout');
Route::get('/perhitungan', [ObatController::class, 'showRekapStok'])->name('obat.rekap');
Route::get('/peramalan', [ObatController::class, 'hitungPeramalan'])->name('obat.peramalan');

Route::get('/perhitungan', [ObatController::class, 'showRekapStok'])->name('obat.rekap');
Route::get('/', [ObatController::class, 'hitungPeramalan'])->name('obat.peramalan');

Route::get('/data-obat', function () {
    $obat = DataObat::all();
    return view('obat', compact('obat'));
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('/karyawan', function () {
    return view('karyawan');
});

Route::post('/tambah-obat', function (Request $request) {
    $request->validate([
        'nama_obat' => 'required|unique:data_obat,nama_obat|max:100',
        'harga' => 'required|numeric|min:1',
        'stok' => 'required|integer|min:0',
    ]);

    DataObat::create([
        'nama_obat' => $request->nama_obat,
        'harga' => $request->harga,
        'stok' => $request->stok,
    ]);

    return redirect('/data-obat')->with('success', 'Obat berhasil ditambahkan');
});

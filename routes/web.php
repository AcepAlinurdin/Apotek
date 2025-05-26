<?php

use Illuminate\Support\Facades\Route;
use App\Models\DataObat;
use Illuminate\Http\Request;
use App\Http\Controllers\ObatController;

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
Route::get('/beranda', function () {
    return view('Awal');
});
Route::get('/PengelolaanObat', function () {
    return view('kedua');
});


// Rute untuk menampilkan halaman daftar obat (indeks)
Route::get('/obat', [ObatController::class, 'index']);
Route::post('/checkout', [ObatController::class, 'checkout'])->name('checkout');

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


Route::get('/tambah-obat', function () {
    return view('tambah');
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

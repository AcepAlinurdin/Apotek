<?php

namespace App\Http\Controllers;

use App\Models\DataObat;
use App\Models\PenjualanObat; // Memperbaiki nama model dari 'master'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class master_dataController extends Controller
{
    /**
     * Menampilkan halaman Master Data dengan semua data obat.
     */
    public function showMasterData()
    {
        // Mengambil semua data dari tabel data_obat
        $data_obats = DataObat::orderBy('nama_obat', 'asc')->get();

        // Mengirim data ke view 'master_data'
        return view('master_data', compact('data_obats'));
    }

    /**
     * Menyimpan data obat baru yang dikirim melalui AJAX.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
        ]);

        // Membuat data baru di database
        $obat = DataObat::create([
            'nama_obat' => $request->nama_obat,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'harga_satuan' => $request->harga, // 'harga' dari form disimpan ke 'harga_satuan'
        ]);

        // Mengirim respon sukses dalam format JSON
        return response()->json(['success' => true, 'data' => $obat]);
    }

    // --- CATATAN ---
    // Kode di bawah ini adalah untuk fungsi checkout dan tidak perlu diubah,
    // tapi pastikan Anda sudah menyesuaikannya sesuai kebutuhan.
    // Pastikan juga model PenjualanObat Anda sudah benar.

    // public function index()
    // {
    //     $obats = DataObat::all();
    //     $riwayatPenjualans = PenjualanObat::orderBy('tanggal', 'desc')
    //                                         ->orderBy('created_at', 'desc')
    //                                         ->get();
    //     return view('penjualan', compact('obats', 'riwayatPenjualans'));
    // }

    // public function checkout(Request $request) { ... }
    // protected function processCheckout(array $cartItems) { ... }
    // protected function validateRequest(Request $request) { ... }
}

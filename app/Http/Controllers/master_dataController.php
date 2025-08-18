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
        DB::beginTransaction();
        try {
            // Validasi data
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama_obat' => 'required|string|max:255',
                'kategori' => 'required|string|max:255',
                'supplier' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'harga_satuan' => 'required|numeric|min:0',
                'harga_box' => 'nullable|numeric|min:0',
            ]);

            // Cari ID supplier berdasarkan nama
            $supplier = Supplier::where('nama_supplier', $validatedData['supplier'])->first();
            if (!$supplier) {
                return response()->json(['success' => false, 'message' => 'Supplier tidak ditemukan.'], 404);
            }

            // Mencari apakah obat dengan nama yang sama sudah ada
            $existingObat = Obat::where('nama_obat', $validatedData['nama_obat'])->first();

            if ($existingObat) {
                // Jika suppliernya sama, kembalikan error
                if ($existingObat->supplier_id === $supplier->id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'nama_obat' => ['Obat dengan nama dan supplier yang sama sudah ada.']
                    ]);
                }
                
                // Jika suppliernya berbeda, update supplier_id dari obat yang sudah ada
                $existingObat->update(['supplier_id' => $supplier->id]);
                $obat = $existingObat; // Gunakan objek yang sudah ada
            } else {
                // Jika obat belum ada, buat entri baru
                $obat = Obat::create([
                    'tanggal' => $validatedData['tanggal'],
                    'nama_obat' => $validatedData['nama_obat'],
                    'kategori' => $validatedData['kategori'],
                    'supplier_id' => $supplier->id,
                    'harga_satuan' => $validatedData['harga_satuan'],
                    'harga_box' => $validatedData['harga_box'],
                ]);
            }

            // Buat entri di tabel stoks untuk stok awal
            if ($validatedData['stok'] > 0) {
                Stok::create([
                    'obat_id' => $obat->id,
                    'tipe_pergerakan' => 'masuk',
                    'jumlah' => $validatedData['stok'],
                    'tanggal' => $validatedData['tanggal'],
                    'keterangan' => 'Stok awal saat pendaftaran obat',
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data obat berhasil disimpan.', 'data' => $obat]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

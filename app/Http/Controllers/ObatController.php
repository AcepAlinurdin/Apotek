<?php

namespace App\Http\Controllers;

use App\Models\DataObat;
use App\Models\PenjualanObat; // Pastikan model PenjualanObat sudah benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ObatController extends Controller
{
    /**
     * Menampilkan halaman utama penjualan obat dengan daftar obat dan riwayat penjualan.
     */
    public function index()
    {
        // Mengambil semua data obat dari database
        $obats = DataObat::all();

        // Mengambil semua data riwayat penjualan, diurutkan berdasarkan tanggal terbaru
        // Asumsi model Anda untuk tabel 'data_penjualan' adalah 'PenjualanObat'
        // dan memiliki kolom 'tanggal' atau 'created_at' untuk pengurutan.
        // Kita akan menggunakan 'tanggal' jika ada, atau 'created_at' sebagai fallback.
        $riwayatPenjualans = PenjualanObat::orderBy('tanggal', 'desc')->get();
        // Jika Anda lebih suka mengurutkan berdasarkan ID (record terbaru):
        // $riwayatPenjualans = PenjualanObat::orderBy('id', 'desc')->get();


        // Mengembalikan tampilan 'ketiga.blade.php' dan melewatkan data $obats dan $riwayatPenjualans
        return view('ketiga', compact('obats', 'riwayatPenjualans'));
    }

    // ... (method checkout, processCheckout, validateRequest tetap sama seperti sebelumnya) ...
    // Pastikan method-method ini sudah disesuaikan untuk tidak menggunakan 'harga_satuan'
    // saat create PenjualanObat, sesuai instruksi Anda sebelumnya.

    protected function processCheckout(array $cartItems): array
    {
        $result = [];

        foreach ($cartItems as $item) {
            $obat = DataObat::where('nama_obat', $item['name'])->firstOrFail();

            if ($obat->qty < $item['quantity']) {
                throw new \Exception("Stok {$obat->nama_obat} tidak mencukupi.");
            }

            $penjualan = PenjualanObat::create([
                'obat_id' => $obat->id,
                'kode_obat' => $obat->kode_obat,
                'nama_obat' => $obat->nama_obat,
                'qty' => $item['quantity'],
                'total_harga' => $obat->harga_satuan * $item['quantity'], // harga_satuan diambil dari DataObat
                'tanggal' => Carbon::now()
            ]);

            $obat->decrement('qty', $item['quantity']);
            $result[] = $penjualan;
        }
        return $result;
    }

    public function checkout(Request $request)
    {
        $validated = $this->validateRequest($request);
        DB::beginTransaction();
        try {
            $result = $this->processCheckout($validated['cartItems']);
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Checkout berhasil'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage().' Stack: '.$e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat checkout: ' . $e->getMessage()
            ], 400);
        }
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'cartItems' => 'required|array|min:1',
            'cartItems.*.name' => 'required|string|max:255',
            'cartItems.*.quantity' => 'required|integer|min:1'
        ]);
    }
}
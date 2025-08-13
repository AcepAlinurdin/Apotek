<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function rekap(Request $request)
    {
        // Ambil data dari request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $selectedObatId = $request->input('obat_id');

        // Logika untuk mengambil data hasil peramalan dari database
        // **CATATAN PENTING:** Bagian ini harus Anda sesuaikan dengan
        // implementasi Fuzzy Mamdani Anda. Contoh di bawah ini adalah
        // cara mengambil data penjualan historis sebagai ganti data dummy.
        // Anda perlu menggantinya dengan logika yang menghitung
        // "rekomendasi_pembelian" berdasarkan metode Fuzzy Mamdani.
        
        $query = Obat::query();
        if ($selectedObatId) {
            $query->where('id', $selectedObatId);
        }

        $allObat = $query->get();
        $hasilPeramalan = [];

        foreach ($allObat as $obat) {
            // ✅ KOREKSI: Melakukan JOIN antara tabel 'penjualans' dan 'detail_penjualans'
            // untuk mendapatkan total penjualan berdasarkan tanggal dan obat_id.
            $totalPenjualan = DB::table('penjualans')
                                ->join('detail_penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                                ->where('detail_penjualans.obat_id', $obat->id)
                                ->whereBetween('penjualans.tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                                ->sum('detail_penjualans.jumlah');

            // Logika Fuzzy Mamdani Anda seharusnya ada di sini
            // untuk menghasilkan nilai 'rekomendasi_pembelian'.
            // Contoh sederhana untuk demonstrasi:
            $rekomendasi = $totalPenjualan > $obat->stok ? $totalPenjualan - $obat->stok : 0;

            $hasilPeramalan[] = [
                'obat_id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'stok_saat_ini' => $obat->stok,
                'total_penjualan_periode' => $totalPenjualan,
                'rekomendasi_pembelian' => $rekomendasi,
                'supplier_id' => $obat->supplier_id, // Asumsi supplier_id ada di tabel obat
                'harga_box' => $obat->harga_box, // Asumsi harga_box ada di tabel obat
                'harga_pcs' => $obat->harga_pcs, // Asumsi harga_pcs ada di tabel obat
            ];
        }

        // Ambil semua data supplier untuk dropdown
        $suppliers = Supplier::all();

        return view('laporan-rekomendasi', compact('hasilPeramalan', 'suppliers', 'tanggalMulai', 'tanggalAkhir', 'selectedObatId'));
    }

    public function simpan(Request $request)
    {
        // Gunakan try-catch dan transaksi database untuk memastikan integritas data
        DB::beginTransaction();

        try {
            $data = $request->json()->all();

            $validated = validator($data['detail_pembelian'], [
                '*.obat_id' => 'required|exists:obat,id',
                '*.jumlah' => 'required|integer|min:1',
                '*.supplier_id' => 'required|exists:suppliers,id',
                '*.harga_beli_satuan' => 'required|numeric|min:0', // Validasi nama field baru
            ])->validate();

            // Hitung total harga pembelian
            $totalHarga = 0;
            foreach ($validated as $item) {
                $totalHarga += $item['jumlah'] * $item['harga_beli_satuan'];
            }

            // Buat entri pembelian utama
            $pembelian = Pembelian::create([
                'pegawai_id' => Auth::id(), // Asumsi pegawai_id diambil dari user yang login
                'supplier_id' => $validated[0]['supplier_id'], // Ambil supplier dari item pertama
                'tanggal_pembelian' => now(),
                'total_harga' => $totalHarga,
                'status' => 'pending', // Anda bisa menambahkan status lain jika perlu
            ]);

            // Simpan detail pembelian
            foreach ($validated as $item) {
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $item['obat_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli_satuan' => $item['harga_beli_satuan'],
                    'subtotal' => $item['jumlah'] * $item['harga_beli_satuan'],
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pembelian berhasil disimpan!'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use Carbon\Carbon; // 1. Tambahkan use Carbon

class PembelianController extends Controller
{
    public function rekap(Request $request)
{
    $tanggalMulai = Carbon::create(2023, 10, 1)->startOfDay(); // 1 Oktober 2023
    $tanggalAkhir = Carbon::create(2023, 12, 31)->endOfDay();
    $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
    $semuaObatForDropdown = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'supplier_id', 'harga_satuan', 'harga_box']);
    
    $obatStokMenipis = Obat::with('supplier')->where('stok', '<', 21)->get();
    $hasilPeramalan = [];
    
    foreach ($obatStokMenipis as $obat) {
        $stokSaatIni = $obat->stok;
        $totalPenjualanPeriode = DetailPenjualan::where('obat_id', $obat->id)
            ->whereHas('penjualan', function($q) use ($tanggalMulai, $tanggalAkhir) {
                $q->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
            })
            ->sum('jumlah');

        $rekomendasi = $this->jalankanMesinFuzzy($totalPenjualanPeriode, $stokSaatIni, $obat);
        
        if ($rekomendasi > 0) {
             $hasilPeramalan[] = [
                'obat_id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'supplier' => $obat->supplier->nama_supplier ?? 'N/A',
                'supplier_id' => $obat->supplier_id,
                'kategori' => $obat->kategori,
                'stok_saat_ini' => $stokSaatIni,
                'total_penjualan_periode' => $totalPenjualanPeriode,
                'rekomendasi_pembelian' => $rekomendasi,
                'harga_box' => $obat->harga_box,
                'harga_pcs' => $obat->harga_satuan,
                'satuan' => $obat->satuan,
            ];
        }
    }
    
    // ======================================================================
    // === BAGIAN KRITIS: PASTIKAN ANDA MENGAMBIL DAN MENGIRIM DATA INI =====
    // ======================================================================
    $semuaObatList = Obat::orderBy('nama_obat', 'asc')->get();

    return view('perhitungan', [
        'hasilPeramalan' => $hasilPeramalan,
        'semuaObatList' => $semuaObatList, // <-- PASTIKAN BARIS INI ADA
        'tanggalMulai' => $tanggalMulai->format('Y-m-d'),
        'tanggalAkhir' => $tanggalAkhir->format('Y-m-d'),
        'suppliers' => $suppliers,
        'semuaObat' => $semuaObatForDropdown
    ]);
}
    // ===================================================================
    // =========== LOGIKA FUZZY MAMDANI DINAMIS ==========================
    // ===================================================================

    private function jalankanMesinFuzzy($penjualan, $stok, $obat) {
        // 1. Dapatkan parameter fuzzy yang dinamis, spesifik untuk obat ini
        $fuzzyParams = $this->getDynamicFuzzyParams($penjualan, $obat);

        // 2. Fuzzifikasi, Implikasi, dan Defuzzifikasi (tidak berubah)
        $derajat = $this->fuzzifikasi($penjualan, $stok, $fuzzyParams);
        $implikasi = $this->implikasiAturan($derajat);
        return $this->defuzzifikasi($implikasi, $fuzzyParams);
    }

    /**
     * FUNGSI KUNCI: Membuat parameter fuzzy dinamis untuk setiap obat.
     * "Penggaris" untuk penjualan kini dibuat berdasarkan histori penjualan obat itu sendiri.
     */
    private function getDynamicFuzzyParams($totalPenjualanObat, $obat): array
    {
        // Domain Penjualan: Dibuat berdasarkan total penjualan 3 bulan terakhir obat ini.
        // Diberi sedikit ruang ekstra (misal * 1.5) untuk menangani lonjakan.
        // Jika penjualan 0, diberi nilai default kecil.
        $maxPenjualan = ($totalPenjualanObat > 0) ? ceil($totalPenjualanObat * 1.5) : 10;
        $minPenjualan = 0;

        // Domain Stok: Bisa tetap global atau dibuat lebih spesifik jika ada data.
        // Untuk saat ini, kita gunakan min/max global agar lebih stabil.
        $minStok = Obat::min('stok') ?? 0;
        $maxStok = Obat::max('stok') ?? 50;

        // Domain Pembelian: Disesuaikan dengan potensi penjualan obat ini.
        // Rekomendasi maksimal adalah sekitar 120% dari penjualan terakhir.
        $maxPembelian = ceil($totalPenjualanObat * 0.3);
        if ($maxPembelian < 20) {
            $maxPembelian = 20; // Batas bawah rekomendasi
        }

        $params = [
            'penjualan' => [$minPenjualan, $maxPenjualan],
            'stok' => [$minStok, $maxStok],
            'pembelian' => [0, $maxPembelian],
        ];

        // Mencegah error jika min dan max sama
        if ($params['penjualan'][0] == $params['penjualan'][1]) $params['penjualan'][1]++;
        if ($params['stok'][0] == $params['stok'][1]) $params['stok'][1]++;

        return $params;
    }

    private function fuzzifikasi($penjualan, $stok, array $fuzzyParams): array {
        $batasPenjualan = $fuzzyParams['penjualan'];
        $batasStok = $fuzzyParams['stok'];

        return [
            'penjualan_sedikit' => $this->turun($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'penjualan_banyak'  => $this->naik($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'stok_sedikit'      => $this->turun($stok, $batasStok[0], $batasStok[1]),
            'stok_banyak'       => $this->naik($stok, $batasStok[0], $batasStok[1]),
        ];
    }
    
    private function implikasiAturan($derajat): array {
        // Aturan Fuzzy:
        // R1: JIKA penjualan SEDIKIT AND stok SEDIKIT MAKA pembelian SEDIKIT
        // R2: JIKA penjualan SEDIKIT AND stok BANYAK MAKA pembelian SEDIKIT
        // R3: JIKA penjualan BANYAK AND stok SEDIKIT MAKA pembelian BANYAK  <-- ATURAN UTAMA
        // R4: JIKA penjualan BANYAK AND stok BANYAK MAKA pembelian SEDIKIT
        
        $alpha1 = min($derajat['penjualan_sedikit'], $derajat['stok_sedikit']);
        $alpha2 = min($derajat['penjualan_sedikit'], $derajat['stok_banyak']);
        $alpha3 = min($derajat['penjualan_banyak'], $derajat['stok_sedikit']);
        $alpha4 = min($derajat['penjualan_banyak'], $derajat['stok_banyak']);

        return [
            'sedikit' => max($alpha1, $alpha2, $alpha4),
            'banyak' => $alpha3,
        ];
    }

    private function defuzzifikasi($implikasi, $fuzzyParams) {
        $batasPembelian = $fuzzyParams['pembelian'];
        $alpha_sedikit = $implikasi['sedikit'];
        $alpha_banyak = $implikasi['banyak'];
        
        $pembilang = 0;
        $penyebut = 0;

        // Menggunakan metode Centroid
        for ($z = $batasPembelian[0]; $z <= $batasPembelian[1]; $z++) {
            $miuSedikit = $this->turun($z, $batasPembelian[0], $batasPembelian[1]);
            $miuBanyak = $this->naik($z, $batasPembelian[0], $batasPembelian[1]);
            
            $areaSedikit = min($alpha_sedikit, $miuSedikit);
            $areaBanyak = min($alpha_banyak, $miuBanyak);
            
            $areaGabungan = max($areaSedikit, $areaBanyak);
            
            if ($areaGabungan > 0) {
                $pembilang += $z * $areaGabungan;
                $penyebut += $areaGabungan;
            }
        }
        
        if ($penyebut == 0) return 0;
        
        return round($pembilang / $penyebut);
    }

    // Fungsi Keanggotaan (Membership Functions)
    private function turun($x, $a, $b) {
        if ($x <= $a) return 1;
        if ($x >= $b) return 0;
        return ($b - $x) / ($b - $a);
    }

    private function naik($x, $a, $b) {
        if ($x <= $a) return 0;
        if ($x >= $b) return 1;
        return ($x - $a) / ($b - $a);
    }
    // ===================================================================
    // =========== METHOD UNTUK MENYIMPAN PEMBELIAN ======================
    // ===================================================================

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'status' => 'required|string|in:Lunas,Belum Lunas',
            'detail_pembelian' => 'required|array|min:1',
            'detail_pembelian.*.obat_id' => 'required|integer|exists:obats,id',
            'detail_pembelian.*.supplier_id' => 'required|integer|exists:suppliers,id', 
            'detail_pembelian.*.jumlah' => 'required|integer|min:1',
            'detail_pembelian.*.harga_beli_satuan' => 'required|numeric|min:0',
            'detail_pembelian.*.harga_beli_box' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $pembelian = Pembelian::create([
                'pegawai_id' => auth()->id(),
                'supplier_id' => $validated['detail_pembelian'][0]['supplier_id'], 
                'tanggal_pembelian' => $validated['tanggal_pembelian'],
                'total_harga' => 0,
                'status' => $validated['status'],
            ]);

            $totalHargaPembelian = 0;

            foreach ($validated['detail_pembelian'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli_satuan'];
                
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $item['obat_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli_satuan' => $item['harga_beli_satuan'],
                    'harga_beli_box' => $item['harga_beli_box'],
                    'subtotal' => $subtotal,
                    'satuan' => Obat::find($item['obat_id'])->satuan,
                ]);

                $obat = Obat::find($item['obat_id']);
                $obat->increment('stok', $item['jumlah']);
                $totalHargaPembelian += $subtotal;
            }

            $pembelian->total_harga = $totalHargaPembelian;
            $pembelian->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaksi pembelian berhasil disimpan!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pembelian: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan pembelian.'], 500);
        }
    }
    public function cetak(Pembelian $pembelian)
{
    // Eager load relasi untuk mengambil data detail, obat, supplier, dan pegawai
    $pembelian->load(['detailPembelians.obat', 'supplier', 'pegawai']);

    // Kirim data pembelian ke view 'pembelian.cetak'
    return view('pembelian.cetak', compact('pembelian'));
}
public function updateStatusLunas(Pembelian $pembelian)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'kepala apotek'])) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
        }

        try {
            $pembelian->status = 'Lunas';
            $pembelian->save();
            
            return response()->json(['success' => true, 'message' => 'Status pembelian berhasil diubah menjadi Lunas.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat update.'], 500);
        }
    }
}
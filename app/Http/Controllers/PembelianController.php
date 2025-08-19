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

class PembelianController extends Controller
{
    public function rekap(Request $request)
    {

        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $semuaObatForDropdown = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'supplier_id', 'harga_satuan', 'harga_box']);
        
        if (!$tanggalMulai || !$tanggalAkhir) {
            return view('perhitungan', [
                'hasilPeramalan' => [],
                'tanggalMulai' => $tanggalMulai,
                'tanggalAkhir' => $tanggalAkhir,
                'suppliers' => $suppliers,
                'semuaObat' => $semuaObatForDropdown
            ]);
        }


        $fuzzyParams = $this->getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir);
        $obatStokMenipis = Obat::with('supplier')->where('stok', '<', 21)->get();
        $hasilPeramalan = [];
        

        foreach ($obatStokMenipis as $obat) {
 
            $totalPenjualanPeriode = DetailPenjualan::where('obat_id', $obat->id)
                ->whereHas('penjualan', function($q) use ($tanggalMulai, $tanggalAkhir) {
                    $q->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
                })
                ->sum('jumlah');

            $stokSaatIni = $obat->stok;

            $rekomendasi = $this->jalankanMesinFuzzy($totalPenjualanPeriode, $stokSaatIni, $fuzzyParams);
            
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
                ];
            }
        }
        
        return view('perhitungan', [
            'hasilPeramalan' => $hasilPeramalan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'suppliers' => $suppliers,
            'semuaObat' => $semuaObatForDropdown
        ]);
    }

    // ===================================================================
    // =========== LOGIKA FUZZY MAMDANI DINAMIS ==========================
    // ===================================================================

    private function getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir): array
{
    $penjualanPerObat = DetailPenjualan::select(DB::raw('SUM(jumlah) as total_jual'))
        ->join('penjualans', 'detail_penjualans.penjualan_id', '=', 'penjualans.id')
        ->whereBetween('penjualans.tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
        ->groupBy('detail_penjualans.obat_id')
        ->pluck('total_jual');

    $minPenjualan = $penjualanPerObat->min() ?? 0;
    $maxPenjualan = $penjualanPerObat->max() ?? 10;

    $minStok = Obat::min('stok') ?? 0;
    $maxStok = Obat::max('stok') ?? 50;

    // =================================================================
    // =========== PERUBAHAN UTAMA ADA DI BAGIAN INI =====================
    // =================================================================
    $maxPembelian = ceil($maxPenjualan * 0.2)+4;
    if ($maxPembelian < 30) {
        $maxPembelian = 30;
    }

    $params = [
        'penjualan' => [$minPenjualan, $maxPenjualan],
        'stok' => [$minStok, $maxStok],
        'pembelian' => [0, $maxPembelian],
    ];

    if ($params['penjualan'][0] == $params['penjualan'][1]) $params['penjualan'][1]++;
    if ($params['stok'][0] == $params['stok'][1]) $params['stok'][1]++;

    return $params;
}

    private function jalankanMesinFuzzy($penjualan, $stok, array $fuzzyParams) {
        $derajat = $this->fuzzifikasi($penjualan, $stok, $fuzzyParams);
        $implikasi = $this->implikasiAturan($derajat);
        return $this->defuzzifikasi($implikasi, $fuzzyParams);
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
        
        if ($penyebut == 0) {
            return 0;
        }
        
        return round($pembilang / $penyebut);
    }

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
}
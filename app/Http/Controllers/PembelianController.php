<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Stok;
use App\Models\Supplier;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\DetailPembelian;

class PembelianController extends Controller
{
    /**
     * Menampilkan hasil peramalan dan rekomendasi pembelian.
     */
    public function rekap(Request $request)
    {
        // --- Bagian 1: Mengambil semua input dari user ---
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $searchTerm = $request->input('search');
        $selectedObatId = $request->input('obat_id');
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $semuaObat = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'supplier_id', 'harga_satuan', 'harga_box']);
        // Jika tanggal tidak diisi, set hasil peramalan menjadi kosong
        if (!$tanggalMulai || !$tanggalAkhir) {
            $hasilPeramalan = [];
            return view('perhitungan', compact('hasilPeramalan', 'searchTerm', 'tanggalMulai', 'tanggalAkhir', 'selectedObatId','suppliers','semuaObat'));
        }

        // Ambil parameter fuzzy berdasarkan rentang tanggal
        $fuzzyParams = $this->getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir);

        // --- Bagian 2: Membangun Query Utama ---
        $query = Obat::with('supplier');
        if ($searchTerm) {
            $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
        }
        $semuaObat = $query->orderBy('nama_obat', 'asc')->get();
        
        // --- Bagian 3: Proses Perhitungan Fuzzy ---
        $hasilPeramalan = [];
        
        foreach ($semuaObat as $obat) {
            // Perbaikan: Hanya proses obat dengan stok <= 21
            if ($obat->stok <= 21) {
                // Mendapatkan setiap entri penjualan untuk obat ini dalam periode tertentu
                $detailPenjualans = DetailPenjualan::where('obat_id', $obat->id)
                    ->whereHas('penjualan', function($q) use ($tanggalMulai, $tanggalAkhir) {
                        $q->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
                    })
                    ->get();
                
                // Inisialisasi rekomendasi untuk obat ini
                $rekomendasiTemp = [];

                // Jika tidak ada penjualan, tambahkan 0
                if ($detailPenjualans->isEmpty()) {
                    $rekomendasiTemp[] = 0;
                } else {
                    foreach ($detailPenjualans as $detailPenjualan) {
                        $inputPenjualan = $detailPenjualan->jumlah;
                        $inputStok = $obat->stok;
                        
                        $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok, $fuzzyParams);
                        $rekomendasi = ($rekomendasi > 0) ? round($rekomendasi) : 0;
                        
                        $rekomendasiTemp[] = $rekomendasi;
                    }
                }

                // Ambil rekomendasi tertinggi
                $rekomendasiFinal = max($rekomendasiTemp);
                $totalPenjualan = $detailPenjualans->sum('jumlah');

                // Tambahkan ke array hasil peramalan jika rekomendasi > 0
                if ($rekomendasiFinal > 0) {
                     $hasilPeramalan[] = [
                        'obat_id' => $obat->id,
                        'nama_obat' => $obat->nama_obat,
                        'supplier' => $obat->supplier->nama_supplier ?? 'N/A',
                        'kategori' => $obat->kategori,
                        'stok_saat_ini' => $obat->stok,
                        'total_penjualan_periode' => $totalPenjualan,
                        'rekomendasi_pembelian' => $rekomendasiFinal,
                        'harga_box' => $obat->harga_box,
                        'harga_pcs' => $obat->harga_satuan,
                    ];
                }
            }
        }
        
        return view('perhitungan', compact('hasilPeramalan', 'searchTerm', 'tanggalMulai', 'tanggalAkhir', 'selectedObatId','suppliers','semuaObat'));
    }

    /**
     * Mendapatkan parameter dinamis untuk Fuzzy dari database.
     */
    private function getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir): array
    {
        $penjualanPerObat = DetailPenjualan::select(DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereHas('penjualan', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
            })
            ->groupBy('obat_id')
            ->pluck('total_jumlah');

        $minPenjualan = $penjualanPerObat->min();
        $maxPenjualan = $penjualanPerObat->max();

        $minStok = Obat::min('stok');
        $maxStok = Obat::max('stok');

        $params['penjualan'] = [$minPenjualan ?? 1, $maxPenjualan ?? 10];
        $params['stok'] = [$minStok ?? 10, $maxStok ?? 50];

        if ($params['penjualan'][0] == $params['penjualan'][1]) $params['penjualan'][1]++;
        if ($params['stok'][0] == $params['stok'][1]) $params['stok'][1]++;

        return $params;
    }

    /**
     * Fungsi utama yang mengorkestrasi proses Fuzzy.
     */
    private function jalankanMesinFuzzy($penjualan, $stok, array $fuzzyParams) {
        $derajat = $this->fuzzifikasi($penjualan, $stok, $fuzzyParams);
        $kekuatanAturan = $this->evaluasiAturan($derajat);
        return $this->defuzzifikasi($kekuatanAturan);
    }

    /**
     * Tahap 1: FUZZIFIKASI
     */
    private function fuzzifikasi($penjualan, $stok, array $fuzzyParams): array
    {
        $batasPenjualan = $fuzzyParams['penjualan'];
        $batasStok = $fuzzyParams['stok'];

        return [
            'penjualan_naik' => $this->hitungNaik($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'penjualan_turun' => $this->hitungTurun($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'stok_banyak' => $this->hitungNaik($stok, $batasStok[0], $batasStok[1]),
            'stok_sedikit' => $this->hitungTurun($stok, $batasStok[0], $batasStok[1]),
        ];
    }

    /**
     * Tahap 2: EVALUASI ATURAN (INFERENSI)
     */
    private function evaluasiAturan($derajat) {
        $alpha1 = min($derajat['penjualan_naik'], $derajat['stok_banyak']);
        $alpha2 = min($derajat['penjualan_naik'], $derajat['stok_sedikit']);
        $alpha3 = min($derajat['penjualan_turun'], $derajat['stok_banyak']);
        $alpha4 = min($derajat['penjualan_turun'], $derajat['stok_sedikit']);

        return ['R1' => $alpha1, 'R2' => $alpha2, 'R3' => $alpha3, 'R4' => $alpha4];
    }

    /**
     * Tahap 3: DEFUZZIFIKASI
     */
    private function defuzzifikasi($kekuatanAturan) {
        $kekuatanBerkurang = max($kekuatanAturan['R1'], $kekuatanAturan['R3']);
        $kekuatanBertambah = max($kekuatanAturan['R2'], $kekuatanAturan['R4']);

        $params = [
            'pembelian_berkurang' => [5, 15],
            'pembelian_bertambah' => [10, 20],
        ];
        
        $pembilang = 0;
        $penyebut = 0;

        for ($z = 0; $z <= 35; $z++) {
            $miuBerkurang = $this->hitungTurun($z, $params['pembelian_berkurang'][0], $params['pembelian_berkurang'][1]);
            $miuBertambah = $this->hitungNaik($z, $params['pembelian_bertambah'][0], $params['pembelian_bertambah'][1]);

            $areaBerkurang = min($kekuatanBerkurang, $miuBerkurang);
            $areaBertambah = min($kekuatanBertambah, $miuBertambah);

            $areaGabungan = max($areaBerkurang, $areaBertambah);

            $pembilang += $z * $areaGabungan;
            $penyebut += $areaGabungan;
        }

        if ($penyebut == 0) return 0;
        
        return $pembilang / $penyebut;
    }

    /**
     * Fungsi keanggotaan linear turun.
     */
    private function hitungTurun($x, $bawah, $atas) {
        if ($x <= $bawah) return 1;
        if ($x >= $atas) return 0;
        return ($atas - $x) / ($atas - $bawah);
    }

    /**
     * Fungsi keanggotaan linear naik.
     */
    private function hitungNaik($x, $bawah, $atas) {
        if ($x <= $bawah) return 0;
        if ($x >= $atas) return 1;
        return ($x - $bawah) / ($atas - $bawah);
    }

    public function simpanTransaksiSementara(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:obats,id',
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|in:pembelian,penjualan',
            'jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $obat = \App\Models\Obat::findOrFail($request->obat_id);
            $pegawai_id = auth()->id();

            if ($request->jenis_transaksi == 'pembelian') {
                $pembelian = \App\Models\Pembelian::create([
                    'pegawai_id' => $pegawai_id,
                    'supplier_id' => $obat->supplier_id,
                    'tanggal_pembelian' => $request->tanggal,
                    'total_harga' => 0,
                    'status' => 'Selesai',
                ]);

                \App\Models\DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $request->jumlah,
                    'harga_beli_satuan' => 0,
                    'subtotal' => 0,
                ]);

                $obat->increment('stok', $request->jumlah);

            } elseif ($request->jenis_transaksi == 'penjualan') {
                if ($obat->stok < $request->jumlah) {
                    DB::rollBack();
                    return back()->with('error', 'Stok tidak mencukupi untuk penjualan!');
                }

                $penjualan = \App\Models\Penjualan::create([
                    'pegawai_id' => $pegawai_id,
                    'tanggal_penjualan' => $request->tanggal,
                    'total_harga' => 0,
                ]);

                \App\Models\DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $request->jumlah,
                    'harga_satuan' => $obat->harga_satuan,
                    'subtotal' => $obat->harga_satuan * $request->jumlah,
                ]);

                $obat->decrement('stok', $request->jumlah);
            }

            DB::commit();
            return back()->with('success', 'Transaksi sementara berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage()); 
            Log::error('Gagal simpan transaksi sementara: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }

    }
   // Di dalam file app/Http/Controllers/PembelianController.php

// Di dalam file app/Http/Controllers/PembelianController.php

public function simpan(Request $request)
{
    // 1. Validasi data yang masuk
    $validated = $request->validate([
        'tanggal_pembelian' => 'required|date',
        'status' => 'required|string|in:Lunas,Belum Lunas',
        'detail_pembelian' => 'required|array|min:1',
        'detail_pembelian.*.obat_id' => 'required|integer|exists:obats,id',
        'detail_pembelian.*.supplier_id' => 'required|integer|exists:suppliers,id', // <-- Ubah menjadi 'required'
        'detail_pembelian.*.jumlah' => 'required|integer|min:1',
        'detail_pembelian.*.harga_beli_satuan' => 'required|numeric|min:0',
        'detail_pembelian.*.harga_beli_box' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // 2. Buat record "header" di tabel 'pembelians'
        // Kita bisa ambil supplier dari item pertama sebagai referensi, atau biarkan null jika tidak relevan.
        $pembelian = Pembelian::create([
            'pegawai_id' => auth()->id(),
            'supplier_id' => $validated['detail_pembelian'][0]['supplier_id'], // Ambil supplier dari item pertama
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'total_harga' => 0,
            'status' => $validated['status'],
        ]);

        $totalHargaPembelian = 0;

        // 3. Loop melalui setiap item obat dan simpan
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

            // Jika perlu, Anda bisa mengupdate supplier default di tabel obats di sini
            // Obat::find($item['obat_id'])->update(['supplier_id' => $item['supplier_id']]);

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
    
    // ... (Tambahkan kembali metode-metode fuzzy lainnya di sini) ...
}

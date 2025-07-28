<?php

namespace App\Http\Controllers;

// Menggabungkan semua 'use' yang dibutuhkan
use App\Models\DataObat;
use App\Models\PenjualanObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ObatController extends Controller
{
    // =================================================================
    // =========== LOGIKA UNTUK HALAMAN PENJUALAN / KASIR ==============
    // =================================================================

    /**
     * Menampilkan halaman utama penjualan (kasir).
     * Mengambil daftar obat yang sudah dikelompokkan dan dijumlahkan stoknya,
     * serta riwayat penjualan terakhir.
     * Dipanggil oleh route GET /penjualan.
     */
    public function index()
    {
        // Mengambil obat untuk ditampilkan di daftar pilihan,
        // dengan mengelompokkan berdasarkan nama dan harga untuk menyatukan stok
        $obats = DataObat::select(
                'nama_obat',
                'harga_satuan',
                DB::raw('SUM(qty) as total_stok') // Menjumlahkan kolom qty sebagai total_stok
            )
            ->groupBy('nama_obat', 'harga_satuan') // Mengelompokkan berdasarkan nama dan harga
            ->orderBy('nama_obat', 'asc')
            ->get();

        // Mengambil riwayat penjualan untuk ditampilkan di tabel
        $riwayatPenjualans = PenjualanObat::orderBy('tanggal', 'desc')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        return view('penjualan2', compact('obats', 'riwayatPenjualans'));
    }

    /**
     * Memproses permintaan checkout dari kasir.
     * Fungsi ini dipanggil oleh method checkout().
     */
    public function checkout(Request $request)
    {
        // 1. Validasi input keranjang belanja (cart)
        $validated = $this->validateRequest($request);

        // 2. Memulai transaksi database untuk memastikan semua proses berhasil
        DB::beginTransaction();

        try {
            // 3. Memproses setiap item di keranjang
            $result = $this->processCheckout($validated['cartItems']);

            // 4. Jika berhasil, simpan semua perubahan ke database
            DB::commit();
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Checkout berhasil']);

        } catch (\Exception $e) {
            // 5. Jika terjadi error, batalkan semua perubahan dan kirim pesan error
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage().' Stack: '.$e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat checkout: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Logika inti untuk memproses setiap item dalam keranjang saat checkout.
     * - Memvalidasi stok.
     * - Membuat catatan penjualan.
     * - Mengurangi stok obat.
     */
    protected function processCheckout(array $cartItems): array
    {
        $result = [];
        foreach ($cartItems as $item) {
            // Mencari batch obat yang akan dikurangi stoknya
            // Menggunakan firstOrFail() agar jika obat tidak ada, proses langsung gagal
            $obat = DataObat::where('nama_obat', $item['name'])->firstOrFail();

            // Cek ketersediaan stok
            if ($obat->qty < $item['quantity']) {
                throw new \Exception("Stok {$obat->nama_obat} tidak mencukupi.");
            }

            // Buat record baru di tabel penjualan
            $penjualan = PenjualanObat::create([
                'obat_id' => $obat->id,
                // 'kode_obat' => $obat->kode_obat,
                'nama_obat' => $obat->nama_obat,
                'qty' => $item['quantity'],
                'total_harga' => $obat->harga_satuan * $item['quantity'],
                'tanggal' => Carbon::now()
            ]);

            // Kurangi stok dari batch obat yang ditemukan
            $obat->decrement('qty', $item['quantity']);
            $result[] = $penjualan;
        }
        return $result;
    }

    /**
     * Aturan validasi untuk request checkout.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'cartItems' => 'required|array|min:1',
            'cartItems.*.name' => 'required|string|max:255',
            'cartItems.*.quantity' => 'required|integer|min:1'
        ]);
    }


    // =================================================================
    // =========== LOGIKA UNTUK HALAMAN MASTER DATA (CRUD) =============
    // =================================================================

    /**
     * Menampilkan halaman utama master data dengan semua data obat. (READ)
     */
   // app/Http/Controllers/ObatController.php

public function masterIndex(Request $request)
{
    $searchTerm = $request->input('search');
    $query = DataObat::query();

    if ($searchTerm) {
        // ✅ PERBAIKAN: Hanya mencari berdasarkan nama_obat
        $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
    }

    $data_obats = $query->orderBy('nama_obat', 'asc')->get();
    return view('master_data', compact('data_obats', 'searchTerm'));
}

public function masterStore(Request $request)
{
    $request->validate([
        'tanggal'      => 'required|date',
        'nama_obat'    => 'required|string|max:255',
        'kategori'     => 'required|string|max:255',
        'supplier'     => 'nullable|string|max:255',
        'stok'         => 'required|integer|min:0',
        'harga_satuan' => 'required|numeric|min:0',
        'harga_box'    => 'required|numeric|min:0',
    ]);

    DataObat::create([
        'tanggal'      => $request->tanggal,
        'nama_obat'    => $request->nama_obat,
        'kategori'     => $request->kategori,
        'supplier'     => $request->supplier,
        'qty'          => $request->stok,
        'harga_satuan' => $request->harga_satuan,
        'harga_box'    => $request->harga_box,
    ]);

    return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan!']);
}

public function masterUpdate(Request $request, $id)
{
    $obat = DataObat::findOrFail($id);

    $request->validate([
        'tanggal'      => 'required|date',
        'nama_obat'    => 'required|string|max:255',
        'kategori'     => 'required|string|max:255',
        'supplier'     => 'nullable|string|max:255',
        'stok'         => 'required|integer|min:0',
        'harga_satuan' => 'required|numeric|min:0',
        'harga_box'    => 'required|numeric|min:0',
    ]);

    $obat->update([
        'tanggal'      => $request->tanggal,
        'nama_obat'    => $request->nama_obat,
        'kategori'     => $request->kategori,
        'supplier'     => $request->supplier,
        'qty'          => $request->stok,
        'harga_satuan' => $request->harga_satuan,
        'harga_box'    => $request->harga_box,
    ]);

    return response()->json(['success' => true, 'message' => 'Data obat berhasil diperbarui.']);
}
    // =================================================================
    // =========== LOGIKA UNTUK HALAMAN REKAP & PERAMALAN ==============
    // =================================================================

    /**
     * Menampilkan halaman 'perhitungan' dengan hasil peramalan Fuzzy.
     * Fungsi ini mengambil obat dengan stok kurang, menghitung input penjualan terakhir,
     * dan menjalankan logika fuzzy untuk mendapatkan rekomendasi pembelian.
     */
    // public function showRekapStok()
    // {
    //     // Mengambil obat dengan stok kritis (kurang dari 20)
    //     $stok_kurang = DataObat::withSum('penjualan', 'qty')
    //                            ->where('qty', '<', 20)
    //                            ->orderBy('qty', 'asc')
    //                            ->get();

    //     $hasilPeramalan = [];

    //     foreach ($stok_kurang as $obat) {
    //         // Cari tanggal penjualan terakhir untuk obat ini
    //         $tanggalPenjualanTerakhir = PenjualanObat::where('obat_id', $obat->id)->max('tanggal');
    //         $inputPenjualan = 0;

    //         // Jika ada riwayat penjualan, hitung total penjualan pada hari terakhir itu
    //         if ($tanggalPenjualanTerakhir) {
    //             $inputPenjualan = PenjualanObat::where('obat_id', $obat->id)
    //                                             ->whereDate('tanggal', $tanggalPenjualanTerakhir)
    //                                             ->sum('qty');
    //         }

    //         // Ambil stok saat ini sebagai input
    //         $inputStok = $obat->qty;
            
    //         // Jalankan mesin fuzzy untuk mendapatkan rekomendasi
    //         $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok);

    //         // Simpan hasil untuk ditampilkan di view
    //         $hasilPeramalan[] = [
    //             'nama_obat' => $obat->nama_obat,
    //             'kategori' => $obat->kategori,
    //             'stok_saat_ini' => $inputStok,
    //             'penjualan_terakhir_input' => $inputPenjualan,
    //             'total_penjualan' => $obat->penjualan_sum_qty ?? 0,
    //             'rekomendasi_pembelian' => round($rekomendasi)
    //         ];
    //     }

    //     return view('perhitungan', compact('hasilPeramalan'));
    // }

    // // =================================================================
    // // ============ LOGIKA UNTUK HALAMAN CEK STOK KESELURUHAN ==========
    // // =================================================================

    // /**
    //  * Menampilkan halaman 'cek_stok'.
    //  * Menyajikan dua daftar: semua obat dan obat dengan stok kritis.
    //  */
   public function showStok()
    {
        // Query untuk daftar semua obat tetap sama, tidak difilter
        $semua_obat = DataObat::withSum('penjualan', 'qty')
                              ->orderBy('nama_obat', 'asc')
                              ->get();
        
        // ==========================================================
        // =========== FILTER OTOMATIS DIMULAI DARI SINI ============
        // ==========================================================

        // 1. Cari tanggal transaksi paling terakhir di database.
        $tanggalTerakhir = PenjualanObat::max('tanggal');
        
        // 2. Siapkan variabel untuk menampung hasil query stok kritis.
        $stok_kurang = collect(); // Defaultnya adalah koleksi kosong.

        // 3. JIKA ada riwayat transaksi, jalankan filter.
        if ($tanggalTerakhir) {
            // Ambil tahun dan bulan dari tanggal terakhir tersebut.
            $carbonDate = Carbon::parse($tanggalTerakhir);
            $tahunTerakhir = $carbonDate->year;
            $bulanTerakhir = $carbonDate->month;

            // Jalankan query untuk mencari stok kritis YANG MEMILIKI
            // riwayat penjualan pada bulan dan tahun terakhir.
            $stok_kurang = DataObat::withSum('penjualan', 'qty')
                // Kondisi utama: stok di bawah 20
                ->where('qty', '<', 20)
                // ✅ Kondisi filter tambahan:
                // Hanya jika obat ini memiliki penjualan pada bulan & tahun terakhir.
                ->whereHas('penjualan', function ($query) use ($tahunTerakhir, $bulanTerakhir) {
                    $query->whereYear('tanggal', $tahunTerakhir)
                          ->whereMonth('tanggal', '>', $bulanTerakhir - 2);
                })
                ->orderBy('qty', 'asc')
                ->get();
        }

        // 4. Kirim kedua data ke view.
        // Jika tidak ada transaksi, $stok_kurang akan menjadi kosong.
        return view('cek_stok', compact('semua_obat', 'stok_kurang'));
    }

// =================================================================
    // =========== LOGIKA UNTUK HALAMAN REKAP & PERAMALAN ==============
    // =================================================================

    /**
     * Menampilkan halaman 'perhitungan' dengan hasil peramalan Fuzzy.
     * Fungsi ini mengambil obat dengan stok kurang, menghitung input,
     * dan menjalankan logika fuzzy untuk mendapatkan rekomendasi pembelian.
     */
    public function showRekapStok(Request $request) // <-- Tambahkan Request $request
{
    // 1. Ambil kata kunci pencarian dari input user
    $searchTerm = $request->input('search');

    // 2. Ambil parameter fuzzy (logika ini tidak berubah)
    $fuzzyParams = $this->getFuzzyParamsFromDB();

    // 3. Bangun query dasar untuk obat stok kritis
    $query = DataObat::withSum('penjualan', 'qty')->where('qty', '<', 20);

    // 4. JIKA ada input pencarian, tambahkan filter WHERE
    if ($searchTerm) {
        // Cari berdasarkan nama obat yang cocok dengan kata kunci
        $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
    }

    // 5. Eksekusi query untuk mendapatkan daftar obat yang sudah difilter
    $stok_kurang = $query->orderBy('qty', 'asc')->get();

    // Proses peramalan fuzzy tetap sama, namun sekarang menggunakan
    // data $stok_kurang yang mungkin sudah terfilter
    $hasilPeramalan = [];
    foreach ($stok_kurang as $obat) {
        $tanggalPenjualanTerakhir = PenjualanObat::where('obat_id', $obat->id)->max('tanggal');
        $inputPenjualan = 0;

        if ($tanggalPenjualanTerakhir) {
            $inputPenjualan = PenjualanObat::where('obat_id', $obat->id)
                                          ->whereDate('tanggal', $tanggalPenjualanTerakhir)
                                          ->sum('qty');
        }

        $inputStok = $obat->qty;
        $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok, $fuzzyParams);

        $hasilPeramalan[] = [
            'nama_obat' => $obat->nama_obat,
            'kategori' => $obat->kategori,
            'stok_saat_ini' => $inputStok,
            'penjualan_terakhir_input' => $inputPenjualan,
            'total_penjualan' => $obat->penjualan_sum_qty ?? 0,
            'rekomendasi_pembelian' => round($rekomendasi)
        ];
    }

    // 6. Kirim hasil peramalan DAN kata kunci pencarian ke view
    return view('perhitungan', compact('hasilPeramalan', 'searchTerm'));
}

    // =================================================================
    // =============== MESIN INFERENSI FUZZY (MAMDANI) ===================
    // =================================================================

    /**
     * Mengambil parameter min-max untuk Fuzzifikasi dari database.
     * Data diambil dari histori 3 bulan terakhir untuk membuatnya dinamis.
     *
     * @return array
     */
    private function getFuzzyParamsFromDB(): array
    {
        // Tentukan batas waktu, yaitu 3 bulan dari sekarang
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // 1. Ambil parameter untuk PENJUALAN dari tabel 'penjualan_obats'
        $minPenjualan = PenjualanObat::where('tanggal', '>=', $threeMonthsAgo)->min('qty');
        $maxPenjualan = PenjualanObat::where('tanggal', '>=', $threeMonthsAgo)->max('qty');

        // 2. Ambil parameter untuk STOK dari tabel 'data_obats'
        $minStok = DataObat::where('tanggal', '>=', $threeMonthsAgo)->min('qty');
        $maxStok = DataObat::where('tanggal', '>=', $threeMonthsAgo)->max('qty');

        // 3. Fallback & Pencegahan Error
        // Jika tidak ada data, gunakan nilai default (misal: 10 & 18 untuk penjualan)
        $params['penjualan'] = [$minPenjualan ?? 10, $maxPenjualan ?? 18];
        $params['stok']      = [$minStok ?? 15, $maxStok ?? 25];

        // Mencegah error pembagian dengan nol jika min dan max sama
        if ($params['penjualan'][0] == $params['penjualan'][1]) {
            $params['penjualan'][1]++;
        }
        if ($params['stok'][0] == $params['stok'][1]) {
            $params['stok'][1]++;
        }

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
     * Menghitung derajat keanggotaan menggunakan parameter dinamis dari database.
     */
    private function fuzzifikasi($penjualan, $stok, array $fuzzyParams): array
    {
        $batasPenjualan = $fuzzyParams['penjualan'];
        $batasStok      = $fuzzyParams['stok'];

        return [
            'penjualan_naik'  => $this->hitungNaik($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'penjualan_turun' => $this->hitungTurun($penjualan, $batasPenjualan[0], $batasPenjualan[1]),
            'stok_banyak'     => $this->hitungNaik($stok, $batasStok[0], $batasStok[1]),
            'stok_sedikit'    => $this->hitungTurun($stok, $batasStok[0], $batasStok[1]),
        ];
    }

    /**
     * Tahap 2: EVALUASI ATURAN (INFERENSI)
     * Menerapkan aturan Fuzzy dan mencari nilai alpha (α) menggunakan fungsi MIN.
     */
    private function evaluasiAturan($derajat) {
        // [R1] IF Penjualan NAIK AND Stok BANYAK THEN Pembelian BERKURANG
        $alpha1 = min($derajat['penjualan_naik'], $derajat['stok_banyak']);
        // [R2] IF Penjualan NAIK AND Stok SEDIKIT THEN Pembelian BERTAMBAH
        $alpha2 = min($derajat['penjualan_naik'], $derajat['stok_sedikit']);
        // [R3] IF Penjualan TURUN AND Stok BANYAK THEN Pembelian BERKURANG
        $alpha3 = min($derajat['penjualan_turun'], $derajat['stok_banyak']);
        // [R4] IF Penjualan TURUN AND Stok SEDIKIT THEN Pembelian BERTAMBAH
        $alpha4 = min($derajat['penjualan_turun'], $derajat['stok_sedikit']);

        return ['R1' => $alpha1, 'R2' => $alpha2, 'R3' => $alpha3, 'R4' => $alpha4];
    }

    /**
     * Tahap 3: DEFUZZIFIKASI (Metode Mamdani - Center of Gravity / Centroid)
     * Menghitung output tegas (crisp) dengan mencari titik pusat dari area fuzzy gabungan.
     */
    private function defuzzifikasi($kekuatanAturan) {
        // Gabungkan kekuatan aturan yang memiliki konsekuen (THEN) yang sama menggunakan MAX.
        $kekuatanBerkurang = max($kekuatanAturan['R1'], $kekuatanAturan['R3']);
        $kekuatanBertambah = max($kekuatanAturan['R2'], $kekuatanAturan['R4']);

        // Parameter untuk variabel output 'pembelian' (bisa juga dibuat dinamis jika perlu)
        $params = [
            'pembelian_berkurang' => [5, 15],
            'pembelian_bertambah' => [10, 20],
        ];
        
        $pembilang = 0; // Untuk menyimpan Σ(z * μ(z))
        $penyebut = 0;  // Untuk menyimpan Σ(μ(z))

        // Ambil sampel diskrit di sepanjang rentang output (misal: 0 hingga 35)
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
}
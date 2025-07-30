<?php

namespace App\Http\Controllers;

// [REFACTORED] Menggunakan model-model baru sesuai struktur database
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pegawai; // Asumsi Anda sudah membuat model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ObatController extends Controller
{
    // =================================================================
    // =========== HALAMAN PENJUALAN / KASIR ==============
    // =================================================================

    /**
     * Menampilkan halaman utama penjualan (kasir).
     */
    public function index()
    {
        // [REFACTORED] Mengambil data dari tabel master 'obats'.
        // Tidak perlu grouping lagi karena stok sudah terpusat.
        $obats = Obat::orderBy('nama_obat', 'asc')->get();

        // [REFACTORED] Mengambil riwayat dari 'detail_penjualans' dan menyertakan data relasinya.
        $riwayatPenjualans = DetailPenjualan::with(['obat', 'penjualan.user'])
                                            ->orderBy('id', 'desc') // Mengurutkan berdasarkan ID (transaksi terbaru)
                                            ->take(20) // Ambil 20 transaksi terakhir saja agar tidak berat
                                            ->get();

        return view('penjualan2', compact('obats', 'riwayatPenjualans'));
    }

    /**
     * Memproses permintaan checkout dari kasir.
     */
    public function checkout(Request $request)
    {
        $validated = $this->validateRequest($request);
        DB::beginTransaction();

        try {
            // [REFACTORED] Logika checkout dirombak total untuk struktur header-detail
            $this->processNewCheckout($validated['cartItems']);
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Checkout berhasil']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage().' Stack: '.$e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * [REFACTORED] Logika inti baru untuk checkout dengan header-detail.
     */
    protected function processNewCheckout(array $cartItems)
    {
        // 1. Hitung total harga keseluruhan terlebih dahulu
        $totalHargaKeseluruhan = 0;
        foreach ($cartItems as $item) {
            $obat = Obat::where('nama_obat', $item['name'])->first();
            if ($obat) {
                $totalHargaKeseluruhan += $obat->harga_satuan * $item['quantity'];
            }
        }

        // 2. Buat satu record di tabel 'penjualans' (header)
        // TODO: Ganti 'pegawai_id' dengan ID pegawai yang sedang login.
        // Untuk sementara, kita gunakan ID 1 sebagai contoh.
        $penjualan = Penjualan::create([
            'pegawai_id' => auth()->id(), // Mengambil ID user yang terautentikasi
            'tanggal_penjualan' => now(),
            'total_harga' => $totalHargaKeseluruhan,
        ]);

        // 3. Loop lagi untuk membuat record di 'detail_penjualans' dan mengurangi stok
        foreach ($cartItems as $item) {
            $obat = Obat::where('nama_obat', $item['name'])->firstOrFail();

            // Cek ketersediaan stok
            if ($obat->stok < $item['quantity']) {
                throw new \Exception("Stok {$obat->nama_obat} tidak mencukupi.");
            }

            // Buat record detail penjualan
            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'obat_id' => $obat->id,
                'jumlah' => $item['quantity'],
                'harga_satuan' => $obat->harga_satuan, // Simpan harga saat transaksi
                'subtotal' => $obat->harga_satuan * $item['quantity'],
            ]);

            // Kurangi stok dari tabel master 'obats'
            $obat->decrement('stok', $item['quantity']);
        }
    }

    /**
     * Aturan validasi untuk request checkout.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'cartItems' => 'required|array|min:1',
            'cartItems.*.name' => 'required|string|exists:obats,nama_obat',
            'cartItems.*.quantity' => 'required|integer|min:1'
        ]);
    }


    // =================================================================
    // =========== HALAMAN MASTER DATA (CRUD) =============
    // =================================================================

    public function masterIndex(Request $request)
    {
        $searchTerm = $request->input('search');
        // [REFACTORED] Menggunakan model Obat
        $query = Obat::with('supplier'); // Eager load data supplier

        if ($searchTerm) {
            $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
        }

        $data_obats = $query->orderBy('nama_obat', 'asc')->get();
        return view('master_data', compact('data_obats', 'searchTerm'));
    }

    public function masterStore(Request $request)
    {
        // Gunakan Validator manual untuk kontrol penuh atas response
        $validator = Validator::make($request->all(), [
            'nama_obat'    => 'required|string|max:255|unique:obats,nama_obat',
            'kategori'     => 'required|string|max:255',
            'supplier'     => 'required|string|max:255',
            'stok'         => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'harga_box'    => 'nullable|numeric|min:0',
        ]);

        // Jika validasi gagal, kirim kembali error dalam format JSON
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Cari atau buat supplier baru
        $supplier = Supplier::firstOrCreate(['nama_supplier' => $request->supplier]);

        // Buat data obat
        Obat::create([
            'supplier_id'  => $supplier->id,
            'nama_obat'    => $request->nama_obat,
            'kategori'     => $request->kategori,
            'stok'         => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'harga_box'    => $request->harga_box,
        ]);

        return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan!']);
    }

    public function masterUpdate(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            // Pastikan validasi unique mengabaikan ID obat yang sedang diedit
            'nama_obat'    => 'required|string|max:255|unique:obats,nama_obat,'.$id,
            'kategori'     => 'required|string|max:255',
            'supplier'     => 'required|string|max:255',
            'stok'         => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'harga_box'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::firstOrCreate(['nama_supplier' => $request->supplier]);

        $obat->update([
            'supplier_id'  => $supplier->id,
            'nama_obat'    => $request->nama_obat,
            'kategori'     => $request->kategori,
            'stok'         => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'harga_box'    => $request->harga_box,
        ]);

        return response()->json(['success' => true, 'message' => 'Data obat berhasil diperbarui.']);
    }
    
    public function masterDestroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();
        return response()->json(['success' => true, 'message' => 'Data obat berhasil dihapus.']);
    }


    // =================================================================
    // =========== HALAMAN REKAP & PERAMALAN ==============
    // =================================================================
    
    public function showRekapStok(Request $request)
    {
        $searchTerm = $request->input('search');
        $query = Obat::where('stok', '<', 20);

        if ($searchTerm) {
            $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
        }

        $stok_kurang = $query->with('supplier')->orderBy('stok', 'asc')->get();
        $hasilPeramalan = [];

        foreach ($stok_kurang as $obat) {
            // [FIXED] Mengubah latest() menjadi orderBy('id', 'desc')
            $detailPenjualanTerakhir = DetailPenjualan::where('obat_id', $obat->id)
                                        ->orderBy('id', 'desc')
                                        ->first();
            
            $inputPenjualan = 0;
            if ($detailPenjualanTerakhir) {
                // Pastikan 'use Carbon\Carbon;' ada di atas file
$tanggalTerakhir = \Carbon\Carbon::parse($detailPenjualanTerakhir->penjualan->tanggal_penjualan);
                $inputPenjualan = DetailPenjualan::where('obat_id', $obat->id)
                                    ->whereHas('penjualan', function($q) use ($tanggalTerakhir) {
                                        $q->whereDate('tanggal_penjualan', $tanggalTerakhir->toDateString());
                                    })
                                    ->sum('jumlah');
            }

            $inputStok = $obat->stok;
            // $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok); // Fuzzy logic di-nonaktifkan sementara

            $hasilPeramalan[] = [
                'nama_obat' => $obat->nama_obat,
                'supplier' => $obat->supplier->nama_supplier ?? 'N/A',
                'kategori' => $obat->kategori,
                'stok_saat_ini' => $inputStok,
                'total_penjualan' => DetailPenjualan::where('obat_id', $obat->id)->sum('jumlah'),
                'rekomendasi_pembelian' => 0, //round($rekomendasi), // Default 0
                'harga_box' => $obat->harga_box,
                'harga_pcs' => $obat->harga_satuan
            ];
        }

        return view('perhitungan', compact('hasilPeramalan', 'searchTerm'));
    }
     public function showStok()
    {
        // Mengambil semua data obat, diurutkan berdasarkan nama
        $semua_obat = Obat::with('supplier')->orderBy('nama_obat', 'asc')->get();
        
        // Mengambil data obat yang stoknya kritis (kurang dari 20), diurutkan dari yang paling sedikit
        $stok_kurang = Obat::with('supplier')->where('stok', '<', 20)->orderBy('stok', 'asc')->get();

        // Kirim kedua data ke view
        return view('cek_stok', compact('semua_obat', 'stok_kurang'));
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
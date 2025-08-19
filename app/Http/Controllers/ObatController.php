<?php

namespace App\Http\Controllers;

// [REFACTORED] Menggunakan model-model baru sesuai struktur database
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\Penjualan;
use App\Models\Perhitungan;
use App\Models\DetailPerhitungan;
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
                                 // Ambil 20 transaksi terakhir saja agar tidak berat
                                            ->get();

        return view('penjualan2', compact('obats', 'riwayatPenjualans'));
    }

    /**
     * Memproses permintaan checkout dari kasir.
     */
    public function checkout(Request $request)
{
    // UBAH VALIDASI: Kita sekarang mewajibkan 'id', bukan 'name'.
    $validated = $request->validate([
        'cartItems' => 'required|array|min:1',
        'cartItems.*.id' => 'required|integer|exists:obats,id', // Validasi berdasarkan ID
        'cartItems.*.quantity' => 'required|integer|min:1'
    ]);

    DB::beginTransaction();

    try {
        // Panggil method processNewCheckout dengan data yang sudah divalidasi
        $this->processNewCheckout($validated['cartItems']);
        
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Checkout berhasil']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Checkout error: '.$e->getMessage().' Stack: '.$e->getTraceAsString());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

/**
 * Logika inti untuk checkout.
 */
protected function processNewCheckout(array $cartItems)
{
    $totalHargaKeseluruhan = 0;
    foreach ($cartItems as $item) {
        // UBAH CARA PENCARIAN: Gunakan find() dengan ID, lebih cepat dan akurat.
        $obat = Obat::find($item['id']); 
        if ($obat) {
            $totalHargaKeseluruhan += $obat->harga_satuan * $item['quantity'];
        }
    }

    $penjualan = Penjualan::create([
        'pegawai_id' => auth()->id(),
        'tanggal_penjualan' => now(),
        'total_harga' => $totalHargaKeseluruhan,
    ]);

    foreach ($cartItems as $item) {
        $obat = Obat::find($item['id']);

        if ($obat->stok < $item['quantity']) {
            // Rollback manual tidak perlu di sini karena sudah ada di catch
            throw new \Exception("Stok {$obat->nama_obat} tidak mencukupi.");
        }

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'obat_id' => $obat->id, // Gunakan ID langsung dari item
            'jumlah' => $item['quantity'],
            'harga_satuan' => $obat->harga_satuan,
            'subtotal' => $obat->harga_satuan * $item['quantity'],
        ]);

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
public function showStok()
{
    // Mengambil semua data obat, diurutkan berdasarkan nama
    $semua_obat = \App\Models\Obat::with('supplier')->orderBy('nama_obat', 'asc')->get();
    
    // Mengambil data obat yang stoknya kritis (kurang dari 20), diurutkan dari yang paling sedikit
    $stok_kurang = \App\Models\Obat::with('supplier')->where('stok', '<', 21)->orderBy('stok', 'asc')->get();

    // Kirim kedua data ke view 'cek_stok'
    return view('cek_stok', compact('semua_obat', 'stok_kurang'));
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
        $suppliers = \App\Models\Supplier::orderBy('nama_supplier', 'asc')->get();


        $data_obats = $query->orderBy('nama_obat', 'asc')->get();
        return view('master_data', compact('data_obats', 'searchTerm', 'suppliers'));
    }

   public function masterStore(Request $request)
{
    

    $validator = Validator::make($request->all(), [
        'tanggal'      => 'required|date',
        'nama_obat'    => 'required|string|max:255',
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

    // ✅ Simpan obat yang baru dibuat ke dalam variabel
    $obat = Obat::create([
         'tanggal'      => $request->tanggal,
        'supplier_id'  => $supplier->id,
        'nama_obat'    => $request->nama_obat,
        'kategori'     => $request->kategori,
        'stok'         => $request->stok,
        'harga_satuan' => $request->harga_satuan,
        'harga_box'    => $request->harga_box,
    ]);

    // ✅ Muat relasi supplier agar bisa ditampilkan di tabel
    $obat->load('supplier');

    // ✅ Kembalikan data obat yang baru dibuat dalam response
    return response()->json([
        'success' => true, 
        'message' => 'Obat berhasil ditambahkan!',
        'data'    => $obat 
    ]);
}

public function masterUpdate(Request $request, $id)
{
    $obat = Obat::findOrFail($id);
    
    $validator = Validator::make($request->all(), [
        'tanggal'      => 'required|date',
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
        'tanggal'      => $request->tanggal,
        'supplier_id'  => $supplier->id,
        'nama_obat'    => $request->nama_obat,
        'kategori'     => $request->kategori,
        'stok'         => $request->stok,
        'harga_satuan' => $request->harga_satuan,
        'harga_box'    => $request->harga_box,
    ]);
    
    // ✅ Muat relasi supplier yang mungkin baru
    $obat->load('supplier');

    // ✅ Kembalikan data obat yang sudah diupdate dalam response
    return response()->json([
        'success' => true, 
        'message' => 'Data obat berhasil diperbarui.',
        'data' => $obat
    ]);
}
public function masterDestroy($id)
{
    // Cari obat berdasarkan ID, jika tidak ketemu akan otomatis error 404
    try {
        $obat = Obat::findOrFail($id);
        $obat->delete();
        return response()->json(['success' => true, 'message' => 'Data obat berhasil dihapus.']);
    } catch (\Illuminate\Database\QueryException $e) {
        // Tangkap error jika ada foreign key constraint
        if ($e->errorInfo[1] == 1451) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus! Obat ini sudah memiliki riwayat transaksi penjualan.'
            ], 409); // 409 Conflict
        }
        // Untuk error database lainnya
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada database.'], 500);
    }
}

// =================================================================
    // =========== [NEW] HALAMAN KELOLA SUPPLIER (CRUD) =============
    // =================================================================

    /**
     * Menampilkan daftar semua supplier.
     */
    public function supplierIndex()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Menampilkan formulir untuk membuat supplier baru.
     */
    public function supplierCreate()
    {
        return view('suppliers.create');
    }

    /**
     * Menyimpan supplier baru ke database.
     */
    public function supplierStore(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit supplier.
     */
    public function supplierEdit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Memperbarui data supplier di database.
     */
    public function supplierUpdate(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier,' . $supplier->id,
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')
                         ->with('success', 'Data supplier berhasil diperbarui.');
    }

    /**
     * Menghapus supplier dari database.
     */
    public function supplierDestroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier berhasil dihapus.');
    }



    // =================================================================
    // =========== HALAMAN REKAP & PERAMALAN ==============
    // =================================================================
    
  
 public function showRekapStok(Request $request)
{
    // --- Bagian 1: Mengambil semua input dari user (Tidak ada perubahan) ---
    $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(30)->toDateString());
    $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->toDateString());
    $searchTerm = $request->input('search');
    $selectedObatId = $request->input('obat_id');
     $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
     $semuaObat = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'supplier_id', 'harga_satuan', 'harga_box']);
    // Ambil parameter fuzzy berdasarkan rentang tanggal
    $fuzzyParams = $this->getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir);

    // --- Bagian 2: Membangun Query Utama (DIUBAH KEMBALI) ---
    // ✅ [DIUBAH] Query tidak lagi menggunakan GROUP BY.
    // Ia akan mengambil setiap baris dari tabel 'obat' secara individual.
    $query = Obat::with('supplier');

    // --- Bagian 3: Logika Filter (DIUBAH KEMBALI) ---
    if ($selectedObatId) {
        // Jika sedang dalam mode detail, cari berdasarkan ID unik.
        $query->where('id', $selectedObatId);
    } else {
        // Jika sedang dalam mode daftar, filter berdasarkan stok < 20 dan pencarian.
        $query->where('stok', '<', 20);
        if ($searchTerm) {
            $query->where('nama_obat', 'like', '%' . $searchTerm . '%');
        }
    }

    // Eksekusi query
    $stok_kurang = $query->orderBy('nama_obat', 'asc')->get();
    
    // --- Bagian 4: Proses Perhitungan Fuzzy ---
    $hasilPeramalan = [];
    // ✅ [DIUBAH] Loop sekarang langsung menggunakan model $obat, bukan hasil group.
    foreach ($stok_kurang as $obat) {

        // ✅ [DIUBAH] Menghitung penjualan berdasarkan ID unik obat ($obat->id)
        $inputPenjualan = DetailPenjualan::where('obat_id', $obat->id)
            ->whereHas('penjualan', function($q) use ($tanggalMulai, $tanggalAkhir) {
                $q->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
            })
            ->sum('jumlah');

        // ✅ [DIUBAH] Input stok diambil langsung dari properti stok per baris.
        $inputStok = $obat->stok;
        
        $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok, $fuzzyParams);
        
        $hasilPeramalan[] = [
            'obat_id' => $obat->id,
            'nama_obat' => $obat->nama_obat,
            'supplier' => $obat->supplier->nama_supplier ?? 'N/A',
            'kategori' => $obat->kategori,
            'stok_saat_ini' => $inputStok,
            'total_penjualan_periode' => $inputPenjualan, 
            'rekomendasi_pembelian' => round($rekomendasi),
            'harga_box' => $obat->harga_box,
            'harga_pcs' => $obat->harga_satuan
            
        ];
    }
    
    // --- Bagian 5: Kirim data ke View (Tidak ada perubahan) ---
    return view('perhitungan', compact('hasilPeramalan', 'searchTerm', 'tanggalMulai', 'tanggalAkhir', 'selectedObatId','suppliers','semuaObat'));
}


private function getFuzzyParamsFromDB($tanggalMulai, $tanggalAkhir): array
{
    $queryPenjualan = DetailPenjualan::whereHas('penjualan', function ($query) use ($tanggalMulai, $tanggalAkhir) {
        $query->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir]);
    });

    $minPenjualan = $queryPenjualan->clone()->min('jumlah');
    $maxPenjualan = $queryPenjualan->clone()->max('jumlah');

    $minStok = Obat::whereBetween('updated_at', [$tanggalMulai, $tanggalAkhir])->min('stok');
    $maxStok = Obat::whereBetween('updated_at', [$tanggalMulai, $tanggalAkhir])->max('stok');

    $params['penjualan'] = [$minPenjualan ?? 1, $maxPenjualan ?? 10];
    $params['stok']      = [$minStok ?? 10, $maxStok ?? 50];

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
public function simpanPerhitungan(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'hasil' => 'required|array|min:1',
        'hasil.*.obat_id' => 'required|integer|exists:obats,id',
        'hasil.*.nama_obat' => 'required|string', // Pastikan nama_obat juga dikirim
        'hasil.*.stok_saat_ini' => 'required|integer',
        'hasil.*.total_penjualan_periode' => 'required|integer',
        'hasil.*.rekomendasi_pembelian' => 'required|integer',
    ]);

    DB::beginTransaction();

    try {
        // 1. Siapkan data ringkasan rekomendasi untuk kolom hasil_json
        // Kita akan membuat array yang berisi nama obat dan jumlah rekomendasinya.
        $rekomendasiUntukJson = array_map(function ($item) {
            return [
                'nama_obat' => $item['nama_obat'],
                'rekomendasi' => $item['rekomendasi_pembelian']
            ];
        }, $validated['hasil']);

        // 2. Buat record "header" di tabel 'perhitungans'
        $perhitungan = Perhitungan::create([
            'pegawai_id'          => auth()->id(),
            'tanggal_perhitungan' => now(),
            'hasil_json'          => json_encode($rekomendasiUntukJson) // Simpan ringkasan rekomendasi
        ]);

        // 3. Loop dan simpan data lengkap ke 'detail_perhitungans'
        foreach ($validated['hasil'] as $item) {
            DetailPerhitungan::create([
                'perhitungan_id'          => $perhitungan->id,
                'obat_id'                 => $item['obat_id'],
                'stok_saat_ini'           => $item['stok_saat_ini'],
                'total_penjualan_terakhir'=> $item['total_penjualan_periode'],
                'rekomendasi_pembelian'   => $item['rekomendasi_pembelian'],
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Hasil perhitungan berhasil disimpan!',
            'perhitungan_id' => $perhitungan->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal menyimpan perhitungan: ' . $e->getMessage() . ' at line ' . $e->getLine());
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal saat menyimpan data.'], 500);
    }
}
}

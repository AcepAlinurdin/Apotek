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
    // == FUNGSI UNTUK HALAMAN PENJUALAN / KASIR ==
    // =================================================================

    /**
     * Menampilkan halaman utama penjualan obat dengan daftar obat dan riwayat penjualan.
     * Dipanggil oleh route GET /penjualan.
     */
    public function index()
    {
        // PERBAIKAN: Mengelompokkan obat dan menjumlahkan stok
        $obats = DataObat::select(
                'nama_obat',
                'harga_satuan',
                DB::raw('SUM(qty) as total_stok') // Menjumlahkan kolom qty sebagai total_stok
            )
            ->groupBy('nama_obat', 'harga_satuan') // Mengelompokkan berdasarkan nama dan harga
            ->orderBy('nama_obat', 'asc')
            ->get();

        $riwayatPenjualans = PenjualanObat::orderBy('tanggal', 'desc')
                                          ->orderBy('created_at', 'desc')
                                          ->get();
                                          
        return view('penjualan', compact('obats', 'riwayatPenjualans'));
    }

    protected function processCheckout(array $cartItems): array
    {
        $result = [];
        foreach ($cartItems as $item) {
            // Saat checkout, kita tetap perlu mencari obat spesifik untuk mengurangi stoknya
            $obat = DataObat::where('nama_obat', $item['name'])->firstOrFail();

            if ($obat->qty < $item['quantity']) {
                throw new \Exception("Stok {$obat->nama_obat} tidak mencukupi.");
            }

            $penjualan = PenjualanObat::create([
                'obat_id' => $obat->id,
                'kode_obat' => $obat->kode_obat,
                'nama_obat' => $obat->nama_obat,
                'qty' => $item['quantity'],
                'total_harga' => $obat->harga_satuan * $item['quantity'],
                'tanggal' => Carbon::now()
            ]);

            // Mengurangi stok dari batch obat yang ditemukan
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
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Checkout berhasil']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage().' Stack: '.$e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat checkout: ' . $e->getMessage()], 400);
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


    // =================================================================
    // == FUNGSI UNTUK HALAMAN MASTER DATA (CRUD) ==
    // =================================================================

    /**
     * Menampilkan halaman CRUD dengan semua data obat. (READ)
     */
    public function masterIndex()
    {
        $data_obats = DataObat::orderBy('nama_obat', 'asc')->get();
        return view('master_data', compact('data_obats'));
    }

    /**
     * Menyimpan data obat baru. (CREATE)
     */
    public function masterStore(Request $request)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'kode_obat'    => 'required|string',
            'nama_obat'    => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'supplier'     => 'nullable|string|max:255',
            'stok'         => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'harga_box'    => 'required|numeric|min:0',
        ]);

        DataObat::create([
            'tanggal'      => $request->tanggal,
            'kode_obat'    => $request->kode_obat,
            'nama_obat'    => $request->nama_obat,
            'kategori'     => $request->kategori,
            'supplier'     => $request->supplier,
            'qty'          => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'harga_box'    => $request->harga_box,
        ]);

        return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan!']);
    }

    /**
     * Memperbarui data obat. (UPDATE)
     */
    public function masterUpdate(Request $request, $id)
    {
        $obat = DataObat::findOrFail($id);
        
        $request->validate([
            'tanggal'      => 'required|date',
            'kode_obat'    => 'required|string',
            'nama_obat'    => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'supplier'     => 'nullable|string|max:255',
            'stok'         => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'harga_box'    => 'required|numeric|min:0',
        ]);

        $obat->update([
            'tanggal'      => $request->tanggal,
            'kode_obat'    => $request->kode_obat,
            'nama_obat'    => $request->nama_obat,
            'kategori'     => $request->kategori,
            'supplier'     => $request->supplier,
            'qty'          => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'harga_box'    => $request->harga_box,
        ]);

        return response()->json(['success' => true, 'message' => 'Data obat berhasil diperbarui.']);
    }

    /**
     * Menghapus data obat. (DELETE)
     */
    public function masterDestroy($id)
    {
        $obat = DataObat::findOrFail($id);
        $obat->delete();
        return response()->json(['success' => true, 'message' => 'Data obat berhasil dihapus.']);
    }
    
    // =================================================================
    // == FUNGSI UNTUK HALAMAN PERHITUNGAN FUZZY ==
    // =================================================================
    
    public function showRekapStok()
    {
        $semua_obat = DataObat::withSum('penjualan', 'qty')->orderBy('nama_obat', 'asc')->get();
        $stok_kurang = DataObat::withSum('penjualan', 'qty')->where('qty', '<', 20)->orderBy('qty', 'asc')->get();
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
            $rekomendasi = $this->jalankanMesinFuzzy($inputPenjualan, $inputStok);
            
            $hasilPeramalan[] = [
                'nama_obat' => $obat->nama_obat,
                'kategori' => $obat->kategori,
                'stok_saat_ini' => $inputStok,
                'penjualan_terakhir_input' => $inputPenjualan,
                'total_penjualan' => $obat->penjualan_sum_qty ?? 0,
                'rekomendasi_pembelian' => round($rekomendasi)
            ];
        }
        return view('perhitungan', compact('semua_obat', 'stok_kurang', 'hasilPeramalan'));
    }

    private function jalankanMesinFuzzy($penjualan, $stok) {
        $derajat = $this->fuzzifikasi($penjualan, $stok);
        $kekuatanAturan = $this->evaluasiAturan($derajat);
        return $this->defuzzifikasi($kekuatanAturan);
    }
    
    private function fuzzifikasi($penjualan, $stok){$params=['penjualan_turun'=>[10,18],'penjualan_naik'=>[10,18],'stok_sedikit'=>[15,25],'stok_banyak'=>[15,25],];return ['penjualan_naik'=>$this->hitungNaik($penjualan,$params['penjualan_naik'][0],$params['penjualan_naik'][1]),'penjualan_turun'=>$this->hitungTurun($penjualan,$params['penjualan_turun'][0],$params['penjualan_turun'][1]),'stok_banyak'=>$this->hitungNaik($stok,$params['stok_banyak'][0],$params['stok_banyak'][1]),'stok_sedikit'=>$this->hitungTurun($stok,$params['stok_sedikit'][0],$params['stok_sedikit'][1]),];}
    private function evaluasiAturan($derajat){$alpha1=min($derajat['penjualan_naik'],$derajat['stok_banyak']);$alpha2=min($derajat['penjualan_naik'],$derajat['stok_sedikit']);$alpha3=min($derajat['penjualan_turun'],$derajat['stok_banyak']);$alpha4=min($derajat['penjualan_turun'],$derajat['stok_sedikit']);return ['R1'=>$alpha1,'R2'=>$alpha2,'R3'=>$alpha3,'R4'=>$alpha4,];}
    private function defuzzifikasi($kekuatanAturan){$kekuatanBerkurang=max($kekuatanAturan['R1'],$kekuatanAturan['R3']);$kekuatanBertambah=max($kekuatanAturan['R2'],$kekuatanAturan['R4']);$params=['pembelian_berkurang'=>[5,15],'pembelian_bertambah'=>[10,20],];$pembilang=0;$penyebut=0;for($z=0;$z<=35;$z++){$miuBerkurang=$this->hitungTurun($z,$params['pembelian_berkurang'][0],$params['pembelian_berkurang'][1]);$miuBertambah=$this->hitungNaik($z,$params['pembelian_bertambah'][0],$params['pembelian_bertambah'][1]);$areaBerkurang=min($kekuatanBerkurang,$miuBerkurang);$areaBertambah=min($kekuatanBertambah,$miuBertambah);$areaGabungan=max($areaBerkurang,$areaBertambah);$pembilang+=$z*$areaGabungan;$penyebut+=$areaGabungan;}if($penyebut==0)return 0;return $pembilang/$penyebut;}
    private function hitungTurun($x,$bawah,$atas){if($x<=$bawah)return 1;if($x>=$atas)return 0;return($atas-$x)/($atas-$bawah);}
    private function hitungNaik($x,$bawah,$atas){if($x<=$bawah)return 0;if($x>=$atas)return 1;return($x-$bawah)/($atas-$bawah);}
}

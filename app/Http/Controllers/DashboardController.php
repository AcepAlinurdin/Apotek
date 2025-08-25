<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\DetailPenjualan;
use App\Models\DetailPembelian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- Data untuk Laporan Penjualan (yang hilang) ---
        $filterTanggal = $request->input('tanggal', '');
        $filterBulan = $request->input('bulan', date('m'));
        $filterTahun = $request->input('tahun', date('Y'));

        $penjualanQuery = DetailPenjualan::join('obats', 'detail_penjualans.obat_id', '=', 'obats.id')
            ->join('penjualans', 'detail_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select('obats.nama_obat', DB::raw('SUM(detail_penjualans.jumlah) as jumlah'), DB::raw('SUM(detail_penjualans.subtotal) as subtotal'));

        if ($filterTanggal) {
            $penjualanQuery->whereDate('penjualans.tanggal_penjualan', $filterTanggal);
            $laporanPenjualanTitle = 'Laporan Penjualan Tanggal ' . Carbon::parse($filterTanggal)->format('d F Y');
        } else {
            $penjualanQuery->whereMonth('penjualans.tanggal_penjualan', $filterBulan)
                           ->whereYear('penjualans.tanggal_penjualan', $filterTahun);
            $laporanPenjualanTitle = 'Laporan Penjualan Bulan ' . Carbon::create()->month($filterBulan)->format('F') . ' ' . $filterTahun;
        }

        $penjualanData = $penjualanQuery->groupBy('obats.nama_obat')->orderBy('subtotal', 'desc')->get();
        $totalPendapatan = $penjualanData->sum('subtotal');

        // --- Data untuk Laporan Pembelian (yang hilang) ---
        $filterPembelianMulai = $request->input('pembelian_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $filterPembelianAkhir = $request->input('pembelian_akhir', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $pembelianQuery = DetailPembelian::join('obats', 'detail_pembelians.obat_id', '=', 'obats.id')
            ->join('pembelians', 'detail_pembelians.pembelian_id', '=', 'pembelians.id')
            ->whereBetween('pembelians.tanggal_pembelian', [$filterPembelianMulai, $filterPembelianAkhir])
            ->select('pembelians.tanggal_pembelian', 'obats.nama_obat', 'detail_pembelians.jumlah', 'pembelians.status');

        $pembelianData = $pembelianQuery->orderBy('pembelians.tanggal_pembelian', 'desc')->get();
        $totalPengeluaran = Pembelian::whereBetween('tanggal_pembelian', [$filterPembelianMulai, $filterPembelianAkhir])->sum('total_harga');
        $laporanPembelianTitle = 'Laporan Pembelian ' . Carbon::parse($filterPembelianMulai)->format('d M Y') . ' - ' . Carbon::parse($filterPembelianAkhir)->format('d M Y');

        // --- Data BARU untuk Tabel Belum Lunas ---
        $pembelianBelumLunas = Pembelian::where('status', 'Belum Lunas')
                                ->with('supplier')
                                ->orderBy('tanggal_pembelian', 'desc')
                                ->get();
        
        // --- Kirim SEMUA data ke view ---
        return view('dashboard', compact(
            'penjualanData', 'totalPendapatan', 'laporanPenjualanTitle', 'filterTanggal', 'filterBulan', 'filterTahun',
            'pembelianData', 'totalPengeluaran', 'laporanPembelianTitle', 'filterPembelianMulai', 'filterPembelianAkhir',
            'pembelianBelumLunas'
        ));
    }
}
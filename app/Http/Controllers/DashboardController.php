<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan laporan dinamis
     * berdasarkan peran pengguna dan filter tanggal.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $viewData = []; // Menyiapkan array untuk dikirim ke view

        // Hanya siapkan data laporan jika role-nya adalah admin atau kepala apotek
        if ($user->hasRole('admin') || $user->hasRole('kepala apotek')) {
            
            // =============================================
            // LOGIKA UNTUK LAPORAN PENJUALAN
            // =============================================
            $queryPenjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', '=', 'penjualans.id')
                ->join('obats', 'detail_penjualans.obat_id', '=', 'obats.id');

            $laporanPenjualanTitle = 'Laporan Penjualan Hari Ini';
            $tanggal = $request->input('tanggal');
            $bulan = $request->input('bulan', date('m'));
            $tahun = $request->input('tahun', date('Y'));

            if ($tanggal) {
                // Jika ada filter tanggal spesifik, gunakan itu
                $queryPenjualan->whereDate('penjualans.tanggal_penjualan', $tanggal);
                $laporanPenjualanTitle = 'Laporan Penjualan Tanggal ' . Carbon::parse($tanggal)->format('d F Y');
            } else {
                // Jika tidak, gunakan filter bulan dan tahun (default bulan ini)
                $queryPenjualan->whereMonth('penjualans.tanggal_penjualan', $bulan)
                               ->whereYear('penjualans.tanggal_penjualan', $tahun);
                $laporanPenjualanTitle = 'Laporan Penjualan Bulan ' . Carbon::create()->month($bulan)->format('F') . ' ' . $tahun;
            }

            $penjualanData = $queryPenjualan
                ->select('obats.nama_obat', 'detail_penjualans.jumlah', 'detail_penjualans.subtotal')
                ->get();

            // Memasukkan data penjualan ke array viewData
            $viewData['penjualanData'] = $penjualanData;
            $viewData['totalPendapatan'] = $penjualanData->sum('subtotal');
            $viewData['laporanPenjualanTitle'] = $laporanPenjualanTitle;
            $viewData['filterTanggal'] = $tanggal;
            $viewData['filterBulan'] = $bulan;
            $viewData['filterTahun'] = $tahun;


            // =============================================
            // LOGIKA UNTUK LAPORAN PEMBELIAN
            // =============================================
            $tanggalMulaiPembelian = $request->input('pembelian_mulai', Carbon::now()->subDays(6)->toDateString());
            $tanggalAkhirPembelian = $request->input('pembelian_akhir', Carbon::now()->toDateString());
            
            $pembelianData = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', '=', 'pembelians.id')
                ->join('obats', 'detail_pembelians.obat_id', '=', 'obats.id')
                ->whereBetween('pembelians.tanggal_pembelian', [$tanggalMulaiPembelian, $tanggalAkhirPembelian])
                ->select(
                    'pembelians.tanggal_pembelian', 
                    'obats.nama_obat', 
                    'detail_pembelians.jumlah', 
                    'pembelians.status',
                    DB::raw('detail_pembelians.jumlah * detail_pembelians.harga_beli_satuan as subtotal') 
                )
                ->orderBy('pembelians.tanggal_pembelian', 'desc')
                ->get();
            
            $laporanPembelianTitle = 'Laporan Pembelian (' . Carbon::parse($tanggalMulaiPembelian)->format('d/m/y') . ' - ' . Carbon::parse($tanggalAkhirPembelian)->format('d/m/y') . ')';
                
            // Memasukkan data pembelian ke array viewData
            $viewData['pembelianData'] = $pembelianData;
            $viewData['totalPengeluaran'] = $pembelianData->sum('subtotal');
            $viewData['laporanPembelianTitle'] = $laporanPembelianTitle;
            $viewData['filterPembelianMulai'] = $tanggalMulaiPembelian;
            $viewData['filterPembelianAkhir'] = $tanggalAkhirPembelian;
        }

        // Mengirim semua data yang terkumpul ke view 'dashboard'
        return view('dashboard', $viewData);
    }
}
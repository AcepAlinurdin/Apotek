<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    /**
     * Menampilkan daftar semua pasien dengan paginasi.
     */
    public function index()
    {
        // Mengambil semua data pasien, diurutkan dari yang terbaru, dan dibagi per 10 data per halaman
        $pasiens = Pasien::latest()->paginate(10);
        
        // PERBAIKAN: Mengarahkan ke view yang benar: 'pasien.index'
        return view('pasien', compact('pasiens'));
    }

    /**
     * Menampilkan formulir untuk membuat pasien baru.
     */
    public function create()
    {
        // Mengarahkan ke view 'pasien.create' yang berisi form
        return view('pasien.create');
    }

    /**
     * Menyimpan data pasien baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi input dari formulir
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nomor_ktp' => 'nullable|string|size:16|unique:pasiens,nomor_ktp',
            'catatan_alergi' => 'nullable|string',
            'riwayat_penyakit'=>'nullable|string',
        ]);

        // Jika validasi lolos, buat data pasien baru
        Pasien::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pasien.index')
                         ->with('success', 'Data pasien baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik dari satu pasien.
     * (Route model binding: Laravel otomatis mencari Pasien berdasarkan ID)
     */
    public function show(Pasien $pasien)
    {
        // Anda bisa membuat view 'pasien.show' jika butuh halaman detail terpisah
        return view('pasien.show', compact('pasien'));
    }

    /**
     * Menampilkan formulir untuk mengedit data pasien.
     */
    public function edit(Pasien $pasien)
    {
        // Mengarahkan ke view 'pasien.edit' dengan membawa data pasien yang akan diedit
        return view('pasien.edit', compact('pasien'));
    }

    /**
     * Memperbarui data pasien di dalam database.
     */
    public function update(Request $request, Pasien $pasien)
    {
        // Validasi input, dengan pengecualian untuk nomor KTP unik milik pasien itu sendiri
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nomor_ktp' => 'nullable|string|size:16|unique:pasiens,nomor_ktp,' . $pasien->id,
            'catatan_alergi' => 'nullable|string',
            'riwayat_penyakit'=>'nullable|string',
        ]);

        // Update data pasien dengan data baru dari form
        $pasien->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pasien.index')
                         ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Menghapus data pasien dari database.
     */
    public function destroy(Pasien $pasien)
    {
        // Hapus data pasien
        $pasien->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pasien.index')
                         ->with('success', 'Data pasien berhasil dihapus.');
    }

    // --- FUNGSI API UNTUK TRANSAKSI (YANG SUDAH ADA) ---

    /**
     * API untuk mencari pasien secara real-time.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $pasiens = Pasien::where('nama_lengkap', 'LIKE', "%{$query}%")
                         ->orWhere('nomor_telepon', 'LIKE', "%{$query}%")
                         ->limit(10)
                         ->get(['id', 'nama_lengkap', 'nomor_telepon']);
        
        return response()->json($pasiens);
    }

    /**
     * API untuk mengambil riwayat transaksi pasien.
     */
    public function getHistory(Pasien $pasien)
    {
        // Ambil riwayat penjualan yang terkait dengan pasien ini
        $riwayat = DetailPenjualan::whereHas('penjualan', function ($query) use ($pasien) {
            $query->where('id_pasien', $pasien->id);
        })->with(['obat', 'penjualan'])->latest()->get();

        return response()->json([
            'pasien' => $pasien,
            'riwayat' => $riwayat,
        ]);
    }
}



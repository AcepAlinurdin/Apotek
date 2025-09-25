<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PaketObat;
use App\Models\DetailPaketObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketObatController extends Controller
{
    /**
     * Menampilkan halaman manajemen paket obat.
     */
    public function index()
    {
        // Mengambil semua paket beserta detail dan data obat terkait (eager loading)
        $pakets = PaketObat::with('detailPaketObats.obat')->latest()->paginate(10);
        
        // Mengambil semua data obat untuk digunakan di form tambah/edit
        $obats = Obat::orderBy('nama_obat')->get();

        return view('paket', compact('pakets', 'obats'));
    }

    /**
     * Menyimpan paket obat baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255|unique:paket_obats,nama_paket',
            'obats' => 'required|array|min:1',
            'obats.*.id' => 'required|exists:obats,id',
            'obats.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $paket = PaketObat::create(['nama_paket' => $validated['nama_paket']]);

            foreach ($validated['obats'] as $obat) {
                DetailPaketObat::create([
                    'paket_obat_id' => $paket->id,
                    'obat_id' => $obat['id'],
                    'jumlah' => $obat['jumlah'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage());
        }

        return redirect()->route('paket-obat.index')->with('success', 'Paket obat baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data paket obat.
     */
    public function update(Request $request, PaketObat $paketObat)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255|unique:paket_obats,nama_paket,' . $paketObat->id,
            'obats' => 'required|array|min:1',
            'obats.*.id' => 'required|exists:obats,id',
            'obats.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update nama paket
            $paketObat->update(['nama_paket' => $validated['nama_paket']]);

            // Hapus detail lama dan buat yang baru
            $paketObat->detailPaketObats()->delete();

            foreach ($validated['obats'] as $obat) {
                DetailPaketObat::create([
                    'paket_obat_id' => $paketObat->id,
                    'obat_id' => $obat['id'],
                    'jumlah' => $obat['jumlah'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui paket: ' . $e->getMessage());
        }

        return redirect()->route('paket-obat.index')->with('success', 'Paket obat berhasil diperbarui.');
    }

    /**
     * Menghapus paket obat.
     */
    public function destroy(PaketObat $paketObat)
    {
        $paketObat->delete();
        return redirect()->route('paket-obat.index')->with('success', 'Paket obat berhasil dihapus.');
    }
}

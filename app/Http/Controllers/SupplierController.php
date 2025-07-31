<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Menampilkan daftar supplier dan formulir.
     */
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        
        // [FIXED] Mengirim variabel '$supplier' kosong untuk mode 'create'
        // Ini akan memastikan $supplier->exists selalu bisa dievaluasi tanpa error.
        return view('suppliers.index', [
            'suppliers' => $suppliers, 
            'supplier' => new Supplier()
        ]);
    }

    /**
     * Menyimpan supplier baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255|unique:suppliers,nama_supplier',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit data supplier di halaman index.
     */
    public function edit(Supplier $supplier)
    {
        $suppliers = Supplier::latest()->paginate(10);
        // Mengirim data supplier yang akan diedit ke view
        return view('suppliers.index', compact('suppliers', 'supplier'));
    }

    /**
     * Memperbarui data supplier di dalam database.
     */
    public function update(Request $request, Supplier $supplier)
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
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier berhasil dihapus.');
    }
}

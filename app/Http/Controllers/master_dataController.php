<?php

namespace App\Http\Controllers;

use App\Models\DataObat;
use App\Models\PenjualanObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class master_dataController extends Controller
{
    public function showMasterData()
    {

        $data_obats = DataObat::orderBy('nama_obat', 'asc')->get();

        return view('master_data', compact('data_obats'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama_obat' => 'required|string|max:255',
                'kategori' => 'required|string|max:255',
                'supplier' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'harga_satuan' => 'required|numeric|min:0',
                'harga_box' => 'nullable|numeric|min:0',
            ]);

            $supplier = Supplier::where('nama_supplier', $validatedData['supplier'])->first();
            if (!$supplier) {
                return response()->json(['success' => false, 'message' => 'Supplier tidak ditemukan.'], 404);
            }

            $existingObat = Obat::where('nama_obat', $validatedData['nama_obat'])->first();

            if ($existingObat) {
                if ($existingObat->supplier_id === $supplier->id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'nama_obat' => ['Obat dengan nama dan supplier yang sama sudah ada.']
                    ]);
                }
                $existingObat->update(['supplier_id' => $supplier->id]);
                $obat = $existingObat;
            } else {
                $obat = Obat::create([
                    'tanggal' => $validatedData['tanggal'],
                    'nama_obat' => $validatedData['nama_obat'],
                    'kategori' => $validatedData['kategori'],
                    'supplier_id' => $supplier->id,
                    'harga_satuan' => $validatedData['harga_satuan'],
                    'harga_box' => $validatedData['harga_box'],
                ]);
            }
            if ($validatedData['stok'] > 0) {
                Stok::create([
                    'obat_id' => $obat->id,
                    'tipe_pergerakan' => 'masuk',
                    'jumlah' => $validatedData['stok'],
                    'tanggal' => $validatedData['tanggal'],
                    'keterangan' => 'Stok awal saat pendaftaran obat',
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data obat berhasil disimpan.', 'data' => $obat]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}

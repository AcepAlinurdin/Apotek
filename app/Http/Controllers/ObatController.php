<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\DataObat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        return response()->json(DataObat::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|unique:obats',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        $obat = ObDataObatat::create($request->all());
        return response()->json($obat, 201);
    }

    public function show($id)
    {
        $obat = DataObat::find($id);
        if (!$obat) return response()->json(['message' => 'Obat tidak ditemukan'], 404);
        return response()->json($obat, 200);
    }

    public function update(Request $request, $id)
    {
        $obat = DataObat::find($id);
        if (!$obat) return response()->json(['message' => 'Obat tidak ditemukan'], 404);

        $obat->update($request->all());
        return response()->json($obat, 200);
    }

    public function destroy($id)
    {
        $obat = DataObat::find($id);
        if (!$obat) return response()->json(['message' => 'Obat tidak ditemukan'], 404);

        $obat->delete();
        return response()->json(['message' => 'Obat berhasil dihapus'], 200);
    }
}

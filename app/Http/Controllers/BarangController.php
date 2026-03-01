<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:50',
            'harga' => 'required|numeric',
        ]);

        Barang::create($request->all());
        return redirect()->back()->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
{
    $barang = Barang::findOrFail($id);
    return response()->json($barang);
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|max:50',
        'harga' => 'required|numeric',
    ]);

    $barang = Barang::findOrFail($id);
    $barang->update([
        'nama' => $request->nama,
        'harga' => $request->harga,
    ]);

    return redirect()->back()->with('success', 'Data buku berhasil diperbarui!');
}
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus!');
    }

    public function cetakLabel(Request $request)
    {
        $ids = $request->selected_ids;
        if (!$ids) return back()->with('error', 'Pilih barang dulu!');

        $barangs = Barang::whereIn('id_barang', $ids)->get();
        $skip = (($request->y - 1) * 5) + ($request->x - 1);

        $pdf = Pdf::loadView('barang.cetak_pdf', compact('barangs', 'skip'));
        return $pdf->setPaper('a4', 'portrait')->stream('label-umkm.pdf');
    }
    
}
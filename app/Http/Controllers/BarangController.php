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

        Barang::create($request->only(['nama','harga']));
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
        $barang->update($request->only(['nama','harga']));

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
    $request->validate([
        'selected_ids' => 'required|array',
        'x' => 'required|numeric',
        'y' => 'required|numeric',
    ]);

    $barangs = Barang::whereIn('id_barang', $request->selected_ids)->get();

    if ($barangs->isEmpty()) {
        return back()->with('error', 'Data tidak ditemukan.');
    }

    $skip = (($request->y - 1) * 5) + ($request->x - 1);

    $pdf = Pdf::loadView('barang.cetak_pdf', [
        'barangs' => $barangs,
        'skip' => $skip
    ]);

    return $pdf->setPaper('a4', 'portrait')
               ->stream('label-harga.pdf');
}
}
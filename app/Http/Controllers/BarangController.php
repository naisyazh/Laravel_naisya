<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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

        Barang::create([
            'id_barang' => $this->generateBarangId(),
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
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

        return redirect()->back()->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus!');
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
            'skip' => $skip,
        ]);

        return $pdf->setPaper('a4', 'portrait')
            ->stream('label-harga.pdf');
    }

    private function generateBarangId(): string
    {
        $lastBarang = Barang::query()
            ->orderByDesc('id_barang')
            ->value('id_barang');

        if (! $lastBarang) {
            return 'BRG00001';
        }

        $nextNumber = (int) Str::of($lastBarang)->replace('BRG', '')->toString() + 1;

        return 'BRG' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }
}

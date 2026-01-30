<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    // Halaman Daftar Paket
    public function index()
    {
        $pakets = Paket::all();
        return view('paket.index', compact('pakets'));
    }

    // Halaman Form Tambah
    public function create()
    {
        return view('paket.create');
    }

    // Proses Simpan Paket Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'durasi_menit' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        Paket::create([
            'nama_paket' => $request->nama_paket,
            'durasi_menit' => $request->durasi_menit,
            'harga' => $request->harga,
        ]);

        return redirect()->route('paket.index')->with('success', 'Paket berhasil ditambahkan!');
    }

    // Halaman Form Edit
    public function edit($id)
    {
        $paket = Paket::findOrFail($id);
        return view('paket.edit', compact('paket'));
    }

    // Proses Update Paket
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'durasi_menit' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        $paket = Paket::findOrFail($id);
        $paket->update([
            'nama_paket' => $request->nama_paket,
            'durasi_menit' => $request->durasi_menit,
            'harga' => $request->harga,
        ]);

        return redirect()->route('paket.index')->with('success', 'Paket berhasil diperbarui!');
    }

    // Hapus Paket
    public function destroy($id)
    {
        $paket = Paket::findOrFail($id);
        $paket->delete();
        return redirect()->route('paket.index')->with('success', 'Paket berhasil dihapus!');
    }
}
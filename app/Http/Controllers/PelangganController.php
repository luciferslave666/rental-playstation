<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    // Tampilkan Daftar Pelanggan
    public function index()
    {
        $pelanggans = Pelanggan::latest()->paginate(10);
        return view('pelanggan.index', compact('pelanggans'));
    }

    // Tampilkan Form Tambah
    public function create()
    {
        return view('pelanggan.create');
    }

    // Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100', // Sesuai PDF hal 6
            'no_hp' => 'required|numeric', // Sesuai PDF hal 6
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    // Tampilkan Form Edit
    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    // Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp' => 'required|numeric',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan diperbarui');
    }

    // Hapus Data
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Cek dulu apakah pelanggan ini punya riwayat transaksi?
        // Kalau ada, jangan dihapus sembarangan (bisa error foreign key)
        if($pelanggan->transaksi()->exists()){
            return back()->with('error', 'Gagal! Pelanggan ini memiliki riwayat transaksi.');
        }

        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan dihapus');
    }
}
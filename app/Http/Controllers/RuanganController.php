<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Konsol;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    // Tampilkan Daftar Ruangan
    public function index()
    {
        // Kita ambil data ruangan beserta info konsolnya
        $ruangans = Ruangan::with('konsol')->get();
        return view('ruangan.index', compact('ruangans'));
    }

    // Form Tambah
    public function create()
    {
        return view('ruangan.create');
    }

    // Simpan Data (Ruangan + Konsol)
    public function store(Request $request)
    {
        // Validasi Input Sesuai PDF
        $request->validate([
            'nomor_ruangan' => 'required|string|max:10', // [cite: 51]
            'tipe_ruangan' => 'required|in:Reguler,VIP', // [cite: 51]
            'tarif_per_jam' => 'required|numeric|min:0', // [cite: 51]
            'deskripsi_fasilitas' => 'nullable|string', // [cite: 51]
            'seri_konsol' => 'required|string|max:50', // [cite: 54]
        ]);

        // 1. Simpan Data Ruangan dulu
        $ruangan = Ruangan::create([
            'nomor_ruangan' => $request->nomor_ruangan,
            'tipe_ruangan' => $request->tipe_ruangan,
            'tarif_per_jam' => $request->tarif_per_jam,
            'deskripsi_fasilitas' => $request->deskripsi_fasilitas,
        ]);

        // 2. Simpan Data Konsol (Otomatis terhubung via id_ruangan)
        Konsol::create([
            'id_ruangan' => $ruangan->id_ruangan, // Ambil ID dari ruangan yg baru dibuat
            'seri_konsol' => $request->seri_konsol
        ]);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan & Konsol berhasil ditambahkan');
    }

    // Form Edit
    public function edit($id)
    {
        $ruangan = Ruangan::with('konsol')->findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    // Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_ruangan' => 'required|string|max:10',
            'tipe_ruangan' => 'required|in:Reguler,VIP',
            'tarif_per_jam' => 'required|numeric',
            'seri_konsol' => 'required|string',
        ]);

        $ruangan = Ruangan::findOrFail($id);

        // Update Tabel Ruangan
        $ruangan->update([
            'nomor_ruangan' => $request->nomor_ruangan,
            'tipe_ruangan' => $request->tipe_ruangan,
            'tarif_per_jam' => $request->tarif_per_jam,
            'deskripsi_fasilitas' => $request->deskripsi_fasilitas,
        ]);

        // Update Tabel Konsol (Jika ada, update. Jika belum ada, create)
        if ($ruangan->konsol) {
            $ruangan->konsol->update(['seri_konsol' => $request->seri_konsol]);
        } else {
            Konsol::create([
                'id_ruangan' => $ruangan->id_ruangan,
                'seri_konsol' => $request->seri_konsol
            ]);
        }

        return redirect()->route('ruangan.index')->with('success', 'Data Ruangan diperbarui');
    }

    // Hapus Data
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        // Cek transaksi aktif agar tidak error
        if($ruangan->transaksi()->exists()){
            return back()->with('error', 'Gagal! Ruangan ini memiliki riwayat transaksi.');
        }

        // Konsol akan otomatis terhapus karena kita set onDelete('cascade') di migration
        $ruangan->delete();
        
        return redirect()->route('ruangan.index')->with('success', 'Ruangan dihapus');
    }
}
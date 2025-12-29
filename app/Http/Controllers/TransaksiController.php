<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth; // Untuk ambil ID user yang login
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Menampilkan Form Tambah Transaksi
    public function create()
    {
        // 1. Ambil Data Pelanggan untuk Dropdown
        $pelanggans = Pelanggan::all();

        // 2. Ambil Hanya Ruangan yang KOSONG (Available)
        // Logika: Cari ruangan yang TIDAK punya transaksi aktif (waktu_selesai masih NULL)
        $ruangans = Ruangan::whereDoesntHave('transaksi', function ($query) {
            $query->whereNull('waktu_selesai');
        })->get();

        return view('transaksi.create', compact('pelanggans', 'ruangans'));
    }

    // Menyimpan Data Transaksi Baru (Mulai Main)
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
        ]);

        // Simpan ke Database
        Transaksi::create([
            'id_user' => Auth::id(), // Mengambil ID User yang sedang login (Fallback ke 1 jika null sementara)
            'id_pelanggan' => $request->id_pelanggan,
            'id_ruangan' => $request->id_ruangan,
            'waktu_mulai' => Carbon::now(), // Waktu saat tombol ditekan
            'waktu_selesai' => null, // Masih main
            'total_biaya' => 0,
            'status_pembayaran' => 'Belum Lunas'
        ]);

        // Redirect kembali ke Dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Sesi sewa berhasil dimulai!');
    }
    // Menampilkan Detail Transaksi (Checkout Page)
public function show($id_transaksi)
{
    // Ambil transaksi beserta relasinya
    $transaksi = Transaksi::with(['pelanggan', 'ruangan', 'user'])->findOrFail($id_transaksi);

    // Hitung Durasi & Biaya Real-time (Untuk Preview)
    $waktuMulai = Carbon::parse($transaksi->waktu_mulai);
    $waktuSekarang = Carbon::now();
    
    // Hitung selisih durasi
    $durasiJam = $waktuMulai->diffInHours($waktuSekarang);
    $durasiMenit = $waktuMulai->diffInMinutes($waktuSekarang) % 60;
    
    // Hitung Total Biaya (Pembulatan ke atas per jam atau hitung per menit)
    // Logika Sederhana: Hitung per jam (jika main 1 jam 10 menit dihitung 2 jam)
    $totalJamBayar = ceil($waktuMulai->diffInMinutes($waktuSekarang) / 60);
    // Jika baru mulai (< 1 jam), minimal bayar 1 jam
    $totalJamBayar = $totalJamBayar < 1 ? 1 : $totalJamBayar;
    
    $estimasiBiaya = $totalJamBayar * $transaksi->ruangan->tarif_per_jam;

    return view('transaksi.show', compact('transaksi', 'durasiJam', 'durasiMenit', 'estimasiBiaya', 'totalJamBayar'));
}

// Proses Selesai Main (Checkout)
    public function complete($id_transaksi)
    {
        $transaksi = Transaksi::findOrFail($id_transaksi);

        // Validasi: Jangan checkout kalau sudah lunas
        if($transaksi->status_pembayaran == 'Lunas') {
            return redirect()->route('dashboard')->with('error', 'Transaksi ini sudah selesai!');
        }

        // 1. Set Waktu Selesai
        $waktuSelesai = Carbon::now();
        
        // 2. Hitung Final Biaya
        $waktuMulai = Carbon::parse($transaksi->waktu_mulai);
        $totalJamBayar = ceil($waktuMulai->diffInMinutes($waktuSelesai) / 60);
        $totalJamBayar = $totalJamBayar < 1 ? 1 : $totalJamBayar;
        
        $totalBiaya = $totalJamBayar * $transaksi->ruangan->tarif_per_jam;

        // 3. Update Database
        $transaksi->update([
            'waktu_selesai' => $waktuSelesai,
            'total_biaya' => $totalBiaya,
            'status_pembayaran' => 'Lunas' // Sesuai PDF hal 7 [cite: 132]
        ]);

        return redirect()->route('dashboard')->with('success', "Checkout Berhasil! Total Tagihan: Rp " . number_format($totalBiaya));
    }
    // Menampilkan Riwayat Semua Transaksi
    public function index()
    {
        // Ambil semua transaksi, urutkan dari yang terbaru
        $semuaTransaksi = Transaksi::with(['user', 'pelanggan', 'ruangan'])
            ->latest() // Urutkan created_at DESC
            ->paginate(10); // Batasi 10 per halaman

        return view('   transaksi.index', compact('semuaTransaksi'));
    }
}
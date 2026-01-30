<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\Paket;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // 1. Tampilkan Riwayat Transaksi
    public function index()
    {
        $semuaTransaksi = Transaksi::with(['user', 'pelanggan', 'ruangan'])
            ->latest()
            ->paginate(10);

        return view('transaksi.index', compact('semuaTransaksi'));
    }

    // 2. Tampilkan Form Transaksi Baru
    public function create()
    {
        // Ambil data pelanggan
        $pelanggans = Pelanggan::all();

        // Ambil data paket
        $pakets = Paket::all(); 

        // Ambil hanya ruangan yang KOSONG
        $ruangans = Ruangan::whereDoesntHave('transaksi', function ($query) {
            $query->whereNull('waktu_selesai');
        })->get();

        return view('transaksi.create', compact('pelanggans', 'ruangans', 'pakets'));
    }

    // 3. Simpan Transaksi (Logika Utama)
    public function store(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            
            // Validasi Kondisional: Pelanggan
            'tipe_pelanggan' => 'required|in:lama,baru',
            'id_pelanggan' => 'required_if:tipe_pelanggan,lama',
            'new_nama' => 'required_if:tipe_pelanggan,baru',
            'new_no_hp' => 'required_if:tipe_pelanggan,baru',

            // Validasi Billing (Opsional semua karena defaultnya Open Billing)
            'durasi_custom' => 'nullable|numeric|min:1',
            'id_paket' => 'nullable|exists:paket,id_paket',
        ]);

        // B. Tentukan ID Pelanggan (Lama atau Baru?)
        $finalIdPelanggan = null;

        if ($request->tipe_pelanggan == 'baru') {
            // Buat Pelanggan Baru
            $pelangganBaru = Pelanggan::create([
                'nama_pelanggan' => $request->new_nama,
                'no_hp' => $request->new_no_hp
            ]);
            $finalIdPelanggan = $pelangganBaru->id_pelanggan;
        } else {
            // Pakai Pelanggan Lama
            $finalIdPelanggan = $request->id_pelanggan;
        }

        // C. Tentukan Logika Waktu & Biaya (Custom vs Paket vs Open)
        $ruangan = Ruangan::findOrFail($request->id_ruangan);
        $waktuMulai = Carbon::now();
        $waktuSelesai = null;
        $totalBiaya = 0;
        $idPaket = null;

        if ($request->filled('durasi_custom')) {
            // KASUS 1: CUSTOM DURATION
            $durasiJam = $request->durasi_custom;
            $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam);
            $totalBiaya = $durasiJam * $ruangan->tarif_per_jam;
        
        } elseif ($request->filled('id_paket')) {
            // KASUS 2: PAKET HEMAT
            $paket = Paket::findOrFail($request->id_paket);
            $waktuSelesai = $waktuMulai->copy()->addMinutes($paket->durasi_menit);
            $totalBiaya = $paket->harga;
            $idPaket = $paket->id_paket;

        } else {
            // KASUS 3: OPEN BILLING (Default)
            // Waktu selesai & biaya dihitung nanti saat checkout
        }

        // D. Simpan ke Database
        Transaksi::create([
            'id_user' => Auth::id(), // ID Operator yang login
            'id_pelanggan' => $finalIdPelanggan,
            'id_ruangan' => $request->id_ruangan,
            'id_paket' => $idPaket,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'total_biaya' => $totalBiaya,
            'status_pembayaran' => 'Belum Lunas'
        ]);

        return redirect()->route('dashboard')->with('success', 'Sesi sewa berhasil dimulai!');
    }

    // 4. Halaman Detail / Checkout
    public function show($id_transaksi)
    {
        $transaksi = Transaksi::with(['pelanggan', 'ruangan', 'user', 'paket'])->findOrFail($id_transaksi);

        $waktuMulai = Carbon::parse($transaksi->waktu_mulai);
        $waktuSekarang = Carbon::now();
        
        $durasiJam = $waktuMulai->diffInHours($waktuSekarang);
        $durasiMenit = $waktuMulai->diffInMinutes($waktuSekarang) % 60;
        
        // Logika Tampilan Estimasi Biaya
        if($transaksi->total_biaya > 0) {
            // Jika biaya sudah diset di awal (Paket / Custom), tampilkan itu
            $estimasiBiaya = $transaksi->total_biaya;
            
            // Hitung durasi berdasarkan target selesai
            if($transaksi->waktu_selesai) {
                $selesai = Carbon::parse($transaksi->waktu_selesai);
                $totalJamBayar = ceil($waktuMulai->diffInMinutes($selesai) / 60);
            } else {
                $totalJamBayar = ceil($waktuMulai->diffInMinutes($waktuSekarang) / 60);
            }
        } else {
            // Jika Open Billing (Biaya 0), hitung real-time
            $totalJamBayar = ceil($waktuMulai->diffInMinutes($waktuSekarang) / 60);
            $totalJamBayar = $totalJamBayar < 1 ? 1 : $totalJamBayar;
            $estimasiBiaya = $totalJamBayar * $transaksi->ruangan->tarif_per_jam;
        }

        return view('transaksi.show', compact('transaksi', 'durasiJam', 'durasiMenit', 'estimasiBiaya', 'totalJamBayar'));
    }

    // 5. Proses Checkout (Stop & Bayar)
    public function complete($id_transaksi)
    {
        $transaksi = Transaksi::findOrFail($id_transaksi);

        if($transaksi->status_pembayaran == 'Lunas') {
            return redirect()->route('dashboard')->with('error', 'Transaksi ini sudah selesai!');
        }

        // Jika Open Billing (total_biaya masih 0), hitung & kunci harga sekarang
        if($transaksi->total_biaya == 0) {
            $waktuSelesai = Carbon::now();
            $waktuMulai = Carbon::parse($transaksi->waktu_mulai);
            
            $totalJamBayar = ceil($waktuMulai->diffInMinutes($waktuSelesai) / 60);
            $totalJamBayar = $totalJamBayar < 1 ? 1 : $totalJamBayar;
            
            $totalBiaya = $totalJamBayar * $transaksi->ruangan->tarif_per_jam;

            $transaksi->update([
                'waktu_selesai' => $waktuSelesai,
                'total_biaya' => $totalBiaya,
                'status_pembayaran' => 'Lunas'
            ]);
        } else {
            // Jika Paket/Custom (Harga sudah ada), cukup update status
            // Opsional: Update waktu selesai aktual jika mau
            $transaksi->update([
                'status_pembayaran' => 'Lunas'
            ]);
        }

        return redirect()->route('dashboard')->with('success', "Checkout Berhasil! Total Tagihan: Rp " . number_format($transaksi->total_biaya));
    }
    public function print($id_transaksi)
    {
        $transaksi = Transaksi::with(['pelanggan', 'ruangan', 'paket', 'user'])->findOrFail($id_transaksi);
        return view('transaksi.print', compact('transaksi'));
    }
}
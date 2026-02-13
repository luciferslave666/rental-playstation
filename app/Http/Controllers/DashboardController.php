<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. DATA MONITORING RUANGAN (YANG DIPERBAIKI)
        // ==========================================
        
        // Kita gunakan "Eager Loading" (with) agar database tidak dipanggil berkali-kali
        // Kita panggil relasi 'transaksiAktif' yang baru kita buat di Model
        $ruangans = Ruangan::with(['transaksiAktif.pelanggan', 'transaksiAktif.paket', 'konsol'])
                    ->orderBy('nomor_ruangan', 'asc')
                    ->get();

        // Hitung statistik ringkas untuk kartu paling atas
        $totalTransaksiHariIni = Transaksi::whereDate('created_at', Carbon::today())->count();
        
        $pendapatanHariIni = Transaksi::whereDate('created_at', Carbon::today())
                                      ->where('status_pembayaran', 'Lunas')
                                      ->sum('total_biaya');
                                      
        // Hitung berapa ruangan yang sedang merah (ada isinya)
        $ruanganTerpakai = Ruangan::whereHas('transaksiAktif')->count();


        // ==========================================
        // 2. DATA CHART / GRAFIK (LOGIKA LAMA ANDA)
        // ==========================================
        
        // Data Pendapatan 7 Hari Terakhir
        $incomeLabels = [];
        $incomeData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $incomeLabels[] = $date->format('d M');
            
            $sum = Transaksi::whereDate('created_at', $date->format('Y-m-d'))
                    ->where('status_pembayaran', 'Lunas')
                    ->sum('total_biaya');
            $incomeData[] = $sum;
        }

        // Data Popularitas Tipe Ruangan
        $tipeRuanganStats = Transaksi::join('ruangan', 'transaksi.id_ruangan', '=', 'ruangan.id_ruangan')
            ->select('ruangan.tipe_ruangan', DB::raw('count(*) as total'))
            ->groupBy('ruangan.tipe_ruangan')
            ->pluck('total', 'ruangan.tipe_ruangan');

        $chartVip = $tipeRuanganStats['VIP'] ?? 0;
        $chartReguler = $tipeRuanganStats['Reguler'] ?? 0;

        // ==========================================
        // 3. KIRIM SEMUA KE VIEW
        // ==========================================
        return view('dashboard', compact(
            'ruangans', 
            'totalTransaksiHariIni', 
            'pendapatanHariIni', 
            'ruanganTerpakai',
            'incomeLabels',
            'incomeData',
            'chartVip',
            'chartReguler'
        ));
    }
}
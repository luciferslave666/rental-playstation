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
        // --- EXISTING CODE (Stats Card) ---
        $pendapatanHariIni = Transaksi::whereDate('created_at', Carbon::today())->sum('total_biaya');
        $unitTerpakai = Transaksi::whereNull('waktu_selesai')->count();
        $totalUnit = Ruangan::count();
        
        $ruangans = Ruangan::with('konsol')->get()->map(function ($ruangan) {
            $transaksiAktif = Transaksi::where('id_ruangan', $ruangan->id_ruangan)
                ->whereNull('waktu_selesai')
                ->first();
            $ruangan->status = $transaksiAktif ? 'Terisi' : 'Kosong';
            $ruangan->transaksi_aktif = $transaksiAktif; 
            return $ruangan;
        });

        // 1. Data Pendapatan 7 Hari Terakhir
        $incomeLabels = [];
        $incomeData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $incomeLabels[] = $date->format('d M'); // Label tgl
            
            // Query sum total_biaya per tanggal
            $sum = Transaksi::whereDate('created_at', $date->format('Y-m-d'))
                    ->where('status_pembayaran', 'Lunas') // Hanya yang sudah bayar
                    ->sum('total_biaya');
            $incomeData[] = $sum;
        }

        // 2. Data Popularitas Tipe Ruangan (VIP vs Reguler)
        // Menghitung berapa kali VIP disewa vs Reguler disewa
        $tipeRuanganStats = Transaksi::join('ruangan', 'transaksi.id_ruangan', '=', 'ruangan.id_ruangan')
            ->select('ruangan.tipe_ruangan', DB::raw('count(*) as total'))
            ->groupBy('ruangan.tipe_ruangan')
            ->pluck('total', 'ruangan.tipe_ruangan');

        $chartVip = $tipeRuanganStats['VIP'] ?? 0;
        $chartReguler = $tipeRuanganStats['Reguler'] ?? 0;

        return view('dashboard', [
            'pendapatanHariIni' => $pendapatanHariIni,
            'unitTerpakai' => $unitTerpakai,
            'totalUnit' => $totalUnit,
            'ruangans' => $ruangans,
            // Kirim data chart ke view
            'incomeLabels' => $incomeLabels,
            'incomeData' => $incomeData,
            'chartVip' => $chartVip,
            'chartReguler' => $chartReguler
        ]);
    }
}
@extends('layouts.app')

@section('title', 'Dashboard Rental')
@section('header', 'Dashboard Utama')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-lg flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-6 -mt-6 transition group-hover:bg-emerald-500/20"></div>
        <div class="w-14 h-14 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-2xl">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div class="relative z-10">
            <p class="text-slate-400 text-sm font-medium">Pendapatan Hari Ini</p>
            <h3 class="text-3xl font-bold text-white">Rp {{ number_format($pendapatanHariIni, 0,',','.') }}</h3>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-lg flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-ps-blue/10 rounded-full blur-2xl -mr-6 -mt-6 transition group-hover:bg-ps-blue/20"></div>
        <div class="w-14 h-14 rounded-xl bg-ps-blue/20 flex items-center justify-center text-ps-blue text-2xl">
            <i class="fa-solid fa-gamepad"></i>
        </div>
        <div class="relative z-10">
            <p class="text-slate-400 text-sm font-medium">Ruangan Terpakai</p>
            <h3 class="text-3xl font-bold text-white">
                {{ $ruanganTerpakai }} <span class="text-lg text-slate-500 font-normal">/ {{ $ruangans->count() }} Unit</span>
            </h3>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-lg flex items-center gap-4 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl -mr-6 -mt-6 transition group-hover:bg-yellow-500/20"></div>
        <div class="w-14 h-14 rounded-xl bg-yellow-500/20 flex items-center justify-center text-yellow-500 text-2xl">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="relative z-10">
            <p class="text-slate-400 text-sm font-medium">Total Transaksi</p>
            <h3 class="text-3xl font-bold text-white">{{ $totalTransaksiHariIni }} <span class="text-lg text-slate-500 font-normal">Sesi</span></h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    
    <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-lg">
        <h4 class="text-white font-bold mb-6 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-ps-blue"></i> Statistik Pendapatan (7 Hari)
        </h4>
        <div class="h-64">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-lg">
        <h4 class="text-white font-bold mb-6 flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-yellow-500"></i> Tipe Favorit
        </h4>
        <div class="h-64 relative">
            <canvas id="popularChart"></canvas>
        </div>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <h3 class="text-white font-bold text-lg flex items-center gap-2">
        <i class="fa-solid fa-tv text-slate-400"></i> Status Live Ruangan
    </h3>
    <div class="flex gap-4 text-xs font-bold">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> KOSONG</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span> TERISI</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
    @foreach($ruangans as $r)
        
        @php
            $transaksi = $r->transaksiAktif; 
            $isDipakai = $transaksi != null;
        @endphp

        <div class="relative bg-slate-800 rounded-2xl border {{ $isDipakai ? 'border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.15)]' : 'border-slate-700 hover:border-emerald-500/50' }} p-5 transition-all duration-300">
            
            <div class="flex justify-between items-start mb-4 border-b border-slate-700/50 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $isDipakai ? 'bg-red-500/10 text-red-500' : 'bg-slate-700 text-slate-400' }} flex items-center justify-center text-xl font-bold">
                        {{ $r->tipe_ruangan == 'VIP' ? 'V' : 'R' }}
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-white">{{ $r->nomor_ruangan }}</h4>
                        <p class="text-[10px] uppercase font-bold tracking-wider {{ $r->tipe_ruangan == 'VIP' ? 'text-yellow-500' : 'text-slate-400' }}">
                            {{ $r->tipe_ruangan }}
                        </p>
                    </div>
                </div>
                
                @if($isDipakai)
                    <div class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded animate-pulse">PLAYING</div>
                @else
                    <div class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded">READY</div>
                @endif
            </div>

            @if($isDipakai)
                <div class="space-y-3">
                    <div class="bg-slate-900/50 p-3 rounded-lg border border-slate-700/50">
                        <div class="flex items-center gap-2 text-slate-300 text-sm mb-1">
                            <i class="fa-solid fa-user text-xs text-slate-500"></i>
                            <span class="font-bold truncate">{{ Str::limit($transaksi->pelanggan->nama_pelanggan, 12) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400 text-xs">
                            <i class="fa-solid fa-clock text-slate-500"></i>
                            <span>Mulai: {{ \Carbon\Carbon::parse($transaksi->waktu_mulai)->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="text-center">
                        @if($transaksi->id_paket)
                            <span class="px-3 py-1 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 rounded text-[10px] font-bold uppercase tracking-wide">
                                {{ Str::limit($transaksi->paket->nama_paket, 20) }}
                            </span>
                        @else
                        @endif
                    </div>
                    
                    <div class="text-center pt-1">
                        @if($transaksi->id_paket)
                            @php
                                $sisaMenit = \Carbon\Carbon::now()->diffInMinutes(\Carbon\Carbon::parse($transaksi->waktu_selesai), false);
                            @endphp
                            @if($sisaMenit > 0)
                                <p class="text-xs text-slate-400">Sisa Waktu</p>
                                <p class="text-xl font-mono font-bold text-yellow-500">{{ $sisaMenit }} Menit</p>
                            @else
                                <p class="text-red-500 font-bold text-sm animate-bounce">WAKTU HABIS!</p>
                            @endif
                        @else
                            <p class="text-xs text-slate-400">Durasi Main</p>
                            <p class="text-xl font-mono font-bold text-blue-400">
                                @php
                                    $start = \Carbon\Carbon::parse($transaksi->waktu_mulai);
                                    $now = \Carbon\Carbon::now();
                                    $jam = floor($start->diffInMinutes($now) / 60); 
                                    $menit = $start->diffInMinutes($now) % 60;
                                @endphp
                                {{ $jam }} Jam {{ $menit }} Mnt
                            </p>
                        @endif
                    </div>

                    <a href="{{ route('transaksi.show', $transaksi->id_transaksi) }}" class="block w-full py-2 bg-slate-700 hover:bg-red-600 text-slate-200 hover:text-white text-center rounded-xl text-xs font-bold transition">
                        DETAIL / STOP
                    </a>
                </div>
            @else
                <div class="py-4 text-center space-y-1">
                    <p class="text-slate-500 text-xs">Fasilitas:</p>
                    <p class="text-slate-400 text-xs italic truncate px-2">{{ $r->konsol->seri_konsol ?? 'PS Standard' }}</p>
                    <p class="text-ps-blue font-bold text-sm pt-2">Rp {{ number_format($r->tarif_per_jam/1000) }}k / Jam</p>
                </div>
                
<a href="{{ route('transaksi.create', ['room_id' => $r->id_ruangan]) }}" class="block w-full py-2 mt-2 border border-dashed border-slate-600 text-slate-400 hover:border-emerald-500 hover:bg-emerald-500 hover:text-white text-center rounded-xl text-xs font-bold transition">
    + BARU
</a>
            @endif

        </div>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Setup Data dari Controller ke JS
    const incomeLabels = @json($incomeLabels);
    const incomeData = @json($incomeData);
    const chartVip = @json($chartVip);
    const chartReguler = @json($chartReguler);

    // 2. Render Chart Pendapatan (Line Chart)
    const ctxIncome = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxIncome, {
        type: 'line',
        data: {
            labels: incomeLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: incomeData,
                borderColor: '#0070d1', // PS Blue
                backgroundColor: 'rgba(0, 112, 209, 0.1)',
                borderWidth: 3,
                tension: 0.4, // Garis melengkung halus
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0070d1',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#334155' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });

    // 3. Render Chart Popularitas (Doughnut Chart)
    const ctxPop = document.getElementById('popularChart').getContext('2d');
    new Chart(ctxPop, {
        type: 'doughnut',
        data: {
            labels: ['VIP', 'Reguler'],
            datasets: [{
                data: [chartVip, chartReguler],
                backgroundColor: [
                    '#eab308', // Yellow (VIP)
                    '#64748b'  // Slate (Reguler)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#cbd5e1' }
                }
            },
            cutout: '70%' // Lubang tengah
        }
    });
</script>

@endsection
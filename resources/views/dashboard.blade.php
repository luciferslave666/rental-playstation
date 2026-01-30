@extends('layouts.app')

@section('title', 'Dashboard Monitoring')
@section('header', 'Dashboard Monitoring')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="p-6 rounded-2xl border border-slate-700 bg-gradient-to-br from-slate-800 to-slate-900 relative overflow-hidden group hover:border-ps-blue/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-white mt-1">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-slate-700/50 rounded-lg text-blue-400">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>
        
        <div class="p-6 rounded-2xl border border-slate-700 bg-gradient-to-br from-slate-800 to-slate-900">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Unit Terpakai</p>
                    <h3 class="text-2xl font-bold text-white mt-1">{{ $unitTerpakai }} / {{ $totalUnit }} Unit</h3>
                </div>
                <div class="p-2 bg-slate-700/50 rounded-lg text-purple-400">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
            </div>
            <div class="w-full bg-slate-700 h-1.5 rounded-full mt-2">
                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $totalUnit > 0 ? ($unitTerpakai / $totalUnit) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-ps-blue"></i> Tren Pendapatan (7 Hari)
            </h3>
            <div class="relative h-64 w-full">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-xl">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-purple-500"></i> Popularitas Ruangan
            </h3>
            <div class="relative h-48 w-full flex justify-center">
                <canvas id="roomChart"></canvas>
            </div>
            <div class="mt-4 flex justify-center gap-4 text-xs">
                <div class="flex items-center gap-1 text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span> VIP
                </div>
                <div class="flex items-center gap-1 text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span> Reguler
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxIncome = document.getElementById('incomeChart').getContext('2d');
        const gradient = ctxIncome.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 112, 209, 0.5)');
        gradient.addColorStop(1, 'rgba(0, 112, 209, 0.0)');

        new Chart(ctxIncome, {
            type: 'line',
            data: {
                labels: {!! json_encode($incomeLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($incomeData) !!},
                    borderColor: '#0070d1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0070d1',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        const ctxRoom = document.getElementById('roomChart').getContext('2d');
        new Chart(ctxRoom, {
            type: 'doughnut',
            data: {
                labels: ['VIP', 'Reguler'],
                datasets: [{
                    data: [{{ $chartVip }}, {{ $chartReguler }}],
                    backgroundColor: ['#eab308', '#0070d1'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%',
            }
        });
    </script>

    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-xl font-bold text-white">Status Ruangan</h3>
        <div class="flex gap-2">
            <span class="flex items-center gap-2 text-xs text-slate-400"><span class="w-3 h-3 rounded-full bg-red-500"></span> Terisi</span>
            <span class="flex items-center gap-2 text-xs text-slate-400"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Kosong</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($ruangans as $ruangan)
            @if($ruangan->status == 'Terisi')
                <div class="room-card bg-slate-800 rounded-2xl p-5 border border-red-500/30 shadow-[0_0_15px_rgba(239,68,68,0.15)] relative overflow-hidden">
                    
                    <div class="absolute top-0 right-0 {{ $ruangan->tipe_ruangan == 'VIP' ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' : 'bg-slate-700 text-slate-400' }} text-xs font-bold px-3 py-1 rounded-bl-xl border-l border-b">
                        {{ $ruangan->tipe_ruangan }}
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-700 flex items-center justify-center text-2xl">
                            {{ $ruangan->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                        </div>
                        <div>
                            <h4 class="text-white font-bold">{{ $ruangan->nomor_ruangan }}</h4>
                            <p class="text-xs text-slate-400">{{ $ruangan->konsol->seri_konsol ?? 'No Console' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-slate-900/50 rounded-xl p-3 border border-slate-700/50 mb-3">
                        @if($ruangan->transaksi_aktif->id_paket)
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-emerald-400 font-bold">PAKET AKTIF</span>
                                <span class="text-xs text-slate-400">Selesai: {{ $ruangan->transaksi_aktif->waktu_selesai->format('H:i') }}</span>
                            </div>
                            <div class="text-sm text-white text-center font-bold">
                                Sisa: {{ $ruangan->transaksi_aktif->waktu_selesai->diffForHumans(null, true) }}
                            </div>
                        @else
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-400">Mulai: {{ $ruangan->transaksi_aktif->waktu_mulai->format('H:i') }}</span>
                                <span class="text-red-400 text-xs font-bold animate-pulse">Main</span>
                            </div>
                            <div class="text-xl font-mono font-bold text-white text-center">
                                {{ $ruangan->transaksi_aktif->waktu_mulai->diffForHumans(null, true) }}
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('transaksi.show', $ruangan->transaksi_aktif->id_transaksi) }}" class="block w-full text-center py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-medium transition">
                        Detail Transaksi
                    </a>
                </div>

            @else
                <div class="room-card bg-slate-800 rounded-2xl p-5 border border-slate-700 hover:border-emerald-500/50 transition-all cursor-pointer group relative">
                    <div class="absolute top-0 right-0 {{ $ruangan->tipe_ruangan == 'VIP' ? 'bg-yellow-500/10 text-yellow-500/50' : 'bg-slate-700 text-slate-500' }} text-xs font-bold px-3 py-1 rounded-bl-xl">
                        {{ $ruangan->tipe_ruangan }}
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-700 group-hover:bg-emerald-500/20 group-hover:text-emerald-500 transition flex items-center justify-center text-2xl text-slate-500">
                            {{ $ruangan->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                        </div>
                        <div>
                            <h4 class="text-white font-bold group-hover:text-emerald-500 transition">{{ $ruangan->nomor_ruangan }}</h4>
                            <p class="text-xs text-slate-400">{{ $ruangan->konsol->seri_konsol ?? 'No Console' }}</p>
                        </div>
                    </div>
                    
                    <div class="h-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-700 rounded-xl group-hover:border-emerald-500/30 transition">
                        <span class="text-emerald-500 font-bold text-sm">Available</span>
                        <span class="text-slate-500 text-xs">Siap digunakan</span>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

@endsection
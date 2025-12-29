@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('header', 'Detail & Checkout')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <div class="md:col-span-2 space-y-6">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
            <h3 class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-4">Informasi Sesi</h3>
            
            <div class="flex items-center gap-6 mb-6">
                <div class="w-20 h-20 rounded-2xl bg-slate-700 flex items-center justify-center text-4xl">
                    {{ $transaksi->ruangan->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-white">{{ $transaksi->ruangan->nomor_ruangan }}</h2>
                    <p class="text-ps-blue font-medium">{{ $transaksi->ruangan->tipe_ruangan }} Room</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="p-4 bg-slate-900 rounded-xl">
                    <p class="text-slate-500 mb-1">Pelanggan</p>
                    <p class="text-white font-bold text-lg">{{ $transaksi->pelanggan->nama_pelanggan }}</p>
                    <p class="text-slate-400 text-xs">{{ $transaksi->pelanggan->no_hp }}</p>
                </div>
                <div class="p-4 bg-slate-900 rounded-xl">
                    <p class="text-slate-500 mb-1">Operator / Kasir</p>
                    <p class="text-white font-bold text-lg">{{ $transaksi->user->nama }}</p>
                </div>
                <div class="p-4 bg-slate-900 rounded-xl">
                    <p class="text-slate-500 mb-1">Waktu Mulai</p>
                    <p class="text-white font-bold text-lg">{{ \Carbon\Carbon::parse($transaksi->waktu_mulai)->format('H:i') }} WIB</p>
                </div>
                <div class="p-4 bg-slate-900 rounded-xl border border-ps-blue/30 relative overflow-hidden">
                    <div class="absolute inset-0 bg-ps-blue/5"></div>
                    <p class="text-ps-blue mb-1 font-semibold">Durasi Berjalan</p>
                    <p class="text-white font-bold text-lg font-mono">{{ $durasiJam }} Jam {{ $durasiMenit }} Menit</p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-slate-700 p-6 shadow-xl relative">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 rounded-full blur-xl -mr-4 -mt-4"></div>
            
            <h3 class="text-slate-400 text-sm font-bold uppercase tracking-wider mb-6">Rincian Biaya</h3>

            <div class="space-y-3 mb-6 border-b border-slate-700 pb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Tarif per Jam</span>
                    <span class="text-white font-medium">Rp {{ number_format($transaksi->ruangan->tarif_per_jam, 0,',','.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Total Durasi (Dibulatkan)</span>
                    <span class="text-white font-medium">{{ $totalJamBayar }} Jam</span>
                </div>
            </div>

            <div class="flex justify-between items-end mb-8">
                <span class="text-slate-300 font-bold">Total Tagihan</span>
                <span class="text-3xl font-bold text-green-400">Rp {{ number_format($estimasiBiaya, 0,',','.') }}</span>
            </div>

            <form action="{{ route('transaksi.complete', $transaksi->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah yakin ingin menyelesaikan sesi ini? Waktu akan dihentikan.')">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full py-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold shadow-lg shadow-red-600/30 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-stop-circle"></i> STOP & BAYAR
                </button>
            </form>
            
            <a href="{{ route('dashboard') }}" class="block text-center mt-4 text-slate-400 hover:text-white text-sm">Kembali ke Dashboard</a>
        </div>
    </div>
</div>
@endsection
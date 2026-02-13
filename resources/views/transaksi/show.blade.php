@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('header', 'Detail Transaksi #' . $transaksi->id_transaksi)

@section('content')
<div class="max-w-5xl mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <p class="text-slate-400 text-sm">Tanggal Transaksi</p>
            <h3 class="text-white font-bold text-lg">{{ $transaksi->created_at->format('d F Y, H:i') }} WIB</h3>
        </div>
        <div>
            @if($transaksi->status_pembayaran == 'Lunas')
                <span class="px-4 py-2 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> LUNAS / SELESAI
                </span>
            @else
                <span class="px-4 py-2 rounded-lg bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 font-bold flex items-center gap-2 animate-pulse">
                    <i class="fa-solid fa-clock"></i> BELUM LUNAS / BERJALAN
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-lg">
                <h4 class="text-white font-bold mb-4 flex items-center gap-2 border-b border-slate-700 pb-4">
                    <i class="fa-solid fa-circle-info text-ps-blue"></i> Informasi Sewa
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold mb-1">Pelanggan</p>
                        <p class="text-white font-bold text-lg">{{ $transaksi->pelanggan->nama_pelanggan }}</p>
                        <p class="text-slate-400 text-sm"><i class="fa-brands fa-whatsapp mr-1 text-emerald-500"></i> {{ $transaksi->pelanggan->no_hp }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold mb-1">Ruangan</p>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded bg-slate-700 flex items-center justify-center text-xl">
                                {{ $transaksi->ruangan->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                            </span>
                            <div>
                                <p class="text-white font-bold">{{ $transaksi->ruangan->nomor_ruangan }}</p>
                                <p class="text-xs text-slate-400">{{ $transaksi->ruangan->tipe_ruangan }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold mb-1">Operator / Kasir</p>
                        <p class="text-white text-sm">{{ $transaksi->user->nama ?? 'Sistem' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold mb-1">Konsol</p>
                        <p class="text-white text-sm">{{ $transaksi->ruangan->konsol->seri_konsol ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 shadow-lg">
                <h4 class="text-white font-bold mb-4 flex items-center gap-2 border-b border-slate-700 pb-4">
                    <i class="fa-solid fa-stopwatch text-yellow-500"></i> Rincian Waktu
                </h4>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Waktu Mulai</span>
                        <span class="text-white font-mono">{{ $transaksi->waktu_mulai->format('H:i:s') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Waktu Selesai</span>
                        @if($transaksi->waktu_selesai)
                            <span class="text-white font-mono">{{ $transaksi->waktu_selesai->format('H:i:s') }}</span>
                        @else
                            <span class="text-yellow-500 font-bold text-sm animate-pulse">Sedang Main...</span>
                        @endif
                    </div>

                    <div class="border-t border-dashed border-slate-700 my-2"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm font-bold">Jenis Billing</span>
                        @if($transaksi->id_paket)
                            <span class="px-3 py-1 rounded bg-yellow-500/10 text-yellow-500 text-xs font-bold border border-yellow-500/20">
                                {{ $transaksi->paket->nama_paket }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded bg-blue-500/10 text-blue-400 text-xs font-bold border border-blue-500/20">
                                Open Billing (Per Jam)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6">
            
            <div class="bg-slate-900 rounded-2xl border border-slate-700 p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                
                <p class="text-slate-400 text-sm mb-2 text-center">Total Tagihan</p>
                
                <h2 class="text-4xl font-bold text-white text-center mb-2">
                    @if($transaksi->status_pembayaran == 'Lunas')
                        Rp {{ number_format($transaksi->total_biaya, 0,',','.') }}
                    @else
                        Rp {{ number_format($estimasiBiaya, 0,',','.') }}
                    @endif
                </h2>

                <p class="text-center text-xs text-slate-500 mb-6">
                    @if($transaksi->id_paket)
                        *Harga Paket Tetap
                    @else
                        *Estimasi {{ $totalJamBayar }} Jam x Rp {{ number_format($transaksi->ruangan->tarif_per_jam/1000) }}k
                    @endif
                </p>

                @if($transaksi->status_pembayaran == 'Belum Lunas')
<form action="{{ route('transaksi.complete', $transaksi->id_transaksi) }}" method="POST" onsubmit="return confirm('Stop timer dan selesaikan pembayaran?');">
    @csrf
    @method('PUT')  <button type="submit" class="w-full md:w-auto px-8 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold shadow-lg shadow-red-600/30 transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-stop"></i> STOP & BAYAR
    </button>
</form>
                @else
                    <a href="{{ route('transaksi.print', $transaksi->id_transaksi) }}" target="_blank" class="block w-full py-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-center transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i> Cetak Struk
                    </a>
                @endif
            </div>

            <div class="space-y-3">
                
                @php
                    // 1. Format Nomor HP (08xx -> 628xx)
                    $hp = preg_replace('/^0/', '62', $transaksi->pelanggan->no_hp);
                    
                    // 2. Tentukan Total Akhir
                    $totalAkhir = ($transaksi->status_pembayaran == 'Lunas') ? $transaksi->total_biaya : $estimasiBiaya;

                    // 3. Susun Pesan
                    $pesan = "*STRUK DIGITAL - RENTAL PS MANTAP*%0A";
                    $pesan .= "----------------------------------%0A";
                    $pesan .= "📅 Tgl: " . $transaksi->created_at->format('d M Y H:i') . "%0A";
                    $pesan .= "👤 Pelanggan: " . $transaksi->pelanggan->nama_pelanggan . "%0A";
                    $pesan .= "🎮 Ruangan: " . $transaksi->ruangan->nomor_ruangan . " (" . $transaksi->ruangan->tipe_ruangan . ")%0A";
                    
                    if($transaksi->id_paket) {
                        $pesan .= "📦 Paket: " . $transaksi->paket->nama_paket . "%0A";
                    } else {
                        $pesan .= "⏱️ Durasi: " . $totalJamBayar . " Jam%0A";
                    }

                    $pesan .= "----------------------------------%0A";
                    $pesan .= "*💰 TOTAL: Rp " . number_format($totalAkhir, 0, ',', '.') . "*%0A";
                    $pesan .= "----------------------------------%0A";
                    $pesan .= "Terima kasih kak! Simpan nomor ini untuk booking selanjutnya ya! 🙏";
                @endphp

                <a href="https://wa.me/{{ $hp }}?text={{ $pesan }}" target="_blank" class="block w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-center shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-xl"></i> Kirim Struk WA
                </a>

                <a href="{{ route('dashboard') }}" class="block w-full py-3 rounded-xl border border-slate-600 text-slate-400 hover:bg-slate-700 hover:text-white text-center transition font-semibold">
                    Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
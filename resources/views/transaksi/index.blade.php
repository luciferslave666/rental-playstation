@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Transaksi')

@section('content')
<div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-200 uppercase font-bold text-xs">
                <tr>
                    <th class="px-6 py-4">No Nota</th>
                    <th class="px-6 py-4">Waktu Main</th>
                    <th class="px-6 py-4">Pelanggan</th>
                    <th class="px-6 py-4">Ruangan</th>
                    <th class="px-6 py-4">Total Biaya</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Kasir</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($semuaTransaksi as $t)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 font-mono text-white">#TRX-{{ sprintf('%04d', $t->id_transaksi) }}</td>
                    <td class="px-6 py-4">
                        <div class="text-white font-medium">{{ $t->waktu_mulai->format('d M Y') }}</div>
                        <div class="text-xs">{{ $t->waktu_mulai->format('H:i') }} - {{ $t->waktu_selesai ? $t->waktu_selesai->format('H:i') : '...' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-white font-bold">{{ $t->pelanggan->nama_pelanggan }}</div>
                        <div class="text-xs">{{ $t->pelanggan->no_hp }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $t->ruangan->tipe_ruangan == 'VIP' ? 'text-yellow-400' : 'text-slate-300' }} font-bold">
                            {{ $t->ruangan->nomor_ruangan }}
                        </span>
                        <div class="text-xs">{{ $t->ruangan->tipe_ruangan }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-white">
                        Rp {{ number_format($t->total_biaya, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($t->status_pembayaran == 'Lunas')
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold">
                                Lunas
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 text-xs font-bold animate-pulse">
                                Belum Lunas
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $t->user->nama }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($t->status_pembayaran == 'Belum Lunas')
                            <a href="{{ route('transaksi.show', $t->id_transaksi) }}" class="text-ps-blue hover:text-white transition" title="Proses Checkout">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </a>
                        @else
                            <button class="text-slate-500 cursor-not-allowed" title="Sudah Selesai">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                        Belum ada data transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $semuaTransaksi->links() }}
    </div>
</div>
@endsection
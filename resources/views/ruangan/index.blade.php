@extends('layouts.app')

@section('title', 'Data Ruangan & Konsol')
@section('header', 'Data Ruangan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-slate-400 text-sm">Kelola unit ruangan dan aset konsol.</p>
    <a href="{{ route('ruangan.create') }}" class="bg-ps-blue hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Unit Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($ruangans as $r)
    <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden relative group hover:border-ps-blue/50 transition-all">
        
        <div class="absolute top-0 right-0 {{ $r->tipe_ruangan == 'VIP' ? 'bg-yellow-500 text-slate-900' : 'bg-slate-600 text-white' }} text-xs font-bold px-3 py-1 rounded-bl-xl z-10">
            {{ $r->tipe_ruangan }}
        </div>

        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl {{ $r->tipe_ruangan == 'VIP' ? 'bg-yellow-500/20 text-yellow-500' : 'bg-slate-700 text-slate-400' }} flex items-center justify-center text-3xl">
                    {{ $r->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $r->nomor_ruangan }}</h3>
                    <p class="text-sm text-ps-blue font-mono">Rp {{ number_format($r->tarif_per_jam, 0,',','.') }} / jam</p>
                </div>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between text-sm border-b border-slate-700 pb-2">
                    <span class="text-slate-500">Unit Konsol</span>
                    <span class="text-white font-medium">{{ $r->konsol->seri_konsol ?? '-' }}</span>
                </div>
                <div class="text-sm">
                    <span class="text-slate-500 block mb-1">Fasilitas:</span>
                    <p class="text-slate-300 text-xs leading-relaxed">{{ $r->deskripsi_fasilitas }}</p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('ruangan.edit', $r->id_ruangan) }}" class="flex-1 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-center text-sm font-medium transition">
                    Edit Data
                </a>
                <form action="{{ route('ruangan.destroy', $r->id_ruangan) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini? Konsol di dalamnya juga akan terhapus.')" class="w-10">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full h-full rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition flex items-center justify-center">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
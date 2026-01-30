@extends('layouts.app')

@section('title', 'Kelola Paket Billing')
@section('header', 'Daftar Paket Billing')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <p class="text-slate-400 text-sm">Buat paket promo agar pelanggan tertarik main lebih lama.</p>
    <a href="{{ route('paket.create') }}" class="bg-ps-blue hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-600/20 flex items-center gap-2 transition transform hover:-translate-y-1">
        <i class="fa-solid fa-plus"></i> Tambah Paket Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($pakets as $p)
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 hover:border-yellow-500/50 transition group relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-500/5 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-yellow-500/10 transition"></div>

        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-slate-700 flex items-center justify-center text-yellow-500 text-2xl group-hover:bg-yellow-500 group-hover:text-slate-900 transition shadow-inner">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="flex gap-1">
                <a href="{{ route('paket.edit', $p->id_paket) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-700 text-slate-400 hover:text-white transition" title="Edit">
                    <i class="fa-solid fa-pen text-xs"></i>
                </a>
                <form action="{{ route('paket.destroy', $p->id_paket) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-500/10 text-slate-400 hover:text-red-500 transition" title="Hapus">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        <h3 class="text-lg font-bold text-white mb-1 group-hover:text-yellow-500 transition">{{ $p->nama_paket }}</h3>
        
        <div class="flex items-center gap-4 text-xs text-slate-400 mb-6">
            <span class="flex items-center gap-1"><i class="fa-solid fa-hourglass-half"></i> {{ $p->durasi_menit }} Menit</span>
            <span class="flex items-center gap-1"><i class="fa-regular fa-clock"></i> {{ round($p->durasi_menit/60, 1) }} Jam</span>
        </div>
        
        <div class="pt-4 border-t border-slate-700/50 flex justify-between items-center">
            <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Harga</span>
            <span class="text-xl font-bold text-white">Rp {{ number_format($p->harga, 0,',','.') }}</span>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 border-2 border-dashed border-slate-700 rounded-2xl">
        <div class="text-slate-500 text-5xl mb-4"><i class="fa-solid fa-box-open"></i></div>
        <h3 class="text-white font-bold text-lg">Belum ada paket</h3>
        <p class="text-slate-400 text-sm">Silakan tambahkan paket baru.</p>
    </div>
    @endforelse
</div>
@endsection
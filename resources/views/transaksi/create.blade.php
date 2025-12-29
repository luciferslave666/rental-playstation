@extends('layouts.app')

@section('title', 'Mulai Transaksi Baru')
@section('header', 'Mulai Sesi Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-32 h-32 bg-ps-blue/10 rounded-full blur-2xl -mr-10 -mt-10"></div>

        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-ps-blue flex items-center justify-center text-sm">
                <i class="fa-solid fa-gamepad"></i>
            </span>
            Isi Data Penyewaan
        </h2>

        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Pilih Pelanggan</label>
                <div class="relative">
                    <select name="id_pelanggan" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue appearance-none transition-all cursor-pointer">
                        <option value="" disabled selected>-- Pilih Nama Pelanggan --</option>
                        @foreach($pelanggans as $p)
                            <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }} ({{ $p->no_hp }})</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-3.5 text-slate-500 pointer-events-none">
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">Pelanggan baru? <a href="#" class="text-ps-blue hover:underline">Tambah disini</a></p>
            </div>

            <div class="mb-8">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Pilih Ruangan (Available)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($ruangans as $r)
                    <label class="cursor-pointer">
                        <input type="radio" name="id_ruangan" value="{{ $r->id_ruangan }}" class="peer sr-only" required>
                        <div class="bg-slate-900 border border-slate-700 p-4 rounded-xl hover:border-slate-500 peer-checked:border-ps-blue peer-checked:bg-ps-blue/10 peer-checked:shadow-[0_0_15px_rgba(0,112,209,0.3)] transition-all flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-xl text-slate-400 peer-checked:text-ps-blue">
                                {{ $r->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm">{{ $r->nomor_ruangan }}</h4>
                                <p class="text-xs text-slate-400">{{ $r->tipe_ruangan }} - Rp {{ number_format($r->tarif_per_jam, 0,',','.') }}/jam</p>
                            </div>
                        </div>
                    </label>
                    @empty
                        <div class="col-span-2 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-center text-sm">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i> Semua ruangan sedang penuh!
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-slate-700 pt-6">
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition font-medium">
                    Batal
                </a>
                <button type="submit" class="px-8 py-2.5 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-1">
                    <i class="fa-solid fa-play mr-2"></i> Mulai Main
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Tambah Paket')
@section('header', 'Buat Paket Baru')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-700 bg-slate-800/50">
            <h3 class="text-white font-bold text-lg"><i class="fa-solid fa-plus mr-2 text-ps-blue"></i> Form Paket</h3>
        </div>
        
        <form action="{{ route('paket.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Paket</label>
                <input type="text" name="nama_paket" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition placeholder-slate-600" placeholder="Contoh: Paket VIP 3 Jam" required>
                <p class="text-[10px] text-slate-500 mt-1 italic">Saran: Beri nama yang jelas (misal: sebutkan jenis ruangannya).</p>
            </div>

            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Durasi (Menit)</label>
                <div class="relative">
                    <input type="number" name="durasi_menit" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition placeholder-slate-600 pl-4 pr-16" placeholder="180" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500 text-sm font-bold">Menit</div>
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="document.querySelector('[name=durasi_menit]').value=60" class="px-2 py-1 bg-slate-700 rounded text-xs text-slate-300 hover:text-white hover:bg-slate-600">1 Jam</button>
                    <button type="button" onclick="document.querySelector('[name=durasi_menit]').value=120" class="px-2 py-1 bg-slate-700 rounded text-xs text-slate-300 hover:text-white hover:bg-slate-600">2 Jam</button>
                    <button type="button" onclick="document.querySelector('[name=durasi_menit]').value=180" class="px-2 py-1 bg-slate-700 rounded text-xs text-slate-300 hover:text-white hover:bg-slate-600">3 Jam</button>
                    <button type="button" onclick="document.querySelector('[name=durasi_menit]').value=300" class="px-2 py-1 bg-slate-700 rounded text-xs text-slate-300 hover:text-white hover:bg-slate-600">5 Jam</button>
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Harga Paket (Rp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-500 font-bold">Rp</div>
                    <input type="number" name="harga" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition placeholder-slate-600 pl-10" placeholder="75000" required>
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <a href="{{ route('paket.index') }}" class="flex-1 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-slate-700 text-center font-semibold transition">Batal</a>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition">Simpan Paket</button>
            </div>
        </form>
    </div>
</div>
@endsection
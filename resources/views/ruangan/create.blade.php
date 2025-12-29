@extends('layouts.app')

@section('title', 'Tambah Ruangan')
@section('header', 'Setup Ruangan Baru')

@section('content')
<div class="max-w-2xl mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('ruangan.store') }}" method="POST">
        @csrf
        
        <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fa-solid fa-door-open text-ps-blue"></i> Data Ruangan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Nomor / Nama Ruangan</label>
                <input type="text" name="nomor_ruangan" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" placeholder="Contoh: VIP-03" required>
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Tipe Layanan</label>
                <select name="tipe_ruangan" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">
                    <option value="Reguler">Reguler</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Tarif per Jam (Rp)</label>
            <input type="number" name="tarif_per_jam" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" placeholder="15000" required>
        </div>

        <div class="mb-8 border-t border-slate-700 pt-6">
            <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fa-brands fa-playstation text-ps-blue"></i> Data Konsol
            </h3>
            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Seri / Model Konsol</label>
                <input type="text" name="seri_konsol" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" placeholder="Contoh: PS5-Digital-003" required>
            </div>
            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Fasilitas Ruangan</label>
                <textarea name="deskripsi_fasilitas" rows="3" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" placeholder="Contoh: TV 43 Inch, AC, Sofa..."></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('ruangan.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition">
                Simpan Unit
            </button>
        </div>
    </form>
</div>
@endsection
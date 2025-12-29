@extends('layouts.app')

@section('title', 'Edit Ruangan')
@section('header', 'Edit Data Ruangan')

@section('content')
<div class="max-w-2xl mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('ruangan.update', $ruangan->id_ruangan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Nomor Ruangan</label>
                <input type="text" name="nomor_ruangan" value="{{ $ruangan->nomor_ruangan }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
            </div>
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Tipe Layanan</label>
                <select name="tipe_ruangan" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">
                    <option value="Reguler" {{ $ruangan->tipe_ruangan == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="VIP" {{ $ruangan->tipe_ruangan == 'VIP' ? 'selected' : '' }}>VIP</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Tarif per Jam (Rp)</label>
            <input type="number" name="tarif_per_jam" value="{{ $ruangan->tarif_per_jam }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-8 border-t border-slate-700 pt-6">
            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Seri / Model Konsol</label>
                <input type="text" name="seri_konsol" value="{{ $ruangan->konsol->seri_konsol ?? '' }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
            </div>
            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-semibold mb-2">Fasilitas Ruangan</label>
                <textarea name="deskripsi_fasilitas" rows="3" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">{{ $ruangan->deskripsi_fasilitas }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('ruangan.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold shadow-lg shadow-yellow-600/30 transition">
                Update Unit
            </button>
        </div>
    </form>
</div>
@endsection
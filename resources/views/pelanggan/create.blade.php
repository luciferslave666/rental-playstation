@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('header', 'Tambah Pelanggan Baru')

@section('content')
<div class="max-w-lg mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="nama_pelanggan" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition" placeholder="Contoh: Dandy Muhammad" required>
        </div>

        <div class="mb-8">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nomor HP / WhatsApp</label>
            <input type="number" name="no_hp" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition" placeholder="0812..." required>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('pelanggan.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition">
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
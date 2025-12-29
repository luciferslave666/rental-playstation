@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('header', 'Edit Data Pelanggan')

@section('content')
<div class="max-w-lg mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('pelanggan.update', $pelanggan->id_pelanggan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="nama_pelanggan" value="{{ $pelanggan->nama_pelanggan }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-8">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nomor HP / WhatsApp</label>
            <input type="number" name="no_hp" value="{{ $pelanggan->no_hp }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('pelanggan.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold shadow-lg shadow-yellow-600/30 transition">
                Update Data
            </button>
        </div>
    </form>
</div>
@endsection
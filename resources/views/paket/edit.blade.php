@extends('layouts.app')

@section('title', 'Edit Paket')
@section('header', 'Edit Paket Billing')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-700 bg-slate-800/50">
            <h3 class="text-white font-bold text-lg"><i class="fa-solid fa-pen-to-square mr-2 text-yellow-500"></i> Edit Data Paket</h3>
        </div>
        
        <form action="{{ route('paket.update', $paket->id_paket) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Paket</label>
                <input type="text" name="nama_paket" value="{{ $paket->nama_paket }}" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition" required>
            </div>

            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Durasi (Menit)</label>
                <div class="relative">
                    <input type="number" name="durasi_menit" value="{{ $paket->durasi_menit }}" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition pl-4 pr-16" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500 text-sm font-bold">Menit</div>
                </div>
            </div>

            <div>
                <label class="block text-slate-400 text-sm font-semibold mb-2">Harga Paket (Rp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-500 font-bold">Rp</div>
                    <input type="number" name="harga" value="{{ $paket->harga }}" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition pl-10" required>
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <a href="{{ route('paket.index') }}" class="flex-1 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-slate-700 text-center font-semibold transition">Batal</a>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold shadow-lg shadow-yellow-600/30 transition">Update Paket</button>
            </div>
        </form>
    </div>
</div>
@endsection
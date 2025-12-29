@extends('layouts.app')

@section('title', 'Tambah Pegawai')
@section('header', 'Registrasi Pegawai Baru')

@section('content')
<div class="max-w-lg mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('user.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="nama" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Username (untuk Login)</label>
            <input type="text" name="username" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Role / Jabatan</label>
            <select name="role" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">
                <option value="kasir">Kasir</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="mb-8">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Password</label>
            <input type="password" name="password" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('user.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition">
                Simpan Akun
            </button>
        </div>
    </form>
</div>
@endsection
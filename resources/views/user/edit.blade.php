@extends('layouts.app')

@section('title', 'Edit Pegawai')
@section('header', 'Edit Data Pegawai')

@section('content')
<div class="max-w-lg mx-auto bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl">
    <form action="{{ route('user.update', $user->id_user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ $user->nama }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Username</label>
            <input type="text" name="username" value="{{ $user->username }}" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition" required>
        </div>

        <div class="mb-6">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Role / Jabatan</label>
            <select name="role" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">
                <option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="mb-8">
            <label class="block text-slate-400 text-sm font-semibold mb-2">Password Baru <span class="font-normal text-xs text-slate-500">(Kosongkan jika tidak diganti)</span></label>
            <input type="password" name="password" class="w-full bg-slate-900 border border-slate-700 text-white px-4 py-3 rounded-xl focus:ring-1 focus:ring-ps-blue transition">
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('user.index') }}" class="px-6 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-yellow-600 hover:bg-yellow-500 text-white font-bold shadow-lg shadow-yellow-600/30 transition">
                Update Akun
            </button>
        </div>
    </form>
</div>
@endsection
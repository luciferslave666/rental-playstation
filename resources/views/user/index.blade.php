@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('header', 'Data Pegawai (User)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-slate-400 text-sm">Kelola akun admin dan kasir.</p>
    <a href="{{ route('user.create') }}" class="bg-ps-blue hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
        <i class="fa-solid fa-user-plus"></i> Tambah Pegawai
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($users as $u)
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 flex flex-col items-center text-center hover:border-ps-blue/50 transition relative overflow-hidden">
        
        <div class="absolute top-4 right-4 text-xs font-bold px-2 py-1 rounded {{ $u->role == 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }} uppercase">
            {{ $u->role }}
        </div>

        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center text-3xl font-bold text-white mb-4 shadow-lg">
            {{ substr($u->nama, 0, 1) }}
        </div>

        <h3 class="text-lg font-bold text-white">{{ $u->nama }}</h3>
        <p class="text-sm text-slate-400 mb-6">{{ '@'.$u->username }}</p>

        <div class="w-full flex gap-2 mt-auto">
            <a href="{{ route('user.edit', $u->id_user) }}" class="flex-1 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-medium transition">
                Edit
            </a>
            @if(auth()->id() != $u->id_user)
            <form action="{{ route('user.destroy', $u->id_user) }}" method="POST" onsubmit="return confirm('Hapus pegawai ini?')" class="w-10">
                @csrf @method('DELETE')
                <button type="submit" class="w-full h-full rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition flex items-center justify-center">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
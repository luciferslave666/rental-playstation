@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('header', 'Data Pelanggan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-slate-400 text-sm">Kelola data member rental Anda disini.</p>
    <a href="{{ route('pelanggan.create') }}" class="bg-ps-blue hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Pelanggan
    </a>
</div>

<div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-200 uppercase font-bold text-xs">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Nama Pelanggan</th>
                    <th class="px-6 py-4">No. HP / WhatsApp</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($pelanggans as $p)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 text-slate-500">#PLG-{{ $p->id_pelanggan }}</td>
                    <td class="px-6 py-4 font-bold text-white">{{ $p->nama_pelanggan }}</td>
                    <td class="px-6 py-4 font-mono text-ps-blue">{{ $p->no_hp }}</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-3">
                        <a href="{{ route('pelanggan.edit', $p->id_pelanggan) }}" class="text-slate-400 hover:text-yellow-400 transition" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-400 transition" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                        Belum ada data pelanggan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $pelanggans->links() }}
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Mulai Transaksi Baru')
@section('header', 'Mulai Sesi Baru')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl relative">
        
        @if ($errors->any())
        <div class="mb-6 bg-red-500/10 border border-red-500/50 rounded-xl p-4 relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                <h4 class="text-red-500 font-bold">Gagal Menyimpan Transaksi</h4>
            </div>
            <ul class="list-disc list-inside text-sm text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="absolute top-0 right-0 w-32 h-32 bg-ps-blue/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                <div>
                    <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-ps-blue flex items-center justify-center text-sm"><i class="fa-solid fa-user"></i></span>
                        Data & Ruangan
                    </h3>

                    <div class="mb-8 border-b border-slate-700 pb-8">
                        <label class="block text-slate-400 text-sm font-semibold mb-3">Identitas Pelanggan</label>
                        <div class="flex bg-slate-900 p-1 rounded-xl mb-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="tipe_pelanggan" value="lama" class="sr-only peer" checked onchange="togglePelanggan(false)">
                                <div class="text-center py-2 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-slate-700 peer-checked:text-white transition">
                                    <i class="fa-solid fa-users mr-1"></i> Member Lama
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="tipe_pelanggan" value="baru" class="sr-only peer" onchange="togglePelanggan(true)">
                                <div class="text-center py-2 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-emerald-600 peer-checked:text-white transition">
                                    <i class="fa-solid fa-user-plus mr-1"></i> Input Baru
                                </div>
                            </label>
                        </div>
                        <div id="input-member-lama">
                            <select name="id_pelanggan" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition cursor-pointer">
                                <option value="" disabled selected>-- Cari Nama Pelanggan --</option>
                                @foreach($pelanggans as $p)
                                    <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }} ({{ $p->no_hp }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="input-member-baru" class="hidden space-y-3">
                            <input type="text" name="new_nama" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Nama Lengkap">
                            <input type="number" name="new_no_hp" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Nomor HP / WhatsApp">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-sm font-semibold mb-3">Pilih Ruangan (Available)</label>
                        <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($ruangans as $r)
                            <label class="cursor-pointer">
                                <input type="radio" name="id_ruangan" value="{{ $r->id_ruangan }}" 
                                       data-tarif="{{ (int) $r->tarif_per_jam }}" 
                                       class="peer hidden" 
                                       required 
                                       onchange="hitungTotal()"
                                       {{ request('room_id') == $r->id_ruangan ? 'checked' : '' }}> 
                                
                                <div class="bg-slate-900 border border-slate-700 p-3 rounded-xl hover:border-slate-500 peer-checked:border-ps-blue peer-checked:bg-ps-blue/10 peer-checked:shadow-[0_0_15px_rgba(0,112,209,0.3)] transition-all flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-xl text-slate-400 peer-checked:text-ps-blue">
                                            {{ $r->tipe_ruangan == 'VIP' ? '📺' : '🎮' }}
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold text-sm">{{ $r->nomor_ruangan }}</h4>
                                            <p class="text-xs text-slate-400">{{ $r->tipe_ruangan }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-ps-blue font-bold">Rp {{ number_format($r->tarif_per_jam, 0,',','.') }}</p>
                                        <p class="text-[10px] text-slate-500">/jam</p>
                                    </div>
                                </div>
                            </label>
                            @empty
                                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-center text-sm">
                                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Semua ruangan penuh!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900/50 rounded-2xl p-6 border border-slate-700/50 flex flex-col h-full" id="area-kalkulator">
                    <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-yellow-500"></i> Kalkulator Sewa
                    </h3>

                    <div class="mb-6">
                        <label class="block text-slate-400 text-sm font-semibold mb-2">Mau Main Berapa Jam?</label>
                        <div class="flex items-center gap-4">
                            <input type="number" id="input_durasi" name="durasi_custom" min="1" class="w-full bg-slate-900 border border-slate-700 text-white text-center text-2xl font-bold rounded-xl px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition" placeholder="0" oninput="hitungTotal()">
                            <span class="text-slate-400 font-bold">Jam</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">*Kosongkan input ini jika ingin Open Billing (Main sepuasnya).</p>
                    </div>

                    <div class="mb-6 pt-6 border-t border-slate-700">
                        <p class="text-xs text-slate-400 font-semibold uppercase mb-3">Atau Pilih Paket Hemat:</p>
                        <div class="space-y-2">
                            @foreach($pakets as $pkt)
                            <label class="cursor-pointer block">
                                <input type="radio" name="id_paket" value="{{ $pkt->id_paket }}" 
                                       data-harga="{{ (int) $pkt->harga }}" 
                                       data-durasi="{{ (int) $pkt->durasi_menit }}" 
                                       class="peer hidden" onchange="pilihPaket(this)">
                                <div class="bg-slate-800 border border-slate-700 p-3 rounded-xl hover:border-yellow-500/50 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/10 flex items-center justify-between transition">
                                    <div class="text-sm font-bold text-white">{{ $pkt->nama_paket }}</div>
                                    <div class="text-xs font-bold text-yellow-500">Rp {{ number_format($pkt->harga/1000) }}k</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-auto bg-slate-800 p-6 rounded-xl border border-slate-700 text-center">
                        <p class="text-slate-400 text-sm mb-1">Total Estimasi Bayar</p>
                        <h2 id="display_total" class="text-4xl font-bold text-white mb-2">Open Billing</h2>
                        <p id="display_waktu" class="text-xs text-emerald-400 font-mono hidden">Waktu Main: 0 Jam</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-slate-700 pt-6 mt-8">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-700 transition font-medium">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-ps-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-1">
                    <i class="fa-solid fa-play mr-2"></i> Mulai Main
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let tarifRuangan = 0;

    document.addEventListener("DOMContentLoaded", function() {
        // PERBAIKAN: Jika ada request room_id, hitung langsung
        const adaRuangan = document.querySelector('input[name="id_ruangan"]:checked');
        if(adaRuangan) {
            hitungTotal();
            // Scroll sedikit ke kanan agar user sadar sudah terpilih (opsional untuk mobile)
        }
    });

    function togglePelanggan(isBaru) {
        const inputLama = document.getElementById('input-member-lama');
        const inputBaru = document.getElementById('input-member-baru');
        
        if (isBaru) {
            inputLama.classList.add('hidden');
            inputBaru.classList.remove('hidden');
            document.querySelector('select[name="id_pelanggan"]').value = "";
        } else {
            inputLama.classList.remove('hidden');
            inputBaru.classList.add('hidden');
            document.querySelector('input[name="new_nama"]').value = "";
            document.querySelector('input[name="new_no_hp"]').value = "";
        }
    }

    // Fungsi Pilih Paket (Dengan Jeda agar Radio Tercentang dulu)
    function pilihPaket(el) {
        const inputDurasi = document.getElementById('input_durasi');
        if(inputDurasi) inputDurasi.value = "";
        
        // Jeda 50ms sangat penting untuk menunggu browser update status checked
        setTimeout(hitungTotal, 50);
    }

    function hitungTotal() {
        // 1. Ambil Tarif Ruangan
        const ruanganChecked = document.querySelector('input[name="id_ruangan"]:checked');
        if (ruanganChecked) {
            tarifRuangan = parseInt(ruanganChecked.getAttribute('data-tarif')) || 0;
        }

        // 2. Ambil Input Manual
        const inputDurasi = document.getElementById('input_durasi');
        let durasiJam = inputDurasi ? (parseInt(inputDurasi.value) || 0) : 0;

        // 3. Ambil Paket (Pastikan pakai querySelector terbaru)
        const paketChecked = document.querySelector('input[name="id_paket"]:checked');
        let hargaPaket = 0;
        let durasiPaket = 0;
        let isPaketActive = false;

        if (paketChecked) {
            hargaPaket = parseInt(paketChecked.getAttribute('data-harga')) || 0;
            durasiPaket = parseInt(paketChecked.getAttribute('data-durasi')) || 0;
            isPaketActive = true;
        }

        // --- LOGIKA TAMPILAN ---
        if (durasiJam > 0) {
            // Jika ketik manual, uncheck paket
            const allPaket = document.getElementsByName('id_paket');
            for(let r of allPaket) r.checked = false;
            
            const total = durasiJam * tarifRuangan;
            updateDisplay(total, durasiJam + " Jam (Custom)");
        } 
        else if (isPaketActive) {
            // Jika paket aktif
            if(inputDurasi) inputDurasi.value = "";
            updateDisplay(hargaPaket, (durasiPaket/60) + " Jam (Paket)");
        } 
        else {
            // Default: Open Billing
            const elTotal = document.getElementById('display_total');
            const elWaktu = document.getElementById('display_waktu');
            
            if(elTotal) elTotal.innerText = "Open Billing";
            if(elWaktu) elWaktu.classList.add('hidden');
        }
    }

    function updateDisplay(total, infoWaktu) {
        const elTotal = document.getElementById('display_total');
        const elWaktu = document.getElementById('display_waktu');
        const formatted = new Intl.NumberFormat('id-ID').format(total);

        if(elTotal) elTotal.innerText = "Rp " + formatted;
        if(elWaktu) {
            elWaktu.innerText = "Waktu Main: " + infoWaktu;
            elWaktu.classList.remove('hidden');
        }
    }
</script>
@endsection
@extends('layouts.app')

@section('title', 'Mulai Transaksi Baru')
@section('header', 'Mulai Sesi Baru')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 shadow-2xl relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-32 h-32 bg-ps-blue/10 rounded-full blur-2xl -mr-10 -mt-10"></div>

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
                                       class="peer sr-only" required onchange="hitungTotal()">
                                
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

                <div class="bg-slate-900/50 rounded-2xl p-6 border border-slate-700/50 flex flex-col h-full">
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
                                       class="peer sr-only" onchange="pilihPaket(this)">
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
    // Variable Global
    let tarifRuangan = 0;

    // Pastikan DOM sudah siap sebelum script jalan
    document.addEventListener("DOMContentLoaded", function() {
        hitungTotal();
    });

    // 1. Fungsi Toggle Pelanggan (Lama vs Baru)
    function togglePelanggan(isBaru) {
        const inputLama = document.getElementById('input-member-lama');
        const inputBaru = document.getElementById('input-member-baru');
        
        if (isBaru) {
            if(inputLama) inputLama.classList.add('hidden');
            if(inputBaru) inputBaru.classList.remove('hidden');
            const selectPelanggan = document.querySelector('select[name="id_pelanggan"]');
            if(selectPelanggan) selectPelanggan.value = "";
        } else {
            if(inputLama) inputLama.classList.remove('hidden');
            if(inputBaru) inputBaru.classList.add('hidden');
            const namaBaru = document.querySelector('input[name="new_nama"]');
            const hpBaru = document.querySelector('input[name="new_no_hp"]');
            if(namaBaru) namaBaru.value = "";
            if(hpBaru) hpBaru.value = "";
        }
    }

    // 2. Logika Hitung Otomatis (Live Calculator)
    function hitungTotal() {
        // Cek tarif ruangan yang dipilih
        const ruanganChecked = document.querySelector('input[name="id_ruangan"]:checked');
        
        if (ruanganChecked) {
            // Gunakan parseInt dengan fallback 0 agar tidak NaN
            tarifRuangan = parseInt(ruanganChecked.getAttribute('data-tarif')) || 0;
        }

        // Ambil input jam
        const inputDurasi = document.getElementById('input_durasi');
        const durasiJam = inputDurasi ? (parseInt(inputDurasi.value) || 0) : 0;

        // Ambil data Paket
        const paketRadios = document.getElementsByName('id_paket');
        let paketDipilih = false;
        let hargaPaket = 0;
        let durasiPaket = 0;

        // Cek apakah ada paket yang dicentang
        for(let r of paketRadios) {
            if(r.checked) {
                paketDipilih = true;
                hargaPaket = parseInt(r.getAttribute('data-harga')) || 0;
                durasiPaket = parseInt(r.getAttribute('data-durasi')) || 0;
                break;
            }
        }

        // --- LOGIKA PENENTUAN TAMPILAN ---
        if (durasiJam > 0) {
            // KASUS A: User ketik jam manual (Custom)
            // Matikan pilihan paket jika user mengetik manual
            for(let r of paketRadios) r.checked = false;
            
            const total = durasiJam * tarifRuangan;
            updateDisplay(total, durasiJam + " Jam (Custom)");

        } else if (paketDipilih) {
            // KASUS B: User pilih paket
            // Kosongkan input manual
             if(inputDurasi) inputDurasi.value = "";
             
            updateDisplay(hargaPaket, (durasiPaket/60) + " Jam (Paket)");

        } else {
            // KASUS C: Open Billing (Default / Tidak ada input)
            const elTotal = document.getElementById('display_total');
            const elWaktu = document.getElementById('display_waktu');
            
            if(elTotal) elTotal.innerText = "Open Billing";
            if(elWaktu) elWaktu.classList.add('hidden');
        }
    }

    // 3. Helper saat User Klik Paket
    function pilihPaket(el) {
        // Kosongkan input jam manual
        const inputDurasi = document.getElementById('input_durasi');
        if(inputDurasi) inputDurasi.value = "";
        hitungTotal();
    }

    // 4. Helper update text di layar
    function updateDisplay(total, infoWaktu) {
        const elTotal = document.getElementById('display_total');
        const elWaktu = document.getElementById('display_waktu');

        // Format Rupiah yang aman
        const formatted = new Intl.NumberFormat('id-ID').format(total);

        if(elTotal) elTotal.innerText = "Rp " + formatted;
        
        if(elWaktu) {
            elWaktu.innerText = "Waktu Main: " + infoWaktu;
            elWaktu.classList.remove('hidden');
        }
    }
</script>
@endsection
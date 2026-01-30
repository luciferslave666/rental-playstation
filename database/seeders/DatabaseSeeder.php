<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\Konsol;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\Paket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BUAT AKUN ADMIN & KASIR
        // ==========================================
        User::create([
            'nama' => 'Sendi Dwi Putra',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'nama' => 'Dandy Muhammad Fadillah',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);
        
        $users = User::all();

        // ==========================================
        // 2. DATA RUANGAN & KONSOL
        // ==========================================
        $dataRuangan = [
            // VIP (Mahal - 30rb/40rb)
            ['VIP-01', 30000, 'VIP', 'PS5 Pro, TV 55" 4K, Sofa Recliner, AC, Soundbar'],
            ['VIP-02', 30000, 'VIP', 'PS5 Pro, TV 55" 4K, Sofa L-Shape, AC'],
            ['VIP-03', 35000, 'VIP', 'PS5 Pro, TV 65" 4K, Gaming Chair, AC, Private'],
            ['VIP-04', 30000, 'VIP', 'PS5 Standard, TV 50" 4K, Bean Bag Jumbo, AC'],
            ['VIP-05', 40000, 'VIP', 'PS5 Pro, TV 70" 4K, Sultan Sofa, AC, Netflix'],
            
            // Reguler (Murah - 10rb/15rb)
            ['REG-01', 15000, 'Reguler', 'PS4 Pro, TV 43" FHD, Karpet, Kipas Angin'],
            ['REG-02', 15000, 'Reguler', 'PS4 Pro, TV 43" FHD, Karpet, Kipas Angin'],
            ['REG-03', 15000, 'Reguler', 'PS4 Slim, TV 40" FHD, Lesehan, Kipas Angin'],
            ['REG-04', 15000, 'Reguler', 'PS4 Slim, TV 40" FHD, Lesehan, Kipas Angin'],
            ['REG-05', 12000, 'Reguler', 'PS4 Fat, TV 32" HD, Karpet, Kipas Angin'],
            ['REG-06', 12000, 'Reguler', 'PS4 Fat, TV 32" HD, Karpet, Kipas Angin'],
            ['REG-07', 10000, 'Reguler', 'PS3 Slim, TV 32" HD, Karpet, Kipas Angin'],
            ['REG-08', 10000, 'Reguler', 'PS3 Slim, TV 32" HD, Karpet, Kipas Angin'],
        ];

        foreach ($dataRuangan as $idx => $r) {
            $ruangan = Ruangan::create([
                'nomor_ruangan' => $r[0],
                'tarif_per_jam' => $r[1],
                'tipe_ruangan' => $r[2],
                'deskripsi_fasilitas' => $r[3],
            ]);

            // Auto create console berdasarkan tipe ruangan
            $jenisKonsol = ($r[2] == 'VIP') ? 'PS5' : (($r[1] >= 15000) ? 'PS4-PRO' : 'PS4-SLIM');
            Konsol::create([
                'id_ruangan' => $ruangan->id_ruangan,
                'seri_konsol' => $jenisKonsol . '-SERIES-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $ruanganVIP = Ruangan::where('tipe_ruangan', 'VIP')->get();
        $ruanganReguler = Ruangan::where('tipe_ruangan', 'Reguler')->get();

        // ==========================================
        // 3. DATA PAKET (PERBEDAAN HARGA JELAS)
        // ==========================================
        // Paket VIP (Harga lebih mahal)
        $paketVIP = [
            Paket::create(['nama_paket' => 'Paket VIP 2 Jam', 'durasi_menit' => 120, 'harga' => 55000]), // Hemat 5rb
            Paket::create(['nama_paket' => 'Paket VIP 3 Jam', 'durasi_menit' => 180, 'harga' => 80000]), // Hemat 10rb
            Paket::create(['nama_paket' => 'Paket VIP 5 Jam', 'durasi_menit' => 300, 'harga' => 130000]), // Hemat 20rb
        ];

        // Paket Reguler (Harga rakyat)
        $paketReguler = [
            Paket::create(['nama_paket' => 'Paket Reguler 3 Jam', 'durasi_menit' => 180, 'harga' => 40000]), // Hemat 5rb
            Paket::create(['nama_paket' => 'Paket Reguler 5 Jam', 'durasi_menit' => 300, 'harga' => 65000]), // Hemat 10rb
            Paket::create(['nama_paket' => 'Paket Bergadang (7 Jam)', 'durasi_menit' => 420, 'harga' => 85000]),
        ];

        // ==========================================
        // 4. DATA PELANGGAN (50 ORANG)
        // ==========================================
        $namaPelanggan = [
            'Asep Surasep', 'Budi Santoso', 'Cecep Gorbachev', 'Dedi Mizwar', 'Eko Patrio',
            'Fajar Sadboy', 'Galih Ginanjar', 'Hendra Setiawan', 'Indra Bekti', 'Joko Anwar',
            'Kevin Sanjaya', 'Lesti Kejora', 'Maulana Malik', 'Naufal Samudra', 'Opick Tomboati',
            'Putri Delina', 'Qorygore', 'Rizky Billar', 'Sule Prikitiw', 'Taufik Hidayat',
            'Udin Sedunia', 'Vicky Prasetyo', 'Wendy Cagur', 'Xabiru', 'Yoga Arizona',
            'Zayn Malik KW', 'Aldi Taher', 'Baim Wong', 'Cinta Laura', 'Desta Mahendra',
            'Erik Tohir', 'Fadil Jaidi', 'Gading Marten', 'Habib Jafar', 'Irfan Hakim',
            'Jerome Polin', 'Kaesang Pangarep', 'Luna Maya', 'Marshel Widianto', 'Nagita Slavina',
            'Olga Syahputra', 'Prilly Latuconsina', 'Raditya Dika', 'Raffi Ahmad', 'Sandra Dewi',
            'Teria Yulis', 'Uus', 'Vincent Rompies', 'Wika Salim', 'Youtuber Gaming'
        ];

        foreach ($namaPelanggan as $nama) {
            Pelanggan::create([
                'nama_pelanggan' => $nama,
                'no_hp' => '08' . rand(11, 99) . rand(10000000, 99999999),
            ]);
        }
        $pelanggans = Pelanggan::all();

        // ==========================================
        // 5. GENERATE HISTORI TRANSAKSI (30 HARI TERAKHIR)
        // ==========================================
        // Kita akan buat grafik pendapatan naik turun agar terlihat real
        
        $now = Carbon::now();
        
        // Loop mundur 30 hari ke belakang
        for ($i = 30; $i >= 1; $i--) {
            $tanggal = $now->copy()->subDays($i);
            
            // Random jumlah transaksi per hari (Weekend lebih ramai)
            $isWeekend = $tanggal->isWeekend();
            $jumlahTransaksi = $isWeekend ? rand(15, 25) : rand(5, 12);

            for ($j = 0; $j < $jumlahTransaksi; $j++) {
                
                // 1. Tentukan Ruangan (VIP / Reguler)
                $isVIP = rand(0, 100) < 40; // 40% kemungkinan VIP
                $ruangan = $isVIP ? $ruanganVIP->random() : $ruanganReguler->random();

                // 2. Tentukan Jenis Billing (Paket / Open)
                $pakePaket = rand(0, 100) < 60; // 60% orang suka paket
                
                $jamMulai = rand(9, 22); // Buka jam 9 pagi - 10 malam
                $waktuMulai = $tanggal->copy()->setHour($jamMulai)->setMinute(rand(0, 59));
                
                $idPaket = null;
                $totalBiaya = 0;
                $durasiMenit = 0;

                if ($pakePaket) {
                    // Ambil paket yang SESUAI tipe ruangan
                    $paketDipilih = $isVIP ? collect($paketVIP)->random() : collect($paketReguler)->random();
                    
                    $idPaket = $paketDipilih->id_paket;
                    $totalBiaya = $paketDipilih->harga;
                    $durasiMenit = $paketDipilih->durasi_menit;
                } else {
                    // Open Billing (Main 1-4 Jam)
                    $durasiJam = rand(1, 4);
                    $durasiMenit = $durasiJam * 60;
                    $totalBiaya = $durasiJam * $ruangan->tarif_per_jam;
                }

                $waktuSelesai = $waktuMulai->copy()->addMinutes($durasiMenit);

                // Simpan History Lunas
                Transaksi::create([
                    'id_user' => $users->random()->id_user,
                    'id_pelanggan' => $pelanggans->random()->id_pelanggan,
                    'id_ruangan' => $ruangan->id_ruangan,
                    'id_paket' => $idPaket,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'total_biaya' => $totalBiaya,
                    'status_pembayaran' => 'Lunas',
                    'created_at' => $waktuMulai, // Penting untuk grafik!
                    'updated_at' => $waktuSelesai,
                ]);
            }
        }

        // ==========================================
        // 6. GENERATE TRANSAKSI AKTIF (SEKARANG)
        // ==========================================
        // Buat 6 ruangan sedang terpakai saat ini
        
        $ruanganAktif = Ruangan::inRandomOrder()->take(6)->get();

        foreach ($ruanganAktif as $ruangan) {
            $isVIP = $ruangan->tipe_ruangan == 'VIP';
            $pakePaket = rand(0, 1); // 50:50

            // Mereka mulai main antara 10 menit s/d 2 jam yang lalu
            $waktuMulai = Carbon::now()->subMinutes(rand(10, 120));
            
            $idPaket = null;
            $waktuSelesai = null;
            $totalBiaya = 0;

            if ($pakePaket) {
                // Sedang main paket
                $paketDipilih = $isVIP ? collect($paketVIP)->random() : collect($paketReguler)->random();
                
                $idPaket = $paketDipilih->id_paket;
                $totalBiaya = $paketDipilih->harga;
                // Waktu selesai sudah ditentukan di masa depan
                $waktuSelesai = $waktuMulai->copy()->addMinutes($paketDipilih->durasi_menit);
            } else {
                // Sedang main Open Billing
                // Waktu selesai NULL (belum stop)
                $waktuSelesai = null;
                $totalBiaya = 0; // Belum dihitung
            }

            Transaksi::create([
                'id_user' => $users->random()->id_user,
                'id_pelanggan' => $pelanggans->random()->id_pelanggan,
                'id_ruangan' => $ruangan->id_ruangan,
                'id_paket' => $idPaket,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'total_biaya' => $totalBiaya,
                'status_pembayaran' => 'Belum Lunas', // Masih main
            ]);
        }
    }
}
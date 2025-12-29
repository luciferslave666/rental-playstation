<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\Konsol;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT USER (ADMIN & KASIR)
        // Sesuai PDF: id_user, nama, username, password, role [cite: 89-96]
        $admin = User::create([
            'nama' => 'Sendi Dwi Putra',
            'username' => 'admin',
            'password' => Hash::make('password'), // Password default: password
            'role' => 'admin'
        ]);

        $kasir = User::create([
            'nama' => 'Dandy Muhammad Fadillah',
            'username' => 'kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir'
        ]);

        // 2. BUAT DATA RUANGAN (REGULER & VIP)
        // Sesuai PDF: tipe_ruangan Reguler/VIP, tarif_per_jam [cite: 51, 107-111]
        
        // --- Ruangan VIP ---
        $vip1 = Ruangan::create([
            'nomor_ruangan' => 'VIP-01',
            'tarif_per_jam' => 25000,
            'tipe_ruangan' => 'VIP',
            'deskripsi_fasilitas' => 'TV 50 Inch 4K, Sofa Premium Empuk, AC Dingin, Bonus Minuman'
        ]);

        $vip2 = Ruangan::create([
            'nomor_ruangan' => 'VIP-02',
            'tarif_per_jam' => 25000,
            'tipe_ruangan' => 'VIP',
            'deskripsi_fasilitas' => 'TV 50 Inch 4K, Bean Bag Besar, AC Dingin, Sound Bar'
        ]);

        // --- Ruangan Reguler ---
        $reg1 = Ruangan::create([
            'nomor_ruangan' => 'REG-01',
            'tarif_per_jam' => 15000,
            'tipe_ruangan' => 'Reguler',
            'deskripsi_fasilitas' => 'TV 32 Inch, Karpet Nyaman, Kipas Angin'
        ]);

        $reg2 = Ruangan::create([
            'nomor_ruangan' => 'REG-02',
            'tarif_per_jam' => 15000,
            'tipe_ruangan' => 'Reguler',
            'deskripsi_fasilitas' => 'TV 32 Inch, Karpet Nyaman, Kipas Angin'
        ]);

        // 3. BUAT DATA KONSOL
        // Sesuai PDF: Relasi 1-to-1 dengan ruangan [cite: 54, 117-120, 137]
        Konsol::create([
            'id_ruangan' => $vip1->id_ruangan,
            'seri_konsol' => 'PS5-PRO-001'
        ]);

        Konsol::create([
            'id_ruangan' => $vip2->id_ruangan,
            'seri_konsol' => 'PS5-DIGITAL-002'
        ]);

        Konsol::create([
            'id_ruangan' => $reg1->id_ruangan,
            'seri_konsol' => 'PS4-SLIM-001'
        ]);

        Konsol::create([
            'id_ruangan' => $reg2->id_ruangan,
            'seri_konsol' => 'PS4-FAT-002'
        ]);

        // 4. BUAT DATA PELANGGAN
        // Sesuai PDF: nama_pelanggan, no_hp [cite: 100-101]
        $p1 = Pelanggan::create([
            'nama_pelanggan' => 'Naufal Putra',
            'no_hp' => '081234567890'
        ]);

        $p2 = Pelanggan::create([
            'nama_pelanggan' => 'Budi Santoso',
            'no_hp' => '089876543210'
        ]);

        // 5. BUAT CONTOH TRANSAKSI
        // Sesuai PDF: Transaksi menghubungkan user, ruangan, dan pelanggan [cite: 121-132]
        
        // Transaksi 1: Sudah Selesai & Lunas
        Transaksi::create([
            'id_user' => $admin->id_user,
            'id_ruangan' => $vip1->id_ruangan,
            'id_pelanggan' => $p1->id_pelanggan,
            'waktu_mulai' => Carbon::now()->subHours(3),
            'waktu_selesai' => Carbon::now()->subHours(1),
            'total_biaya' => 50000, // 2 Jam x 25.000
            'status_pembayaran' => 'Lunas'
        ]);

        // Transaksi 2: Sedang Main (Aktif)
        // Waktu selesai NULL berarti masih main [cite: 130]
        Transaksi::create([
            'id_user' => $kasir->id_user,
            'id_ruangan' => $reg1->id_ruangan,
            'id_pelanggan' => $p2->id_pelanggan,
            'waktu_mulai' => Carbon::now()->subMinutes(30),
            'waktu_selesai' => null, // Masih main
            'total_biaya' => 0,      // Belum dihitung
            'status_pembayaran' => 'Belum Lunas'
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    protected $fillable = [
        'nomor_ruangan',
        'tarif_per_jam',
        'tipe_ruangan',
        'deskripsi_fasilitas'
    ];

    // Relasi Konsol
    public function konsol()
    {
        return $this->hasOne(Konsol::class, 'id_ruangan');
    }

    // Relasi History Semua Transaksi
    public function transaksi() 
    { 
        return $this->hasMany(Transaksi::class, 'id_ruangan'); 
    }

    // --- TAMBAHAN PENTING (Agar Dashboard Merah) ---
    public function transaksiAktif()
    {
        // Ambil transaksi yang statusnya BELUM LUNAS
        return $this->hasOne(Transaksi::class, 'id_ruangan')
                    ->where('status_pembayaran', 'Belum Lunas')
                    ->latest();
    }
}
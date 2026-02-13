<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'no_hp'
    ];

    // --- TAMBAHKAN BAGIAN INI AGAR CONTROLLER TIDAK ERROR ---
    public function transaksi()
    {
        // Memberitahu Laravel bahwa Pelanggan ini terhubung ke tabel Transaksi
        return $this->hasMany(Transaksi::class, 'id_pelanggan');
    }
}
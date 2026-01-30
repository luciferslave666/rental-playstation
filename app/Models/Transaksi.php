<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_user',
        'id_ruangan',
        'id_pelanggan',
        'waktu_mulai',
        'waktu_selesai',
        'total_biaya',
        'status_pembayaran'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    // Relasi ke tabel lain
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
        public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }
}
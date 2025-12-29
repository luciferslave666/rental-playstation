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

    // Relasi: Satu ruangan punya satu konsol
    public function konsol()
    {
        return $this->hasOne(Konsol::class, 'id_ruangan');
    }
    public function transaksi() { return $this->hasMany(Transaksi::class, 'id_ruangan'); }
}
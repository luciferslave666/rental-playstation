<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsol extends Model
{
    protected $table = 'konsol';
    protected $primaryKey = 'id_konsol';

    protected $fillable = [
        'id_ruangan',
        'seri_konsol'
    ];

    // Relasi kebalikannya: Konsol milik Ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }
}
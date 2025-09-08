<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AplikasiKepegawaian extends Model
{
    use HasFactory;

    protected $table = 'aplikasi_kepegawaian';

    protected $fillable = [
        'nama_aplikasi',
        'sumber',
        'keterangan',
        'link',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk data aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk sumber tertentu
    public function scopeSumber($query, $sumber)
    {
        return $query->where('sumber', $sumber);
    }
}

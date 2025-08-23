<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsulanValidasi extends Model
{
    protected $table = 'usulan_validasi';

    protected $fillable = [
        'usulan_id',
        'role',
        'data',
        'submitted_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function usulan()
    {
        return $this->belongsTo(\App\Models\KepegawaianUniversitas\Usulan::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(\App\Models\KepegawaianUniversitas\Pegawai::class, 'submitted_by');
    }
}

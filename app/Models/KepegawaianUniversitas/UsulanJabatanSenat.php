<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Model;

class UsulanJabatanSenat extends Model
{
    protected $table = 'usulan_jabatan_senat';

    protected $fillable = [
        'usulan_id',
        'anggota_senat_id',
        'keputusan',
        'catatan',
        'diputuskan_pada',
    ];

    public function usulan()
    {
        return $this->belongsTo(Usulan::class);
    }
}

<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Model;

class SubUnitKerja extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_kerja_id',
        'nama',
    ];

    /**
     * Get the unit kerja that owns the sub unit kerja.
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    /**
     * Get the sub sub unit kerjas for the sub unit kerja.
     */
    public function subSubUnitKerjas()
    {
        return $this->hasMany(SubSubUnitKerja::class);
    }
}

<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * Get the sub unit kerjas for the unit kerja.
     */
    public function subUnitKerjas()
    {
        return $this->hasMany(SubUnitKerja::class);
    }

    /**
     * Get the sub sub unit kerjas through sub unit kerjas.
     */
    public function subSubUnitKerjas()
    {
        return $this->hasManyThrough(SubSubUnitKerja::class, SubUnitKerja::class);
    }
}

<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Model;

class SubSubUnitKerja extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sub_unit_kerja_id',
        'nama',
    ];

    /**
     * Get the sub unit kerja that owns the sub sub unit kerja.
     */
    public function subUnitKerja()
    {
        return $this->belongsTo(SubUnitKerja::class);
    }

    /**
     * Get the unit kerja through sub unit kerja.
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id', 'id');
    }

    /**
     * Get the unit kerja through sub unit kerja using accessor.
     */
    public function getUnitKerjaAttribute()
    {
        if ($this->subUnitKerja && $this->subUnitKerja->unitKerja) {
            return $this->subUnitKerja->unitKerja;
        }
        return null;
    }
}

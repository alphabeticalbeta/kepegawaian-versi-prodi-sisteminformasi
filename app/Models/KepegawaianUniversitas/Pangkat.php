<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    use HasFactory;

    protected $fillable = [
        'pangkat',
        'hierarchy_level',
        'status_pangkat'
    ];

    protected $casts = [
        'hierarchy_level' => 'integer',
    ];

    // Scope untuk mengurutkan berdasarkan hirarki
    public function scopeOrderByHierarchy($query, $direction = 'asc')
    {
        return $query->orderByRaw('
            CASE
                WHEN status_pangkat = "PNS" THEN 1
                WHEN status_pangkat = "PPPK" THEN 2
                WHEN status_pangkat = "Non-ASN" THEN 3
                ELSE 4
            END,
            hierarchy_level ' . $direction . ',
            pangkat ' . $direction
        );
    }

    // Scope untuk pangkat dengan hirarki
    public function scopeWithHierarchy($query)
    {
        return $query->whereNotNull('hierarchy_level');
    }

    // Scope untuk pangkat tanpa hirarki
    public function scopeWithoutHierarchy($query)
    {
        return $query->whereNull('hierarchy_level');
    }

    // Scope untuk mendapatkan semua pangkat dengan hirarki
    public static function getAllHierarchy()
    {
        return self::orderByHierarchy('asc')->get();
    }

    // Accessor untuk badge class berdasarkan status pangkat
    public function getStatusPangkatBadgeClassAttribute()
    {
        switch ($this->status_pangkat) {
            case 'PNS':
                return 'bg-green-100 text-green-800 border-green-300';
            case 'PPPK':
                return 'bg-blue-100 text-blue-800 border-blue-300';
            case 'Non-ASN':
                return 'bg-orange-100 text-orange-800 border-orange-300';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-300';
        }
    }

    // Accessor untuk icon berdasarkan status pangkat
    public function getStatusIconAttribute()
    {
        switch ($this->status_pangkat) {
            case 'PNS':
                return 'shield-check';
            case 'PPPK':
                return 'user-check';
            case 'Non-ASN':
                return 'user';
            default:
                return 'user';
        }
    }

    // Accessor untuk badge class berdasarkan level hirarki
    public function getHierarchyBadgeClassAttribute()
    {
        if (!$this->hierarchy_level) {
            return 'bg-gray-100 text-gray-800';
        }

        if ($this->hierarchy_level >= 1 && $this->hierarchy_level <= 4) {
            return 'bg-red-100 text-red-800'; // Golongan I
        } elseif ($this->hierarchy_level >= 5 && $this->hierarchy_level <= 8) {
            return 'bg-yellow-100 text-yellow-800'; // Golongan II
        } elseif ($this->hierarchy_level >= 9 && $this->hierarchy_level <= 12) {
            return 'bg-green-100 text-green-800'; // Golongan III
        } elseif ($this->hierarchy_level >= 13 && $this->hierarchy_level <= 17) {
            return 'bg-blue-100 text-blue-800'; // Golongan IV
        }

        return 'bg-purple-100 text-purple-800'; // Level khusus
    }

    // Accessor untuk nama golongan
    public function getGolonganAttribute()
    {
        if (!$this->hierarchy_level) {
            return null;
        }

        if ($this->hierarchy_level >= 1 && $this->hierarchy_level <= 4) {
            return 'Golongan I';
        } elseif ($this->hierarchy_level >= 5 && $this->hierarchy_level <= 8) {
            return 'Golongan II';
        } elseif ($this->hierarchy_level >= 9 && $this->hierarchy_level <= 12) {
            return 'Golongan III';
        } elseif ($this->hierarchy_level >= 13 && $this->hierarchy_level <= 17) {
            return 'Golongan IV';
        }

        return 'Golongan Khusus';
    }

    // Accessor untuk informasi hirarki
    public function getHierarchyInfoAttribute()
    {
        if (!$this->hierarchy_level) {
            return 'Tanpa Hirarki';
        }

        return $this->golongan . ' - Level ' . $this->hierarchy_level;
    }

    // Accessor untuk format display lengkap
    public function getFormattedDisplayAttribute()
    {
        $display = $this->pangkat . ' (' . $this->status_pangkat . ')';

        if ($this->hierarchy_level) {
            $display .= ' - Level ' . $this->hierarchy_level;
        }

        return $display;
    }

    // Method untuk mengecek apakah pangkat memiliki hirarki
    public function hasHierarchy()
    {
        return !is_null($this->hierarchy_level);
    }

    // Method untuk mendapatkan pangkat yang bisa dipromosikan
    public function getValidPromotionTargets()
    {
        if (!$this->hasHierarchy()) {
            return collect(); // Non-ASN tidak memiliki promosi
        }

        return self::where('hierarchy_level', '>', $this->hierarchy_level)
                   ->where('status_pangkat', $this->status_pangkat)
                   ->orderByHierarchy('asc')
                   ->get();
    }

    // Relationship dengan pegawai (jika ada)
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}

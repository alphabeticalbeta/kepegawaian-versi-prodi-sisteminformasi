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
        return $this->hasMany(Pegawai::class, 'pangkat_terakhir_id');
    }

    // =====================================================
    // SCOPES UNTUK FILTERING BERDASARKAN JENIS PEGAWAI
    // =====================================================

    /**
     * Scope untuk pangkat yang tersedia untuk Dosen
     */
    public function scopeForDosen($query)
    {
        return $query->whereIn('status_pangkat', ['PNS', 'PPPK'])
                     ->whereNotNull('hierarchy_level')
                     ->where('hierarchy_level', '>=', 9); // Golongan III ke atas untuk Dosen
    }

    /**
     * Scope untuk pangkat yang tersedia untuk Tenaga Kependidikan
     */
    public function scopeForTenagaKependidikan($query)
    {
        return $query->whereIn('status_pangkat', ['PNS', 'PPPK'])
                     ->whereNotNull('hierarchy_level');
    }

    /**
     * Scope untuk pangkat yang lebih tinggi dari level tertentu
     */
    public function scopeHigherThan($query, $level)
    {
        return $query->where('hierarchy_level', '>', $level)
                     ->whereNotNull('hierarchy_level');
    }

    /**
     * Scope untuk pangkat dalam range level tertentu
     */
    public function scopeInHierarchyRange($query, $minLevel, $maxLevel)
    {
        return $query->whereBetween('hierarchy_level', [$minLevel, $maxLevel])
                     ->whereNotNull('hierarchy_level');
    }

    /**
     * Scope untuk pangkat berdasarkan jenis jabatan Tenaga Kependidikan
     */
    public function scopeForJabatanAdministrasi($query)
    {
        return $query->where(function($q) {
            $q->where('pangkat', 'like', '%Penata%')
              ->orWhere('pangkat', 'like', '%Pembina%')
              ->orWhere('pangkat', 'like', '%Pranata%');
        });
    }

    public function scopeForJabatanFungsionalTertentu($query)
    {
        return $query->where(function($q) {
            $q->where('pangkat', 'like', '%Ahli%')
              ->orWhere('pangkat', 'like', '%Terampil%');
        });
    }

    public function scopeForJabatanStruktural($query)
    {
        return $query->where(function($q) {
            $q->where('pangkat', 'like', '%Kepala%')
              ->orWhere('pangkat', 'like', '%Kepala Sub%')
              ->orWhere('pangkat', 'like', '%Kepala Bagian%')
              ->orWhere('pangkat', 'like', '%Kepala Biro%');
        });
    }

    // =====================================================
    // HELPER METHODS UNTUK HIERARCHY
    // =====================================================

    /**
     * Check if this pangkat is higher than another pangkat
     */
    public function isHigherThan(Pangkat $otherPangkat): bool
    {
        if (!$this->hasHierarchy() || !$otherPangkat->hasHierarchy()) {
            return false;
        }

        return $this->hierarchy_level > $otherPangkat->hierarchy_level;
    }

    /**
     * Check if this pangkat is lower than another pangkat
     */
    public function isLowerThan(Pangkat $otherPangkat): bool
    {
        if (!$this->hasHierarchy() || !$otherPangkat->hasHierarchy()) {
            return false;
        }

        return $this->hierarchy_level < $otherPangkat->hierarchy_level;
    }

    /**
     * Check if this pangkat is at the same level as another pangkat
     */
    public function isSameLevelAs(Pangkat $otherPangkat): bool
    {
        if (!$this->hasHierarchy() || !$otherPangkat->hasHierarchy()) {
            return false;
        }

        return $this->hierarchy_level === $otherPangkat->hierarchy_level;
    }

    /**
     * Get the next level pangkat
     */
    public function getNextLevelPangkat()
    {
        if (!$this->hasHierarchy()) {
            return null;
        }

        return self::where('hierarchy_level', $this->hierarchy_level + 1)
                   ->where('status_pangkat', $this->status_pangkat)
                   ->first();
    }

    /**
     * Get the previous level pangkat
     */
    public function getPreviousLevelPangkat()
    {
        if ($this->hierarchy_level <= 1) {
            return null;
        }

        return self::where('hierarchy_level', $this->hierarchy_level - 1)
                   ->where('status_pangkat', $this->status_pangkat)
                   ->first();
    }

    /**
     * Get all pangkat levels higher than current
     */
    public function getHigherLevelPangkats()
    {
        if (!$this->hasHierarchy()) {
            return collect();
        }

        return self::where('hierarchy_level', '>', $this->hierarchy_level)
                   ->where('status_pangkat', $this->status_pangkat)
                   ->orderBy('hierarchy_level', 'asc')
                   ->get();
    }

    /**
     * Get recommended pangkat levels (next 3 levels)
     */
    public function getRecommendedPangkats($limit = 3)
    {
        if (!$this->hasHierarchy()) {
            return collect();
        }

        return self::where('hierarchy_level', '>', $this->hierarchy_level)
                   ->where('hierarchy_level', '<=', $this->hierarchy_level + $limit)
                   ->where('status_pangkat', $this->status_pangkat)
                   ->orderBy('hierarchy_level', 'asc')
                   ->get();
    }

    // =====================================================
    // STATIC METHODS UNTUK UTILITIES
    // =====================================================

    /**
     * Get pangkat statistics
     */
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'pns' => self::where('status_pangkat', 'PNS')->count(),
            'pppk' => self::where('status_pangkat', 'PPPK')->count(),
            'non_asn' => self::where('status_pangkat', 'Non-ASN')->count(),
            'with_hierarchy' => self::whereNotNull('hierarchy_level')->count(),
            'without_hierarchy' => self::whereNull('hierarchy_level')->count(),
            'golongan_i' => self::whereBetween('hierarchy_level', [1, 4])->count(),
            'golongan_ii' => self::whereBetween('hierarchy_level', [5, 8])->count(),
            'golongan_iii' => self::whereBetween('hierarchy_level', [9, 12])->count(),
            'golongan_iv' => self::whereBetween('hierarchy_level', [13, 17])->count(),
        ];
    }

    /**
     * Get pangkat options for dropdown
     */
    public static function getOptionsForDropdown($filter = null)
    {
        $query = self::query();

        if ($filter) {
            $query = $filter($query);
        }

        return $query->orderByHierarchy('asc')
                     ->get()
                     ->mapWithKeys(function ($pangkat) {
                         $label = $pangkat->pangkat;
                         if ($pangkat->hierarchy_level) {
                             $label .= " (Level {$pangkat->hierarchy_level})";
                         }
                         return [$pangkat->id => $label];
                     });
    }

    /**
     * Find pangkat by name (case insensitive)
     */
    public static function findByName($name)
    {
        return self::where('pangkat', 'like', "%{$name}%")->first();
    }

    /**
     * Get pangkat by golongan
     */
    public static function getByGolongan($golongan)
    {
        $ranges = [
            'I' => [1, 4],
            'II' => [5, 8],
            'III' => [9, 12],
            'IV' => [13, 17],
        ];

        if (!isset($ranges[$golongan])) {
            return collect();
        }

        [$min, $max] = $ranges[$golongan];
        return self::whereBetween('hierarchy_level', [$min, $max])
                   ->orderBy('hierarchy_level', 'asc')
                   ->get();
    }
}

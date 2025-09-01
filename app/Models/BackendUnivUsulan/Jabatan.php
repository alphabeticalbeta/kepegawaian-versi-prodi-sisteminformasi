<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Jabatan extends Model
{
    protected $fillable = [
        'jenis_pegawai',
        'jenis_jabatan',
        'jabatan',
        'hierarchy_level'
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    /**
     * Jabatan yang menggunakan jabatan ini sebagai jabatan lama
     */
    public function usulanJabatanLama()
    {
        return $this->hasMany(Usulan::class, 'jabatan_lama_id');
    }

    /**
     * Jabatan yang menggunakan jabatan ini sebagai jabatan tujuan
     */
    public function usulanJabatanTujuan()
    {
        return $this->hasMany(Usulan::class, 'jabatan_tujuan_id');
    }

    /**
     * Pegawai yang memiliki jabatan ini
     */
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'jabatan_terakhir_id');
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope untuk filter berdasarkan jenis pegawai
     */
    public function scopeForJenisPegawai(Builder $query, string $jenisPegawai): Builder
    {
        return $query->where('jenis_pegawai', $jenisPegawai);
    }

    /**
     * Scope untuk filter berdasarkan jenis jabatan
     */
    public function scopeForJenisJabatan(Builder $query, string $jenisJabatan): Builder
    {
        return $query->where('jenis_jabatan', $jenisJabatan);
    }

    /**
     * Scope untuk jabatan yang memiliki hirarki
     */
    public function scopeWithHierarchy(Builder $query): Builder
    {
        return $query->whereNotNull('hierarchy_level');
    }

    /**
     * Scope untuk jabatan yang tidak memiliki hirarki (flat)
     */
    public function scopeWithoutHierarchy(Builder $query): Builder
    {
        return $query->whereNull('hierarchy_level');
    }

    /**
     * Scope untuk urutkan berdasarkan hierarchy level
     */
    public function scopeOrderByHierarchy(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('hierarchy_level', $direction);
    }

    /**
     * Scope untuk jabatan yang bisa diajukan usulan (exclude Struktural)
     */
    public function scopeEligibleForUsulan(Builder $query): Builder
    {
        return $query->where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural');
    }

    // =====================================
    // HIERARCHY HELPER METHODS
    // =====================================

    /**
     * Cek apakah jabatan ini memiliki hirarki
     */
    public function hasHierarchy(): bool
    {
        return !is_null($this->hierarchy_level);
    }

    /**
     * Mendapatkan jabatan level berikutnya (next level) untuk jabatan hierarki
     */
    public function getNextLevel(): ?Jabatan
    {
        if (!$this->hasHierarchy()) {
            return null;
        }

        return static::where('jenis_pegawai', $this->jenis_pegawai)
                    ->where('jenis_jabatan', $this->jenis_jabatan)
                    ->where('hierarchy_level', '>', $this->hierarchy_level)
                    ->orderBy('hierarchy_level', 'asc')
                    ->first();
    }

    /**
     * Mendapatkan jabatan level sebelumnya (previous level) untuk jabatan hierarki
     */
    public function getPreviousLevel(): ?Jabatan
    {
        if (!$this->hasHierarchy()) {
            return null;
        }

        return static::where('jenis_pegawai', $this->jenis_pegawai)
                    ->where('jenis_jabatan', $this->jenis_jabatan)
                    ->where('hierarchy_level', '<', $this->hierarchy_level)
                    ->orderBy('hierarchy_level', 'desc')
                    ->first();
    }

    /**
     * Mendapatkan jabatan yang bisa diajukan dari jabatan saat ini
     */
    public function getValidPromotionTargets(): Collection
    {
        // Dosen hanya bisa dalam Dosen Fungsional (hierarki)
        if ($this->jenis_pegawai === 'Dosen') {
            return $this->getDosenPromotionTargets();
        }

        // Tenaga Kependidikan bisa lintas jenis_jabatan (exclude Struktural)
        if ($this->jenis_pegawai === 'Tenaga Kependidikan') {
            return $this->getTenagaKependidikanPromotionTargets();
        }

        return new Collection();
    }

    /**
     * Mendapatkan target promosi untuk Dosen
     * FIXED: Return Eloquent Collection instead of Support Collection
     */
    private function getDosenPromotionTargets(): Collection
    {
        // Dosen hanya bisa naik dalam Dosen Fungsional (hierarki)
        if ($this->jenis_jabatan === 'Dosen Fungsional' && $this->hasHierarchy()) {
            // Ambil level berikutnya saja (tidak bisa lompat level)
            $nextLevel = $this->getNextLevel();
            if ($nextLevel) {
                // FIXED: Buat Eloquent Collection baru dengan array model
                return new Collection([$nextLevel]);
            }
        }

        // Dosen dengan Tugas Tambahan tidak ada usulan (sudah pindah manual)
        return new Collection();
    }

    /**
     * Mendapatkan target promosi untuk Tenaga Kependidikan
     * FIXED: Return Eloquent Collection instead of Support Collection
     */
    private function getTenagaKependidikanPromotionTargets(): Collection
    {
        // 1. Jika jabatan hierarki (Fungsional Tertentu), bisa ke level berikutnya
        $targets = new Collection();

        if ($this->hasHierarchy()) {
            $nextLevel = $this->getNextLevel();
            if ($nextLevel) {
                $targets = $targets->concat([$nextLevel]);
            }
        }

        // 2. Bisa pindah ke jenis jabatan lain (exclude Struktural)
        $otherJenisJabatan = [
            'Tenaga Kependidikan Fungsional Umum',
            'Tenaga Kependidikan Fungsional Tertentu',
            'Tenaga Kependidikan Tugas Tambahan'
        ];

        // Remove current jenis_jabatan dari list
        $otherJenisJabatan = array_filter($otherJenisJabatan, function($jenis) {
            return $jenis !== $this->jenis_jabatan;
        });

        // Ambil semua jabatan dari jenis lain
        foreach ($otherJenisJabatan as $jenisJabatan) {
            $otherJabatan = static::where('jenis_pegawai', 'Tenaga Kependidikan')
                                 ->where('jenis_jabatan', $jenisJabatan)
                                 ->get();
            $targets = $targets->concat($otherJabatan);
        }

        // FIXED: Return unique Eloquent Collection
        return $targets->unique('id');
    }

    /**
     * Cek apakah bisa naik ke jabatan tertentu
     */
    public function canPromoteTo(Jabatan $targetJabatan): bool
    {
        $validTargets = $this->getValidPromotionTargets();
        return $validTargets->contains('id', $targetJabatan->id);
    }

    /**
     * Mendapatkan jabatan berdasarkan jenis pegawai untuk display di halaman jabatan
     */
    public static function getJabatanForAdmin(): array
    {
        $result = [];

        // Group by jenis_pegawai, lalu by jenis_jabatan
        $jabatans = static::orderBy('jenis_pegawai')
                         ->orderBy('jenis_jabatan')
                         ->orderBy('hierarchy_level')
                         ->orderBy('jabatan')
                         ->get()
                         ->groupBy('jenis_pegawai');

        foreach ($jabatans as $jenisPegawai => $jabatansByPegawai) {
            $result[$jenisPegawai] = $jabatansByPegawai->groupBy('jenis_jabatan');
        }

        return $result;
    }

    /**
     * Mendapatkan jabatan yang bisa diajukan usulan (untuk form usulan)
     */
    public static function getEligibleJabatanForUsulan(): Collection
    {
        return static::eligibleForUsulan()
                    ->orderBy('jenis_pegawai')
                    ->orderBy('jenis_jabatan')
                    ->orderBy('hierarchy_level')
                    ->orderBy('jabatan')
                    ->get();
    }

    // =====================================
    // ACCESSORS & HELPERS
    // =====================================

    /**
     * Get hierarchy info for display
     */
    public function getHierarchyInfoAttribute(): string
    {
        if ($this->hasHierarchy()) {
            return "Level {$this->hierarchy_level}";
        }
        return "Non-Hierarki";
    }

    /**
     * Get badge class based on jenis_jabatan
     */
    public function getJenisJabatanBadgeClassAttribute(): string
    {
        return match($this->jenis_jabatan) {
            'Dosen Fungsional' => 'bg-blue-100 text-blue-800',
            'Dosen dengan Tugas Tambahan' => 'bg-purple-100 text-purple-800',
            'Tenaga Kependidikan Fungsional Umum' => 'bg-green-100 text-green-800',
            'Tenaga Kependidikan Fungsional Tertentu' => 'bg-orange-100 text-orange-800',
            'Tenaga Kependidikan Struktural' => 'bg-red-100 text-red-800',
            'Tenaga Kependidikan Tugas Tambahan' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if this jabatan can be used for usulan
     */
    public function isEligibleForUsulan(): bool
    {
        return $this->jenis_jabatan !== 'Tenaga Kependidikan Struktural';
    }
}

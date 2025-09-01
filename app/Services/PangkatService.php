<?php

namespace App\Services;

use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\Pangkat;
use Illuminate\Support\Collection;

class PangkatService
{
    /**
     * Get available pangkats for a specific pegawai
     */
    public function getAvailablePangkats(Pegawai $pegawai): Collection
    {
        $currentPangkat = $pegawai->pangkat;
        
        if (!$currentPangkat) {
            return collect();
        }
        
        // Filter based on jenis pegawai using model scopes
        if ($pegawai->jenis_pegawai === 'Dosen') {
            // For Dosen, use scope and show higher pangkats only
            $query = Pangkat::forDosen()->higherThan($currentPangkat->hierarchy_level ?? 0);
        } else {
            // For Tenaga Kependidikan, use scope and filter based on jenis jabatan
            $query = Pangkat::forTenagaKependidikan();
            $query = $this->filterPangkatsForTenagaKependidikan($query, $pegawai);
        }
        
        return $query->orderByHierarchy('asc')->get();
    }

    /**
     * Filter pangkats for Tenaga Kependidikan based on jenis jabatan
     */
    protected function filterPangkatsForTenagaKependidikan($query, Pegawai $pegawai)
    {
        $jenisJabatan = $pegawai->jenis_jabatan ?? '';
        
        switch ($jenisJabatan) {
            case 'Tenaga Kependidikan Fungsional Umum':
                // Jabatan Administrasi - use scope
                return $query->forJabatanAdministrasi();
                            
            case 'Tenaga Kependidikan Fungsional Tertentu':
                // Jabatan Fungsional Tertentu - use scope
                return $query->forJabatanFungsionalTertentu();
                            
            case 'Tenaga Kependidikan Struktural':
                // Jabatan Struktural - use scope
                return $query->forJabatanStruktural();
                            
            default:
                // Default - show all pangkats higher than current
                $currentPangkat = $pegawai->pangkat;
                return $query->higherThan($currentPangkat->hierarchy_level ?? 0);
        }
    }

    /**
     * Get current pangkat of pegawai
     */
    public function getCurrentPangkat(Pegawai $pegawai): ?Pangkat
    {
        return $pegawai->pangkat;
    }

    /**
     * Get pangkat by ID
     */
    public function getPangkatById(int $pangkatId): ?Pangkat
    {
        return Pangkat::find($pangkatId);
    }

    /**
     * Get pangkat hierarchy level
     */
    public function getPangkatHierarchyLevel(Pangkat $pangkat): int
    {
        return $pangkat->hierarchy_level ?? 0;
    }

    /**
     * Check if pangkat is higher than current pangkat
     */
    public function isPangkatHigher(Pangkat $targetPangkat, Pangkat $currentPangkat): bool
    {
        return $targetPangkat->isHigherThan($currentPangkat);
    }

    /**
     * Get pangkat options for dropdown
     */
    public function getPangkatOptions(Collection $pangkats): array
    {
        $options = [];
        
        foreach ($pangkats as $pangkat) {
            $label = $pangkat->pangkat;
            if ($pangkat->hierarchy_level) {
                $label .= " (Level {$pangkat->hierarchy_level})";
            }
            
            $options[$pangkat->id] = $label;
        }
        
        return $options;
    }

    /**
     * Get pangkat by status (PNS, PPPK, Non-ASN)
     */
    public function getPangkatsByStatus(string $status): Collection
    {
        return Pangkat::where('status_pangkat', $status)
                     ->orderBy('hierarchy_level', 'asc')
                     ->get();
    }

    /**
     * Get pangkat by hierarchy range
     */
    public function getPangkatsByHierarchyRange(int $minLevel, int $maxLevel): Collection
    {
        return Pangkat::whereBetween('hierarchy_level', [$minLevel, $maxLevel])
                     ->orderBy('hierarchy_level', 'asc')
                     ->get();
    }

    /**
     * Get pangkat statistics
     */
    public function getPangkatStatistics(): array
    {
        return Pangkat::getStatistics();
    }

    /**
     * Validate pangkat selection for pegawai
     */
    public function validatePangkatSelection(Pegawai $pegawai, int $targetPangkatId): array
    {
        $currentPangkat = $this->getCurrentPangkat($pegawai);
        $targetPangkat = $this->getPangkatById($targetPangkatId);
        
        if (!$currentPangkat) {
            return [
                'valid' => false,
                'message' => 'Pegawai tidak memiliki data pangkat saat ini'
            ];
        }
        
        if (!$targetPangkat) {
            return [
                'valid' => false,
                'message' => 'Pangkat yang dipilih tidak ditemukan'
            ];
        }
        
        if (!$this->isPangkatHigher($targetPangkat, $currentPangkat)) {
            return [
                'valid' => false,
                'message' => 'Pangkat yang dipilih harus lebih tinggi dari pangkat saat ini'
            ];
        }
        
        // Check if pangkat is available for this pegawai
        $availablePangkats = $this->getAvailablePangkats($pegawai);
        $isAvailable = $availablePangkats->contains('id', $targetPangkatId);
        
        if (!$isAvailable) {
            return [
                'valid' => false,
                'message' => 'Pangkat yang dipilih tidak tersedia untuk jenis pegawai dan jabatan Anda'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Pangkat valid untuk diajukan'
        ];
    }

    /**
     * Get pangkat recommendation based on current pangkat
     */
    public function getPangkatRecommendation(Pegawai $pegawai): Collection
    {
        $currentPangkat = $this->getCurrentPangkat($pegawai);
        
        if (!$currentPangkat) {
            return collect();
        }
        
        // Use model helper method for recommendations
        return $currentPangkat->getRecommendedPangkats(3);
    }
}

<?php

namespace App\Services;

use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentAccessService
{
    /**
     * Determine disk storage untuk field tertentu
     * 
     * NOTE: Semua dokumen usulan saat ini disimpan di disk 'public' untuk kemudahan akses multi-role
     * Dokumen pegawai pribadi tetap di disk 'local' untuk keamanan
     */
    public function getDiskForField(string $field): string
    {
        // Dokumen pribadi pegawai (sangat sensitif) - tetap di local
        $privateFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir', 'foto'
        ];

        // Dokumen pribadi pegawai - gunakan local
        if (in_array($field, $privateFields)) {
            return 'local';
        }

        // Semua dokumen usulan (termasuk BKD) - gunakan public untuk kemudahan akses multi-role
        // Ini konsisten dengan FileStorageService yang mengupload ke public disk
        return 'public';
    }

    /**
     * Check apakah user bisa mengakses dokumen
     */
    public function canAccessDocument($user, $usulan, $field): bool
    {
        // Debug: cek apakah user dan usulan valid
        if (!$user || !$usulan) {
            Log::error('Invalid user or usulan', [
                'user' => $user ? 'exists' : 'null',
                'usulan' => $usulan ? 'exists' : 'null'
            ]);
            return false;
        }

        $userRole = $user->roles->first()->name ?? null;
        
        // Log access attempt
        Log::info('Document access attempt', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'usulan_id' => $usulan->id,
            'field' => $field,
            'pegawai_id' => $usulan->pegawai_id,
            'user_has_roles' => $user->roles->pluck('name')->toArray()
        ]);

        // Pegawai: hanya dokumen sendiri
        if ($userRole === 'Pegawai') {
            $canAccess = $usulan->pegawai_id === $user->id;
            Log::info('Pegawai access check', [
                'can_access' => $canAccess,
                'usulan_pegawai_id' => $usulan->pegawai_id,
                'user_id' => $user->id,
                'comparison' => $usulan->pegawai_id . ' === ' . $user->id
            ]);
            return $canAccess;
        }
        
        // Admin Fakultas: dokumen pegawai di fakultasnya
        if ($userRole === 'Admin Fakultas') {
            $canAccess = $usulan->pegawai->unitKerja->subUnitKerja->unit_kerja_id === $user->unit_kerja_id;
            Log::info('Admin Fakultas access check', [
                'can_access' => $canAccess,
                'pegawai_fakultas_id' => $usulan->pegawai->unitKerja->subUnitKerja->unit_kerja_id ?? null,
                'admin_fakultas_id' => $user->unit_kerja_id ?? null
            ]);
            return $canAccess;
        }
        
        // Admin Universitas: SEMUA dokumen (full access)
        if ($userRole === 'Admin Universitas') {
            Log::info('Admin Universitas access granted');
            return true;
        }
        
        // Penilai: dokumen yang di-assign
        if ($userRole === 'Penilai Universitas') {
            $canAccess = $usulan->isAssignedToPenilai($user->id);
            Log::info('Penilai access check', [
                'can_access' => $canAccess,
                'is_assigned' => $usulan->isAssignedToPenilai($user->id)
            ]);
            return $canAccess;
        }
        
        // Fallback: jika user tidak memiliki role yang jelas, cek apakah dia pemilik usulan
        if (!$userRole) {
            $canAccess = $usulan->pegawai_id === $user->id;
            Log::info('Fallback access check (no role)', [
                'can_access' => $canAccess,
                'usulan_pegawai_id' => $usulan->pegawai_id,
                'user_id' => $user->id
            ]);
            return $canAccess;
        }
        
        // TEMPORARY FIX: Untuk testing, izinkan akses jika user adalah pemilik usulan
        if ($usulan->pegawai_id === $user->id) {
            Log::info('Temporary fix: allowing access for usulan owner', [
                'usulan_pegawai_id' => $usulan->pegawai_id,
                'user_id' => $user->id,
                'user_role' => $userRole
            ]);
            return true;
        }
        
        Log::warning('Unknown role or access denied', [
            'user_role' => $userRole,
            'user_id' => $user->id
        ]);
        
        return false;
    }

    /**
     * Get allowed fields berdasarkan role
     */
    public function getAllowedFields($user): array
    {
        $userRole = $user->roles->first()->name ?? null;
        
        // Debug: log role check
        Log::info('Getting allowed fields', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'has_roles' => $user->roles->count()
        ]);
        
        $baseFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        // Semua role bisa akses BKD
        $bkdFields = ['bkd_semester_1', 'bkd_semester_2', 'bkd_semester_3', 'bkd_semester_4'];

        if (in_array($userRole, ['Admin Universitas', 'Admin Fakultas', 'Penilai Universitas'])) {
            // Role ini bisa akses dokumen profil juga
            $profilFields = [
                'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
                'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
                'disertasi_thesis_terakhir', 'foto'
            ];
            
            $allowedFields = array_merge($baseFields, $bkdFields, $profilFields);
            Log::info('Admin role - returning all fields', ['count' => count($allowedFields)]);
            return $allowedFields;
        }

        // Pegawai atau user tanpa role: bisa akses dokumen usulan
        $allowedFields = array_merge($baseFields, $bkdFields);
        Log::info('Pegawai/no role - returning usulan fields', ['count' => count($allowedFields)]);
        return $allowedFields;
    }

    /**
     * Validate field access
     */
    public function validateFieldAccess($user, $field): bool
    {
        // Debug: log field validation attempt
        Log::info('Field validation attempt', [
            'user_id' => $user->id,
            'field' => $field,
            'user_role' => $user->roles->first()->name ?? 'no role'
        ]);

        $allowedFields = $this->getAllowedFields($user);
        
        // Check exact match
        if (in_array($field, $allowedFields)) {
            Log::info('Field validation: exact match found', ['field' => $field]);
            return true;
        }
        
        // Check BKD pattern
        if (str_starts_with($field, 'bkd_') && in_array('bkd_semester_1', $allowedFields)) {
            Log::info('Field validation: BKD pattern match', ['field' => $field]);
            return true;
        }
        
        // Fallback: jika user tidak memiliki role, izinkan semua field
        if (!$user->roles->first()) {
            Log::info('Field validation: fallback (no role) - allowing all fields', ['field' => $field]);
            return true;
        }
        
        Log::warning('Field validation: access denied', [
            'field' => $field,
            'allowed_fields' => $allowedFields,
            'user_role' => $user->roles->first()->name ?? 'no role'
        ]);
        
        return false;
    }

    /**
     * Log document access
     */
    public function logDocumentAccess($user, $usulan, $field, $success = true): void
    {
        Log::info('Document access logged', [
            'user_id' => $user->id,
            'user_role' => $user->roles->first()->name ?? null,
            'usulan_id' => $usulan->id,
            'field' => $field,
            'success' => $success,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PenilaiDocumentService
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Show usulan document for penilai
     */
    public function showUsulanDocument(Usulan $usulan, $field, $penilaiId)
    {
        // Validate penilai access
        if (!$usulan->isAssignedToPenilai($penilaiId)) {
            abort(403, 'Anda tidak memiliki akses untuk dokumen ini.');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        if (str_starts_with($field, 'bkd_')) {
            $allowedFields[] = $field;
        }

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Cari path file menggunakan method getDocumentPath
        $filePath = $usulan->getDocumentPath($field);

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk and check file existence
        // Dokumen usulan (termasuk BKD) disimpan di 'public' sesuai DocumentAccessService
        $disk = 'public';
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Log document access
        $this->logDocumentAccess($usulan->id, $penilaiId, $field, 'usulan_document');

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show pegawai document for penilai
     */
    public function showPegawaiDocument(Usulan $usulan, $field, $penilaiId)
    {
        // Validate penilai access
        if (!$usulan->isAssignedToPenilai($penilaiId)) {
            abort(403, 'Anda tidak memiliki akses untuk dokumen ini.');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen profil tidak valid.');
        }

        // Ambil path dokumen dari pegawai
        $filePath = $usulan->pegawai->{$field} ?? null;

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk and check file existence
        $disk = $this->getFileDisk($field);
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Log document access
        $this->logDocumentAccess($usulan->id, $penilaiId, $field, 'pegawai_document');

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show admin fakultas document for penilai
     */
    public function showAdminFakultasDocument(Usulan $usulan, $field, $penilaiId)
    {
        // Validate penilai access
        if (!$usulan->isAssignedToPenilai($penilaiId)) {
            abort(403, 'Anda tidak memiliki akses untuk dokumen ini.');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'file_surat_usulan', 'file_berita_senat'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen admin fakultas tidak valid.');
        }

        // Ambil path dari validasi data admin fakultas
        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
        $pathKey = $field . '_path';
        $filePath = $dokumenPendukung[$pathKey] ?? null;

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Check file existence in public disk
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Log document access
        $this->logDocumentAccess($usulan->id, $penilaiId, $field, 'admin_fakultas_document');

        // Serve file
        $fullPath = Storage::disk('public')->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Determine file disk based on field type
     */
    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'pak_integrasi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Log document access for audit trail
     */
    private function logDocumentAccess($usulanId, $penilaiId, $documentField, $documentType)
    {
        try {
            Log::info('Penilai document access', [
                'usulan_id' => $usulanId,
                'penilai_id' => $penilaiId,
                'document_field' => $documentField,
                'document_type' => $documentType,
                'access_time' => now()->toDateTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log document access', [
                'error' => $e->getMessage(),
                'usulan_id' => $usulanId,
                'penilai_id' => $penilaiId
            ]);
        }
    }

    /**
     * Get document access statistics for penilai
     */
    public function getDocumentAccessStats($penilaiId, $days = 30)
    {
        // This would typically query a document access log table
        // For now, return basic stats
        return [
            'total_accesses' => 0,
            'accesses_today' => 0,
            'most_accessed_documents' => [],
            'access_trend' => []
        ];
    }
}

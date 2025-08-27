<?php

namespace App\Services;

use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Support\Facades\Log;

class ValidationService
{
    /**
     * Standard validation rules untuk dokumen pendukung
     */
    public function getDokumenPendukungRules()
    {
        return [
            'dokumen_pendukung.file_surat_usulan' => 'nullable|file|mimes:pdf|max:2048',
            'dokumen_pendukung.file_berita_senat' => 'nullable|file|mimes:pdf|max:2048',
            'dokumen_pendukung.nomor_surat_usulan' => 'nullable|string|max:255',
            'dokumen_pendukung.nomor_berita_senat' => 'nullable|string|max:255',
        ];
    }

    /**
     * Validate usulan submission untuk role tertentu
     */
    public function validateUsulanSubmission(Usulan $usulan, $role)
    {
        $errors = [];

        // Check required documents berdasarkan role
        $requiredDocuments = $this->getRequiredDocumentsForRole($role);

        foreach ($requiredDocuments as $document) {
            if (!$usulan->hasDocument($document)) {
                $errors[] = "Dokumen {$document} harus diisi";
            }
        }

        // Check validation completeness
        if (!$usulan->isValidationComplete($role)) {
            $errors[] = "Validasi belum lengkap";
        }

        // Log validation attempt
        Log::info('Usulan validation attempt', [
            'usulan_id' => $usulan->id,
            'role' => $role,
            'errors_count' => count($errors),
            'errors' => $errors
        ]);

        return $errors;
    }

    /**
     * Check apakah bisa lanjut ke stage berikutnya
     */
    public function canProceedToNextStage(Usulan $usulan, $currentRole, $nextRole)
    {
        $validationErrors = $this->validateUsulanSubmission($usulan, $currentRole);

        if (!empty($validationErrors)) {
            return [
                'can_proceed' => false,
                'errors' => $validationErrors,
                'current_role' => $currentRole,
                'next_role' => $nextRole
            ];
        }

        return [
            'can_proceed' => true,
            'next_stage' => $nextRole,
            'current_role' => $currentRole
        ];
    }

    /**
     * Check apakah usulan bisa diubah ke status Sister
     */
    public function canSubmitToSister(Usulan $usulan): bool
    {
        return in_array($usulan->status_usulan, [
            'Usulan Direkomendasikan oleh Tim Senat',
            'Usulan Sudah Dikirim ke Sister'
        ]);
    }

    /**
     * Check apakah usulan bisa diperbaiki dari Sister
     */
    public function canSubmitPerbaikanFromSister(Usulan $usulan): bool
    {
        return $usulan->status_usulan === 'Permintaan Perbaikan Usulan dari Tim Sister';
    }

    /**
     * Check apakah usulan bisa dikirim ke Admin Univ Usulan untuk validasi
     */
    public function canSubmitToAdminUnivUsulan(Usulan $usulan): bool
    {
        return in_array($usulan->status_usulan, [
            'Permintaan Perbaikan Usulan dari Tim Sister',
            'Usulan Dikirim ke Admin Fakultas'
        ]);
    }

    /**
     * Get required documents berdasarkan role
     */
    private function getRequiredDocumentsForRole($role)
    {
        $documentRequirements = [
            'admin_fakultas' => [
                'file_surat_usulan',
                'nomor_surat_usulan'
            ],
            'admin_universitas' => [
                // Admin universitas tidak memerlukan dokumen pendukung
            ],
            'tim_penilai' => [
                // Tim penilai tidak memerlukan dokumen pendukung
            ],
            'tim_senat' => [
                // Tim senat tidak memerlukan dokumen pendukung
            ],
            'pegawai' => [
                // Pegawai tidak memerlukan dokumen pendukung untuk perbaikan sister
            ]
        ];

        return $documentRequirements[$role] ?? [];
    }

    /**
     * Validate file upload untuk dokumen pendukung dengan detail per field
     */
    public function validateDokumenPendukung($request, $usulan, $role)
    {
        $errors = [];
        $debugInfo = [
            'usulan_id' => $usulan->id,
            'role' => $role,
            'request_method' => $request->method(),
            'request_content_type' => $request->header('Content-Type'),
            'has_files' => $request->hasFile('dokumen_pendukung'),
            'all_request_keys' => array_keys($request->all()),
            'request_all_data' => $request->all(), // Tambahkan semua data request
            'files_data' => $request->allFiles(), // Tambahkan data files
            'dokumen_pendukung_data' => $request->input('dokumen_pendukung'), // Data dokumen pendukung
        ];

        // Debug file detection
        $debugInfo['file_detection'] = [
            'has_file_dokumen_pendukung' => $request->hasFile('dokumen_pendukung'),
            'has_file_dokumen_pendukung_file_surat_usulan' => $request->hasFile('dokumen_pendukung.file_surat_usulan'),
            'has_file_dokumen_pendukung_file_berita_senat' => $request->hasFile('dokumen_pendukung.file_berita_senat'),
            'has_file_dokumen_pendukung_file_surat_usulan_bracket' => $request->hasFile('dokumen_pendukung[file_surat_usulan]'),
            'has_file_dokumen_pendukung_file_berita_senat_bracket' => $request->hasFile('dokumen_pendukung[file_berita_senat]'),
        ];

        // Debug raw files array
        $debugInfo['raw_files'] = $_FILES ?? [];

        // === VALIDASI FIELD BY FIELD ===

        // 1. Validasi File Surat Usulan
        $fileSuratValidation = $this->validateFileSuratUsulan($request, $usulan);
        $debugInfo['file_surat_validation'] = $fileSuratValidation;
        if (!empty($fileSuratValidation['errors'])) {
            $errors = array_merge($errors, $fileSuratValidation['errors']);
        }

        // 2. Validasi File Berita Senat
        $fileBeritaValidation = $this->validateFileBeritaSenat($request, $usulan);
        $debugInfo['file_berita_validation'] = $fileBeritaValidation;
        if (!empty($fileBeritaValidation['errors'])) {
            $errors = array_merge($errors, $fileBeritaValidation['errors']);
        }

        // 3. Validasi Nomor Surat Usulan
        $nomorSuratValidation = $this->validateNomorSuratUsulan($request, $usulan);
        $debugInfo['nomor_surat_validation'] = $nomorSuratValidation;
        if (!empty($nomorSuratValidation['errors'])) {
            $errors = array_merge($errors, $nomorSuratValidation['errors']);
        }

        // 4. Validasi Nomor Berita Senat
        $nomorBeritaValidation = $this->validateNomorBeritaSenat($request, $usulan);
        $debugInfo['nomor_berita_validation'] = $nomorBeritaValidation;
        if (!empty($nomorBeritaValidation['errors'])) {
            $errors = array_merge($errors, $nomorBeritaValidation['errors']);
        }

        // Log detailed validation info
        Log::info('Dokumen pendukung validation detailed', [
            'debug_info' => $debugInfo ?: [],
            'total_errors' => count($errors),
            'errors' => $errors ?: []
        ]);

        return $errors;
    }

    /**
     * Validasi File Surat Usulan
     */
    private function validateFileSuratUsulan($request, $usulan)
    {
        $validation = [
            'field' => 'file_surat_usulan',
            'errors' => [],
            'debug' => []
        ];

        // Check existing file
        $existingPath = $usulan->getDocumentPath('file_surat_usulan');
        $validation['debug']['existing_path'] = $existingPath;
        $validation['debug']['has_existing'] = !empty($existingPath);

        // Check new file upload (both notations)
        $hasFileDot = $request->hasFile('dokumen_pendukung.file_surat_usulan');
        $hasFileBracket = $request->hasFile('dokumen_pendukung[file_surat_usulan]');

        $validation['debug']['has_file_dot'] = $hasFileDot;
        $validation['debug']['has_file_bracket'] = $hasFileBracket;
        $validation['debug']['has_any_file'] = $hasFileDot || $hasFileBracket;

        // Get file if exists
        $file = null;
        if ($hasFileDot) {
            $file = $request->file('dokumen_pendukung.file_surat_usulan');
            $validation['debug']['file_source'] = 'dot_notation';
        } elseif ($hasFileBracket) {
            $file = $request->file('dokumen_pendukung[file_surat_usulan]');
            $validation['debug']['file_source'] = 'bracket_notation';
        }

        if ($file) {
            $validation['debug']['file_name'] = $file->getClientOriginalName();
            $validation['debug']['file_size'] = $file->getSize();
            $validation['debug']['file_mime'] = $file->getMimeType();
            $validation['debug']['file_extension'] = $file->getClientOriginalExtension();
        }

        // Logic untuk Admin Fakultas
        if (request()->is('admin-fakultas/*')) {
            $hasFile = $hasFileDot || $hasFileBracket || !empty($existingPath);

            if (!$hasFile) {
                $validation['errors'][] = 'File Surat Usulan harus diisi (tidak ada file yang diupload atau file existing)';
                $validation['debug']['reason'] = 'no_file_found';
                $validation['debug']['details'] = [
                    'has_file_dot' => $hasFileDot,
                    'has_file_bracket' => $hasFileBracket,
                    'has_existing_file' => !empty($existingPath),
                    'existing_path' => $existingPath
                ];
            } elseif ($file) {
                // Validate file type
                if ($file->getClientOriginalExtension() !== 'pdf') {
                    $validation['errors'][] = 'File Surat Usulan harus berformat PDF (file yang diupload: ' . $file->getClientOriginalExtension() . ')';
                    $validation['debug']['reason'] = 'invalid_file_type';
                }

                // Validate file size (2MB = 2048 KB)
                if ($file->getSize() > 2048 * 1024) {
                    $validation['errors'][] = 'File Surat Usulan maksimal 2MB (file yang diupload: ' . round($file->getSize() / 1024 / 1024, 2) . 'MB)';
                    $validation['debug']['reason'] = 'file_too_large';
                }
            }
        }

        return $validation;
    }

    /**
     * Validasi File Berita Senat
     */
    private function validateFileBeritaSenat($request, $usulan)
    {
        $validation = [
            'field' => 'file_berita_senat',
            'errors' => [],
            'debug' => []
        ];

        // Check existing file
        $existingPath = $usulan->getDocumentPath('file_berita_senat');
        $validation['debug']['existing_path'] = $existingPath;
        $validation['debug']['has_existing'] = !empty($existingPath);

        // Check new file upload (both notations)
        $hasFileDot = $request->hasFile('dokumen_pendukung.file_berita_senat');
        $hasFileBracket = $request->hasFile('dokumen_pendukung[file_berita_senat]');

        $validation['debug']['has_file_dot'] = $hasFileDot;
        $validation['debug']['has_file_bracket'] = $hasFileBracket;
        $validation['debug']['has_any_file'] = $hasFileDot || $hasFileBracket;

        // Get file if exists
        $file = null;
        if ($hasFileDot) {
            $file = $request->file('dokumen_pendukung.file_berita_senat');
            $validation['debug']['file_source'] = 'dot_notation';
        } elseif ($hasFileBracket) {
            $file = $request->file('dokumen_pendukung[file_berita_senat]');
            $validation['debug']['file_source'] = 'bracket_notation';
        }

        if ($file) {
            $validation['debug']['file_name'] = $file->getClientOriginalName();
            $validation['debug']['file_size'] = $file->getSize();
            $validation['debug']['file_mime'] = $file->getMimeType();
            $validation['debug']['file_extension'] = $file->getClientOriginalExtension();
        }

        // Logic untuk Admin Fakultas
        if (request()->is('admin-fakultas/*')) {
            $hasFile = $hasFileDot || $hasFileBracket || !empty($existingPath);

            if (!$hasFile) {
                $validation['errors'][] = 'File Berita Senat harus diisi (tidak ada file yang diupload atau file existing)';
                $validation['debug']['reason'] = 'no_file_found';
                $validation['debug']['details'] = [
                    'has_file_dot' => $hasFileDot,
                    'has_file_bracket' => $hasFileBracket,
                    'has_existing_file' => !empty($existingPath),
                    'existing_path' => $existingPath
                ];
            } elseif ($file) {
                // Validate file type
                if ($file->getClientOriginalExtension() !== 'pdf') {
                    $validation['errors'][] = 'File Berita Senat harus berformat PDF (file yang diupload: ' . $file->getClientOriginalExtension() . ')';
                    $validation['debug']['reason'] = 'invalid_file_type';
                }

                // Validate file size (2MB = 2048 KB)
                if ($file->getSize() > 2048 * 1024) {
                    $validation['errors'][] = 'File Berita Senat maksimal 2MB (file yang diupload: ' . round($file->getSize() / 1024 / 1024, 2) . 'MB)';
                    $validation['debug']['reason'] = 'file_too_large';
                }
            }
        }

        return $validation;
    }

    /**
     * Validasi Nomor Surat Usulan
     */
    private function validateNomorSuratUsulan($request, $usulan)
    {
        $validation = [
            'field' => 'nomor_surat_usulan',
            'errors' => [],
            'debug' => []
        ];

        // Check both notations
        $nomorDot = $request->input('dokumen_pendukung.nomor_surat_usulan');
        $nomorBracket = $request->input('dokumen_pendukung[nomor_surat_usulan]');

        $validation['debug']['nomor_dot'] = $nomorDot;
        $validation['debug']['nomor_bracket'] = $nomorBracket;
        $validation['debug']['nomor_final'] = $nomorDot ?? $nomorBracket;

        // Check existing data
        $existingData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
        $existingNomor = $existingData['nomor_surat_usulan'] ?? null;
        $validation['debug']['existing_nomor'] = $existingNomor;

        // Logic untuk Admin Fakultas
        if (request()->is('admin-fakultas/*')) {
            $hasNomor = !empty($nomorDot) || !empty($nomorBracket) || !empty($existingNomor);

            if (!$hasNomor) {
                $validation['errors'][] = 'Nomor Surat Usulan harus diisi';
                $validation['debug']['reason'] = 'no_nomor_found';
            } else {
                $finalNomor = $nomorDot ?? $nomorBracket ?? $existingNomor;

                // Validate length
                if (strlen($finalNomor) > 255) {
                    $validation['errors'][] = 'Nomor Surat Usulan maksimal 255 karakter';
                    $validation['debug']['reason'] = 'nomor_too_long';
                }

                // Validate format (basic)
                if (!preg_match('/^[A-Z0-9\/\-\.\s]+$/i', $finalNomor)) {
                    $validation['errors'][] = 'Nomor Surat Usulan hanya boleh berisi huruf, angka, dan karakter / - .';
                    $validation['debug']['reason'] = 'invalid_nomor_format';
                }
            }
        }

        return $validation;
    }

    /**
     * Validasi Nomor Berita Senat
     */
    private function validateNomorBeritaSenat($request, $usulan)
    {
        $validation = [
            'field' => 'nomor_berita_senat',
            'errors' => [],
            'debug' => []
        ];

        // Check both notations
        $nomorDot = $request->input('dokumen_pendukung.nomor_berita_senat');
        $nomorBracket = $request->input('dokumen_pendukung[nomor_berita_senat]');

        $validation['debug']['nomor_dot'] = $nomorDot;
        $validation['debug']['nomor_bracket'] = $nomorBracket;
        $validation['debug']['nomor_final'] = $nomorDot ?? $nomorBracket;

        // Check existing data
        $existingData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
        $existingNomor = $existingData['nomor_berita_senat'] ?? null;
        $validation['debug']['existing_nomor'] = $existingNomor;

        // Logic untuk Admin Fakultas
        if (request()->is('admin-fakultas/*')) {
            $hasNomor = !empty($nomorDot) || !empty($nomorBracket) || !empty($existingNomor);

            if (!$hasNomor) {
                $validation['errors'][] = 'Nomor Berita Senat harus diisi';
                $validation['debug']['reason'] = 'no_nomor_found';
            } else {
                $finalNomor = $nomorDot ?? $nomorBracket ?? $existingNomor;

                // Validate length
                if (strlen($finalNomor) > 255) {
                    $validation['errors'][] = 'Nomor Berita Senat maksimal 255 karakter';
                    $validation['debug']['reason'] = 'nomor_too_long';
                }

                // Validate format (basic)
                if (!preg_match('/^[A-Z0-9\/\-\.\s]+$/i', $finalNomor)) {
                    $validation['errors'][] = 'Nomor Berita Senat hanya boleh berisi huruf, angka, dan karakter / - .';
                    $validation['debug']['reason'] = 'invalid_nomor_format';
                }
            }
        }

        return $validation;
    }

    /**
     * Get validation status untuk usulan
     */
    public function getValidationStatus(Usulan $usulan, $role)
    {
        $validationData = $usulan->getValidasiByRole($role) ?? [];
        $totalFields = 0;
        $validatedFields = 0;
        $invalidFields = 0;

        foreach ($validationData as $groupKey => $groupData) {
            if (is_array($groupData)) {
                foreach ($groupData as $fieldKey => $fieldData) {
                    if (is_array($fieldData) && isset($fieldData['status'])) {
                        $totalFields++;

                        if ($fieldData['status'] === 'sesuai') {
                            $validatedFields++;
                        } elseif ($fieldData['status'] === 'tidak_sesuai') {
                            $invalidFields++;
                        }
                    }
                }
            }
        }

        return [
            'total_fields' => $totalFields,
            'validated_fields' => $validatedFields,
            'invalid_fields' => $invalidFields,
            'completion_percentage' => $totalFields > 0 ? ($validatedFields / $totalFields) * 100 : 0,
            'is_complete' => $totalFields > 0 && $invalidFields === 0 && $validatedFields === $totalFields
        ];
    }
}

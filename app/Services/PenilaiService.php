<?php

namespace App\Services;

use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Penilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PenilaiService
{
    protected $validationService;
    protected $fileStorageService;

    public function __construct(ValidationService $validationService, FileStorageService $fileStorageService)
    {
        $this->validationService = $validationService;
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Get usulans assigned to specific penilai
     */
    public function getAssignedUsulans($penilaiId, $filters = [])
    {
        $cacheKey = "penilai_assigned_usulans_{$penilaiId}_" . md5(serialize($filters));

        return Cache::remember($cacheKey, 300, function () use ($penilaiId, $filters) {
            $query = Usulan::query()
                ->whereHas('penilais', function ($penilaiQuery) use ($penilaiId) {
                    $penilaiQuery->where('penilai_id', $penilaiId);
                })
                ->with([
                    'pegawai:id,nama_lengkap,email,nip,jenis_pegawai,unit_kerja_id',
                    'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                    'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                    'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                    'jabatanLama:id,jabatan',
                    'jabatanTujuan:id,jabatan',
                    'periodeUsulan:id,nama_periode',
                    'penilais' // Add penilais relationship for status update
                ]);

            // Apply filters
            if (isset($filters['periode_id'])) {
                $query->where('periode_usulan_id', $filters['periode_id']);
            }

            if (isset($filters['search'])) {
                $query->where(function ($subQuery) use ($filters) {
                    $subQuery->where('jenis_usulan', 'like', "%{$filters['search']}%")
                        ->orWhereHas('pegawai', function ($pegawaiQuery) use ($filters) {
                            $pegawaiQuery->where('nama_lengkap', 'like', "%{$filters['search']}%");
                        });
                });
            }

            $usulans = $query->latest()->paginate(15);

            // Auto-update status for each usulan based on penilai progress
            foreach ($usulans as $usulan) {
                $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();
                if ($statusWasUpdated) {
                    // Log status update for debugging
                    Log::info('Auto-updated usulan status in penilai list', [
                        'usulan_id' => $usulan->id,
                        'new_status' => $usulan->status_usulan,
                        'penilai_id' => $penilaiId
                    ]);
                    
                    // Clear related caches
                    $this->clearPenilaiCache($penilaiId);
                }
            }

            return $usulans;
        });
    }

    /**
     * Get penilai statistics
     */
    public function getPenilaiStatistics($penilaiId)
    {
        $cacheKey = "penilai_statistics_{$penilaiId}";

        return Cache::remember($cacheKey, 300, function () use ($penilaiId) {
            $usulans = Usulan::whereHas('penilais', function ($penilaiQuery) use ($penilaiId) {
                $penilaiQuery->where('penilai_id', $penilaiId);
            });

            return [
                'total_assigned' => $usulans->count(),
                'pending_review' => $usulans->whereIn('status_usulan', [
                    Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                    Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
                    Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS
                ])->count(),
                'completed_review' => $usulans->whereIn('status_usulan', [
                    Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                    Usulan::STATUS_DIREKOMENDASIKAN
                ])->count(),
                'by_status' => [
                    'Usulan Dikirim ke Admin Fakultas' => $usulans->where('status_usulan', Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
                    'Usulan Disetujui Admin Fakultas' => $usulans->where('status_usulan', Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS)->count(),
                    'Usulan Disetujui Kepegawaian Universitas' => $usulans->where('status_usulan', Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS)->count(),
                    'Menunggu Hasil Penilaian Tim Penilai' => $usulans->where('status_usulan', Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI)->count(),
                    'Usulan Perbaikan dari Penilai Universitas' => $usulans->where('status_usulan', Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS)->count(),
                    'Usulan Direkomendasi Penilai Universitas' => $usulans->where('status_usulan', Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                    'Direkomendasikan' => $usulans->where('status_usulan', Usulan::STATUS_DIREKOMENDASIKAN)->count(),
                    'Tidak Direkomendasikan' => $usulans->where('status_usulan', Usulan::STATUS_TIDAK_DIREKOMENDASIKAN)->count(),
                ]
            ];
        });
    }

    /**
     * Validate penilai access to usulan
     */
    public function validatePenilaiAccess($usulanId, $penilaiId)
    {
        $usulan = Usulan::find($usulanId);

        if (!$usulan) {
            return [
                'has_access' => false,
                'message' => 'Usulan tidak ditemukan.'
            ];
        }

        $isAssigned = $usulan->isAssignedToPenilai($penilaiId);

        return [
            'has_access' => $isAssigned,
            'message' => $isAssigned ? 'Akses valid.' : 'Anda tidak memiliki akses untuk usulan ini.',
            'usulan' => $usulan
        ];
    }

    /**
     * Process penilai validation
     */
    public function processPenilaiValidation(Request $request, Usulan $usulan, $penilaiId)
    {
        try {
            Log::info('Penilai validation started', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'action_type' => $request->input('action_type'),
                'request_data' => $request->all()
            ]);

            // Validate access
            $accessValidation = $this->validatePenilaiAccess($usulan->id, $penilaiId);
            if (!$accessValidation['has_access']) {
                return [
                    'success' => false,
                    'message' => $accessValidation['message']
                ];
            }

            $actionType = $request->input('action_type');

            switch ($actionType) {
                case 'autosave':
                    return $this->handleAutoSave($request, $usulan, $penilaiId);

                case 'rekomendasikan':
                    return $this->handleRekomendasi($request, $usulan, $penilaiId);

                case 'perbaikan_usulan':
                    return $this->handlePerbaikanUsulan($request, $usulan, $penilaiId);

                default:
                    return [
                        'success' => false,
                        'message' => 'Aksi tidak valid.'
                    ];
            }

        } catch (\Exception $e) {
            Log::error('Penilai validation error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses validasi.'
            ];
        }
    }

    /**
     * Handle auto-save validation
     */
    private function handleAutoSave(Request $request, Usulan $usulan, $penilaiId)
    {
        $validationData = $request->input('validation');

        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        // Save validation data
        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Validasi berhasil disimpan otomatis.'
        ];
    }

    /**
     * Handle rekomendasi - KIRIM KE ADMIN UNIV USULAN DULU
     */
    private function handleRekomendasi(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Update usulan status - KIRIM KE ADMIN UNIV USULAN DULU
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;

        // Save validation data
        $validationData = $request->input('validation');
        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);

        // Add recommendation data with new flow
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['tim_penilai']['recommendation'] = 'direkomendasikan';
        $currentValidasi['tim_penilai']['catatan_rekomendasi'] = $request->input('catatan_umum');
        $currentValidasi['tim_penilai']['tanggal_rekomendasi'] = now()->toDateTimeString();
        $currentValidasi['tim_penilai']['penilai_id'] = $penilaiId;
        $currentValidasi['tim_penilai']['status'] = 'menunggu_admin_univ_review';

        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.'
        ];
    }

    /**
     * Handle perbaikan usulan - KIRIM KE ADMIN UNIV USULAN DULU
     */
    private function handlePerbaikanUsulan(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status - KIRIM KE ADMIN UNIV USULAN DULU
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;

        // Save validation data
        $validationData = $request->input('validation');
        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);

        // Add return data with new flow
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['tim_penilai']['perbaikan_usulan'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'penilai_id' => $penilaiId,
            'status' => 'menunggu_admin_univ_review'
        ];

        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.'
        ];
    }

    /**
     * Clear penilai cache
     */
    public function clearPenilaiCache($penilaiId)
    {
        Cache::forget("penilai_assigned_usulans_{$penilaiId}_*");
        Cache::forget("penilai_statistics_{$penilaiId}");
        Cache::forget("penilai_dashboard_{$penilaiId}");
    }

    /**
     * Get individual penilai status for usulan
     */
    public function getPenilaiIndividualStatus(Usulan $usulan, $penilaiId)
    {
        $penilai = $usulan->penilais()->where('penilai_id', $penilaiId)->first();
        
        if (!$penilai) {
            return [
                'status' => 'Tidak Ditugaskan',
                'catatan' => '',
                'updated_at' => null,
                'is_completed' => false
            ];
        }

        return [
            'status' => $penilai->pivot->status_penilaian ?? 'Belum Dinilai',
            'catatan' => $penilai->pivot->catatan_penilaian ?? '',
            'updated_at' => $penilai->pivot->updated_at ?? null,
            'is_completed' => !in_array($penilai->pivot->status_penilaian ?? 'Belum Dinilai', ['Belum Dinilai'])
        ];
    }

    /**
     * Get penilai dashboard data
     */
    public function getDashboardData($penilaiId)
    {
        $cacheKey = "penilai_dashboard_{$penilaiId}";

        return Cache::remember($cacheKey, 300, function () use ($penilaiId) {
            // Get active periods that have usulans assigned to this penilai
            // PERBAIKAN: Ambil periode yang memiliki usulan dengan status yang relevan untuk penilai
            $activePeriods = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('status', 'Buka')
                ->whereHas('usulans', function($query) use ($penilaiId) {
                    $query->whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                        $penilaiQuery->where('penilai_id', $penilaiId);
                    })
                    ->whereIn('status_usulan', [
                        Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                        Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
                        Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                        Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
                    ]);
                })
                ->with(['usulans' => function($query) use ($penilaiId) {
                    $query->whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                        $penilaiQuery->where('penilai_id', $penilaiId);
                    })
                    ->whereIn('status_usulan', [
                        Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                        Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
                        Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                        Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
                    ])
                    ->with(['pegawai:id,nama_lengkap,nip', 'penilais:id,nama_lengkap'])
                    ->latest()
                    ->limit(5);
                }])
                ->get();

            // Get recent usulans assigned to this penilai dengan status yang relevan
            $recentUsulans = Usulan::whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                    $penilaiQuery->where('penilai_id', $penilaiId);
                })
                ->whereIn('status_usulan', [
                    Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                    Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
                    Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                    Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
                ])
                ->with(['pegawai:id,nama_lengkap,nip', 'periodeUsulan'])
                ->latest()
                ->limit(10)
                ->get();

            return [
                'activePeriods' => $activePeriods,
                'recentUsulans' => $recentUsulans,
                'stats' => $this->getPenilaiStatistics($penilaiId)
            ];
        });
    }

    /**
     * ENHANCED: Process field-by-field validation for Penilai Universitas
     */
    public function processFieldByFieldValidation(Request $request, Usulan $usulan, $penilaiId)
    {
        try {
            Log::info('Penilai field-by-field validation started', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'action_type' => $request->input('action_type'),
                'request_data' => $request->all()
            ]);

            // Validate access
            $accessValidation = $this->validatePenilaiAccess($usulan->id, $penilaiId);
            if (!$accessValidation['has_access']) {
                return [
                    'success' => false,
                    'message' => $accessValidation['message']
                ];
            }

            $actionType = $request->input('action_type');

            switch ($actionType) {
                case 'autosave':
                    return $this->handleFieldByFieldAutoSave($request, $usulan, $penilaiId);

                case 'save_only':
                    return $this->handleFieldByFieldSave($request, $usulan, $penilaiId);

                case 'rekomendasikan':
                    return $this->handleFieldByFieldRekomendasi($request, $usulan, $penilaiId);

                case 'perbaikan_usulan':
                    return $this->handleFieldByFieldPerbaikan($request, $usulan, $penilaiId);

                default:
                    return [
                        'success' => false,
                        'message' => 'Aksi tidak valid.'
                    ];
            }

        } catch (\Exception $e) {
            Log::error('Penilai field-by-field validation error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses validasi field-by-field.'
            ];
        }
    }

    /**
     * Handle field-by-field auto-save validation
     */
    private function handleFieldByFieldAutoSave(Request $request, Usulan $usulan, $penilaiId)
    {
        $validationData = $request->input('validation');

        // Validate field data structure
        $validationResult = $this->validateFieldData($validationData);
        if (!$validationResult['is_valid']) {
            return [
                'success' => false,
                'message' => 'Data validasi tidak valid: ' . $validationResult['message']
            ];
        }

        // Save validation data
        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Data validasi tersimpan otomatis.'
        ];
    }

    /**
     * Handle field-by-field save validation
     */
    private function handleFieldByFieldSave(Request $request, Usulan $usulan, $penilaiId)
    {
        $validationData = $request->input('validation');

        // Validate field data structure
        $validationResult = $this->validateFieldData($validationData);
        if (!$validationResult['is_valid']) {
            return [
                'success' => false,
                'message' => 'Data validasi tidak valid: ' . $validationResult['message']
            ];
        }

        // Save validation data
        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ];
    }

    /**
     * Handle field-by-field rekomendasi
     */
    private function handleFieldByFieldRekomendasi(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        $validationData = $request->input('validation');

        // Validate field data structure
        $validationResult = $this->validateFieldData($validationData);
        if (!$validationResult['is_valid']) {
            return [
                'success' => false,
                'message' => 'Data validasi tidak valid: ' . $validationResult['message']
            ];
        }

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;

        // Save validation data
        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);

        // Add recommendation data with enhanced structure
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['tim_penilai']['recommendation'] = 'direkomendasikan';
        $currentValidasi['tim_penilai']['catatan_rekomendasi'] = $request->input('catatan_umum');
        $currentValidasi['tim_penilai']['tanggal_rekomendasi'] = now()->toDateTimeString();
        $currentValidasi['tim_penilai']['penilai_id'] = $penilaiId;
        $currentValidasi['tim_penilai']['status'] = 'menunggu_admin_univ_review';

        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.',
            'redirect' => route('penilai-universitas.pusat-usulan.index')
        ];
    }

    /**
     * Handle field-by-field perbaikan usulan
     */
    private function handleFieldByFieldPerbaikan(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        $validationData = $request->input('validation');

        // Validate field data structure
        $validationResult = $this->validateFieldData($validationData);
        if (!$validationResult['is_valid']) {
            return [
                'success' => false,
                'message' => 'Data validasi tidak valid: ' . $validationResult['message']
            ];
        }

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;

        // Save validation data
        $usulan->setValidasiByRole('tim_penilai', $validationData, $penilaiId);

        // Add return data with enhanced structure
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['tim_penilai']['perbaikan_usulan'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'penilai_id' => $penilaiId,
            'status' => 'menunggu_admin_univ_review'
        ];

        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear cache
        $this->clearPenilaiCache($penilaiId);

        return [
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.',
            'redirect' => route('penilai-universitas.pusat-usulan.index')
        ];
    }

    /**
     * Validate field data structure for Penilai Universitas
     */
    public function validateFieldData($validationData)
    {
        try {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }

            if (!is_array($validationData)) {
                return [
                    'is_valid' => false,
                    'message' => 'Data validasi harus berupa array'
                ];
            }

            // Define expected field groups for Penilai Universitas
            $expectedGroups = [
                'data_pribadi',
                'data_kepegawaian', 
                'data_pendidikan',
                'data_kinerja',
                'dokumen_profil',
                'bkd',
                'karya_ilmiah',
                'dokumen_usulan',
                'syarat_guru_besar',
                'dokumen_admin_fakultas'
            ];

            // Check if all expected groups are present
            foreach ($expectedGroups as $group) {
                if (!isset($validationData[$group])) {
                    return [
                        'is_valid' => false,
                        'message' => "Group '{$group}' tidak ditemukan dalam data validasi"
                    ];
                }

                if (!is_array($validationData[$group])) {
                    return [
                        'is_valid' => false,
                        'message' => "Group '{$group}' harus berupa array"
                    ];
                }

                // Validate each field in the group
                foreach ($validationData[$group] as $field => $fieldData) {
                    if (!is_array($fieldData)) {
                        return [
                            'is_valid' => false,
                            'message' => "Field '{$field}' dalam group '{$group}' harus berupa array"
                        ];
                    }

                    // Check required field properties
                    if (!isset($fieldData['status'])) {
                        return [
                            'is_valid' => false,
                            'message' => "Field '{$field}' dalam group '{$group}' tidak memiliki status"
                        ];
                    }

                    // Validate status values
                    $validStatuses = ['sesuai', 'tidak_sesuai'];
                    if (!in_array($fieldData['status'], $validStatuses)) {
                        return [
                            'is_valid' => false,
                            'message' => "Status field '{$field}' dalam group '{$group}' tidak valid"
                        ];
                    }

                    // If status is 'tidak_sesuai', keterangan is required
                    if ($fieldData['status'] === 'tidak_sesuai' && empty($fieldData['keterangan'])) {
                        return [
                            'is_valid' => false,
                            'message' => "Keterangan wajib diisi untuk field '{$field}' dalam group '{$group}' yang tidak sesuai"
                        ];
                    }
                }
            }

            return [
                'is_valid' => true,
                'message' => 'Data validasi valid'
            ];

        } catch (\Exception $e) {
            Log::error('Field data validation error', [
                'error' => $e->getMessage(),
                'validation_data' => $validationData
            ]);

            return [
                'is_valid' => false,
                'message' => 'Error saat validasi data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get validation summary for Penilai Universitas
     */
    public function getValidationSummary(Usulan $usulan, $penilaiId)
    {
        try {
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            $validationData = $timPenilaiData['validation'] ?? [];

            $summary = [
                'total_fields' => 0,
                'sesuai_count' => 0,
                'tidak_sesuai_count' => 0,
                'completion_percentage' => 0,
                'groups' => []
            ];

            foreach ($validationData as $groupKey => $groupData) {
                $groupSummary = [
                    'group_name' => $this->getGroupDisplayName($groupKey),
                    'total_fields' => count($groupData),
                    'sesuai_count' => 0,
                    'tidak_sesuai_count' => 0,
                    'completion_percentage' => 0
                ];

                foreach ($groupData as $fieldKey => $fieldData) {
                    $summary['total_fields']++;
                    $groupSummary['total_fields']++;

                    if ($fieldData['status'] === 'sesuai') {
                        $summary['sesuai_count']++;
                        $groupSummary['sesuai_count']++;
                    } elseif ($fieldData['status'] === 'tidak_sesuai') {
                        $summary['tidak_sesuai_count']++;
                        $groupSummary['tidak_sesuai_count']++;
                    }
                }

                // Calculate group completion percentage
                if ($groupSummary['total_fields'] > 0) {
                    $groupSummary['completion_percentage'] = 
                        (($groupSummary['sesuai_count'] + $groupSummary['tidak_sesuai_count']) / $groupSummary['total_fields']) * 100;
                }

                $summary['groups'][$groupKey] = $groupSummary;
            }

            // Calculate overall completion percentage
            if ($summary['total_fields'] > 0) {
                $summary['completion_percentage'] = 
                    (($summary['sesuai_count'] + $summary['tidak_sesuai_count']) / $summary['total_fields']) * 100;
            }

            return $summary;

        } catch (\Exception $e) {
            Log::error('Get validation summary error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'error' => $e->getMessage()
            ]);

            return [
                'total_fields' => 0,
                'sesuai_count' => 0,
                'tidak_sesuai_count' => 0,
                'completion_percentage' => 0,
                'groups' => []
            ];
        }
    }

    /**
     * Get group display name
     */
    private function getGroupDisplayName($groupKey)
    {
        $groupNames = [
            'data_pribadi' => 'Data Pribadi',
            'data_kepegawaian' => 'Data Kepegawaian',
            'data_pendidikan' => 'Data Pendidikan',
            'data_kinerja' => 'Data Kinerja',
            'dokumen_profil' => 'Dokumen Profil',
            'bkd' => 'Beban Kinerja Dosen (BKD)',
            'karya_ilmiah' => 'Karya Ilmiah',
            'dokumen_usulan' => 'Dokumen Usulan',
            'syarat_guru_besar' => 'Syarat Guru Besar',
            'dokumen_admin_fakultas' => 'Dokumen Admin Fakultas'
        ];

        return $groupNames[$groupKey] ?? ucwords(str_replace('_', ' ', $groupKey));
    }

    /**
     * Check if penilai has completed validation
     */
    public function hasCompletedValidation(Usulan $usulan, $penilaiId)
    {
        try {
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            
            // Check if penilai has submitted recommendation or perbaikan
            $hasRecommendation = !empty($timPenilaiData['recommendation']);
            $hasPerbaikan = !empty($timPenilaiData['perbaikan_usulan']);
            
            return $hasRecommendation || $hasPerbaikan;

        } catch (\Exception $e) {
            Log::error('Check completion validation error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}

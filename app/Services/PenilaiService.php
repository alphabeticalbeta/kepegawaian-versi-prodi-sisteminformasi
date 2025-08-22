<?php

namespace App\Services;

use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Penilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
                ->where('status_usulan', 'Sedang Direview')
                ->whereHas('penilais', function ($penilaiQuery) use ($penilaiId) {
                    $penilaiQuery->where('penilai_id', $penilaiId);
                })
                ->with([
                    'pegawai:id,nama_lengkap,email,nip',
                    'jabatanLama:id,jabatan',
                    'jabatanTujuan:id,jabatan',
                    'periodeUsulan:id,nama_periode'
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

            return $query->latest()->paginate(15);
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
                'pending_review' => $usulans->where('status_usulan', 'Sedang Direview')->count(),
                'completed_review' => $usulans->where('status_usulan', 'Direkomendasikan')->count(),
                'by_status' => [
                    'Diajukan' => $usulans->where('status_usulan', 'Diajukan')->count(),
                    'Diusulkan ke Universitas' => $usulans->where('status_usulan', 'Diusulkan ke Universitas')->count(),
                    'Sedang Direview' => $usulans->where('status_usulan', 'Sedang Direview')->count(),
                    'Direkomendasikan' => $usulans->where('status_usulan', 'Direkomendasikan')->count(),
                    'Disetujui' => $usulans->where('status_usulan', 'Disetujui')->count(),
                    'Ditolak' => $usulans->where('status_usulan', 'Ditolak')->count(),
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
        $usulan->status_usulan = 'Menunggu Review Admin Univ';

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
        $usulan->status_usulan = 'Menunggu Review Admin Univ';

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
    }

    /**
     * Get penilai dashboard data
     */
    public function getDashboardData($penilaiId)
    {
        $cacheKey = "penilai_dashboard_{$penilaiId}";

        return Cache::remember($cacheKey, 300, function () use ($penilaiId) {
            // Get active periods that have usulans assigned to this penilai
            $activePeriods = \App\Models\BackendUnivUsulan\PeriodeUsulan::where('status', 'Buka')
                ->whereHas('usulans', function($query) use ($penilaiId) {
                    $query->where('status_usulan', 'Sedang Direview')
                          ->whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                              $penilaiQuery->where('penilai_id', $penilaiId);
                          });
                })
                ->with(['usulans' => function($query) use ($penilaiId) {
                    $query->where('status_usulan', 'Sedang Direview')
                          ->whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                              $penilaiQuery->where('penilai_id', $penilaiId);
                          })
                          ->with('pegawai:id,nama_lengkap,nip')
                          ->latest()
                          ->limit(5);
                }])
                ->get();

            // Get recent usulans
            $recentUsulans = Usulan::where('status_usulan', 'Sedang Direview')
                ->whereHas('penilais', function($penilaiQuery) use ($penilaiId) {
                    $penilaiQuery->where('penilai_id', $penilaiId);
                })
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
}

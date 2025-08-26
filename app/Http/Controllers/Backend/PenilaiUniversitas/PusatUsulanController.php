<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Services\PenilaiService;
use App\Services\PenilaiDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PusatUsulanController extends Controller
{
    protected $penilaiService;
    protected $documentService;

    public function __construct(PenilaiService $penilaiService, PenilaiDocumentService $documentService)
    {
        $this->penilaiService = $penilaiService;
        $this->documentService = $documentService;
    }

    public function index(Request $request)
    {
        $currentPenilai = Auth::user();

        $filters = [];
        if ($request->get('q')) {
            $filters['search'] = $request->get('q');
        }
        if ($request->get('periode_id')) {
            $filters['periode_id'] = $request->get('periode_id');
        }

        $usulans = $this->penilaiService->getAssignedUsulans($currentPenilai->id, $filters);

        return view('backend.layouts.views.penilai-universitas.pusat-usulan.index', compact('usulans'));
    }

            public function show(Usulan $usulan)
    {
        try {
            $currentPenilai = Auth::user();

            if (!$currentPenilai) {
                abort(401, 'Anda harus login terlebih dahulu.');
            }

            // Check if usulan is assigned to current penilai
            if (!$usulan->isAssignedToPenilai($currentPenilai->id)) {
                abort(403, 'Anda tidak memiliki akses untuk usulan ini. Usulan ini tidak ditugaskan kepada Anda.');
            }

            // ENHANCED: Consistency Check for Penilai Universitas
            $consistencyCheck = $this->performPenilaiConsistencyCheck($usulan);

            // OPTIMASI: Eager load semua relasi yang dibutuhkan sekaligus
            $usulan->load([
                'pegawai.pangkat',
                'pegawai.jabatan',
                'pegawai.unitKerja.subUnitKerja.unitKerja',
                'jabatanLama',
                'jabatanTujuan',
                'periodeUsulan',
                'dokumens',
                'logs.dilakukanOleh' => function ($query) {
                    $query->latest();
                },
            ]);

            // UPDATED: Pass usulan object and role to get dynamic BKD fields
            $validationFields = \App\Models\KepegawaianUniversitas\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'penilai');

            // ADDED: Get BKD labels for display
            $bkdLabels = $usulan->getBkdDisplayLabels();

            // Get existing validation data if any
            $existingValidation = $usulan->getValidasiByRole('penilai_universitas');

            // ENHANCED: Get validation summary for progress display
            $validationSummary = $this->penilaiService->getValidationSummary($usulan, $currentPenilai->id);

            // Get individual penilai status
            $penilaiIndividualStatus = $this->penilaiService->getPenilaiIndividualStatus($usulan, $currentPenilai->id);

            // ENHANCED: Determine if can edit based on status AND individual penilai completion
            $canEdit = in_array($usulan->status_usulan, [
                'Diusulkan ke Universitas',
                'Sedang Direview',
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_TIM_PENILAI,
                'Menunggu Hasil Penilaian Tim Penilai'
            ]) && !$penilaiIndividualStatus['is_completed'];

            // Determine action permissions based on status
            $canReturn = in_array($usulan->status_usulan, ['Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai']);
            $canForward = in_array($usulan->status_usulan, ['Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai']);

            return view('backend.layouts.views.penilai-universitas.pusat-usulan.detail', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
                'bkdLabels' => $bkdLabels,
                'canEdit' => $canEdit,
                'consistencyCheck' => $consistencyCheck,
                'penilaiIndividualStatus' => $penilaiIndividualStatus,
                'validationSummary' => $validationSummary,
                'config' => [
                    'canReturn' => $canReturn,
                    'canForward' => $canForward,
                    'routePrefix' => 'penilai-universitas',
                    'canEdit' => $canEdit,
                    'canView' => true,
                    'submitFunctions' => ['save', 'rekomendasikan', 'perbaikan_usulan']
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in PenilaiUniversitas show method: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id ?? 'unknown',
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('penilai-universitas.pusat-usulan.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail usulan: ' . $e->getMessage());
        }
    }

    /**
     * Show pendaftar for specific periode.
     */
    public function showPendaftar(\App\Models\KepegawaianUniversitas\PeriodeUsulan $periode)
    {
        $currentPenilai = Auth::user();

        $filters = ['periode_id' => $periode->id];
        $usulans = $this->penilaiService->getAssignedUsulans($currentPenilai->id, $filters);

        return view('backend.layouts.views.penilai-universitas.pusat-usulan.show-pendaftar', compact('periode', 'usulans'));
    }

    /**
     * Process usulan validation.
     */
    public function process(Request $request, Usulan $usulan)
    {
        try {
            $currentPenilai = Auth::user();

            if (!$currentPenilai) {
                abort(401, 'Anda harus login terlebih dahulu.');
            }

            // Check if usulan is assigned to current penilai
            if (!$usulan->isAssignedToPenilai($currentPenilai->id)) {
                abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
            }

            // ENHANCED: Check if usulan is in correct status for Penilai Universitas
            $allowedStatuses = [
                'Sedang Direview',
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_TIM_PENILAI,
                'Menunggu Hasil Penilaian Tim Penilai'
            ];

            if (!in_array($usulan->status_usulan, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usulan tidak dapat divalidasi karena status tidak sesuai. Status saat ini: ' . $usulan->status_usulan
                ], 422);
            }

            $actionType = $request->input('action_type');

            // ENHANCED: Handle different action types like Admin Univ Usulan
            switch ($actionType) {
                case 'autosave':
                    return $this->autosaveValidation($request, $usulan, $currentPenilai->id);
                
                case 'save_only':
                    return $this->saveSimpleValidation($request, $usulan, $currentPenilai->id);
                
                case 'rekomendasikan':
                    return $this->handleRekomendasi($request, $usulan, $currentPenilai->id);
                
                case 'perbaikan_usulan':
                    return $this->handlePerbaikanUsulan($request, $usulan, $currentPenilai->id);
                
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Aksi tidak valid.'
                    ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Penilai Universitas validation error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => Auth::id(),
                'action_type' => $request->input('action_type'),
                'error' => $e->getMessage(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Penilai Universitas validation error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => Auth::id(),
                'action_type' => $request->input('action_type'),
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses validasi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses validasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Auto-save validation data - similar to Admin Univ Usulan.
     */
    private function autosaveValidation(Request $request, Usulan $usulan, $penilaiId)
    {
        $validationData = $request->input('validation');

        // If validation data is JSON string, decode it
        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        // Save validation data using the model method
        $usulan->setValidasiByRole('penilai_universitas', $validationData, $penilaiId);
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_penilai_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi tersimpan otomatis.'
        ]);
    }

    /**
     * Save simple validation - similar to Admin Univ Usulan.
     */
    private function saveSimpleValidation(Request $request, Usulan $usulan, $penilaiId)
    {
        $validationData = $request->input('validation');

        // Save validation data using the model method
        $usulan->setValidasiByRole('penilai_universitas', $validationData, $penilaiId);
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_penilai_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ]);
    }

    /**
     * Handle rekomendasi - enhanced version.
     */
    private function handleRekomendasi(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Save validation data
        $validationData = $request->input('validation');
        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        $usulan->setValidasiByRole('penilai_universitas', $validationData, $penilaiId);

        // Update penilai individual status in pivot table
        $usulan->penilais()->updateExistingPivot($penilaiId, [
            'status_penilaian' => 'Sesuai',
            'catatan_penilaian' => $request->input('catatan_umum'),
            'updated_at' => now()
        ]);

        // Add recommendation data with enhanced structure
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['penilai_universitas']['recommendation'] = 'direkomendasikan';
        $currentValidasi['penilai_universitas']['catatan_rekomendasi'] = $request->input('catatan_umum');
        $currentValidasi['penilai_universitas']['tanggal_rekomendasi'] = now()->toDateTimeString();
        $currentValidasi['penilai_universitas']['penilai_id'] = $penilaiId;
        $currentValidasi['penilai_universitas']['status'] = 'menunggu_admin_univ_review';

        $usulan->validasi_data = $currentValidasi;

        // Determine and update usulan status based on all penilai progress
        $newStatus = $usulan->determinePenilaiFinalStatus();
        if ($newStatus) {
            $usulan->status_usulan = $newStatus;
        }

        $usulan->save();

        // Clear cache
        $this->penilaiService->clearPenilaiCache($penilaiId);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.',
            'redirect' => route('penilai-universitas.pusat-usulan.index')
        ]);
    }

    /**
     * Handle perbaikan usulan - enhanced version.
     */
    private function handlePerbaikanUsulan(Request $request, Usulan $usulan, $penilaiId)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Save validation data
        $validationData = $request->input('validation');
        if (is_string($validationData)) {
            $validationData = json_decode($validationData, true);
        }

        $usulan->setValidasiByRole('penilai_universitas', $validationData, $penilaiId);

        // Update penilai individual status in pivot table
        $usulan->penilais()->updateExistingPivot($penilaiId, [
            'status_penilaian' => 'Perlu Perbaikan',
            'catatan_penilaian' => $request->input('catatan_umum'),
            'updated_at' => now()
        ]);

        // Add return data with enhanced structure
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['penilai_universitas']['perbaikan_usulan'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'penilai_id' => $penilaiId,
            'status' => 'menunggu_admin_univ_review'
        ];

        $usulan->validasi_data = $currentValidasi;

        // Determine and update usulan status based on all penilai progress
        $newStatus = $usulan->determinePenilaiFinalStatus();
        if ($newStatus) {
            $usulan->status_usulan = $newStatus;
        }

        $usulan->save();

        // Clear cache
        $this->penilaiService->clearPenilaiCache($penilaiId);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.',
            'redirect' => route('penilai-universitas.pusat-usulan.index')
        ]);
    }

    /**
     * Show usulan document
     */
    public function showDocument(Usulan $usulan, $field)
    {
        $currentPenilai = Auth::user();
        return $this->documentService->showUsulanDocument($usulan, $field, $currentPenilai->id);
    }

    /**
     * Show pegawai document
     */
    public function showPegawaiDocument(Usulan $usulan, $field)
    {
        $currentPenilai = Auth::user();
        return $this->documentService->showPegawaiDocument($usulan, $field, $currentPenilai->id);
    }

    /**
     * Show admin fakultas document
     */
    public function showAdminFakultasDocument(Usulan $usulan, $field)
    {
        $currentPenilai = Auth::user();
        return $this->documentService->showAdminFakultasDocument($usulan, $field, $currentPenilai->id);
    }

    /**
     * ENHANCED: Perform consistency check for Penilai Universitas
     */
    private function performPenilaiConsistencyCheck(Usulan $usulan)
    {
        $issues = [];
        $corrections = [];
        $warnings = [];

        try {
            $currentPenilai = Auth::user();
            
            // Check 1: Penilai Assignment Validation
            if (!$usulan->isAssignedToPenilai($currentPenilai->id)) {
                $issues[] = "Anda tidak ditugaskan untuk usulan ini";
            }

            // Check 2: Status Validation for Penilai
            $allowedStatuses = [
                'Sedang Direview',
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_TIM_PENILAI,
                'Menunggu Hasil Penilaian Tim Penilai'
            ];
            
            if (!in_array($usulan->status_usulan, $allowedStatuses)) {
                $issues[] = "Status usulan tidak sesuai untuk penilaian: '{$usulan->status_usulan}'";
            }

            // Check 3: Validation Data Integrity
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            
            // Check if current penilai has existing validation data
            $hasExistingValidation = !empty($timPenilaiData['validation']);
            $hasRecommendation = !empty($timPenilaiData['recommendation']);
            $hasPerbaikan = !empty($timPenilaiData['perbaikan_usulan']);
            
            if ($hasRecommendation && $hasPerbaikan) {
                $warnings[] = "Usulan memiliki data rekomendasi dan perbaikan sekaligus";
            }

            // Check 4: Document Access Validation
            $requiredDocuments = ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja'];
            foreach ($requiredDocuments as $docType) {
                if (!isset($timPenilaiData['validation'][$docType])) {
                    $warnings[] = "Data validasi untuk {$docType} belum tersedia";
                }
            }

            // Check 5: Assessment Progress Validation
            $penilais = $usulan->penilais ?? collect();
            $totalPenilai = $penilais->count();
            $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
            
            if ($totalPenilai === 0) {
                $warnings[] = "Tidak ada penilai yang ditugaskan untuk usulan ini";
            } elseif ($completedPenilai === $totalPenilai) {
                $warnings[] = "Semua penilai telah menyelesaikan penilaian";
            } elseif ($completedPenilai > 0) {
                $warnings[] = "Progress penilaian: {$completedPenilai}/{$totalPenilai} penilai selesai";
            }

        } catch (\Exception $e) {
            Log::error('Penilai consistency check error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $issues[] = "Error during consistency check: " . $e->getMessage();
        }

        return [
            'has_issues' => !empty($issues),
            'has_warnings' => !empty($warnings),
            'has_corrections' => !empty($corrections),
            'issues' => $issues,
            'warnings' => $warnings,
            'corrections' => $corrections,
            'total_checks' => 5,
            'checks_passed' => 5 - count($issues) - count($warnings)
        ];
    }
}

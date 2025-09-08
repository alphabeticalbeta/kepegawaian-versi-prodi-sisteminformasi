<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class UsulanValidationController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all usulans for university overview (no status filter)
        $usulans = Usulan::with([
                'pegawai:id,nama_lengkap,nip,jenis_pegawai,unit_kerja_id,unit_kerja_id',
                'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai,status'
            ])
            ->latest()
            ->paginate(10);

        // Get periode information (using the first usulan's periode or create default)
        $periode = null;
        if ($usulans->count() > 0) {
            $periode = $usulans->first()->periodeUsulan;
        } else {
            // If no usulans, get the most recent active periode
            $periode = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('status', 'Buka')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Set default values for view compatibility
        $jenisUsulan = 'jabatan';
        $namaUsulan = 'Usulan Jabatan';

        // Calculate statistics
        $stats = [
            'total_usulan' => $usulans->total(),
            'usulan_disetujui' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
            'usulan_ditolak' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_pending' => $usulans->whereIn('status_usulan', [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS
            ])->count(),
        ];

        return view('backend.layouts.views.kepegawaian-universitas.usulan.index', compact(
            'usulans',
            'periode',
            'namaUsulan',
            'jenisUsulan',
            'stats'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        $usulan = $usulan->load([
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'jabatanLama',
            'jabatanTujuan',
            'periodeUsulan',
            'penilais'
        ]);

        // ENHANCED: Consistency Check and Auto-Correction
        $consistencyCheck = $this->performConsistencyCheck($usulan);

        // ENHANCED: Auto-update status based on penilai progress
        $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();

        // If status was updated, reload the usulan to get fresh data
        if ($statusWasUpdated) {
            $usulan->refresh();
            Log::info('Status auto-updated in Admin Universitas show method', [
                'usulan_id' => $usulan->id,
                'new_status' => $usulan->status_usulan
            ]);
        }

        // ENHANCED: Check if usulan is in correct status for Kepegawaian Unviersitas Universitas
        $allowedStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_KE_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN
        ];

        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return redirect()->route('backend.kepegawaian-universitas.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai. Status saat ini: ' . $usulan->status_usulan);
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];

        // Determine if Admin Universitas can edit (based on status)
        $canEdit = in_array($usulan->status_usulan, [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_KE_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
        ]);

        // Special case: View only for status where perbaikan sudah dikirim ke Admin Fakultas atau Pegawai
        if ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS
            || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS) {
            $canEdit = false; // View only mode
        }

        // Get active penilais for selection
        $penilais = \App\Models\KepegawaianUniversitas\Penilai::getActivePenilais();

        // Get currently assigned penilais for this usulan
        $assignedPenilaiIds = $usulan->penilais()->pluck('penilai_id')->toArray();

        // Get action buttons based on status
        $actionButtons = $this->getActionButtonsForStatus($usulan->status_usulan);

        // Get penilai assessment progress information
        $penilaiProgress = $usulan->getPenilaiAssessmentProgress();

        // Get detailed penilai progress data for status penilaian section
        $penilaiProgressData = $this->getPenilaiProgressData($usulan);

        return view('backend.layouts.views.kepegawaian-universitas.usulan.detail', compact(
            'usulan',
            'existingValidation',
            'canEdit',
            'penilais',
            'assignedPenilaiIds', // ← NEW: IDs penilai yang sudah ditugaskan
            'actionButtons',
            'penilaiProgress',
            'penilaiProgressData', // ← NEW
            'statusWasUpdated',
            'consistencyCheck'
        ));
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {
        $actionType = $request->input('action_type');

        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Hanya validasi business logic yang wajar, tidak ada batasan status ketat

        // Business logic validations for Kepegawaian Universitas
        if (in_array($actionType, ['kirim_ke_senat'])) {
            // Cek apakah sudah ada rekomendasi dari penilai sebelum kirim ke senat
            $hasPenilaiRecommendation = $usulan->penilais()
                ->whereNotNull('hasil_penilaian')
                ->where('hasil_penilaian', 'rekomendasi')
                ->exists();

            if (!$hasPenilaiRecommendation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usulan belum dapat dikirim ke Tim Senat karena belum ada rekomendasi dari Tim Penilai.'
                ], 422);
            }
        }

        // Validasi untuk action yang memerlukan catatan
        if (in_array($actionType, ['perbaikan_ke_pegawai', 'perbaikan_ke_fakultas', 'perbaikan_penilai_ke_pegawai', 'perbaikan_penilai_ke_fakultas', 'kirim_perbaikan_ke_penilai', 'tidak_direkomendasikan', 'kirim_ke_senat'])) {
            $catatan = $request->input('catatan_verifikator');
            if (empty($catatan) || strlen(trim($catatan)) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan wajib diisi minimal 10 karakter untuk tindakan ini.'
                ], 422);
            }
        }

        try {
            if ($actionType === 'autosave') {
                return $this->autosaveValidation($request, $usulan);
            } elseif ($actionType === 'return_to_pegawai') {
                return $this->returnToPegawai($request, $usulan);
            } elseif ($actionType === 'return_to_fakultas') {
                return $this->returnToFakultas($request, $usulan);
            } elseif ($actionType === 'forward_to_penilai') {
                return $this->forwardToPenilai($request, $usulan);
            } elseif ($actionType === 'forward_to_senat') {
                return $this->forwardToSenat($request, $usulan);
            } elseif ($actionType === 'return_from_penilai') {
                return $this->returnFromPenilai($request, $usulan);
            } elseif (in_array($actionType, ['approve_perbaikan', 'approve_rekomendasi', 'reject_perbaikan', 'reject_rekomendasi'])) {
                return $this->handlePenilaiReview($request, $usulan);
            } elseif ($actionType === 'tidak_direkomendasikan') {
                return $this->handleTidakDirekomendasikan($request, $usulan);
            } elseif ($actionType === 'perbaikan_ke_pegawai') {
                return $this->perbaikanKePegawai($request, $usulan);
            } elseif ($actionType === 'perbaikan_ke_fakultas') {
                return $this->perbaikanKeFakultas($request, $usulan);
            } elseif ($actionType === 'perbaikan_penilai_ke_pegawai') {
                return $this->perbaikanPenilaiKePegawai($request, $usulan);
            } elseif ($actionType === 'perbaikan_penilai_ke_fakultas') {
                return $this->perbaikanPenilaiKeFakultas($request, $usulan);
            } elseif ($actionType === 'kirim_perbaikan_ke_penilai') {
                return $this->kirimPerbaikanKePenilai($request, $usulan);
            } elseif ($actionType === 'kirim_ke_senat') {
                return $this->kirimKeSenat($request, $usulan);
            } elseif ($actionType === 'kirim_ke_penilai') {
                return $this->kirimKePenilai($request, $usulan);
            } elseif ($actionType === 'teruskan_ke_penilai') {
                return $this->teruskanKePenilai($request, $usulan);
            } elseif ($actionType === 'send_to_assessor_team') {
                // NEW: Kirim ke Tim Penilai (menggunakan route save-validation)
                return $this->sendToAssessorTeam($request, $usulan);
            } elseif ($actionType === 'kembali') {
                return $this->kembali($request, $usulan);
            } elseif ($actionType === 'save_only') {
                return $this->saveSimpleValidation($request, $usulan);
            } else {
                return $this->saveSimpleValidation($request, $usulan);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Admin Universitas validation error', [
                'usulan_id' => $usulan->id,
                'action_type' => $actionType,
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
            Log::error('Admin Universitas validation error', [
                'usulan_id' => $usulan->id,
                'action_type' => $actionType,
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi.'
            ], 500);
        }
    }

    /**
     * Auto-save validation data.
     */
    private function autosaveValidation(Request $request, Usulan $usulan)
    {
        $validationData = json_decode($request->input('validation_data'), true);

        // Only save validation data if it exists and is not null
        if ($validationData && is_array($validationData)) {
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi tersimpan otomatis.'
        ]);
    }

    /**
     * Save simple validation.
     */
    private function saveSimpleValidation(Request $request, Usulan $usulan)
    {
        try {
            $validationData = $request->input('validation');
            $keteranganUmum = $request->input('keterangan_umum');

            // Debug: Log received data
            Log::info('saveSimpleValidation received data', [
                'usulan_id' => $usulan->id,
                'validation_data' => $validationData,
                'keterangan_umum' => $keteranganUmum,
                'request_all' => $request->all()
            ]);

            // Process validation data - ensure it's an array
            $processedValidationData = [];
            if ($validationData && is_array($validationData)) {
                $processedValidationData = $validationData;
            } elseif ($validationData && is_string($validationData)) {
                // If it's a JSON string, decode it first
                $decodedData = json_decode($validationData, true);
                if ($decodedData && is_array($decodedData)) {
                    $processedValidationData = $decodedData;
                }
            }

            // Validate that we have validation data to save
            if (empty($processedValidationData) && empty($keteranganUmum)) {
                Log::warning('No validation data to save', [
                    'usulan_id' => $usulan->id,
                    'validation_data' => $validationData,
                    'keterangan_umum' => $keteranganUmum
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data validasi yang perlu disimpan.'
                ], 400);
            }

            // Save validation data with keterangan_umum
            $usulan->setValidasiByRole('kepegawaian_universitas', $processedValidationData, Auth::id(), $keteranganUmum ?? '');



            $usulan->save();

            // Clear related caches
            $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
            Cache::forget($cacheKey);

            Log::info('Validation data saved successfully', [
                'usulan_id' => $usulan->id,
                'validation_data_count' => !empty($processedValidationData) ? count($processedValidationData) : 0,
                'has_keterangan_umum' => !empty($keteranganUmum)
            ]);

            // Return JSON response for consistency
            return response()->json([
                'success' => true,
                'message' => 'Data validasi berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save simple validation', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'validation_data' => $validationData ?? null,
                'keterangan_umum' => $keteranganUmum ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * NEW: Handler untuk aksi "Kirim Usulan ke Tim Penilai" melalui route save-validation
     * Agar tetap menggunakan route backend.kepegawaian-universitas.usulan.save-validation
     */
    private function sendToAssessorTeam(Request $request, Usulan $usulan)
    {
        // Validasi input assessor
        $validated = $request->validate([
            'assessor_ids' => 'required|array|min:1|max:3',
            'assessor_ids.*' => 'exists:pegawais,id'
        ], [
            'assessor_ids.required' => 'Pilih minimal 1 penilai.',
            'assessor_ids.array' => 'Format data penilai tidak valid.',
            'assessor_ids.min' => 'Pilih minimal 1 penilai.',
            'assessor_ids.max' => 'Pilih maksimal 3 penilai.',
            'assessor_ids.*.exists' => 'Penilai yang dipilih tidak valid.'
        ]);

        $assessorIds = $validated['assessor_ids'];

        // Siapkan data pivot dengan default status
        $assessorData = [];
        foreach ($assessorIds as $assessorId) {
            $assessorData[$assessorId] = [
                'status_penilaian' => 'Belum Dinilai',
                'catatan_penilaian' => null,
            ];
        }

        // Sync: menambah yang baru dan menghapus yang di-uncheck
        $usulan->penilais()->sync($assessorData);

        // Update status usulan sesuai kebutuhan
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS;
        $usulan->save();

        // Tulis log singkat
        if (method_exists($this, 'createUsulanLog')) {
            $this->createUsulanLog(
                $usulan,
                'Usulan Disetujui Kepegawaian Universitas dan Menunggu Penilaian',
                'Usulan dikirim ke Tim Penilai (' . count($assessorIds) . ' penilai)'
            );
        }

        // Kembalikan JSON sesuai format yang diminta
        return response()->json([
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ]);
    }

    /**
     * Return usulan to pegawai for revision.
     */
    private function returnToPegawai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            // Only save if validation data is valid array
            if ($validationData && is_array($validationData)) {
                $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
            }
        }
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Pegawai untuk perbaikan.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Penilai.
     */
    private function forwardToPenilai(Request $request, Usulan $usulan)
    {
        // Add detailed logging for debugging like Admin Fakultas
        Log::info('Kepegawaian Universitas forwardToPenilai started', [
            'usulan_id' => $usulan->id,
            'request_data' => $request->all(),
            'selected_penilais' => $request->input('selected_penilais'),
            'user_id' => Auth::id()
        ]);

        // Check available penilais for debugging
        $availablePenilais = \App\Models\KepegawaianUniversitas\Penilai::all(['id', 'nama_lengkap', 'status_kepegawaian']);
        Log::info('Available penilais for validation', [
            'penilais_count' => $availablePenilais->count(),
            'penilais_data' => $availablePenilais->toArray()
        ]);

        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000',
            'selected_penilais' => 'required|array|min:1',
            'selected_penilais.*' => 'exists:pegawais,id'
        ]);

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS;

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            // Only save if validation data is valid array
            if ($validationData && is_array($validationData)) {
                $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
            }
        }

        // Assign selected penilais to usulan
        $selectedPenilais = $request->input('selected_penilais');
        $usulan->penilais()->sync($selectedPenilais);

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['kepegawaian_universitas']['forward_to_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'selected_penilais' => $selectedPenilais
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '🎉 Usulan berhasil diteruskan ke Tim Penilai! Status usulan telah berubah menjadi "Sedang Direview". Tim Penilai akan segera memproses usulan ini.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Return usulan to fakultas for revision.
     */
    private function returnToFakultas(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Admin Fakultas untuk perbaikan.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Senat.
     */
    private function forwardToSenat(Request $request, Usulan $usulan)
    {
        // Check if Tim Penilai has given recommendation
        $hasRecommendation = $usulan->validasi_data['tim_penilai']['recommendation'] ?? false;
        if ($hasRecommendation !== 'direkomendasikan') {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat diteruskan ke senat karena belum ada rekomendasi dari tim penilai.'
            ], 422);
        }

        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS;

        // Save validation data with forward note
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['kepegawaian_universitas']['forward_to_senat'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diteruskan ke Tim Senat.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle review dari Tim Penilai - ALUR BARU
     */
    private function handlePenilaiReview(Request $request, Usulan $usulan)
    {
        $request->validate([
            'action_type' => 'required|in:approve_perbaikan,approve_rekomendasi,reject_perbaikan,reject_rekomendasi',
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        $actionType = $request->input('action_type');
        $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
        $hasRecommendation = $penilaiReview['recommendation'] ?? false;

        switch ($actionType) {
            case 'approve_perbaikan':
                // Admin Univ setuju dengan perbaikan usulan, teruskan ke pegawai
                $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
                $catatan = "Admin Universitas menyetujui hasil review Tim Penilai. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['kepegawaian_universitas']['review_penilai'] = [
                    'action' => 'approve_perbaikan',
                    'catatan' => $request->input('catatan_umum'),
                    'tanggal_review' => now()->toDateTimeString(),
                    'admin_id' => Auth::id()
                ];
                $usulan->validasi_data = $currentValidasi;

                $message = 'Usulan berhasil diteruskan ke Pegawai untuk perbaikan.';
                break;

            case 'approve_rekomendasi':
                // Admin Univ setuju dengan rekomendasi, teruskan ke tim senat
                if ($hasRecommendation !== 'direkomendasikan') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada rekomendasi dari Tim Penilai untuk disetujui.'
                    ], 422);
                }

                $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS;
                $catatan = "Admin Universitas menyetujui rekomendasi Tim Penilai. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['kepegawaian_universitas']['review_penilai'] = [
                    'action' => 'approve_rekomendasi',
                    'catatan' => $request->input('catatan_umum'),
                    'tanggal_review' => now()->toDateTimeString(),
                    'admin_id' => Auth::id()
                ];
                $usulan->validasi_data = $currentValidasi;

                $message = 'Usulan berhasil diteruskan ke Tim Senat.';
                break;

            case 'reject_perbaikan':
                // Admin Univ tidak setuju dengan perbaikan, kembalikan ke penilai
                $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS;
                $catatan = "Admin Universitas tidak menyetujui hasil review. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['kepegawaian_universitas']['review_penilai'] = [
                    'action' => 'reject_perbaikan',
                    'catatan' => $request->input('catatan_umum'),
                    'tanggal_review' => now()->toDateTimeString(),
                    'admin_id' => Auth::id()
                ];
                $usulan->validasi_data = $currentValidasi;

                $message = 'Usulan dikembalikan ke Tim Penilai untuk review ulang.';
                break;

            case 'reject_rekomendasi':
                // Admin Univ tidak setuju dengan rekomendasi, kembalikan ke penilai
                $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS;
                $catatan = "Admin Universitas tidak menyetujui rekomendasi. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['kepegawaian_universitas']['review_penilai'] = [
                    'action' => 'reject_rekomendasi',
                    'catatan' => $request->input('catatan_umum'),
                    'tanggal_review' => now()->toDateTimeString(),
                    'admin_id' => Auth::id()
                ];
                $usulan->validasi_data = $currentValidasi;

                $message = 'Rekomendasi ditolak, usulan dikembalikan ke Tim Penilai.';
                break;
        }

        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Tidak Direkomendasikan" action
     */
    private function handleTidakDirekomendasikan(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Tidak ada batasan status, bisa dilakukan kapan saja

        $catatan = $request->input('catatan_verifikator');

        // Update usulan status to "Tidak Direkomendasikan"
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
        $usulan->catatan_verifikator = $catatan;

        // Add rejection data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['tidak_direkomendasikan'] = [
            'catatan' => $catatan,
            'tanggal_rejection' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'alasan' => 'Usulan tidak direkomendasikan untuk periode berjalan',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Tidak Direkomendasikan', 'Usulan ditandai tidak direkomendasikan: ' . $catatan);

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        Log::info('Usulan tidak direkomendasikan', [
            'usulan_id' => $usulan->id,
            'admin_id' => Auth::id(),
            'catatan' => $catatan,
            'status' => 'Tidak Direkomendasikan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usulan telah ditandai sebagai tidak direkomendasikan. Usulan tidak dapat diajukan kembali pada periode berjalan.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Return usulan from Tim Penilai back to Admin Universitas.
     */
    private function returnFromPenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status back to 'Diusulkan ke Universitas'
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS;

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Add return information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['kepegawaian_universitas']['return_from_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan dari Tim Penilai ke Admin Universitas.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Show document.
     */
    public function showDocument(Usulan $usulan, $field)
    {
        // Get document path based on field
        $docPath = $usulan->getDocumentPath($field);

        if (!$docPath || !Storage::disk('local')->exists($docPath)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(Storage::disk('local')->path($docPath));
    }

    /**
     * Show pegawai document.
     */
    public function showPegawaiDocument(Usulan $usulan, $field)
    {
        // Get pegawai document path
        $docPath = $usulan->pegawai->$field ?? null;

        if (!$docPath || !Storage::disk('local')->exists($docPath)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(Storage::disk('local')->path($docPath));
    }

    /**
     * Toggle periode status (Buka/Tutup).
     */
    public function togglePeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_usulans,id'
        ]);

        try {
            $periode = \App\Models\KepegawaianUniversitas\PeriodeUsulan::findOrFail($request->periode_id);

            // Toggle status
            $newStatus = $periode->status === 'Buka' ? 'Tutup' : 'Buka';
            $periode->status = $newStatus;
            $periode->save();

            $statusText = $newStatus === 'Buka' ? 'dibuka' : 'ditutup';

            return response()->json([
                'success' => true,
                'message' => "Periode berhasil {$statusText}.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling periode status: ' . $e->getMessage(), [
                'periode_id' => $request->periode_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status periode.'
            ], 500);
        }
    }

    /**
     * Get action buttons based on usulan status
     * ENHANCED: Consistent with new Blade template logic
     */
    private function getActionButtonsForStatus($status)
    {
        switch ($status) {
            case 'Diusulkan ke Universitas':
                return [
                    'perbaikan_ke_pegawai' => 'Perbaikan ke Pegawai',
                    'perbaikan_ke_fakultas' => 'Perbaikan ke Fakultas',
                    'teruskan_ke_penilai' => 'Teruskan ke Tim Penilai',
                    'tidak_direkomendasikan' => 'Tidak Direkomendasikan'
                ];

            case \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI:
                return [
                    'kirim_ke_penilai' => 'Kirim Ke Penilai',
                    'kembali' => 'Kembali'
                ];

            case 'Perbaikan Dari Tim Penilai':
                return [
                    'perbaikan_ke_pegawai' => 'Teruskan Perbaikan ke Pegawai',
                    'perbaikan_ke_fakultas' => 'Teruskan Perbaikan ke Fakultas',
                    'kirim_perbaikan_ke_penilai' => 'Kirim Perbaikan ke Penilai Universitas',
                    'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
                    'kirim_ke_senat' => 'Kirim Ke Senat'
                ];

            case 'Usulan Direkomendasi Tim Penilai':
                return [
                    'perbaikan_ke_pegawai' => 'Teruskan Perbaikan ke Pegawai',
                    'perbaikan_ke_fakultas' => 'Teruskan Perbaikan ke Fakultas',
                    'kirim_perbaikan_ke_penilai' => 'Kirim Perbaikan ke Penilai Universitas',
                    'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
                    'kirim_ke_senat' => 'Kirim Ke Senat'
                ];

            case 'Sedang Direview':
                return [
                    'kirim_ke_penilai' => 'Kirim Ke Penilai',
                    'kembali' => 'Kembali'
                ];

            case \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS:
                return [
                    'kembali' => 'Kembali'
                ];

            case \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS:
                return [
                    'perbaikan_ke_pegawai' => 'Permintaan Perbaikan Ke Pegawai dari Kepegawaian Universitas',
                    'perbaikan_ke_fakultas' => 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas',
                    'teruskan_ke_penilai' => 'Teruskan ke Tim Penilai',
                    'tidak_direkomendasikan' => 'Tidak Direkomendasikan'
                ];

            default:
                return [];
        }
    }

    /**
     * Handle "Perbaikan ke Pegawai" action
     */
    private function perbaikanKePegawai(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Tidak ada batasan status, bisa dilakukan kapan saja

        $catatan = $request->input('catatan_verifikator');

        // AUTO-SAVE: Simpan validasi field terlebih dahulu
        if ($request->has('validation')) {
            $validationData = $request->input('validation');
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS;
        $usulan->catatan_verifikator = $catatan;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['perbaikan_ke_pegawai'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_ke_pegawai',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Perbaikan Usulan', 'Usulan dikirim ke Pegawai untuk perbaikan: ' . $catatan);

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Pegawai untuk perbaikan.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Perbaikan ke Fakultas" action
     */
    private function perbaikanKeFakultas(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Tidak ada batasan status, bisa dilakukan kapan saja

        $catatan = $request->input('catatan_verifikator');

        // AUTO-SAVE: Simpan validasi field terlebih dahulu
        if ($request->has('validation')) {
            $validationData = $request->input('validation');
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS;
        $usulan->catatan_verifikator = $catatan;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['perbaikan_ke_fakultas'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_ke_fakultas',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Perbaikan Usulan', 'Usulan dikirim ke Admin Fakultas untuk perbaikan: ' . $catatan);

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Fakultas untuk perbaikan.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Kirim Perbaikan ke Penilai Universitas" action
     */
    private function kirimPerbaikanKePenilai(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Tidak ada batasan status, bisa dilakukan kapan saja

        $catatan = $request->input('catatan_verifikator');

        // Update usulan status with new constant
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_KE_PENILAI_UNIVERSITAS;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['kirim_perbaikan_ke_penilai'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_perbaikan_ke_penilai',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'prevent_auto_update' => true // Flag untuk mencegah auto-update
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Sedang Direview', 'Usulan dikirim kembali ke Tim Penilai untuk penilaian ulang: ' . $catatan);

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim kembali ke Tim Penilai untuk penilaian ulang.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Kirim Ke Senat" action
     */
    private function kirimKeSenat(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Business logic: Cek apakah sudah ada rekomendasi dari penilai (sudah divalidasi di atas)

        $catatan = $request->input('catatan_verifikator');

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['kirim_ke_senat'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_ke_senat',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Direkomendasikan', 'Usulan dikirim ke Tim Senat untuk keputusan final: ' . $catatan);

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Tim Senat untuk keputusan final.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Kirim Ke Penilai" action (for intermediate status)
     */
    private function kirimKePenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['kepegawaian_universitas']['kirim_ke_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_ke_penilai'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Instruksi berhasil dikirim ke Tim Penilai.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle "Kembali" action
     */
    private function kembali(Request $request, Usulan $usulan)
    {
        // Save validation data if any
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Kembali ke halaman sebelumnya.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * ENHANCED: Perform consistency check and auto-correction
     */
    private function performConsistencyCheck(Usulan $usulan)
    {
        $issues = [];
        $corrections = [];
        $warnings = [];

        try {
            // Check 1: Status vs Penilai Assignment Consistency
            $penilais = $usulan->penilais ?? collect();
            $totalPenilai = $penilais->count();
            $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();

            // Check if status matches penilai progress
            $expectedStatus = $this->determineExpectedStatus($totalPenilai, $completedPenilai, $usulan->status_usulan);

            if ($expectedStatus !== $usulan->status_usulan) {
                $issues[] = "Status inconsistency: Current status '{$usulan->status_usulan}' doesn't match penilai progress";
                $corrections[] = "Status should be: '{$expectedStatus}'";

                // Auto-correct if needed
                if ($this->shouldAutoCorrectStatus($usulan->status_usulan, $expectedStatus)) {
                    $oldStatus = $usulan->status_usulan;
                    $usulan->status_usulan = $expectedStatus;
                    $usulan->save();

                    Log::info('Status auto-corrected for consistency', [
                        'usulan_id' => $usulan->id,
                        'old_status' => $oldStatus,
                        'new_status' => $expectedStatus,
                        'total_penilai' => $totalPenilai,
                        'completed_penilai' => $completedPenilai
                    ]);
                }
            }

            // Check 2: Penilai Data Integrity
            foreach ($penilais as $penilai) {
                $pivot = $penilai->pivot ?? null;

                if ($pivot) {
                    // Check for incomplete assessment data
                    if (!empty($pivot->hasil_penilaian) && empty($pivot->tanggal_penilaian)) {
                        $warnings[] = "Penilai {$penilai->nama_lengkap} has assessment result but no date";

                        // Auto-correct: Set default date if missing
                        if (empty($pivot->tanggal_penilaian)) {
                            $usulan->penilais()->updateExistingPivot($penilai->id, [
                                'tanggal_penilaian' => now()
                            ]);
                            $corrections[] = "Added missing assessment date for {$penilai->nama_lengkap}";
                        }
                    }

                    // Check for invalid assessment results
                    $validResults = ['rekomendasi', 'perbaikan', 'tidak_rekomendasi'];
                    if (!empty($pivot->hasil_penilaian) && !in_array($pivot->hasil_penilaian, $validResults)) {
                        $issues[] = "Invalid assessment result for {$penilai->nama_lengkap}: '{$pivot->hasil_penilaian}'";
                    }
                }
            }

            // Check 3: Validasi Data Consistency
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];

            // Check if assessment summary matches actual penilai data
            if (isset($timPenilaiData['assessment_summary'])) {
                $summary = $timPenilaiData['assessment_summary'];
                $summaryTotal = $summary['total_penilai'] ?? 0;
                $summaryCompleted = $summary['completed_penilai'] ?? 0;

                if ($summaryTotal !== $totalPenilai || $summaryCompleted !== $completedPenilai) {
                    $issues[] = "Assessment summary data mismatch";
                    $corrections[] = "Summary shows {$summaryCompleted}/{$summaryTotal}, actual: {$completedPenilai}/{$totalPenilai}";

                    // Auto-correct summary data
                    $timPenilaiData['assessment_summary']['total_penilai'] = $totalPenilai;
                    $timPenilaiData['assessment_summary']['completed_penilai'] = $completedPenilai;
                    $timPenilaiData['assessment_summary']['remaining_penilai'] = max(0, $totalPenilai - $completedPenilai);
                    $timPenilaiData['assessment_summary']['progress_percentage'] = $totalPenilai > 0 ? ($completedPenilai / $totalPenilai) * 100 : 0;
                    $timPenilaiData['assessment_summary']['is_complete'] = ($totalPenilai > 0) && ($completedPenilai === $totalPenilai);
                    $timPenilaiData['assessment_summary']['is_intermediate'] = ($totalPenilai > 0) && ($completedPenilai < $totalPenilai);

                    $validasiData['tim_penilai'] = $timPenilaiData;
                    $usulan->validasi_data = $validasiData;
                    $usulan->save();

                    Log::info('Assessment summary auto-corrected', [
                        'usulan_id' => $usulan->id,
                        'old_summary' => $summary,
                        'new_summary' => $timPenilaiData['assessment_summary']
                    ]);
                }
            }

            // Check 4: Orphaned Penilai Assignments
            $orphanedPenilais = $penilais->filter(function($penilai) {
                return empty($penilai->pivot->hasil_penilaian) &&
                       $penilai->pivot->created_at &&
                       $penilai->pivot->created_at->diffInDays(now()) > 30;
            });

            if ($orphanedPenilais->count() > 0) {
                $warnings[] = "Found {$orphanedPenilais->count()} penilai assignments older than 30 days without assessment";
            }

        } catch (\Exception $e) {
            Log::error('Consistency check error', [
                'usulan_id' => $usulan->id,
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
            'total_checks' => 4,
            'checks_passed' => 4 - count($issues) - count($warnings)
        ];
    }

    /**
     * Determine expected status based on penilai progress
     */
    private function determineExpectedStatus($totalPenilai, $completedPenilai, $currentStatus)
    {
        // If no penilai assigned, status should not be in assessment phase
        if ($totalPenilai === 0) {
            if (in_array($currentStatus, ['Sedang Direview', \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI])) {
                return 'Diusulkan ke Universitas';
            }
            return $currentStatus;
        }

        // If all penilai completed, determine final status
        if ($completedPenilai === $totalPenilai) {
            return $this->determineFinalStatus($totalPenilai, $completedPenilai);
        }

        // If some penilai completed but not all, should be intermediate status
        if ($completedPenilai > 0 && $completedPenilai < $totalPenilai) {
            return \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;
        }

        // If no penilai completed but assigned, should be in review status
        if ($completedPenilai === 0 && $totalPenilai > 0) {
            return 'Sedang Direview';
        }

        return $currentStatus;
    }

    /**
     * Determine final status based on assessment results
     */
    private function determineFinalStatus($totalPenilai, $completedPenilai)
    {
        // This should match the logic in Usulan model
        // For now, return a default status - this should be enhanced based on actual assessment results
        return 'Perbaikan Dari Tim Penilai'; // Default fallback
    }

    /**
     * Determine if status should be auto-corrected
     */
    private function shouldAutoCorrectStatus($currentStatus, $expectedStatus)
    {
        // Only auto-correct in specific scenarios to avoid unwanted changes
        $autoCorrectScenarios = [
            // From intermediate to final status
            [\App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI, 'Perbaikan Dari Tim Penilai'],
            [\App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI, 'Usulan Direkomendasi Tim Penilai'],

            // From final to intermediate status (if penilai data changed)
            ['Perbaikan Dari Tim Penilai', \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI],
            ['Usulan Direkomendasi Tim Penilai', \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI],

            // From review to intermediate status
            ['Sedang Direview', \App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI],

            // From assessment status to initial status (if no penilai)
            ['Sedang Direview', 'Diusulkan ke Universitas'],
            [\App\Models\KepegawaianUniversitas\Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI, 'Diusulkan ke Universitas']
        ];

        return in_array([$currentStatus, $expectedStatus], $autoCorrectScenarios);
    }

    /**
     * Get detailed penilai progress data for the status penilaian section.
     */
    private function getPenilaiProgressData(Usulan $usulan)
    {
        $penilais = $usulan->penilais ?? collect();
        $totalPenilai = $penilais->count();
        $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();

        $penilaiDetails = [];

        foreach ($penilais as $penilai) {
            $detail = [
                'nama' => $penilai->nama_lengkap ?? $penilai->name,
                'status' => !empty($penilai->pivot->hasil_penilaian) ? 'completed' : 'pending'
            ];

            if ($detail['status'] === 'completed') {
                // Data dari pivot table (prioritas utama)
                $detail['tanggal_penilaian'] = $penilai->pivot->tanggal_penilaian;
                $detail['hasil_penilaian'] = $penilai->pivot->hasil_penilaian;

                // Data dari validasi_data (pelengkap detail)
                $validasiData = $usulan->validasi_data ?? [];
                $penilaiData = $validasiData['tim_penilai'] ?? [];

                $detail['field_tidak_sesuai'] = $penilaiData['field_tidak_sesuai'] ?? [];
                $detail['keterangan_field'] = $penilaiData['keterangan_field'] ?? [];
                $detail['keterangan_umum'] = $penilaiData['keterangan_umum'] ??
                                           ($penilai->pivot->keterangan ?? '');
            } else {
                $detail['status_text'] = 'Masih dalam proses penilaian';
            }

            $penilaiDetails[] = $detail;
        }

        return [
            'total_penilai' => $totalPenilai,
            'completed_penilai' => $completedPenilai,
            'penilai_details' => $penilaiDetails
        ];
    }

    /**
     * Handle "Teruskan ke Tim Penilai" action
     */
    private function teruskanKePenilai(Request $request, Usulan $usulan)
    {
        // KEPEGAWAIAN UNIVERSITAS - FLEKSIBILITAS PENUH
        // Tidak ada batasan status, bisa dilakukan kapan saja

        // AUTO-SAVE: Simpan validasi field terlebih dahulu
        if ($request->has('validation')) {
            $validationData = $request->input('validation');
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Update usulan status
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['teruskan_ke_penilai'] = [
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'teruskan_ke_penilai',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Usulan Disetujui Kepegawaian Universitas', 'Usulan diteruskan ke Tim Penilai untuk penilaian.');

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diteruskan ke Tim Penilai.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Create usulan log entry
     */
    private function createUsulanLog(Usulan $usulan, $statusBaru, $catatan)
    {
        try {
            $usulanLog = new \App\Models\KepegawaianUniversitas\UsulanLog();
            $usulanLog->usulan_id = $usulan->id;
            $usulanLog->dilakukan_oleh_id = Auth::id();
            $usulanLog->status_sebelumnya = $usulan->getOriginal('status_usulan');
            $usulanLog->status_baru = $statusBaru;
            $usulanLog->catatan = $catatan;
            $usulanLog->save();
        } catch (\Exception $e) {
            Log::error('Failed to create usulan log', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle perbaikan penilai ke pegawai action
     */
    private function perbaikanPenilaiKePegawai(Request $request, Usulan $usulan)
    {
        $catatan = $request->input('catatan_verifikator');

        // AUTO-SAVE: Simpan validasi field terlebih dahulu
        if ($request->has('validation')) {
            $validationData = $request->input('validation');
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Update usulan status dengan konstanta baru
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_PENILAI;
        $usulan->catatan_verifikator = $catatan;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['perbaikan_penilai_ke_pegawai'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_penilai_ke_pegawai',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Permintaan Perbaikan Ke Pegawai Dari Penilai', "Permintaan perbaikan dari Tim Penilai diteruskan ke Pegawai: {$catatan}");

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan perbaikan dari Tim Penilai berhasil diteruskan ke Pegawai.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Handle perbaikan penilai ke fakultas action
     */
    private function perbaikanPenilaiKeFakultas(Request $request, Usulan $usulan)
    {
        $catatan = $request->input('catatan_verifikator');

        // AUTO-SAVE: Simpan validasi field terlebih dahulu
        if ($request->has('validation')) {
            $validationData = $request->input('validation');
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());
        }

        // Update usulan status dengan konstanta baru
        $usulan->status_usulan = \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_PENILAI;
        $usulan->catatan_verifikator = $catatan;

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data ?? [];
        $currentValidasi['kepegawaian_universitas'] = $currentValidasi['kepegawaian_universitas'] ?? [];
        $currentValidasi['kepegawaian_universitas']['perbaikan_penilai_ke_fakultas'] = [
            'catatan' => $catatan,
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_penilai_ke_fakultas',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan')
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Create usulan log
        $this->createUsulanLog($usulan, 'Permintaan Perbaikan Ke Admin Fakultas Dari Penilai', "Permintaan perbaikan dari Tim Penilai diteruskan ke Admin Fakultas: {$catatan}");

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan perbaikan dari Tim Penilai berhasil diteruskan ke Admin Fakultas.',
            'redirect' => route('backend.kepegawaian-universitas.usulan.index')
        ]);
    }

    /**
     * Display halaman validasi field-by-field untuk usulan kepangkatan
     */
    public function showKepangkatanValidation(Usulan $usulan)
    {
        // Load relationships yang diperlukan
        $usulan = $usulan->load([
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'pangkatTujuan',
            'periodeUsulan'
        ]);

        // Cek apakah usulan adalah jenis kepangkatan
        if ($usulan->jenis_usulan !== 'usulan-kepangkatan') {
            return redirect()->back()->with('error', 'Halaman ini hanya untuk usulan kepangkatan.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];

        // Get penilai validation if exists
        $penilaiValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];

        // Get current role (hardcoded untuk kepegawaian universitas)
        $currentRole = 'Kepegawaian Universitas';

        // Field groups configuration untuk usulan kepangkatan
        $fieldGroups = [
            'data_pribadi' => [
                'label' => 'Data Pribadi',
                'icon' => 'user',
                'fields' => [
                    'jenis_pegawai' => 'Jenis Pegawai',
                    'status_kepegawaian' => 'Status Kepegawaian',
                    'nip' => 'NIP',
                    'nuptk' => 'NUPTK',
                    'nama_lengkap' => 'Nama Lengkap',
                    'email' => 'Email',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'jenis_kelamin' => 'Jenis Kelamin',
                    'nomor_handphone' => 'Nomor Handphone'
                ]
            ],
            'data_kepegawaian' => [
                'label' => 'Data Kepegawaian',
                'icon' => 'briefcase',
                'fields' => [
                    'pangkat_saat_usul' => 'Pangkat Saat Ini',
                    'tmt_pangkat' => 'TMT Pangkat',
                    'jabatan_saat_usul' => 'Jabatan Saat Ini',
                    'tmt_jabatan' => 'TMT Jabatan',
                    'tmt_cpns' => 'TMT CPNS',
                    'tmt_pns' => 'TMT PNS',
                    'unit_kerja_saat_usul' => 'Unit Kerja'
                ]
            ],
            'data_pendidikan' => [
                'label' => 'Data Pendidikan & Fungsional',
                'icon' => 'graduation-cap',
                'fields' => [
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                    'nama_prodi_jurusan' => 'Program Studi/Jurusan'
                ]
            ],
            'data_kinerja' => [
                'label' => 'Data Kinerja',
                'icon' => 'trending-up',
                'fields' => [
                    'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                    'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                    'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
                ]
            ],
            'dokumen_profil' => [
                'label' => 'Dokumen Profil',
                'icon' => 'folder',
                'fields' => [
                    'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                    'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                    'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                    'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                    'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                    'pak_integrasi' => 'PAK Integrasi',
                    'sk_cpns' => 'SK CPNS',
                    'sk_pns' => 'SK PNS',
                    'ijazah_terakhir' => 'Ijazah Terakhir',
                    'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir'
                ]
            ],
            'dokumen_usulan' => [
                'label' => 'Dokumen Usulan',
                'icon' => 'file-text',
                'fields' => []
            ]
        ];

        // Add dynamic fields based on jenis usulan pangkat
        $jenisUsulanPangkat = $usulan->data_usulan['jenis_usulan_pangkat'] ?? '';

        if ($jenisUsulanPangkat === 'Dosen PNS') {
            $fieldGroups['dokumen_usulan']['fields']['dokumen_ukom_sk_jabatan'] = 'Dokumen UKOM dan SK Jabatan';
        } elseif ($jenisUsulanPangkat === 'Jabatan Administrasi') {
            $fieldGroups['dokumen_usulan']['fields']['surat_pencantuman_gelar'] = 'Surat Pencantuman Gelar';
            $fieldGroups['dokumen_usulan']['fields']['surat_lulus_ujian_dinas'] = 'Surat Lulus Ujian Dinas';
        } elseif ($jenisUsulanPangkat === 'Jabatan Fungsional Tertentu') {
            $fieldGroups['dokumen_usulan']['fields']['dokumen_uji_kompetensi'] = 'Surat Uji Kompetensi';
        } elseif ($jenisUsulanPangkat === 'Jabatan Struktural') {
            $fieldGroups['dokumen_usulan']['fields']['surat_pelantikan_berita_acara'] = 'Surat Pelantikan dan Berita Acara Jabatan Terakhir';
            $fieldGroups['dokumen_usulan']['fields']['surat_pencantuman_gelar'] = 'Surat Pencantuman Gelar';
            $fieldGroups['dokumen_usulan']['fields']['sertifikat_diklat_pim_pkm'] = 'Sertifikat Diklat / PIM / PKM';
        }

        // Configuration untuk view
        $config = [
            'title' => 'Validasi Usulan Kepangkatan',
            'description' => 'Validasi field-by-field usulan kepangkatan',
            'routePrefix' => 'backend.kepegawaian-universitas.usulan',
            'documentRoutePrefix' => 'backend.kepegawaian-universitas.usulan',
            'validationFields' => array_keys($fieldGroups)
        ];

        return view('backend.layouts.views.shared.usul-kepangkatan.validasi-kepangkatan', compact(
            'usulan',
            'existingValidation',
            'penilaiValidation',
            'currentRole',
            'fieldGroups',
            'config'
        ));
    }

    /**
     * Save validasi field-by-field untuk usulan kepangkatan
     */
    public function saveKepangkatanValidation(Request $request, Usulan $usulan)
    {
        try {
            // Validate request
            $request->validate([
                'validation' => 'required|array',
                'keterangan_umum' => 'nullable|string|max:1000'
            ]);

            // Get validation data
            $rawValidationData = $request->input('validation');
            $keteranganUmum = $request->input('keterangan_umum');

            // Debug logging untuk keterangan umum
            Log::info('Keterangan umum received in saveKepangkatanValidation', [
                'usulan_id' => $usulan->id,
                'keterangan_umum_raw' => $keteranganUmum,
                'keterangan_umum_type' => gettype($keteranganUmum),
                'keterangan_umum_empty' => empty($keteranganUmum),
                'keterangan_umum_null' => is_null($keteranganUmum),
                'request_all' => $request->all()
            ]);

            // Validate that rawValidationData is not null and is an array
            if (empty($rawValidationData) || !is_array($rawValidationData)) {
                throw new \Exception('Data validasi tidak valid atau kosong');
            }

            // Additional validation: check if any group contains non-array data
            foreach ($rawValidationData as $groupKey => $groupData) {
                if (!is_array($groupData)) {
                    throw new \Exception("Group '$groupKey' berisi data dengan tipe '" . gettype($groupData) . "', seharusnya array");
                }
            }

            // Log raw data structure for debugging
            Log::info('Raw validation data structure', [
                'usulan_id' => $usulan->id,
                'raw_data_keys' => array_keys($rawValidationData),
                'raw_data_types' => array_map(function($item) { return gettype($item); }, $rawValidationData),
                'sample_data' => array_slice($rawValidationData, 0, 2, true), // First 2 items for debugging
                'raw_data_full' => $rawValidationData // Full data for debugging
            ]);

            // Deep log each group and field for debugging
            foreach ($rawValidationData as $groupKey => $groupData) {
                Log::info("Group: $groupKey", [
                    'group_data_type' => gettype($groupData),
                    'group_data' => $groupData
                ]);

                if (is_array($groupData)) {
                    foreach ($groupData as $fieldKey => $fieldData) {
                        Log::info("Field: $groupKey.$fieldKey", [
                            'field_data_type' => gettype($fieldData),
                            'field_data' => $fieldData,
                            'has_status' => is_array($fieldData) && isset($fieldData['status'])
                        ]);
                    }
                }
            }

            // Process and restructure validation data
            $validationData = [];

            // Process each group (data_pribadi, data_kepegawaian, etc.)
            foreach ($rawValidationData as $groupKey => $groupData) {
                // Skip if groupData is not an array or is empty
                if (!is_array($groupData) || empty($groupData)) {
                    Log::warning('Skipping invalid group data', [
                        'group_key' => $groupKey,
                        'group_data' => $groupData,
                        'group_data_type' => gettype($groupData)
                    ]);
                    continue;
                }

                $validationData[$groupKey] = [];

                // Process each field in the group
                foreach ($groupData as $fieldKey => $fieldData) {
                    // Skip if fieldData is not an array or doesn't have status
                    if (!is_array($fieldData) || !isset($fieldData['status'])) {
                        Log::warning('Skipping invalid field data', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'field_data' => $fieldData,
                            'field_data_type' => gettype($fieldData),
                            'field_data_value' => $fieldData
                        ]);
                        continue;
                    }

                    // Additional validation: ensure status is string and keterangan is string
                    $status = $fieldData['status'] ?? 'sesuai';
                    $keterangan = $fieldData['keterangan'] ?? '';

                    // Convert to string if needed
                    if (!is_string($status)) {
                        $status = (string) $status;
                        Log::warning('Status converted to string', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'original_status' => $fieldData['status'],
                            'converted_status' => $status
                        ]);
                    }

                    if (!is_string($keterangan)) {
                        $keterangan = (string) $keterangan;
                        Log::warning('Keterangan converted to string', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'original_keterangan' => $fieldData['keterangan'],
                            'converted_keterangan' => $keterangan
                        ]);
                    }

                    $validationData[$groupKey][$fieldKey] = [
                        'status' => $status,
                        'keterangan' => $keterangan
                    ];
                }
            }

            // Validate that we have processed data
            if (empty($validationData)) {
                throw new \Exception('Tidak ada data validasi yang valid untuk diproses');
            }

            // Log processed data for debugging
            Log::info('Processed validation data for kepangkatan', [
                'usulan_id' => $usulan->id,
                'raw_data' => $rawValidationData,
                'raw_data_types' => array_map(function($item) { return gettype($item); }, $rawValidationData),
                'processed_data' => $validationData,
                'keterangan_umum' => $keteranganUmum
            ]);

            // Save validation data (without keterangan_umum in the main validation array)
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id());

            // Save keterangan umum separately by updating the validation structure directly
            if (!empty($keteranganUmum)) {
                $currentValidasi = $usulan->validasi_data ?? [];
                if (!isset($currentValidasi['kepegawaian_universitas'])) {
                    $currentValidasi['kepegawaian_universitas'] = [
                        'validation' => [],
                        'validated_by' => Auth::id(),
                        'validated_at' => now()->toISOString()
                    ];
                }
                $currentValidasi['kepegawaian_universitas']['keterangan_umum'] = $keteranganUmum;
                $usulan->validasi_data = $currentValidasi;

                // Debug logging untuk keterangan umum
                Log::info('Keterangan umum saved for kepangkatan', [
                    'usulan_id' => $usulan->id,
                    'keterangan_umum' => $keteranganUmum,
                    'current_validasi_structure' => $currentValidasi['kepegawaian_universitas']
                ]);
            } else {
                Log::info('No keterangan umum to save for kepangkatan', [
                    'usulan_id' => $usulan->id,
                    'keterangan_umum' => $keteranganUmum
                ]);
            }

            // Save usulan to persist keterangan_umum changes
            $usulan->save();

            // Clear caches
            $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
            Cache::forget($cacheKey);

            return redirect()->route('backend.kepegawaian-universitas.usulan.index')
                ->with('success', 'Simpan Validasi Berhasil! Data validasi usulan kepangkatan telah disimpan.');

        } catch (\Exception $e) {
            Log::error('Failed to save kepangkatan validation', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan validasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show validation form untuk usulan NUPTK
     */
    public function showNuptkValidation(Usulan $usulan)
    {
        // Load relationships yang diperlukan
        $usulan = $usulan->load([
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'periodeUsulan'
        ]);

        // Cek apakah usulan adalah jenis NUPTK
        if ($usulan->jenis_usulan !== 'usulan-nuptk') {
            return redirect()->back()->with('error', 'Halaman ini hanya untuk usulan NUPTK.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];

        // Get current role (hardcoded untuk kepegawaian universitas)
        $currentRole = 'Kepegawaian Universitas';

        // Field groups configuration untuk usulan NUPTK
        $fieldGroups = [
            'data_pribadi' => [
                'label' => 'Data Pribadi',
                'icon' => 'user',
                'fields' => [
                    'email' => 'Email',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'jenis_kelamin' => 'Jenis Kelamin',
                    'nomor_handphone' => 'Nomor Handphone',
                    'nik' => 'NIK',
                    'nama_ibu_kandung' => 'Nama Ibu Kandung',
                    'status_kawin' => 'Status Kawin',
                    'agama' => 'Agama',
                    'alamat_lengkap' => 'Alamat Lengkap'
                ]
            ],
            'data_pendidikan' => [
                'label' => 'Data Pendidikan',
                'icon' => 'graduation-cap',
                'fields' => [
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                    'nama_prodi_jurusan' => 'Program Studi/Jurusan'
                ]
            ],
            'dokumen_profil' => [
                'label' => 'Dokumen Profil',
                'icon' => 'folder',
                'fields' => [
                    'ktp' => 'KTP',
                    'kartu_keluarga' => 'Kartu Keluarga',
                    'sk_cpns' => 'SK CPNS',
                    'sk_pns' => 'SK PNS',
                    'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                    'sk_jabatan_terakhir' => 'SK Jabatan Terakhir'
                ]
            ],
            'dokumen_usulan' => [
                'label' => 'Dokumen Usulan',
                'icon' => 'file-text',
                'fields' => []
            ]
        ];

        // Add dynamic fields based on jenis NUPTK
        $jenisNuptk = $usulan->jenis_nuptk ?? '';

        if ($jenisNuptk === 'dosen_tetap') {
            $fieldGroups['dokumen_usulan']['fields']['surat_keterangan_sehat'] = 'Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika';
            $fieldGroups['dokumen_usulan']['fields']['surat_pernyataan_pimpinan'] = 'Surat Pernyataan dari Pimpinan PTN';
            $fieldGroups['dokumen_usulan']['fields']['surat_pernyataan_dosen_tetap'] = 'Surat Pernyataan Dosen Tetap';
            $fieldGroups['dokumen_usulan']['fields']['surat_keterangan_aktif_tridharma'] = 'Surat Keterangan Aktif Melaksanakan Tridharma';
        } elseif ($jenisNuptk === 'dosen_tidak_tetap') {
            $fieldGroups['dokumen_usulan']['fields']['surat_keterangan_sehat'] = 'Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika';
            $fieldGroups['dokumen_usulan']['fields']['surat_pernyataan_pimpinan'] = 'Surat Pernyataan dari Pimpinan PTN';
            $fieldGroups['dokumen_usulan']['fields']['surat_izin_instansi_induk'] = 'Surat Izin dari Instansi Induk';
            $fieldGroups['dokumen_usulan']['fields']['surat_perjanjian_kerja'] = 'Surat Perjanjian Kerja';
            $fieldGroups['dokumen_usulan']['fields']['sk_tenaga_pengajar'] = 'SK Tenaga Pengajar';
        } elseif ($jenisNuptk === 'pengajar_non_dosen') {
            $fieldGroups['dokumen_usulan']['fields']['surat_keterangan_sehat'] = 'Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika';
            $fieldGroups['dokumen_usulan']['fields']['surat_pernyataan_pimpinan'] = 'Surat Pernyataan dari Pimpinan PTN';
            $fieldGroups['dokumen_usulan']['fields']['surat_izin_instansi_induk'] = 'Surat Izin dari Instansi Induk';
            $fieldGroups['dokumen_usulan']['fields']['surat_perjanjian_kerja'] = 'Surat Perjanjian Kerja';
            $fieldGroups['dokumen_usulan']['fields']['sk_tenaga_pengajar'] = 'SK Tenaga Pengajar';
        } elseif ($jenisNuptk === 'jabatan_fungsional_tertentu') {
            $fieldGroups['dokumen_usulan']['fields']['nota_dinas'] = 'Nota Dinas';
        }

        // Get validation configuration
        $config = [
            'validationFields' => array_keys($fieldGroups)
        ];

        // Check if usulan is in view-only status
        $viewOnlyStatuses = [
            Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_TIM_SISTER,
            Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER,
            Usulan::STATUS_DIREKOMENDASIKAN_SISTER
        ];

        $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);

        return view('backend.layouts.views.shared.usul-nuptk.validasi-nuptk', compact(
            'usulan',
            'existingValidation',
            'fieldGroups',
            'config',
            'currentRole',
            'isViewOnly'
        ));
    }

    /**
     * Save validasi field-by-field untuk usulan NUPTK
     */
    public function saveNuptkValidation(Request $request, Usulan $usulan)
    {
        try {
            // Validate request
            $request->validate([
                'validation' => 'required|array',
                'keterangan_umum' => 'nullable|string|max:1000'
            ]);

            // Get validation data
            $rawValidationData = $request->input('validation');
            $keteranganUmum = $request->input('keterangan_umum', '');

            // Debug logging untuk keterangan umum
            Log::info('Keterangan umum received in saveNuptkValidation', [
                'usulan_id' => $usulan->id,
                'keterangan_umum_raw' => $keteranganUmum,
                'keterangan_umum_type' => gettype($keteranganUmum),
                'keterangan_umum_empty' => empty($keteranganUmum),
                'keterangan_umum_null' => is_null($keteranganUmum),
                'request_all' => $request->all()
            ]);

            // Validate that rawValidationData is not null and is an array
            if (empty($rawValidationData) || !is_array($rawValidationData)) {
                throw new \Exception('Data validasi tidak valid atau kosong');
            }

            // Additional validation: check if any group contains non-array data
            foreach ($rawValidationData as $groupKey => $groupData) {
                if (!is_array($groupData)) {
                    throw new \Exception("Group '$groupKey' berisi data dengan tipe '" . gettype($groupData) . "', seharusnya array");
                }
            }

            // Log raw data structure for debugging
            Log::info('Raw validation data structure', [
                'usulan_id' => $usulan->id,
                'raw_data_keys' => array_keys($rawValidationData),
                'raw_data_types' => array_map(function($item) { return gettype($item); }, $rawValidationData),
                'sample_data' => array_slice($rawValidationData, 0, 2, true), // First 2 items for debugging
                'raw_data_full' => $rawValidationData // Full data for debugging
            ]);

            // Deep log each group and field for debugging
            foreach ($rawValidationData as $groupKey => $groupData) {
                Log::info("Group: $groupKey", [
                    'group_data_type' => gettype($groupData),
                    'group_data' => $groupData
                ]);

                if (is_array($groupData)) {
                    foreach ($groupData as $fieldKey => $fieldData) {
                        Log::info("Field: $groupKey.$fieldKey", [
                            'field_data_type' => gettype($fieldData),
                            'field_data' => $fieldData,
                            'has_status' => is_array($fieldData) && isset($fieldData['status'])
                        ]);
                    }
                }
            }

            // Process and restructure validation data
            $validationData = [];

            // Process each group (data_pribadi, data_kepegawaian, etc.)
            foreach ($rawValidationData as $groupKey => $groupData) {
                // Skip if groupData is not an array or is empty
                if (!is_array($groupData) || empty($groupData)) {
                    Log::warning('Skipping invalid group data', [
                        'group_key' => $groupKey,
                        'group_data' => $groupData,
                        'group_data_type' => gettype($groupData)
                    ]);
                    continue;
                }

                $validationData[$groupKey] = [];

                // Process each field in the group
                foreach ($groupData as $fieldKey => $fieldData) {
                    // Skip if fieldData is not an array or doesn't have status
                    if (!is_array($fieldData) || !isset($fieldData['status'])) {
                        Log::warning('Skipping invalid field data', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'field_data' => $fieldData,
                            'field_data_type' => gettype($fieldData),
                            'field_data_value' => $fieldData
                        ]);
                        continue;
                    }

                    // Additional validation: ensure status is string and keterangan is string
                    $status = $fieldData['status'] ?? 'sesuai';
                    $keterangan = $fieldData['keterangan'] ?? '';

                    // Validate status value
                    if (!in_array($status, ['sesuai', 'tidak_sesuai', 'belum_ada'])) {
                        Log::warning('Invalid status value', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'status' => $status
                        ]);
                        $status = 'sesuai'; // Default to 'sesuai' if invalid
                    }

                    // Validate keterangan length
                    if (strlen($keterangan) > 500) {
                        $keterangan = substr($keterangan, 0, 500);
                        Log::warning('Keterangan truncated', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'original_length' => strlen($fieldData['keterangan']),
                            'truncated_length' => strlen($keterangan)
                        ]);
                    }

                    $validationData[$groupKey][$fieldKey] = [
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'validated_at' => now()->toISOString(),
                        'validated_by' => Auth::id()
                    ];
                }
            }

            // Save validation data with role 'kepegawaian_universitas'
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id(), $keteranganUmum ?? '');
            $usulan->save();

            // Log the validation
            Log::info('NUPTK validation saved', [
                'usulan_id' => $usulan->id,
                'validated_by' => Auth::id(),
                'keterangan_umum' => $keteranganUmum,
                'validation_data' => $validationData
            ]);

            // Save keterangan umum if provided
            if (!empty($keteranganUmum)) {
                $currentValidasi = $usulan->validasi_data ?? [];

                if (!isset($currentValidasi['kepegawaian_universitas'])) {
                    $currentValidasi['kepegawaian_universitas'] = [];
                }

                $currentValidasi['kepegawaian_universitas']['keterangan_umum'] = $keteranganUmum;
                $usulan->validasi_data = $currentValidasi;

                // Debug logging untuk keterangan umum
                Log::info('Keterangan umum saved for NUPTK', [
                    'usulan_id' => $usulan->id,
                    'keterangan_umum' => $keteranganUmum,
                    'current_validasi_structure' => $currentValidasi['kepegawaian_universitas']
                ]);
            } else {
                Log::info('No keterangan umum to save for NUPTK', [
                    'usulan_id' => $usulan->id,
                    'keterangan_umum' => $keteranganUmum
                ]);
            }

            // Save usulan to persist keterangan_umum changes
            $usulan->save();

            // Clear caches
            $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'message' => 'Data validasi usulan NUPTK telah berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save NUPTK validation', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save BKN validation data
     */
    public function saveBknValidation(Request $request, Usulan $usulan)
    {
        try {
            // Validate request
            $request->validate([
                'validation_data' => 'required|array',
                'keterangan_umum' => 'nullable|string|max:1000'
            ]);

            $validationData = $request->input('validation_data');
            $keteranganUmum = $request->input('keterangan_umum', '');

            // Save validation data with role 'bkn'
            $usulan->setValidasiByRole('bkn', $validationData, Auth::id(), $keteranganUmum);
            $usulan->save();

            // Log the validation
            Log::info('BKN validation saved', [
                'usulan_id' => $usulan->id,
                'validated_by' => Auth::id(),
                'keterangan_umum' => $keteranganUmum,
                'validation_data' => $validationData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Validasi BKN berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save BKN validation', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi BKN: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change usulan status
     */
    public function changeStatus(Request $request, Usulan $usulan)
    {
        try {
            // Validate request
            $request->validate([
                'new_status' => 'required|string',
                'keterangan' => 'nullable|string|max:1000'
            ]);

            $newStatus = $request->input('new_status');
            $keterangan = $request->input('keterangan', 'Status diubah melalui halaman validasi kepangkatan');

            // Validate status transition
            $allowedStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT
            ];

            if (!in_array($newStatus, $allowedStatuses)) {
                throw new \Exception('Status tidak diizinkan untuk transisi ini');
            }

            // Check if current status allows this transition
            $allowedCurrentStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEMENTERIAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER
            ];

            if (!in_array($usulan->status_usulan, $allowedCurrentStatuses)) {
                throw new \Exception('Status saat ini tidak memungkinkan perubahan status ini');
            }

            // Update status
            $oldStatus = $usulan->status_usulan;
            $usulan->status_usulan = $newStatus;
            $usulan->save();

            // Jika status berubah ke STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN,
            // otomatis simpan validasi BKN dengan data default
            if ($newStatus === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) {
                // Data validasi default untuk BKN (field yang bermasalah)
                $defaultBknValidation = [
                    'data_pribadi' => [
                        'nip' => ['status' => 'tidak_sesuai', 'keterangan' => 'NIP tidak valid sesuai data BKN'],
                        'nama_lengkap' => ['status' => 'tidak_sesuai', 'keterangan' => 'Nama tidak sesuai dengan data BKN']
                    ],
                    'data_kepegawaian' => [
                        'pangkat_saat_usul' => ['status' => 'tidak_sesuai', 'keterangan' => 'Pangkat tidak sesuai dengan data BKN'],
                        'jabatan_saat_usul' => ['status' => 'tidak_sesuai', 'keterangan' => 'Jabatan tidak sesuai dengan data BKN']
                    ],
                    'dokumen_profil' => [
                        'sk_pangkat_terakhir' => ['status' => 'tidak_sesuai', 'keterangan' => 'SK Pangkat tidak valid'],
                        'sk_jabatan_terakhir' => ['status' => 'tidak_sesuai', 'keterangan' => 'SK Jabatan tidak valid']
                    ]
                ];

                // Simpan validasi BKN
                $usulan->setValidasiByRole('bkn', $defaultBknValidation, Auth::id(), $keterangan);
                $usulan->save();

                Log::info('BKN validation auto-saved for status change', [
                    'usulan_id' => $usulan->id,
                    'validated_by' => Auth::id(),
                    'keterangan_umum' => $keterangan
                ]);
            }

            // Log the status change
            Log::info('Usulan status changed', [
                'usulan_id' => $usulan->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => Auth::id(),
                'keterangan' => $keterangan
            ]);

            // Create status change log entry
            $usulan->logs()->create([
                'usulan_id' => $usulan->id,
                'status_sebelumnya' => $oldStatus,
                'status_baru' => $newStatus,
                'catatan' => $keterangan,
                'dilakukan_oleh_id' => Auth::id()
            ]);

            // Clear caches
            $cacheKey = "usulan_validation_{$usulan->id}_kepegawaian_universitas";
            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'message' => "Status usulan berhasil dikirim, status berubah Dari '{$oldStatus}' ke '{$newStatus}'",
                'new_status' => $newStatus,
                'old_status' => $oldStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to change usulan status', [
                'usulan_id' => $usulan->id,
                'new_status' => $request->input('new_status'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show validation form untuk usulan Tugas Belajar
     */
    public function showTubelValidation(Usulan $usulan)
    {
        // Load relationships yang diperlukan
        $usulan = $usulan->load([
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'periodeUsulan'
        ]);

        // Cek apakah usulan adalah jenis Tugas Belajar
        if ($usulan->jenis_usulan !== 'usulan-tugas-belajar') {
            return redirect()->back()->with('error', 'Halaman ini hanya untuk usulan Tugas Belajar.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];

        // Get current role (hardcoded untuk kepegawaian universitas)
        $currentRole = 'Kepegawaian Universitas';

        // Field groups configuration untuk usulan Tugas Belajar
        $fieldGroups = [
            'data_pribadi' => [
                'label' => 'Data Pribadi Pegawai',
                'icon' => 'user',
                'fields' => [
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'nomor_handphone' => 'Nomor Handphone',
                    'email' => 'Email',
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'pangkat' => 'Pangkat Terakhir',
                    'tmt_pangkat' => 'TMT Pangkat',
                    'jabatan' => 'Jabatan Terakhir',
                    'tmt_jabatan' => 'TMT Jabatan'
                ]
            ],
            'data_usulan_tugas_belajar' => [
                'label' => 'Data Usulan Tugas Belajar',
                'icon' => 'book-open',
                'fields' => [
                    'tahun_studi' => 'Tahun Studi',
                    'alamat_lengkap' => 'Alamat Lengkap',
                    'pendidikan_ditempuh' => 'Pendidikan yang Ditempuh',
                    'nama_prodi_dituju' => 'Nama Prodi yang Dituju',
                    'nama_fakultas_dituju' => 'Nama Fakultas yang Dituju',
                    'nama_universitas_dituju' => 'Nama Universitas yang Dituju',
                    'negara_studi' => 'Negara Studi'
                ]
            ],
            'dokumen_tugas_belajar' => [
                'label' => 'Dokumen Tugas Belajar',
                'icon' => 'folder',
                'fields' => [
                    'kartu_pegawai' => 'Kartu Pegawai/Kartu Virtual ASN',
                    'dokumen_setneg' => 'Dokumen Setneg (Luar Negeri)'
                ]
            ]
        ];

        // Conditional field groups berdasarkan jenis_tubel
        $jenisTubel = $usulan->data_usulan['jenis_tubel'] ?? null;

        if ($jenisTubel === 'Tugas Belajar') {
            $fieldGroups['dokumen_tubel'] = [
                'label' => 'Dokumen Tugas Belajar',
                'icon' => 'file-text',
                'fields' => [
                    'surat_tunjangan_keluarga' => 'Surat Keterangan Pembayaran Tunjangan Keluarga',
                    'akta_nikah' => 'Akta Nikah/Surat Keterangan Belum Menikah',
                    'surat_rekomendasi_atasan' => 'Surat Rekomendasi dari Atasan Langsung',
                    'surat_perjanjian_tubel' => 'Surat Perjanjian Tugas Belajar',
                    'surat_jaminan_pembiayaan' => 'Surat Jaminan Pembiayaan Tugas Belajar',
                    'surat_keterangan_pimpinan' => 'Surat Keterangan dari Pimpinan Unit Kerja',
                    'surat_hasil_kelulusan' => 'Surat Hasil Kelulusan dari Lembaga Pendidikan (LoA)',
                    'surat_pernyataan_pimpinan' => 'Surat Pernyataan dari Pimpinan Unit Kerja (10 Poin)',
                    'surat_pernyataan_bersangkutan' => 'Asli Surat Pernyataan yang Bersangkutan (3 Poin)',
                    'dokumen_akreditasi' => 'Dokumen Akreditasi Prodi dan PT/Tangkap Layar Daftar PTLN'
                ]
            ];
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $fieldGroups['dokumen_perpanjangan_tubel'] = [
                'label' => 'Dokumen Perpanjangan Tugas Belajar',
                'icon' => 'file-text',
                'fields' => [
                    'surat_perjanjian_perpanjangan' => 'Surat Perjanjian Perpanjangan Pemberian Tugas Belajar',
                    'surat_perpanjangan_jaminan_pembiayaan' => 'Surat Perpanjangan Jaminan Pembiayaan Tugas Belajar',
                    'surat_rekomendasi_lembaga_pendidikan' => 'Surat Rekomendasi Perpanjangan Pemberian Tugas Belajar dari Lembaga Pendidikan',
                    'surat_rekomendasi_pimpinan_unit' => 'Surat Rekomendasi Perpanjangan Tugas Belajar dari Pimpinan Unit Kerja',
                    'sk_tugas_belajar' => 'SK Tugas Belajar'
                ]
            ];
        }

        // Get validation configuration
        $config = [
            'validationFields' => array_keys($fieldGroups)
        ];

        // Check if usulan is in view-only status
        $viewOnlyStatuses = [
            Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN,
            Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN,
            Usulan::STATUS_DIREKOMENDASIKAN_KEMENTERIAN
        ];

        $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);

        return view('backend.layouts.views.shared.usul-tubel.validasi-tubel', compact(
            'usulan',
            'existingValidation',
            'fieldGroups',
            'config',
            'currentRole',
            'isViewOnly'
        ));
    }

    /**
     * Save validasi field-by-field untuk usulan Tugas Belajar
     */
    public function saveTubelValidation(Request $request, Usulan $usulan)
    {
        try {
            // Validate request
            $request->validate([
                'validation' => 'required|array',
                'keterangan_umum' => 'nullable|string|max:1000'
            ]);

            // Get validation data
            $rawValidationData = $request->input('validation');
            $keteranganUmum = $request->input('keterangan_umum', '');

            // Debug logging untuk keterangan umum
            Log::info('Keterangan umum received in saveTubelValidation', [
                'usulan_id' => $usulan->id,
                'keterangan_umum_raw' => $keteranganUmum,
                'keterangan_umum_type' => gettype($keteranganUmum),
                'keterangan_umum_empty' => empty($keteranganUmum),
                'keterangan_umum_null' => is_null($keteranganUmum),
                'request_all' => $request->all()
            ]);

            // Validate that rawValidationData is not null and is an array
            if (empty($rawValidationData) || !is_array($rawValidationData)) {
                throw new \Exception('Data validasi tidak valid atau kosong');
            }

            // Additional validation: check if any group contains non-array data
            foreach ($rawValidationData as $groupKey => $groupData) {
                if (!is_array($groupData)) {
                    throw new \Exception("Group '$groupKey' berisi data dengan tipe '" . gettype($groupData) . "', seharusnya array");
                }
            }

            // Log raw data structure for debugging
            Log::info('Raw validation data structure', [
                'usulan_id' => $usulan->id,
                'raw_validation_data' => $rawValidationData,
                'data_type' => gettype($rawValidationData),
                'data_count' => count($rawValidationData)
            ]);

            // Process validation data - same logic as NUPTK
            $validationData = [];
            foreach ($rawValidationData as $groupKey => $groupData) {
                if (!is_array($groupData)) {
                    Log::warning('Skipping non-array group data', [
                        'group_key' => $groupKey,
                        'data_type' => gettype($groupData),
                        'data_value' => $groupData
                    ]);
                    continue;
                }

                $validationData[$groupKey] = [];
                foreach ($groupData as $fieldKey => $fieldData) {
                    if (!is_array($fieldData)) {
                        Log::warning('Skipping non-array field data', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'data_type' => gettype($fieldData),
                            'data_value' => $fieldData
                        ]);
                        continue;
                    }

                    $status = $fieldData['status'] ?? 'sesuai';
                    $keterangan = $fieldData['keterangan'] ?? '';

                    // Validate status
                    if (!in_array($status, ['sesuai', 'tidak_sesuai'])) {
                        Log::warning('Invalid status value, defaulting to sesuai', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'invalid_status' => $status
                        ]);
                        $status = 'sesuai';
                    }

                    // Validate keterangan length
                    if (strlen($keterangan) > 500) {
                        $keterangan = substr($keterangan, 0, 500);
                        Log::warning('Keterangan truncated', [
                            'group_key' => $groupKey,
                            'field_key' => $fieldKey,
                            'original_length' => strlen($fieldData['keterangan']),
                            'truncated_length' => strlen($keterangan)
                        ]);
                    }

                    $validationData[$groupKey][$fieldKey] = [
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'validated_at' => now()->toISOString(),
                        'validated_by' => Auth::id()
                    ];
                }
            }

            // Save validation data with role 'kepegawaian_universitas'
            $usulan->setValidasiByRole('kepegawaian_universitas', $validationData, Auth::id(), $keteranganUmum ?? '');
            $usulan->save();

            // Log the validation
            Log::info('TUBEL validation saved', [
                'usulan_id' => $usulan->id,
                'validated_by' => Auth::id(),
                'keterangan_umum' => $keteranganUmum,
                'validation_data' => $validationData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Validasi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save TUBEL validation', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi: ' . $e->getMessage()
            ], 500);
        }
    }
}

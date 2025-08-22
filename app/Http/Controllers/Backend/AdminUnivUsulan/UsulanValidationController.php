<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
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
        // Get usulans that are ready for university validation
        $usulans = Usulan::whereIn('status_usulan', [
                'Diusulkan ke Universitas',
                'Menunggu Review Admin Univ', // Status yang sebenarnya ada
                'Perbaikan Usulan', // Include returned usulans
                'Sedang Direview'   // Include those being reviewed (can be returned)
            ])
            ->with([
                'pegawai:id,nama_lengkap,nip,jenis_pegawai,unit_kerja_id,unit_kerja_terakhir_id',
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
            $periode = \App\Models\BackendUnivUsulan\PeriodeUsulan::where('status', 'Buka')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Set default values for view compatibility
        $jenisUsulan = 'jabatan';
        $namaUsulan = 'Usulan Jabatan';

        // Calculate statistics
        $stats = [
            'total_usulan' => $usulans->total(),
            'usulan_disetujui' => $usulans->where('status_usulan', 'Disetujui')->count(),
            'usulan_ditolak' => $usulans->where('status_usulan', 'Ditolak')->count(),
            'usulan_pending' => $usulans->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses', 'Diusulkan ke Universitas'])->count(),
        ];

        return view('backend.layouts.views.admin-univ-usulan.usulan.index', compact(
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
            'periodeUsulan'
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

        // ENHANCED: Check if usulan is in correct status for Admin Universitas
        $allowedStatuses = [
            'Diusulkan ke Universitas',
            'Perbaikan Usulan', 
            'Sedang Direview',
            'Menunggu Review Admin Univ',
            'Menunggu Hasil Penilaian Tim Penilai',    // ← STATUS INTERMEDIATE BARU
            'Perbaikan Dari Tim Penilai',              // ← STATUS BARU
            'Usulan Direkomendasi Tim Penilai'         // ← STATUS BARU
        ];
        
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return redirect()->route('backend.admin-univ-usulan.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai. Status saat ini: ' . $usulan->status_usulan);
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('admin_universitas') ?? [];

        // Determine if Admin Universitas can edit (based on status)
        $canEdit = in_array($usulan->status_usulan, [
            'Diusulkan ke Universitas', 
            'Menunggu Review Admin Univ',
            'Menunggu Hasil Penilaian Tim Penilai',    // ← SUPPORT STATUS INTERMEDIATE
            'Perbaikan Dari Tim Penilai',              // ← SUPPORT STATUS BARU
            'Usulan Direkomendasi Tim Penilai'         // ← SUPPORT STATUS BARU
        ]);

        // Get active penilais for selection
        $penilais = \App\Models\BackendUnivUsulan\Penilai::getActivePenilais();

        // Get action buttons based on status
        $actionButtons = $this->getActionButtonsForStatus($usulan->status_usulan);

        // Get penilai assessment progress information
        $penilaiProgress = $usulan->getPenilaiAssessmentProgress();

        // Get detailed penilai progress data for status penilaian section
        $penilaiProgressData = $this->getPenilaiProgressData($usulan);

        return view('backend.layouts.views.admin-univ-usulan.usulan.detail', compact(
            'usulan', 
            'existingValidation', 
            'canEdit', 
            'penilais',
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

        // Check if usulan is in correct status for the action
        $allowedStatuses = ['Diusulkan ke Universitas'];

        // For return actions, also allow already processed usulans to be returned again
        if (in_array($actionType, ['return_to_pegawai', 'return_to_fakultas', 'forward_to_penilai', 'return_from_penilai'])) {
            $allowedStatuses[] = 'Perbaikan Usulan';
            $allowedStatuses[] = 'Sedang Direview';
        }

        // For penilai review actions, allow usulans waiting for admin review
        if (in_array($actionType, ['approve_perbaikan', 'approve_rekomendasi', 'reject_perbaikan', 'reject_rekomendasi'])) {
            $allowedStatuses[] = 'Menunggu Review Admin Univ';
        }

        // For new action buttons, allow intermediate and final statuses
        if (in_array($actionType, ['perbaikan_ke_pegawai', 'perbaikan_ke_fakultas', 'kirim_perbaikan_ke_penilai', 'kirim_ke_senat', 'tidak_direkomendasikan'])) {
            $allowedStatuses[] = 'Perbaikan Dari Tim Penilai';
            $allowedStatuses[] = 'Usulan Direkomendasi Tim Penilai';
        }

        // For intermediate status actions
        if (in_array($actionType, ['kirim_ke_penilai', 'kembali'])) {
            $allowedStatuses[] = 'Menunggu Hasil Penilaian Tim Penilai';
            $allowedStatuses[] = 'Sedang Direview';
        }

        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat divalidasi karena status tidak sesuai.'
            ], 422);
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
            } elseif ($actionType === 'kirim_perbaikan_ke_penilai') {
                return $this->kirimPerbaikanKePenilai($request, $usulan);
            } elseif ($actionType === 'kirim_ke_senat') {
                return $this->kirimKeSenat($request, $usulan);
            } elseif ($actionType === 'kirim_ke_penilai') {
                return $this->kirimKePenilai($request, $usulan);
            } elseif ($actionType === 'kembali') {
                return $this->kembali($request, $usulan);
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

        // Save validation data using the model method
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
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
        $validationData = $request->input('validation');

        // Save validation data using the model method
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

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
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Pegawai untuk perbaikan.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Penilai.
     */
    private function forwardToPenilai(Request $request, Usulan $usulan)
    {
        // Add detailed logging for debugging like Admin Fakultas
        Log::info('AdminUnivUsulan forwardToPenilai started', [
            'usulan_id' => $usulan->id,
            'request_data' => $request->all(),
            'selected_penilais' => $request->input('selected_penilais'),
            'user_id' => Auth::id()
        ]);

        // Check available penilais for debugging
        $availablePenilais = \App\Models\BackendUnivUsulan\Penilai::all(['id', 'nama_lengkap', 'status_kepegawaian']);
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
        $usulan->status_usulan = 'Sedang Direview';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Assign selected penilais to usulan
        $selectedPenilais = $request->input('selected_penilais');
        $usulan->penilais()->sync($selectedPenilais);

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['forward_to_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'selected_penilais' => $selectedPenilais
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '🎉 Usulan berhasil diteruskan ke Tim Penilai! Status usulan telah berubah menjadi "Sedang Direview". Tim Penilai akan segera memproses usulan ini.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Admin Fakultas untuk perbaikan.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
        $usulan->status_usulan = 'Direkomendasikan';

        // Save validation data with forward note
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['forward_to_senat'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diteruskan ke Tim Senat.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
                $usulan->status_usulan = 'Perbaikan Usulan';
                $catatan = "Admin Universitas menyetujui hasil review Tim Penilai. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['admin_universitas']['review_penilai'] = [
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

                $usulan->status_usulan = 'Direkomendasikan';
                $catatan = "Admin Universitas menyetujui rekomendasi Tim Penilai. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['admin_universitas']['review_penilai'] = [
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
                $usulan->status_usulan = 'Sedang Direview';
                $catatan = "Admin Universitas tidak menyetujui hasil review. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['admin_universitas']['review_penilai'] = [
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
                $usulan->status_usulan = 'Sedang Direview';
                $catatan = "Admin Universitas tidak menyetujui rekomendasi. " . $request->input('catatan_umum');
                $usulan->catatan_verifikator = $catatan;

                // Add admin review data
                $currentValidasi = $usulan->validasi_data;
                $currentValidasi['admin_universitas']['review_penilai'] = [
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
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Handle "Tidak Direkomendasikan" action
     */
    private function handleTidakDirekomendasikan(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status to "Tidak Direkomendasikan"
        $usulan->status_usulan = 'Tidak Direkomendasikan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add rejection data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['tidak_direkomendasikan'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_rejection' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'alasan' => 'Usulan tidak direkomendasikan untuk periode berjalan'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        Log::info('Usulan tidak direkomendasikan', [
            'usulan_id' => $usulan->id,
            'admin_id' => Auth::id(),
            'catatan' => $request->input('catatan_umum'),
            'status' => 'Tidak Direkomendasikan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usulan telah ditandai sebagai tidak direkomendasikan. Usulan tidak dapat diajukan kembali pada periode berjalan.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
        $usulan->status_usulan = 'Diusulkan ke Universitas';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add return information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['return_from_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan dari Tim Penilai ke Admin Universitas.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
            $periode = \App\Models\BackendUnivUsulan\PeriodeUsulan::findOrFail($request->periode_id);

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
                
            case 'Menunggu Hasil Penilaian Tim Penilai':
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
                
            default:
                return [];
        }
    }

    /**
     * Handle "Perbaikan ke Pegawai" action
     */
    private function perbaikanKePegawai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['perbaikan_ke_pegawai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_ke_pegawai'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Pegawai untuk perbaikan.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Handle "Perbaikan ke Fakultas" action
     */
    private function perbaikanKeFakultas(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['perbaikan_ke_fakultas'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'perbaikan_ke_fakultas'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Fakultas untuk perbaikan.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Handle "Kirim Perbaikan ke Penilai Universitas" action
     */
    private function kirimPerbaikanKePenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status back to review
        $usulan->status_usulan = 'Sedang Direview';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['kirim_perbaikan_ke_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_perbaikan_ke_penilai'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim kembali ke Tim Penilai untuk penilaian ulang.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Handle "Kirim Ke Senat" action
     */
    private function kirimKeSenat(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Direkomendasikan';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['kirim_ke_senat'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_ke_senat'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Tim Senat untuk keputusan final.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add action data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['kirim_ke_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_action' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'action' => 'kirim_ke_penilai'
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Instruksi berhasil dikirim ke Tim Penilai.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Kembali ke halaman sebelumnya.',
            'redirect' => route('backend.admin-univ-usulan.usulan.index')
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
            if (in_array($currentStatus, ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai'])) {
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
            return 'Menunggu Hasil Penilaian Tim Penilai';
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
            ['Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai'],
            ['Menunggu Hasil Penilaian Tim Penilai', 'Usulan Direkomendasi Tim Penilai'],
            
            // From final to intermediate status (if penilai data changed)
            ['Perbaikan Dari Tim Penilai', 'Menunggu Hasil Penilaian Tim Penilai'],
            ['Usulan Direkomendasi Tim Penilai', 'Menunggu Hasil Penilaian Tim Penilai'],
            
            // From review to intermediate status
            ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai'],
            
            // From assessment status to initial status (if no penilai)
            ['Sedang Direview', 'Diusulkan ke Universitas'],
            ['Menunggu Hasil Penilaian Tim Penilai', 'Diusulkan ke Universitas']
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
}

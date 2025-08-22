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
        // Include usulans that were returned for revision but can be processed again
        $usulans = Usulan::whereIn('status_usulan', [
                'Diusulkan ke Universitas',
                'Perbaikan Usulan', // Include returned usulans
                'Sedang Direview'   // Include those being reviewed (can be returned)
            ])
            ->with(['pegawai.unitKerja.subUnitKerja.unitKerja', 'periodeUsulan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.admin-univ-usulan.usulan.validation-index', compact('usulans'));
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

        // Check if usulan is in correct status for Admin Universitas
        $allowedStatuses = ['Diusulkan ke Universitas', 'Perbaikan Usulan', 'Sedang Direview'];
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return redirect()->route('backend.admin-univ-usulan.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('admin_universitas') ?? [];

        // Determine if Admin Universitas can edit (based on status)
        $canEdit = in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Perbaikan Usulan', 'Sedang Direview']);

        // Get active penilais for selection
        $penilais = \App\Models\BackendUnivUsulan\Penilai::getActivePenilais();

        return view('backend.layouts.views.admin-univ-usulan.usulan.detail', compact('usulan', 'existingValidation', 'canEdit', 'penilais'));
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
}

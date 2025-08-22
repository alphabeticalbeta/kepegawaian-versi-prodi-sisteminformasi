<?php

namespace App\Http\Controllers\Backend\TimPenilai;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class UsulanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get usulans that are ready for penilai validation
        $usulans = Usulan::where('status_usulan', 'Sedang Direview')
            ->with(['pegawai', 'periodeUsulan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.tim-penilai.usulan.index', compact('usulans'));
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
            'periodeUsulan'
        ]);

        // ENHANCED: Consistency Check for Tim Penilai
        $consistencyCheck = $this->performTimPenilaiConsistencyCheck($usulan);

        // ENHANCED: Check if usulan is in correct status for Tim Penilai
        // Allow both 'Sedang Direview' and 'Menunggu Review Admin Univ' (for review from Admin Univ)
        // Also allow 'Menunggu Hasil Penilaian Tim Penilai' (intermediate status)
        $allowedStatuses = ['Sedang Direview', 'Menunggu Review Admin Univ', 'Menunggu Hasil Penilaian Tim Penilai'];
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return redirect()->route('tim-penilai.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai. Status saat ini: ' . $usulan->status_usulan);
        }

        // ENHANCED: Check if current user is assigned to this usulan
        $currentPenilaiId = Auth::id();
        $isAssigned = $usulan->isAssignedToPenilai($currentPenilaiId);
        
        if (!$isAssigned) {
            // If not assigned, check if this is the original penilai (fallback for backward compatibility)
            $validasiData = $usulan->validasi_data;
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            $originalPenilaiId = $timPenilaiData['penilai_id'] ?? null;
            
            if ($originalPenilaiId != $currentPenilaiId) {
                Log::warning('Tim Penilai access denied - not assigned', [
                    'usulan_id' => $usulan->id,
                    'current_penilai_id' => $currentPenilaiId,
                    'original_penilai_id' => $originalPenilaiId,
                    'status' => $usulan->status_usulan
                ]);
                
                return redirect()->route('tim-penilai.usulan.index')
                    ->with('error', 'Anda tidak memiliki akses untuk usulan ini. Usulan mungkin sudah di-assign ke penilai lain.');
            }
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];

        // Get penilais data for popup
        $penilais = \App\Models\BackendUnivUsulan\Pegawai::whereHas('roles', function($query) {
            $query->where('name', 'Penilai Universitas');
        })->orderBy('nama_lengkap')->get();

        // Log access for debugging
        Log::info('Tim Penilai accessing usulan detail', [
            'usulan_id' => $usulan->id,
            'penilai_id' => $currentPenilaiId,
            'status' => $usulan->status_usulan,
            'is_assigned' => $isAssigned,
            'has_existing_validation' => !empty($existingValidation)
        ]);

        return view('backend.layouts.views.tim-penilai.usulan.detail', compact(
            'usulan', 
            'existingValidation', 
            'penilais',
            'consistencyCheck'
        ));
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {
        // ENHANCED: Check if usulan is in correct status for Tim Penilai
        // Allow both 'Sedang Direview' and 'Menunggu Review Admin Univ'
        // Also allow 'Menunggu Hasil Penilaian Tim Penilai' (intermediate status)
        $allowedStatuses = ['Sedang Direview', 'Menunggu Review Admin Univ', 'Menunggu Hasil Penilaian Tim Penilai'];
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat divalidasi karena status tidak sesuai. Status saat ini: ' . $usulan->status_usulan
            ], 422);
        }

        // ENHANCED: Check if current user is assigned to this usulan
        $currentPenilaiId = Auth::id();
        $isAssigned = $usulan->isAssignedToPenilai($currentPenilaiId);
        
        if (!$isAssigned) {
            // Fallback: Check if this is the original penilai
            $validasiData = $usulan->validasi_data;
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            $originalPenilaiId = $timPenilaiData['penilai_id'] ?? null;
            
            if ($originalPenilaiId != $currentPenilaiId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk memvalidasi usulan ini.'
                ], 403);
            }
        }

        $actionType = $request->input('action_type');

        try {
            if ($actionType === 'autosave') {
                return $this->autosaveValidation($request, $usulan);
            } elseif ($actionType === 'return_to_pegawai') {
                return $this->returnToPegawai($request, $usulan);
            } elseif ($actionType === 'rekomendasikan') {
                return $this->rekomendasikan($request, $usulan);
            } else {
                return $this->saveSimpleValidation($request, $usulan);
            }
        } catch (\Exception $e) {
            Log::error('Tim Penilai validation error', [
                'usulan_id' => $usulan->id,
                'action_type' => $actionType,
                'error' => $e->getMessage()
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
        $usulan->setValidasiByRole('tim_penilai', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_penilai";
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
        $usulan->setValidasiByRole('tim_penilai', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_penilai";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ]);
    }

    /**
     * Return usulan to Admin Univ for review (new flow).
     */
    private function returnToPegawai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // ENHANCED: New flow - send to Admin Univ first for review
        $usulan->status_usulan = 'Menunggu Review Admin Univ';
        $usulan->catatan_perbaikan = $request->input('catatan_umum');

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('tim_penilai', $validationData, Auth::id());

        // Add perbaikan data to validasi_data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['tim_penilai']['perbaikan_usulan'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'penilai_id' => Auth::id(),
            'status' => 'menunggu_admin_univ_review'
        ];
        $usulan->validasi_data = $currentValidasi;
        
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_penilai";
        Cache::forget($cacheKey);

        Log::info('Tim Penilai returned usulan to Admin Univ for review', [
            'usulan_id' => $usulan->id,
            'penilai_id' => Auth::id(),
            'catatan' => $request->input('catatan_umum'),
            'new_status' => 'Menunggu Review Admin Univ'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikirim ke Admin Universitas untuk review.',
            'redirect' => route('tim-penilai.usulan.index')
        ]);
    }

    /**
     * Recommend usulan to Tim Senat.
     */
    private function rekomendasikan(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Direkomendasikan';
        $usulan->data_usulan['rekomendasi_tim_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_rekomendasi' => now()->toDateTimeString(),
            'penilai_id' => Auth::id()
        ];
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('tim_penilai', $validationData, Auth::id());
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_penilai";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '🎉 Usulan berhasil direkomendasikan ke Tim Senat! Status usulan telah berubah menjadi "Direkomendasikan". Tim Senat akan segera memproses usulan ini.',
            'redirect' => route('tim-penilai.usulan.index')
        ]);
    }

    /**
     * Submit individual penilaian
     */
    public function submitPenilaian(Request $request, Usulan $usulan)
    {
        $request->validate([
            'hasil_penilaian' => 'required|in:rekomendasi,perbaikan,tidak_rekomendasi',
            'catatan_penilaian' => 'nullable|string|max:1000'
        ]);

        $penilaiId = Auth::id();
        $hasilPenilaian = $request->input('hasil_penilaian');
        $catatanPenilaian = $request->input('catatan_penilaian');

        // Check if penilai is assigned to this usulan
        $isAssigned = $usulan->penilais()->where('penilai_id', $penilaiId)->exists();
        if (!$isAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak ter-assign untuk usulan ini.'
            ], 403);
        }

        // Check if usulan is in correct status
        $allowedStatuses = ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai'];
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat dinilai karena status tidak sesuai.'
            ], 422);
        }

        try {
            // Update penilaian in pivot table
            $usulan->penilais()->updateExistingPivot($penilaiId, [
                'hasil_penilaian' => $hasilPenilaian,
                'catatan_penilaian' => $catatanPenilaian,
                'tanggal_penilaian' => now(),
                'status_penilaian' => 'Selesai'
            ]);

            // ENHANCED: Auto-update status based on penilai progress
            $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();
            
            // Get current progress information
            $progressInfo = $usulan->getPenilaiAssessmentProgress();
            
            // Add assessment summary to validasi_data
            $currentValidasi = $usulan->validasi_data ?? [];
            $timPenilaiData = $currentValidasi['tim_penilai'] ?? [];
            
            $timPenilaiData['assessment_summary'] = [
                'tanggal_penilaian' => now()->toDateTimeString(),
                'total_penilai' => $progressInfo['total_penilai'],
                'completed_penilai' => $progressInfo['completed_penilai'],
                'remaining_penilai' => $progressInfo['remaining_penilai'],
                'progress_percentage' => $progressInfo['progress_percentage'],
                'hasil_penilaian' => $usulan->penilais->map(function($penilai) {
                    return [
                        'penilai_id' => $penilai->id,
                        'nama_penilai' => $penilai->nama_lengkap ?? 'Nama tidak tersedia',
                        'hasil' => $penilai->pivot->hasil_penilaian ?? null,
                        'catatan' => $penilai->pivot->catatan_penilaian ?? null,
                        'tanggal' => $penilai->pivot->tanggal_penilaian ?? null,
                        'status_penilaian' => $penilai->pivot->status_penilaian ?? 'Belum Selesai'
                    ];
                }),
                'current_status' => $usulan->status_usulan,
                'is_final' => $progressInfo['is_complete'],
                'is_intermediate' => $progressInfo['is_intermediate'],
                'status_was_updated' => $statusWasUpdated
            ];
            
            $currentValidasi['tim_penilai'] = $timPenilaiData;
            $usulan->validasi_data = $currentValidasi;
            $usulan->save();

            // Log assessment completion
            Log::info('Tim Penilai assessment updated', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'current_status' => $usulan->status_usulan,
                'completed_count' => $progressInfo['completed_penilai'],
                'total_count' => $progressInfo['total_penilai'],
                'is_final' => $progressInfo['is_complete'],
                'is_intermediate' => $progressInfo['is_intermediate'],
                'status_was_updated' => $statusWasUpdated
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penilaian berhasil disimpan.',
                'all_completed' => $progressInfo['is_complete'],
                'current_status' => $usulan->status_usulan,
                'completed_count' => $progressInfo['completed_penilai'],
                'total_count' => $progressInfo['total_penilai'],
                'remaining_count' => $progressInfo['remaining_penilai'],
                'progress_percentage' => $progressInfo['progress_percentage'],
                'is_intermediate' => $progressInfo['is_intermediate'],
                'status_was_updated' => $statusWasUpdated
            ]);

        } catch (\Exception $e) {
            Log::error('Tim Penilai assessment error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $penilaiId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan penilaian.'
            ], 500);
        }
    }

    /**
     * Show document.
     */
    public function showDocument($usulanId, $field)
    {
        $usulan = Usulan::findOrFail($usulanId);

        // Get document path based on field
        $docPath = $usulan->getDocumentPath($field);

        if (!$docPath || !file_exists(storage_path('app/' . $docPath))) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $docPath));
    }

    /**
     * Show pegawai document.
     */
    public function showPegawaiDocument($usulanId, $field)
    {
        $usulan = Usulan::with('pegawai')->findOrFail($usulanId);

        // Get pegawai document path
        $docPath = $usulan->pegawai->$field ?? null;

        if (!$docPath || !file_exists(storage_path('app/' . $docPath))) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $docPath));
    }

    /**
     * ENHANCED: Perform consistency check for Tim Penilai
     */
    private function performTimPenilaiConsistencyCheck(Usulan $usulan)
    {
        $issues = [];
        $warnings = [];
        $corrections = [];

        try {
            $currentPenilaiId = Auth::id();
            $penilais = $usulan->penilais ?? collect();
            
            // Check 1: Current user assignment consistency
            $isAssigned = $usulan->isAssignedToPenilai($currentPenilaiId);
            $currentPenilai = $penilais->where('id', $currentPenilaiId)->first();
            
            if (!$isAssigned && $currentPenilai) {
                $issues[] = "Current user assignment inconsistency";
                $corrections[] = "User should be properly assigned to this usulan";
            }

            // Check 2: Assessment data integrity for current user
            if ($currentPenilai && $currentPenilai->pivot) {
                $pivot = $currentPenilai->pivot;
                
                // Check for incomplete assessment data
                if (!empty($pivot->hasil_penilaian)) {
                    if (empty($pivot->tanggal_penilaian)) {
                        $warnings[] = "Assessment date missing for current user";
                        
                        // Auto-correct: Set assessment date
                        $usulan->penilais()->updateExistingPivot($currentPenilaiId, [
                            'tanggal_penilaian' => now()
                        ]);
                        $corrections[] = "Added missing assessment date";
                    }
                    
                    if (empty($pivot->status_penilaian)) {
                        $warnings[] = "Assessment status missing for current user";
                        
                        // Auto-correct: Set assessment status
                        $usulan->penilais()->updateExistingPivot($currentPenilaiId, [
                            'status_penilaian' => 'Selesai'
                        ]);
                        $corrections[] = "Added missing assessment status";
                    }
                }
                
                // Check for valid assessment result
                $validResults = ['rekomendasi', 'perbaikan', 'tidak_rekomendasi'];
                if (!empty($pivot->hasil_penilaian) && !in_array($pivot->hasil_penilaian, $validResults)) {
                    $issues[] = "Invalid assessment result: '{$pivot->hasil_penilaian}'";
                }
            }

            // Check 3: Overall assessment progress consistency
            $totalPenilai = $penilais->count();
            $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
            
            // Check if status matches progress
            if ($totalPenilai > 0) {
                if ($completedPenilai === 0 && $usulan->status_usulan !== 'Sedang Direview') {
                    $warnings[] = "Status should be 'Sedang Direview' when no penilai has completed assessment";
                } elseif ($completedPenilai > 0 && $completedPenilai < $totalPenilai && $usulan->status_usulan !== 'Menunggu Hasil Penilaian Tim Penilai') {
                    $warnings[] = "Status should be 'Menunggu Hasil Penilaian Tim Penilai' when some penilai have completed assessment";
                }
            }

            // Check 4: Validasi data consistency for current user
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            
            // Check if current user's assessment is properly recorded in validasi_data
            if (!empty($timPenilaiData['penilai_id']) && $timPenilaiData['penilai_id'] != $currentPenilaiId) {
                $warnings[] = "Validasi data shows different penilai ID than current user";
            }

        } catch (\Exception $e) {
            Log::error('Tim Penilai consistency check error', [
                'usulan_id' => $usulan->id,
                'penilai_id' => $currentPenilaiId ?? null,
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
}

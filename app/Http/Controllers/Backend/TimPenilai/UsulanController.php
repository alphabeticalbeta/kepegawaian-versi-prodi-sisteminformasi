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

        // Check if usulan is in correct status for Tim Penilai
        if ($usulan->status_usulan !== 'Sedang Direview') {
            return redirect()->route('tim-penilai.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];

        // Get penilais data for popup
        $penilais = \App\Models\BackendUnivUsulan\Pegawai::whereHas('roles', function($query) {
            $query->where('name', 'Penilai Universitas');
        })->orderBy('nama_lengkap')->get();

        return view('backend.layouts.views.tim-penilai.usulan.detail', compact('usulan', 'existingValidation', 'penilais'));
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {

        // Check if usulan is in correct status
        if ($usulan->status_usulan !== 'Sedang Direview') {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat divalidasi karena status tidak sesuai.'
            ], 422);
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
     * Return usulan to pegawai for revision.
     */
    private function returnToPegawai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_perbaikan = $request->input('catatan_umum');
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
            'message' => 'Usulan berhasil dikembalikan untuk perbaikan.',
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
}

<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UsulanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get usulans that are ready for university validation
        $usulans = Usulan::where('status_usulan', 'Diusulkan ke Universitas')
            ->with(['pegawai', 'periodeUsulan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.kepegawaian-universitas.usulan.validation-index', compact('usulans'));
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

        // Check if usulan is in correct status for Admin Universitas
        if ($usulan->status_usulan !== 'Diusulkan ke Universitas') {
            return redirect()->route('admin-universitas.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('admin_universitas') ?? [];

        // Determine action permissions based on status
        $canReturn = in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview']);
        $canForward = in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview']);

        return view('backend.layouts.views.kepegawaian-universitas.usulan.detail', [
            'usulan' => $usulan,
            'existingValidation' => $existingValidation,
            'config' => [
                'canReturn' => $canReturn,
                'canForward' => $canForward,
                'routePrefix' => 'admin-universitas',
                'canEdit' => in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview']),
                'canView' => true,
                'submitFunctions' => ['save', 'return_to_pegawai', 'forward_to_penilai']
            ]
        ]);
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {

        // Check if usulan is in correct status
        if ($usulan->status_usulan !== 'Diusulkan ke Universitas') {
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
            } elseif ($actionType === 'forward_to_penilai') {
                return $this->forwardToPenilai($request, $usulan);
            } else {
                return $this->saveSimpleValidation($request, $usulan);
            }
        } catch (\Exception $e) {
            Log::error('Admin Universitas validation error', [
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
        $usulan->setValidasiByRole('admin_universitas', $validationData, auth()->id());
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
        $usulan->setValidasiByRole('admin_universitas', $validationData, auth()->id());
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
        $usulan->catatan_perbaikan = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('admin_universitas', $validationData, auth()->id());
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan untuk perbaikan.',
            'redirect' => route('admin-universitas.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Penilai.
     */
    private function forwardToPenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'nomor_surat_usulan' => 'required|string|max:100',
            'file_surat_usulan' => 'required|file|mimes:pdf|max:1024',
            'nomor_berita_senat' => 'required|string|max:100',
            'file_berita_senat' => 'required|file|mimes:pdf|max:1024',
            'catatan_forward' => 'nullable|string|max:1000'
        ]);

        // Handle file uploads
        $suratPath = $request->file('file_surat_usulan')->store('usulan/dokumen/admin-universitas', 'local');
        $beritaPath = $request->file('file_berita_senat')->store('usulan/dokumen/admin-universitas', 'local');

        // Update usulan status and data
        $usulan->status_usulan = 'Sedang Direview';
        $usulan->data_usulan['dokumen_admin_universitas'] = [
            'nomor_surat_usulan' => $request->input('nomor_surat_usulan'),
            'file_surat_usulan' => $suratPath,
            'nomor_berita_senat' => $request->input('nomor_berita_senat'),
            'file_berita_senat' => $beritaPath,
            'catatan_forward' => $request->input('catatan_forward'),
            'tanggal_forward' => now()->toDateTimeString()
        ];
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('admin_universitas', $validationData, auth()->id());
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diteruskan ke Tim Penilai.',
            'redirect' => route('admin-universitas.usulan.index')
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

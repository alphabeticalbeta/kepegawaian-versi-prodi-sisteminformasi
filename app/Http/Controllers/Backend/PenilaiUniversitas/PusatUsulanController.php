<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use App\Services\PenilaiDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'penilai');

            // ADDED: Get BKD labels for display
            $bkdLabels = $usulan->getBkdDisplayLabels();

            // Get existing validation data if any
            $existingValidation = $usulan->getValidasiByRole('penilai');

            // Determine if can edit based on status
            $canEdit = in_array($usulan->status_usulan, [
                'Diusulkan ke Universitas',
                'Sedang Direview',
            ]);

            return view('backend.layouts.views.penilai-universitas.pusat-usulan.detail-usulan', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
                'bkdLabels' => $bkdLabels,
                'canEdit' => $canEdit,
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
    public function showPendaftar(\App\Models\BackendUnivUsulan\PeriodeUsulan $periode)
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

            // Use service to process validation
            $result = $this->penilaiService->processPenilaiValidation($request, $usulan, $currentPenilai->id);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result);
            }

            if ($result['success']) {
                return redirect()->route('penilai-universitas.pusat-usulan.index')
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Error in PenilaiUniversitas process method: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id ?? 'unknown',
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
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
}

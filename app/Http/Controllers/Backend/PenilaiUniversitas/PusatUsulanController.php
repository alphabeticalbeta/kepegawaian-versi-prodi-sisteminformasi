<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PusatUsulanController extends Controller
{
    public function index(Request $request)
    {
        // OPTIMASI: Gunakan Eloquent relationship instead of raw DB query
        $pegawaiId = Auth::user()->id;

        $query = Usulan::query()
            ->whereHas('penilais', function ($q) use ($pegawaiId) {
                $q->where('penilai_id', $pegawaiId);
            })
            ->with([
                'pegawai:id,nama_lengkap,email',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode'
            ])
            ->latest();

        // OPTIMASI: Gunakan when() untuk conditional filtering
        $query->when($request->get('q'), function ($q, $search) {
            return $q->where(function ($subQuery) use ($search) {
                $subQuery->where('jenis_usulan', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function ($pegawaiQuery) use ($search) {
                      $pegawaiQuery->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        });

        $usulans = $query->paginate(15);

        return view('backend.layouts.penilai-universitas.pusat-usulan.index', compact('usulans'));
    }

    public function show(Usulan $usulan)
    {
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
            'Sedang Direview Universitas',
        ]);

        return view('backend.layouts.penilai-universitas.pusat-usulan.detail-usulan', [
            'usulan' => $usulan,
            'validationFields' => $validationFields,
            'existingValidation' => $existingValidation,
            'bkdLabels' => $bkdLabels,
            'canEdit' => $canEdit,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Support\Facades\Auth;

class PeriodeUsulanController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pegawai')->user();

        $periodes = PeriodeUsulan::withCount('usulans')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.layouts.views.admin-universitas.periode-usulan.index', compact('user', 'periodes'));
    }

    public function create()
    {
        return view('backend.layouts.views.admin-universitas.periode-usulan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'jenis_usulan' => 'required|string|max:255',
            'tahun_periode' => 'required|integer|min:2020|max:2050',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_mulai_perbaikan' => 'nullable|date|after:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after:tanggal_mulai_perbaikan',
            'senat_min_setuju' => 'nullable|integer|min:1|max:100',
            'status' => 'required|in:Buka,Tutup'
        ]);

        PeriodeUsulan::create($request->all());

        return redirect()->route('admin-universitas.periode-usulan.index')
            ->with('success', 'Periode usulan berhasil dibuat.');
    }

    public function show(PeriodeUsulan $periode)
    {
        $periode->loadCount('usulans');

        // Statistics for this period
        $stats = [
            'total_usulan' => $periode->usulans()->count(),
            'usulan_disetujui' => $periode->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
            'usulan_ditolak' => $periode->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_pending' => $periode->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
        ];

        return view('backend.layouts.views.admin-universitas.periode-usulan.show', compact('periode', 'stats'));
    }

    public function edit(PeriodeUsulan $periode)
    {
        return view('backend.layouts.views.admin-universitas.periode-usulan.edit', compact('periode'));
    }

    public function update(Request $request, PeriodeUsulan $periode)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'jenis_usulan' => 'required|string|max:255',
            'tahun_periode' => 'required|integer|min:2020|max:2050',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_mulai_perbaikan' => 'nullable|date|after:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after:tanggal_mulai_perbaikan',
            'senat_min_setuju' => 'nullable|integer|min:1|max:100',
            'status' => 'required|in:Buka,Tutup'
        ]);

        $periode->update($request->all());

        return redirect()->route('admin-universitas.periode-usulan.index')
            ->with('success', 'Periode usulan berhasil diperbarui.');
    }

    public function destroy(PeriodeUsulan $periode)
    {
        // Check if periode has any usulan
        if ($periode->usulans()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Periode tidak dapat dihapus karena masih memiliki usulan.');
        }

        $periode->delete();

        return redirect()->route('admin-universitas.periode-usulan.index')
            ->with('success', 'Periode usulan berhasil dihapus.');
    }
}

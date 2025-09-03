<?php

namespace App\Http\Controllers\Backend\TimPenilai;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get statistics for Tim Penilai dashboard
        $totalUsulan = Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS)->count();
        $usulanDirekomendasikan = Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count();
        $usulanDitolak = Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count();

        return view('backend.layouts.views.tim-penilai.dashboard', compact(
            'totalUsulan',
            'usulanDirekomendasikan',
            'usulanDitolak'
        ));
    }
}

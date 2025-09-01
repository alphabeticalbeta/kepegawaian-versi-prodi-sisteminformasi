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
        $totalUsulan = Usulan::where('status_usulan', 'Sedang Direview')->count();
        $usulanDirekomendasikan = Usulan::where('status_usulan', 'Direkomendasikan')->count();
        $usulanDitolak = Usulan::where('status_usulan', 'Ditolak')->count();

        return view('backend.layouts.views.tim-penilai.dashboard', compact(
            'totalUsulan',
            'usulanDirekomendasikan',
            'usulanDitolak'
        ));
    }
}

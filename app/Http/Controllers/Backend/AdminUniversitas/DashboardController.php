<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the admin universitas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get active periods for different usulan types
            $activePeriods = \App\Models\BackendUnivUsulan\PeriodeUsulan::where('status', 'Buka')
                ->with(['usulans' => function($query) {
                    $query->with('pegawai:id,nama_lengkap,nip')
                          ->latest()
                          ->limit(5);
                }])
                ->get();

            // Get recent usulans for validation (status: Diusulkan ke Universitas)
            $recentUsulans = \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diusulkan ke Universitas')
                ->with(['pegawai:id,nama_lengkap,nip', 'periodeUsulan'])
                ->latest()
                ->limit(10)
                ->get();

            // Get statistics
            $stats = [
                'total_periods' => $activePeriods->count(),
                'total_usulans_pending' => $recentUsulans->count(),
                'total_usulans_all' => \App\Models\BackendUnivUsulan\Usulan::count(),
                'usulans_by_status' => [
                    'Diajukan' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diajukan')->count(),
                    'Diusulkan ke Universitas' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diusulkan ke Universitas')->count(),
                    'Sedang Direview' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Sedang Direview')->count(),
                    'Direkomendasikan' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Direkomendasikan')->count(),
                    'Disetujui' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Disetujui')->count(),
                    'Ditolak' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Ditolak')->count(),
                ]
            ];

            return view('backend.layouts.views.admin-univ-usulan.dashboard', [
                'activePeriods' => $activePeriods,
                'recentUsulans' => $recentUsulans,
                'stats' => $stats,
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('AdminUniversitas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.admin-univ-usulan.dashboard', [
                'activePeriods' => collect(),
                'recentUsulans' => collect(),
                'stats' => [],
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ]);
        }
    }


}

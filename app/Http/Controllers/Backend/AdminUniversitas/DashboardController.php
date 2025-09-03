<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;

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
            $activePeriods = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('status', 'Buka')
                ->with(['usulans' => function($query) {
                    $query->with('pegawai:id,nama_lengkap,nip')
                          ->latest()
                          ->limit(5);
                }])
                ->get();

            // Get recent usulans for validation (status: Diusulkan ke Universitas)
            $recentUsulans = \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', 'Diusulkan ke Universitas')
                ->with(['pegawai:id,nama_lengkap,nip', 'periodeUsulan'])
                ->latest()
                ->limit(10)
                ->get();

            // Get statistics
            $stats = [
                'total_periods' => $activePeriods->count(),
                'total_usulans_pending' => $recentUsulans->count(),
                'total_usulans_all' => \App\Models\KepegawaianUniversitas\Usulan::count(),
                'usulans_by_status' => [
                    'Diajukan' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
                    'Diusulkan ke Universitas' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS)->count(),
                    'Sedang Direview' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS)->count(),
                    'Direkomendasikan' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                    'Disetujui' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                    'Ditolak' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
                ]
            ];

            return view('backend.layouts.views.kepegawaian-universitas.dashboard', [
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
            return view('backend.layouts.views.kepegawaian-universitas.dashboard', [
                'activePeriods' => collect(),
                'recentUsulans' => collect(),
                'stats' => [],
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ]);
        }
    }


}

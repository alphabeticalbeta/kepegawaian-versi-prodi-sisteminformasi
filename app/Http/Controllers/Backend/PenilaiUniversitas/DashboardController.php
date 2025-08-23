<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use App\Services\PenilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;

class DashboardController extends Controller
{
    protected $penilaiService;

    public function __construct(PenilaiService $penilaiService)
    {
        $this->penilaiService = $penilaiService;
    }

    /**
     * Display the penilai universitas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            \Log::info('PenilaiUniversitas Dashboard accessed', ['user_id' => Auth::id()]);

            $currentPenilai = Auth::user();

            // Use service to get dashboard data
            $dashboardData = $this->penilaiService->getDashboardData($currentPenilai->id);

            return view('backend.layouts.views.penilai-universitas.dashboard', [
                'activePeriods' => $dashboardData['activePeriods'],
                'recentUsulans' => $dashboardData['recentUsulans'],
                'stats' => $dashboardData['stats'],
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            // Log the specific error
            \Log::error('PenilaiUniversitas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // If even the minimal version fails, return a basic error page
            return response()->view('backend.layouts.views.penilai-universitas.dashboard', [
                'activePeriods' => collect(),
                'recentUsulans' => collect(),
                'stats' => [],
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ], 500);
        }
    }


}

<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the pegawai dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pegawaiId = Auth::id();

        // Get pegawai data
        $pegawai = Pegawai::findOrFail($pegawaiId);

        return view('backend.layouts.views.pegawai-unmul.dashboard', [
            'pegawai' => $pegawai,
            'user' => Auth::user()
        ]);
    }

    /**
     * Get usulan statistics for the pegawai.
     *
     * @param int $pegawaiId
     * @return array
     */
    private function getUsulanStatistics($pegawaiId)
    {
        return [
            'total_usulan' => Usulan::where('pegawai_id', $pegawaiId)->count(),
            'usulan_pending' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
            'usulan_approved' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
            'usulan_returned' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS)->count(),
            'usulan_draft' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN)->count(),

            // Tambahan statistik untuk status baru
            'usulan_direkomendasikan_kepegawaian_universitas' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_direkomendasikan_bkn' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_sk_terbit' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT)->count(),
            'usulan_direkomendasikan_sister' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER)->count(),
            'usulan_tidak_direkomendasikan_kepegawaian_universitas' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_tidak_direkomendasikan_bkn' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_tidak_direkomendasikan_sister' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_TIM_SISTER)->count(),
        ];
    }

    /**
     * Get recent usulans for the pegawai.
     *
     * @param int $pegawaiId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentUsulans($pegawaiId)
    {
        return Usulan::where('pegawai_id', $pegawaiId)
            ->with(['periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get active periods.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getActivePeriods()
    {
        return PeriodeUsulan::where('status', 'Buka')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->get();
    }

    /**
     * Get chart data for dashboard.
     *
     * @param int $pegawaiId
     * @return array
     */
    private function getChartData($pegawaiId)
    {
        // Monthly usulan submission data for this pegawai
        $monthlyData = Usulan::where('pegawai_id', $pegawaiId)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Status distribution for this pegawai
        $statusData = Usulan::where('pegawai_id', $pegawaiId)
            ->selectRaw('status_usulan, COUNT(*) as count')
            ->groupBy('status_usulan')
            ->pluck('count', 'status_usulan')
            ->toArray();

        return [
            'monthly_submissions' => $monthlyData,
            'status_distribution' => $statusData,
        ];
    }
}

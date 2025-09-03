<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardUsulanController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pegawai')->user();

        // Get all periods with statistics
        $periodes = PeriodeUsulan::withCount([
            'usulans',
            'usulans as usulan_disetujui_count' => function ($query) {
                $query->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS);
            },
            'usulans as usulan_ditolak_count' => function ($query) {
                $query->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS);
            },
            'usulans as usulan_pending_count' => function ($query) {
                $query->whereIn('status_usulan', [
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS
                ]);
            }
        ])->orderBy('created_at', 'desc')->get();

        // Overall statistics
        $overallStats = [
            'total_periodes' => PeriodeUsulan::count(),
            'periodes_aktif' => PeriodeUsulan::where('status', 'Buka')->count(),
            'total_usulan' => Usulan::count(),
            'usulan_bulan_ini' => Usulan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count()
        ];

        return view('backend.layouts.views.admin-universitas.dashboard-usulan.index', compact('user', 'periodes', 'overallStats'));
    }

    public function show(PeriodeUsulan $periode)
    {
        $user = Auth::guard('pegawai')->user();

        // Detailed statistics for this period
        $stats = [
            'total_usulan' => $periode->usulans()->count(),
            'usulan_disetujui' => $periode->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
            'usulan_ditolak' => $periode->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_pending' => $periode->usulans()->whereIn('status_usulan', [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS
            ])->count(),
        ];

        // Usulan by status for chart
        $usulanByStatus = $periode->usulans()
            ->select('status_usulan', DB::raw('count(*) as count'))
            ->groupBy('status_usulan')
            ->get()
            ->pluck('count', 'status_usulan')
            ->toArray();

        // Usulan by jenis pegawai
        $usulanByJenisPegawai = $periode->usulans()
            ->join('pegawais', 'usulans.pegawai_id', '=', 'pegawais.id')
            ->select('pegawais.jenis_pegawai', DB::raw('count(*) as count'))
            ->groupBy('pegawais.jenis_pegawai')
            ->get()
            ->pluck('count', 'jenis_pegawai')
            ->toArray();

        // Recent usulans for this period
        $recentUsulans = $periode->usulans()
            ->with(['pegawai:id,nama_lengkap,nip,jenis_pegawai'])
            ->latest()
            ->take(10)
            ->get();

        // Timeline data - usulan per month
        $timelineData = $periode->usulans()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month', 'year')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'year' => $item->year,
                    'count' => $item->count,
                    'label' => \Carbon\Carbon::create($item->year, $item->month)->format('M Y')
                ];
            });

        return view('backend.layouts.views.admin-universitas.dashboard-usulan.show', compact(
            'user',
            'periode',
            'stats',
            'usulanByStatus',
            'usulanByJenisPegawai',
            'recentUsulans',
            'timelineData'
        ));
    }
}

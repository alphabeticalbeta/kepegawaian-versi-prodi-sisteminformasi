<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the admin fakultas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            \Log::info('AdminFakultas Dashboard accessed', ['user_id' => Auth::guard('pegawai')->id()]);
            $user = Auth::guard('pegawai')->user();

            // Handle case where user is not authenticated (for testing)
            if (!$user) {
                return view('backend.layouts.views.admin-fakultas.dashboard', [
                    'unitKerja' => null,
                    'periodeUsulans' => collect(),
                    'user' => null,
                    'statistics' => [
                        'total_periode' => 0,
                        'total_pengusul' => 0,
                        'total_perbaikan' => 0,
                        'total_usulan' => 0,
                        'unit_kerja_name' => 'Tidak diketahui',
                        'has_pending_review' => false,
                        'has_perbaikan' => false
                    ]
                ]);
            }

            // Check if user has unit kerja data
            if (!$user->unitKerjaPengelola) {
                return view('backend.layouts.views.admin-fakultas.dashboard', [
                    'unitKerja' => null,
                    'periodeUsulans' => collect(),
                    'user' => $user,
                    'error' => 'Unit kerja tidak ditemukan. Periksa pengaturan akun Anda.',
                    'statistics' => [
                        'total_periode' => 0,
                        'total_pengusul' => 0,
                        'total_perbaikan' => 0,
                        'total_usulan' => 0,
                        'unit_kerja_name' => 'Tidak diketahui',
                        'has_pending_review' => false,
                        'has_perbaikan' => false
                    ]
                ]);
            }

            // Get unit kerja data safely
            $unitKerja = $user->unitKerjaPengelola;

            // Get periode usulans with statistics
            $periodeUsulans = $this->getPeriodeUsulansWithStats($user);

            // Calculate dashboard statistics
            $statistics = $this->getDashboardStatistics($periodeUsulans, $unitKerja);

            return view('backend.layouts.views.admin-fakultas.dashboard', [
                'unitKerja' => $unitKerja,
                'periodeUsulans' => $periodeUsulans,
                'user' => $user,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('AdminFakultas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::guard('pegawai')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.admin-fakultas.dashboard', [
                'unitKerja' => null,
                'periodeUsulans' => collect(),
                'user' => Auth::guard('pegawai')->user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.',
                'statistics' => [
                    'total_periode' => 0,
                    'total_pengusul' => 0,
                    'total_perbaikan' => 0,
                    'total_usulan' => 0,
                    'unit_kerja_name' => 'Tidak diketahui',
                    'has_pending_review' => false,
                    'has_perbaikan' => false
                ]
            ]);
        }
    }

    /**
     * Get periode usulans with statistics for faculty.
     *
     * @param \App\Models\KepegawaianUniversitas\Pegawai $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getPeriodeUsulansWithStats($user)
    {
        // Admin fakultas mengelola unit kerja (fakultas)
        $unitKerja = $user->unitKerjaPengelola;

        if (!$unitKerja) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $unitKerjaId = $unitKerja->id;

        $periodeUsulans = PeriodeUsulan::withCount([
            'usulans as jumlah_pengusul' => function ($query) use ($unitKerjaId) {
                $query->whereIn('status_usulan', [
                    Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                    Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS
                ])
                    ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
            },
            'usulans as perbaikan' => function ($query) use ($unitKerjaId) {
                $query->whereIn('status_usulan', [
                    Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
                    Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS
                ])
                    ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
            },
            'usulans as total_usulan' => function ($query) use ($unitKerjaId) {
                $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                    $subQuery->where('id', $unitKerjaId);
                });
            }
        ])->latest()->paginate(10);

        return $periodeUsulans;
    }

    /**
     * Get dashboard statistics
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $periodeUsulans
     * @param \App\Models\KepegawaianUniversitas\UnitKerja|null $unitKerja
     * @return array
     */
    private function getDashboardStatistics($periodeUsulans, $unitKerja)
    {
        $totalPengusul = collect($periodeUsulans->items())->sum('jumlah_pengusul');
        $totalPerbaikan = collect($periodeUsulans->items())->sum('perbaikan');
        $totalUsulan = collect($periodeUsulans->items())->sum('total_usulan');

        return [
            'total_periode' => $periodeUsulans->total(),
            'total_pengusul' => $totalPengusul,
            'total_perbaikan' => $totalPerbaikan,
            'total_usulan' => $totalUsulan,
            'unit_kerja_name' => $unitKerja ? $unitKerja->nama : 'Tidak diketahui',
            'has_pending_review' => $totalPengusul > 0,
            'has_perbaikan' => $totalPerbaikan > 0
        ];
    }
}

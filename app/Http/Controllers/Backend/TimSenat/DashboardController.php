<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\Pegawai;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('pegawai')->user();

            // Handle case where user is not authenticated (for testing)
            if (!$user) {
                return view('backend.layouts.views.tim-senat.dashboard', [
                    'stats' => $this->getDefaultStats(),
                    'recentUsulans' => collect(),
                    'usulans' => collect(),
                    'user' => null
                ]);
            }

            // Statistik senat
            $stats = [
                'total_usulan_dosen' => Usulan::whereHas('pegawai', function($q) {
                    $q->where('jenis_pegawai', 'Dosen');
                })->count(),
                'usulan_pending_review' => Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                'usulan_reviewed' => Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT)->count(),
                'total_dosen' => Pegawai::where('jenis_pegawai', 'Dosen')->count(),
            ];

            // Get all usulans for Tim Senat dashboard with all required relationships
            $usulans = Usulan::with([
                'pegawai:id,nama_lengkap,nip,unit_kerja_id,sub_sub_unit_kerja_id',
                'pegawai.unitKerja:id,nama',
                'pegawai.subSubUnitKerja:id,nama',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai',
                'penilais:id,nama_lengkap,nip'
            ])
            ->whereIn('status_usulan', [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER
            ])
            ->latest()
            ->get();

            // Recent activities
            $recentUsulans = Usulan::with(['pegawai:id,nama_lengkap,nip', 'jabatanTujuan'])
                ->whereHas('pegawai', function($q) {
                    $q->where('jenis_pegawai', 'Dosen');
                })
                ->latest()
                ->take(10)
                ->get();

            return view('backend.layouts.views.tim-senat.dashboard', [
                'stats' => $stats,
                'recentUsulans' => $recentUsulans,
                'usulans' => $usulans,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('TimSenat Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.tim-senat.dashboard', [
                'stats' => $this->getDefaultStats(),
                'recentUsulans' => collect(),
                'usulans' => collect(),
                'user' => Auth::guard('pegawai')->user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get default statistics when database is not available.
     *
     * @return array
     */
    private function getDefaultStats()
    {
        return [
            'total_usulan_dosen' => 0,
            'usulan_pending_review' => 0,
            'usulan_reviewed' => 0,
            'total_dosen' => 0,
        ];
    }
}

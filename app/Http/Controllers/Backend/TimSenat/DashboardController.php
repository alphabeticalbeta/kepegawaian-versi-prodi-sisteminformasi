<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

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
                    'user' => null
                ]);
            }

            // Statistik senat
            $stats = [
                'total_usulan_dosen' => Usulan::whereHas('pegawai', function($q) {
                    $q->where('jenis_pegawai', 'Dosen');
                })->count(),
                'usulan_pending_review' => Usulan::where('status_usulan', 'Menunggu Review Senat')->count(),
                'usulan_reviewed' => Usulan::where('status_usulan', 'Sudah Direview Senat')->count(),
                'total_dosen' => Pegawai::where('jenis_pegawai', 'Dosen')->count(),
            ];

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

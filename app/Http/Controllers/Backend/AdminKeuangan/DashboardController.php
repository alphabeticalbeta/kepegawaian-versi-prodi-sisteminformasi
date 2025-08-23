<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\Usulan;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('pegawai')->user();

            // Handle case where user is not authenticated (for testing)
            if (!$user) {
                return view('backend.layouts.views.admin-keuangan.dashboard', [
                    'stats' => $this->getDefaultStats(),
                    'recentUsulans' => collect(),
                    'user' => null
                ]);
            }

            // Statistik keuangan
            $stats = [
                'total_usulan' => Usulan::count(),
                'usulan_pending' => Usulan::where('status_usulan', 'Menunggu Verifikasi')->count(),
                'usulan_approved' => Usulan::where('status_usulan', 'Disetujui')->count(),
                'usulan_rejected' => Usulan::where('status_usulan', 'Ditolak')->count(),
            ];

            // Recent activities
            $recentUsulans = Usulan::with(['pegawai:id,nama_lengkap,nip'])
                ->latest()
                ->take(10)
                ->get();

            return view('backend.layouts.views.admin-keuangan.dashboard', [
                'stats' => $stats,
                'recentUsulans' => $recentUsulans,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('AdminKeuangan Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.admin-keuangan.dashboard', [
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
            'total_usulan' => 0,
            'usulan_pending' => 0,
            'usulan_approved' => 0,
            'usulan_rejected' => 0,
        ];
    }
}

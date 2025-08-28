<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class DashboardController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Display the admin univ usulan dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            \Log::info('Kepegawaian Universitas Dashboard accessed successfully', ['user_id' => Auth::id()]);

            // Get active periods for different usulan types
            $activePeriods = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('status', 'Buka')
                ->with(['usulans' => function($query) {
                    $query->with('pegawai:id,nama_lengkap,nip')
                          ->latest()
                          ->limit(5);
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get recent usulans for validation (status: Diusulkan ke Universitas)
            $recentUsulans = \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS)
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
                    'Diajukan' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN)->count(),
                    'Diusulkan ke Universitas' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS)->count(),
                    'Sedang Direview' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS)->count(),
                    'Direkomendasikan' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN)->count(),
                    'Disetujui' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN)->count(),
                    'Ditolak' => \App\Models\KepegawaianUniversitas\Usulan::where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN)->count(),
                ]
            ];

            // Get periode aktif dari session (jika ada)
            $periodeAktif = null;
            if (session('periode_aktif_id')) {
                $periodeAktif = \App\Models\KepegawaianUniversitas\PeriodeUsulan::find(session('periode_aktif_id'));
            }

            return view('backend.layouts.views.kepegawaian-universitas.dashboard', [
                'activePeriods' => $activePeriods,
                'recentUsulans' => $recentUsulans,
                'stats' => $stats,
                'periodeAktif' => $periodeAktif,
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            // Log the specific error
            \Log::error('Kepegawaian Universitas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // If even the minimal version fails, return a basic error page
            return response()->view('backend.layouts.views.kepegawaian-universitas.dashboard', [
                'activePeriods' => collect(),
                'recentUsulans' => collect(),
                'stats' => [],
                'periodeAktif' => null,
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Mendapatkan histori periode untuk sidebar
     */
    public function getHistoriPeriode(Request $request)
    {
        $jenisUsulan = $request->get('jenis');
        
        // Mapping jenis usulan dari sidebar ke nama periode
        $jenisMapping = [
            'nuptk' => 'Usulan NUPTK',
            'laporan-lkd' => 'Usulan Laporan LKD',
            'presensi' => 'Usulan Presensi',
            'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
            'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah',
            'jabatan-dosen-regular' => 'Usulan Jabatan Dosen Reguler',
            'jabatan-dosen-pengangkatan' => 'Usulan Jabatan Dosen Pengangkatan Pertama',
            'laporan-serdos' => 'Usulan Laporan Serdos',
            'pensiun' => 'Usulan Pensiun',
            'kepangkatan' => 'Usulan Kepangkatan',
            'pencantuman-gelar' => 'Usulan Pencantuman Gelar',
            'id-sinta-sister' => 'Usulan ID SINTA ke SISTER',
            'satyalancana' => 'Usulan Satyalancana',
            'tugas-belajar' => 'Usulan Tugas Belajar',
            'pengaktifan-kembali' => 'Usulan Pengaktifan Kembali'
        ];

        $namaUsulan = $jenisMapping[$jenisUsulan] ?? null;

        if (!$namaUsulan) {
            return response()->json(['error' => 'Jenis usulan tidak valid'], 400);
        }

        // Ambil semua periode untuk jenis usulan ini (histori)
        $periodes = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('jenis_usulan', $namaUsulan)
            ->withCount('usulans')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'nama_periode', 'tahun_periode', 'status', 'tanggal_mulai', 'tanggal_selesai']);

        return response()->json([
            'success' => true,
            'data' => $periodes
        ]);
    }
}

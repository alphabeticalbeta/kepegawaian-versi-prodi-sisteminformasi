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

            // Menggunakan logika dari DashboardPeriodeController dengan jenis 'all'
            $jenisUsulan = 'all';

            // Mapping jenis usulan dari sidebar ke nama periode
            $jenisMapping = [
                'all' => 'Semua Usulan Aktif',
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

            $namaUsulan = $jenisMapping[$jenisUsulan] ?? 'Usulan Jabatan';

            // Ambil semua periode untuk jenis usulan ini (histori)
            $periodes = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where(function($query) use ($namaUsulan, $jenisUsulan) {
                    // Jika jenis usulan adalah 'all', ambil semua periode aktif
                    if ($jenisUsulan === 'all') {
                        $query->where('status', 'Buka');
                    } else {
                        // Exact match untuk jenis usulan utama
                        $query->where('jenis_usulan', $namaUsulan);
                    }
                })
                ->withCount([
                    'usulans',
                    'usulans as usulan_disetujui_count' => function ($query) {
                        $query->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN);
                    },
                    'usulans as usulan_ditolak_count' => function ($query) {
                        $query->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN);
                    },
                    'usulans as usulan_pending_count' => function ($query) {
                        $query->whereIn('status_usulan', [
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS
                        ]);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Overall statistics untuk jenis usulan ini
            $overallStats = [
                'total_periodes' => $periodes->count(),
                'periodes_aktif' => $periodes->where('status', 'Buka')->count(),
                'total_usulan' => $periodes->sum('usulans_count'),
                'usulan_disetujui' => $periodes->sum('usulan_disetujui_count'),
                'usulan_ditolak' => $periodes->sum('usulan_ditolak_count'),
                'usulan_pending' => $periodes->sum('usulan_pending_count'),
            ];

            return view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.index', [
                'periodes' => $periodes,
                'jenisUsulan' => $jenisUsulan,
                'namaUsulan' => $namaUsulan,
                'overallStats' => $overallStats,
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            // Log the specific error
            \Log::error('Kepegawaian Universitas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // If even the minimal version fails, return a basic error page
            return response()->view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.index', [
                'periodes' => collect(),
                'jenisUsulan' => 'all',
                'namaUsulan' => 'Semua Usulan Aktif',
                'overallStats' => [
                    'total_periodes' => 0,
                    'periodes_aktif' => 0,
                    'total_usulan' => 0,
                    'usulan_disetujui' => 0,
                    'usulan_ditolak' => 0,
                    'usulan_pending' => 0,
                ],
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

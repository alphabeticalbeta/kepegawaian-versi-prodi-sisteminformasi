<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class DashboardPeriodeController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Menampilkan dashboard untuk jenis usulan tertentu
     */
    public function index(Request $request)
    {
        $jenisUsulan = $request->get('jenis', 'jabatan');

        // Mapping jenis usulan dari sidebar ke nama periode
        $jenisMapping = [
            'nuptk' => 'Usulan NUPTK',
            'laporan-lkd' => 'Usulan Laporan LKD',
            'presensi' => 'Usulan Presensi',
            'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
            'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah',
            'jabatan' => 'Usulan Jabatan',
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

        // Debug: Log untuk melihat mapping yang digunakan
        \Log::info('DashboardPeriodeController - Mapping Debug', [
            'jenisUsulan' => $jenisUsulan,
            'namaUsulan' => $namaUsulan,
            'mapping' => $jenisMapping
        ]);

        // Ambil semua periode untuk jenis usulan ini (termasuk sub-jenis)
        $periodes = PeriodeUsulan::where(function($query) use ($namaUsulan, $jenisUsulan) {
                // Exact match untuk jenis usulan utama
                $query->where('jenis_usulan', $namaUsulan);

                // Jika jenis usulan adalah jabatan, juga ambil sub-jenis
                if ($jenisUsulan === 'jabatan') {
                    $query->orWhereIn('jenis_usulan', ['usulan-jabatan-dosen', 'usulan-jabatan-tendik']);
                }
            })
            ->withCount([
                'usulans',
                'usulans as usulan_disetujui_count' => function ($query) {
                    $query->where('status_usulan', 'Disetujui');
                },
                'usulans as usulan_ditolak_count' => function ($query) {
                    $query->where('status_usulan', 'Ditolak');
                },
                'usulans as usulan_pending_count' => function ($query) {
                    $query->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug: Log untuk melihat data yang diambil
        \Log::info('DashboardPeriodeController - Data Debug', [
            'jenisUsulan' => $jenisUsulan,
            'namaUsulan' => $namaUsulan,
            'totalPeriodes' => $periodes->count(),
            'periodes' => $periodes->toArray()
        ]);

        // Coba ambil semua periode untuk debugging
        $allPeriodes = PeriodeUsulan::all(['id', 'nama_periode', 'jenis_usulan', 'status']);
        \Log::info('DashboardPeriodeController - All Periodes Debug', [
            'allPeriodes' => $allPeriodes->toArray()
        ]);

        // Overall statistics untuk jenis usulan ini
        $overallStats = [
            'total_periodes' => $periodes->count(),
            'periodes_aktif' => $periodes->where('status', 'Buka')->count(),
            'total_usulan' => $periodes->sum('usulans_count'),
            'usulan_disetujui' => $periodes->sum('usulan_disetujui_count'),
            'usulan_ditolak' => $periodes->sum('usulan_ditolak_count'),
            'usulan_pending' => $periodes->sum('usulan_pending_count'),
        ];

        return view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.index', compact(
            'periodes',
            'jenisUsulan',
            'namaUsulan',
            'overallStats'
        ));
    }

    /**
     * Menampilkan detail dashboard untuk periode tertentu
     */
    public function show(PeriodeUsulan $periode)
    {
        $user = Auth::guard('pegawai')->user();

        // Detailed statistics for this period
        $stats = [
            'total_usulan' => $periode->usulans()->count(),
            'usulan_disetujui' => $periode->usulans()->where('status_usulan', 'Disetujui')->count(),
            'usulan_ditolak' => $periode->usulans()->where('status_usulan', 'Ditolak')->count(),
            'usulan_pending' => $periode->usulans()->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses'])->count(),
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

        return view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.show', compact(
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

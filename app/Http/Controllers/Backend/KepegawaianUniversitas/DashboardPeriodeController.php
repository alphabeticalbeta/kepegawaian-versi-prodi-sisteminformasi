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
        $periodes = PeriodeUsulan::where(function($query) use ($namaUsulan, $jenisUsulan) {
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

        return view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.index', compact(
            'periodes',
            'jenisUsulan',
            'namaUsulan',
            'overallStats'
        ));
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
        $periodes = PeriodeUsulan::where('jenis_usulan', $namaUsulan)
            ->withCount('usulans')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'nama_periode', 'tahun_periode', 'status', 'tanggal_mulai', 'tanggal_selesai']);

        return response()->json([
            'success' => true,
            'data' => $periodes
        ]);
    }

    /**
     * Set periode aktif untuk session
     */
    public function setPeriodeAktif(Request $request)
    {
        $periodeId = $request->get('periode_id');

        if ($periodeId) {
            $periode = PeriodeUsulan::find($periodeId);
            if ($periode) {
                session(['periode_aktif_id' => $periodeId]);
                session(['periode_aktif_nama' => $periode->nama_periode]);
                session(['periode_aktif_jenis' => $periode->jenis_usulan]);

                return response()->json([
                    'success' => true,
                    'message' => 'Periode aktif berhasil diatur',
                    'periode' => $periode
                ]);
            }
        }

        return response()->json(['error' => 'Periode tidak ditemukan'], 404);
    }

    /**
     * Clear periode aktif dari session
     */
    public function clearPeriodeAktif(Request $request)
    {
        session()->forget(['periode_aktif_id', 'periode_aktif_nama', 'periode_aktif_jenis']);

        return response()->json([
            'success' => true,
            'message' => 'Periode aktif berhasil dihapus'
        ]);
    }

    /**
     * Menampilkan detail periode usulan tertentu
     */
    public function show(PeriodeUsulan $periode, Request $request)
    {
        // Ambil filter dari request
        $filter = $request->get('filter');
        $filterValue = $request->get('value');

        // Query dasar untuk usulan
        $usulansQuery = $periode->usulans();

        // Terapkan filter jika ada
        if ($filter === 'jenis_usulan_pangkat' && $filterValue) {
            $usulansQuery->whereJsonContains('data_usulan->jenis_usulan_pangkat', $filterValue);
        } elseif ($filter === 'jenis_nuptk' && $filterValue) {
            $usulansQuery->where('jenis_nuptk', $filterValue);
        } elseif ($filter === 'jenis_tugas_belajar' && $filterValue) {
            // Filter berdasarkan jenis pegawai untuk tugas belajar
            $usulansQuery->whereHas('pegawai', function($query) use ($filterValue) {
                if ($filterValue === 'dosen') {
                    $query->where('jenis_pegawai', 'Dosen');
                } elseif ($filterValue === 'tenaga_kependidikan') {
                    $query->where('jenis_pegawai', 'Tenaga Kependidikan');
                }
            });
        }

        // Ambil usulan dengan filter
        $usulans = $usulansQuery->with([
            'pegawai:id,nama_lengkap,nip,jenis_pegawai,unit_kerja_id',
            'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
            'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
            'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
            'jabatanTujuan:id,jabatan'
        ])->get();

        // Hitung statistik berdasarkan usulan yang sudah difilter
        $stats = [
            'total_usulan' => $usulans->count(),
            'usulan_disetujui' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
            'usulan_ditolak' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_pending' => $usulans->whereIn('status_usulan', [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS
            ])->count(),

            // Tambahan statistik untuk status baru
            'usulan_direkomendasikan_kepegawaian_universitas' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_direkomendasikan_bkn' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_sk_terbit' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT)->count(),
            'usulan_direkomendasikan_sister' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER)->count(),
            'usulan_tidak_direkomendasikan_kepegawaian_universitas' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_tidak_direkomendasikan_bkn' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_tidak_direkomendasikan_sister' => $usulans->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_TIM_SISTER)->count(),
        ];

        // Usulan by status for chart (berdasarkan data yang sudah difilter)
        $usulanByStatus = $usulans->groupBy('status_usulan')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();

        // Usulan by jenis pegawai (berdasarkan data yang sudah difilter)
        $usulanByJenisPegawai = $usulans->groupBy('pegawai.jenis_pegawai')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();

        // Recent usulans (berdasarkan data yang sudah difilter)
        $recentUsulans = $usulans->take(10);

        // Timeline data (berdasarkan data yang sudah difilter)
        $timelineData = $usulans->groupBy(function ($usulan) {
                return \Carbon\Carbon::parse($usulan->created_at)->format('M Y');
            })
            ->map(function ($group, $label) {
                return [
                    'month' => \Carbon\Carbon::parse($group->first()->created_at)->month,
                    'year' => \Carbon\Carbon::parse($group->first()->created_at)->year,
                    'count' => $group->count(),
                    'label' => $label
                ];
            })
            ->values()
            ->sortBy(['year', 'month']);

        // Data untuk ditampilkan di view
        $viewData = [
            'user' => Auth::guard('pegawai')->user(),
            'periode' => $periode,
            'stats' => $stats,
            'usulanByStatus' => $usulanByStatus,
            'usulanByJenisPegawai' => $usulanByJenisPegawai,
            'recentUsulans' => $recentUsulans,
            'timelineData' => $timelineData,
            'filter' => $filter,
            'filterValue' => $filterValue,
            'usulans' => $usulans // Tambahkan data usulan lengkap untuk ditampilkan
        ];

        return view('backend.layouts.views.kepegawaian-universitas.dashboard-periode.show', $viewData);
    }

    /**
     * Get count of NUPTK usulans by jenis
     */
    public function getUsulanNuptkCount(PeriodeUsulan $periode)
    {
        try {
            $counts = [
                'dosen_tetap' => $periode->usulans()->where('jenis_nuptk', 'dosen_tetap')->count(),
                'dosen_tidak_tetap' => $periode->usulans()->where('jenis_nuptk', 'dosen_tidak_tetap')->count(),
                'pengajar_non_dosen' => $periode->usulans()->where('jenis_nuptk', 'pengajar_non_dosen')->count(),
                'jabatan_fungsional_tertentu' => $periode->usulans()->where('jenis_nuptk', 'jabatan_fungsional_tertentu')->count(),
            ];

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data jumlah pengusul NUPTK'
            ], 500);
        }
    }

    /**
     * Get count of Tugas Belajar usulans by jenis
     */
    public function getUsulanTugasBelajarCount(PeriodeUsulan $periode)
    {
        try {
            $counts = [
                'dosen' => $periode->usulans()->whereHas('pegawai', function($query) {
                    $query->where('jenis_pegawai', 'Dosen');
                })->count(),
                'tenaga_kependidikan' => $periode->usulans()->whereHas('pegawai', function($query) {
                    $query->where('jenis_pegawai', 'Tenaga Kependidikan');
                })->count(),
            ];

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data jumlah pengusul Tugas Belajar'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class UsulanController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Menampilkan halaman usulan berdasarkan jenis dengan periode otomatis
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
        \Log::info('UsulanController - Mapping Debug', [
            'jenisUsulan' => $jenisUsulan,
            'namaUsulan' => $namaUsulan,
            'mapping' => $jenisMapping
        ]);

        // Cari periode yang sesuai dengan logika yang sama seperti DashboardPeriodeController
        $periode = $this->findPeriodeByJenisUsulan($namaUsulan, $jenisUsulan);

        // Debug: Log untuk melihat periode yang ditemukan
        \Log::info('UsulanController - Periode Debug', [
            'periode_id' => $periode->id ?? 'null',
            'periode_nama' => $periode->nama_periode ?? 'null',
            'periode_jenis' => $periode->jenis_usulan ?? 'null',
            'periode_status' => $periode->status ?? 'null',
            'periode_tahun' => $periode->tahun_periode ?? 'null'
        ]);

        // Ambil usulan untuk periode ini
        $usulans = $periode->usulans()
            ->with([
                'pegawai:id,nama_lengkap,nip,jenis_pegawai',
                'pegawai.unitKerja:id,nama_unit_kerja',
                'pegawai.unitKerja.subUnitKerja:id,nama_sub_unit_kerja,unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama_unit_kerja',
                'jabatanTujuan:id,jabatan'
            ])
            ->latest()
            ->paginate(15);

        // Debug: Log untuk melihat jumlah usulan yang ditemukan
        \Log::info('UsulanController - Usulans Debug', [
            'periode_id' => $periode->id,
            'total_usulans' => $usulans->total(),
            'usulans_count' => $usulans->count()
        ]);

        // Statistik untuk periode ini
        $stats = [
            'total_usulan' => $periode->usulans()->count(),
            'usulan_disetujui' => $periode->usulans()->where('status_usulan', 'Disetujui')->count(),
            'usulan_ditolak' => $periode->usulans()->where('status_usulan', 'Ditolak')->count(),
            'usulan_pending' => $periode->usulans()->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses'])->count(),
        ];

        return view('backend.layouts.views.kepegawaian-universitas.usulan.index', compact(
            'periode',
            'usulans',
            'jenisUsulan',
            'namaUsulan',
            'stats'
        ));
    }

    /**
     * Menampilkan detail usulan
     */
    public function show(Usulan $usulan)
    {
        // Eager load relasi yang dibutuhkan
        $usulan->load([
            'pegawai.pangkat',
            'pegawai.jabatan',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'jabatanLama',
            'jabatanTujuan',
            'periodeUsulan',
            'dokumens',
            'logs' => function ($query) {
                $query->with('dilakukanOleh')->latest();
            }
        ]);

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('admin_universitas') ?? [];

        // Get penilais data for popup
        $penilais = \App\Models\KepegawaianUniversitas\Pegawai::whereHas('roles', function($query) {
            $query->where('name', 'Penilai Universitas');
        })->orderBy('nama_lengkap')->get();

        // Determine action permissions based on status
        $canReturn = in_array($usulan->status_usulan, ['Usulan Direkomendasi Tim Penilai', 'Tidak Direkomendasikan', 'Diusulkan ke Universitas', 'Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai']);
        $canForward = in_array($usulan->status_usulan, ['Usulan Direkomendasi Tim Penilai', 'Tidak Direkomendasikan', 'Diusulkan ke Universitas', 'Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai']);

        return view('backend.layouts.views.kepegawaian-universitas.usulan.detail', [
            'usulan' => $usulan,
            'existingValidation' => $existingValidation,
            'penilais' => $penilais,
            'config' => [
                'canReturn' => $canReturn,
                'canForward' => $canForward,
                'routePrefix' => 'kepegawaian-universitas',
                'canEdit' => in_array($usulan->status_usulan, ['Usulan Direkomendasi Tim Penilai', 'Tidak Direkomendasikan', 'Diusulkan ke Universitas', 'Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai']),
                'canView' => true,
                'submitFunctions' => ['save', 'return_to_pegawai', 'forward_to_penilai', 'recommend', 'not_recommend']
            ]
        ]);
    }

    /**
     * Membuat form usulan baru
     */
    public function create(Request $request)
    {
        $jenisUsulan = $request->get('jenis', 'jabatan');

        // Mapping jenis usulan
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

        // Cari atau buat periode untuk jenis usulan ini
        $periode = $this->findOrCreatePeriode($namaUsulan, $jenisUsulan);

        return view('backend.layouts.views.kepegawaian-universitas.usulan.create', compact(
            'periode',
            'jenisUsulan',
            'namaUsulan'
        ));
    }

    /**
     * Mencari periode berdasarkan jenis usulan dengan logika yang sama seperti DashboardPeriodeController
     */
    private function findPeriodeByJenisUsulan($namaUsulan, $jenisUsulan)
    {
        // Debug: Log parameter yang diterima
        \Log::info('UsulanController - findPeriodeByJenisUsulan called', [
            'namaUsulan' => $namaUsulan,
            'jenisUsulan' => $jenisUsulan
        ]);

        // Gunakan logika yang sama seperti DashboardPeriodeController
        $periodes = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where(function($query) use ($namaUsulan, $jenisUsulan) {
                // Exact match untuk jenis usulan utama
                $query->where('jenis_usulan', $namaUsulan);

                // Jika jenis usulan adalah jabatan, juga ambil sub-jenis
                if ($jenisUsulan === 'jabatan') {
                    $query->orWhereIn('jenis_usulan', ['usulan-jabatan-dosen', 'usulan-jabatan-tendik']);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug: Log untuk melihat semua periode yang ditemukan
        \Log::info('UsulanController - All Periodes Found', [
            'namaUsulan' => $namaUsulan,
            'jenisUsulan' => $jenisUsulan,
            'total_periodes' => $periodes->count(),
            'periodes' => $periodes->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_periode' => $p->nama_periode,
                    'jenis_usulan' => $p->jenis_usulan,
                    'status' => $p->status,
                    'tahun_periode' => $p->tahun_periode
                ];
            })->toArray()
        ]);

        // Jika tidak ada periode yang ditemukan dengan logika DashboardPeriodeController,
        // gunakan logika lama sebagai fallback
        if ($periodes->count() === 0) {
            \Log::warning('UsulanController - No periode found with DashboardPeriodeController logic, using fallback');
            
            $tahunSekarang = \Carbon\Carbon::now()->year;
            $activePeriode = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('jenis_usulan', $namaUsulan)
                ->where('tahun_periode', $tahunSekarang)
                ->where('status', 'Buka')
                ->first();

            if (!$activePeriode) {
                // Jika masih tidak ada, ambil periode terbaru dengan jenis usulan yang sama
                $activePeriode = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('jenis_usulan', $namaUsulan)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$activePeriode) {
                // Jika masih tidak ada, buat periode baru
                \Log::warning('UsulanController - No periode found, creating new one');
                $activePeriode = $this->findOrCreatePeriode($namaUsulan, $jenisUsulan);
            }
        } else {
            // Ambil periode yang aktif (status Buka) atau periode terbaru
            $activePeriode = $periodes->where('status', 'Buka')->first();
            
            if (!$activePeriode) {
                // Jika tidak ada periode aktif, ambil periode terbaru
                $activePeriode = $periodes->first();
            }
        }

        // Debug: Log periode yang dipilih
        \Log::info('UsulanController - Selected Periode', [
            'periode_id' => $activePeriode->id ?? 'null',
            'periode_nama' => $activePeriode->nama_periode ?? 'null',
            'periode_jenis' => $activePeriode->jenis_usulan ?? 'null',
            'periode_status' => $activePeriode->status ?? 'null'
        ]);

        return $activePeriode;
    }

    /**
     * Mencari atau membuat periode usulan otomatis
     */
    private function findOrCreatePeriode($namaUsulan, $jenisUsulan)
    {
        $tahunSekarang = Carbon::now()->year;

        // Cari periode yang aktif untuk jenis usulan ini di tahun ini
        $periode = PeriodeUsulan::where('jenis_usulan', $namaUsulan)
            ->where('tahun_periode', $tahunSekarang)
            ->where('status', 'Buka')
            ->first();

        // Jika tidak ada, buat periode baru
        if (!$periode) {
            $periode = PeriodeUsulan::create([
                'nama_periode' => $namaUsulan . ' - ' . $tahunSekarang,
                'jenis_usulan' => $namaUsulan,
                'tahun_periode' => $tahunSekarang,
                'tanggal_mulai' => Carbon::now()->startOfYear(),
                'tanggal_selesai' => Carbon::now()->endOfYear(),
                'status' => 'Buka',
                'senat_min_setuju' => 70, // Default 70%
            ]);
        }

        return $periode;
    }

    /**
     * Toggle status periode (buka/tutup)
     */
    public function togglePeriode(Request $request)
    {
        $jenisUsulan = $request->get('jenis', 'jabatan');

        // Mapping jenis usulan
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
        $periode = $this->findOrCreatePeriode($namaUsulan, $jenisUsulan);

        // Toggle status
        $periode->status = $periode->status === 'Buka' ? 'Tutup' : 'Buka';
        $periode->save();

        $message = $periode->status === 'Buka'
            ? 'Periode ' . $namaUsulan . ' berhasil dibuka.'
            : 'Periode ' . $namaUsulan . ' berhasil ditutup.';

        return redirect()->back()->with('success', $message);
    }
}

<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan;
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

        // Cari atau buat periode untuk jenis usulan ini
        $periode = $this->findOrCreatePeriode($namaUsulan, $jenisUsulan);

        // Ambil usulan untuk periode ini
        $usulans = $periode->usulans()
            ->with(['pegawai:id,nama_lengkap,nip,jenis_pegawai'])
            ->latest()
            ->paginate(15);

        // Statistik untuk periode ini
        $stats = [
            'total_usulan' => $periode->usulans()->count(),
            'usulan_disetujui' => $periode->usulans()->where('status_usulan', 'Disetujui')->count(),
            'usulan_ditolak' => $periode->usulans()->where('status_usulan', 'Ditolak')->count(),
            'usulan_pending' => $periode->usulans()->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses'])->count(),
        ];

        return view('backend.layouts.views.admin-univ-usulan.usulan.index', compact(
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

        // Determine if can edit based on status
        $canEdit = in_array($usulan->status_usulan, [
            'Diusulkan ke Universitas',
            'Sedang Direview',
            'Menunggu Review Admin Univ',
            'Perbaikan Usulan'
        ]);

        // Get penilais data for popup
        $penilais = \App\Models\BackendUnivUsulan\Pegawai::whereHas('roles', function($query) {
            $query->where('name', 'Penilai Universitas');
        })->orderBy('nama_lengkap')->get();

        return view('backend.layouts.views.admin-univ-usulan.usulan.detail', compact('usulan', 'existingValidation', 'penilais', 'canEdit'));
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

        return view('backend.layouts.views.admin-univ-usulan.usulan.create', compact(
            'periode',
            'jenisUsulan',
            'namaUsulan'
        ));
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

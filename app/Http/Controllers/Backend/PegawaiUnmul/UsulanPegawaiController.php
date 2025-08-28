<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsulanPegawaiController extends Controller
{
    /**
     * Dashboard utama - menampilkan semua usulan pegawai
     */
    public function index()
    {
        /** @var \App\Models\KepegawaianUniversitas\Pegawai $pegawai */
        $pegawai = Auth::user();

        $usulans = $pegawai->usulans()
                        ->with([
                            'periodeUsulan',
                            'logs' => function($query) {
                                $query->with('dilakukanOleh')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5); // Ambil 5 log terbaru untuk preview
                            }
                        ])
                        ->latest()
                        ->paginate(10);

        // Statistik usulan
        $statistics = [
            'total_usulan' => $pegawai->usulans()->count(),
            'usulan_draft' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN)->count(),
            'usulan_diajukan' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
            'usulan_disetujui' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN)->count(),
            'usulan_ditolak' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN)->count(),
        ];

        // Periode usulan yang sedang aktif
        $activePeriods = PeriodeUsulan::where('status', 'Buka')
                                     ->where('tanggal_mulai', '<=', now())
                                     ->where('tanggal_selesai', '>=', now())
                                     ->get();

        return view('backend.layouts.views.pegawai-unmul.dashboard', [
            'usulans' => $usulans,
            'statistics' => $statistics,
            'activePeriods' => $activePeriods,
        ]);
    }

    /**
     * Router untuk mengarahkan ke controller usulan yang tepat
     */
    public function createUsulan(Request $request)
    {
        $jenisUsulan = $request->query('jenis');

        if (!$jenisUsulan) {
            return $this->showUsulanSelector();
        }

        // Redirect ke controller yang sesuai berdasarkan jenis usulan
        return match($jenisUsulan) {
            'jabatan' => redirect()->route('pegawai-unmul.usulan-jabatan.create'),
            'pangkat' => redirect()->route('pegawai-unmul.usulan-pangkat.create'),
            'tunjangan' => redirect()->route('pegawai-unmul.usulan-tunjangan.create'),
            'pensiun' => redirect()->route('pegawai-unmul.usulan-pensiun.create'),
            'mutasi' => redirect()->route('pegawai-unmul.usulan-mutasi.create'),
            'cuti' => redirect()->route('pegawai-unmul.usulan-cuti.create'),
            'pendidikan' => redirect()->route('pegawai-unmul.usulan-pendidikan.create'),
            'penelitian' => redirect()->route('pegawai-unmul.usulan-penelitian.create'),
            'pengabdian' => redirect()->route('pegawai-unmul.usulan-pengabdian.create'),
            default => redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                             ->with('error', 'Jenis usulan tidak valid.')
        };
    }

    /**
     * Tampilkan halaman selector jenis usulan
     */
    protected function showUsulanSelector()
    {
        $pegawai = Auth::user();

        // Daftar jenis usulan yang tersedia
        $jenisUsulanOptions = [
            'jabatan' => [
                'title' => 'Usulan Jabatan',
                'description' => 'Pengajuan kenaikan jabatan fungsional',
                'icon' => 'trending-up',
                'color' => 'blue',
                'available' => $this->isUsulanAvailable('jabatan', $pegawai),
                'route' => 'pegawai-unmul.usulan-jabatan.create'
            ],
            'pangkat' => [
                'title' => 'Usulan Pangkat',
                'description' => 'Pengajuan kenaikan pangkat',
                'icon' => 'award',
                'color' => 'green',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'tunjangan' => [
                'title' => 'Usulan Tunjangan',
                'description' => 'Pengajuan tunjangan tambahan',
                'icon' => 'dollar-sign',
                'color' => 'yellow',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'pensiun' => [
                'title' => 'Usulan Pensiun',
                'description' => 'Pengajuan pensiun dini atau reguler',
                'icon' => 'calendar-x',
                'color' => 'red',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'mutasi' => [
                'title' => 'Usulan Mutasi',
                'description' => 'Pengajuan pindah unit kerja',
                'icon' => 'map-pin',
                'color' => 'purple',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'cuti' => [
                'title' => 'Usulan Cuti',
                'description' => 'Pengajuan cuti panjang/khusus',
                'icon' => 'calendar',
                'color' => 'indigo',
                'available' => false, // Belum tersedia
                'route' => null
            ],
        ];

        return view('backend.layouts.views.pegawai-unmul.usulan-selector', [
            'pegawai' => $pegawai,
            'jenisUsulanOptions' => $jenisUsulanOptions,
        ]);
    }

    /**
     * Cek apakah jenis usulan tersedia untuk pegawai
     */
    protected function isUsulanAvailable(string $jenisUsulan, $pegawai): bool
    {
        return match($jenisUsulan) {
            'jabatan' => $this->isJabatanUsulanAvailable($pegawai),
            'pangkat' => false, // Implementasi nanti
            'tunjangan' => false, // Implementasi nanti
            default => false
        };
    }

    /**
     * Cek apakah usulan jabatan tersedia
     */
    protected function isJabatanUsulanAvailable($pegawai): bool
    {
        // Cek status kepegawaian
        $eligibleStatuses = ['Dosen PNS', 'Tenaga Kependidikan PNS'];
        if (!in_array($pegawai->status_kepegawaian, $eligibleStatuses)) {
            return false;
        }

        // Cek periode aktif
        $jenisUsulanPeriode = $pegawai->jenis_pegawai === 'Dosen'
            ? 'usulan-jabatan-dosen'
            : 'usulan-jabatan-tendik';

        $activePeriod = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->exists();

        if (!$activePeriod) {
            return false;
        }

        // Cek usulan aktif
        $hasActiveUsulan = $pegawai->usulans()
            ->where('jenis_usulan', $jenisUsulanPeriode)
                            ->whereNotIn('status_usulan', [\App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN, \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN])
            ->exists();

        return !$hasActiveUsulan;
    }

    /**
     * Show usulan detail (generic untuk semua jenis)
     */
    public function show($usulanId)
    {
        $usulan = Auth::user()->usulans()
                            ->with(['periodeUsulan', 'logs.dilakukanOleh'])
                            ->findOrFail($usulanId);

        // Redirect ke controller yang sesuai berdasarkan jenis usulan
        return match($usulan->jenis_usulan) {
            'usulan-jabatan-dosen', 'usulan-jabatan-tendik' =>
                redirect()->route('pegawai-unmul.usulan-jabatan.edit', $usulan->id),
            'usulan-pangkat' =>
                redirect()->route('pegawai-unmul.usulan-pangkat.edit', $usulan->id),
            default =>
                view('backend.layouts.pegawai-unmul.usulan-detail-generic', compact('usulan'))
        };
    }

    /**
     * API endpoint untuk statistik dashboard
     */
    public function getStatistics()
    {
        $pegawai = Auth::user();

        $statistics = [
            'usulan_by_status' => [
                'Draft' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN)->count(),
                'Diajukan' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS)->count(),
                'Sedang Direview' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS)->count(),
                'Perlu Perbaikan' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS)->count(),
                'Disetujui' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN)->count(),
                'Ditolak' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN)->count(),
            ],
            'usulan_by_type' => $pegawai->usulans()
                                      ->selectRaw('jenis_usulan, count(*) as total')
                                      ->groupBy('jenis_usulan')
                                      ->pluck('total', 'jenis_usulan')
                                      ->toArray(),
            'recent_activities' => $pegawai->usulans()
                                         ->with('logs')
                                         ->latest()
                                         ->take(5)
                                         ->get()
                                         ->map(function($usulan) {
                                             $latestLog = $usulan->logs->first();
                                             return [
                                                 'usulan_id' => $usulan->id,
                                                 'jenis_usulan' => $usulan->jenis_usulan,
                                                 'status' => $usulan->status_usulan,
                                                 'last_activity' => $latestLog ? $latestLog->created_at->diffForHumans() : null,
                                                 'created_at' => $usulan->created_at->isoFormat('D MMM YYYY'),
                                             ];
                                         })
        ];

        return response()->json($statistics);
    }
}

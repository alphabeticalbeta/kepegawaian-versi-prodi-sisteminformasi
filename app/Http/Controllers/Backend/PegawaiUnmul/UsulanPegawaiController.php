<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;
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
            'usulan_disetujui' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                            'usulan_ditolak' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),

            // Tambahan statistik untuk status baru
            'usulan_direkomendasikan_kepegawaian_universitas' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_direkomendasikan_bkn' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_sk_terbit' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT)->count(),
            'usulan_direkomendasikan_sister' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER)->count(),
            'usulan_tidak_direkomendasikan_kepegawaian_universitas' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
            'usulan_tidak_direkomendasikan_bkn' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN)->count(),
            'usulan_tidak_direkomendasikan_sister' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_TIM_SISTER)->count(),
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
            'nuptk' => redirect()->route('pegawai-unmul.usulan-nuptk.create'),
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
            'nuptk' => [
                'title' => 'Usulan NUPTK',
                'description' => 'Pengajuan Nomor Unik Pendidik dan Tenaga Kependidikan',
                'icon' => 'user-check',
                'color' => 'green',
                'available' => $this->isUsulanAvailable('nuptk', $pegawai),
                'route' => 'pegawai-unmul.usulan-nuptk.create'
            ],
            'pangkat' => [
                'title' => 'Usulan Pangkat',
                'description' => 'Pengajuan kenaikan pangkat',
                'icon' => 'award',
                'color' => 'emerald',
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
            'nuptk' => $this->isNuptkUsulanAvailable($pegawai),
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
                            ->whereNotIn('status_usulan', [\App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS, \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS])
            ->exists();

        return !$hasActiveUsulan;
    }

    /**
     * Cek apakah usulan NUPTK tersedia
     */
    protected function isNuptkUsulanAvailable($pegawai): bool
    {
        // Cek status kepegawaian
        $eligibleStatuses = ['Dosen PNS', 'Tenaga Kependidikan PNS'];
        if (!in_array($pegawai->status_kepegawaian, $eligibleStatuses)) {
            return false;
        }

        // Cek periode aktif
        $activePeriod = PeriodeUsulan::where('jenis_usulan', 'usulan-nuptk')
            ->where('status', 'Buka')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->exists();

        if (!$activePeriod) {
            return false;
        }

        // Cek usulan aktif
        $hasActiveUsulan = $pegawai->usulans()
            ->where('jenis_usulan', 'usulan-nuptk')
            ->whereNotIn('status_usulan', [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS
            ])
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
                'Disetujui' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS)->count(),
                'Ditolak' => $pegawai->usulans()->where('status_usulan', \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)->count(),
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

    /**
     * Mendapatkan histori periode untuk sidebar
     */
    public function getHistoriPeriode(Request $request)
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
                'description' => 'Pengajuan cuti tahunan atau khusus',
                'icon' => 'calendar',
                'color' => 'indigo',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'pendidikan' => [
                'title' => 'Usulan Pendidikan',
                'description' => 'Pengajuan tugas belajar atau studi lanjut',
                'icon' => 'book-open',
                'color' => 'teal',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'penelitian' => [
                'title' => 'Usulan Penelitian',
                'description' => 'Pengajuan penelitian atau pengabdian',
                'icon' => 'flask',
                'color' => 'orange',
                'available' => false, // Belum tersedia
                'route' => null
            ],
            'pengabdian' => [
                'title' => 'Usulan Pengabdian',
                'description' => 'Pengajuan kegiatan pengabdian masyarakat',
                'icon' => 'heart',
                'color' => 'pink',
                'available' => false, // Belum tersedia
                'route' => null
            ]
        ];

        return view('backend.layouts.views.pegawai-unmul.usulan-selector', [
            'jenisUsulanOptions' => $jenisUsulanOptions
        ]);
    }

    /**
     * Mendapatkan data log untuk usulan tertentu (untuk modal)
     */
    public function getLogs(\App\Models\KepegawaianUniversitas\Usulan $usulan)
    {
        try {
            $pegawai = Auth::user();

            // Verify this usulan belongs to the authenticated user
            if ($usulan->pegawai_id !== $pegawai->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this usulan'
                ], 403);
            }

            // Get logs for this usulan
            $logs = $usulan->logs()
                ->with('dilakukanOleh:id,nama_lengkap')
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform logs to simple array to avoid accessor issues
            $transformedLogs = $logs->map(function($log) {
                return [
                    'id' => $log->id,
                    'status_sebelumnya' => $log->status_sebelumnya,
                    'status_baru' => $log->status_baru,
                    'catatan' => $log->catatan,
                    'created_at' => $log->created_at->toISOString(),
                    'dilakukan_oleh' => $log->dilakukanOleh ? [
                        'id' => $log->dilakukanOleh->id,
                        'nama_lengkap' => $log->dilakukanOleh->nama_lengkap
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'logs' => $transformedLogs,
                'usulan_info' => [
                    'id' => $usulan->id,
                    'jenis_usulan' => $usulan->jenis_usulan,
                    'status_saat_ini' => $usulan->status_usulan
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getLogs', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman logs-simple untuk usulan tertentu
     */
    public function showLogsSimple(\App\Models\KepegawaianUniversitas\Usulan $usulan)
    {
        try {
            $pegawai = Auth::user();

            // Verify this usulan belongs to the authenticated user
            if ($usulan->pegawai_id !== $pegawai->id) {
                abort(403, 'Unauthorized access to this usulan');
            }

            // Get logs for this usulan
            $logs = $usulan->logs()
                ->with('dilakukanOleh:id,nama_lengkap')
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform logs to simple array to avoid accessor issues
            $formattedLogs = [];
            foreach ($logs as $log) {
                // Get user name safely
                $userName = 'System';
                if ($log->dilakukan_oleh_id) {
                    $user = \App\Models\KepegawaianUniversitas\Pegawai::find($log->dilakukan_oleh_id);
                    if ($user) {
                        $userName = $user->nama_lengkap;
                    }
                }

                // Format date safely
                $formattedDate = 'Unknown';
                if ($log->created_at) {
                    try {
                        $formattedDate = $log->created_at->format('d F Y, H:i');
                    } catch (\Exception $e) {
                        $formattedDate = $log->created_at->toDateString();
                    }
                }

                $formattedLogs[] = [
                    'id' => $log->id,
                    'status' => $log->status_baru ?? $log->status_sebelumnya ?? 'Unknown',
                    'status_sebelumnya' => $log->status_sebelumnya,
                    'status_baru' => $log->status_baru,
                    'keterangan' => $log->catatan ?? 'No description',
                    'user_name' => $userName,
                    'formatted_date' => $formattedDate,
                    'created_at' => $log->created_at ? $log->created_at->toISOString() : null,
                ];
            }

            // Load usulan with relationships for the view
            $usulanWithRelations = $usulan->load([
                'pegawai',
                'periodeUsulan',
                'jabatanLama',
                'jabatanTujuan'
            ]);

            // Return simple HTML view
            return view('backend.layouts.views.pegawai-unmul.logs-usulan', [
                'logs' => $formattedLogs,
                'usulan' => $usulanWithRelations
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showLogsSimple', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Gagal memuat data log: ' . $e->getMessage());
        }
    }
}

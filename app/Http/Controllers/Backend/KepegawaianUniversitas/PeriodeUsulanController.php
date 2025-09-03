<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Http\Request;
use App\Rules\NoDateRangeOverlap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class PeriodeUsulanController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Menampilkan daftar semua resource.
     */
    public function index(Request $request)
    {
        $jenisUsulan = $request->query('jenis');
        
        // Mapping jenis usulan dari sidebar ke nama periode yang benar (sesuai data di database)
        $jenisMapping = [
            'nuptk' => 'usulan-nuptk',
            'laporan-lkd' => 'usulan-laporan-lkd',
            'presensi' => 'usulan-presensi',
            'penyesuaian-masa-kerja' => 'usulan-penyesuaian-masa-kerja',
            'ujian-dinas-ijazah' => 'usulan-ujian-dinas-ijazah',
            'jabatan-dosen-regular' => 'jabatan-dosen-regular',
            'jabatan-dosen-pengangkatan' => 'jabatan-dosen-pengangkatan',
            'laporan-serdos' => 'usulan-laporan-serdos',
            'pensiun' => 'usulan-pensiun',
            'kepangkatan' => 'usulan-kepangkatan',
            'pencantuman-gelar' => 'usulan-pencantuman-gelar',
            'id-sinta-sister' => 'usulan-id-sinta-sister',
            'satyalancana' => 'usulan-satyalancana',
            'tugas-belajar' => 'usulan-tugas-belajar',
            'pengaktifan-kembali' => 'usulan-pengaktifan-kembali'
        ];
        
        $query = PeriodeUsulan::withCount('usulans');
        
        // Filter berdasarkan jenis usulan jika parameter diberikan
        if ($jenisUsulan && $jenisUsulan !== 'all') {
            $namaUsulan = $jenisMapping[$jenisUsulan] ?? $jenisUsulan;
            $query->where('jenis_usulan', $namaUsulan);
        }
        
        $periodeUsulans = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Tambahkan parameter jenis ke pagination links
        if ($jenisUsulan) {
            $periodeUsulans->appends(['jenis' => $jenisUsulan]);
        }

        return view('backend.layouts.views.periode-usulan.index', compact('periodeUsulans', 'jenisUsulan'));
    }

    /**
     * Menampilkan form untuk membuat resource baru.
     */
    public function create(Request $request)
    {
        $jenisUsulan = $request->query('jenis', 'jabatan');

        // Mapping dari parameter URL ke jenis usulan yang benar
        $jenisMapping = [
            'all' => 'Semua Usulan Aktif',
            'jabatan-dosen-regular' => 'jabatan-dosen-regular',
            'jabatan-dosen-pengangkatan' => 'jabatan-dosen-pengangkatan',
            'usulan-nuptk' => 'usulan-nuptk',
            'usulan-laporan-lkd' => 'usulan-laporan-lkd',
            'usulan-presensi' => 'usulan-presensi',
            'usulan-id-sinta-sister' => 'usulan-id-sinta-sister',
            'usulan-satyalancana' => 'usulan-satyalancana',
            'usulan-tugas-belajar' => 'usulan-tugas-belajar',
            'usulan-pengaktifan-kembali' => 'usulan-pengaktifan-kembali',
            'usulan-penyesuaian-masa-kerja' => 'usulan-penyesuaian-masa-kerja',
            'usulan-ujian-dinas-ijazah' => 'usulan-ujian-dinas-ijazah',
            'usulan-laporan-serdos' => 'usulan-laporan-serdos',
            'usulan-pensiun' => 'usulan-pensiun',
            'usulan-kepangkatan' => 'usulan-kepangkatan',
            'usulan-pencantuman-gelar' => 'usulan-pencantuman-gelar'
        ];

        $jenisUsulanOtomatis = $jenisMapping[$jenisUsulan] ?? 'jabatan-dosen-regular';

        // Tentukan view berdasarkan jenis usulan
        $viewMapping = [
            'jabatan-dosen-regular' => 'backend.layouts.views.periode-usulan.form',
            'jabatan-dosen-pengangkatan' => 'backend.layouts.views.periode-usulan.form',
            'usulan-nuptk' => 'backend.layouts.views.periode-usulan.form',
            'usulan-laporan-lkd' => 'backend.layouts.views.periode-usulan.form',
            'usulan-presensi' => 'backend.layouts.views.periode-usulan.form',
            'usulan-id-sinta-sister' => 'backend.layouts.views.periode-usulan.form',
            'usulan-satyalancana' => 'backend.layouts.views.periode-usulan.form',
            'usulan-tugas-belajar' => 'backend.layouts.views.periode-usulan.form',
            'usulan-pengaktifan-kembali' => 'backend.layouts.views.periode-usulan.form',
            'usulan-penyesuaian-masa-kerja' => 'backend.layouts.views.periode-usulan.form',
            'usulan-ujian-dinas-ijazah' => 'backend.layouts.views.periode-usulan.form',
            'usulan-laporan-serdos' => 'backend.layouts.views.periode-usulan.form',
            'usulan-pensiun' => 'backend.layouts.views.periode-usulan.form',
            'usulan-kepangkatan' => 'backend.layouts.views.periode-usulan.form',
            'usulan-pencantuman-gelar' => 'backend.layouts.views.periode-usulan.form'
        ];

        $view = $viewMapping[$jenisUsulan] ?? 'backend.layouts.views.periode-usulan.form';

        return view($view, [
            'jenis_usulan_otomatis' => $jenisUsulanOtomatis,
            'jenis_usulan_key' => $jenisUsulan,
            'nama_usulan' => ucwords(str_replace('-', ' ', $jenisUsulanOtomatis))
        ]);
    }

    /**
     * Menyimpan resource yang baru dibuat.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'nama_periode'            => ['required', 'string', 'max:255'],
            'jenis_usulan'            => ['required', 'string', 'max:255'],
            'status_kepegawaian'      => ['required', 'array', 'min:1'],
            'status_kepegawaian.*'    => ['string', 'in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN'],
            'tanggal_mulai'           => [
                'required', 'date',
                new NoDateRangeOverlap(
                    table: 'periode_usulans',
                    startColumn: 'tanggal_mulai',
                    endColumn: 'tanggal_selesai',
                    filters: [
                        'jenis_usulan' => $request->input('jenis_usulan')
                    ],
                    excludeId: null
                ),
            ],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_mulai_perbaikan' => ['nullable', 'date'],
            'tanggal_selesai_perbaikan' => ['nullable', 'date'],
            'status'                  => ['required', 'in:Buka,Tutup'],
            'senat_min_setuju'        => ['nullable', 'integer', 'min:0'],
        ];

        // Conditional validation untuk tanggal perbaikan
        if ($request->filled('tanggal_mulai_perbaikan')) {
            $validationRules['tanggal_mulai_perbaikan'][] = 'after_or_equal:tanggal_selesai';
        }

        if ($request->filled('tanggal_selesai_perbaikan')) {
            $validationRules['tanggal_selesai_perbaikan'][] = 'after_or_equal:tanggal_mulai_perbaikan';
        }

        $validated = $request->validate($validationRules);

        // Log untuk debugging
        \Log::info('Periode Usulan Store Request', [
            'request_data' => $request->all(),
            'validated_data' => $validated,
            'jenis_usulan' => $request->input('jenis_usulan'),
            'status_kepegawaian' => $request->input('status_kepegawaian'),
            'has_errors' => $request->has('_token')
        ]);

        // Hitung tahun_periode dari tanggal_mulai
        $validated['tahun_periode'] = Carbon::parse($validated['tanggal_mulai'])->year;

        // (Opsional) Jika ingin selalu buka saat buat:
        // $validated['status'] = 'Buka';

        // Defaultkan senat_min_setuju bila kosong
        $validated['senat_min_setuju'] = (int) ($validated['senat_min_setuju'] ?? 0);

        try {
            DB::transaction(function () use ($validated) {
                $periode = new PeriodeUsulan();
                $periode->fill($validated);   // boleh fill karena kita kontrol $validated
                $periode->save();
            });

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Semua Usulan Aktif' => 'all',
                'jabatan-dosen-regular' => 'jabatan-dosen-regular',
                'jabatan-dosen-pengangkatan' => 'jabatan-dosen-pengangkatan',
                'usulan-nuptk' => 'nuptk',
                'usulan-laporan-lkd' => 'laporan-lkd',
                'usulan-presensi' => 'presensi',
                'usulan-penyesuaian-masa-kerja' => 'penyesuaian-masa-kerja',
                'usulan-ujian-dinas-ijazah' => 'ujian-dinas-ijazah',
                'usulan-laporan-serdos' => 'laporan-serdos',
                'usulan-pensiun' => 'pensiun',
                'usulan-kepangkatan' => 'kepangkatan',
                'usulan-pencantuman-gelar' => 'pencantuman-gelar',
                'usulan-id-sinta-sister' => 'id-sinta-sister',
                'usulan-satyalancana' => 'satyalancana',
                'usulan-tugas-belajar' => 'tugas-belajar',
                'usulan-pengaktifan-kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$validated['jenis_usulan']] ?? 'jabatan-dosen-regular';

            return redirect()->route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $validated['nama_periode'] . '" berhasil dibuat!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', '❌ Validasi gagal. Silakan periksa kembali data yang dimasukkan.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Gagal menyimpan periode usulan. Silakan coba lagi.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Menampilkan resource yang spesifik.
     */
    public function show(PeriodeUsulan $periodeUsulan)
    {
        return view('backend.layouts.views.periode-usulan.show', [
            'periode' => $periodeUsulan
        ]);
    }

    /**
     * Menampilkan form untuk mengedit resource.
     */
    public function edit(PeriodeUsulan $periodeUsulan)
    {
        // Debug logging untuk model binding di edit
        \Log::info('PeriodeUsulan Edit Request - Model Binding Check', [
            'periode_id' => $periodeUsulan->id ?? 'NULL',
            'periode_exists' => isset($periodeUsulan),
            'periode_class' => get_class($periodeUsulan),
            'route_parameter' => request()->route('periodeUsulan'),
            'current_data' => [
                'nama_periode' => $periodeUsulan->nama_periode ?? 'NULL',
                'jenis_usulan' => $periodeUsulan->jenis_usulan ?? 'NULL',
                'status' => $periodeUsulan->status ?? 'NULL',
            ]
        ]);
        
        return view('backend.layouts.views.periode-usulan.form', [
            'periode' => $periodeUsulan,
            'nama_usulan' => $periodeUsulan->jenis_usulan
        ]);
    }

    /**
     * Memperbarui resource yang ada di storage.
     */
    public function update(Request $request, PeriodeUsulan $periodeUsulan)
    {
        // Debug logging untuk model binding
        \Log::info('PeriodeUsulan Update Request - Model Binding Check', [
            'periode_id' => $periodeUsulan->id ?? 'NULL',
            'periode_exists' => isset($periodeUsulan),
            'periode_class' => get_class($periodeUsulan),
            'route_parameter' => $request->route('periodeUsulan'),
            'request_data' => $request->all(),
            'current_data' => [
                'nama_periode' => $periodeUsulan->nama_periode ?? 'NULL',
                'jenis_usulan' => $periodeUsulan->jenis_usulan ?? 'NULL',
                'status' => $periodeUsulan->status ?? 'NULL',
                'tanggal_mulai' => $periodeUsulan->tanggal_mulai ?? 'NULL',
                'tanggal_selesai' => $periodeUsulan->tanggal_selesai ?? 'NULL',
            ]
        ]);
        
        // Cek apakah perlu validasi overlap
        $needsOverlapValidation = false;
        
        // Cek apakah tanggal berubah (bandingkan dalam format yang sama)
        $requestTanggalMulai = $request->input('tanggal_mulai');
        $requestTanggalSelesai = $request->input('tanggal_selesai');
        
        // Pastikan tanggal tidak null sebelum format
        $currentTanggalMulai = $periodeUsulan->tanggal_mulai ? $periodeUsulan->tanggal_mulai->format('Y-m-d') : null;
        $currentTanggalSelesai = $periodeUsulan->tanggal_selesai ? $periodeUsulan->tanggal_selesai->format('Y-m-d') : null;
        
        // Cek apakah tanggal berubah
        $tanggalBerubah = ($requestTanggalMulai != $currentTanggalMulai || $requestTanggalSelesai != $currentTanggalSelesai);
        
        // Cek apakah status berubah dari Tutup ke Buka
        $statusBerubahKeBuka = ($request->input('status') == 'Buka' && $periodeUsulan->status == 'Tutup');
        
        // Debug logging untuk validasi
        \Log::info('PeriodeUsulan Update Validation Check', [
            'tanggal_berubah' => $tanggalBerubah,
            'status_berubah_ke_buka' => $statusBerubahKeBuka,
            'needs_overlap_validation' => ($tanggalBerubah || $statusBerubahKeBuka),
            'request_tanggal' => [
                'mulai' => $requestTanggalMulai,
                'selesai' => $requestTanggalSelesai
            ],
            'current_tanggal' => [
                'mulai' => $currentTanggalMulai,
                'selesai' => $currentTanggalSelesai
            ]
        ]);
        
        // Hanya perlu validasi overlap jika ada perubahan yang signifikan
        if ($tanggalBerubah || $statusBerubahKeBuka) {
            $needsOverlapValidation = true;
        }
        

        

        
        $validationRules = [
            'nama_periode'            => ['required', 'string', 'max:255'],
            'jenis_usulan'            => ['required', 'string', 'max:255'],
            'status_kepegawaian'      => ['required', 'array', 'min:1'],
            'status_kepegawaian.*'    => ['string', 'in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN'],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_mulai_perbaikan' => ['nullable', 'date'],
            'tanggal_selesai_perbaikan' => ['nullable', 'date'],
            'status'                  => ['required', 'in:Buka,Tutup'],
            'senat_min_setuju' => ['nullable', 'integer', 'min:0'],
        ];
        
        // Hanya tambahkan validasi overlap jika diperlukan
        if ($needsOverlapValidation) {
            $validationRules['tanggal_mulai'][] = new NoDateRangeOverlap(
                table: 'periode_usulans',
                startColumn: 'tanggal_mulai',
                endColumn: 'tanggal_selesai',
                filters: [
                    'jenis_usulan' => $request->input('jenis_usulan')
                ],
                excludeId: $periodeUsulan->id
            );
        }

        // Conditional validation untuk tanggal perbaikan
        if ($request->filled('tanggal_mulai_perbaikan')) {
            $validationRules['tanggal_mulai_perbaikan'][] = 'after_or_equal:tanggal_selesai';
        }

        if ($request->filled('tanggal_selesai_perbaikan')) {
            $validationRules['tanggal_selesai_perbaikan'][] = 'after_or_equal:tanggal_mulai_perbaikan';
        }

        // Debug logging untuk validation rules
        \Log::info('PeriodeUsulan Update Validation Rules', [
            'rules' => $validationRules,
            'needs_overlap_validation' => $needsOverlapValidation
        ]);

        try {
            $validated = $request->validate($validationRules);
            
            // Debug logging untuk validation success
            \Log::info('PeriodeUsulan Update Validation Success', [
                'validated_data' => $validated
            ]);
            
                    DB::transaction(function () use ($request, $periodeUsulan) {
            $periodeUsulan->nama_periode      = $request->input('nama_periode');
            $periodeUsulan->jenis_usulan      = $request->input('jenis_usulan');
            $periodeUsulan->status_kepegawaian = $request->input('status_kepegawaian');
            $periodeUsulan->tanggal_mulai     = $request->input('tanggal_mulai');
            $periodeUsulan->tanggal_selesai   = $request->input('tanggal_selesai');
            $periodeUsulan->tanggal_mulai_perbaikan = $request->input('tanggal_mulai_perbaikan');
            $periodeUsulan->tanggal_selesai_perbaikan = $request->input('tanggal_selesai_perbaikan');
            $periodeUsulan->status            = $request->input('status');
            $periodeUsulan->senat_min_setuju  = (int) $request->input('senat_min_setuju', $periodeUsulan->senat_min_setuju ?? 0);
                
                                // Debug logging untuk data yang akan disimpan
                \Log::info('PeriodeUsulan Update Data to Save', [
                    'data' => [
                        'nama_periode' => $periodeUsulan->nama_periode,
                        'jenis_usulan' => $periodeUsulan->jenis_usulan,
                        'status_kepegawaian' => $periodeUsulan->status_kepegawaian,
                        'tanggal_mulai' => $periodeUsulan->tanggal_mulai,
                        'tanggal_selesai' => $periodeUsulan->tanggal_selesai,
                        'status' => $periodeUsulan->status,
                        'senat_min_setuju' => $periodeUsulan->senat_min_setuju,
                    ]
                ]);

                $periodeUsulan->save();

                // Debug logging untuk save success
                \Log::info('PeriodeUsulan Update Save Success', [
                    'periode_id' => $periodeUsulan->id
                ]);
            });

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'jabatan-dosen-regular' => 'jabatan-dosen-regular',
                'jabatan-dosen-pengangkatan' => 'jabatan-dosen-pengangkatan',
                'usulan-nuptk' => 'nuptk',
                'usulan-laporan-lkd' => 'laporan-lkd',
                'usulan-presensi' => 'presensi',
                'usulan-penyesuaian-masa-kerja' => 'penyesuaian-masa-kerja',
                'usulan-ujian-dinas-ijazah' => 'ujian-dinas-ijazah',
                'usulan-laporan-serdos' => 'laporan-serdos',
                'usulan-pensiun' => 'pensiun',
                'usulan-kepangkatan' => 'kepangkatan',
                'usulan-pencantuman-gelar' => 'pencantuman-gelar',
                'usulan-id-sinta-sister' => 'id-sinta-sister',
                'usulan-satyalancana' => 'satyalancana',
                'usulan-tugas-belajar' => 'tugas-belajar',
                'usulan-pengaktifan-kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$periodeUsulan->jenis_usulan] ?? 'jabatan-dosen-regular';

            return redirect()->route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $request->input('nama_periode') . '" berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Debug logging untuk validation error
            \Log::error('PeriodeUsulan Update Validation Error', [
                'errors' => $e->errors(),
                'periode_id' => $periodeUsulan->id
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', '❌ Validasi gagal. Silakan periksa kembali data yang dimasukkan.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Debug logging untuk database error
            \Log::error('PeriodeUsulan Update Database Error', [
                'error' => $e->getMessage(),
                'periode_id' => $periodeUsulan->id
            ]);
            report($e);
            return back()->withInput()->with('error', '❌ Gagal memperbarui periode usulan. Silakan coba lagi.');
        } catch (\Throwable $e) {
            // Debug logging untuk general error
            \Log::error('PeriodeUsulan Update General Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'periode_id' => $periodeUsulan->id
            ]);
            report($e);
            return back()->withInput()->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Menghapus resource dari storage.
     */
    public function destroy(PeriodeUsulan $periodeUsulan)
    {
        try {
            if ($periodeUsulan->usulans()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menghapus! Periode "' . $periodeUsulan->nama_periode . '" sudah memiliki pendaftar.'
                    ]);
                }
                return back()->with('error', '❌ Gagal menghapus! Periode "' . $periodeUsulan->nama_periode . '" sudah memiliki pendaftar.');
            }

            // Simpan jenis usulan sebelum dihapus untuk redirect
            $jenisUsulan = $periodeUsulan->jenis_usulan;
            $namaPeriode = $periodeUsulan->nama_periode;

            $periodeUsulan->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Periode usulan "' . $namaPeriode . '" berhasil dihapus!'
                ]);
            }

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'jabatan-dosen-regular' => 'jabatan-dosen-regular',
                'jabatan-dosen-pengangkatan' => 'jabatan-dosen-pengangkatan',
                'usulan-nuptk' => 'nuptk',
                'usulan-laporan-lkd' => 'laporan-lkd',
                'usulan-presensi' => 'presensi',
                'usulan-penyesuaian-masa-kerja' => 'penyesuaian-masa-kerja',
                'usulan-ujian-dinas-ijazah' => 'ujian-dinas-ijazah',
                'usulan-laporan-serdos' => 'laporan-serdos',
                'usulan-pensiun' => 'pensiun',
                'usulan-kepangkatan' => 'kepangkatan',
                'usulan-pencantuman-gelar' => 'pencantuman-gelar',
                'usulan-id-sinta-sister' => 'id-sinta-sister',
                'usulan-satyalancana' => 'satyalancana',
                'usulan-tugas-belajar' => 'tugas-belajar',
                'usulan-pengaktifan-kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$jenisUsulan] ?? 'jabatan-dosen-regular';

            return redirect()->route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $namaPeriode . '" berhasil dihapus!');
        } catch (\Throwable $e) {
            report($e);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus periode usulan. Silakan coba lagi.'
                ]);
            }
            return back()->with('error', '❌ Terjadi kesalahan saat menghapus periode usulan. Silakan coba lagi.');
        }
    }

    /**
     * Validasi overlap berdasarkan jenis usulan yang sama
     */
    private function validateOverlapByJenisUsulan(Request $request, $excludeId = null)
    {
        $jenisUsulan = $request->jenis_usulan;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $query = PeriodeUsulan::where('jenis_usulan', $jenisUsulan)
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhere(function ($subQ) use ($tanggalMulai, $tanggalSelesai) {
                      $subQ->where('tanggal_mulai', '<=', $tanggalMulai)
                           ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $overlappingPeriode = $query->first();

        if ($overlappingPeriode) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal_mulai' => [
                    "Tanggal periode overlapping dengan periode '{$overlappingPeriode->nama_periode}' yang memiliki jenis usulan yang sama ({$jenisUsulan})"
                ]
            ]);
        }
    }

    /**
     * API untuk mendapatkan count usulan kepangkatan per jenis
     */
    public function getUsulanKepangkatanCount(PeriodeUsulan $periodeUsulan)
    {
        try {
            // Pastikan periode ini adalah jenis kepangkatan
            if ($periodeUsulan->jenis_usulan !== 'usulan-kepangkatan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode ini bukan jenis usulan kepangkatan'
                ], 400);
            }

            // Ambil semua usulan dari periode ini
            $usulans = $periodeUsulan->usulans()->get();

            // Hitung berdasarkan jenis_usulan_pangkat
            $counts = [
                'dosen_pns' => 0,
                'jabatan_administrasi' => 0,
                'jabatan_fungsional' => 0,
                'jabatan_struktural' => 0
            ];

            foreach ($usulans as $usulan) {
                $jenisUsulanPangkat = $usulan->data_usulan['jenis_usulan_pangkat'] ?? null;
                
                if ($jenisUsulanPangkat) {
                    switch ($jenisUsulanPangkat) {
                        case 'Dosen PNS':
                            $counts['dosen_pns']++;
                            break;
                        case 'Jabatan Administrasi':
                            $counts['jabatan_administrasi']++;
                            break;
                        case 'Jabatan Fungsional Tertentu':
                            $counts['jabatan_fungsional']++;
                            break;
                        case 'Jabatan Struktural':
                            $counts['jabatan_struktural']++;
                            break;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'counts' => $counts,
                'total' => $usulans->count()
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung usulan kepangkatan'
            ], 500);
        }
    }
}

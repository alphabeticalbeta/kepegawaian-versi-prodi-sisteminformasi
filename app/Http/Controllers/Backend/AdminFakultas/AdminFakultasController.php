<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;
use App\Services\ValidationService;

/**
 * Admin Fakultas Controller
 *
 * Controller untuk mengelola fitur-fitur admin fakultas termasuk:
 * - Dashboard dengan statistik usulan
 * - Hybrid approach untuk usulan jabatan & pangkat
 * - Validasi dan approval usulan
 *
 * HYBRID APPROACH ARCHITECTURE:
 * - Separate routes: /usulan/jabatan, /usulan/pangkat
 * - Shared view template: index-dynamic.blade.php
 * - Dynamic configuration based on type
 * - Performance optimized with caching
 *
 * @package App\Http\Controllers\Backend\AdminFakultas
 * @author Development Team
 * @version 2.0 - Hybrid Approach Implementation
 */

class AdminFakultasController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }
    /**
    * Menampilkan dashboard dengan daftar usulan untuk fakultas terkait.
    */
    public function dashboard()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        // Gunakan helper method untuk mendapatkan periode usulan
        $periodeUsulans = $this->getPeriodeUsulanWithCount($unitKerja);

        // Get dashboard statistics
        $statistics = $this->getDashboardStatistics($periodeUsulans, $unitKerja);

        return view('backend.layouts.views.admin-fakultas.dashboard', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }

    /**
     * Dashboard khusus untuk usulan jabatan.
     */
    public function dashboardJabatan()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        if (!$unitKerja) {
            return view('backend.layouts.views.admin-fakultas.usulan.dashboard-jabatan', [
                'periodeUsulans' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'unitKerja' => null,
                'statistics' => [
                    'total_periode' => 0,
                    'total_usulan' => 0,
                    'menunggu_validasi' => 0,
                    'dikirim_universitas' => 0,
                    'perbaikan' => 0,
                    'disetujui' => 0,
                    'ditolak' => 0
                ]
            ]);
        }

        $unitKerjaId = $unitKerja->id;

        // Get periode usulan khusus jabatan - Show all periods for jabatan
        $periodeUsulans = PeriodeUsulan::query()
            ->whereIn('jenis_usulan', ['jabatan', 'Usulan Jabatan', 'usulan-jabatan-dosen', 'usulan-jabatan-tendik'])
            ->withCount([
                'usulans as total_usulan' => function ($query) use ($unitKerjaId) {
                    $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
                },
                'usulans as menunggu_validasi' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Draft', 'Menunggu Verifikasi'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as dikirim_universitas' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as perbaikan' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Perbaikan Usulan', 'Dikembalikan'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as disetujui' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Disetujui', 'Direkomendasikan'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as ditolak' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Ditolak', 'Tidak Disetujui'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $statistics = [
            'total_periode' => $periodeUsulans->total(),
            'total_usulan' => $periodeUsulans->sum('total_usulan'),
            'menunggu_validasi' => $periodeUsulans->sum('menunggu_validasi'),
            'dikirim_universitas' => $periodeUsulans->sum('dikirim_universitas'),
            'perbaikan' => $periodeUsulans->sum('perbaikan'),
            'disetujui' => $periodeUsulans->sum('disetujui'),
            'ditolak' => $periodeUsulans->sum('ditolak')
        ];

        return view('backend.layouts.views.admin-fakultas.usulan.dashboard-jabatan', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }

    /**
     * Dashboard khusus untuk usulan pangkat.
     */
    public function dashboardPangkat()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        if (!$unitKerja) {
            return view('backend.layouts.views.admin-fakultas.usulan.dashboard-pangkat', [
                'periodeUsulans' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'unitKerja' => null,
                'statistics' => [
                    'total_periode' => 0,
                    'total_usulan' => 0,
                    'menunggu_validasi' => 0,
                    'dikirim_universitas' => 0,
                    'perbaikan' => 0,
                    'disetujui' => 0,
                    'ditolak' => 0
                ]
            ]);
        }

        $unitKerjaId = $unitKerja->id;

        // Get periode usulan khusus pangkat - Show all periods for pangkat
        $periodeUsulans = PeriodeUsulan::query()
            ->whereIn('jenis_usulan', ['pangkat', 'Usulan Kepangkatan', 'kepangkatan'])
            ->withCount([
                'usulans as total_usulan' => function ($query) use ($unitKerjaId) {
                    $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
                },
                'usulans as menunggu_validasi' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Draft', 'Menunggu Verifikasi'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as dikirim_universitas' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as perbaikan' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Perbaikan Usulan', 'Dikembalikan'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as disetujui' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Disetujui', 'Direkomendasikan'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as ditolak' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Ditolak', 'Tidak Disetujui'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $statistics = [
            'total_periode' => $periodeUsulans->total(),
            'total_usulan' => $periodeUsulans->sum('total_usulan'),
            'menunggu_validasi' => $periodeUsulans->sum('menunggu_validasi'),
            'dikirim_universitas' => $periodeUsulans->sum('dikirim_universitas'),
            'perbaikan' => $periodeUsulans->sum('perbaikan'),
            'disetujui' => $periodeUsulans->sum('disetujui'),
            'ditolak' => $periodeUsulans->sum('ditolak')
        ];

        return view('backend.layouts.views.admin-fakultas.usulan.dashboard-pangkat', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }



    /**
     * Menampilkan detail satu usulan spesifik untuk VALIDASI.
     * OPTIMIZED: Menggunakan query scopes dan caching untuk performa optimal
     */
    public function show(Usulan $usulan)
    {
        try {
            /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
            $admin = Auth::user();

            // OPTIMASI: Cache admin data untuk mengurangi query
            $adminFakultasId = Cache::remember("admin_fakultas_id_{$admin->id}", 300, function () use ($admin) {
                return $admin->unit_kerja_id;
            });

            // OPTIMASI: Gunakan eager loading yang optimal
            $usulan->load([
                'pegawai:id,nama_lengkap,email,nip,gelar_depan,gelar_belakang,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id,jenis_pegawai,status_kepegawaian,nuptk,tempat_lahir,tanggal_lahir,jenis_kelamin,nomor_handphone,nomor_kartu_pegawai,tmt_pangkat,tmt_jabatan,tmt_cpns,tmt_pns,pendidikan_terakhir,nama_universitas_sekolah,nama_prodi_jurusan,mata_kuliah_diampu,ranting_ilmu_kepakaran,url_profil_sinta,predikat_kinerja_tahun_pertama,predikat_kinerja_tahun_kedua,nilai_konversi,ijazah_terakhir,transkrip_nilai_terakhir,sk_pangkat_terakhir,sk_jabatan_terakhir,skp_tahun_pertama,skp_tahun_kedua,pak_konversi,sk_cpns,sk_pns,sk_penyetaraan_ijazah,disertasi_thesis_terakhir',
                'pegawai.pangkat:id,pangkat',
                'pegawai.jabatan:id,jabatan',
                'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai,status',
                'dokumens:id,usulan_id,nama_dokumen,path',
                'logs:id,usulan_id,status_baru,catatan,created_at,dilakukan_oleh_id',
                'logs.dilakukanOleh:id,nama_lengkap'
            ]);

            // OPTIMASI: Cache fakultas ID untuk authorization check
            $usulanPegawaiFakultasId = Cache::remember("usulan_fakultas_{$usulan->id}", 300, function () use ($usulan) {
                return $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;
            });

            if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
                Log::warning('Admin Fakultas mencoba akses usulan dari fakultas lain.', [
                    'admin_id' => $admin->id,
                    'admin_fakultas_id' => $adminFakultasId,
                    'usulan_id' => $usulan->id,
                    'usulan_fakultas_id' => $usulanPegawaiFakultasId
                ]);
                return redirect()->route('admin-fakultas.dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak berhak melihat usulan dari fakultas lain.');
            }

            // OPTIMASI: Cache validation fields
            $validationFields = Cache::remember("validation_fields_{$usulan->id}_admin_fakultas", 300, function () use ($usulan) {
                return \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_fakultas');
            });

            // OPTIMASI: Cache BKD labels
            $bkdLabels = Cache::remember("bkd_labels_{$usulan->id}", 300, function () use ($usulan) {
                return $usulan->getBkdDisplayLabels();
            });

            // Get validation data directly from database (no cache for now)
            $existingValidation = $usulan->getValidasiByRole('admin_fakultas');
            \Log::info('Loading validation data directly', [
                'usulan_id' => $usulan->id,
                'validation_keys' => array_keys($existingValidation),
                'has_validation_key' => isset($existingValidation['validation'])
            ]);

            // OPTIMASI: Cache dokumen data
            $dokumenData = Cache::remember("dokumen_data_{$usulan->id}", 300, function () use ($usulan) {
                return $this->processDokumenDataForView($usulan);
            });

            // Get penilais data for popup
            $penilais = \App\Models\BackendUnivUsulan\Pegawai::whereHas('roles', function($query) {
                $query->where('name', 'Penilai Universitas');
            })->orderBy('nama_lengkap')->get();

            return view('backend.layouts.views.admin-fakultas.usulan.detail', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
                'bkdLabels' => $bkdLabels,
                'dokumenData' => $dokumenData,
                'penilais' => $penilais,
                // Multi-role configuration
                'currentRole' => 'admin_fakultas',
                'formAction' => route('admin-fakultas.usulan.save-validation', $usulan->id),
                'backUrl' => route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id),
                'backText' => 'Kembali ke Daftar Pengusul',
                'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan']),
                'roleConfig' => [
                    'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan']),
                    'canView' => true, // Always allow viewing data
                    'submitFunctions' => ['save', 'return_to_pegawai', 'reject_to_pegawai', 'forward_to_university', 'resend_to_university']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail usulan: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'admin_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data detail usulan. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan hasil validasi admin fakultas.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {
        // Dispatcher: prioritise explicit action_type (complex flows)
        if ($request->filled('action_type')) {
            return $this->saveComplexValidation($request, $usulan);
        }

        // Fallback to simple validation (legacy submit with 'action')
        if ($request->has('validation')) {
            return $this->saveSimpleValidation($request, $usulan);
        }

        return redirect()->back()->with('error', 'Permintaan tidak dikenali.');
    }

    /**
     * Save simple validation form
     */
    private function saveSimpleValidation(Request $request, Usulan $usulan)
    {
        $validatedData = $request->validate([
            'validation' => 'required|array',
            'action' => 'required|in:save_draft,submit'
        ]);

        $adminId = Auth::id();
        $action = $validatedData['action'];
        $validationData = $validatedData['validation'];

        DB::beginTransaction();
        try {
            // Save validation data
            $usulan->setValidasiByRole('admin_fakultas', $validationData, $adminId);

            // Update status based on action
            if ($action === 'submit') {
                $usulan->status_usulan = 'Diusulkan ke Universitas';
                $logMessage = 'Usulan diteruskan ke universitas';
            } else {
                $logMessage = 'Draft validasi disimpan';
            }

            $usulan->save();

            // Create log
            $this->createUsulanLog($usulan, $usulan->status_usulan, $logMessage, $adminId);

            DB::commit();

            return redirect()->back()->with('success', $logMessage . ' berhasil.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan validasi: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'admin_id' => $adminId,
                'action' => $action
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan validasi.');
        }
    }

    /**
     * Save complex validation form (existing method)
     */
    private function saveComplexValidation(Request $request, Usulan $usulan)
    {
        // Ambil data awal yang dibutuhkan untuk semua aksi
        $actionType = $request->input('action_type', 'save_only');
        $adminId = Auth::id();
        $statusLama = $usulan->status_usulan;

        DB::beginTransaction();
        try {
            switch ($actionType) {
                case 'return_to_pegawai':
                    // Validasi khusus untuk aksi 'kembalikan ke pegawai'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_umum' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_umum.required' => 'Catatan untuk pegawai wajib diisi.',
                        'catatan_umum.min' => 'Catatan untuk pegawai minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    $invalidFields = $usulan->getInvalidFields('admin_fakultas');

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan dikembalikan oleh Admin Fakultas untuk perbaikan."];
                    if (count($invalidFields) > 0) {
                        $catatanDetail[] = "\nItem yang perlu diperbaiki:";
                        foreach ($invalidFields as $field) {
                            $catatanDetail[] = "• " . ucwords(str_replace('_', ' ', $field['field'])) . ": {$field['keterangan']}";
                        }
                    }
                    $catatanDetail[] = "\nCatatan Tambahan:";
                    $catatanDetail[] = $validatedData['catatan_umum'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan
                    $usulan->status_usulan = 'Perbaikan Usulan';
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    break;

                case 'reject_to_pegawai':
                    // Validasi khusus untuk aksi 'belum direkomendasikan'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_reject' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_reject.required' => 'Alasan belum direkomendasikan wajib diisi.',
                        'catatan_reject.min' => 'Alasan belum direkomendasikan minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan belum dapat direkomendasikan oleh Admin Fakultas."];
                    $catatanDetail[] = "\nAlasan:";
                    $catatanDetail[] = $validatedData['catatan_reject'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan - mencegah submit di periode ini
                    $usulan->status_usulan = 'Ditolak';
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan ditandai belum direkomendasikan oleh Admin Fakultas.';
                    break;

                case 'forward_to_university':
                    // Validasi khusus untuk aksi 'teruskan ke universitas'
                    // Beda validasi untuk usulan pertama kali vs perbaikan
                    $rules = [
                        'validation' => 'required|array',
                    ];

                    $messages = [];

                    // Jika status "Perbaikan Usulan", maka perlu dokumen pendukung
                    if ($usulan->status_usulan === 'Perbaikan Usulan') {
                        $rules['dokumen_pendukung.nomor_surat_usulan'] = 'required|string|max:255';
                        $rules['dokumen_pendukung.nomor_berita_senat'] = 'required|string|max:255';
                        $rules['dokumen_pendukung.file_surat_usulan'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung.file_berita_senat'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_surat_usulan]'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_berita_senat]'] = 'nullable|file|mimes:pdf|max:2048';
                        
                        $messages['dokumen_pendukung.nomor_surat_usulan.required'] = 'Nomor surat usulan wajib diisi.';
                        $messages['dokumen_pendukung.nomor_berita_senat.required'] = 'Nomor berita senat wajib diisi.';
                        $messages['dokumen_pendukung.file_surat_usulan.mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung.file_berita_senat.mimes'] = 'File berita senat harus berformat PDF.';
                        $messages['dokumen_pendukung[file_surat_usulan].mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung[file_berita_senat].mimes'] = 'File berita senat harus berformat PDF.';
                    }

                    $validatedData = $request->validate($rules, $messages);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Cek lagi setelah data validasi disimpan
                    if ($usulan->hasInvalidFields('admin_fakultas')) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'validation' => 'Tidak dapat meneruskan usulan. Masih ada item yang tidak sesuai dalam validasi.'
                        ]);
                    }

                    // Hanya proses dokumen pendukung jika status "Perbaikan Usulan"
                    if ($usulan->status_usulan === 'Perbaikan Usulan') {
                        // Simpan dokumen pendukung fakultas
                        $currentValidasi = $usulan->validasi_data;
                        $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                        // Update text fields
                        $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                        $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];

                        // Handle file uploads menggunakan FileStorageService
                        $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_surat_usulan',
                            'dokumen-fakultas/surat-usulan'
                        );

                        $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_berita_senat',
                            'dokumen-fakultas/berita-senat'
                        );

                        $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                        $usulan->validasi_data = $currentValidasi;

                        // Final check untuk memastikan file sudah ada (only if files were uploaded)
                        if (!empty($currentDokumenPendukung['file_surat_usulan_path']) || !empty($currentDokumenPendukung['file_berita_senat_path'])) {
                            // If one file is uploaded, both should be uploaded
                            if (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path'])) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'file_upload' => 'Jika mengunggah file, kedua file (surat usulan dan berita senat) harus diunggah.'
                                ]);
                            }
                        }
                    }

                    // Log message sesuai kondisi sebelum update status
                    $statusLama = $usulan->status_usulan;
                    if ($statusLama === 'Perbaikan Usulan') {
                        $logMessage = 'Usulan diperbaiki dan dikirim kembali ke Universitas.';
                    } else {
                        $logMessage = 'Usulan divalidasi dan diteruskan ke Universitas.';
                    }
                    
                    // Update status usulan
                    $usulan->status_usulan = 'Diusulkan ke Universitas';
                    break;

                                case 'resend_to_university':
                    // Validasi lengkap untuk memastikan semua data valid
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'dokumen_pendukung' => 'nullable|array',
                        'dokumen_pendukung.nomor_surat_usulan' => 'nullable|string|max:255',
                        'dokumen_pendukung.nomor_berita_senat' => 'nullable|string|max:255',
                        'dokumen_pendukung.file_surat_usulan' => 'nullable|file|mimes:pdf|max:2048',
                        'dokumen_pendukung.file_berita_senat' => 'nullable|file|mimes:pdf|max:2048',
                        // Support bracket notation for compatibility
                        'dokumen_pendukung[file_surat_usulan]' => 'nullable|file|mimes:pdf|max:2048',
                        'dokumen_pendukung[file_berita_senat]' => 'nullable|file|mimes:pdf|max:2048'
                    ]);

                    // Simpan validasi data
                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Cek apakah masih ada field yang tidak valid
                    if ($usulan->hasInvalidFields('admin_fakultas')) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'validation' => 'Tidak dapat mengirim kembali usulan. Masih ada item yang tidak sesuai dalam validasi.'
                        ]);
                    }

                    // Log request details sebelum validasi
                    Log::info('Request details before dokumen pendukung validation', [
                        'usulan_id' => $usulan->id,
                        'action_type' => $actionType,
                        'request_method' => $request->method(),
                        'content_type' => $request->header('Content-Type'),
                        'all_request_keys' => array_keys($request->all()),
                        'has_files' => $request->hasFile('dokumen_pendukung'),
                        'files_data' => $request->allFiles(),
                        'dokumen_pendukung_data' => $request->input('dokumen_pendukung'),
                        'raw_files' => $_FILES ?? []
                    ]);

                    // Additional debugging for file detection
                    Log::info('Request all files', $request->allFiles() ?: []);
                    Log::info('Request file dokumen_pendukung', $request->file('dokumen_pendukung') ?: []);

                    // Validasi dokumen pendukung menggunakan service dengan logging detail
                    Log::info('Starting dokumen pendukung validation', [
                        'usulan_id' => $usulan->id,
                        'action_type' => $actionType,
                        'request_keys' => array_keys($request->all()),
                        'has_files' => $request->hasFile('dokumen_pendukung'),
                        'content_type' => $request->header('Content-Type')
                    ]);

                    $dokumenErrors = $this->validationService->validateDokumenPendukung($request, $usulan, 'admin_fakultas');

                    Log::info('Dokumen pendukung validation completed', [
                        'usulan_id' => $usulan->id,
                        'errors_count' => count($dokumenErrors),
                        'errors' => $dokumenErrors
                    ]);

                    if (!empty($dokumenErrors)) {
                        Log::warning('Dokumen pendukung validation failed', [
                            'usulan_id' => $usulan->id,
                            'errors' => $dokumenErrors
                        ]);

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'dokumen_pendukung' => $dokumenErrors
                        ]);
                    }

                    // Update dokumen pendukung menggunakan FileStorageService
                    if (!empty($validatedData['dokumen_pendukung'])) {
                        $currentValidasi = $usulan->validasi_data;
                        $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                        // Update text fields
                        if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
                            $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                        }
                        if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
                            $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
                        }

                        // Handle file uploads menggunakan FileStorageService
                        $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_surat_usulan',
                            'dokumen-fakultas/surat-usulan'
                        );

                        $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_berita_senat',
                            'dokumen-fakultas/berita-senat'
                        );

                        $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                        $usulan->validasi_data = $currentValidasi;
                    }

                    // Update status usulan
                    $usulan->status_usulan = 'Diusulkan ke Universitas';

                    $logMessage = 'Usulan berhasil diperbaiki dan dikirim kembali ke Universitas.';
                    break;

                default: // save_only
                    // Validasi khusus untuk aksi 'simpan saja'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // DON'T change status automatically - keep it as 'Diajukan' until dokumen pendukung is filled
                    $logMessage = 'Hasil validasi disimpan oleh Admin Fakultas.';
                    break;
            }

            $usulan->save();
            $usulan->createLog($usulan->status_usulan, $statusLama, $logMessage, $adminId);

            // OPTIMASI: Clear cache setelah data berubah
            $this->clearUsulanCache($usulan);
            $this->clearAdminCache($adminId);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $logMessage,
                    'status' => $usulan->status_usulan,
                ]);
            }

            return redirect()->route('admin-fakultas.dashboard')->with('success', 'Aksi pada usulan berhasil diproses.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid. Silakan periksa kembali.',
                    'errors' => $e->errors(),
                ], 422);
            }
            // Penting: Mengembalikan ke halaman sebelumnya dengan error dan input lama
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Data yang dimasukkan tidak valid. Silakan periksa kembali.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan validasi: ' . $e->getMessage(), ['usulan_id' => $usulan->id]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses validasi.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses validasi.');
        }
    }

    /**
     * Menampilkan daftar pengusul per periode.
     */
    public function showPendaftar(PeriodeUsulan $periodeUsulan)
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();
        $unitKerjaId = $admin->unit_kerja_id;

        // PERBAIKAN: Tampilkan semua usulan (bukan hanya yang berstatus "Diajukan")
        // agar admin fakultas bisa melihat riwayat semua usulan yang pernah mereka proses
        $usulans = Usulan::query()
            ->where('periode_usulan_id', $periodeUsulan->id)
            // HAPUS filter status_usulan agar semua usulan ditampilkan
            // ->where('status_usulan', 'Diajukan')
            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($unitKerjaId) {
                $query->where('id', $unitKerjaId);
            })
            ->with(['pegawai', 'jabatanLama', 'jabatanTujuan'])
            ->latest()
            ->paginate(15);

        return view('backend.layouts.views.admin-fakultas.usulan.pengusul', [
            'periode' => $periodeUsulan,
            'usulans' => $usulans,
        ]);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        // Authorization check untuk admin fakultas
        $admin = Auth::user();
        $adminFakultasId = $admin->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat dokumen dari fakultas lain.');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        if (str_starts_with($field, 'bkd_')) {
            $allowedFields[] = $field;
        }

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Cari path file menggunakan method getDocumentPath
        $filePath = $usulan->getDocumentPath($field);

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk and check file existence
        $disk = 'local'; // Dokumen usulan selalu disimpan di local disk
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show dokumen profil pegawai untuk Admin Fakultas
     */
    public function showPegawaiDocument(Usulan $usulan, $field)
    {
        // Authorization check
        $admin = Auth::user();
        $adminFakultasId = $admin->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat dokumen dari fakultas lain.');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen profil tidak valid.');
        }

        // Ambil path dokumen dari pegawai
        $filePath = $usulan->pegawai->{$field} ?? null;

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk and check file existence
        $disk = $this->getFileDisk($field);
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show dokumen pendukung fakultas
     */
    public function showDokumenPendukung(Usulan $usulan, $field)
    {
        // Authorization check
        $admin = Auth::user();
        $adminFakultasId = $admin->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi field
        $allowedFields = ['file_surat_usulan', 'file_berita_senat'];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen pendukung tidak valid.');
        }

        // Ambil path dari validasi data
        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
        $pathKey = $field . '_path';
        $filePath = $dokumenPendukung[$pathKey] ?? null;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File dokumen pendukung tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    /**
     * Helper method untuk mendapatkan unit kerja admin fakultas
     */
    private function getAdminUnitKerja($admin)
    {
        try {
            // Coba ambil unit kerja langsung dari admin
            if ($admin->unit_kerja_id) {
                return \App\Models\BackendUnivUsulan\UnitKerja::find($admin->unit_kerja_id);
            }

            // Fallback: ambil dari hierarki jika admin tidak punya unit_kerja_id
            if ($admin->unit_kerja_terakhir_id) {
                $subSubUnit = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with('subUnitKerja.unitKerja')
                    ->find($admin->unit_kerja_terakhir_id);

                if ($subSubUnit && $subSubUnit->subUnitKerja && $subSubUnit->subUnitKerja->unitKerja) {
                    return $subSubUnit->subUnitKerja->unitKerja;
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting admin unit kerja: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper method untuk mendapatkan periode usulan dengan count
     */
    private function getPeriodeUsulanWithCount($unitKerja)
    {
        if (!$unitKerja) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        try {
            return \App\Models\BackendUnivUsulan\PeriodeUsulan::withCount([
                'usulans as jumlah_pengusul' => function ($query) use ($unitKerja) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                            $subQuery->where('id', $unitKerja->id);
                        });
                },
                'usulans as perbaikan' => function ($query) use ($unitKerja) {
                    $query->whereIn('status_usulan', ['Perbaikan Usulan', 'Dikembalikan'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                            $subQuery->where('id', $unitKerja->id);
                        });
                },
                'usulans as total_usulan' => function ($query) use ($unitKerja) {
                    $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                        $subQuery->where('id', $unitKerja->id);
                    });
                }
            ])->latest()->paginate(10);
        } catch (\Exception $e) {
            \Log::error('Error getting periode usulan: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
    }

    private function getDashboardStatistics($periodeUsulans, $unitKerja)
    {
        return [
            'total_periode' => $periodeUsulans->total(),
            'total_pengusul' => $periodeUsulans->sum('jumlah_pengusul'),
            'total_perbaikan' => $periodeUsulans->sum('perbaikan'),
            'total_usulan' => $periodeUsulans->sum('total_usulan'),
            'unit_kerja_name' => $unitKerja ? $unitKerja->nama : 'Tidak diketahui',
            'has_pending_review' => $periodeUsulans->sum('jumlah_pengusul') > 0,
            'has_perbaikan' => $periodeUsulans->sum('perbaikan') > 0
        ];
    }




    /**
     * Get formatted status badge untuk UI
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'Buka':
                return [
                    'class' => 'bg-green-100 text-green-800',
                    'text' => 'Buka'
                ];
            case 'Tutup':
                return [
                    'class' => 'bg-red-100 text-red-800',
                    'text' => 'Tutup'
                ];
            default:
                return [
                    'class' => 'bg-gray-100 text-gray-800',
                    'text' => $status
                ];
        }
    }

    /**
 * FIXED: Enhanced autosave method yang memastikan semua data tersimpan
 * Tambahkan method ini ke AdminFakultasController
 */
    public function autosaveValidation(Request $request, Usulan $usulan)
    {
        try {
            \Log::info('Autosave request received', [
                'usulan_id' => $usulan->id,
                'request_data_keys' => array_keys($request->all()),
                'validation_keys' => array_keys($request->input('validation', []))
            ]);

            // Enhanced validation
            $validatedData = $request->validate([
                'validation' => 'required|array',
                'validation.*' => 'array',
                'validation.*.*' => 'array',
                'validation.*.*.status' => 'required|in:sesuai,tidak_sesuai',
                'validation.*.*.keterangan' => 'nullable|string',
                'action_type' => 'required|in:save_only'
            ]);

            // Quick authorization check
            $admin = Auth::user();
            if ($admin->unit_kerja_id !== $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Use the improved setValidasiByRole method for consistency
            $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $admin->id);

            // DON'T change status automatically - Admin Fakultas needs to fill dokumen pendukung first
            // Status should remain 'Diajukan' until Admin Fakultas explicitly submits with dokumen pendukung

            $usulan->save();

            // Clear all related caches untuk memastikan data terbaru dimuat saat reload
            Cache::forget("existing_validation_{$usulan->id}_admin_fakultas");
            Cache::forget("validation_fields_{$usulan->id}_admin_fakultas");
            Cache::forget("bkd_labels_{$usulan->id}");
            Cache::forget("dokumen_data_{$usulan->id}");
            Cache::forget("usulan_fakultas_{$usulan->id}");
            Cache::forget("admin_fakultas_id_" . Auth::id());

            // Clear all cache patterns related to this usulan
            $cacheKeys = Cache::get('cache_keys') ?? [];
            foreach ($cacheKeys as $key) {
                if (str_contains($key, "{$usulan->id}") || str_contains($key, 'admin_fakultas')) {
                    Cache::forget($key);
                }
            }

            // Get the saved validation data for logging
            $savedValidation = $usulan->getValidasiByRole('admin_fakultas');
            $validationData = $savedValidation['validation'] ?? [];

            // Log success dengan detail
            \Log::info('Autosave successful', [
                'usulan_id' => $usulan->id,
                'saved_categories' => array_keys($validationData),
                'total_fields_saved' => array_sum(array_map('count', $validationData)),
                'status' => $usulan->status_usulan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'saved_fields' => array_sum(array_map('count', $validationData)),
                'categories' => array_keys($validationData)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Autosave validation failed', [
                'usulan_id' => $usulan->id,
                'errors' => $e->errors(),
                'input' => $request->input('validation', [])
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Autosave error: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Save failed'], 500);
        }
    }

    /**
     * NEW: Process validation fields dengan data dari helper
     */
    private function processValidationFieldsForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        $validationFields = Usulan::getValidationFieldsWithDynamicBkd($usulan);
        $processedFields = [];

        foreach ($validationFields as $category => $fields) {
            $processedFields[$category] = [];

            foreach ($fields as $field) {
                // Get processed data from helper
                $fieldValue = $fieldHelper->getFieldValue($category, $field);

                $processedFields[$category][$field] = [
                    'raw_value' => $fieldValue,
                    'display_value' => $fieldValue,
                    'has_link' => $this->checkIfFieldHasLink($fieldValue),
                    'field_type' => $this->determineFieldType($category, $field)
                ];
            }
        }

        return $processedFields;
    }

    /**
     * NEW: Process dokumen data untuk ditampilkan di view
     */
    private function processDokumenDataForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        return [
            'dokumen_profil' => $this->processDokumenProfil($usulan, $fieldHelper),
            'dokumen_usulan' => $this->processDokumenUsulan($usulan, $fieldHelper),
            'dokumen_bkd' => $this->processDokumenBkd($usulan, $fieldHelper),
            'dokumen_pendukung' => $this->processDokumenPendukung($usulan, $fieldHelper)
        ];
    }

    /**
     * SAFE: Process dokumen profil dengan override routing untuk Admin Fakultas
     */
    private function processDokumenProfil(Usulan $usulan, $fieldHelper): array
    {
        $profilFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir'
        ];

        $processed = [];
        foreach ($profilFields as $field) {
            // Ambil raw data dari pegawai (TANPA helper routing)
            $dokumenPath = $usulan->pegawai->{$field} ?? null;

            if (!empty($dokumenPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-pegawai-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-500">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }


    /**
     * NEW: Process dokumen usulan
     */
    private function processDokumenUsulan(Usulan $usulan, $fieldHelper): array
    {
        $usulanFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        $processed = [];
        foreach ($usulanFields as $field) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);

            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

      /**
     * NEW: Process dokumen BKD
     */
    private function processDokumenBkd(Usulan $usulan, $fieldHelper): array
    {
        $bkdLabels = $usulan->getBkdDisplayLabels();
        $processed = [];

        foreach ($bkdLabels as $field => $label) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);

            // Handle legacy BKD mapping jika diperlukan
            if (empty($docPath) && str_starts_with($field, 'bkd_semester_')) {
                $docPath = $this->findLegacyBkdPath($usulan, $field, $label);
            }

            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => $label,
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * NEW: Process dokumen pendukung (dari admin fakultas)
     */
    private function processDokumenPendukung(Usulan $usulan, $fieldHelper): array
    {
        $pendukungFields = [
            'nomor_surat_usulan', 'file_surat_usulan',
            'nomor_berita_senat', 'file_berita_senat'
        ];

        $processed = [];
        foreach ($pendukungFields as $field) {
            if (str_starts_with($field, 'file_')) {
                // Handle file fields dengan override routing
                $validasiData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                $pathKey = $field . '_path';
                $path = $validasiData[$pathKey] ?? $validasiData[$field] ?? null;

                // Handle array path
                if (is_array($path)) {
                    $path = $path['path'] ?? $path['value'] ?? $path[0] ?? null;
                }

                if (!empty($path) && \Storage::disk('public')->exists($path)) {
                    $route = route('admin-fakultas.usulan.show-dokumen-pendukung', [$usulan->id, $field]);
                    $value = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">✓ Lihat Dokumen</a>';
                    $hasDocument = true;
                    $status = 'available';
                } else {
                    $value = '<span class="text-red-600">✗ Belum diunggah</span>';
                    $hasDocument = false;
                    $status = 'missing';
                }
            } else {
                // Handle text fields - bisa pakai helper karena tidak ada routing
                $value = $fieldHelper->getFieldValue('dokumen_pendukung', $field);
                $hasDocument = false;
                $status = (trim($value) !== '' && $value !== '-') ? 'filled' : 'empty';
            }

            $processed[$field] = [
                'label' => $this->getDokumenPendukungLabel($field),
                'value' => $value,
                'has_link' => strpos($value, '<a href=') !== false,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * SAFE: Helper untuk find legacy BKD path (extracted from helper logic)
     */
    private function findLegacyBkdPath(Usulan $usulan, string $field, string $label): ?string
    {
        if (!str_starts_with($field, 'bkd_semester_')) {
            return null;
        }

        $num = (int) str_replace('bkd_semester_', '', $field);
        if ($num < 1 || $num > 4) {
            return null;
        }

        // Parse label untuk mendapatkan semester dan tahun
        if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $label, $m)) {
            $sem = strtolower($m[1]); // ganjil|genap
            $y1  = $m[2];
            $y2  = $m[3];

            // Coba exact legacy key
            $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;
            $docPath = $usulan->getDocumentPath($legacyKey);

            if (!empty($docPath)) {
                return $docPath;
            }

            // Scan semua key BKD di data usulan
            $dokumenUsulan = $usulan->data_usulan['dokumen_usulan'] ?? [];
            foreach ($dokumenUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }

            // Fallback ke struktur lama
            $dataUsulan = $usulan->data_usulan ?? [];
            foreach ($dataUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }
        }

        return null;
    }

    /**
     * NEW: Helper untuk label dokumen pendukung
     */
    private function getDokumenPendukungLabel(string $field): string
    {
        $labels = [
            'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
            'file_surat_usulan' => 'Dokumen Surat Usulan Fakultas',
            'nomor_berita_senat' => 'Nomor Berita Senat',
            'file_berita_senat' => 'Dokumen Berita Senat'
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
    }

    /**
     * NEW: Helper untuk status dokumen pendukung
     */
    private function getDokumenPendukungStatus(string $value): string
    {
        if (strpos($value, '✓ Lihat Dokumen') !== false) {
            return 'available';
        } elseif (strpos($value, '✗ Belum diunggah') !== false || strpos($value, '✗ File tidak ditemukan') !== false) {
            return 'missing';
        } elseif (trim($value) !== '' && $value !== '-') {
            return 'filled';
        }

        return 'empty';
    }

    /**
     * NEW: Check if field value contains link
     */
    private function checkIfFieldHasLink(string $fieldValue): bool
    {
        return strpos($fieldValue, '<a href=') !== false;
    }

    /**
     * NEW: Determine field type for display
     */
    private function determineFieldType(string $category, string $field): string
    {
        if (in_array($category, ['dokumen_profil', 'dokumen_usulan', 'dokumen_bkd'])) {
            return 'document_link';
        } elseif ($category === 'dokumen_pendukung') {
            return str_contains($field, 'file_') ? 'document_file' : 'text_field';
        } elseif (str_contains($field, 'link_')) {
            return 'external_link';
        }

        return 'text_field';
    }

    /**
     * Determine file disk based on field type
     */
    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Clear cache for usulan
     */
    private function clearUsulanCache(Usulan $usulan): void
    {
        $cacheKeys = [
            "validation_fields_{$usulan->id}",
            "bkd_labels_{$usulan->id}",
            "existing_validation_{$usulan->id}_admin_fakultas",
            "dokumen_data_{$usulan->id}",
            "usulan_fakultas_{$usulan->id}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear admin cache
     */
    private function clearAdminCache(int $adminId): void
    {
        Cache::forget("admin_fakultas_id_{$adminId}");
    }


}

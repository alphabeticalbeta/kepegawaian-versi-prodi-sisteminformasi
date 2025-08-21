<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanDokumen;
use App\Models\BackendUnivUsulan\UsulanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class UsulanJabatanController extends BaseUsulanController
{
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        parent::__construct($fileStorage);
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of usulan jabatan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Debug information
        Log::info('UsulanJabatanController@index Debug', [
            'pegawai_id' => $pegawai->id,
            'pegawai_nip' => $pegawai->nip,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'jenis_usulan_periode' => $jenisUsulanPeriode
        ]);

        // Get periode usulan yang sesuai dengan status kepegawaian
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Debug query results
        Log::info('Periode Usulan Query Results', [
            'total_periode_found' => $periodeUsulans->count(),
            'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
            'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray()
        ]);

        // Alternative query if no results
        if ($periodeUsulans->count() == 0) {
            // Try without JSON contains
            $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            Log::info('Alternative Query Results (without JSON contains)', [
                'total_periode_found' => $altPeriodeUsulans->count(),
                'periode_ids' => $altPeriodeUsulans->pluck('id')->toArray(),
                'periode_names' => $altPeriodeUsulans->pluck('nama_periode')->toArray()
            ]);

            // Use alternative results if found
            if ($altPeriodeUsulans->count() > 0) {
                $periodeUsulans = $altPeriodeUsulans;
            }
        }

        // Get usulan yang sudah dibuat oleh pegawai
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', $jenisUsulanPeriode)
                          ->with(['periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
                          ->get();

        // Debug usulan yang ditemukan
        Log::info('Usulan yang ditemukan untuk pegawai', [
            'pegawai_id' => $pegawai->id,
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray()
        ]);

        return view('backend.layouts.views.pegawai-unmul.usul-jabatan.index', compact('periodeUsulans', 'usulans', 'pegawai'));
    }

    /**
     * Display the specified usulan jabatan.
     * Show detail view in read-only mode.
     */
    public function show(Usulan $usulan)
    {
        // Ownership guard (should already be handled by route binding)
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Load relationships
        $usulan->load([
            'pegawai',
            'periodeUsulan',
            'jabatanLama',
            'jabatanTujuan',
            'logs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'logs.dilakukanOleh'
        ]);

        // Get pegawai data
        $pegawai = $usulan->pegawai;

        // Determine jenis usulan berdasarkan pegawai
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Get periode data
        $daftarPeriode = $usulan->periodeUsulan;

        // Get jabatan tujuan
        $jabatanTujuan = $usulan->jabatanTujuan;

        // Determine jenjang type & form config
        $jenjangType = $this->determineJenjangType($pegawai, $usulan->jabatanLama, $jabatanTujuan);
        $formConfig = $this->getFormConfigByJenjang($jenjangType);

        $bkdSemesters = $this->generateBkdSemesterLabels($daftarPeriode);

        // Get document fields for the form
        $documentFields = $this->getDocumentKeys($pegawai->jenis_pegawai, $daftarPeriode);

        // Prepare catatan perbaikan (empty for show mode)
        $catatanPerbaikan = [
            'data_pribadi' => [],
            'data_kepegawaian' => [],
            'data_pendidikan' => [],
            'data_kinerja' => [],
            'dokumen_profil' => [],
            'dokumen_usulan' => [],
            'dokumen_bkd' => [],
            'karya_ilmiah' => [],
        ];

        // Set show mode
        $isReadOnly = true;
        $isEditMode = false;
        $isShowMode = true;

        return view('backend.layouts.views.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulan,
            'jenjangType' => $jenjangType,
            'formConfig' => $formConfig,
            'jenisUsulanPeriode' => $jenisUsulanPeriode,
            'bkdSemesters' => $bkdSemesters,
            'documentFields' => $documentFields,
            'catatanPerbaikan' => $catatanPerbaikan,
            'isReadOnly' => $isReadOnly,
            'isEditMode' => $isEditMode,
            'isShowMode' => $isShowMode,
        ]);
    }

    /**
     * Show the form for creating a new usulan jabatan
     */
    public function create()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $pegawai */
        $pegawai = Pegawai::with(['jabatan', 'pangkat', 'unitKerja'])
                ->findOrFail(Auth::id());

        Log::info('Create usulan jabatan accessed', [
            'user_id' => Auth::id(),
            'pegawai_id' => $pegawai->id,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian
        ]);

        // =============================================================
        // BLOK PENGECEKAN AKSES BARU
        // =============================================================
        $eligibleStatuses = ['Dosen PNS', 'Tenaga Kependidikan PNS'];
        if (!in_array($pegawai->status_kepegawaian, $eligibleStatuses)) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Fitur Usulan Jabatan tidak tersedia untuk status kepegawaian Anda.');
        }
        // =============================================================

        // Determine jenis usulan berdasarkan pegawai
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Cek periode yang sedang buka
        $daftarPeriode = $this->getActivePeriode($jenisUsulanPeriode);

        // Cek usulan yang sudah ada untuk periode ini
        $existingUsulan = null;
        if ($daftarPeriode) {
            $existingUsulan = Usulan::where('pegawai_id', Auth::id())
                ->where('periode_usulan_id', $daftarPeriode->id)
                ->where('jenis_usulan', $jenisUsulanPeriode)
                ->first();
        }

        // Cek jabatan tujuan
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $this->getJabatanTujuan($pegawai, $jabatanLama);

        // Determine jenjang type & form config
        $jenjangType = $this->determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan);
        $formConfig = $this->getFormConfigByJenjang($jenjangType);

        // Buat dummy usulan untuk compatibility dengan blade
        $usulan = new Usulan();

        $bkdSemesters = $this->generateBkdSemesterLabels($daftarPeriode);

        // Get document fields for the form
        $documentFields = $this->getDocumentKeys($pegawai->jenis_pegawai, $daftarPeriode);

        // Prepare catatan perbaikan (empty for new usulan)
        $catatanPerbaikan = [
            'data_pribadi' => [],
            'data_kepegawaian' => [],
            'data_pendidikan' => [],
            'data_kinerja' => [],
            'dokumen_profil' => [],
            'dokumen_usulan' => [],
            'dokumen_bkd' => [],
            'karya_ilmiah' => [],
        ];

        // Set read-only status
        $isReadOnly = false;
        $isEditMode = false;

        return view('backend.layouts.views.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulan,
            'jenjangType' => $jenjangType,
            'formConfig' => $formConfig,
            'jenisUsulanPeriode' => $jenisUsulanPeriode,
            'bkdSemesters' => $bkdSemesters,
            'documentFields' => $documentFields,
            'catatanPerbaikan' => $catatanPerbaikan,
            'isReadOnly' => $isReadOnly,
            'isEditMode' => $isEditMode,
            'existingUsulan' => $existingUsulan,
        ]);
    }

    /**
     * Store a newly created usulan jabatan
     */
    public function store(StoreJabatanUsulanRequest $request)
    {
        $pegawai = Auth::user();

        // =============================================================
        // BLOK PENGECEKAN AKSES BARU
        // =============================================================
        $eligibleStatuses = ['Dosen PNS', 'Tenaga Kependidikan PNS'];
        if (!in_array($pegawai->status_kepegawaian, $eligibleStatuses)) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Fitur Usulan Jabatan tidak tersedia untuk status kepegawaian Anda.');
        }
        // =============================================================

        Log::info('=== USULAN STORE START ===', [
            'user_id' => Auth::id(),
            'action' => $request->input('action'),
            'request_data' => $request->all()
        ]);

        $validatedData = $request->validated();

        Log::info('=== VALIDATED DATA ===', [
            'validated_data' => $validatedData,
            'validation_passed' => true
        ]);

        // Determine status based on action
        $action = $request->input('action');
        $statusUsulan = match($action) {
            'submit' => 'Diajukan',
            'save_draft' => 'Draft',
            default => 'Draft'
        };

        // ... sisa kode method store() tetap sama ...
        // Determine jenis usulan
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Validasi periode usulan dengan logging yang lebih detail
        Log::info('=== VALIDATING PERIODE ===', [
            'requested_periode_id' => $validatedData['periode_usulan_id'],
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'pegawai_status_kepegawaian' => $pegawai->status_kepegawaian
        ]);

        // Coba cari periode dengan berbagai kondisi
        $periodeUsulan = PeriodeUsulan::where('id', $validatedData['periode_usulan_id'])
            ->where('status', 'Buka')
            ->where('jenis_usulan', $jenisUsulanPeriode)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();

        // Jika tidak ditemukan, coba tanpa validasi status_kepegawaian
        if (!$periodeUsulan) {
            Log::info('Periode not found with status_kepegawaian check, trying without...');
            $periodeUsulan = PeriodeUsulan::where('id', $validatedData['periode_usulan_id'])
                ->where('status', 'Buka')
                ->where('jenis_usulan', $jenisUsulanPeriode)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now())
                ->first();
        }

        if (!$periodeUsulan) {
            Log::error('Periode usulan tidak valid', [
                'requested_id' => $validatedData['periode_usulan_id'],
                'jenis_usulan' => $jenisUsulanPeriode,
                'available_periodes' => PeriodeUsulan::where('status', 'Buka')->pluck('id', 'jenis_usulan')->toArray()
            ]);
            return redirect()->back()
                ->withErrors(['periode_usulan_id' => 'Periode usulan tidak valid atau sudah tidak aktif.'])
                ->withInput();
        }

        Log::info('=== PERIODE VALIDATED ===', [
            'periode_id' => $periodeUsulan->id,
            'periode_nama' => $periodeUsulan->nama_periode,
            'periode_jenis' => $periodeUsulan->jenis_usulan
        ]);

        // Validasi jabatan
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $this->getJabatanTujuan($pegawai, $jabatanLama);

        // Cek usulan aktif
        if ($this->hasActiveUsulan($pegawai->id, $jenisUsulanPeriode)) {
            Log::warning('User sudah memiliki usulan aktif');
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda sudah memiliki usulan jabatan yang sedang aktif.');
        }

        try {
            $usulanCreated = null;

            DB::transaction(function () use (
                $request, $pegawai, $periodeUsulan, $jabatanLama, $jabatanTujuan,
                $statusUsulan, $validatedData, $jenisUsulanPeriode, &$usulanCreated
            ) {
                Log::info('=== TRANSACTION START ===', [
                    'pegawai_id' => $pegawai->id,
                    'periode_id' => $periodeUsulan->id
                ]);

                // Persiapan data
                $pegawaiSnapshot = $this->createPegawaiSnapshot($pegawai);
                Log::info('=== PEGAWAI SNAPSHOT CREATED ===', [
                    'snapshot_data' => $pegawaiSnapshot
                ]);

                $karyaIlmiahData = $this->extractKaryaIlmiahData($validatedData);
                Log::info('=== KARYA ILMIAH DATA ===', [
                    'karya_ilmiah_data' => $karyaIlmiahData
                ]);

                $syaratKhususData = $this->extractSyaratKhususData($validatedData);
                Log::info('=== SYARAT KHUSUS DATA ===', [
                    'syarat_khusus_data' => $syaratKhususData
                ]);

                // Upload dokumen
                $documentKeys = $this->getDocumentKeys($pegawai->jenis_pegawai, $periodeUsulan);
                Log::info('=== DOCUMENT KEYS ===', [
                    'document_keys' => $documentKeys
                ]);

                $dokumenPaths = $this->handleDocumentUploads($request, $pegawai, $documentKeys);
                Log::info('=== DOCUMENT PATHS ===', [
                    'dokumen_paths' => $dokumenPaths
                ]);

                // Struktur data usulan
                $dataUsulan = [
                    'metadata' => [
                        'created_at_snapshot' => now()->toISOString(),
                        'version' => '1.0',
                        'submission_type' => $statusUsulan,
                        'jenjang_type' => $this->determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan),
                    ],
                    'pegawai_snapshot' => $pegawaiSnapshot,
                    'karya_ilmiah' => $karyaIlmiahData,
                    'dokumen_usulan' => $dokumenPaths,
                    'syarat_khusus' => $syaratKhususData,
                    'catatan_pengusul' => $validatedData['catatan'] ?? null,
                ];

                // Buat usulan
                $usulanData = [
                    'pegawai_id' => $pegawai->id,
                    'periode_usulan_id' => $periodeUsulan->id,
                    'jenis_usulan' => $jenisUsulanPeriode,
                    'jabatan_lama_id' => $jabatanLama?->id,
                    'jabatan_tujuan_id' => $jabatanTujuan?->id,
                    'status_usulan' => $statusUsulan,
                    'status_kepegawaian' => $pegawai->status_kepegawaian,
                    'data_usulan' => $dataUsulan,
                    'catatan_verifikator' => null,
                ];

                Log::info('=== USULAN DATA PREPARED ===', [
                    'usulan_data' => $usulanData
                ]);

                $usulan = Usulan::create($usulanData);

                Log::info('=== USULAN CREATED ===', [
                    'usulan_id' => $usulan->id,
                    'usulan_status' => $usulan->status_usulan
                ]);

                // Simpan dokumen dan log
                Log::info('=== SAVING DOCUMENTS ===', [
                    'usulan_id' => $usulan->id,
                    'dokumen_paths_count' => count($dokumenPaths)
                ]);

                $this->saveUsulanDocuments($usulan, $dokumenPaths, $pegawai);

                Log::info('=== CREATING LOG ===', [
                    'usulan_id' => $usulan->id,
                    'status' => $statusUsulan
                ]);

                $this->createUsulanLog($usulan, null, $statusUsulan, $pegawai, $validatedData);

                $usulanCreated = $usulan;

                Log::info('=== TRANSACTION COMPLETED ===', [
                    'usulan_id' => $usulan->id,
                    'status' => $statusUsulan
                ]);
            });

            // Dispatch background jobs
            if ($usulanCreated) {
                $this->dispatchUsulanJobs($usulanCreated, $statusUsulan);

                Log::info('=== USULAN STORE SUCCESS ===', [
                    'usulan_id' => $usulanCreated->id,
                    'status' => $statusUsulan
                ]);
            } else {
                Log::error('=== USULAN STORE FAILED ===', [
                    'error' => 'usulanCreated is null',
                    'pegawai_id' => $pegawai->id
                ]);

                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan saat menyimpan usulan. Silakan coba lagi.')
                    ->withInput();
            }

        } catch (\Throwable $e) {
            Log::error('=== USULAN STORE ERROR ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'pegawai_id' => $pegawai->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi. Error: ' . $e->getMessage())
                ->withInput();
        }

        $message = match($action) {
            'submit' => 'Usulan kenaikan jabatan berhasil diajukan. Tim verifikasi akan meninjau usulan Anda.',
            'save_draft' => 'Usulan jabatan berhasil disimpan sebagai draft. Anda dapat melanjutkan pengisian nanti.',
            default => 'Usulan jabatan berhasil disimpan.'
        };

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    /**
     * Test method for form submission - bypasses FormRequest
     */
    public function testStore(Request $request)
    {
        Log::info('=== TEST STORE METHOD CALLED ===', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            $pegawai = Auth::user();

            // Get active periode
            $periode = \App\Models\BackendUnivUsulan\PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
                ->where('status', 'Buka')
                ->first();

            if (!$periode) {
                return response()->json(['error' => 'No active periode found'], 400);
            }

            // Create usulan with minimal data
            $usulan = new \App\Models\BackendUnivUsulan\Usulan();
            $usulan->pegawai_id = $pegawai->id;
            $usulan->periode_usulan_id = $periode->id;
            $usulan->jenis_usulan = 'Usulan Jabatan';
            $usulan->status_usulan = 'Draft';
            $usulan->data_usulan = $request->all();
            $usulan->save();

            // Create usulan log
            $usulanLog = new \App\Models\BackendUnivUsulan\UsulanLog();
            $usulanLog->usulan_id = $usulan->id;
            $usulanLog->dilakukan_oleh_id = $pegawai->id;
            $usulanLog->status_sebelumnya = null;
            $usulanLog->status_baru = 'Draft';
            $usulanLog->catatan = 'Usulan jabatan dibuat via test method';
            $usulanLog->save();

            Log::info('=== TEST STORE SUCCESS ===', [
                'usulan_id' => $usulan->id,
                'log_id' => $usulanLog->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usulan berhasil dibuat',
                'usulan_id' => $usulan->id
            ]);

        } catch (\Exception $e) {
            Log::error('=== TEST STORE ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat usulan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified usulan jabatan
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function edit(Usulan $usulan)
    {

        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $pegawai = Auth::user();

        $isReadOnly = in_array($usulan->status_usulan, [
            'Diajukan', 'Sedang Direview', 'Disetujui', 'Direkomendasikan'
        ]);

        $canEdit = in_array($usulan->status_usulan, [
            'Draft', 'Perbaikan Usulan', 'Dikembalikan'
        ]);

        // Get periode usulan
        $jenisUsulanPeriode = $usulan->jenis_usulan;
        $daftarPeriode = $this->getActivePeriode($jenisUsulanPeriode);

        // Get jabatan info
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $this->getJabatanTujuan($pegawai, $jabatanLama);

        // Determine jenjang type for editing
        $jenjangType = $this->determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan);
        $formConfig = $this->getFormConfigByJenjang($jenjangType);

        // Get validation data from all roles for comprehensive feedback
        $catatanPerbaikan = [];
        $roles = ['admin_fakultas', 'admin_universitas', 'tim_penilai'];

        foreach ($roles as $role) {
            $roleData = $usulan->getValidasiByRole($role);
            if (!empty($roleData) && isset($roleData['validation'])) {
                $catatanPerbaikan[$role] = $roleData['validation'];
            }
        }

        $bkdSemesters = $this->generateBkdSemesterLabels($usulan->periodeUsulan);

        // Get document fields for the form
        $documentFields = $this->getDocumentKeys($pegawai->jenis_pegawai, $usulan->periodeUsulan);

        return view('backend.layouts.views.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulan,
            'jenjangType' => $jenjangType,
            'formConfig' => $formConfig,
            'jenisUsulanPeriode' => $jenisUsulanPeriode,
            'catatanPerbaikan' => $catatanPerbaikan,
            'bkdSemesters' => $bkdSemesters,
            'documentFields' => $documentFields,
            'isReadOnly' => $isReadOnly,
            'isEditMode' => $canEdit,
            'existingUsulan' => $usulan,
            'validationData' => $catatanPerbaikan, // Pass validation data for field highlighting
        ]);
    }

    /**
     * Update the specified usulan jabatan
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function update(StoreJabatanUsulanRequest $request, Usulan $usulan)
    {

        // Authorization check
        if ($usulan->pegawai_id !== Auth::id()) {
            Log::warning('Unauthorized update attempt', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'owner_id' => $usulan->pegawai_id
            ]);
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki akses untuk mengubah usulan ini.');
        }

        // Status validation
        if ($usulan->is_read_only) {
            return redirect()->back()
                ->with('error', 'Usulan dengan status "' . $usulan->status_usulan . '" tidak dapat diubah.');
        }

        $validatedData = $request->validated();
        $pegawai = Auth::user();
        $oldStatus = $usulan->status_usulan;

        // Determine status based on action
        $action = $request->input('action');
        $statusUsulan = match($action) {
            'submit' => 'Diajukan',
            'submit_to_fakultas' => 'Diajukan', // Back to Admin Fakultas
            'submit_to_university' => 'Diusulkan ke Universitas', // Back to Admin Universitas
            'save_draft' => 'Draft',
            default => 'Draft'
        };

        Log::info('Starting usulan update', [
            'usulan_id' => $usulan->id,
            'old_status' => $oldStatus,
            'new_status' => $statusUsulan,
            'user_id' => $pegawai->id,
            'action' => $request->input('action')
        ]);

        try {
            $updatedUsulan = null;

            DB::transaction(function () use ($request, $usulan, $pegawai, $statusUsulan, $validatedData, $oldStatus, &$updatedUsulan) {

                // Get existing data
                $dataUsulanLama = $usulan->data_usulan ?? [];

                // Update karya ilmiah data
                $karyaIlmiahData = $this->extractKaryaIlmiahData($validatedData);
                $dataUsulanLama['karya_ilmiah'] = $karyaIlmiahData;

                // Update syarat khusus data
                $syaratKhususData = $this->extractSyaratKhususData($validatedData);
                $dataUsulanLama['syarat_khusus'] = $syaratKhususData;

                // Update catatan
                $dataUsulanLama['catatan_pengusul'] = $validatedData['catatan'] ?? null;

                // Update metadata
                if (!isset($dataUsulanLama['metadata'])) {
                    $dataUsulanLama['metadata'] = [];
                }
                $dataUsulanLama['metadata']['last_updated'] = now()->toISOString();
                $dataUsulanLama['metadata']['updated_by'] = $pegawai->id;
                $dataUsulanLama['metadata']['submission_type'] = $statusUsulan;
                $dataUsulanLama['metadata']['version'] = ($dataUsulanLama['metadata']['version'] ?? '1.0');

                // Handle document updates
                try {
                    $this->updateDocuments($request, $usulan, $pegawai, $dataUsulanLama);
                    Log::info('Documents updated successfully', ['usulan_id' => $usulan->id]);
                } catch (\Throwable $e) {
                    Log::error('Document update failed', [
                        'usulan_id' => $usulan->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }

                // Update usulan record
                $updateData = [
                    'status_usulan' => $statusUsulan,
                    'status_kepegawaian' => $pegawai->status_kepegawaian,
                    'data_usulan' => $dataUsulanLama,
                ];

                // Clear verifikator notes if status changes from "Perlu Perbaikan"
                if ($oldStatus === 'Dikembalikan ke Pegawai' && $statusUsulan === 'Diajukan') {
                    $updateData['catatan_verifikator'] = null;
                }

                $usulan->update($updateData);

                // Create log entry
                if ($oldStatus !== $statusUsulan) {
                    $this->createUsulanLog($usulan, $oldStatus, $statusUsulan, $pegawai, $validatedData);
                }

                $updatedUsulan = $usulan->fresh();
            });

            // Dispatch background jobs (outside transaction)
            if ($updatedUsulan && $oldStatus !== $statusUsulan) {
                try {
                    $this->dispatchUsulanJobs($updatedUsulan, $statusUsulan);
                    Log::info('Background jobs dispatched', ['usulan_id' => $updatedUsulan->id, 'status' => $statusUsulan]);
                } catch (\Throwable $e) {
                    Log::error('Failed to dispatch background jobs', [
                        'usulan_id' => $updatedUsulan->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = match($action) {
                'submit' => 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Tim verifikasi akan meninjau usulan Anda.',
                'submit_to_fakultas' => 'Usulan berhasil dikembalikan ke Admin Fakultas untuk ditinjau kembali.',
                'submit_to_university' => 'Usulan berhasil dikembalikan ke Admin Universitas untuk ditinjau kembali.',
                'save_draft' => 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.',
                default => 'Perubahan pada usulan Anda berhasil disimpan.'
            };

            Log::info('Usulan update completed successfully', [
                'usulan_id' => $usulan->id,
                'final_status' => $statusUsulan
            ]);

            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during update', [
                'usulan_id' => $usulan->id,
                'errors' => $e->errors()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('Failed to update usulan', [
                'usulan_id' => $usulan->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => $pegawai->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem saat memperbarui usulan. Silakan coba lagi. Jika masalah berlanjut, hubungi administrator.')
                ->withInput();
        }
    }

    /**
     * Show usulan document
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function showUsulanDocument(Usulan $usulan, $field)
    {
        // Authorization check
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Get file path
        $filePath = $usulan->getDocumentPath($field);

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Log document access
        Log::info('Document accessed', [
            'usulan_id' => $usulan->id,
            'field' => $field,
            'user_id' => Auth::id(),
            'file_path' => $filePath
        ]);

        $fullPath = Storage::disk('local')->path($filePath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Get usulan logs
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function getLogs(Usulan $usulan)
    {
        return $this->getUsulanLogs($usulan);
    }

    /**
     * Debug method untuk development
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function debugUpdate(Request $request, Usulan $usulan)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        return response()->json([
            'usulan_id' => $usulan->id,
            'current_status' => $usulan->status_usulan,
            'can_edit' => $usulan->can_edit,
            'is_read_only' => $usulan->is_read_only,
            'owner_id' => $usulan->pegawai_id,
            'current_user_id' => Auth::id(),
            'data_usulan_structure' => array_keys($usulan->data_usulan ?? []),
            'existing_documents' => $usulan->getExistingDocuments(),
            'form_data' => $request->all(),
            'validation_errors' => session()->get('errors')?->all(),
        ]);
    }

    // =====================================================
    // HELPER METHODS SPECIFIC TO JABATAN
    // =====================================================

    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        if ($pegawai->jenis_pegawai === 'Dosen' && $pegawai->status_kepegawaian === 'Dosen PNS') {
            return 'Usulan Jabatan';
        } elseif ($pegawai->jenis_pegawai === 'Tenaga Kependidikan' && $pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
            return 'Usulan Jabatan';
        }

        return 'Usulan Jabatan'; // Fallback
    }

    /**
     * Get jabatan tujuan berdasarkan jabatan saat ini
     */
    protected function getJabatanTujuan($pegawai, $jabatanLama): ?Jabatan
    {
        if (!$jabatanLama) {
            return null;
        }

        return Jabatan::where('jenis_pegawai', $pegawai->jenis_pegawai)
                      ->where('jenis_jabatan', $jabatanLama->jenis_jabatan)
                      ->where('id', '>', $jabatanLama->id)
                      ->orderBy('id', 'asc')
                      ->first();
    }

    /**
     * Determine jenjang type berdasarkan jabatan saat ini dan tujuan
     */
    protected function determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan): string
    {
        // Untuk Tenaga Kependidikan
        if ($pegawai->jenis_pegawai === 'Tenaga Kependidikan') {
            return 'tenaga-kependidikan';
        }

        // Untuk Dosen - berdasarkan jabatan tujuan
        if (!$jabatanTujuan) {
            return 'unknown';
        }

        return match($jabatanTujuan->jabatan) {
            'Asisten Ahli' => 'tenaga-pengajar-to-asisten-ahli',
            'Lektor' => 'asisten-ahli-to-lektor',
            'Lektor Kepala' => 'lektor-to-lektor-kepala',
            'Guru Besar' => 'lektor-kepala-to-guru-besar',
            default => 'unknown'
        };
    }

    /**
     * Get form configuration berdasarkan jenjang
     */
    protected function getFormConfigByJenjang(string $jenjangType): array
    {
        $configs = [
            'tenaga-pengajar-to-asisten-ahli' => [
                'title' => 'Usulan Tenaga Pengajar ke Asisten Ahli',
                'description' => 'Formulir pengajuan kenaikan jabatan dari Tenaga Pengajar ke Asisten Ahli',
                'show_karya_ilmiah' => true,
                'karya_ilmiah_required' => false,
                'show_syarat_khusus' => false,
                'required_documents' => [
                    'pakta_integritas' => true,
                    'bukti_korespondensi' => false,
                    'turnitin' => false,
                    'upload_artikel' => false,
                    'bukti_syarat_guru_besar' => false,
                ],
                'karya_ilmiah_options' => [
                    'Jurnal Nasional Bereputasi',
                    'Jurnal Internasional Bereputasi'
                ],
                'gradient_colors' => 'from-green-600 to-blue-600',
                'icon' => 'user-plus'
            ],

            'asisten-ahli-to-lektor' => [
                'title' => 'Usulan Asisten Ahli ke Lektor',
                'description' => 'Formulir pengajuan kenaikan jabatan dari Asisten Ahli ke Lektor',
                'show_karya_ilmiah' => true,
                'karya_ilmiah_required' => true,
                'show_syarat_khusus' => false,
                'required_documents' => [
                    'pakta_integritas' => true,
                    'bukti_korespondensi' => true,
                    'turnitin' => true,
                    'upload_artikel' => true,
                    'bukti_syarat_guru_besar' => false,
                ],
                'karya_ilmiah_options' => [
                    'Jurnal Nasional Bereputasi',
                    'Jurnal Internasional Bereputasi'
                ],
                'gradient_colors' => 'from-blue-600 to-indigo-600',
                'icon' => 'trending-up'
            ],

            'lektor-to-lektor-kepala' => [
                'title' => 'Usulan Lektor ke Lektor Kepala',
                'description' => 'Formulir pengajuan kenaikan jabatan dari Lektor ke Lektor Kepala',
                'show_karya_ilmiah' => true,
                'karya_ilmiah_required' => true,
                'show_syarat_khusus' => false,
                'required_documents' => [
                    'pakta_integritas' => true,
                    'bukti_korespondensi' => true,
                    'turnitin' => true,
                    'upload_artikel' => true,
                    'bukti_syarat_guru_besar' => false,
                ],
                'karya_ilmiah_options' => [
                    'Jurnal Nasional Bereputasi',
                    'Jurnal Internasional Bereputasi'
                ],
                'gradient_colors' => 'from-indigo-600 to-purple-600',
                'icon' => 'award'
            ],

            'lektor-kepala-to-guru-besar' => [
                'title' => 'Usulan Lektor Kepala ke Guru Besar',
                'description' => 'Formulir pengajuan kenaikan jabatan dari Lektor Kepala ke Guru Besar',
                'show_karya_ilmiah' => true,
                'karya_ilmiah_required' => true,
                'show_syarat_khusus' => true,
                'required_documents' => [
                    'pakta_integritas' => true,
                    'bukti_korespondensi' => true,
                    'turnitin' => true,
                    'upload_artikel' => true,
                    'bukti_syarat_guru_besar' => true,
                ],
                'karya_ilmiah_options' => [
                    'Jurnal Internasional Bereputasi'
                ],
                'gradient_colors' => 'from-purple-600 to-pink-600',
                'icon' => 'crown'
            ],

            'tenaga-kependidikan' => [
                'title' => 'Usulan Jabatan Tenaga Kependidikan',
                'description' => 'Formulir pengajuan usulan jabatan tenaga kependidikan',
                'show_karya_ilmiah' => false,
                'karya_ilmiah_required' => false,
                'show_syarat_khusus' => false,
                'required_documents' => [
                    'pakta_integritas' => true,
                    'bukti_korespondensi' => false,
                    'turnitin' => false,
                    'upload_artikel' => false,
                    'bukti_syarat_guru_besar' => false,
                ],
                'karya_ilmiah_options' => [],
                'gradient_colors' => 'from-emerald-600 to-cyan-600',
                'icon' => 'briefcase',
                'show_development_warning' => true,
            ],
        ];

        return $configs[$jenjangType] ?? $configs['tenaga-pengajar-to-asisten-ahli'];
    }

    /**
     * Extract data karya ilmiah dari validated data
     */
    protected function extractKaryaIlmiahData(array $validatedData): array
    {
        return [
            'jenis_karya' => $validatedData['karya_ilmiah'] ?? null,
            'nama_jurnal' => $validatedData['nama_jurnal'] ?? null,
            'judul_artikel' => $validatedData['judul_artikel'] ?? null,
            'penerbit_artikel' => $validatedData['penerbit_artikel'] ?? null,
            'volume_artikel' => $validatedData['volume_artikel'] ?? null,
            'nomor_artikel' => $validatedData['nomor_artikel'] ?? null,
            'edisi_artikel' => $validatedData['edisi_artikel'] ?? null,
            'halaman_artikel' => $validatedData['halaman_artikel'] ?? null,
            'links' => [
                'artikel' => $validatedData['link_artikel'] ?? null,
                'sinta' => $validatedData['link_sinta'] ?? null,
                'scopus' => $validatedData['link_scopus'] ?? null,
                'scimago' => $validatedData['link_scimago'] ?? null,
                'wos' => $validatedData['link_wos'] ?? null,
            ],
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Extract data syarat khusus (untuk Guru Besar)
     */
    protected function extractSyaratKhususData(array $validatedData): array
    {
        $syarat = $validatedData['syarat_guru_besar'] ?? null;

        return [
            'syarat_guru_besar' => $syarat,
            'deskripsi_syarat' => $this->getSyaratGuruBesarDescription($syarat),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Get deskripsi syarat guru besar
     */
    protected function getSyaratGuruBesarDescription(?string $syarat): ?string
    {
        $descriptions = [
            'hibah' => 'Pernah mendapatkan hibah penelitian',
            'bimbingan' => 'Pernah membimbing program doktor',
            'pengujian' => 'Pernah menguji mahasiswa doktor',
            'reviewer' => 'Sebagai reviewer jurnal internasional'
        ];

        return $syarat ? $descriptions[$syarat] ?? null : null;
    }

    /**
     * Generate 4 BKD semester labels based on the proposal period start date.
     *
     * @param PeriodeUsulan $periode
     * @return array
     */

    /**
     * Get document keys berdasarkan jenis pegawai
     */
    protected function getDocumentKeys(string $jenisPegawai, ?PeriodeUsulan $periode = null): array
    {
        $baseKeys = ['pakta_integritas'];

        if ($jenisPegawai === 'Dosen') {
            // Dinamis mengambil BKD slugs dari periode usulan
            $bkdSlugs = [];
            if ($periode) {
                $bkdSemesters = $this->generateBkdSemesterLabels($periode);
                $bkdSlugs = array_column($bkdSemesters, 'slug');
            }

            return array_merge($baseKeys, [
                'bukti_korespondensi',
                'turnitin',
                'upload_artikel',
                'bukti_syarat_guru_besar'
            ], $bkdSlugs); // Gabungkan dengan BKD slugs
        }

        return $baseKeys;
    }

    /**
     * Update documents dalam usulan - REFACTORED with FileStorageService
     */
    protected function updateDocuments($request, $usulan, $pegawai, &$dataUsulanLama): void
    {
        $documentKeys = $this->getDocumentKeys($pegawai->jenis_pegawai, $usulan->periodeUsulan);

        foreach ($documentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    // 1. Delete old document first
                    $this->deleteOldDocument($dataUsulanLama, $key, $usulan);

                    // 2. Upload new document using FileStorageService
                    $uploadPath = 'usulan-dokumen/' . $pegawai->id . '/' . date('Y/m');
                    $file = $request->file($key);

                    // Use FileStorageService for upload
                    $path = $this->fileStorage->uploadFile($file, $uploadPath);

                    // 3. Prepare new document data
                    $newDocumentData = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                        'uploaded_by' => $pegawai->id,
                    ];

                    // 4. Update data_usulan structure
                    if (!isset($dataUsulanLama['dokumen_usulan'])) {
                        $dataUsulanLama['dokumen_usulan'] = [];
                    }
                    $dataUsulanLama['dokumen_usulan'][$key] = $newDocumentData;

                    // 5. Update UsulanDokumen table
                    UsulanDokumen::updateOrCreate(
                        [
                            'usulan_id' => $usulan->id,
                            'nama_dokumen' => $key
                        ],
                        [
                            'diupload_oleh_id' => $pegawai->id,
                            'path' => $path
                        ]
                    );

                    Log::info("Document updated successfully using FileStorageService", [
                        'usulan_id' => $usulan->id,
                        'document_key' => $key,
                        'file_path' => $path,
                        'file_size' => $file->getSize()
                    ]);

                } catch (\Throwable $e) {
                    Log::error("Failed to update document", [
                        'usulan_id' => $usulan->id,
                        'document_key' => $key,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \RuntimeException("Gagal memperbarui dokumen $key: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Delete old document with proper cleanup - REFACTORED with FileStorageService
     */
    protected function deleteOldDocument(&$dataUsulanLama, string $key, $usulan): void
    {
        $oldFilePath = null;

        // Check new structure first
        if (isset($dataUsulanLama['dokumen_usulan'][$key]['path'])) {
            $oldFilePath = $dataUsulanLama['dokumen_usulan'][$key]['path'];
        }
        // Fallback to old structure
        elseif (isset($dataUsulanLama[$key])) {
            $oldFilePath = $dataUsulanLama[$key];
        }

        if ($oldFilePath) {
            try {
                // Use FileStorageService for deletion
                $this->fileStorage->deleteFile($oldFilePath);

                // Remove from data structure
                if (isset($dataUsulanLama['dokumen_usulan'][$key])) {
                    unset($dataUsulanLama['dokumen_usulan'][$key]);
                }
                if (isset($dataUsulanLama[$key])) {
                    unset($dataUsulanLama[$key]);
                }

                Log::info("Old document file deleted using FileStorageService", [
                    'file_path' => $oldFilePath,
                    'usulan_id' => $usulan->id
                ]);

            } catch (\Throwable $e) {
                Log::warning("Failed to delete old document file", [
                    'file_path' => $oldFilePath,
                    'usulan_id' => $usulan->id,
                    'error' => $e->getMessage()
                ]);
                // Don't throw exception, just log warning
            }
        }
    }

    /**
     * Generate 4 BKD semester labels based on the proposal period start date.
     * NEW LOGIC: Mulai dari 2 semester sebelumnya, hitung mundur untuk 4 semester
     *
     * @param PeriodeUsulan $periode
     * @return array
     */
    protected function generateBkdSemesterLabels(PeriodeUsulan $periode): array
    {
        $startDate = Carbon::parse($periode->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester based on month
        $currentSemester = '';
        $currentYear = 0;

        if ($month >= 1 && $month <= 6) {
            // Januari - Juni: Semester Genap sedang berjalan
            $currentSemester = 'Genap';
            $currentYear = $year - 1; // Tahun akademik dimulai tahun sebelumnya
        } elseif ($month >= 7 && $month <= 12) {
            // Juli - Desember: Semester Ganjil sedang berjalan
            $currentSemester = 'Ganjil';
            $currentYear = $year;
        }

        // NEW LOGIC: Mundur 2 semester dari periode saat ini untuk titik awal BKD
        $bkdStartSemester = $currentSemester;
        $bkdStartYear = $currentYear;

        // Mundur 2 semester
        for ($i = 0; $i < 2; $i++) {
            if ($bkdStartSemester === 'Ganjil') {
                $bkdStartSemester = 'Genap';
                $bkdStartYear--;
            } else {
                $bkdStartSemester = 'Ganjil';
            }
        }

        // Generate 4 semester BKD mulai dari titik awal (mundur)
        $semesters = [];
        $tempSemester = $bkdStartSemester;
        $tempYear = $bkdStartYear;

        for ($i = 0; $i < 4; $i++) {
            $academicYear = $tempYear . '/' . ($tempYear + 1);
            $label = "BKD Semester {$tempSemester} {$academicYear}";
            $slug = 'bkd_' . strtolower($tempSemester) . '_' . str_replace('/', '_', $academicYear);

            $semesters[] = [
                'label' => $label,
                'slug' => $slug,
            ];

            // Move to previous semester (mundur)
            if ($tempSemester === 'Ganjil') {
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                $tempSemester = 'Ganjil';
            }
        }

        return $semesters;
    }

    /**
     * Remove the specified usulan from storage.
     */
    public function destroy($id)
    {
        $usulan = Usulan::where('id', $id)
                        ->where('pegawai_id', Auth::id())
                        ->firstOrFail();

        // Only allow deletion if status is Draft or Perlu Perbaikan
        if (!in_array($usulan->status_usulan, ['Draft', 'Perlu Perbaikan'])) {
            return redirect()->route('pegawai-unmul.usulan-jabatan.index')
                ->with('error', 'Usulan tidak dapat dihapus karena status tidak memungkinkan.');
        }

        try {
            DB::beginTransaction();

            // Delete related documents
            if ($usulan->dokumen_usulan) {
                foreach ($usulan->dokumen_usulan as $document) {
                    if (isset($document['path']) && Storage::disk('local')->exists($document['path'])) {
                        Storage::disk('local')->delete($document['path']);
                    }
                }
            }

            // Delete usulan
            $usulan->delete();

            DB::commit();

            return redirect()->route('pegawai-unmul.usulan-jabatan.index')
                ->with('success', 'Usulan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete usulan', [
                'usulan_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pegawai-unmul.usulan-jabatan.index')
                ->with('error', 'Gagal menghapus usulan. Silakan coba lagi.');
        }
    }

}

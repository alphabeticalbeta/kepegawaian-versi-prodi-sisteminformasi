<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UsulanJabatanController extends BaseUsulanController
{
    /**
     * Display a listing of usulan jabatan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        $usulans = $pegawai->usulans()
                          ->whereIn('jenis_usulan', ['usulan-jabatan-dosen', 'usulan-jabatan-tendik'])
                          ->with(['periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
                          ->latest()
                          ->paginate(10);

        return view('backend.layouts.pegawai-unmul.usulan-jabatan.index', compact('usulans'));
    }

    /**
     * Show the form for creating a new usulan jabatan
     */
    public function create()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $pegawai */
        $pegawai = Pegawai::with(['jabatan', 'pangkat', 'unitKerja'])
                ->findOrFail(Auth::id());

        // Load dokumen profil pegawai
        $documentFields = [
            'sk_pangkat_terakhir' => ['label' => 'SK Pangkat Terakhir', 'icon' => 'file-text'],
            'sk_jabatan_terakhir' => ['label' => 'SK Jabatan Terakhir', 'icon' => 'file-text'],
            'ijazah_terakhir' => ['label' => 'Ijazah Terakhir', 'icon' => 'award'],
            'transkrip_nilai_terakhir' => ['label' => 'Transkrip Nilai Terakhir', 'icon' => 'file-text'],
            'sk_cpns' => ['label' => 'SK CPNS', 'icon' => 'file-text'],
            'sk_pns' => ['label' => 'SK PNS', 'icon' => 'file-text'],
            'skp_tahun_pertama' => ['label' => 'SKP Tahun Pertama', 'icon' => 'clipboard'],
            'skp_tahun_kedua' => ['label' => 'SKP Tahun Kedua', 'icon' => 'clipboard'],
        ];
        // Add dosen-specific documents
        if ($pegawai->jenis_pegawai === 'Dosen') {
            $documentFields['pak_konversi'] = ['label' => 'PAK Konversi', 'icon' => 'file-check'];
            $documentFields['sk_penyetaraan_ijazah'] = ['label' => 'SK Penyetaraan Ijazah', 'icon' => 'file-text'];
            $documentFields['disertasi_thesis_terakhir'] = ['label' => 'Disertasi/Thesis Terakhir', 'icon' => 'book'];
        }

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

        // Validasi kelengkapan profil
        $missingFields = $this->validateProfileCompleteness($pegawai);
        if ($missingFields) {
            return redirect()->route('pegawai-unmul.profile.edit')
                ->with('warning', 'Profil Anda belum lengkap. Silakan lengkapi semua data profil sebelum membuat usulan. Data yang belum lengkap: ' . count($missingFields) . ' field.');
        }

        if (!$pegawai->jabatan) {
            return redirect()->route('pegawai-unmul.profile.edit')
                ->with('error', 'Data Jabatan Fungsional Belum Ditentukan. Silakan perbarui profil Anda atau hubungi admin.');
        }

        // ... sisa kode method create() tetap sama ...
        // Determine jenis usulan berdasarkan pegawai
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Cek periode yang sedang buka
        $daftarPeriode = $this->getActivePeriode($jenisUsulanPeriode);
        if (!$daftarPeriode) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Saat ini tidak ada periode pengajuan usulan jabatan yang aktif untuk jenis pegawai Anda.');
        }

        $isRejectedInCurrentPeriod = Usulan::where('pegawai_id', Auth::id())
            ->where('periode_usulan_id', $daftarPeriode->id)
            ->where('status_usulan', 'Ditolak Universitas')
            ->exists();


        if ($isRejectedInCurrentPeriod) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda tidak dapat mengajukan usulan baru pada periode ini karena usulan Anda sebelumnya telah ditolak oleh universitas.');
        }

        // Cek usulan aktif
        if ($this->hasActiveUsulan(Auth::id(), $jenisUsulanPeriode)) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda sudah memiliki usulan jabatan yang sedang aktif.');
        }

        // Cek jabatan tujuan
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $this->getJabatanTujuan($pegawai, $jabatanLama);

        if (!$jabatanTujuan && $pegawai->jenis_pegawai === 'Dosen') {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('warning', 'Anda sudah berada di jabatan fungsional tertinggi. Tidak ada jabatan yang lebih tinggi untuk diajukan.');
        }

        // Determine jenjang type & form config
        $jenjangType = $this->determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan);
        $formConfig = $this->getFormConfigByJenjang($jenjangType);

        // Buat dummy usulan untuk compatibility dengan blade
        $usulan = new Usulan();

        $bkdSemesters = $this->generateBkdSemesterLabels($daftarPeriode);

        // Get document fields for the form
        $documentFields = $this->getDocumentKeys($pegawai->jenis_pegawai);

        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulan,
            'jenjangType' => $jenjangType,
            'formConfig' => $formConfig,
            'jenisUsulanPeriode' => $jenisUsulanPeriode,
            'bkdSemesters' => $bkdSemesters,
            'documentFields' => $documentFields,
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
            'action' => $request->input('action')
        ]);

        $validatedData = $request->validated();
        $statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        // ... sisa kode method store() tetap sama ...
        // Determine jenis usulan
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Validasi periode usulan
        $periodeUsulan = PeriodeUsulan::where('id', $validatedData['periode_usulan_id'])
            ->where('status', 'Buka')
            ->where('jenis_usulan', $jenisUsulanPeriode)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeUsulan) {
            Log::error('Periode usulan tidak valid', [
                'requested_id' => $validatedData['periode_usulan_id'],
                'jenis_usulan' => $jenisUsulanPeriode
            ]);
            return redirect()->back()
                ->withErrors(['periode_usulan_id' => 'Periode usulan tidak valid atau sudah tidak aktif.'])
                ->withInput();
        }

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
                // Persiapan data
                $pegawaiSnapshot = $this->createPegawaiSnapshot($pegawai);
                $karyaIlmiahData = $this->extractKaryaIlmiahData($validatedData);
                $syaratKhususData = $this->extractSyaratKhususData($validatedData);

                // Upload dokumen
                $documentKeys = $this->getDocumentKeys($pegawai->jenis_pegawai);
                $dokumenPaths = $this->handleDocumentUploads($request, $pegawai, $documentKeys);

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
                    'data_usulan' => $dataUsulan,
                    'catatan_verifikator' => null,
                ];

                $usulan = Usulan::create($usulanData);

                // Simpan dokumen dan log
                $this->saveUsulanDocuments($usulan, $dokumenPaths, $pegawai);
                $this->createUsulanLog($usulan, null, $statusUsulan, $pegawai, $validatedData);

                $usulanCreated = $usulan;
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

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diajukan. Tim verifikasi akan meninjau usulan Anda.'
            : 'Usulan jabatan berhasil disimpan sebagai draft. Anda dapat melanjutkan pengisian nanti.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified usulan jabatan
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function edit(Usulan $usulanJabatan)
    {

        if ($usulanJabatan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $pegawai = Auth::user();

        $isReadOnly = in_array($usulanJabatan->status_usulan, [
            'Diajukan', 'Sedang Direview', 'Disetujui', 'Direkomendasikan'
        ]);

        $canEdit = in_array($usulanJabatan->status_usulan, [
            'Draft', 'Perlu Perbaikan', 'Dikembalikan'
        ]);

        // Get periode usulan
        $jenisUsulanPeriode = $usulanJabatan->jenis_usulan;
        $daftarPeriode = $this->getActivePeriode($jenisUsulanPeriode);

        // Get jabatan info
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $this->getJabatanTujuan($pegawai, $jabatanLama);

        // Determine jenjang type for editing
        $jenjangType = $this->determineJenjangType($pegawai, $jabatanLama, $jabatanTujuan);
        $formConfig = $this->getFormConfigByJenjang($jenjangType);

        $catatanPerbaikan = $usulanJabatan->getValidasiByRole('admin_fakultas');

        $bkdSemesters = $this->generateBkdSemesterLabels($usulanJabatan->periodeUsulan);

        // Get document fields for the form
        $documentFields = $this->getDocumentKeys($pegawai->jenis_pegawai);

        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulanJabatan,
            'jenjangType' => $jenjangType,
            'formConfig' => $formConfig,
            'jenisUsulanPeriode' => $jenisUsulanPeriode,
            'catatanPerbaikan' => $catatanPerbaikan,
            'bkdSemesters' => $bkdSemesters,
            'documentFields' => $documentFields,
        ]);
    }

    /**
     * Update the specified usulan jabatan
     * SIMPLIFIED: Back to standard {usulan} parameter
     */
    public function update(StoreJabatanUsulanRequest $request, Usulan $usulanJabatan)
    {

        // Authorization check
        if ($usulanJabatan->pegawai_id !== Auth::id()) {
            Log::warning('Unauthorized update attempt', [
                'usulan_id' => $usulanJabatan->id,
                'user_id' => Auth::id(),
                'owner_id' => $usulanJabatan->pegawai_id
            ]);
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki akses untuk mengubah usulan ini.');
        }

        // Status validation
        if ($usulanJabatan->is_read_only) {
            return redirect()->back()
                ->with('error', 'Usulan dengan status "' . $usulanJabatan->status_usulan . '" tidak dapat diubah.');
        }

        $validatedData = $request->validated();
        $pegawai = Auth::user();
        $oldStatus = $usulanJabatan->status_usulan;
        $statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        Log::info('Starting usulan update', [
            'usulan_id' => $usulanJabatan->id,
            'old_status' => $oldStatus,
            'new_status' => $statusUsulan,
            'user_id' => $pegawai->id,
            'action' => $request->input('action')
        ]);

        try {
            $updatedUsulan = null;

            DB::transaction(function () use ($request, $usulanJabatan, $pegawai, $statusUsulan, $validatedData, $oldStatus, &$updatedUsulan) {

                // Get existing data
                $dataUsulanLama = $usulanJabatan->data_usulan ?? [];

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
                    $this->updateDocuments($request, $usulanJabatan, $pegawai, $dataUsulanLama);
                    Log::info('Documents updated successfully', ['usulan_id' => $usulanJabatan->id]);
                } catch (\Throwable $e) {
                    Log::error('Document update failed', [
                        'usulan_id' => $usulanJabatan->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }

                // Update usulan record
                $updateData = [
                    'status_usulan' => $statusUsulan,
                    'data_usulan' => $dataUsulanLama,
                ];

                // Clear verifikator notes if status changes from "Perlu Perbaikan"
                if ($oldStatus === 'Dikembalikan ke Pegawai' && $statusUsulan === 'Diajukan') {
                    $updateData['catatan_verifikator'] = null;
                }

                $usulanJabatan->update($updateData);

                // Create log entry
                if ($oldStatus !== $statusUsulan) {
                    $this->createUsulanLog($usulanJabatan, $oldStatus, $statusUsulan, $pegawai, $validatedData);
                }

                $updatedUsulan = $usulanJabatan->fresh();
            });

            // Dispatch background jobs (outside transaction)
            if ($updatedUsulan && $oldStatus !== 'Diajukan' && $statusUsulan === 'Diajukan') {
                try {
                    $this->dispatchUsulanJobs($updatedUsulan, $statusUsulan);
                    Log::info('Background jobs dispatched', ['usulan_id' => $updatedUsulan->id]);
                } catch (\Throwable $e) {
                    Log::error('Failed to dispatch background jobs', [
                        'usulan_id' => $updatedUsulan->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = $statusUsulan === 'Diajukan'
                ? 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Tim verifikasi akan meninjau usulan Anda.'
                : 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.';

            Log::info('Usulan update completed successfully', [
                'usulan_id' => $usulanJabatan->id,
                'final_status' => $statusUsulan
            ]);

            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during update', [
                'usulan_id' => $usulanJabatan->id,
                'errors' => $e->errors()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('Failed to update usulan', [
                'usulan_id' => $usulanJabatan->id,
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
    public function showUsulanDocument(Usulan $usulanJabatan, $field)
    {

        if ($usulanJabatan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak punya akses ke dokumen ini');
        }

        // Validasi field yang diizinkan
        $allowedFields = [
            'pakta_integritas',
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'bukti_syarat_guru_besar',
            // BKD fields - pattern matching
        ];

        // Allow BKD fields dynamically
        if (str_starts_with($field, 'bkd_')) {
            $allowedFields[] = $field;
        }

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Coba struktur baru dulu
        $filePath = null;
        if (isset($usulanJabatan->data_usulan['dokumen_usulan'][$field]['path'])) {
            $filePath = $usulanJabatan->data_usulan['dokumen_usulan'][$field]['path'];
        }
        // Fallback ke struktur lama
        elseif (isset($usulanJabatan->data_usulan[$field])) {
            $filePath = $usulanJabatan->data_usulan[$field];
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Log document access
        Log::info('Document accessed', [
            'usulan_id' => $usulanJabatan->id,
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
    public function debugUpdate(Request $request, Usulan $usulanJabatan)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        return response()->json([
            'usulan_id' => $usulanJabatan->id,
            'current_status' => $usulanJabatan->status_usulan,
            'can_edit' => $usulanJabatan->can_edit,
            'is_read_only' => $usulanJabatan->is_read_only,
            'owner_id' => $usulanJabatan->pegawai_id,
            'current_user_id' => Auth::id(),
            'data_usulan_structure' => array_keys($usulanJabatan->data_usulan ?? []),
            'existing_documents' => $usulanJabatan->getExistingDocuments(),
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
            return 'usulan-jabatan-dosen';
        } elseif ($pegawai->jenis_pegawai === 'Tenaga Kependidikan' && $pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
            return 'usulan-jabatan-tendik';
        }

        return 'usulan-jabatan-dosen'; // Fallback
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
    protected function getDocumentKeys(string $jenisPegawai): array
    {
        $baseKeys = ['pakta_integritas'];

        if ($jenisPegawai === 'Dosen') {
            // Dinamis mengambil BKD slugs dari periode usulan saat ini
            $periodeId = request()->input('periode_usulan_id');
            $bkdSlugs = [];
            if ($periodeId) {
                $periode = PeriodeUsulan::find($periodeId);
                if ($periode) {
                    $bkdSemesters = $this->generateBkdSemesterLabels($periode);
                    $bkdSlugs = array_column($bkdSemesters, 'slug');
                }
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
     * Update documents dalam usulan - ENHANCED VERSION
     */
    protected function updateDocuments($request, $usulan, $pegawai, &$dataUsulanLama): void
    {
        $documentKeys = $this->getDocumentKeys($pegawai->jenis_pegawai);

        foreach ($documentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    // 1. Delete old document first
                    $this->deleteOldDocument($dataUsulanLama, $key, $usulan);

                    // 2. Upload new document
                    $uploadPath = 'usulan-dokumen/' . $pegawai->id . '/' . date('Y/m');
                    $file = $request->file($key);
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $key . '_' . time() . '_' . uniqid() . '.' . $extension;

                    $path = $file->storeAs($uploadPath, $fileName, 'local');

                    // 3. Prepare new document data
                    $newDocumentData = [
                        'path' => $path,
                        'original_name' => $originalName,
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

                    Log::info("Document updated successfully", [
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
     * Delete old document with proper cleanup - ENHANCED VERSION
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
                // Delete physical file
                if (Storage::disk('local')->exists($oldFilePath)) {
                    Storage::disk('local')->delete($oldFilePath);
                    Log::info("Old document file deleted", [
                        'file_path' => $oldFilePath,
                        'usulan_id' => $usulan->id
                    ]);
                }

                // Remove from data structure
                if (isset($dataUsulanLama['dokumen_usulan'][$key])) {
                    unset($dataUsulanLama['dokumen_usulan'][$key]);
                }
                if (isset($dataUsulanLama[$key])) {
                    unset($dataUsulanLama[$key]);
                }

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
}

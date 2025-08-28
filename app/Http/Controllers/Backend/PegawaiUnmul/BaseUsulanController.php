<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
use App\Models\KepegawaianUniversitas\UsulanLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessUsulanDocuments;
use App\Jobs\SendUsulanNotification;
use App\Jobs\GenerateUsulanReport;
use App\Services\FileStorageService;

abstract class BaseUsulanController extends Controller
{
    protected $fileStorage;

    public function __construct(FileStorageService $fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    /**
     * Get base required fields untuk validasi profil
     */
    protected function getBaseRequiredFields($pegawai): array
    {
        $baseFields = [
            'nip', 'nama_lengkap', 'email', 'jenis_pegawai', 'status_kepegawaian',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'nomor_handphone',
            'nomor_kartu_pegawai', 'pangkat_terakhir_id', 'tmt_pangkat',
            'jabatan_terakhir_id', 'tmt_jabatan', 'unit_kerja_id',
            'pendidikan_terakhir', 'tmt_cpns', 'tmt_pns',
            'predikat_kinerja_tahun_pertama', 'predikat_kinerja_tahun_kedua',
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns'
        ];

        // Additional fields for Dosen
        if ($pegawai->jenis_pegawai == 'Dosen') {
            $baseFields[] = 'mata_kuliah_diampu';
            $baseFields[] = 'ranting_ilmu_kepakaran';
        }

        return $baseFields;
    }

    /**
     * Validasi kelengkapan profil pegawai
     */
    protected function validateProfileCompleteness($pegawai): ?array
    {
        $requiredFields = $this->getBaseRequiredFields($pegawai);
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($pegawai->$field)) {
                $missingFields[] = $field;
            }
        }

        return !empty($missingFields) ? $missingFields : null;
    }

    /**
     * Cek apakah pegawai memiliki usulan aktif untuk jenis tertentu
     */
    protected function hasActiveUsulan($pegawaiId, string $jenisUsulan): bool
    {
        return Usulan::where('pegawai_id', $pegawaiId)
            ->where('jenis_usulan', $jenisUsulan)
            ->whereNotIn('status_usulan', [\App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN, \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN])
            ->exists();
    }

    /**
     * Get periode usulan yang aktif untuk jenis tertentu
     */
    protected function getActivePeriode(string $jenisUsulan): ?PeriodeUsulan
    {
        // Ambil periode berdasarkan jenis usulan yang tepat
        return PeriodeUsulan::where('jenis_usulan', $jenisUsulan)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where('status', 'Buka')
            ->orderBy('tanggal_mulai', 'desc')
            ->first();
    }

    /**
     * Create snapshot data pegawai saat usulan dibuat
     */
    protected function createPegawaiSnapshot($pegawai): array
    {
        return [
            // Data Pribadi
            'nip' => $pegawai->nip,
            'nuptk' => $pegawai->nuptk,
            'nama_lengkap' => $pegawai->nama_lengkap,
            'gelar_depan' => $pegawai->gelar_depan,
            'gelar_belakang' => $pegawai->gelar_belakang,
            'email' => $pegawai->email,
            'tempat_lahir' => $pegawai->tempat_lahir,
            'tanggal_lahir' => $pegawai->tanggal_lahir?->toDateString(),
            'jenis_kelamin' => $pegawai->jenis_kelamin,
            'nomor_handphone' => $pegawai->nomor_handphone,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,

            // Data Kepegawaian
            'pangkat_saat_usul' => $pegawai->pangkat?->pangkat,
            'pangkat_id' => $pegawai->pangkat_terakhir_id,
            'tmt_pangkat' => $pegawai->tmt_pangkat?->toDateString(),
            'jabatan_saat_usul' => $pegawai->jabatan?->jabatan,
            'jabatan_id' => $pegawai->jabatan_terakhir_id,
            'tmt_jabatan' => $pegawai->tmt_jabatan?->toDateString(),
            'unit_kerja_saat_usul' => $pegawai->unitKerja?->nama,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
            'tmt_cpns' => $pegawai->tmt_cpns?->toDateString(),
            'tmt_pns' => $pegawai->tmt_pns?->toDateString(),

            // Data Pendidikan & Fungsional
            'pendidikan_terakhir' => $pegawai->pendidikan_terakhir,
            'mata_kuliah_diampu' => $pegawai->mata_kuliah_diampu,
            'ranting_ilmu_kepakaran' => $pegawai->ranting_ilmu_kepakaran,
            'url_profil_sinta' => $pegawai->url_profil_sinta,

            // Data Kinerja
            'predikat_kinerja_tahun_pertama' => $pegawai->predikat_kinerja_tahun_pertama,
            'predikat_kinerja_tahun_kedua' => $pegawai->predikat_kinerja_tahun_kedua,
            'nilai_konversi' => $pegawai->nilai_konversi,

            // Dokumen Profil (path saja)
            'ijazah_terakhir' => $pegawai->ijazah_terakhir,
            'transkrip_nilai_terakhir' => $pegawai->transkrip_nilai_terakhir,
            'sk_pangkat_terakhir' => $pegawai->sk_pangkat_terakhir,
            'sk_jabatan_terakhir' => $pegawai->sk_jabatan_terakhir,
            'skp_tahun_pertama' => $pegawai->skp_tahun_pertama,
            'skp_tahun_kedua' => $pegawai->skp_tahun_kedua,
            'pak_konversi' => $pegawai->pak_konversi,
            'sk_cpns' => $pegawai->sk_cpns,
            'sk_pns' => $pegawai->sk_pns,
            'sk_penyetaraan_ijazah' => $pegawai->sk_penyetaraan_ijazah,
            'disertasi_thesis_terakhir' => $pegawai->disertasi_thesis_terakhir,
        ];
    }

    /**
     * Handle upload dokumen (generic untuk semua jenis usulan) - REFACTORED with FileStorageService
     */
    protected function handleDocumentUploads($request, $pegawai, array $documentKeys): array
    {
        $filePaths = [];
        $uploadPath = 'usulan-dokumen/' . $pegawai->id . '/' . date('Y/m');

        foreach ($documentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    $file = $request->file($key);

                    // Enhanced validation
                    $this->validateUploadedFile($file, $key);

                    // Use FileStorageService for upload
                    $path = $this->fileStorage->uploadFile($file, $uploadPath, $key);

                    $filePaths[$key] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                        'uploaded_by' => $pegawai->id,
                    ];

                    Log::info("Document uploaded successfully using FileStorageService", [
                        'document_key' => $key,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'pegawai_id' => $pegawai->id
                    ]);

                } catch (\Throwable $e) {
                    Log::error("Failed to upload document", [
                        'document_key' => $key,
                        'error' => $e->getMessage(),
                        'pegawai_id' => $pegawai->id
                    ]);
                    throw new \RuntimeException("Gagal mengupload dokumen $key: " . $e->getMessage());
                }
            }
        }

        return $filePaths;
    }

    /**
     * NEW: Enhanced file validation
     */
    protected function validateUploadedFile($file, string $key): void
    {
        // Check file size (2MB max for BKD, 1MB for others)
        $maxSize = strpos($key, 'bkd_semester') !== false ? 2 * 1024 * 1024 : 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            $maxSizeMB = $maxSize / (1024 * 1024);
            throw new \RuntimeException("File $key terlalu besar. Maksimal {$maxSizeMB}MB.");
        }

        // Check file type
        $allowedMimes = ['application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \RuntimeException("File $key harus berformat PDF.");
        }

        // Check file signature for PDF
        $handle = fopen($file->getRealPath(), 'r');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            throw new \RuntimeException("File $key bukan file PDF yang valid.");
        }

        // Check if file is readable
        if (!is_readable($file->getRealPath())) {
            throw new \RuntimeException("File $key tidak dapat dibaca.");
        }
    }

    /**
     * Simpan dokumen ke tabel usulan_dokumens
     */
    protected function saveUsulanDocuments($usulan, array $dokumenPaths, $pegawai): void
    {
        foreach ($dokumenPaths as $nama => $fileData) {
            UsulanDokumen::create([
                'usulan_id' => $usulan->id,
                'diupload_oleh_id' => $pegawai->id,
                'nama_dokumen' => $nama,
                'path' => $fileData['path'],
            ]);
        }
    }

    /**
     * Buat log usulan
     */
    protected function createUsulanLog($usulan, $statusLama, $statusBaru, $pegawai, $validatedData = []): void
    {
        try {
            // Ensure usulan exists and has ID
            if (!$usulan || !$usulan->exists || !$usulan->getKey()) {
                throw new \RuntimeException('Cannot create log for usulan that doesn\'t exist');
            }

            $catatan = match($statusBaru) {
                'Draft' => $statusLama ? 'Usulan diperbarui sebagai draft' : 'Usulan disimpan sebagai draft oleh pegawai',
                'Diajukan' => $statusLama === 'Draft' ? 'Usulan diajukan oleh pegawai untuk review' : 'Usulan diperbarui dan diajukan ulang',
                default => 'Status usulan diubah'
            };

            if (!empty($validatedData['catatan'])) {
                $catatan .= '. Catatan: ' . $validatedData['catatan'];
            }

            UsulanLog::create([
                'usulan_id' => $usulan->getKey(),
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
                'dilakukan_oleh_id' => $pegawai->id,
            ]);

            Log::info('Usulan log created successfully', [
                'usulan_id' => $usulan->getKey(),
                'status_change' => "{$statusLama} -> {$statusBaru}",
                'user_id' => $pegawai->id
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to create usulan log', [
                'usulan_id' => $usulan?->getKey(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception to prevent breaking the main flow
        }
    }

    /**
     * Dispatch background jobs untuk usulan
     */
    protected function dispatchUsulanJobs($usulan, string $status): void
    {
        try {
            // Process documents (always)
            ProcessUsulanDocuments::dispatch($usulan)
                ->delay(now()->addSeconds(10));

            // Send notifications and generate reports (for submitted and university submissions)
            if (in_array($status, ['Diajukan', 'Diusulkan ke Universitas'])) {
                SendUsulanNotification::dispatch($usulan, 'submitted')
                    ->delay(now()->addSeconds(5));

                GenerateUsulanReport::dispatch($usulan)
                    ->delay(now()->addMinutes(2));
            }

            Log::info('Background jobs dispatched', [
                'usulan_id' => $usulan->id,
                'status' => $status
            ]);

        } catch (\Throwable $e) {
            Log::error('Gagal dispatch background jobs', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception, just log it
        }
    }

    /**
     * Show dokumen usulan dengan access control
     */
    protected function showDocument($usulan, $field)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak punya akses ke dokumen ini');
        }

        // Coba struktur baru dulu (dengan nested structure)
        $filePath = null;
        if (isset($usulan->data_usulan['dokumen_usulan'][$field]['path'])) {
            $filePath = $usulan->data_usulan['dokumen_usulan'][$field]['path'];
        }
        // Fallback ke struktur lama (untuk backward compatibility)
        elseif (isset($usulan->data_usulan[$field])) {
            $filePath = $usulan->data_usulan[$field];
        }

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
     * Get usulan logs - SIMPLE HTML VIEW
     */
    protected function getUsulanLogs($usulan)
    {
        // Pastikan hanya pemilik usulan yang bisa melihat log
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            // Simple query without eager loading to avoid potential issues
            $logs = $usulan->logs()
                ->orderBy('created_at', 'desc')
                ->limit(50) // Limit to prevent infinite loops
                ->get();

            $formattedLogs = [];

            foreach ($logs as $log) {
                // Get user name safely
                $userName = 'System';
                if ($log->dilakukan_oleh_id) {
                    $user = Pegawai::find($log->dilakukan_oleh_id);
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

        // Return simple HTML view instead of JSON
        return view('backend.layouts.views.pegawai-unmul.logs-simple', [
            'logs' => $formattedLogs,
            'usulan' => $usulanWithRelations
        ]);

        } catch (\Throwable $e) {
            Log::error('Error getting usulan logs: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Gagal mengambil data log: ' . $e->getMessage());
        }
    }
}

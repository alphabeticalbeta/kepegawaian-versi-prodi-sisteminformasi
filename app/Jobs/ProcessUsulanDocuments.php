<?php

namespace App\Jobs;

use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\UsulanLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ProcessUsulanDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $usulan;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Usulan $usulan)
    {
        $this->usulan = $usulan;
        $this->onQueue('documents'); // Specific queue for documents
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting document processing', ['usulan_id' => $this->usulan->id]);

        try {
            DB::beginTransaction();

            $dataUsulan = $this->usulan->data_usulan;
            $processedFiles = [];
            $errors = [];

            // Document fields to process
            $documentFields = [
                'bukti_korespondensi' => ['max_size' => 2048000, 'type' => 'pdf'],
                'pakta_integritas' => ['max_size' => 2048000, 'type' => 'pdf'],
                'turnitin' => ['max_size' => 2048000, 'type' => 'pdf'],
                'upload_artikel' => ['max_size' => 5120000, 'type' => 'pdf'],
                'bukti_syarat_guru_besar' => ['max_size' => 2048000, 'type' => 'pdf'],
                'sk_pangkat_terakhir' => ['max_size' => 2048000, 'type' => 'pdf'],
                'sk_jabatan_terakhir' => ['max_size' => 2048000, 'type' => 'pdf'],
                'ijazah_terakhir' => ['max_size' => 2048000, 'type' => 'pdf'],
                'transkrip_nilai_terakhir' => ['max_size' => 2048000, 'type' => 'pdf'],
            ];

            foreach ($documentFields as $field => $rules) {
                if (!empty($dataUsulan[$field])) {
                    $result = $this->processDocument($field, $dataUsulan[$field], $rules);

                    if ($result['success']) {
                        $processedFiles[$field] = $result['data'];
                    } else {
                        $errors[$field] = $result['error'];
                    }
                }
            }

            // Update usulan with processed data
            if (!empty($processedFiles)) {
                $updatedData = array_merge($dataUsulan, ['processed_files' => $processedFiles]);
                $this->usulan->update(['data_usulan' => $updatedData]);
            }

            // Log the processing
            UsulanLog::create([
                'usulan_id' => $this->usulan->id,
                'status_sebelumnya' => $this->usulan->status_usulan,
                'status_baru' => $this->usulan->status_usulan,
                'catatan' => 'Dokumen berhasil diproses: ' . count($processedFiles) . ' file',
                'dilakukan_oleh_id' => null, // System process
            ]);

            DB::commit();

            Log::info('Document processing completed', [
                'usulan_id' => $this->usulan->id,
                'processed' => count($processedFiles),
                'errors' => count($errors)
            ]);

            // If there are errors, notify
            if (!empty($errors)) {
                $this->notifyErrors($errors);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document processing failed', [
                'usulan_id' => $this->usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process individual document
     */
    private function processDocument($fieldName, $filePath, $rules)
    {
        try {
            // Check if file exists
            if (!Storage::disk('local')->exists($filePath)) {
                return [
                    'success' => false,
                    'error' => 'File tidak ditemukan'
                ];
            }

            // Get file info
            $size = Storage::disk('local')->size($filePath);
            $mimeType = File::mimeType(Storage::disk('local')->path($filePath));

            // Validate size
            if ($size > $rules['max_size']) {
                return [
                    'success' => false,
                    'error' => 'Ukuran file melebihi batas (' . ($rules['max_size'] / 1048576) . 'MB)'
                ];
            }

            // Validate type
            $expectedMime = $rules['type'] === 'pdf' ? 'application/pdf' : $mimeType;
            if ($rules['type'] === 'pdf' && $mimeType !== 'application/pdf') {
                return [
                    'success' => false,
                    'error' => 'File harus berformat PDF'
                ];
            }

            // Generate metadata
            $metadata = [
                'original_path' => $filePath,
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'mime_type' => $mimeType,
                'processed_at' => now()->toDateTimeString(),
                'hash' => hash_file('md5', Storage::disk('local')->path($filePath))
            ];

            // Additional processing based on type
            if ($fieldName === 'turnitin') {
                // Extract similarity percentage if possible
                // This would require PDF parsing library
                $metadata['similarity_check'] = true;
            }

            return [
                'success' => true,
                'data' => $metadata
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Notify about errors
     */
    private function notifyErrors($errors)
    {
        // Send notification to admin or user
        Log::warning('Document processing had errors', [
            'usulan_id' => $this->usulan->id,
            'errors' => $errors
        ]);

        // You can dispatch email notification job here
        // SendDocumentErrorNotification::dispatch($this->usulan, $errors);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessUsulanDocuments job completely failed', [
            'usulan_id' => $this->usulan->id,
            'error' => $exception->getMessage()
        ]);

        // Update usulan status or add failure log
        UsulanLog::create([
            'usulan_id' => $this->usulan->id,
            'status_sebelumnya' => $this->usulan->status_usulan,
            'status_baru' => $this->usulan->status_usulan,
            'catatan' => 'Gagal memproses dokumen: ' . $exception->getMessage(),
            'dilakukan_oleh_id' => null,
        ]);
    }
}

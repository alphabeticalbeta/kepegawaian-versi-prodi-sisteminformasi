<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileStorageService
{
    private $maxFileSize;
    private $allowedMimes;
    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
        $this->maxFileSize = 2048; // 2MB
        $this->allowedMimes = ['pdf', 'doc', 'docx'];
    }

    /**
     * Upload file dengan validasi dan logging
     */
    public function uploadFile($file, $path, $field, $filename = null)
    {
        try {
            // Validasi file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $filename ?? $this->generateUniqueFilename($file);

            // Determine disk based on field type
            $disk = $this->documentAccessService->getDiskForField($field);

            // Upload file
            $filePath = $file->storeAs($path, $filename, $disk);

            // Log upload activity
            $this->logUploadActivity($filePath, $file->getSize(), $disk);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'field' => $field,
                'disk' => $this->documentAccessService->getDiskForField($field)
            ]);
            throw $e;
        }
    }

    /**
     * Delete file dengan logging
     */
    public function deleteFile($filePath, $field = null)
    {
        try {
            // Determine disk based on field type
            $disk = $field ? $this->documentAccessService->getDiskForField($field) : 'local';
            
            if (Storage::disk($disk)->exists($filePath)) {
                Storage::disk($disk)->delete($filePath);
                $this->logDeleteActivity($filePath, $disk);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'file_path' => $filePath,
                'field' => $field,
                'disk' => $disk ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Handle dokumen pendukung upload
     */
    public function handleDokumenPendukung(Request $request, $usulan, $fieldName, $storagePath)
    {
        $debugInfo = [
            'usulan_id' => $usulan->id,
            'field_name' => $fieldName,
            'storage_path' => $storagePath,
            'request_method' => $request->method(),
            'request_content_type' => $request->header('Content-Type'),
            'all_request_keys' => array_keys($request->all())
        ];

        $hasExistingFile = !empty($usulan->getDocumentPath($fieldName));
        $debugInfo['has_existing_file'] = $hasExistingFile;
        $debugInfo['existing_file_path'] = $usulan->getDocumentPath($fieldName);

        // Support both bracket notation and dot notation for compatibility
        $hasNewFileDot = $request->hasFile("dokumen_pendukung.{$fieldName}");
        $hasNewFileBracket = $request->hasFile("dokumen_pendukung[{$fieldName}]");

        $debugInfo['has_new_file_dot'] = $hasNewFileDot;
        $debugInfo['has_new_file_bracket'] = $hasNewFileBracket;
        $debugInfo['has_new_file'] = $hasNewFileDot || $hasNewFileBracket;

        if ($hasNewFileDot || $hasNewFileBracket) {
            // Try dot notation first, then bracket notation
            $file = $request->file("dokumen_pendukung.{$fieldName}") ??
                   $request->file("dokumen_pendukung[{$fieldName}]");

            if ($file) {
                $debugInfo['file_details'] = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError()
                ];

                $debugInfo['notation_used'] = $hasNewFileDot ? 'dot' : 'bracket';
            }

            try {
                $filePath = $this->uploadFile($file, $storagePath, $fieldName);
                $debugInfo['upload_success'] = true;
                $debugInfo['uploaded_file_path'] = $filePath;

                Log::info('Dokumen pendukung uploaded successfully', [
                    'usulan_id' => $usulan->id,
                    'field_name' => $fieldName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'notation_used' => $debugInfo['notation_used'],
                    'debug_info' => $debugInfo
                ]);

                return $filePath;
            } catch (\Exception $e) {
                $debugInfo['upload_success'] = false;
                $debugInfo['upload_error'] = $e->getMessage();

                Log::error('Dokumen pendukung upload failed', [
                    'usulan_id' => $usulan->id,
                    'field_name' => $fieldName,
                    'error' => $e->getMessage(),
                    'debug_info' => $debugInfo
                ]);

                throw $e;
            }
        } else {
            $debugInfo['upload_success'] = false;
            $debugInfo['upload_error'] = 'No new file provided';

            // Try to get existing file path from validasi_data first
            $existingPath = null;
            $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
            $pathKey = $fieldName . '_path';

            // Check for new key format first (e.g., 'file_surat_usulan_path')
            if (!empty($dokumenPendukung[$pathKey])) {
                $existingPath = $dokumenPendukung[$pathKey];
                $debugInfo['existing_path_from_validasi'] = $existingPath;
                $debugInfo['lookup_key_used'] = $pathKey;
            } 
            // Fallback to old key format (e.g., 'file_surat_usulan')
            elseif (!empty($dokumenPendukung[$fieldName])) {
                $existingPath = $dokumenPendukung[$fieldName];
                $debugInfo['existing_path_from_validasi'] = $existingPath;
                $debugInfo['lookup_key_used'] = $fieldName;
            }

            // If not found in validasi_data, try getDocumentPath method as a final fallback
            if (empty($existingPath)) {
                $existingPath = $usulan->getDocumentPath($fieldName);
                $debugInfo['existing_path_from_getDocumentPath'] = $existingPath;
            }

            Log::info('Dokumen pendukung - using existing file', [
                'usulan_id' => $usulan->id,
                'field_name' => $fieldName,
                'existing_file_path' => $existingPath,
                'debug_info' => $debugInfo
            ]);

            return $existingPath;
        }
    }

    /**
     * Validate file sebelum upload
     */
    private function validateFile($file)
    {
        $validator = Validator::make(['file' => $file], [
            'file' => "required|file|mimes:" . implode(',', $this->allowedMimes) . "|max:{$this->maxFileSize}"
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    /**
     * Generate unique filename
     */
    private function generateUniqueFilename($file)
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);

        return "{$originalName}_{$timestamp}_{$randomString}.{$extension}";
    }

    /**
     * Log upload activity
     */
    private function logUploadActivity($filePath, $fileSize, $disk)
    {
        Log::info('File uploaded successfully', [
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'disk' => $disk,
            'uploaded_at' => now()
        ]);
    }

    /**
     * Log delete activity
     */
    private function logDeleteActivity($filePath, $disk)
    {
        Log::info('File deleted successfully', [
            'file_path' => $filePath,
            'disk' => $disk,
            'deleted_at' => now()
        ]);
    }

    /**
     * Get storage usage statistics
     */
    public function getStorageUsage()
    {
        $localSize = Storage::disk('local')->size('/');
        $publicSize = Storage::disk('public')->size('/');
        $totalSize = $localSize + $publicSize;
        $usagePercentage = ($totalSize / (1024 * 1024 * 1024 * 100)) * 100; // 100GB limit

        return [
            'local_size_bytes' => $localSize,
            'public_size_bytes' => $publicSize,
            'total_size_bytes' => $totalSize,
            'total_size_gb' => $totalSize / (1024 * 1024 * 1024),
            'usage_percentage' => $usagePercentage,
            'disks' => ['local', 'public']
        ];
    }

    /**
     * Get disk for field
     */
    public function getDiskForField(string $field): string
    {
        return $this->documentAccessService->getDiskForField($field);
    }
}

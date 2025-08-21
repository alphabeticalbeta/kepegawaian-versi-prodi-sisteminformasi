<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileStorageService
{
    private $disk;
    private $maxFileSize;
    private $allowedMimes;

    public function __construct()
    {
        $this->disk = 'public';
        $this->maxFileSize = 2048; // 2MB
        $this->allowedMimes = ['pdf', 'doc', 'docx'];
    }

    /**
     * Upload file dengan validasi dan logging
     */
    public function uploadFile($file, $path, $filename = null)
    {
        try {
            // Validasi file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $filename ?? $this->generateUniqueFilename($file);

            // Upload file
            $filePath = $file->storeAs($path, $filename, $this->disk);

            // Log upload activity
            $this->logUploadActivity($filePath, $file->getSize());

            return $filePath;
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize()
            ]);
            throw $e;
        }
    }

    /**
     * Delete file dengan logging
     */
    public function deleteFile($filePath)
    {
        try {
            if (Storage::disk($this->disk)->exists($filePath)) {
                Storage::disk($this->disk)->delete($filePath);
                $this->logDeleteActivity($filePath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
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
                $filePath = $this->uploadFile($file, $storagePath);
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

            Log::info('Dokumen pendukung - using existing file', [
                'usulan_id' => $usulan->id,
                'field_name' => $fieldName,
                'existing_file_path' => $usulan->getDocumentPath($fieldName),
                'debug_info' => $debugInfo
            ]);
        }

        return $usulan->getDocumentPath($fieldName);
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
    private function logUploadActivity($filePath, $fileSize)
    {
        Log::info('File uploaded successfully', [
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'disk' => $this->disk,
            'uploaded_at' => now()
        ]);
    }

    /**
     * Log delete activity
     */
    private function logDeleteActivity($filePath)
    {
        Log::info('File deleted successfully', [
            'file_path' => $filePath,
            'disk' => $this->disk,
            'deleted_at' => now()
        ]);
    }

    /**
     * Get storage usage statistics
     */
    public function getStorageUsage()
    {
        $totalSize = Storage::disk($this->disk)->size('/');
        $usagePercentage = ($totalSize / (1024 * 1024 * 1024 * 100)) * 100; // 100GB limit

        return [
            'total_size_bytes' => $totalSize,
            'total_size_gb' => $totalSize / (1024 * 1024 * 1024),
            'usage_percentage' => $usagePercentage,
            'disk' => $this->disk
        ];
    }
}

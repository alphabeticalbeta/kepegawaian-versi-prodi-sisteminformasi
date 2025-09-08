<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiController extends Controller
{
    /**
     * Get current struktur organisasi image
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get the latest uploaded file
            $directory = 'struktur-organisasi';
            $files = Storage::disk('public')->files($directory);

            if (empty($files)) {
                $response = response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Belum ada gambar struktur organisasi'
                ]);
            } else {
                // Get the most recent file
                $latestFile = collect($files)->sortByDesc(function ($file) {
                    return Storage::disk('public')->lastModified($file);
                })->first();

                $filename = basename($latestFile);
                $imageUrl = asset('storage/' . $latestFile);

                $strukturData = [
                    'id' => 1,
                    'image_url' => $imageUrl,
                    'description' => 'Struktur organisasi Universitas Mulawarman',
                    'filename' => $filename,
                    'path' => $latestFile,
                    'created_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile)),
                    'updated_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile))
                ];

                $response = response()->json([
                    'success' => true,
                    'data' => $strukturData,
                    'message' => 'Data struktur organisasi berhasil diambil'
                ]);
            }

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            \Log::error('Error in index method:', ['error' => $e->getMessage()]);

            $response = response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Terjadi kesalahan saat mengambil data struktur organisasi: ' . $e->getMessage()
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Upload/Update struktur organisasi image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Debug: Log request data
            \Log::info('Struktur Organisasi Store Request:', [
                'has_file' => $request->hasFile('struktur_image'),
                'file_size' => $request->hasFile('struktur_image') ? $request->file('struktur_image')->getSize() : null,
                'file_name' => $request->hasFile('struktur_image') ? $request->file('struktur_image')->getClientOriginalName() : null,
                'description' => $request->input('description'),
                'all_input' => $request->all()
            ]);

            // Validate request
            $request->validate([
                'struktur_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'description' => 'nullable|string|max:500'
            ]);

            // Handle file upload
            if ($request->hasFile('struktur_image')) {
                $file = $request->file('struktur_image');
                $filename = 'struktur-organisasi-' . time() . '.' . $file->getClientOriginalExtension();

                // Ensure directory exists
                $directory = 'struktur-organisasi';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Store file in public storage
                $path = $file->storeAs($directory, $filename, 'public');

                // Generate URL
                $imageUrl = asset('storage/' . $path);

                // Here you would save to database
                // For now, we'll return the uploaded file info
                $strukturData = [
                    'id' => 1,
                    'image_url' => $imageUrl,
                    'description' => $request->input('description'),
                    'filename' => $filename,
                    'path' => $path,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                \Log::info('File uploaded successfully:', $strukturData);

                $response = response()->json([
                    'success' => true,
                    'data' => $strukturData,
                    'message' => 'Gambar struktur organisasi berhasil diupload'
                ]);

                // Add CORS headers
                $response->header('Access-Control-Allow-Origin', '*');
                $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

                return $response;
            }

            throw new \Exception('File tidak ditemukan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $response = response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
            ], 422);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            $response = response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage()
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Delete struktur organisasi image
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        try {
            // Here you would delete from database and storage
            // For now, we'll just return success

            $response = response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Gambar struktur organisasi berhasil dihapus'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            $response = response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage()
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }
}

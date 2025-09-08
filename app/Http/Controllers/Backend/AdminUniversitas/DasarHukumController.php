<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\DasarHukum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DasarHukumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.layouts.views.admin-universitas.dasar-hukum');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis_dasar_hukum' => 'required|in:keputusan,pedoman,peraturan,surat_edaran,surat_kementerian,surat_rektor,undang_undang',
                'sub_jenis' => 'nullable|in:peraturan,surat_keputusan,sk_non_pns',
                'nomor_dokumen' => 'required|string|max:100',
                'tanggal_dokumen' => 'required|date',
                'nama_instansi' => 'required|string|max:255',
                'masa_berlaku' => 'nullable|date',
                'penulis' => 'required|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'required|array|min:1',
                'lampiran.*' => 'required|file|mimes:pdf|max:10240',
                'status' => 'required|in:draft,published,archived',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle thumbnail upload
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . Str::slug($request->judul) . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnailPath = $thumbnail->storeAs('dasar-hukum/thumbnails', $thumbnailName, 'public');
            }

            // Handle lampiran upload
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/dasar-hukum/lampiran', $fileName);
                    $lampiranPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
            }

            // Handle file uploads
            $data = $request->except(['thumbnail', 'lampiran']);

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
                $thumbnailPath = $thumbnail->storeAs('public/dasar-hukum/thumbnails', $thumbnailName);
                $data['thumbnail'] = Storage::url($thumbnailPath);
            }

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                $data['lampiran'] = $lampiranPaths;
            }

            // Parse tags
            if ($request->tags && trim($request->tags)) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            } else {
                $data['tags'] = null;
            }

            // Auto-fill penulis (always from logged in user)
            if (Auth::guard('pegawai')->check()) {
                $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
            } else {
                $data['penulis'] = Auth::user()->name ?? 'Administrator';
            }

            // FIFO Logic untuk Featured (max 5)
            if (isset($data['is_featured']) && $data['is_featured']) {
                $currentFeatured = DasarHukum::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = DasarHukum::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3)
            if (isset($data['is_pinned']) && $data['is_pinned']) {
                $currentPinned = DasarHukum::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = DasarHukum::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }

            // Save to database
            $dasarHukum = DasarHukum::create($data);

            Log::info('Dasar Hukum data saved to database', [
                'id' => $dasarHukum->id,
                'jenis_dasar_hukum' => $request->jenis_dasar_hukum,
                'judul' => $request->judul
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $dasarHukum
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving dasar hukum data to database', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $dasarHukum
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis_dasar_hukum' => 'required|in:keputusan,pedoman,peraturan,surat_edaran,surat_kementerian,surat_rektor,undang_undang',
                'sub_jenis' => 'nullable|in:peraturan,surat_keputusan,sk_non_pns',
                'nomor_dokumen' => 'required|string|max:100',
                'tanggal_dokumen' => 'required|date',
                'nama_instansi' => 'required|string|max:255',
                'masa_berlaku' => 'nullable|date',
                'penulis' => 'required|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'nullable|array',
                'lampiran.*' => 'file|mimes:pdf|max:10240',
                'status' => 'required|in:draft,published,archived',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file uploads
            $data = $request->except(['thumbnail', 'lampiran']);

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($dasarHukum->thumbnail) {
                    $oldThumbnail = str_replace('/storage/', 'public/', $dasarHukum->thumbnail);
                    Storage::delete($oldThumbnail);
                }

                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
                $thumbnailPath = $thumbnail->storeAs('public/dasar-hukum/thumbnails', $thumbnailName);
                $data['thumbnail'] = Storage::url($thumbnailPath);
            }

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                $lampiranFiles = [];
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/dasar-hukum/lampiran', $fileName);
                    $lampiranFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
                $data['lampiran'] = $lampiranFiles;
            }

            // Parse tags
            if ($request->tags && trim($request->tags)) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            } else {
                $data['tags'] = null;
            }

            // Auto-fill penulis (always from logged in user)
            if (Auth::guard('pegawai')->check()) {
                $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
            } else {
                $data['penulis'] = Auth::user()->name ?? 'Administrator';
            }

            // FIFO Logic untuk Featured (max 5) - hanya jika item ini belum featured
            if (isset($data['is_featured']) && $data['is_featured'] && !$dasarHukum->is_featured) {
                $currentFeatured = DasarHukum::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = DasarHukum::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3) - hanya jika item ini belum pinned
            if (isset($data['is_pinned']) && $data['is_pinned'] && !$dasarHukum->is_pinned) {
                $currentPinned = DasarHukum::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = DasarHukum::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }

            // Update in database
            $dasarHukum->update($data);

            Log::info('Dasar Hukum data updated in database', [
                'id' => $dasarHukum->id,
                'jenis_dasar_hukum' => $request->jenis_dasar_hukum,
                'judul' => $request->judul
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!',
                'data' => $dasarHukum
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating dasar hukum data in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            // Delete files
            if ($dasarHukum->thumbnail) {
                $thumbnail = str_replace('/storage/', 'public/', $dasarHukum->thumbnail);
                Storage::delete($thumbnail);
            }

            if ($dasarHukum->lampiran) {
                foreach ($dasarHukum->lampiran as $file) {
                    $filePath = str_replace('/storage/', 'public/', $file['path']);
                    Storage::delete($filePath);
                }
            }

            // Delete from database
            $dasarHukum->delete();

            Log::info('Dasar Hukum data deleted from database', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting dasar hukum data from database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dasar hukum data for API
     */
    public function getData(Request $request)
    {
        try {
            $query = DasarHukum::query();

            // Search
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Filter by jenis
            if ($request->has('jenis') && $request->jenis) {
                $query->byJenis($request->jenis);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by sub_jenis
            if ($request->has('sub_jenis') && $request->sub_jenis) {
                $query->where('sub_jenis', $request->sub_jenis);
            }

            // Sort
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'title':
                    $query->orderBy('judul', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            
            Log::info('Dasar Hukum API Request', [
                'page' => $page,
                'per_page' => $perPage,
                'search' => $request->search,
                'jenis' => $request->jenis,
                'status' => $request->status
            ]);

            $dasarHukum = $query->paginate($perPage, ['*'], 'page', $page);

            Log::info('Dasar Hukum API Response', [
                'total' => $dasarHukum->total(),
                'current_page' => $dasarHukum->currentPage(),
                'last_page' => $dasarHukum->lastPage(),
                'per_page' => $dasarHukum->perPage(),
                'items_count' => count($dasarHukum->items())
            ]);

            return response()->json([
                'success' => true,
                'data' => $dasarHukum->items(),
                'current_page' => $dasarHukum->currentPage(),
                'last_page' => $dasarHukum->lastPage(),
                'per_page' => $dasarHukum->perPage(),
                'total' => $dasarHukum->total(),
                'from' => $dasarHukum->firstItem(),
                'to' => $dasarHukum->lastItem()
            ]);

        } catch (\Exception $e) {
            Log::error('Dasar Hukum API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show document file
     */
    public function showDocument($filename)
    {
        // Authentication check - user must be logged in
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Anda harus login untuk mengakses dokumen ini.');
        }

        // Get file path from storage - check both lampiran and thumbnails
        $filePath = 'public/dasar-hukum/lampiran/' . $filename;

        // Check if file exists in lampiran folder
        if (!Storage::disk('local')->exists($filePath)) {
            // Check in thumbnails folder
            $thumbnailPath = 'public/dasar-hukum/thumbnails/' . $filename;
            if (Storage::disk('local')->exists($thumbnailPath)) {
                $filePath = $thumbnailPath;
            } else {
                abort(404, 'File tidak ditemukan: ' . $filename);
            }
        }

        // Get the full path to the file
        $fullPath = Storage::disk('local')->path($filePath);

        // Return the file
        return response()->file($fullPath);
    }
}

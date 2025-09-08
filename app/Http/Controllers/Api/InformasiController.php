<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class InformasiController extends Controller
{
    /**
     * Get published informasi data for frontend
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Informasi::published()->notExpired();

            // Filter by jenis
            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            // Filter by tags
            if ($request->filled('tags')) {
                $tags = explode(',', $request->tags);
                $query->where(function($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhereJsonContains('tags', trim($tag));
                    }
                });
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%");
                });
            }

            // Sort
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'popular':
                    $query->orderBy('view_count', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'latest':
                default:
                    $query->byPriority()->orderBy('created_at', 'desc');
                    break;
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $informasi = $query->paginate($perPage);

            Log::info('Informasi API called from database', [
                'data_count' => $informasi->total(),
                'filters' => $request->only(['jenis', 'tags', 'search'])
            ]);

            $response = response()->json([
                'success' => true,
                'data' => $informasi->items(),
                'pagination' => [
                    'current_page' => $informasi->currentPage(),
                    'last_page' => $informasi->lastPage(),
                    'per_page' => $informasi->perPage(),
                    'total' => $informasi->total(),
                    'from' => $informasi->firstItem(),
                    'to' => $informasi->lastItem(),
                    'has_more' => $informasi->hasMorePages()
                ],
                'message' => 'Data informasi berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Informasi API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data informasi'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Get featured informasi (for homepage)
     *
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        try {
            $featured = Informasi::published()
                ->notExpired()
                ->featured()
                ->byPriority()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            Log::info('Featured Informasi API called', [
                'data_count' => $featured->count()
            ]);

            $response = response()->json([
                'success' => true,
                'data' => $featured,
                'message' => 'Data informasi featured berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Featured Informasi API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data informasi featured'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Get pinned pengumuman
     *
     * @return JsonResponse
     */
    public function pinned(): JsonResponse
    {
        try {
            $pinned = Informasi::published()
                ->notExpired()
                ->pengumuman()
                ->pinned()
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            Log::info('Pinned Pengumuman API called', [
                'data_count' => $pinned->count()
            ]);

            $response = response()->json([
                'success' => true,
                'data' => $pinned,
                'message' => 'Data pengumuman pinned berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Pinned Pengumuman API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data pengumuman pinned'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Get single informasi by ID
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $informasi = Informasi::published()->notExpired()->findOrFail($id);

            // Increment view count
            $informasi->incrementViewCount();

            Log::info('Informasi detail API called', [
                'id' => $id,
                'judul' => $informasi->judul
            ]);

            $response = response()->json([
                'success' => true,
                'data' => $informasi,
                'message' => 'Data informasi berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Informasi detail API', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Data informasi tidak ditemukan'
            ], 404);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }

    /**
     * Get latest informasi (for homepage)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function latest(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 6);

            $latest = Informasi::published()
                ->notExpired()
                ->byPriority()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            Log::info('Latest Informasi API called', [
                'data_count' => $latest->count(),
                'limit' => $limit
            ]);

            $response = response()->json([
                'success' => true,
                'data' => $latest,
                'message' => 'Data informasi terbaru berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Latest Informasi API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data informasi terbaru'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }
}

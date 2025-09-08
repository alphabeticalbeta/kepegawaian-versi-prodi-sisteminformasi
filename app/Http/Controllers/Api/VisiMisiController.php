<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisiMisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class VisiMisiController extends Controller
{
    /**
     * Get active visi and misi data for frontend
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get data from database
            $visiMisiData = VisiMisi::aktif()->orderBy('created_at', 'desc')->get();

            Log::info('Visi Misi API called from database', ['data_count' => $visiMisiData->count()]);

            $response = response()->json([
                'success' => true,
                'data' => $visiMisiData,
                'message' => 'Data visi dan misi berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Visi Misi API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data visi dan misi'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }
}

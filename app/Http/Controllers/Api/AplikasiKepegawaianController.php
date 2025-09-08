<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AplikasiKepegawaian;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AplikasiKepegawaianController extends Controller
{
    /**
     * Get active aplikasi kepegawaian data for frontend
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get data from database
            $aplikasiData = AplikasiKepegawaian::aktif()->orderBy('created_at', 'desc')->get();

            Log::info('Aplikasi Kepegawaian API called from database', ['data_count' => $aplikasiData->count()]);

            $response = response()->json([
                'success' => true,
                'data' => $aplikasiData,
                'message' => 'Data aplikasi kepegawaian berhasil diambil'
            ]);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in Aplikasi Kepegawaian API', [
                'error' => $e->getMessage()
            ]);

            $response = response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data aplikasi kepegawaian'
            ], 500);

            // Add CORS headers
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            return $response;
        }
    }
}

<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\AplikasiKepegawaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AplikasiKepegawaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.layouts.views.admin-universitas.aplikasi-kepegawaian', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Get data for API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            // Validate input parameters
            $request->validate([
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:aktif,tidak_aktif'
            ]);

            // Check authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Build query with filters
            $query = AplikasiKepegawaian::query();

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama_aplikasi', 'like', "%{$search}%")
                      ->orWhere('sumber', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Apply pagination
            $limit = $request->input('limit', 10);
            $aplikasiData = $query->orderBy('created_at', 'desc')->paginate($limit);

            Log::info('Aplikasi Kepegawaian data retrieved', [
                'user_id' => Auth::id(),
                'count' => $aplikasiData->count(),
                'filters' => $request->only(['search', 'status'])
            ]);

            return response()->json([
                'success' => true,
                'data' => $aplikasiData->items(),
                'pagination' => [
                    'current_page' => $aplikasiData->currentPage(),
                    'last_page' => $aplikasiData->lastPage(),
                    'per_page' => $aplikasiData->perPage(),
                    'total' => $aplikasiData->total()
                ],
                'message' => 'Data aplikasi kepegawaian berhasil diambil'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error getting aplikasi kepegawaian data from database', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Check authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Validate request
            $request->validate([
                'nama_aplikasi' => 'required|string|max:255',
                'sumber' => 'required|string|max:255',
                'keterangan' => 'required|string|max:1000',
                'link' => 'required|url|max:500',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Sanitize input
            $data = [
                'nama_aplikasi' => strip_tags(trim($request->nama_aplikasi)),
                'sumber' => strip_tags(trim($request->sumber)),
                'keterangan' => strip_tags(trim($request->keterangan)),
                'link' => filter_var($request->link, FILTER_SANITIZE_URL),
                'status' => $request->status
            ];

            // Save to database
            $aplikasi = AplikasiKepegawaian::create($data);

            Log::info('Aplikasi Kepegawaian data saved to database', [
                'user_id' => Auth::id(),
                'id' => $aplikasi->id,
                'nama_aplikasi' => $data['nama_aplikasi']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $aplikasi
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error saving aplikasi kepegawaian data to database', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'nama_aplikasi' => 'required|string|max:255',
                'sumber' => 'required|string|max:255',
                'keterangan' => 'required|string|max:1000',
                'link' => 'required|url|max:500',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Find and update in database
            $aplikasi = AplikasiKepegawaian::findOrFail($id);
            $aplikasi->update([
                'nama_aplikasi' => $request->nama_aplikasi,
                'sumber' => $request->sumber,
                'keterangan' => $request->keterangan,
                'link' => $request->link,
                'status' => $request->status
            ]);

            Log::info('Aplikasi Kepegawaian data updated in database', [
                'id' => $aplikasi->id,
                'nama_aplikasi' => $request->nama_aplikasi
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!',
                'data' => $aplikasi
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating aplikasi kepegawaian data in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find and delete from database
            $aplikasi = AplikasiKepegawaian::findOrFail($id);
            $aplikasi->delete();

            Log::info('Aplikasi Kepegawaian data deleted from database', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting aplikasi kepegawaian data from database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }
}

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
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

            // Save to database
            $aplikasi = AplikasiKepegawaian::create([
                'nama_aplikasi' => $request->nama_aplikasi,
                'sumber' => $request->sumber,
                'keterangan' => $request->keterangan,
                'link' => $request->link,
                'status' => $request->status
            ]);

            Log::info('Aplikasi Kepegawaian data saved to database', [
                'id' => $aplikasi->id,
                'nama_aplikasi' => $request->nama_aplikasi
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $aplikasi
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving aplikasi kepegawaian data to database', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

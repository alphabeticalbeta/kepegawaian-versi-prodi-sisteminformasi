<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\VisiMisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisiMisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.layouts.views.admin-universitas.visi-misi', [
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
                'jenis' => 'required|in:visi,misi',
                'konten' => 'required|string|max:5000',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Save to database
            $visiMisi = VisiMisi::create([
                'jenis' => $request->jenis,
                'konten' => $request->konten,
                'status' => $request->status
            ]);

            Log::info('Visi Misi data saved to database', [
                'id' => $visiMisi->id,
                'jenis' => $request->jenis
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $visiMisi
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving visi misi data to database', [
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
                'jenis' => 'required|in:visi,misi',
                'konten' => 'required|string|max:5000',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Find and update in database
            $visiMisi = VisiMisi::findOrFail($id);
            $visiMisi->update([
                'jenis' => $request->jenis,
                'konten' => $request->konten,
                'status' => $request->status
            ]);

            Log::info('Visi Misi data updated in database', [
                'id' => $visiMisi->id,
                'jenis' => $request->jenis
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!',
                'data' => $visiMisi
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating visi misi data in database', [
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
            $visiMisi = VisiMisi::findOrFail($id);
            $visiMisi->delete();

            Log::info('Visi Misi data deleted from database', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting visi misi data from database', [
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

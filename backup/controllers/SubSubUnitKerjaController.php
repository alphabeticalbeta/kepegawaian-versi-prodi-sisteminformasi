<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;
use App\Models\KepegawaianUniversitas\SubUnitKerja;
use App\Models\KepegawaianUniversitas\UnitKerja;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class SubSubUnitKerjaController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subSubUnitKerjas = SubSubUnitKerja::with(['subUnitKerja', 'unitKerja'])
            ->orderBy('nama')
            ->paginate(10);

        return view('backend.layouts.views.kepegawaian-universitas.sub-sub-unitkerja.master-data-sub-sub-unitkerja', compact('subSubUnitKerjas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        $subUnitKerjas = collect(); // Empty collection initially

        return view('backend.layouts.views.kepegawaian-universitas.sub-sub-unitkerja.form-sub-sub-unitkerja', compact('unitKerjas', 'subUnitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sub_unit_kerja_id' => 'required|exists:sub_unit_kerjas,id',
            'nama' => 'required|string|max:255',
        ]);

        SubSubUnitKerja::create($data);

        return redirect()->route('backend.kepegawaian-universitas.sub-sub-unitkerja.index')
                         ->with('success', 'Sub Sub Unit Kerja berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubSubUnitKerja $subSubUnitKerja)
    {
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        $subUnitKerjas = SubUnitKerja::where('unit_kerja_id', $subSubUnitKerja->subUnitKerja->unit_kerja_id)
            ->orderBy('nama')
            ->get();

        return view('backend.layouts.views.kepegawaian-universitas.sub-sub-unitkerja.form-sub-sub-unitkerja', compact('subSubUnitKerja', 'unitKerjas', 'subUnitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubSubUnitKerja $subSubUnitKerja)
    {
        $data = $request->validate([
            'sub_unit_kerja_id' => 'required|exists:sub_unit_kerjas,id',
            'nama' => 'required|string|max:255',
        ]);

        $subSubUnitKerja->update($data);

        return redirect()->route('backend.kepegawaian-universitas.sub-sub-unitkerja.index')
                         ->with('success', 'Sub Sub Unit Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubSubUnitKerja $subSubUnitKerja)
    {
        $subSubUnitKerja->delete();

        return redirect()->route('backend.kepegawaian-universitas.sub-sub-unitkerja.index')
                         ->with('success', 'Sub Sub Unit Kerja berhasil dihapus.');
    }

    /**
     * Get sub unit kerjas based on unit kerja (AJAX endpoint)
     */
    public function getSubUnitKerjas(Request $request)
    {
        $unitKerjaId = $request->get('unit_kerja_id');

        if (!$unitKerjaId) {
            return response()->json([]);
        }

        $subUnitKerjas = SubUnitKerja::where('unit_kerja_id', $unitKerjaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subUnitKerjas);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('backend.layouts.views.kepegawaian-universitas.sub-sub-unitkerja.import-sub-sub-unitkerja');
    }

    /**
     * Handle import
     */
    public function import(Request $request)
    {
        // Implementation for import functionality
        // This would typically handle Excel/CSV imports
        return redirect()->route('backend.kepegawaian-universitas.sub-sub-unitkerja.index')
                         ->with('success', 'Data berhasil diimpor.');
    }

    /**
     * Export template
     */
    public function exportTemplate()
    {
        // Implementation for export template functionality
        // This would typically generate Excel/CSV templates
        return response()->download(storage_path('app/templates/sub-sub-unitkerja-template.xlsx'));
    }
}

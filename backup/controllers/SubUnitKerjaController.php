<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\SubUnitKerja;
use App\Models\KepegawaianUniversitas\UnitKerja;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class SubUnitKerjaController extends Controller
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
        $subUnitKerjas = SubUnitKerja::with('unitKerja')->orderBy('nama')->paginate(10);
        return view('backend.layouts.views.kepegawaian-universitas.sub-unitkerja.master-data-sub-unitkerja', compact('subUnitKerjas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        return view('backend.layouts.views.kepegawaian-universitas.sub-unitkerja.form-sub-unitkerja', compact('unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'nama' => 'required|string|max:255',
        ]);

        SubUnitKerja::create($data);

        return redirect()->route('backend.kepegawaian-universitas.sub-unitkerja.index')
                         ->with('success', 'Sub Unit Kerja berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubUnitKerja $subUnitKerja)
    {
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        return view('backend.layouts.views.kepegawaian-universitas.sub-unitkerja.form-sub-unitkerja', compact('subUnitKerja', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubUnitKerja $subUnitKerja)
    {
        $data = $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'nama' => 'required|string|max:255',
        ]);

        $subUnitKerja->update($data);

        return redirect()->route('backend.kepegawaian-universitas.sub-unitkerja.index')
                         ->with('success', 'Sub Unit Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubUnitKerja $subUnitKerja)
    {
        $subUnitKerja->delete();

        return redirect()->route('backend.kepegawaian-universitas.sub-unitkerja.index')
                         ->with('success', 'Sub Unit Kerja berhasil dihapus.');
    }
}

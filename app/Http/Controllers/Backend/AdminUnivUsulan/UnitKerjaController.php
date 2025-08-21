<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\UnitKerja;
use App\Models\BackendUnivUsulan\SubUnitKerja;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class UnitKerjaController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    public function index()
    {
        // Fetch all hierarchical data
        $unitKerjas = UnitKerja::with(['subUnitKerjas.subSubUnitKerjas'])
            ->orderBy('nama')
            ->get();

        return view('backend.layouts.views.admin-univ-usulan.unitkerja.master-data-unitkerja', compact('unitKerjas'));
    }

    public function create()
    {
        return view('backend.layouts.views.admin-univ-usulan.unitkerja.form-unitkerja');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_kerja_nama' => 'nullable|string|max:255',
            'sub_unit_kerja_nama' => 'nullable|string|max:255',
        ]);

        // Determine type based on filled fields
        $type = 'unit_kerja'; // Default
        if ($request->filled('sub_unit_kerja_nama')) {
            $type = 'sub_sub_unit_kerja';
        } elseif ($request->filled('unit_kerja_nama')) {
            $type = 'sub_unit_kerja';
        }

        switch ($type) {
            case 'unit_kerja':
                UnitKerja::create(['nama' => $request->nama]);
                $message = 'Unit Kerja berhasil ditambahkan.';
                break;

            case 'sub_unit_kerja':
                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                SubUnitKerja::create([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub Unit Kerja berhasil ditambahkan.';
                break;

            case 'sub_sub_unit_kerja':
                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                // Find or create sub unit kerja
                $subUnitKerja = SubUnitKerja::firstOrCreate([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->sub_unit_kerja_nama
                ]);

                SubSubUnitKerja::create([
                    'sub_unit_kerja_id' => $subUnitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub-sub Unit Kerja berhasil ditambahkan.';
                break;
        }

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', $message);
    }

    public function edit($type, $id)
    {
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::with('unitKerja')->findOrFail($id);
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::with(['subUnitKerja', 'unitKerja'])->findOrFail($id);
                break;

            default:
                abort(404);
        }

        return view('backend.layouts.views.admin-univ-usulan.unitkerja.form-unitkerja', compact('item', 'type'));
    }

    public function update(Request $request, $type, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_kerja_nama' => 'nullable|string|max:255',
            'sub_unit_kerja_nama' => 'nullable|string|max:255',
        ]);

        // Use the type parameter from URL since we're editing an existing item
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                $item->update(['nama' => $request->nama]);
                $message = 'Unit Kerja berhasil diperbarui.';
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::findOrFail($id);

                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                $item->update([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub Unit Kerja berhasil diperbarui.';
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::findOrFail($id);

                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                // Find or create sub unit kerja
                $subUnitKerja = SubUnitKerja::firstOrCreate([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->sub_unit_kerja_nama
                ]);

                $item->update([
                    'sub_unit_kerja_id' => $subUnitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub-sub Unit Kerja berhasil diperbarui.';
                break;

            default:
                abort(404);
        }

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', $message);
    }

    public function destroy($type, $id)
    {
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                // Check if has sub units
                if ($item->subUnitKerjas()->count() > 0) {
                    return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                                     ->with('error', 'Unit Kerja tidak dapat dihapus karena masih memiliki Sub Unit Kerja.');
                }
                $item->delete();
                $message = 'Unit Kerja berhasil dihapus.';
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::findOrFail($id);
                // Check if has sub sub units
                if ($item->subSubUnitKerjas()->count() > 0) {
                    return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                                     ->with('error', 'Sub Unit Kerja tidak dapat dihapus karena masih memiliki Sub-sub Unit Kerja.');
                }
                $item->delete();
                $message = 'Sub Unit Kerja berhasil dihapus.';
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::findOrFail($id);
                $item->delete();
                $message = 'Sub-sub Unit Kerja berhasil dihapus.';
                break;

            default:
                abort(404);
        }

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', $message);
    }

    // API method untuk mendapatkan sub unit kerja berdasarkan unit kerja
    public function getSubUnitKerja($unitKerjaId)
    {
        $subUnitKerjas = SubUnitKerja::where('unit_kerja_id', $unitKerjaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subUnitKerjas);
    }

    // API method untuk mendapatkan sub sub unit kerja berdasarkan sub unit kerja
    public function getSubSubUnitKerja($subUnitKerjaId)
    {
        $subSubUnitKerjas = SubSubUnitKerja::where('sub_unit_kerja_id', $subUnitKerjaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subSubUnitKerjas);
    }
}

<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Jabatan;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class JabatanController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        // Build query dengan filter
        $query = Jabatan::query();

        // Filter berdasarkan jenis pegawai
        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        // Filter berdasarkan jenis jabatan
        if ($request->filled('jenis_jabatan')) {
            $query->where('jenis_jabatan', $request->jenis_jabatan);
        }

        // Search berdasarkan nama jabatan
        if ($request->filled('search')) {
            $query->where('jabatan', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan hirarki
        if ($request->filled('has_hierarchy')) {
            if ($request->has_hierarchy === 'yes') {
                $query->whereNotNull('hierarchy_level');
            } elseif ($request->has_hierarchy === 'no') {
                $query->whereNull('hierarchy_level');
            }
        }

        // Filter berdasarkan eligibility untuk usulan
        if ($request->filled('eligible_usulan')) {
            if ($request->eligible_usulan === 'yes') {
                $query->where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural');
            } elseif ($request->eligible_usulan === 'no') {
                $query->where('jenis_jabatan', 'Tenaga Kependidikan Struktural');
            }
        }

        // SORTING HIERARCHY: Terendah ke Tertinggi
        $jabatans = $query->orderBy('jenis_pegawai', 'asc')
                          ->orderBy('jenis_jabatan', 'asc')
                          ->orderByRaw('ISNULL(hierarchy_level), hierarchy_level ASC') // NULL di akhir, angka dari kecil ke besar
                          ->orderBy('jabatan', 'asc')
                          ->paginate(20)
                          ->appends($request->query()); // Maintain filter params di pagination

        // Data untuk dropdown filter
        $filterData = [
            'jenis_pegawai_options' => Jabatan::distinct()->pluck('jenis_pegawai')->sort(),
            'jenis_jabatan_options' => Jabatan::distinct()->pluck('jenis_jabatan')->sort(),
        ];

        // Statistik untuk dashboard
        $stats = [
            'total' => Jabatan::count(),
            'dengan_hirarki' => Jabatan::whereNotNull('hierarchy_level')->count(),
            'tanpa_hirarki' => Jabatan::whereNull('hierarchy_level')->count(),
            'dapat_usulan' => Jabatan::where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural')->count(),
        ];

        return view('backend.layouts.views.kepegawaian-universitas.jabatan.master-data-jabatan', compact('jabatans', 'filterData', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.views.kepegawaian-universitas.jabatan.form-jabatan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan',
            'hierarchy_level' => 'nullable|integer|min:1|max:100',
        ], [
            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'jenis_pegawai.in' => 'Jenis pegawai harus Dosen atau Tenaga Kependidikan.',
            'jenis_jabatan.required' => 'Jenis jabatan wajib diisi.',
            'jabatan.required' => 'Nama jabatan wajib diisi.',
            'jabatan.unique' => 'Nama jabatan ini sudah ada.',
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 100.',
        ]);

        try {
            Jabatan::create([
                'jenis_pegawai' => $request->jenis_pegawai,
                'jenis_jabatan' => $request->jenis_jabatan,
                'jabatan' => $request->jabatan,
                'hierarchy_level' => $request->hierarchy_level,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data jabatan berhasil ditambahkan.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.jabatan.index')
                            ->with('success', 'Data jabatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jabatan $jabatan)
    {
        return view('backend.layouts.views.kepegawaian-universitas.jabatan.show', compact('jabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        return view('backend.layouts.views.kepegawaian-universitas.jabatan.form-jabatan', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan,' . $jabatan->id,
            'hierarchy_level' => 'nullable|integer|min:1|max:100',
        ], [
            'jenis_pegawai.required' => 'Jenis pegawai wajib dipilih.',
            'jenis_pegawai.in' => 'Jenis pegawai harus Dosen atau Tenaga Kependidikan.',
            'jenis_jabatan.required' => 'Jenis jabatan wajib diisi.',
            'jabatan.required' => 'Nama jabatan wajib diisi.',
            'jabatan.unique' => 'Nama jabatan ini sudah ada.',
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 100.',
        ]);

        try {
            $jabatan->update([
                'jenis_pegawai' => $request->jenis_pegawai,
                'jenis_jabatan' => $request->jenis_jabatan,
                'jabatan' => $request->jabatan,
                'hierarchy_level' => $request->hierarchy_level,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data jabatan berhasil diperbarui.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.jabatan.index')
                            ->with('success', 'Data jabatan berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        try {
            // Check if jabatan is being used
            $usedInUsulan = $jabatan->usulanJabatanLama()->exists() || $jabatan->usulanJabatanTujuan()->exists();
            $usedByPegawai = $jabatan->pegawais()->exists();

            if ($usedInUsulan || $usedByPegawai) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jabatan tidak dapat dihapus karena sedang digunakan oleh pegawai atau usulan.'
                    ], 400);
                }

                return redirect()->route('backend.kepegawaian-universitas.jabatan.index')
                                ->with('error', 'Jabatan tidak dapat dihapus karena sedang digunakan oleh pegawai atau usulan.');
            }

            $jabatan->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data jabatan berhasil dihapus.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.jabatan.index')
                            ->with('success', 'Data jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('backend.kepegawaian-universitas.jabatan.index')
                            ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export data jabatan (bonus feature)
     */
    public function export(Request $request)
    {
        // Apply same filters as index
        $query = Jabatan::query();

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('jenis_jabatan')) {
            $query->where('jenis_jabatan', $request->jenis_jabatan);
        }

        if ($request->filled('search')) {
            $query->where('jabatan', 'like', '%' . $request->search . '%');
        }

        $jabatans = $query->orderBy('jenis_pegawai', 'asc')
                          ->orderBy('jenis_jabatan', 'asc')
                          ->orderByRaw('ISNULL(hierarchy_level), hierarchy_level ASC')
                          ->orderBy('jabatan', 'asc')
                          ->get();

        // Simple CSV export
        $filename = 'jabatan_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($jabatans) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['No', 'Jenis Pegawai', 'Jenis Jabatan', 'Nama Jabatan', 'Level Hirarki', 'Dapat Usulan']);

            // Data
            foreach ($jabatans as $index => $jabatan) {
                fputcsv($file, [
                    $index + 1,
                    $jabatan->jenis_pegawai,
                    $jabatan->jenis_jabatan,
                    $jabatan->jabatan,
                    $jabatan->hierarchy_level ?? '-',
                    $jabatan->isEligibleForUsulan() ? 'Ya' : 'Tidak'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

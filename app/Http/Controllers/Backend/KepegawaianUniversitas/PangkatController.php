<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pangkat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class PangkatController extends Controller
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
        // Mengurutkan pangkat berdasarkan hierarchy_level menggunakan scope
        $pangkats = Pangkat::orderByHierarchy('asc')->paginate(10);

        return view('backend.layouts.views.kepegawaian-universitas.pangkat.master-data-pangkat', compact('pangkats'));
    }

    public function create()
    {
        return view('backend.layouts.views.kepegawaian-universitas.pangkat.form-pangkat');
    }

    public function store(Request $request)
    {
        $request->validate([
            // UBAH ATURAN INI
            'pangkat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pangkats')->where(function ($query) use ($request) {
                    return $query->where('status_pangkat', $request->status_pangkat);
                }),
            ],
            'status_pangkat' => 'required|in:PNS,PPPK,Non-ASN',
            // UBAH ATURAN INI
            'hierarchy_level' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
                Rule::unique('pangkats')->where(function ($query) use ($request) {
                    return $query->where('status_pangkat', $request->status_pangkat);
                }),
            ],
        ], [
            'pangkat.required' => 'Nama pangkat wajib diisi.',
            'pangkat.unique' => 'Nama pangkat ini sudah ada untuk status kepegawaian yang dipilih.',
            'status_pangkat.required' => 'Status pangkat wajib dipilih.',
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 20.',
            'hierarchy_level.unique' => 'Level hirarki ini sudah digunakan untuk status kepegawaian yang dipilih.',
        ]);

        try {
            Pangkat::create($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Pangkat berhasil ditambahkan.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.pangkat.index')
                                ->with('success', 'Data Pangkat berhasil ditambahkan.');
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

    public function edit(Pangkat $pangkat)
    {
        return view('backend.layouts.views.kepegawaian-universitas.pangkat.form-pangkat', compact('pangkat'));
    }

    public function update(Request $request, Pangkat $pangkat)
    {
        $request->validate([
            // UBAH ATURAN INI
            'pangkat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pangkats')->where(function ($query) use ($request) {
                    return $query->where('status_pangkat', $request->status_pangkat);
                })->ignore($pangkat->id),
            ],
            'status_pangkat' => 'required|in:PNS,PPPK,Non-ASN',
            // UBAH ATURAN INI
            'hierarchy_level' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
                Rule::unique('pangkats')->where(function ($query) use ($request) {
                    return $query->where('status_pangkat', $request->status_pangkat);
                })->ignore($pangkat->id),
            ],
        ], [
            'pangkat.required' => 'Nama pangkat wajib diisi.',
            'pangkat.unique' => 'Nama pangkat ini sudah ada untuk status kepegawaian yang dipilih.',
            'status_pangkat.required' => 'Status pangkat wajib dipilih.',
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 20.',
            'hierarchy_level.unique' => 'Level hirarki ini sudah digunakan untuk status kepegawaian yang dipilih.',
        ]);

        try {
            $pangkat->update($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Pangkat berhasil diperbarui.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.pangkat.index')
                                ->with('success', 'Data Pangkat berhasil diperbarui.');
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

    public function destroy(Pangkat $pangkat)
    {
        try {
            // Cek apakah pangkat sedang digunakan oleh pegawai
            if ($pangkat->pegawais()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pangkat tidak dapat dihapus karena masih digunakan oleh pegawai.'
                    ], 400);
                }

                return redirect()->route('backend.kepegawaian-universitas.pangkat.index')
                                 ->with('error', 'Pangkat tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            $pangkat->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Pangkat berhasil dihapus.'
                ]);
            }

            return redirect()->route('backend.kepegawaian-universitas.pangkat.index')
                             ->with('success', 'Data Pangkat berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('backend.kepegawaian-universitas.pangkat.index')
                            ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint untuk mendapatkan pangkat berdasarkan hirarki
     */
    public function getPangkatByHierarchy(Request $request)
    {
        $type = $request->get('type', 'all'); // all, pns, non-pns

        $query = Pangkat::query();

        switch ($type) {
            case 'pns':
                $query->withHierarchy();
                break;
            case 'non-pns':
                $query->withoutHierarchy();
                break;
            default:
                // all - tidak perlu filter
                break;
        }

        $pangkats = $query->orderByHierarchy('asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $pangkats->map(function ($pangkat) {
                return [
                    'id' => $pangkat->id,
                    'pangkat' => $pangkat->pangkat,
                    'hierarchy_level' => $pangkat->hierarchy_level,
                    'hierarchy_info' => $pangkat->hierarchy_info,
                    'formatted_display' => $pangkat->formatted_display,
                    'golongan' => $pangkat->golongan,
                    'has_hierarchy' => $pangkat->hasHierarchy()
                ];
            })
        ]);
    }

    /**
     * API endpoint untuk mendapatkan pangkat yang bisa dipromosikan
     */
    public function getPromotionTargets(Request $request, Pangkat $pangkat)
    {
        $validTargets = $pangkat->getValidPromotionTargets();

        return response()->json([
            'status' => 'success',
            'current_pangkat' => [
                'id' => $pangkat->id,
                'pangkat' => $pangkat->pangkat,
                'hierarchy_level' => $pangkat->hierarchy_level,
                'formatted_display' => $pangkat->formatted_display
            ],
            'promotion_targets' => $validTargets->map(function ($target) {
                return [
                    'id' => $target->id,
                    'pangkat' => $target->pangkat,
                    'hierarchy_level' => $target->hierarchy_level,
                    'formatted_display' => $target->formatted_display,
                    'golongan' => $target->golongan
                ];
            })
        ]);
    }

    /**
     * API endpoint untuk mendapatkan hirarki lengkap
     */
    public function getHierarchyStructure()
    {
        $pangkats = Pangkat::getAllHierarchy();

        // Group by golongan untuk visualisasi
        $hierarchyStructure = [
            'golongan_i' => $pangkats->whereBetween('hierarchy_level', [1, 4])->values(),
            'golongan_ii' => $pangkats->whereBetween('hierarchy_level', [5, 8])->values(),
            'golongan_iii' => $pangkats->whereBetween('hierarchy_level', [9, 12])->values(),
            'golongan_iv' => $pangkats->whereBetween('hierarchy_level', [13, 17])->values(),
            'non_pns' => $pangkats->whereNull('hierarchy_level')->values()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $hierarchyStructure,
            'statistics' => [
                'total_pangkat' => $pangkats->count(),
                'total_pns' => $pangkats->whereNotNull('hierarchy_level')->count(),
                'total_non_pns' => $pangkats->whereNull('hierarchy_level')->count(),
                'by_golongan' => [
                    'i' => $hierarchyStructure['golongan_i']->count(),
                    'ii' => $hierarchyStructure['golongan_ii']->count(),
                    'iii' => $hierarchyStructure['golongan_iii']->count(),
                    'iv' => $hierarchyStructure['golongan_iv']->count(),
                ]
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pangkat;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;
use App\Models\BackendUnivUsulan\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File as FileFacade;
use App\Services\FileStorageService;

class DataPegawaiController extends Controller
{
    private $fileStorage;

    public function __construct(FileStorageService $fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // OPTIMASI: Gunakan query builder dengan eager loading yang optimal
        $query = Pegawai::withOptimalRelations()
            ->when($request->filter_jenis_pegawai, function ($q, $jenis_pegawai) {
                return $q->byJenisPegawai($jenis_pegawai);
            })
            ->when($request->search, function ($q, $search) {
                return $q->searchByNameOrNip($search);
            })
            ->latest();

        $pegawais = $query->paginate(10)->withQueryString();

        return view('backend.layouts.views.admin-univ-usulan.data-pegawai.master-datapegawai', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // OPTIMASI: Cache data yang sering digunakan dengan cache key yang lebih spesifik
        $pangkats = \Cache::remember('pangkats_all_hierarchy', 3600, function () {
            return Pangkat::orderByHierarchy('asc')->get(['id', 'pangkat', 'hierarchy_level', 'status_pangkat']);
        });

        $jabatans = \Cache::remember('jabatans_all_hierarchy', 3600, function () {
            return Jabatan::orderByHierarchy('asc')->get(['id', 'jabatan', 'jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
        });

        // OPTIMASI: Ambil data unit kerja dengan struktur yang benar
        $unitKerjas = \Cache::remember('unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\UnitKerja::orderBy('nama')->get(['id', 'nama']);
        });

        $subUnitKerjas = \Cache::remember('sub_unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\SubUnitKerja::with('unitKerja:id,nama')
                ->orderBy('nama')
                ->get(['id', 'nama', 'unit_kerja_id']);
        });

        $subSubUnitKerjas = \Cache::remember('sub_sub_unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja:id,nama,unit_kerja_id', 'subUnitKerja.unitKerja:id,nama'])
                ->orderBy('nama')
                ->get(['id', 'nama', 'sub_unit_kerja_id']);
        });

        // Siapkan data untuk dropdown berjenjang dengan struktur yang benar
        $unitKerjaOptions = [];
        $subUnitKerjaOptions = [];
        $subSubUnitKerjaOptions = [];

        // Unit Kerja Options
        foreach ($unitKerjas as $unitKerja) {
            $unitKerjaOptions[$unitKerja->id] = $unitKerja->nama;
        }

        // Sub Unit Kerja Options (grouped by unit_kerja_id)
        foreach ($subUnitKerjas as $subUnitKerja) {
            if ($subUnitKerja->unitKerja) {
                $unitKerjaId = $subUnitKerja->unit_kerja_id;
                $subUnitKerjaOptions[$unitKerjaId][$subUnitKerja->id] = $subUnitKerja->nama;
            }
        }

        // Sub-sub Unit Kerja Options (grouped by sub_unit_kerja_id)
        foreach ($subSubUnitKerjas as $subSubUnitKerja) {
            if ($subSubUnitKerja->subUnitKerja) {
                $subUnitKerjaId = $subSubUnitKerja->sub_unit_kerja_id;
                $subSubUnitKerjaOptions[$subUnitKerjaId][$subSubUnitKerja->id] = $subSubUnitKerja->nama;
            }
        }

        // Buat dummy pegawai untuk form create
        $pegawai = new \App\Models\BackendUnivUsulan\Pegawai();

        // Dummy data untuk dropdown yang dipilih (kosong untuk form create)
        $selectedUnitKerjaId = null;
        $selectedSubUnitKerjaId = null;
        $selectedSubSubUnitKerjaId = null;

        return view('backend.layouts.views.admin-univ-usulan.data-pegawai.form-datapegawai',
            compact('pangkats', 'jabatans', 'unitKerjas', 'subUnitKerjas', 'subSubUnitKerjas', 'unitKerjaOptions', 'subUnitKerjaOptions', 'subSubUnitKerjaOptions', 'pegawai', 'selectedUnitKerjaId', 'selectedSubUnitKerjaId', 'selectedSubSubUnitKerjaId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $this->handleFileUploads($request, $validated);

        // Handle unit_kerja_id berdasarkan unit_kerja_terakhir_id
        if ($request->filled('unit_kerja_terakhir_id')) {
            $subSubUnitKerja = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja', 'subUnitKerja.unitKerja'])
                ->find($request->unit_kerja_terakhir_id);

            if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                // Set unit_kerja_id berdasarkan parent dari Sub-sub Unit Kerja
                $validated['unit_kerja_id'] = $subSubUnitKerja->subUnitKerja->unitKerja->id;
            }
        }

        Pegawai::create($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $pangkats = \Cache::remember('pangkats_all_hierarchy', 3600, function () {
            return Pangkat::orderByHierarchy('asc')->get(['id', 'pangkat', 'hierarchy_level', 'status_pangkat']);
        });
        $jabatans = \Cache::remember('jabatans_all_hierarchy', 3600, function () {
            return Jabatan::orderByHierarchy('asc')->get(['id', 'jabatan', 'jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
        });

        // OPTIMASI: Ambil data unit kerja dengan struktur yang benar
        $unitKerjas = \Cache::remember('unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\UnitKerja::orderBy('nama')->get(['id', 'nama']);
        });

        $subUnitKerjas = \Cache::remember('sub_unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\SubUnitKerja::with('unitKerja:id,nama')
                ->orderBy('nama')
                ->get(['id', 'nama', 'unit_kerja_id']);
        });

        $subSubUnitKerjas = \Cache::remember('sub_sub_unit_kerjas_all', 3600, function () {
            return \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja:id,nama,unit_kerja_id', 'subUnitKerja.unitKerja:id,nama'])
                ->orderBy('nama')
                ->get(['id', 'nama', 'sub_unit_kerja_id']);
        });

        // Siapkan data untuk dropdown berjenjang dengan struktur yang benar
        $unitKerjaOptions = [];
        $subUnitKerjaOptions = [];
        $subSubUnitKerjaOptions = [];

        // Unit Kerja Options
        foreach ($unitKerjas as $unitKerja) {
            $unitKerjaOptions[$unitKerja->id] = $unitKerja->nama;
        }

        // Sub Unit Kerja Options (grouped by unit_kerja_id)
        foreach ($subUnitKerjas as $subUnitKerja) {
            if ($subUnitKerja->unitKerja) {
                $unitKerjaId = $subUnitKerja->unit_kerja_id;
                $subUnitKerjaOptions[$unitKerjaId][$subUnitKerja->id] = $subUnitKerja->nama;
            }
        }

        // Sub-sub Unit Kerja Options (grouped by sub_unit_kerja_id)
        foreach ($subSubUnitKerjas as $subSubUnitKerja) {
            if ($subSubUnitKerja->subUnitKerja) {
                $subUnitKerjaId = $subSubUnitKerja->sub_unit_kerja_id;
                $subSubUnitKerjaOptions[$subUnitKerjaId][$subSubUnitKerja->id] = $subSubUnitKerja->nama;
            }
        }

        // Jika edit mode, siapkan data untuk mengisi dropdown yang sudah dipilih
        $selectedUnitKerjaId = null;
        $selectedSubUnitKerjaId = null;
        $selectedSubSubUnitKerjaId = null;

        if ($pegawai->unit_kerja_terakhir_id) {
            // Cari data berdasarkan unit_kerja_terakhir_id dengan query terpisah
            $selectedSubSubUnit = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja:id,nama,unit_kerja_id', 'subUnitKerja.unitKerja:id,nama'])
                ->find($pegawai->unit_kerja_terakhir_id);

            if ($selectedSubSubUnit && $selectedSubSubUnit->subUnitKerja && $selectedSubSubUnit->subUnitKerja->unitKerja) {
                $selectedSubSubUnitKerjaId = $selectedSubSubUnit->id;
                $selectedSubUnitKerjaId = $selectedSubSubUnit->subUnitKerja->id;
                $selectedUnitKerjaId = $selectedSubSubUnit->subUnitKerja->unitKerja->id;
            }
        }

        return view('backend.layouts.views.admin-univ-usulan.data-pegawai.form-datapegawai', compact(
            'pegawai',
            'pangkats',
            'jabatans',
            'unitKerjas',
            'subUnitKerjas',
            'subSubUnitKerjas',
            'unitKerjaOptions',
            'subUnitKerjaOptions',
            'subSubUnitKerjaOptions',
            'selectedUnitKerjaId',
            'selectedSubUnitKerjaId',
            'selectedSubSubUnitKerjaId'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        // Handle AJAX auto-save requests
        if ($request->ajax() && $request->isMethod('POST')) {
            return $this->handleAutoSave($request, $pegawai);
        }

        $validated = $this->validateRequest($request, $pegawai->id);
        $this->handleFileUploads($request, $validated, $pegawai);

        // Handle password update
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }

        // Handle unit_kerja_id berdasarkan unit_kerja_terakhir_id
        if ($request->filled('unit_kerja_terakhir_id')) {
            $subSubUnitKerja = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja', 'subUnitKerja.unitKerja'])
                ->find($request->unit_kerja_terakhir_id);

            if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                // Set unit_kerja_id berdasarkan parent dari Sub-sub Unit Kerja
                $validated['unit_kerja_id'] = $subSubUnitKerja->subUnitKerja->unitKerja->id;
            }
        }

        $pegawai->update($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    /**
     * Handle auto-save functionality for AJAX requests
     */
    private function handleAutoSave(Request $request, Pegawai $pegawai)
    {
        try {
            // Only allow updating specific dosen fields for auto-save
            $allowedFields = [
                'mata_kuliah_diampu',
                'ranting_ilmu_kepakaran',
                'url_profil_sinta'
            ];

            $updateData = [];
            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            // Validate URL if present
            if (isset($updateData['url_profil_sinta']) && !empty($updateData['url_profil_sinta'])) {
                $urlPattern = '/^https?:\/\/sinta\.kemdikbud\.go\.id\/.*$/';
                if (!preg_match($urlPattern, $updateData['url_profil_sinta'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'URL Sinta tidak valid'
                    ], 422);
                }
            }

            // Update only the allowed fields
            if (!empty($updateData)) {
                $pegawai->update($updateData);

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan otomatis',
                    'updated_at' => $pegawai->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang perlu diperbarui'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Auto-save error: ' . $e->getMessage(), [
                'pegawai_id' => $pegawai->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Pegawai $pegawai)
    {

        // Existing delete logic
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir',
            'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        foreach ($fileColumns as $column) {
            if ($pegawai->$column) {
                $disk = $this->getFileDisk($column);
                Storage::disk($disk)->delete($pegawai->$column);
            }
        }

        $pegawai->delete();

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil dihapus.');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['pangkat', 'jabatan', 'unitKerja']);

        return view('backend.layouts.views.admin-univ-usulan.data-pegawai.show-datapegawai', compact('pegawai'));
    }

    /**
     * Reusable validation logic.
     */
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|in:Dosen Fungsional,Dosen dengan Tugas Tambahan,Tenaga Kependidikan Fungsional Umum,Tenaga Kependidikan Fungsional Tertentu,Tenaga Kependidikan Struktural,Tenaga Kependidikan Tugas Tambahan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawaiId,
            'gelar_belakang' => 'required|string|max:255',
            'nomor_kartu_pegawai' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'pendidikan_terakhir' => 'required|string',
            'nama_universitas_sekolah' => 'nullable|string|max:255',
            'nama_prodi_jurusan_s2' => 'nullable|string|max:255',
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'nomor_handphone' => 'required|string',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',
            'nuptk' => 'nullable|numeric|digits:16',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'nilai_konversi' => 'nullable|numeric',
            'status_kepegawaian' => ['required','string',Rule::in([
                'Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN',
                'Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN'
                ])
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        $fileRules = [
            'sk_pangkat_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'sk_jabatan_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'ijazah_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'transkrip_nilai_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_pertama' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_kedua' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'pak_konversi' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sk_penyetaraan_ijazah' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'disertasi_thesis_terakhir' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
            'sk_cpns' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'sk_pns' => ['required', File::types(['pdf'])->max(2 * 1024)],
        ];

        if ($pegawaiId) {
            foreach ($fileRules as $key => $value) {
                $rules[$key] = ['nullable', ...array_slice($value, 1)];
            }
        } else {
             $rules = array_merge($rules, $fileRules);
        }

        return $request->validate($rules);
    }

    /**
     * Reusable file upload logic - REFACTORED with FileStorageService.
     */
    private function handleFileUploads(Request $request, &$validatedData, $pegawai = null)
    {
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                // Delete old file if exists using FileStorageService
                if ($pegawai && $pegawai->$column) {
                    $this->fileStorage->deleteFile($pegawai->$column);
                }

                // Store new file using FileStorageService
                $uploadPath = 'pegawai-files/' . $column;
                $file = $request->file($column);
                $path = $this->fileStorage->uploadFile($file, $uploadPath);
                $validatedData[$column] = $path;

                \Log::info("File uploaded using FileStorageService", [
                    'column' => $column,
                    'file_path' => $path,
                    'pegawai_id' => $pegawai ? $pegawai->id : 'new'
                ]);
            }
        }
    }

    /**
     * Display a document with access control and logging.
     */
        public function showDocument(Pegawai $pegawai, $field)
    {
        try {
            // Debug info
            \Log::info('showDocument called', [
                'pegawai_id' => $pegawai->id,
                'field' => $field,
                'url' => request()->url()
            ]);
        } catch (\Exception $e) {
            // Handle database connection error
            \Log::error('Database connection error in showDocument', [
                'error' => $e->getMessage(),
                'field' => $field,
                'url' => request()->url()
            ]);

            return response()->json([
                'error' => 'Database connection error',
                'message' => 'Tidak dapat terhubung ke database. Pastikan MySQL server berjalan.',
                'details' => $e->getMessage(),
                'field' => $field,
                'suggestion' => 'Start MySQL server atau gunakan SQLite'
            ], 503);
        }

        // 1. Validasi field yang diizinkan
        $allowedFields = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        if (!in_array($field, $allowedFields)) {
            \Log::warning('Invalid field requested', ['field' => $field]);
            return response()->json([
                'error' => 'Jenis dokumen tidak valid',
                'field' => $field,
                'allowed_fields' => $allowedFields
            ], 404);
        }

        // 2. Cek apakah file ada
        $filePath = $pegawai->$field;

        if (!$filePath) {
            \Log::warning('File path is empty', ['field' => $field, 'pegawai_id' => $pegawai->id]);
            return response()->json([
                'error' => 'File tidak ditemukan',
                'message' => 'Pegawai tidak memiliki file ' . $field,
                'pegawai_id' => $pegawai->id,
                'field' => $field
            ], 404);
        }

        // Determine correct disk based on field type
        $disk = $this->getFileDisk($field);

        if (!Storage::disk($disk)->exists($filePath)) {
            \Log::warning('File not found in storage', [
                'field' => $field,
                'filePath' => $filePath,
                'disk' => $disk
            ]);
            return response()->json([
                'error' => 'File tidak ditemukan di storage',
                'message' => 'File ada di database tapi tidak ditemukan di storage',
                'file_path' => $filePath,
                'disk' => $disk,
                'pegawai_id' => $pegawai->id,
                'field' => $field
            ], 404);
        }

        // 3. **ACCESS CONTROL** - Cek permission berdasarkan role
        // Coba ambil user dari berbagai guard yang mungkin
        $currentUser = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();

        if (!$currentUser) {
            abort(403, 'Anda harus login untuk mengakses dokumen ini.');
        }

        if (!$this->canAccessDocument($currentUser, $pegawai)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman atau dokumen ini.');
        }

        // 4. **LOGGING** - Catat akses dokumen
        $this->logDocumentAccess($pegawai->id, $currentUser->id ?? 0, $field, request());

        // 5. **FIX STORAGE BUG** - Gunakan disk yang sesuai
        $fullPath = Storage::disk($disk)->path($filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = FileFacade::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }

    /**
     * Enhanced access control dengan security terbaik
     */
    private function canAccessDocument($currentUser, $targetPegawai): bool
    {
        // 1. SUPER ADMIN: Admin Universitas Usulan - full access
        if ($currentUser->hasRole('Admin Universitas Usulan') ||
            $currentUser->hasPermissionTo('view_all_pegawai_documents')) {
            return true;
        }

        // 2. ADMIN FAKULTAS: Hanya bisa akses dokumen pegawai di fakultasnya
        if ($currentUser->hasRole('Admin Fakultas')) {
            // Double check: pastikan ada unit_kerja_id
            if (!$currentUser->unit_kerja_id) {
                \Log::warning('Admin Fakultas tanpa unit_kerja_id mencoba akses dokumen', [
                    'admin_id' => $currentUser->id,
                    'target_pegawai_id' => $targetPegawai->id
                ]);
                return false;
            }

            // Cek apakah pegawai target berada di fakultas yang sama
            $targetFakultasId = $targetPegawai->unitKerja?->subUnitKerja?->unit_kerja_id;

            if ($currentUser->unit_kerja_id === $targetFakultasId) {
                \Log::info('Admin Fakultas akses dokumen pegawai di fakultasnya', [
                    'admin_id' => $currentUser->id,
                    'admin_fakultas_id' => $currentUser->unit_kerja_id,
                    'target_pegawai_id' => $targetPegawai->id,
                    'target_fakultas_id' => $targetFakultasId
                ]);
                return true;
            }

            \Log::warning('Admin Fakultas mencoba akses dokumen pegawai dari fakultas lain', [
                'admin_id' => $currentUser->id,
                'admin_fakultas_id' => $currentUser->unit_kerja_id,
                'target_pegawai_id' => $targetPegawai->id,
                'target_fakultas_id' => $targetFakultasId
            ]);
            return false;
        }

        // 3. PEGAWAI: Hanya dokumen sendiri
        if ($currentUser->hasPermissionTo('view_own_documents') ||
            $currentUser->hasRole('Pegawai')) {
            return $currentUser->id === $targetPegawai->id;
        }

        // 4. PENILAI: Akses terbatas (implementasi future)
        if ($currentUser->hasRole('Penilai Universitas')) {
            // TODO: Implementasi logic penilai berdasarkan usulan yang sedang dinilai
            return false;
        }

        // 5. DEFAULT DENY: Tidak ada akses
        \Log::warning('Unauthorized document access attempt', [
            'user_id' => $currentUser->id,
            'user_roles' => $currentUser->getRoleNames()->toArray(),
            'target_pegawai_id' => $targetPegawai->id,
            'field' => request()->route('field')
        ]);

        return false;
    }

    /**
     * Enhanced fakultas checking dengan error handling
     */
    private function isInSameFakultas($user1, $user2): bool
    {
        try {
            // Load relasi dengan error handling
            if (!$user1->relationLoaded('unitKerja')) {
                $user1->load('unitKerja');
            }
            if (!$user2->relationLoaded('unitKerja')) {
                $user2->load('unitKerja');
            }

            // Method 1: Gunakan unit_kerja_id langsung (lebih efisien)
            if ($user1->unit_kerja_id && $user2->unit_kerja_id) {
                return $user1->unit_kerja_id === $user2->unit_kerja_id;
            }

            // Method 2: Fallback dengan relasi langsung
            $fakultas1 = $user1->unitKerja?->id;
            $fakultas2 = $user2->unitKerja?->id;

            return $fakultas1 && $fakultas2 && $fakultas1 === $fakultas2;

        } catch (\Exception $e) {
            \Log::error('Error checking fakultas relationship', [
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Simplified logging tanpa advanced fields
     */
    private function logDocumentAccess($pegawaiId, $accessorId, $documentField, $request): void
    {
        try {
            DocumentAccessLog::create([
                'pegawai_id' => $pegawaiId,
                'accessor_id' => $accessorId,
                'document_field' => $documentField,
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->header('User-Agent', ''), 0, 500),
                'accessed_at' => now(),
            ]);

            // Log role info separately untuk debugging
            $accessor = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();
            if ($accessor) {
                \Log::info('Document accessed', [
                    'pegawai_id' => $pegawaiId,
                    'accessor_id' => $accessorId,
                    'document_field' => $documentField,
                    'accessor_has_roles' => $accessor->roles ? $accessor->roles->count() : 0,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to log document access', [
                'pegawai_id' => $pegawaiId,
                'accessor_id' => $accessorId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the appropriate disk for a given field
     */
    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }
}

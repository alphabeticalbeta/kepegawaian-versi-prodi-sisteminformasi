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

class DataPegawaiController extends Controller
{
    public function __construct()
    {

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

        return view('backend.layouts.admin-univ-usulan.data-pegawai.master-datapegawai', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // OPTIMASI: Cache data yang sering digunakan
        $pangkats = \Cache::remember('pangkats_all', 3600, function () {
            return Pangkat::orderBy('pangkat')->get(['id', 'pangkat']);
        });
        
        $jabatans = \Cache::remember('jabatans_all', 3600, function () {
            return Jabatan::orderBy('jabatan')->get(['id', 'jabatan', 'jenis_pegawai']);
        });
        
        $unitKerjas = \Cache::remember('unit_kerjas_with_relations', 3600, function () {
            return SubSubUnitKerja::with(['subUnitKerja:id,nama,unit_kerja_id', 'subUnitKerja.unitKerja:id,nama'])
                ->orderBy('nama')
                ->get(['id', 'nama', 'sub_unit_kerja_id']);
        });

        return view('backend.layouts.admin-univ-usulan.data-pegawai.form-datapegawai', 
            compact('pangkats', 'jabatans', 'unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $this->handleFileUploads($request, $validated);

        Pegawai::create($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get();

        return view('backend.layouts.admin-univ-usulan.data-pegawai.form-datapegawai', compact('pegawai', 'pangkats', 'jabatans', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $this->validateRequest($request, $pegawai->id);
        $this->handleFileUploads($request, $validated, $pegawai);

        $pegawai->update($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil diperbarui.');
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
                Storage::disk('local')->delete($pegawai->$column); // FIX: Gunakan disk 'local'
            }
        }

        $pegawai->delete();

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil dihapus.');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja']);

        return view('backend.layouts.admin-univ-usulan.data-pegawai.show-datapegawai', compact('pegawai'));
    }

    /**
     * Reusable validation logic.
     */
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
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
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
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
     * Reusable file upload logic.
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
                if ($pegawai && $pegawai->$column) {
                    Storage::disk('local')->delete($pegawai->$column);
                }
                $path = $request->file($column)->store('pegawai-files/' . $column, 'local');
                $validatedData[$column] = $path;
            }
        }
    }

    /**
     * Display a document with access control and logging.
     */
    public function showDocument(Pegawai $pegawai, $field)
    {
        // 1. Validasi field yang diizinkan
        $allowedFields = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // 2. Cek apakah file ada
        $filePath = $pegawai->$field;
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // 3. **ACCESS CONTROL** - Cek permission berdasarkan role
        $currentUser = Auth::guard('pegawai')->user();

        if (!$this->canAccessDocument($currentUser, $pegawai)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman atau dokumen ini.');
        }

        // 4. **LOGGING** - Catat akses dokumen
        $this->logDocumentAccess($pegawai->id, $currentUser->id, $field, request());

        // 5. **FIX STORAGE BUG** - Gunakan disk 'local' yang konsisten
        $fullPath = Storage::disk('local')->path($filePath);

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
                $user1->load('unitKerja.subUnitKerja.unitKerja');
            }
            if (!$user2->relationLoaded('unitKerja')) {
                $user2->load('unitKerja.subUnitKerja.unitKerja');
            }

            // Method 1: Gunakan unit_kerja_id langsung (lebih efisien)
            if ($user1->unit_kerja_id && $user2->unitKerja?->subUnitKerja?->unit_kerja_id) {
                return $user1->unit_kerja_id === $user2->unitKerja->subUnitKerja->unit_kerja_id;
            }

            // Method 2: Fallback dengan relasi lengkap
            $fakultas1 = $user1->unitKerja?->subUnitKerja?->unitKerja?->id;
            $fakultas2 = $user2->unitKerja?->subUnitKerja?->unitKerja?->id;

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
            $accessor = Auth::guard('pegawai')->user();
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
}

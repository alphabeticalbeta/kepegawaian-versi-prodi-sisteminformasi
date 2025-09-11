<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\File;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Models\KepegawaianUniversitas\Jabatan;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;

class ProfileController extends Controller
{
    /**
     * Display the user's profile or create form.
     */
    public function show(Request $request, $pegawaiId = null)
    {
        // 1. Deteksi mode dan route
        $isKepegawaianUniversitasRoute = $request->route()->getName() == 'backend.kepegawaian-universitas.data-pegawai.edit';
        $isCreateRoute = $request->route()->getName() == 'backend.kepegawaian-universitas.data-pegawai.create';

        // 2. Handle create mode
        if ($isCreateRoute) {
            // Mode create - buat pegawai baru
            $pegawai = new Pegawai();
            $isAdmin = true;
            $isCreating = true;
            $isEditing = true; // Form create selalu dalam mode editing
        } else if ($pegawaiId || $isKepegawaianUniversitasRoute) {
            // Mode admin - edit pegawai lain (via kepegawaian-universitas route)
            $pegawai = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja'])
                            ->findOrFail($pegawaiId);
            $isAdmin = true;
            $isCreating = false;
        } else {
            // Mode pegawai - edit diri sendiri (via pegawai-unmul route)
            $pegawai = Auth::guard('pegawai')->user();
            $pegawai = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja'])
                            ->find($pegawai->id);
            $isAdmin = false;
            $isCreating = false;
        }

        // 3. Ambil daftar field dokumen (hanya jika bukan create mode)
        $documentFields = $isCreating ? [] : $this->getDocumentFields($pegawai);

        // 4. Tentukan apakah sedang dalam mode edit atau tidak
        if ($isCreating) {
            $isEditing = true; // Create mode selalu editing
        } else {
            $isEditing = ($request->has('edit') && $request->get('edit') == '1') ||
                         ($request->route('edit') == '1') ||
                         ($pegawaiId && $request->route()->getName() == 'backend.kepegawaian-universitas.data-pegawai.edit');
        }

        // 5. Ambil data untuk dropdown HANYA JIKA sedang mode edit/create
        $pangkats = $isEditing ? \App\Models\KepegawaianUniversitas\Pangkat::orderByHierarchy('asc')->get() : [];

        // Untuk jabatan, preload semua jabatan dengan struktur yang tepat untuk JavaScript filtering
        if ($isEditing) {
            // Ambil semua jabatan dan group berdasarkan jenis pegawai dan jenis jabatan
            $allJabatans = \App\Models\KepegawaianUniversitas\Jabatan::orderBy('jenis_pegawai')
                ->orderBy('jenis_jabatan')
                ->orderByRaw('ISNULL(hierarchy_level), hierarchy_level ASC')
                ->orderBy('jabatan')
                ->get();

            // Group jabatan untuk JavaScript filtering
            $jabatansGrouped = $allJabatans->groupBy('jenis_pegawai')->map(function ($jabatansByPegawai) {
                return $jabatansByPegawai->groupBy('jenis_jabatan');
            });

            // Untuk backward compatibility, tetap berikan jabatans yang sudah difilter
            if ($isCreating) {
                $jabatans = $allJabatans;
            } else {
                $jabatans = \App\Models\KepegawaianUniversitas\Jabatan::forJenisPegawai($pegawai->jenis_pegawai)->orderBy('jabatan')->get();
            }
        } else {
            $jabatans = [];
            $jabatansGrouped = [];
        }

        $unitKerjas = $isEditing ? \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get() : [];

        // 6. Kirim SEMUA variabel ke view di akhir metode
        return view('backend.layouts.views.pegawai-unmul.profile.show', compact(
            'pegawai',
            'documentFields',
            'isEditing',
            'isAdmin',
            'isCreating',
            'pangkats',
            'jabatans',
            'jabatansGrouped',
            'unitKerjas'
        ));
    }
    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return redirect()->route('pegawai-unmul.profile.show', ['edit' => '1']);
    }

    /**
     * Store a newly created pegawai.
     */
    public function store(Request $request)
    {
        \Log::info('ProfileController Store method called', [
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);

        try {
            // Validasi untuk create pegawai baru - hanya NIP, Jenis Pegawai, dan Status Kepegawaian yang wajib
            $validated = $request->validate([
            // Field required untuk create (hanya 3 field yang wajib)
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'nip' => 'required|string|max:18|unique:pegawais,nip',
            'status_kepegawaian' => 'required|string|in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN',

            // Field optional (semua field lainnya menjadi optional)
            'nama_lengkap' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:pegawais,email',
            'new_password' => 'nullable|string|min:8|confirmed',

            // Field optional
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
            'nomor_handphone' => 'nullable|string|max:20',
            'pangkat_terakhir_id' => 'nullable|exists:pangkats,id',
            'jabatan_terakhir_id' => 'nullable|exists:jabatans,id',
            'unit_kerja_id' => 'nullable|exists:sub_sub_unit_kerjas,id',
            'pendidikan_terakhir' => 'nullable|string',
            'nama_universitas_sekolah' => 'nullable|string|max:255',
            'nama_prodi_jurusan' => 'nullable|string|max:255',
            'nama_prodi_jurusan_s2' => 'nullable|string|max:255',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'tmt_pangkat' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date',
            'nomor_kartu_pegawai' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:16',
            'mata_kuliah_diampu' => 'nullable|string',
            'ranting_ilmu_kepakaran' => 'nullable|string',
            'url_profil_sinta' => 'nullable|string|max:255',
            'predikat_kinerja_tahun_pertama' => 'nullable|in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang,Perlu Perbaikan',
            'predikat_kinerja_tahun_kedua' => 'nullable|in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang,Perlu Perbaikan',

            // File uploads
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ijazah_terakhir' => 'nullable|file|mimes:pdf|max:10240',
            'transkrip_nilai_terakhir' => 'nullable|file|mimes:pdf|max:10240',
            'sk_pangkat_terakhir' => 'nullable|file|mimes:pdf|max:10240',
            'sk_jabatan_terakhir' => 'nullable|file|mimes:pdf|max:10240',
            'skp_tahun_pertama' => 'nullable|file|mimes:pdf|max:10240',
            'skp_tahun_kedua' => 'nullable|file|mimes:pdf|max:10240',
            'sk_cpns' => 'nullable|file|mimes:pdf|max:10240',
            'sk_pns' => 'nullable|file|mimes:pdf|max:10240',
            'pak_konversi' => 'nullable|file|mimes:pdf|max:10240',
            'sk_penyetaraan_ijazah' => 'nullable|file|mimes:pdf|max:10240',
            'disertasi_thesis_terakhir' => 'nullable|file|mimes:pdf|max:10240',
            'pak_integrasi' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Handle file uploads
        $this->handleFileUploads($request, $validated, null);

        // Handle unit_kerja_id mapping
        if ($request->filled('unit_kerja_id')) {
            $subSubUnitKerja = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with(['subUnitKerja', 'subUnitKerja.unitKerja'])
                ->find($request->unit_kerja_id);

            if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                // Set unit_kerja_id berdasarkan parent dari Sub-sub Unit Kerja
                $validated['unit_kerja_id'] = $subSubUnitKerja->subUnitKerja->unitKerja->id;
            }
        }

        // Set default values untuk field yang tidak diisi
        $validated['nama_lengkap'] = $validated['nama_lengkap'] ?? 'Belum diisi';
        $validated['email'] = $validated['email'] ?? 'belum@diisi.com';
        $validated['password'] = $validated['new_password'] ? Hash::make($validated['new_password']) : Hash::make($validated['nip']);

        // Set default values untuk field yang tidak boleh null sesuai database constraints
        $validated['gelar_depan'] = $validated['gelar_depan'] ?? '';
        $validated['gelar_belakang'] = $validated['gelar_belakang'] ?? '';
        $validated['tempat_lahir'] = $validated['tempat_lahir'] ?? 'Belum diisi';
        $validated['tanggal_lahir'] = $validated['tanggal_lahir'] ?? '1990-01-01'; // Default date
        $validated['jenis_kelamin'] = $validated['jenis_kelamin'] ?? 'Laki-Laki'; // ENUM: Laki-Laki, Perempuan
        $validated['nomor_handphone'] = $validated['nomor_handphone'] ?? 'Belum diisi';
        $validated['pendidikan_terakhir'] = $validated['pendidikan_terakhir'] ?? '';
        $validated['nama_universitas_sekolah'] = $validated['nama_universitas_sekolah'] ?? '';
        $validated['nama_prodi_jurusan'] = $validated['nama_prodi_jurusan'] ?? '';
        $validated['tmt_cpns'] = $validated['tmt_cpns'] ?? '1990-01-01'; // Default date
        $validated['tmt_pns'] = $validated['tmt_pns'] ?? '1990-01-01'; // Default date
        $validated['tmt_pangkat'] = $validated['tmt_pangkat'] ?? '1990-01-01'; // Default date
        $validated['tmt_jabatan'] = $validated['tmt_jabatan'] ?? '1990-01-01'; // Default date

        // Set default values untuk field tambahan
        $validated['nomor_kartu_pegawai'] = $validated['nomor_kartu_pegawai'] ?? '';
        $validated['nuptk'] = $validated['nuptk'] ?? '';
        $validated['mata_kuliah_diampu'] = $validated['mata_kuliah_diampu'] ?? '';
        $validated['ranting_ilmu_kepakaran'] = $validated['ranting_ilmu_kepakaran'] ?? '';
        $validated['url_profil_sinta'] = $validated['url_profil_sinta'] ?? '';
        $validated['predikat_kinerja_tahun_pertama'] = $validated['predikat_kinerja_tahun_pertama'] ?? null;
        $validated['predikat_kinerja_tahun_kedua'] = $validated['predikat_kinerja_tahun_kedua'] ?? null;

        // Set default values untuk foreign key constraints yang tidak boleh null
        if (!$validated['pangkat_terakhir_id']) {
            // Ambil pangkat default berdasarkan status kepegawaian
            $defaultPangkat = \App\Models\KepegawaianUniversitas\Pangkat::where('status_pangkat', 'PNS')->first();
            if (!$defaultPangkat) {
                $defaultPangkat = \App\Models\KepegawaianUniversitas\Pangkat::first();
            }
            $validated['pangkat_terakhir_id'] = $defaultPangkat ? $defaultPangkat->id : 1;
        }

        if (!$validated['jabatan_terakhir_id']) {
            // Ambil jabatan default berdasarkan jenis pegawai
            $defaultJabatan = \App\Models\KepegawaianUniversitas\Jabatan::where('jenis_pegawai', $validated['jenis_pegawai'])->first();
            if (!$defaultJabatan) {
                $defaultJabatan = \App\Models\KepegawaianUniversitas\Jabatan::first();
            }
            $validated['jabatan_terakhir_id'] = $defaultJabatan ? $defaultJabatan->id : 1;
        }

        if (!$validated['unit_kerja_id']) {
            // Ambil sub-sub unit kerja default (yang pertama tersedia)
            $defaultSubSubUnitKerja = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::first();
            $validated['unit_kerja_id'] = $defaultSubSubUnitKerja ? $defaultSubSubUnitKerja->id : 1;
        }

        // Hapus field yang tidak ada di tabel pegawais
        unset($validated['new_password']);
        unset($validated['new_password_confirmation']);

            // Create pegawai baru
            $pegawai = Pegawai::create($validated);

            \Log::info('Pegawai created successfully', ['pegawai_id' => $pegawai->id]);

            return redirect()->route('backend.kepegawaian-universitas.data-pegawai.index')
                             ->with('success', 'Data Pegawai berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in ProfileController Store:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in ProfileController Store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request, $pegawaiId = null)
    {
        \Log::info('ProfileController Update method called');

        // 1. Deteksi role dan ambil data pegawai
        // Cek apakah akses via route kepegawaian-universitas (admin mode)
        $isKepegawaianUniversitasRoute = $request->route()->getName() == 'backend.kepegawaian-universitas.data-pegawai.update';

        // Debug logging
        \Log::info('ProfileController Update Debug:', [
            'pegawaiId' => $pegawaiId,
            'route_name' => $request->route()->getName(),
            'isKepegawaianUniversitasRoute' => $isKepegawaianUniversitasRoute,
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_data_count' => count($request->all())
        ]);

        if ($pegawaiId || $isKepegawaianUniversitasRoute) {
            // Mode admin - edit pegawai lain (via kepegawaian-universitas route)
            $pegawai = Pegawai::findOrFail($pegawaiId);
            $isAdmin = true;
        } else {
            // Mode pegawai - edit diri sendiri (via pegawai-unmul route)
            $user = Auth::guard('pegawai')->user();
            $pegawai = Pegawai::find($user->id);
            $isAdmin = false;
        }

        // 2. Validasi berdasarkan role
        if ($isAdmin) {
            // Validasi lengkap untuk admin (termasuk field restricted)
            \Log::info('Admin validation started');
            try {
                $validated = $request->validate([
                // Field restricted (hanya admin yang bisa edit)
                'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
                'nip' => 'required|string|max:18|unique:pegawais,nip,' . $pegawai->id,
                'status_kepegawaian' => 'required|string|in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN',

                // Field yang bisa diedit semua role
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawai->id,
                'gelar_depan' => 'nullable|string|max:255',
                'gelar_belakang' => 'nullable|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'nomor_handphone' => 'required|string|max:20',
                'pendidikan_terakhir' => 'nullable|string',
                'nama_universitas_sekolah' => 'nullable|string|max:255',
                'nama_prodi_jurusan' => 'nullable|string|max:255',
                'nuptk' => 'nullable|string|max:16',
                'ranting_ilmu_kepakaran' => 'nullable|string',
                'mata_kuliah_diampu' => 'nullable|string',
                'url_profil_sinta' => 'nullable|url',
                'nomor_kartu_pegawai' => 'nullable|string|max:255',
                'pangkat_terakhir_id' => 'required|exists:pangkats,id',
                'tmt_pangkat' => 'required|date',
                'jabatan_terakhir_id' => 'nullable|exists:jabatans,id',
                'tmt_jabatan' => 'required|date',
                'unit_kerja_id' => 'required|exists:sub_sub_unit_kerjas,id',
                'tmt_cpns' => 'required|date',
                'tmt_pns' => 'required|date',
                'predikat_kinerja_tahun_pertama' => 'required|string',
                'predikat_kinerja_tahun_kedua' => 'required|string',
                'nilai_konversi' => 'nullable|numeric',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            // Use fallback value if jabatan_terakhir_id is empty
            if (empty($validated['jabatan_terakhir_id']) && $request->has('jabatan_terakhir_id_fallback')) {
                $validated['jabatan_terakhir_id'] = $request->input('jabatan_terakhir_id_fallback');
            }

            \Log::info('Admin validation completed successfully');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Admin validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                throw $e;
            }
        } else {
            // Validasi terbatas untuk pegawai (tidak include field restricted)
            $validated = $request->validate([
                // Field yang bisa diedit pegawai
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawai->id,
                'gelar_depan' => 'nullable|string|max:255',
                'gelar_belakang' => 'nullable|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'nomor_handphone' => 'required|string|max:20',
                'pendidikan_terakhir' => 'required|string',
                'nama_universitas_sekolah' => 'nullable|string|max:255',
                'nama_prodi_jurusan' => 'nullable|string|max:255',
                'nuptk' => 'nullable|string|max:16',
                'ranting_ilmu_kepakaran' => 'nullable|string',
                'mata_kuliah_diampu' => 'nullable|string',
                'url_profil_sinta' => 'nullable|url',
                'nomor_kartu_pegawai' => 'nullable|string|max:255',
                'pangkat_terakhir_id' => 'required|exists:pangkats,id',
                'tmt_pangkat' => 'required|date',
                'jabatan_terakhir_id' => 'nullable|exists:jabatans,id',
                'tmt_jabatan' => 'required|date',
                'unit_kerja_id' => 'required|exists:sub_sub_unit_kerjas,id',
                'tmt_cpns' => 'required|date',
                'tmt_pns' => 'required|date',
                'predikat_kinerja_tahun_pertama' => 'required|string',
                'predikat_kinerja_tahun_kedua' => 'required|string',
                'nilai_konversi' => 'nullable|numeric',
                'new_password' => 'nullable|sometimes|min:8|confirmed',
            ]);

            // Use fallback value if jabatan_terakhir_id is empty
            if (empty($validated['jabatan_terakhir_id']) && $request->has('jabatan_terakhir_id_fallback')) {
                $validated['jabatan_terakhir_id'] = $request->input('jabatan_terakhir_id_fallback');
            }
        }

        // 3. Handle file uploads
        $this->handleFileUploads($request, $validated, $pegawai);

        // 4. Handle update password
        if ($isAdmin && $request->filled('password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } elseif (!$isAdmin && $request->filled('new_password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        // 5. Update pegawai
        \Log::info('Updating pegawai with data:', $validated);
        $pegawai->update($validated);
        \Log::info('Pegawai updated successfully');

        // 6. Redirect berdasarkan role
        if ($isAdmin) {
            \Log::info('Redirecting to admin index');
            return redirect()->route('backend.kepegawaian-universitas.data-pegawai.index')
                            ->with('success', 'Data Pegawai berhasil diperbarui.');
        } else {
            \Log::info('Redirecting to pegawai profile');
            return redirect()->route('pegawai-unmul.profile.show')
                            ->with('success', 'Profil berhasil diperbarui.');
        }
    }

    /**
     * Display a document with access control and logging.
     *
     * Route: /pegawai-unmul/profil/dokumen/{field}
     * Same parameter structure as admin route for consistency
     */
    public function showDocument($field)
    {
        $pegawai = Auth::guard('pegawai')->user();

        // 1. Validasi field yang diizinkan
        $allowedFields = array_keys($this->getDocumentFields($pegawai));
        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid. Field: ' . $field . ', Allowed: ' . implode(', ', $allowedFields));
        }

        // 2. Cek apakah file path ada di database
        $filePath = $pegawai->$field;
        if (!$filePath) {
            abort(404, 'File tidak ditemukan di database. Field: ' . $field . ', Value: ' . ($filePath ?? 'null'));
        }

        // 3. Determine correct disk and check file existence
        $disk = $this->getFileDisk($field);
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // 4. LOGGING - Catat akses dokumen
        $this->logDocumentAccess($pegawai->id, $pegawai->id, $field, request());

        // 5. Serve file
        $fullPath = Storage::disk($disk)->path($filePath);
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = FileFacade::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Handle file uploads untuk profile update
     */
    private function handleFileUploads(Request $request, &$validatedData, $pegawai)
    {
        $fileColumns = array_keys($this->getDocumentFields($pegawai));

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                // Validasi file
                if ($column === 'foto') {
                    $request->validate([
                        $column => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
                    ]);
                } else {
                    $request->validate([
                        $column => ['required', File::types(['pdf'])->max(1024)]
                    ]);
                }

                // Hapus file lama jika ada
                if ($pegawai->$column) {
                    $this->deleteOldFile($pegawai->$column, $column);
                }

                // Upload file baru dengan hybrid strategy
                $path = $this->storeFileByType($request->file($column), $column);
                $validatedData[$column] = $path;
            }
        }
    }

    /**
     * Catat akses dokumen ke log
     */
    private function logDocumentAccess($pegawaiId, $accessorId, $documentField, $request): void
    {
        DocumentAccessLog::create([
            'pegawai_id' => $pegawaiId,
            'accessor_id' => $accessorId,
            'document_field' => $documentField,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent', ''),
            'accessed_at' => now(),
        ]);
    }

    private function storeFileByType($file, $column): string
    {
        // Define sensitive vs public files
        $sensitiveFiles = [
            'sk_pangkat_terakhir',
            'sk_jabatan_terakhir',
            'ijazah_terakhir',
            'transkrip_nilai_terakhir',
            'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir',
            'pak_konversi',
            'pak_integrasi',
            'skp_tahun_pertama',
            'skp_tahun_kedua',
            'sk_cpns',
            'sk_pns'
        ];

        if (in_array($column, $sensitiveFiles)) {
            // Sensitive files -> local disk (protected access)
            return $file->store('pegawai-files/' . $column, 'local');
        } else {
            // Public files (foto) -> public disk
            return $file->store('pegawai-files/' . $column, 'public');
        }
    }

    /**
     * Delete old file from appropriate disk
     */
    private function deleteOldFile($filePath, $column): void
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'pak_integrasi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        $disk = in_array($column, $sensitiveFiles) ? 'local' : 'public';
        Storage::disk($disk)->delete($filePath);
    }

    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'pak_integrasi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Get document fields configuration
     */
    private function getDocumentFields($pegawai = null): array
    {
        $documentFields = [
            'sk_pangkat_terakhir' => [
                'label' => 'SK Pangkat Terakhir',
                'icon' => 'file-text'
            ],
            'sk_jabatan_terakhir' => [
                'label' => 'SK Jabatan Terakhir',
                'icon' => 'briefcase'
            ],
            'ijazah_terakhir' => [
                'label' => 'Ijazah Terakhir',
                'icon' => 'graduation-cap'
            ],
            'transkrip_nilai_terakhir' => [
                'label' => 'Transkrip Nilai Terakhir',
                'icon' => 'file-spreadsheet'
            ],
            'sk_penyetaraan_ijazah' => [
                'label' => 'SK Penyetaraan Ijazah',
                'icon' => 'file-check'
            ],
            'disertasi_thesis_terakhir' => [
                'label' => 'Disertasi/Thesis Terakhir',
                'icon' => 'book-open'
            ],
            'pak_konversi' => [
                'label' => 'PAK Konversi' . (date('Y') - 1),
                'icon' => 'calculator'
            ],
            'skp_tahun_pertama' => [
                'label' => 'SKP'. (date('Y') - 1),
                'icon' => 'clipboard-check'
            ],
            'skp_tahun_kedua' => [
                'label' => 'SKP'. (date('Y') - 2),
                'icon' => 'clipboard-check'
            ],
            'sk_cpns' => [
                'label' => 'SK CPNS',
                'icon' => 'file-badge'
            ],
            'sk_pns' => [
                'label' => 'SK PNS',
                'icon' => 'file-badge'
            ],
            'foto' => [
                'label' => 'Foto Pegawai',
                'icon' => 'user'
            ]
        ];

        // Tambahkan PAK Integrasi hanya untuk jabatan tertentu
        if ($pegawai && $this->isPakIntegrasiEligible($pegawai)) {
            $documentFields['pak_integrasi'] = [
                'label' => 'PAK Integrasi',
                'icon' => 'calculator'
            ];
        }

        return $documentFields;
    }

    /**
     * Check if pegawai is eligible for PAK Integrasi
     */
    private function isPakIntegrasiEligible($pegawai): bool
    {
        // Load jabatan relationship if not loaded
        if (!$pegawai->relationLoaded('jabatan')) {
            $pegawai->load('jabatan');
        }

        // Check if pegawai has jabatan
        if (!$pegawai->jabatan) {
            return false;
        }

        // PAK Integrasi hanya untuk jabatan fungsional
        $eligibleJenisJabatan = [
            'Dosen Fungsional',
            'Tenaga Kependidikan Fungsional Tertentu'
        ];

        return in_array($pegawai->jabatan->jenis_jabatan, $eligibleJenisJabatan);
    }
}

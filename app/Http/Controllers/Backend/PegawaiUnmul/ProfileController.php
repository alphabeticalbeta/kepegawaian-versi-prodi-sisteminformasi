<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Validation\Rules\File;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Models\KepegawaianUniversitas\Jabatan;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request)
    {
        // 1. Ambil data pegawai
        $pegawai = Auth::guard('pegawai')->user();
        $pegawai = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja'])
                        ->find($pegawai->id);

        // 2. Ambil daftar field dokumen
        $documentFields = $this->getDocumentFields();

        // 3. Tentukan apakah sedang dalam mode edit atau tidak
        $isEditing = $request->has('edit') && $request->get('edit') == '1';

        // 4. Ambil data untuk dropdown HANYA JIKA sedang mode edit
        $pangkats = $isEditing ? \App\Models\KepegawaianUniversitas\Pangkat::orderByHierarchy('asc')->get() : [];
        $jabatans = $isEditing ? \App\Models\KepegawaianUniversitas\Jabatan::orderBy('jabatan')->get() : [];
        $unitKerjas = $isEditing ? \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get() : [];

        // 5. Kirim SEMUA variabel ke view di akhir metode
        return view('backend.layouts.views.pegawai-unmul.profile.show', compact(
            'pegawai',
            'documentFields',
            'isEditing',
            'pangkats',
            'jabatans',
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
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('pegawai')->user();
        $pegawai = Pegawai::find($user->id);


        $validated = $request->validate([
            // Dari Tab: Data Pribadi
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

            // Dari Tab: Kepegawaian
            'nomor_kartu_pegawai' => 'nullable|string|max:255',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'unit_kerja_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',

            // Dari Tab: PAK & SKP
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'nilai_konversi' => 'nullable|numeric',
            'new_password' => 'nullable|sometimes|min:8|confirmed',
        ]);

        // 3. Handle file uploads
        $this->handleFileUploads($request, $validated, $pegawai);

        // 4. Handle update password
        if ($request->filled('new_password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        // 5. Perintah update dijalankan pada SATU objek pegawai. Ini sekarang akan berhasil.
        $pegawai->update($validated);

        return redirect()->route('pegawai-unmul.profile.show')
                        ->with('success', 'Profil berhasil diperbarui.');
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
        $allowedFields = array_keys($this->getDocumentFields());
        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // 2. Cek apakah file path ada di database
        $filePath = $pegawai->$field;
        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
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
        $fileColumns = array_keys($this->getDocumentFields());

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
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        $disk = in_array($column, $sensitiveFiles) ? 'local' : 'public';
        Storage::disk($disk)->delete($filePath);
    }

    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Get document fields configuration
     */
    private function getDocumentFields(): array
    {
        return [
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
    }
}

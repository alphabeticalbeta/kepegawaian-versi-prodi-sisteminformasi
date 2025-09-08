<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Http\Request;
use App\Models\KepegawaianUniversitas\UsulanLog;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsulanTugasBelajarController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Display a listing of usulan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Debug information
        Log::info('UsulanTugasBelajarController@index Debug', [
            'pegawai_id' => $pegawai->id,
            'pegawai_nip' => $pegawai->nip,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'jenis_usulan_periode' => $jenisUsulanPeriode
        ]);

        // Get periode usulan yang sesuai dengan status kepegawaian
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Debug query results
        Log::info('Periode Usulan Query Results', [
            'total_periode_found' => $periodeUsulans->count(),
            'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
            'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray()
        ]);

        // Alternative query if no results
        if ($periodeUsulans->count() == 0) {
            // Try without JSON contains
            $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            Log::info('Alternative Query Results (without JSON contains)', [
                'total_periode_found' => $altPeriodeUsulans->count(),
                'periode_ids' => $altPeriodeUsulans->pluck('id')->toArray(),
                'periode_names' => $altPeriodeUsulans->pluck('nama_periode')->toArray()
            ]);

            // Use alternative results if found
            if ($altPeriodeUsulans->count() > 0) {
                $periodeUsulans = $altPeriodeUsulans;
            }
        }

        // Get usulan yang sudah dibuat oleh pegawai
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', $jenisUsulanPeriode)
                          ->with(['periodeUsulan'])
                          ->get();

        // Logika filter yang benar:
        // 1. Periode BUKA: Tampilkan semua (sesuai status kepegawaian)
        // 2. Periode TUTUP: Hanya tampilkan jika pegawai pernah submit usulan

        $periodeBuka = $periodeUsulans->where('status', 'Buka');
        $periodeTutup = $periodeUsulans->where('status', 'Tutup');

        // Get periode IDs yang pernah submit usulan (bukan draft)
        $periodeIdsWithSubmittedUsulan = $usulans->where('status_usulan', '!=', 'draft usulan')
                                                ->pluck('periode_usulan_id')
                                                ->toArray();

        // Filter periode tutup: hanya yang pernah submit usulan
        $periodeTutupWithUsulan = $periodeTutup->whereIn('id', $periodeIdsWithSubmittedUsulan);

        // Gabungkan hasil: periode buka + periode tutup yang pernah submit
        $periodeUsulans = $periodeBuka->merge($periodeTutupWithUsulan);

        // Debug usulan yang ditemukan
        Log::info('Usulan yang ditemukan untuk pegawai', [
            'pegawai_id' => $pegawai->id,
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray(),
            'periode_ids_with_submitted_usulan' => $periodeIdsWithSubmittedUsulan,
            'filtered_periode_count' => $periodeUsulans->count()
        ]);

        return view('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.index', compact('periodeUsulans', 'usulans', 'pegawai'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pegawai = Auth::user();

        // Validasi input
        $request->validate([
            'periode_usulan_id' => 'required|exists:periode_usulans,id',
            'jenis_usulan' => 'required|string',
            'jenis_tubel' => 'required|string|in:Tugas Belajar,Perpanjangan Tugas Belajar',
        ]);

        // Get periode usulan
        $periodeUsulan = PeriodeUsulan::findOrFail($request->periode_usulan_id);

        // Check apakah sudah ada usulan untuk periode ini
        $existingUsulan = $pegawai->usulans()
                                 ->where('periode_usulan_id', $request->periode_usulan_id)
                                 ->where('jenis_usulan', 'usulan-tugas-belajar')
                                 ->first();

        if ($existingUsulan) {
            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $existingUsulan->id)
                           ->with('info', 'Anda sudah memiliki usulan untuk periode ini.');
        }

        try {
            // Create usulan dengan jenis yang dipilih
            $usulan = Usulan::create([
                'pegawai_id' => $pegawai->id,
                'periode_usulan_id' => $request->periode_usulan_id,
                'jenis_usulan' => 'usulan-tugas-belajar',
                'jenis_tubel' => $request->jenis_tubel,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'status_usulan' => Usulan::STATUS_DRAFT_USULAN,
                'data_usulan' => [
                    'jenis_tubel' => $request->jenis_tubel,
                    'dokumen_usulan' => []
                ]
            ]);

            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan->id)
                             ->with('success', 'Usulan Tugas Belajar berhasil dibuat. Silakan lengkapi data dan dokumen pendukung.');

        } catch (\Exception $e) {
        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.index')
                           ->with('error', 'Terjadi kesalahan saat membuat usulan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new tugas belajar usulan.
     */
    public function createTugasBelajar(Usulan $usulan)
    {
        $pegawai = Auth::user();

        // Pastikan usulan milik pegawai yang login
        if ($usulan->pegawai_id !== $pegawai->id) {
            abort(403, 'Unauthorized');
        }

        // Pastikan jenis usulan adalah tugas belajar
        if ($usulan->jenis_usulan !== 'usulan-tugas-belajar') {
            abort(404, 'Usulan tidak ditemukan');
        }

        // Load relasi yang diperlukan untuk pegawai
        $pegawai->load(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja']);

        // Load relasi yang diperlukan untuk usulan->pegawai juga
        $usulan->load(['pegawai.pangkat', 'pegawai.jabatan', 'pegawai.unitKerja.subUnitKerja.unitKerja']);

        return view('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', compact('usulan', 'pegawai'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Load relasi yang diperlukan
        $usulan->load([
            'pegawai.unitKerja',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'periodeUsulan'
        ]);

        // Selalu gunakan view create-tugas-belajar untuk konsistensi dengan modul lain
        return view('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', compact('usulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.edit', compact('usulan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan usulan sudah tersimpan di database
        if (!$usulan->exists) {
            return back()->with('error', 'Usulan belum tersimpan di database. Silakan simpan usulan terlebih dahulu.');
        }

        $action = $request->input('action', 'simpan');

        switch ($action) {
            case 'simpan':
                return $this->simpanUsulan($request, $usulan);
            case 'kirim_ke_kepegawaian':
                return $this->kirimKeKepegawaian($request, $usulan);
            case 'kirim_perbaikan_ke_kepegawaian':
                return $this->kirimPerbaikanKeKepegawaian($request, $usulan);
            case 'kirim_perbaikan_kementerian_ke_kepegawaian':
                return $this->kirimPerbaikanKementerianKeKepegawaian($request, $usulan);
            default:
                return $this->simpanUsulan($request, $usulan);
        }
    }

    /**
     * Simpan usulan tanpa mengubah status
     */
    private function simpanUsulan(Request $request, Usulan $usulan)
    {
        // Validasi input berdasarkan jenis tubel
        $validationRules = [
            'tahun_studi' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 1),
            'alamat_lengkap' => 'required|string|max:500',
            'pendidikan_ditempuh' => 'required|string',
            'nama_prodi_dituju' => 'required|string|max:255',
            'nama_fakultas_dituju' => 'required|string|max:255',
            'nama_universitas_dituju' => 'required|string|max:255',
            'negara_studi' => 'required|string|in:Dalam Negeri,Luar Negeri',
        ];

        // Validasi dokumen pendukung (optional untuk simpan draft)
        $validationRules['kartu_pegawai'] = 'nullable|file|mimes:pdf|max:1024';

        // Validasi dokumen setneg jika luar negeri
        if ($request->negara_studi === 'Luar Negeri') {
            $validationRules['dokumen_setneg'] = 'nullable|file|mimes:pdf|max:1024';
        }

        // Validasi dokumen berdasarkan jenis tubel
        $jenisTubel = $usulan->jenis_tubel;
        if ($jenisTubel === 'Tugas Belajar') {
            $validationRules = array_merge($validationRules, [
                'surat_tunjangan_keluarga' => 'nullable|file|mimes:pdf|max:1024',
                'akta_nikah' => 'nullable|file|mimes:pdf|max:1024',
                'surat_rekomendasi_atasan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_perjanjian_tubel' => 'nullable|file|mimes:pdf|max:1024',
                'surat_jaminan_pembiayaan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_keterangan_pimpinan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_hasil_kelulusan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_pernyataan_pimpinan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_pernyataan_bersangkutan' => 'nullable|file|mimes:pdf|max:1024',
                'dokumen_akreditasi' => 'nullable|file|mimes:pdf|max:1024',
            ]);
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $validationRules = array_merge($validationRules, [
                'surat_perjanjian_perpanjangan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_perpanjangan_jaminan_pembiayaan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_rekomendasi_lembaga_pendidikan' => 'nullable|file|mimes:pdf|max:1024',
                'surat_rekomendasi_pimpinan_unit' => 'nullable|file|mimes:pdf|max:1024',
                'sk_tugas_belajar' => 'nullable|file|mimes:pdf|max:1024',
            ]);
        }

        $request->validate($validationRules);

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan - hanya ubah status jika belum ada status atau masih Draft
        $currentDataUsulan = $usulan->data_usulan ?? [];
        $currentDokumenUsulan = $currentDataUsulan['dokumen_usulan'] ?? [];

        $updateData = [
            'data_usulan' => array_merge($currentDataUsulan, [
                'tahun_studi' => $request->tahun_studi,
                'alamat_lengkap' => $request->alamat_lengkap,
                'pendidikan_ditempuh' => $request->pendidikan_ditempuh,
                'nama_prodi_dituju' => $request->nama_prodi_dituju,
                'nama_fakultas_dituju' => $request->nama_fakultas_dituju,
                'nama_universitas_dituju' => $request->nama_universitas_dituju,
                'negara_studi' => $request->negara_studi,
                'dokumen_usulan' => array_merge($currentDokumenUsulan, $dokumenPaths)
            ])
        ];

        // Hanya ubah status ke Draft jika status saat ini null atau sudah Draft
        if (is_null($usulan->status_usulan) || $usulan->status_usulan === Usulan::STATUS_DRAFT_USULAN) {
            $updateData['status_usulan'] = Usulan::STATUS_DRAFT_USULAN;
        }

        $usulan->update($updateData);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
            ->with('success', 'Usulan berhasil disimpan.');
    }

    /**
     * Kirim usulan ke Kepegawaian Universitas
     */
    private function kirimKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validasi input berdasarkan jenis tubel
        $validationRules = [
            'tahun_studi' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 1),
            'alamat_lengkap' => 'required|string|max:500',
            'pendidikan_ditempuh' => 'required|string',
            'nama_prodi_dituju' => 'required|string|max:255',
            'nama_fakultas_dituju' => 'required|string|max:255',
            'nama_universitas_dituju' => 'required|string|max:255',
            'negara_studi' => 'required|string|in:Dalam Negeri,Luar Negeri',
        ];

        // Cek dokumen yang sudah ada di database
        $existingDocuments = $usulan->data_usulan['dokumen_usulan'] ?? [];

        // Validasi dokumen pendukung berdasarkan status di database
        if (!isset($existingDocuments['kartu_pegawai']['path'])) {
            $validationRules['kartu_pegawai'] = 'required|file|mimes:pdf|max:1024';
        } else {
            $validationRules['kartu_pegawai'] = 'nullable|file|mimes:pdf|max:1024';
        }

        // Validasi dokumen setneg jika luar negeri berdasarkan status di database
        if ($request->negara_studi === 'Luar Negeri') {
            if (!isset($existingDocuments['dokumen_setneg']['path'])) {
                $validationRules['dokumen_setneg'] = 'required|file|mimes:pdf|max:1024';
            } else {
                $validationRules['dokumen_setneg'] = 'nullable|file|mimes:pdf|max:1024';
            }
        }

        // Validasi dokumen berdasarkan jenis tubel berdasarkan status di database
        $jenisTubel = $usulan->jenis_tubel;
        if ($jenisTubel === 'Tugas Belajar') {
            $tubelDocuments = [
                'surat_tunjangan_keluarga',
                'akta_nikah',
                'surat_rekomendasi_atasan',
                'surat_perjanjian_tubel',
                'surat_jaminan_pembiayaan',
                'surat_keterangan_pimpinan',
                'surat_hasil_kelulusan',
                'surat_pernyataan_pimpinan',
                'surat_pernyataan_bersangkutan',
                'dokumen_akreditasi',
            ];

            foreach ($tubelDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $perpanjanganDocuments = [
                'surat_perjanjian_perpanjangan',
                'surat_perpanjangan_jaminan_pembiayaan',
                'surat_rekomendasi_lembaga_pendidikan',
                'surat_rekomendasi_pimpinan_unit',
                'sk_tugas_belajar',
            ];

            foreach ($perpanjanganDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        }

        $request->validate($validationRules);

        // Check status - hanya bisa dikirim dari Draft Usulan
        if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN && !is_null($usulan->status_usulan)) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini. Hanya usulan dengan status Draft yang dapat dikirim.');
        }

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan
        $currentDataUsulan = $usulan->data_usulan ?? [];
        $currentDokumenUsulan = $currentDataUsulan['dokumen_usulan'] ?? [];

        $usulan->update([
            'data_usulan' => array_merge($currentDataUsulan, [
                'tahun_studi' => $request->tahun_studi,
                'alamat_lengkap' => $request->alamat_lengkap,
                'pendidikan_ditempuh' => $request->pendidikan_ditempuh,
                'nama_prodi_dituju' => $request->nama_prodi_dituju,
                'nama_fakultas_dituju' => $request->nama_fakultas_dituju,
                'nama_universitas_dituju' => $request->nama_universitas_dituju,
                'negara_studi' => $request->negara_studi,
                'dokumen_usulan' => array_merge($currentDokumenUsulan, $dokumenPaths)
            ]),
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'keterangan' => 'Usulan Tugas Belajar dikirim ke Kepegawaian Universitas',
            'catatan' => 'Usulan berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
            ->with('success', 'Usulan berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan usulan masih dalam status yang bisa dihapus
        $deletableStatuses = [
            Usulan::STATUS_DRAFT_USULAN,
        ];

        if (!in_array($usulan->status_usulan, $deletableStatuses) && !is_null($usulan->status_usulan)) {
            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.index')
                             ->with('error', 'Usulan tidak dapat dihapus karena sudah diproses.');
        }

        try {
            // Hapus dokumen usulan jika ada
            if (isset($usulan->data_usulan['dokumen_usulan'])) {
                foreach ($usulan->data_usulan['dokumen_usulan'] as $docType => $docData) {
                    if (isset($docData['path'])) {
                        Storage::disk('local')->delete($docData['path']);
                    }
                }
            }

            // Hapus dokumen dari tabel usulan_dokumens
            UsulanDokumen::where('usulan_id', $usulan->id)->delete();

            // Hapus usulan
            $usulan->delete();

        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.index')
                         ->with('success', 'Usulan Tugas Belajar berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete usulan tugas belajar', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.index')
                             ->with('error', 'Gagal menghapus usulan. Silakan coba lagi.');
        }
    }

    /**
     * Handle document uploads for usulan tugas belajar
     */
    private function handleDocumentUploads($request, $usulan): array
    {
        $filePaths = [];
        $uploadPath = 'usulan-dokumen/' . $usulan->pegawai->id . '/' . date('Y/m');

        // Document keys for usulan tugas belajar
        $documentKeys = $this->getDocumentKeys($usulan, $request);

        foreach ($documentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    $file = $request->file($key);

                    // Validate file
                    $this->validateUploadedFile($file, $key);

                    // Upload file
                    $path = $file->store($uploadPath, 'local');

                    $filePaths[$key] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                        'uploaded_by' => $usulan->pegawai->id,
                    ];

                } catch (\Throwable $e) {
                    Log::error("Failed to upload document", [
                        'document_key' => $key,
                        'error' => $e->getMessage(),
                        'pegawai_id' => $usulan->pegawai->id
                    ]);
                    throw new \RuntimeException("Gagal mengupload dokumen $key: " . $e->getMessage());
                }
            }
        }

        return $filePaths;
    }

    /**
     * Get document keys for usulan tugas belajar based on jenis_tubel
     */
    private function getDocumentKeys($usulan, $request = null): array
    {
        $jenisTubel = $usulan->jenis_tubel;
        $baseKeys = ['kartu_pegawai']; // Dokumen pendukung yang selalu ada

        // Dokumen setneg hanya jika negara studi luar negeri
        $negaraStudi = null;
        if ($request && $request->has('negara_studi')) {
            $negaraStudi = $request->input('negara_studi');
        } elseif (isset($usulan->data_usulan['negara_studi'])) {
            $negaraStudi = $usulan->data_usulan['negara_studi'];
        }

        if ($negaraStudi === 'Luar Negeri') {
            $baseKeys[] = 'dokumen_setneg';
        }

        if ($jenisTubel === 'Tugas Belajar') {
            $baseKeys = array_merge($baseKeys, [
                'surat_tunjangan_keluarga',
                'akta_nikah',
                'surat_rekomendasi_atasan',
                'surat_perjanjian_tubel',
                'surat_jaminan_pembiayaan',
                'surat_keterangan_pimpinan',
                'surat_hasil_kelulusan',
                'surat_pernyataan_pimpinan',
                'surat_pernyataan_bersangkutan',
                'dokumen_akreditasi'
            ]);
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $baseKeys = array_merge($baseKeys, [
                'surat_perjanjian_perpanjangan',
                'surat_perpanjangan_jaminan_pembiayaan',
                'surat_rekomendasi_lembaga_pendidikan',
                'surat_rekomendasi_pimpinan_unit',
                'sk_tugas_belajar'
            ]);
        }

        return $baseKeys;
    }

    /**
     * Validate uploaded file
     */
    private function validateUploadedFile($file, string $key): void
    {
        // Check file size (1MB max)
        $maxSize = 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            throw new \RuntimeException("File $key terlalu besar. Maksimal 1MB.");
        }

        // Check file type
        $allowedMimes = ['application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \RuntimeException("File $key harus berformat PDF.");
        }

        // Check file signature for PDF
        $handle = fopen($file->getRealPath(), 'r');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            throw new \RuntimeException("File $key bukan file PDF yang valid.");
        }

        // Check if file is readable
        if (!is_readable($file->getRealPath())) {
            throw new \RuntimeException("File $key tidak dapat dibaca.");
        }
    }

    /**
     * Simpan dokumen ke tabel usulan_dokumens
     */
    private function saveUsulanDocuments($usulan, array $dokumenPaths, $pegawai): void
    {
        foreach ($dokumenPaths as $nama => $fileData) {
            UsulanDokumen::create([
                'usulan_id' => $usulan->id,
                'diupload_oleh_id' => $pegawai->id,
                'nama_dokumen' => $nama,
                'path' => $fileData['path'],
            ]);
        }
    }

    /**
     * Show document for usulan tugas belajar
     */
    public function showDocument(Usulan $usulan, $field)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Get document path from data_usulan
        $dokumenUsulan = $usulan->data_usulan['dokumen_usulan'] ?? [];
        $documentData = $dokumenUsulan[$field] ?? null;

        if (!$documentData || !isset($documentData['path'])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $path = $documentData['path'];

        // Check if file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Check if this is a download request
        if (request()->has('download') && request()->get('download') == '1') {
            $filename = basename($path);
            return Storage::disk('local')->download($path, $filename);
        }

        // Return file for viewing
        return Storage::disk('local')->response($path);
    }

    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        return 'usulan-tugas-belajar';
    }

    /**
     * Handle action kirim perbaikan ke kepegawaian
     */
    protected function kirimPerbaikanKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Pastikan usulan dalam status permintaan perbaikan
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS) {
            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Validasi input berdasarkan jenis tubel
        $validationRules = [
            'tahun_studi' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 1),
            'alamat_lengkap' => 'required|string|max:500',
            'pendidikan_ditempuh' => 'required|string',
            'nama_prodi_dituju' => 'required|string|max:255',
            'nama_fakultas_dituju' => 'required|string|max:255',
            'nama_universitas_dituju' => 'required|string|max:255',
            'negara_studi' => 'required|string|in:Dalam Negeri,Luar Negeri',
        ];

        // Cek dokumen yang sudah ada di database
        $existingDocuments = $usulan->data_usulan['dokumen_usulan'] ?? [];

        // Validasi dokumen pendukung berdasarkan status di database
        if (!isset($existingDocuments['kartu_pegawai']['path'])) {
            $validationRules['kartu_pegawai'] = 'required|file|mimes:pdf|max:1024';
        } else {
            $validationRules['kartu_pegawai'] = 'nullable|file|mimes:pdf|max:1024';
        }

        // Validasi dokumen setneg jika luar negeri berdasarkan status di database
        if ($request->negara_studi === 'Luar Negeri') {
            if (!isset($existingDocuments['dokumen_setneg']['path'])) {
                $validationRules['dokumen_setneg'] = 'required|file|mimes:pdf|max:1024';
            } else {
                $validationRules['dokumen_setneg'] = 'nullable|file|mimes:pdf|max:1024';
            }
        }

        // Validasi dokumen berdasarkan jenis tubel berdasarkan status di database
        $jenisTubel = $usulan->jenis_tubel;
        if ($jenisTubel === 'Tugas Belajar') {
            $tubelDocuments = [
                'surat_tunjangan_keluarga',
                'akta_nikah',
                'surat_rekomendasi_atasan',
                'surat_perjanjian_tubel',
                'surat_jaminan_pembiayaan',
                'surat_keterangan_pimpinan',
                'surat_hasil_kelulusan',
                'surat_pernyataan_pimpinan',
                'surat_pernyataan_bersangkutan',
                'dokumen_akreditasi',
            ];

            foreach ($tubelDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $perpanjanganDocuments = [
                'surat_perjanjian_perpanjangan',
                'surat_perpanjangan_jaminan_pembiayaan',
                'surat_rekomendasi_lembaga_pendidikan',
                'surat_rekomendasi_pimpinan_unit',
                'sk_tugas_belajar',
            ];

            foreach ($perpanjanganDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        }

        $request->validate($validationRules);

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan dengan data baru
        $currentDataUsulan = $usulan->data_usulan ?? [];
        $currentDokumenUsulan = $currentDataUsulan['dokumen_usulan'] ?? [];

        $usulan->update([
            'data_usulan' => array_merge($currentDataUsulan, [
                'tahun_studi' => $request->tahun_studi,
                'alamat_lengkap' => $request->alamat_lengkap,
                'pendidikan_ditempuh' => $request->pendidikan_ditempuh,
                'nama_prodi_dituju' => $request->nama_prodi_dituju,
                'nama_fakultas_dituju' => $request->nama_fakultas_dituju,
                'nama_universitas_dituju' => $request->nama_universitas_dituju,
                'negara_studi' => $request->negara_studi,
                'dokumen_usulan' => array_merge($currentDokumenUsulan, $dokumenPaths)
            ]),
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            'catatan' => 'Usulan perbaikan Tugas Belajar dikirim ke Kepegawaian Universitas',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
            ->with('success', 'Usulan perbaikan Tugas Belajar berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Handle action kirim perbaikan kementerian ke kepegawaian
     */
    protected function kirimPerbaikanKementerianKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Pastikan usulan dalam status permintaan perbaikan dari kementerian
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) {
            return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Validasi input berdasarkan jenis tubel
        $validationRules = [
            'tahun_studi' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 1),
            'alamat_lengkap' => 'required|string|max:500',
            'pendidikan_ditempuh' => 'required|string',
            'nama_prodi_dituju' => 'required|string|max:255',
            'nama_fakultas_dituju' => 'required|string|max:255',
            'nama_universitas_dituju' => 'required|string|max:255',
            'negara_studi' => 'required|string|in:Dalam Negeri,Luar Negeri',
        ];

        // Cek dokumen yang sudah ada di database
        $existingDocuments = $usulan->data_usulan['dokumen_usulan'] ?? [];

        // Validasi dokumen pendukung berdasarkan status di database
        if (!isset($existingDocuments['kartu_pegawai']['path'])) {
            $validationRules['kartu_pegawai'] = 'required|file|mimes:pdf|max:1024';
        } else {
            $validationRules['kartu_pegawai'] = 'nullable|file|mimes:pdf|max:1024';
        }

        // Validasi dokumen setneg jika luar negeri berdasarkan status di database
        if ($request->negara_studi === 'Luar Negeri') {
            if (!isset($existingDocuments['dokumen_setneg']['path'])) {
                $validationRules['dokumen_setneg'] = 'required|file|mimes:pdf|max:1024';
            } else {
                $validationRules['dokumen_setneg'] = 'nullable|file|mimes:pdf|max:1024';
            }
        }

        // Validasi dokumen berdasarkan jenis tubel berdasarkan status di database
        $jenisTubel = $usulan->jenis_tubel;
        if ($jenisTubel === 'Tugas Belajar') {
            $tubelDocuments = [
                'surat_tunjangan_keluarga',
                'akta_nikah',
                'surat_rekomendasi_atasan',
                'surat_perjanjian_tubel',
                'surat_jaminan_pembiayaan',
                'surat_keterangan_pimpinan',
                'surat_hasil_kelulusan',
                'surat_pernyataan_pimpinan',
                'surat_pernyataan_bersangkutan',
                'dokumen_akreditasi',
            ];

            foreach ($tubelDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        } elseif ($jenisTubel === 'Perpanjangan Tugas Belajar') {
            $perpanjanganDocuments = [
                'surat_perjanjian_perpanjangan',
                'surat_perpanjangan_jaminan_pembiayaan',
                'surat_rekomendasi_lembaga_pendidikan',
                'surat_rekomendasi_pimpinan_unit',
                'sk_tugas_belajar',
            ];

            foreach ($perpanjanganDocuments as $docKey) {
                if (!isset($existingDocuments[$docKey]['path'])) {
                    $validationRules[$docKey] = 'required|file|mimes:pdf|max:1024';
                } else {
                    $validationRules[$docKey] = 'nullable|file|mimes:pdf|max:1024';
                }
            }
        }

        $request->validate($validationRules);

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan dengan data baru
        $currentDataUsulan = $usulan->data_usulan ?? [];
        $currentDokumenUsulan = $currentDataUsulan['dokumen_usulan'] ?? [];

        $usulan->update([
            'data_usulan' => array_merge($currentDataUsulan, [
                'tahun_studi' => $request->tahun_studi,
                'alamat_lengkap' => $request->alamat_lengkap,
                'pendidikan_ditempuh' => $request->pendidikan_ditempuh,
                'nama_prodi_dituju' => $request->nama_prodi_dituju,
                'nama_fakultas_dituju' => $request->nama_fakultas_dituju,
                'nama_universitas_dituju' => $request->nama_universitas_dituju,
                'negara_studi' => $request->negara_studi,
                'dokumen_usulan' => array_merge($currentDokumenUsulan, $dokumenPaths)
            ]),
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEMENTERIAN
        ]);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN,
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEMENTERIAN,
            'catatan' => 'Usulan perbaikan dari Kementerian dikirim ke Kepegawaian Universitas',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-tugas-belajar.create-tugas-belajar', $usulan)
            ->with('success', 'Usulan perbaikan dari Kementerian berhasil dikirim ke Kepegawaian Universitas.');
    }

    // Method getLogs dihapus - sudah digabung ke UsulanPegawaiController
}

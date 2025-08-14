<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Admin Fakultas Controller
 * 
 * Controller untuk mengelola fitur-fitur admin fakultas termasuk:
 * - Dashboard dengan statistik usulan
 * - Hybrid approach untuk usulan jabatan & pangkat
 * - Validasi dan approval usulan
 * 
 * HYBRID APPROACH ARCHITECTURE:
 * - Separate routes: /usulan/jabatan, /usulan/pangkat
 * - Shared view template: index-dynamic.blade.php
 * - Dynamic configuration based on type
 * - Performance optimized with caching
 * 
 * @package App\Http\Controllers\Backend\AdminFakultas
 * @author Development Team
 * @version 2.0 - Hybrid Approach Implementation
 */

class AdminFakultasController extends Controller
{
    /**
    * Menampilkan dashboard dengan daftar usulan untuk fakultas terkait.
    */
    public function dashboard()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();
        
        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);
        
        // Gunakan helper method untuk mendapatkan periode usulan
        $periodeUsulans = $this->getPeriodeUsulanWithCount($unitKerja);
        
        // Get dashboard statistics
        $statistics = $this->getDashboardStatistics($periodeUsulans, $unitKerja);
        
        return view('backend.layouts.admin-fakultas.dashboard', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }

    /**
     * Menampilkan daftar periode usulan KHUSUS JABATAN.
     */
    public function indexUsulanJabatan()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user()->load('unitKerjaPengelola');
        $unitKerja = $admin->unitKerjaPengelola;

        if (!$unitKerja) {
            return view('backend.layouts.admin-fakultas.usulan.index', [
                'periodeUsulans' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'unitKerja' => null
            ]);
        }

        $unitKerjaId = $unitKerja->id;

        $periodeUsulans = PeriodeUsulan::query()
            ->where('jenis_usulan', 'jabatan')
            ->withCount([
                // Count semua usulan yang memerlukan review (status Diajukan atau Sedang Direview)
                'usulans as jumlah_pengusul' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.admin-fakultas.usulan.index', compact('periodeUsulans', 'unitKerja'));
    }

    /**
     * Menampilkan detail satu usulan spesifik untuk VALIDASI.
     * ENHANCED: Process data from helper untuk ditampilkan di view
     */
    public function show(Usulan $usulan)
    {
        try {
            /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
            $admin = Auth::user();

            // Authorization check (existing code)
            $adminFakultasId = $admin->unit_kerja_id;
            $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

            if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
                Log::warning('Admin Fakultas mencoba akses usulan dari fakultas lain.', [
                    'admin_id' => $admin->id,
                    'admin_fakultas_id' => $adminFakultasId,
                    'usulan_id' => $usulan->id,
                    'usulan_fakultas_id' => $usulanPegawaiFakultasId
                ]);
                return redirect()->route('admin-fakultas.dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak berhak melihat usulan dari fakultas lain.');
            }

            // Eager loading (existing code)
            $usulan->load([
                'pegawai.pangkat',
                'pegawai.jabatan',
                'pegawai.unitKerja',
                'jabatanLama',
                'jabatanTujuan',
                'dokumens',
                'logs' => function ($query) {
                    $query->with('dilakukanOleh')->latest();
                }
            ]);

            // ENHANCED: Process validation fields dengan helper
            $validationFields = $this->processValidationFieldsForView($usulan);
            
            // ENHANCED: Process BKD labels for display
            $bkdLabels = $usulan->getBkdDisplayLabels();

            // Existing validation data
            $existingValidation = $usulan->getValidasiByRole('admin_fakultas');

            // ENHANCED: Process dokumen data untuk view
            $dokumenData = $this->processDokumenDataForView($usulan);

            return view('backend.layouts.admin-fakultas.usulan-detail-wrapper', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
                'bkdLabels' => $bkdLabels,
                'dokumenData' => $dokumenData, // NEW: Processed document data
                // Multi-role configuration
                'currentRole' => 'admin_fakultas',
                'formAction' => route('admin-fakultas.usulan.save-validation', $usulan->id),
                'backUrl' => route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id),
                'backText' => 'Kembali ke Daftar Pengusul',
                'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']),
                'roleConfig' => [
                    'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']),
                    'submitFunctions' => ['save', 'return_to_pegawai', 'reject_to_pegawai', 'forward_to_university']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail usulan: ' . $e->getMessage(), ['usulan_id' => $usulan->id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data detail usulan. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan hasil validasi admin fakultas.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {
        // Ambil data awal yang dibutuhkan untuk semua aksi
        $actionType = $request->input('action_type', 'save_only');
        $adminId = Auth::id();
        $statusLama = $usulan->status_usulan;

        DB::beginTransaction();
        try {
            switch ($actionType) {
                case 'return_to_pegawai':
                    // Validasi khusus untuk aksi 'kembalikan ke pegawai'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_umum' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_umum.required' => 'Catatan untuk pegawai wajib diisi.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    $invalidFields = $usulan->getInvalidFields('admin_fakultas');

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan dikembalikan oleh Admin Fakultas untuk perbaikan."];
                    if (count($invalidFields) > 0) {
                        $catatanDetail[] = "\nItem yang perlu diperbaiki:";
                        foreach ($invalidFields as $field) {
                            $catatanDetail[] = "• " . ucwords(str_replace('_', ' ', $field['field'])) . ": {$field['keterangan']}";
                        }
                    }
                    $catatanDetail[] = "\nCatatan Tambahan:";
                    $catatanDetail[] = $validatedData['catatan_umum'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan
                    $usulan->status_usulan = 'Perlu Perbaikan';
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    break;

                case 'forward_to_university':
                    // Validasi khusus untuk aksi 'teruskan ke universitas'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'nomor_surat_usulan' => 'required|string|max:255',
                        'file_surat_usulan' => 'required|file|mimes:pdf|max:1024', // 1MB = 1024KB
                        'nomor_berita_senat' => 'required|string|max:255',
                        'file_berita_senat' => 'required|file|mimes:pdf|max:1024', // 1MB = 1024KB,
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Cek lagi setelah data validasi disimpan
                    if ($usulan->hasInvalidFields('admin_fakultas')) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'validation' => 'Tidak dapat meneruskan usulan. Masih ada item yang tidak sesuai dalam validasi.'
                        ]);
                    }

                    // Simpan dokumen pendukung fakultas
                    $dokumenPendukung = [
                        'nomor_surat_usulan' => $validatedData['nomor_surat_usulan'],
                        'nomor_berita_senat' => $validatedData['nomor_berita_senat'],
                        'file_surat_usulan_path' => $request->file('file_surat_usulan')->store('dokumen-fakultas/surat-usulan', 'public'),
                        'file_berita_senat_path' => $request->file('file_berita_senat')->store('dokumen-fakultas/berita-senat', 'public'),
                    ];
                    $currentValidasi = $usulan->validasi_data;
                    $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $dokumenPendukung;
                    $usulan->validasi_data = $currentValidasi;

                    // Update status usulan
                    $usulan->status_usulan = 'Diusulkan ke Universitas';
                    $logMessage = 'Usulan divalidasi dan diteruskan ke Universitas.';
                    break;

                default: // save_only
                    // Validasi khusus untuk aksi 'simpan saja'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    if ($usulan->status_usulan === 'Diajukan') {
                        $usulan->status_usulan = 'Sedang Direview';
                    }
                    $logMessage = 'Hasil validasi disimpan oleh Admin Fakultas.';
                    break;
            }

            $usulan->save();
            $usulan->createLog($usulan->status_usulan, $statusLama, $logMessage, $adminId);

            DB::commit();

            return redirect()->route('admin-fakultas.dashboard')->with('success', 'Aksi pada usulan berhasil diproses.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            // Penting: Mengembalikan ke halaman sebelumnya dengan error dan input lama
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Data yang dimasukkan tidak valid. Silakan periksa kembali.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan validasi: ' . $e->getMessage(), ['usulan_id' => $usulan->id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses validasi.');
        }
    }

    /**
     * Menampilkan daftar pengusul per periode.
     */
    public function showPendaftar(PeriodeUsulan $periodeUsulan)
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();
        $unitKerjaId = $admin->unit_kerja_id;

        // PERBAIKAN: Tampilkan semua usulan (bukan hanya yang berstatus "Diajukan")
        // agar admin fakultas bisa melihat riwayat semua usulan yang pernah mereka proses
        $usulans = Usulan::query()
            ->where('periode_usulan_id', $periodeUsulan->id)
            // HAPUS filter status_usulan agar semua usulan ditampilkan
            // ->where('status_usulan', 'Diajukan')
            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($unitKerjaId) {
                $query->where('id', $unitKerjaId);
            })
            ->with(['pegawai', 'jabatanLama', 'jabatanTujuan'])
            ->latest()
            ->paginate(15);

        return view('backend.layouts.admin-fakultas.usulan.pengusul', [
            'periode' => $periodeUsulan,
            'usulans' => $usulans,
        ]);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        // TODO: Tambahkan otorisasi untuk memastikan admin fakultas ini
        // berhak melihat usulan dari fakultas pegawai terkait.

        // Validasi field yang diizinkan
        $allowedFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Cari path file dari data JSON
        $filePath = data_get($usulan->data_usulan, "dokumen_usulan.{$field}.path");

        // Fallback jika struktur datanya lama
        if (!$filePath) {
            $filePath = data_get($usulan->data_usulan, $field);
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di penyimpanan.');
        }

        // Kirim file ke browser
        return response()->file(Storage::disk('local')->path($filePath));
    }

    /**
     * Helper method untuk mendapatkan unit kerja admin fakultas
     */
    private function getAdminUnitKerja($admin)
    {
        try {
            // Coba ambil unit kerja langsung dari admin
            if ($admin->unit_kerja_id) {
                return \App\Models\BackendUnivUsulan\UnitKerja::find($admin->unit_kerja_id);
            }
            
            // Fallback: ambil dari hierarki jika admin tidak punya unit_kerja_id
            if ($admin->unit_kerja_terakhir_id) {
                $subSubUnit = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with('subUnitKerja.unitKerja')
                    ->find($admin->unit_kerja_terakhir_id);
                
                if ($subSubUnit && $subSubUnit->subUnitKerja && $subSubUnit->subUnitKerja->unitKerja) {
                    return $subSubUnit->subUnitKerja->unitKerja;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting admin unit kerja: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper method untuk mendapatkan periode usulan dengan count
     */
    private function getPeriodeUsulanWithCount($unitKerja)
    {
        if (!$unitKerja) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
        
        try {
            return \App\Models\BackendUnivUsulan\PeriodeUsulan::withCount([
                'usulans as jumlah_pengusul' => function ($query) use ($unitKerja) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                            $subQuery->where('id', $unitKerja->id);
                        });
                }
            ])->latest()->paginate(10);
        } catch (\Exception $e) {
            \Log::error('Error getting periode usulan: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
    }

    private function getDashboardStatistics($periodeUsulans, $unitKerja)
    {
        return [
            'total_periode' => $periodeUsulans->total(),
            'total_pengusul' => $periodeUsulans->sum('jumlah_pengusul'),
            'unit_kerja_name' => $unitKerja ? $unitKerja->nama : 'Tidak diketahui',
            'has_pending_review' => $periodeUsulans->sum('jumlah_pengusul') > 0
        ];
    }

    public function usulanJabatan()
    {
        return $this->showUsulanData('jabatan');
    }

    /**
     * Menampilkan usulan pangkat dengan shared view
     */
    public function usulanPangkat()
    {
        return $this->showUsulanData('pangkat');
    }

    /**
     * Shared method untuk menampilkan data usulan berdasarkan type
     */
    private function showUsulanData($type)
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();
        $unitKerja = $this->getAdminUnitKerja($admin);
        
        // Configuration untuk setiap type
        $config = $this->getUsulanConfig($type, $unitKerja);
        
        return view('backend.layouts.admin-fakultas.usulan.index-dynamic', compact('config', 'unitKerja'));
    }

    /**
     * Get configuration untuk setiap type usulan
     */
    private function getUsulanConfig($type, $unitKerja)
    {
        $baseConfig = [
            'type' => $type,
            'unitKerja' => $unitKerja,
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin-fakultas.dashboard')],
                ['name' => 'Usulan ' . ucfirst($type), 'url' => null]
            ]
        ];
        
        switch ($type) {
            case 'jabatan':
                return array_merge($baseConfig, [
                    'title' => 'Usulan Jabatan',
                    'description' => 'Daftar usulan kenaikan jabatan dari pegawai fakultas',
                    'icon' => 'briefcase',
                    'color' => 'indigo',
                    'data' => $this->getJabatanData($unitKerja),
                    'columns' => [
                        'nama_periode' => 'Nama Periode',
                        'jenis_usulan' => 'Jenis',
                        'tanggal' => 'Jadwal Usulan', 
                        'status' => 'Status',
                        'jumlah_pengusul' => 'Review'
                    ]
                ]);
                
            case 'pangkat':
                return array_merge($baseConfig, [
                    'title' => 'Usulan Pangkat', 
                    'description' => 'Daftar usulan kenaikan pangkat dari pegawai fakultas',
                    'icon' => 'award',
                    'color' => 'emerald',
                    'data' => $this->getPangkatData($unitKerja),
                    'columns' => [
                        'nama_periode' => 'Nama Periode',
                        'jenis_usulan' => 'Jenis',
                        'tanggal' => 'Jadwal Usulan', 
                        'status' => 'Status',
                        'jumlah_pengusul' => 'Review'
                    ]
                ]);
                
            default:
                return array_merge($baseConfig, [
                    'title' => 'Data Tidak Ditemukan',
                    'description' => 'Type data tidak dikenali',
                    'icon' => 'alert-circle',
                    'color' => 'red',
                    'data' => collect(),
                    'columns' => []
                ]);
        }
    }

    private function getJabatanData($unitKerja)
    {
        try {
            // SIMPLE QUERY untuk testing
            return \App\Models\BackendUnivUsulan\PeriodeUsulan::query()
                ->where('jenis_usulan', 'jabatan')
                ->latest()
                ->paginate(5);
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }
}

    /**
     * Get data pangkat untuk admin fakultas
     */
    private function getPangkatData($unitKerja)
    {
        try {
            // SIMPLE QUERY untuk testing
            return \App\Models\BackendUnivUsulan\PeriodeUsulan::query()
                ->where('jenis_usulan', 'pangkat')
                ->latest()
                ->paginate(5);
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }
    }

    /**
     * Get formatted status badge untuk UI
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'Buka':
                return [
                    'class' => 'bg-green-100 text-green-800',
                    'text' => 'Buka'
                ];
            case 'Tutup':
                return [
                    'class' => 'bg-red-100 text-red-800', 
                    'text' => 'Tutup'
                ];
            default:
                return [
                    'class' => 'bg-gray-100 text-gray-800',
                    'text' => $status
                ];
        }
    }

    /**
 * FIXED: Enhanced autosave method yang memastikan semua data tersimpan
 * Tambahkan method ini ke AdminFakultasController
 */
    public function autosaveValidation(Request $request, Usulan $usulan)
    {
        try {
            \Log::info('Autosave request received', [
                'usulan_id' => $usulan->id,
                'request_data_keys' => array_keys($request->all()),
                'validation_keys' => array_keys($request->input('validation', []))
            ]);

            // Enhanced validation
            $validatedData = $request->validate([
                'validation' => 'required|array',
                'validation.*' => 'array',
                'validation.*.*' => 'array',
                'validation.*.*.status' => 'required|in:sesuai,tidak_sesuai',
                'validation.*.*.keterangan' => 'nullable|string',
                'action_type' => 'required|in:save_only'
            ]);

            // Quick authorization check
            $admin = Auth::user();
            if ($admin->unit_kerja_id !== $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // CRITICAL: Preserve existing validation data dan merge dengan data baru
            $existingValidation = $usulan->validasi_data ?? [];
            $adminFakultasValidation = $existingValidation['admin_fakultas'] ?? [];
            
            // Get current validation data (preserve non-validation fields)
            $currentValidationData = $adminFakultasValidation['validation'] ?? [];
            
            // Merge dengan data baru - PENTING: jangan replace, tapi merge
            $newValidationData = $validatedData['validation'];
            
            // Deep merge: preserve existing categories dan fields
            foreach ($newValidationData as $category => $fields) {
                if (!isset($currentValidationData[$category])) {
                    $currentValidationData[$category] = [];
                }
                
                foreach ($fields as $field => $fieldData) {
                    $currentValidationData[$category][$field] = $fieldData;
                }
            }

            // Update the complete validation structure
            $adminFakultasValidation['validation'] = $currentValidationData;
            $adminFakultasValidation['validated_by'] = $admin->id;
            $adminFakultasValidation['validated_at'] = now();
            
            // Preserve other admin_fakultas data (like dokumen_pendukung)
            $existingValidation['admin_fakultas'] = $adminFakultasValidation;
            
            // Save dengan complete data structure
            $usulan->validasi_data = $existingValidation;
            
            // Update status jika diperlukan (only on first save)
            if ($usulan->status_usulan === 'Diajukan') {
                $usulan->status_usulan = 'Sedang Direview';
            }
            
            $usulan->save();

            // Log success dengan detail
            \Log::info('Autosave successful', [
                'usulan_id' => $usulan->id,
                'saved_categories' => array_keys($currentValidationData),
                'total_fields_saved' => array_sum(array_map('count', $currentValidationData)),
                'status' => $usulan->status_usulan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'saved_fields' => array_sum(array_map('count', $currentValidationData)),
                'categories' => array_keys($currentValidationData)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Autosave validation failed', [
                'usulan_id' => $usulan->id,
                'errors' => $e->errors(),
                'input' => $request->input('validation', [])
            ]);
            
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Autosave error: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Save failed'], 500);
        }
    }

    /**
     * NEW: Process validation fields dengan data dari helper
     */
    private function processValidationFieldsForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        $validationFields = Usulan::getValidationFieldsWithDynamicBkd($usulan);
        $processedFields = [];

        foreach ($validationFields as $category => $fields) {
            $processedFields[$category] = [];
            
            foreach ($fields as $field) {
                // Get processed data from helper
                $fieldValue = $fieldHelper->getFieldValue($category, $field);
                
                $processedFields[$category][$field] = [
                    'raw_value' => $fieldValue,
                    'display_value' => $fieldValue,
                    'has_link' => $this->checkIfFieldHasLink($fieldValue),
                    'field_type' => $this->determineFieldType($category, $field)
                ];
            }
        }

        return $processedFields;
    }

    /**
     * NEW: Process dokumen data untuk ditampilkan di view
     */
    private function processDokumenDataForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        
        return [
            'dokumen_profil' => $this->processDokumenProfil($usulan, $fieldHelper),
            'dokumen_usulan' => $this->processDokumenUsulan($usulan, $fieldHelper),
            'dokumen_bkd' => $this->processDokumenBkd($usulan, $fieldHelper),
            'dokumen_pendukung' => $this->processDokumenPendukung($usulan, $fieldHelper)
        ];
    }

    /**
     * SAFE: Process dokumen profil dengan override routing untuk Admin Fakultas
     */
    private function processDokumenProfil(Usulan $usulan, $fieldHelper): array
    {
        $profilFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir'
        ];

        $processed = [];
        foreach ($profilFields as $field) {
            // Ambil raw data dari pegawai (TANPA helper routing)
            $dokumenPath = $usulan->pegawai->{$field} ?? null;
            
            if (!empty($dokumenPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-pegawai-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-500">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }


    /**
     * NEW: Process dokumen usulan
     */
    private function processDokumenUsulan(Usulan $usulan, $fieldHelper): array
    {
        $usulanFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        $processed = [];
        foreach ($usulanFields as $field) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);
            
            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

      /**
     * NEW: Process dokumen BKD
     */
    private function processDokumenBkd(Usulan $usulan, $fieldHelper): array
    {
        $bkdLabels = $usulan->getBkdDisplayLabels();
        $processed = [];

        foreach ($bkdLabels as $field => $label) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);
            
            // Handle legacy BKD mapping jika diperlukan
            if (empty($docPath) && str_starts_with($field, 'bkd_semester_')) {
                $docPath = $this->findLegacyBkdPath($usulan, $field, $label);
            }
            
            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => $label,
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * NEW: Process dokumen pendukung (dari admin fakultas)
     */
    private function processDokumenPendukung(Usulan $usulan, $fieldHelper): array
    {
        $pendukungFields = [
            'nomor_surat_usulan', 'file_surat_usulan',
            'nomor_berita_senat', 'file_berita_senat'
        ];

        $processed = [];
        foreach ($pendukungFields as $field) {
            if (str_starts_with($field, 'file_')) {
                // Handle file fields dengan override routing
                $validasiData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                $pathKey = $field . '_path';
                $path = $validasiData[$pathKey] ?? $validasiData[$field] ?? null;

                // Handle array path
                if (is_array($path)) {
                    $path = $path['path'] ?? $path['value'] ?? $path[0] ?? null;
                }

                if (!empty($path) && \Storage::disk('public')->exists($path)) {
                    $route = route('admin-fakultas.usulan.show-dokumen-pendukung', [$usulan->id, $field]);
                    $value = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">✓ Lihat Dokumen</a>';
                    $hasDocument = true;
                    $status = 'available';
                } else {
                    $value = '<span class="text-red-600">✗ Belum diunggah</span>';
                    $hasDocument = false;
                    $status = 'missing';
                }
            } else {
                // Handle text fields - bisa pakai helper karena tidak ada routing
                $value = $fieldHelper->getFieldValue('dokumen_pendukung', $field);
                $hasDocument = false;
                $status = (trim($value) !== '' && $value !== '-') ? 'filled' : 'empty';
            }
            
            $processed[$field] = [
                'label' => $this->getDokumenPendukungLabel($field),
                'value' => $value,
                'has_link' => strpos($value, '<a href=') !== false,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * SAFE: Helper untuk find legacy BKD path (extracted from helper logic)
     */
    private function findLegacyBkdPath(Usulan $usulan, string $field, string $label): ?string
    {
        if (!str_starts_with($field, 'bkd_semester_')) {
            return null;
        }

        $num = (int) str_replace('bkd_semester_', '', $field);
        if ($num < 1 || $num > 4) {
            return null;
        }

        // Parse label untuk mendapatkan semester dan tahun
        if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $label, $m)) {
            $sem = strtolower($m[1]); // ganjil|genap
            $y1  = $m[2];
            $y2  = $m[3];

            // Coba exact legacy key
            $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;
            $docPath = $usulan->getDocumentPath($legacyKey);

            if (!empty($docPath)) {
                return $docPath;
            }

            // Scan semua key BKD di data usulan
            $dokumenUsulan = $usulan->data_usulan['dokumen_usulan'] ?? [];
            foreach ($dokumenUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }

            // Fallback ke struktur lama
            $dataUsulan = $usulan->data_usulan ?? [];
            foreach ($dataUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }
        }

        return null;
    }

    /**
     * NEW: Helper untuk label dokumen pendukung
     */
    private function getDokumenPendukungLabel(string $field): string
    {
        $labels = [
            'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
            'file_surat_usulan' => 'Dokumen Surat Usulan Fakultas',
            'nomor_berita_senat' => 'Nomor Berita Senat',
            'file_berita_senat' => 'Dokumen Berita Senat'
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
    }

    /**
     * NEW: Helper untuk status dokumen pendukung
     */
    private function getDokumenPendukungStatus(string $value): string
    {
        if (strpos($value, '✓ Lihat Dokumen') !== false) {
            return 'available';
        } elseif (strpos($value, '✗ Belum diunggah') !== false || strpos($value, '✗ File tidak ditemukan') !== false) {
            return 'missing';
        } elseif (trim($value) !== '' && $value !== '-') {
            return 'filled';
        }
        
        return 'empty';
    }

    /**
     * NEW: Check if field value contains link
     */
    private function checkIfFieldHasLink(string $fieldValue): bool
    {
        return strpos($fieldValue, '<a href=') !== false;
    }

    /**
     * NEW: Determine field type for display
     */
    private function determineFieldType(string $category, string $field): string
    {
        if (in_array($category, ['dokumen_profil', 'dokumen_usulan', 'dokumen_bkd'])) {
            return 'document_link';
        } elseif ($category === 'dokumen_pendukung') {
            return str_contains($field, 'file_') ? 'document_file' : 'text_field';
        } elseif (str_contains($field, 'link_')) {
            return 'external_link';
        }
        
        return 'text_field';
    }


}

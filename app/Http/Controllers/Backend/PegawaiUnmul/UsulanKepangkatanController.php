<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\UsulanLog;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Services\PangkatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsulanKepangkatanController extends Controller
{
    protected $pangkatService;

    public function __construct(PangkatService $pangkatService)
    {
        $this->pangkatService = $pangkatService;
    }

    /**
     * Display a listing of usulan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Debug information
        Log::info('UsulanKepangkatanController@index Debug', [
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

        // Debug usulan yang ditemukan
        Log::info('Usulan yang ditemukan untuk pegawai', [
            'pegawai_id' => $pegawai->id,
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray()
        ]);

        // Get status kepegawaian dari pegawai
        $statusKepegawaian = $pegawai->status_kepegawaian ?? null;

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.index', compact('periodeUsulans', 'usulans', 'pegawai', 'statusKepegawaian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pegawai = Auth::user();
        
        // Validate request
        $request->validate([
            'periode_id' => 'required|exists:periode_usulans,id',
            'jenis_usulan' => 'required|in:Dosen PNS,Jabatan Administrasi,Jabatan Fungsional Tertentu,Jabatan Struktural',
        ]);
        
        // Get periode usulan
        $periodeUsulan = PeriodeUsulan::findOrFail($request->periode_id);
        
        // Validate if periode is open and accessible for this pegawai
        if ($periodeUsulan->status !== 'Buka') {
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Periode usulan sudah ditutup.');
        }
        
        if (!in_array($pegawai->status_kepegawaian, $periodeUsulan->status_kepegawaian ?? [])) {
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Anda tidak memiliki akses untuk periode ini.');
        }
        
        // Check if pegawai already has active usulan for this periode
        $existingUsulan = $pegawai->usulans()
                                 ->where('periode_usulan_id', $request->periode_id)
                                 ->where('jenis_usulan', 'usulan-kepangkatan')
                                 ->first();
        
        if ($existingUsulan) {
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $existingUsulan->id)
                             ->with('info', 'Anda sudah memiliki usulan untuk periode ini.');
        }
        
        try {
            // Create usulan dengan jenis yang dipilih
            $usulan = Usulan::create([
                'pegawai_id' => $pegawai->id,
                'periode_usulan_id' => $request->periode_id,
                'jenis_usulan' => 'usulan-kepangkatan',
                'status_usulan' => Usulan::STATUS_DRAFT_USULAN,
                'data_usulan' => [
                    'jenis_usulan_pangkat' => $request->jenis_usulan,
                    'dokumen_usulan' => []
                ]
            ]);
            
            // Log aktivitas
            UsulanLog::create([
                'usulan_id' => $usulan->id,
                'dilakukan_oleh_id' => $pegawai->id,
                'action' => 'created',
                'status_sebelumnya' => null,
                'status_baru' => Usulan::STATUS_DRAFT_USULAN,
                'keterangan' => 'Usulan kepangkatan dibuat dengan jenis: ' . $request->jenis_usulan,
                'catatan' => 'Usulan berhasil dibuat'
            ]);
            
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan->id)
                             ->with('success', 'Usulan Kepangkatan berhasil dibuat. Silakan lengkapi data dan dokumen pendukung.');
                             
        } catch (\Exception $e) {
            Log::error('Failed to create usulan kepangkatan', [
                'pegawai_id' => $pegawai->id,
                'periode_id' => $request->periode_id,
                'jenis_usulan' => $request->jenis_usulan,
                'error' => $e->getMessage()
            ]);
            
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Terjadi kesalahan saat membuat usulan. Silakan coba lagi.');
        }
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
            'periodeUsulan',
            'pangkatTujuan'
        ]);

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.show', compact('usulan'));
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

        // Redirect ke halaman show karena pengeditan bisa dilakukan langsung di sana
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan);
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
            case 'kirim_perbaikan_bkn_ke_kepegawaian':
                return $this->kirimPerbaikanBknKeKepegawaian($request, $usulan);
            default:
                return $this->simpanUsulan($request, $usulan);
        }
    }

    /**
     * Simpan usulan tanpa mengubah status
     */
    private function simpanUsulan(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);
        
        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_DRAFT_USULAN
        ]);

        // Update dokumen_usulan in data_usulan
        $dataUsulan = $usulan->data_usulan;
        $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
        $usulan->update(['data_usulan' => $dataUsulan]);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan)
            ->with('success', 'Usulan berhasil disimpan.');
    }

    /**
     * Kirim usulan ke Kepegawaian Universitas
     */
    private function kirimKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);
        
        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status - hanya bisa dikirim dari Draft Usulan
        if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN && !is_null($usulan->status_usulan)) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini. Hanya usulan dengan status Draft yang dapat dikirim.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'keterangan' => 'Usulan dikirim ke Kepegawaian Universitas',
            'catatan' => 'Usulan berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan)
            ->with('success', 'Usulan berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Kirim perbaikan usulan ke Kepegawaian Universitas
     */
    private function kirimPerbaikanKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);
        
        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted_revision',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            'keterangan' => 'Perbaikan usulan dikirim ke Kepegawaian Universitas',
            'catatan' => 'Perbaikan usulan berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan)
            ->with('success', 'Perbaikan usulan berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Kirim perbaikan usulan dari BKN ke Kepegawaian Universitas
     */
    private function kirimPerbaikanBknKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);
        
        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted_bkn_revision',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN,
            'keterangan' => 'Perbaikan usulan dari BKN dikirim ke Kepegawaian Universitas',
            'catatan' => 'Perbaikan usulan dari BKN berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.show', $usulan)
            ->with('success', 'Perbaikan usulan dari BKN berhasil dikirim ke Kepegawaian Universitas.');
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
            'Draft Usulan',
            'Usulan Dikirim ke Kepegawaian Universitas',
            'Usulan Perbaikan Dari Pegawai Ke Kepegawaian Universitas'
        ];

        if (!in_array($usulan->status_usulan, $deletableStatuses) && !is_null($usulan->status_usulan)) {
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
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

            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('success', 'Usulan Kepangkatan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete usulan kepangkatan', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Gagal menghapus usulan. Silakan coba lagi.');
        }
    }

    /**
     * Handle document uploads for usulan kepangkatan
     */
    private function handleDocumentUploads($request, $usulan): array
    {
        $filePaths = [];
        $uploadPath = 'usulan-dokumen/' . $usulan->pegawai->id . '/' . date('Y/m');

        // Document keys for usulan kepangkatan
        $documentKeys = $this->getDocumentKeys($usulan);

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

                    Log::info("Document uploaded successfully", [
                        'document_key' => $key,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'pegawai_id' => $usulan->pegawai->id
                    ]);

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
     * Get document keys for usulan kepangkatan
     */
    private function getDocumentKeys($usulan): array
    {
        $jenisUsulanPangkat = $usulan->data_usulan['jenis_usulan_pangkat'] ?? '';
        
        $baseKeys = [];
        
        if ($jenisUsulanPangkat === 'Dosen PNS') {
            $baseKeys = ['dokumen_ukom_sk_jabatan'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Administrasi') {
            $baseKeys = ['surat_pencantuman_gelar', 'surat_lulus_ujian_dinas'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Fungsional Tertentu') {
            $baseKeys = ['dokumen_uji_kompetensi', 'sk_penyetaraan_ijazah'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Struktural') {
            $baseKeys = ['surat_pelantikan_berita_acara', 'surat_pencantuman_gelar', 'sertifikat_diklat_pim_pkm'];
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
     * Show document for usulan kepangkatan
     */
    public function showDocument(Usulan $usulanKepangkatan, $field)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulanKepangkatan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Get document path from data_usulan
        $dokumenUsulan = $usulanKepangkatan->data_usulan['dokumen_usulan'] ?? [];
        $documentData = $dokumenUsulan[$field] ?? null;

        if (!$documentData || !isset($documentData['path'])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $path = $documentData['path'];

        // Check if file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Return file
        return Storage::disk('local')->response($path);
    }
    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        return 'usulan-kepangkatan';
    }



    /**
     * Get logs for a specific usulan
     */
    public function getLogs(Usulan $usulan)
    {
        // Authorization check
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki akses untuk melihat log usulan ini.');
        }

        try {
            // Get logs for this usulan, ordered by latest first
            $logs = UsulanLog::where('usulan_id', $usulan->id)
                ->with(['dilakukanOleh'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->getActionDescription(),
                        'status_sebelumnya' => $log->status_sebelumnya,
                        'status_baru' => $log->status_baru,
                        'catatan' => $log->catatan,
                        'user_name' => $log->user_name,
                        'created_at' => $log->formatted_date,
                        'relative_time' => $log->relative_time,
                        'status_badge_class' => $log->status_badge_class,
                        'status_icon' => $log->status_icon,
                        'is_status_change' => $log->isStatusChange(),
                        'is_initial_log' => $log->isInitialLog(),
                    ];
                });

            Log::info('Logs retrieved successfully', [
                'usulan_id' => $usulan->id,
                'total_logs' => $logs->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'total' => $logs->count()
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to retrieve logs', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat log aktivitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

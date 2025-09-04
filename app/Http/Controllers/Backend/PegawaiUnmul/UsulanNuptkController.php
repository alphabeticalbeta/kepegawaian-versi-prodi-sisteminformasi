<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\UsulanLog;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsulanNuptkController extends Controller
{
    /**
     * Display a listing of usulan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Get periode usulan yang sesuai dengan status kepegawaian
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Validasi akses berdasarkan periode usulan yang tersedia
        if ($periodeUsulans->count() == 0) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
        }


        // Get usulan yang sudah dibuat oleh pegawai
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', $jenisUsulanPeriode)
                          ->with(['periodeUsulan'])
                          ->get();


        // Get status kepegawaian dari pegawai
        $statusKepegawaian = $pegawai->status_kepegawaian ?? null;

        // Get jenis NUPTK yang tersedia
        $availableNuptkTypes = $this->getAvailableNuptkTypes($pegawai);

        return view('backend.layouts.views.pegawai-unmul.usulan-nuptk.index', compact('periodeUsulans', 'usulans', 'pegawai', 'statusKepegawaian', 'availableNuptkTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawai = Auth::user();

        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Get periode usulan yang tersedia
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Validasi akses berdasarkan periode usulan yang tersedia
        if ($periodeUsulans->count() == 0) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
        }

        // Get jenis NUPTK yang tersedia
        $availableNuptkTypes = $this->getAvailableNuptkTypes($pegawai);

        return view('backend.layouts.views.pegawai-unmul.usulan-nuptk.create-nuptk', compact('periodeUsulans', 'pegawai', 'availableNuptkTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $pegawai = Auth::user();

            $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

            // Validasi akses berdasarkan periode usulan yang tersedia
            $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
                ->get();

            if ($periodeUsulans->count() == 0) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
            }

            // Validate request
            $request->validate([
                'periode_usulan_id' => 'required|exists:periode_usulans,id',
                'jenis_nuptk' => 'required|in:dosen_tetap,dosen_tidak_tetap,pengajar_non_dosen,tenaga_kependidikan',
                'dokumen_usulan.*' => 'nullable|file|mimes:pdf|max:1024'
            ]);

            // Check if periode is still open
            $periodeUsulan = PeriodeUsulan::findOrFail($request->periode_usulan_id);
            if ($periodeUsulan->status !== 'Buka') {
                return redirect()->back()->with('error', 'Periode usulan sudah ditutup.');
            }

            // Validasi jenis NUPTK sesuai status kepegawaian
            $availableTypes = $this->getAvailableNuptkTypes($pegawai);
            if (!array_key_exists($request->jenis_nuptk, $availableTypes)) {
                return redirect()->back()
                    ->with('error', 'Jenis NUPTK yang dipilih tidak sesuai dengan status kepegawaian Anda.')
                    ->withInput();
            }

            // Check if user already has usulan for this periode
            $existingUsulan = $pegawai->usulans()
                ->where('periode_usulan_id', $request->periode_usulan_id)
                ->where('jenis_usulan', $jenisUsulanPeriode)
                ->first();

            if ($existingUsulan) {
                return redirect()->route('pegawai-unmul.usulan-nuptk.show', $existingUsulan->id)
                    ->with('info', 'Anda sudah memiliki usulan untuk periode ini.');
            }

            // Create usulan
            $usulan = Usulan::create([
                'pegawai_id' => $pegawai->id,
                'periode_usulan_id' => $request->periode_usulan_id,
                'jenis_usulan' => $jenisUsulanPeriode,
                'jenis_nuptk' => $request->jenis_nuptk,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'status_usulan' => Usulan::STATUS_DRAFT_USULAN,
                'data_usulan' => $request->data_usulan ?? [],
                'validasi_data' => [],
                'catatan_verifikator' => null
            ]);

            // Handle dokumen upload if any
            if ($request->hasFile('dokumen_usulan')) {
                $this->handleDokumenUpload($usulan, $request->file('dokumen_usulan'), $pegawai);
            }

            // Create log
            UsulanLog::create([
                'usulan_id' => $usulan->id,
                'status_sebelumnya' => null,
                'status_baru' => Usulan::STATUS_DRAFT_USULAN,
                'catatan' => 'Usulan NUPTK dibuat',
                'dilakukan_oleh_id' => $pegawai->id
            ]);

            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan->id)
                ->with('success', 'Usulan NUPTK berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error creating usulan NUPTK', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membuat usulan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        $pegawai = Auth::user();

        // Validasi akses berdasarkan periode usulan yang tersedia
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->get();

        if ($periodeUsulans->count() == 0) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
        }

        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        // Pastikan usulan adalah NUPTK
        if ($usulan->jenis_usulan !== $this->determineJenisUsulanPeriode($pegawai)) {
            abort(404, 'Usulan tidak ditemukan.');
        }

        // Load relasi yang diperlukan
        $usulan->load([
            'pegawai.unitKerja',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'periodeUsulan'
        ]);

        // Selalu gunakan view create-nuptk untuk konsistensi dengan kepangkatan
        return view('backend.layouts.views.pegawai-unmul.usulan-nuptk.create-nuptk', compact('usulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan $usulan)
    {
        $pegawai = Auth::user();

        // Validasi akses berdasarkan periode usulan yang tersedia
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->get();

        if ($periodeUsulans->count() == 0) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
        }

        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        // Pastikan usulan adalah NUPTK
        if ($usulan->jenis_usulan !== $this->determineJenisUsulanPeriode($pegawai)) {
            abort(404, 'Usulan tidak ditemukan.');
        }

        // Pastikan usulan masih bisa diedit
        if (!$usulan->can_edit) {
            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('error', 'Usulan tidak dapat diedit pada status saat ini.');
        }

        $pegawai = Auth::user();
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Get periode usulan yang tersedia
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Get jenis NUPTK yang tersedia
        $availableNuptkTypes = $this->getAvailableNuptkTypes($pegawai);

        return view('backend.layouts.views.pegawai-unmul.usulan-nuptk.edit', compact('usulan', 'periodeUsulans', 'pegawai', 'availableNuptkTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usulan $usulan)
    {
        try {
            $pegawai = Auth::user();

            // Validasi akses berdasarkan periode usulan yang tersedia
            $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
            $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
                ->get();

            if ($periodeUsulans->count() == 0) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
            }

            // Pastikan usulan milik user yang sedang login
            if ($usulan->pegawai_id !== $pegawai->id) {
                abort(403);
            }

            // Pastikan usulan adalah NUPTK
            if ($usulan->jenis_usulan !== $this->determineJenisUsulanPeriode($pegawai)) {
                abort(404, 'Usulan tidak ditemukan.');
            }

            // Handle different actions
            $action = $request->input('action');
            
            if ($action === 'kirim_ke_kepegawaian') {
                return $this->handleKirimKeKepegawaian($usulan, $pegawai);
            } elseif ($action === 'kirim_perbaikan_ke_kepegawaian') {
                return $this->handleKirimPerbaikanKeKepegawaian($usulan, $pegawai);
            } elseif ($action === 'kirim_perbaikan_tim_sister_ke_kepegawaian') {
                return $this->handleKirimPerbaikanTimSisterKeKepegawaian($usulan, $pegawai);
            } elseif ($action === 'kirim_perbaikan_ke_tim_sister') {
                return $this->handleKirimPerbaikanKeTimSister($usulan, $pegawai);
            }

            // Pastikan usulan masih bisa diedit
            if (!$usulan->can_edit) {
                return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                    ->with('error', 'Usulan tidak dapat diedit pada status saat ini.');
            }

            // Validasi jenis NUPTK sesuai status kepegawaian (hanya jika bukan action)
            if (!$action) {
                $availableTypes = $this->getAvailableNuptkTypes($pegawai);
                if (!array_key_exists($request->jenis_nuptk, $availableTypes)) {
                    return redirect()->back()
                        ->with('error', 'Jenis NUPTK yang dipilih tidak sesuai dengan status kepegawaian Anda.')
                        ->withInput();
                }

                // Validate request
                $request->validate([
                    'jenis_nuptk' => 'required|in:dosen_tetap,dosen_tidak_tetap,pengajar_non_dosen,tenaga_kependidikan',
                    'data_usulan' => 'required|array',
                    'dokumen_usulan.*' => 'nullable|file|mimes:pdf|max:1024'
                ]);
            }

            // Update usulan (hanya jika bukan action)
            if (!$action) {
                $usulan->update([
                    'jenis_nuptk' => $request->jenis_nuptk,
                    'data_usulan' => $request->data_usulan
                ]);

                // Handle dokumen upload if any
                if ($request->hasFile('dokumen_usulan')) {
                    $this->handleDokumenUpload($usulan, $request->file('dokumen_usulan'), Auth::user());
                }

                // Create log
                UsulanLog::create([
                    'usulan_id' => $usulan->id,
                    'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
                    'status_baru' => $usulan->status_usulan,
                    'catatan' => 'Usulan NUPTK diperbarui',
                    'dilakukan_oleh_id' => Auth::id()
                ]);

                return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                    ->with('success', 'Usulan NUPTK berhasil diperbarui.');
            }

        } catch (\Exception $e) {
            Log::error('Error updating usulan NUPTK', [
                'user_id' => Auth::id(),
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui usulan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan $usulan)
    {
        try {
            $pegawai = Auth::user();

            // Validasi akses berdasarkan periode usulan yang tersedia
            $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
            $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
                ->get();

            if ($periodeUsulans->count() == 0) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
            }

            // Pastikan usulan milik user yang sedang login
            if ($usulan->pegawai_id !== $pegawai->id) {
                abort(403);
            }

            // Pastikan usulan adalah NUPTK
            if ($usulan->jenis_usulan !== $this->determineJenisUsulanPeriode($pegawai)) {
                abort(404, 'Usulan tidak ditemukan.');
            }

            // Pastikan usulan masih bisa dihapus (draft status)
            if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN) {
                return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                    ->with('error', 'Usulan tidak dapat dihapus pada status saat ini.');
            }

            // Delete related documents
            $usulan->dokumens()->delete();

            // Delete related logs
            $usulan->logs()->delete();

            // Delete usulan
            $usulan->delete();

            return redirect()->route('pegawai-unmul.usulan-nuptk.index')
                ->with('success', 'Usulan NUPTK berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting usulan NUPTK', [
                'user_id' => Auth::id(),
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus usulan: ' . $e->getMessage());
        }
    }

    /**
     * Submit usulan to Kepegawaian Universitas
     */
    public function submitToKepegawaian(Usulan $usulan)
    {
        try {
            $pegawai = Auth::user();

            // Validasi akses berdasarkan periode usulan yang tersedia
            $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
            $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
                ->get();

            if ($periodeUsulans->count() == 0) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
            }

            // Pastikan usulan milik user yang sedang login
            if ($usulan->pegawai_id !== $pegawai->id) {
                abort(403);
            }

            // Pastikan usulan adalah NUPTK
            if ($usulan->jenis_usulan !== $this->determineJenisUsulanPeriode($pegawai)) {
                abort(404, 'Usulan tidak ditemukan.');
            }

            // Pastikan usulan masih draft
            if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN) {
                return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                    ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
            }

            // Update status
            $usulan->update([
                'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
            ]);

            // Create log
            UsulanLog::create([
                'usulan_id' => $usulan->id,
                'status_sebelumnya' => Usulan::STATUS_DRAFT_USULAN,
                'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                'catatan' => 'Usulan NUPTK dikirim ke Kepegawaian Universitas',
                'dilakukan_oleh_id' => Auth::id()
            ]);

            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('success', 'Usulan NUPTK berhasil dikirim ke Kepegawaian Universitas.');

        } catch (\Exception $e) {
            Log::error('Error submitting usulan NUPTK', [
                'user_id' => Auth::id(),
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim usulan: ' . $e->getMessage());
        }
    }

    /**
     * Handle dokumen upload
     */
    private function handleDokumenUpload($usulan, $files, $pegawai)
    {
        foreach ($files as $field => $file) {
            if ($file && $file->isValid()) {
                // Validate file
                $this->validateUploadedFile($file, $field);

                // Generate unique filename
                $filename = time() . '_' . $field . '_' . $usulan->id . '.pdf';
                $path = 'dokumen/usulan/' . $usulan->id . '/' . $filename;

                // Store file
                Storage::disk('local')->put($path, file_get_contents($file));

                // Save to database
                UsulanDokumen::create([
                    'usulan_id' => $usulan->id,
                    'diupload_oleh_id' => $pegawai->id,
                    'nama_dokumen' => $field,
                    'path' => $path,
                ]);

                // Update data_usulan
                $dataUsulan = $usulan->data_usulan;
                $dataUsulan['dokumen_usulan'][$field] = [
                    'path' => $path,
                    'filename' => $filename,
                    'uploaded_at' => now()->toISOString()
                ];
                $usulan->update(['data_usulan' => $dataUsulan]);
            }
        }
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
     * Show document for usulan NUPTK
     */
    public function showDocument(Usulan $usulan, $field)
    {
        $pegawai = Auth::user();

        // Validasi akses berdasarkan periode usulan yang tersedia
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->get();

        if ($periodeUsulans->count() == 0) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Maaf, status kepegawaian Anda tidak memiliki akses ke halaman ini.');
        }

        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== $pegawai->id) {
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
     * Get jenis NUPTK yang tersedia berdasarkan periode usulan yang tersedia
     */
    protected function getAvailableNuptkTypes($pegawai): array
    {
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);
        
        // Get periode usulan yang tersedia untuk status kepegawaian ini
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->get();

        if ($periodeUsulans->count() == 0) {
            return [];
        }

        // Berdasarkan status kepegawaian, tentukan jenis NUPTK yang tersedia
        if (in_array($pegawai->status_kepegawaian, ['Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN'])) {
            // Jenis NUPTK untuk Dosen
            return [
                'dosen_tetap' => 'Dosen Tetap',
                'dosen_tidak_tetap' => 'Dosen Tidak Tetap',
                'pengajar_non_dosen' => 'Pengajar Non Dosen'
            ];
        } elseif ($pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
            // Jenis NUPTK untuk Tenaga Kependidikan
            return [
                'tenaga_kependidikan' => 'Tenaga Kependidikan'
            ];
        }

        return [];
    }

    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        return 'usulan-nuptk';
    }

    /**
     * Handle action kirim ke kepegawaian
     */
    protected function handleKirimKeKepegawaian(Usulan $usulan, $pegawai)
    {
        // Pastikan usulan masih draft
        if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN) {
            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Update status
        $usulan->update([
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_DRAFT_USULAN,
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'catatan' => 'Usulan NUPTK dikirim ke Kepegawaian Universitas',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
            ->with('success', 'Usulan NUPTK berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Handle action kirim perbaikan ke kepegawaian
     */
    protected function handleKirimPerbaikanKeKepegawaian(Usulan $usulan, $pegawai)
    {
        // Pastikan usulan dalam status perbaikan
        if ($usulan->status_usulan !== Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS) {
            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Update status
        $usulan->update([
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'catatan' => 'Usulan perbaikan NUPTK dikirim ke Kepegawaian Universitas',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
            ->with('success', 'Usulan perbaikan NUPTK berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Handle action kirim perbaikan tim sister ke kepegawaian
     */
    protected function handleKirimPerbaikanTimSisterKeKepegawaian(Usulan $usulan, $pegawai)
    {
        // Pastikan usulan dalam status perbaikan dari tim sister
        if ($usulan->status_usulan !== Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) {
            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Update status
        $usulan->update([
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER,
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'catatan' => 'Usulan perbaikan dari Tim Sister dikirim ke Kepegawaian Universitas',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
            ->with('success', 'Usulan perbaikan dari Tim Sister berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Handle action kirim perbaikan ke tim sister
     */
    protected function handleKirimPerbaikanKeTimSister(Usulan $usulan, $pegawai)
    {
        // Pastikan usulan dalam status perbaikan
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER) {
            return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
                ->with('error', 'Usulan tidak dapat dikirim pada status saat ini.');
        }

        // Update status
        $usulan->update([
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER
        ]);

        // Create log
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER,
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER,
            'catatan' => 'Usulan perbaikan NUPTK dikirim ke Tim Sister',
            'dilakukan_oleh_id' => Auth::id()
        ]);

        return redirect()->route('pegawai-unmul.usulan-nuptk.show', $usulan)
            ->with('success', 'Usulan perbaikan NUPTK berhasil dikirim ke Tim Sister.');
    }
}

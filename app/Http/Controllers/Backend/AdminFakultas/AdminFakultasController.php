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

class AdminFakultasController extends Controller
{
    /**
    * Menampilkan dashboard dengan daftar usulan untuk fakultas terkait.
    */
    public function dashboard()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();
        $unitKerjaId = $admin->unit_kerja_id;

        // Jika admin tidak terhubung ke fakultas, kembalikan view kosong.
        if (!$unitKerjaId) {
            return view('backend.layouts.admin-fakultas.dashboard', [
                'periodeUsulans' => collect()
            ]);
        }

        $periodeUsulans = PeriodeUsulan::query()
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

        return view('backend.layouts.admin-fakultas.dashboard', compact('periodeUsulans'));
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
            return view('backend.layouts.admin-fakultas.usulan-jabatan.index', [
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
     */
    public function show(Usulan $usulan)
    {
        try {
            /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
            $admin = Auth::user();

            // =================================================================
            // PERBAIKAN FINAL: Gunakan 'unit_kerja_id' sesuai instruksi Anda
            // =================================================================
            $adminFakultasId = $admin->unit_kerja_id;

            // Mengambil ID Fakultas dari pegawai pengusul dengan aman
            $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

            // Jika admin tidak punya fakultas atau fakultas tidak cocok, tolak akses
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

            // Eager loading untuk mengambil semua relasi yang dibutuhkan
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

            // Dapatkan field-field yang perlu divalidasi
            $validationFields = Usulan::getValidationFields();

            // Dapatkan data validasi yang sudah ada (jika ada)
            $existingValidation = $usulan->getValidasiByRole('admin_fakultas');

            // Mengirim data usulan yang sudah lengkap ke view
            return view('backend.layouts.shared.usulan-detail.usulan-detail', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
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
                        'validation' => 'required|array', // Pastikan data validasi utama juga dikirim
                        'nomor_surat_usulan' => 'required|string|max:255',
                        'file_surat_usulan' => 'required|file|mimes:pdf|max:2048',
                        'nomor_berita_senat' => 'required|string|max:255',
                        'file_berita_senat' => 'required|file|mimes:pdf|max:5120',
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

}

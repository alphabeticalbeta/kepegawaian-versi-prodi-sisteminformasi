<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan; // Pastikan ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\DB;   // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PusatUsulanController extends Controller
{
    public function index()
    {
        $periodeUsulans = PeriodeUsulan::withCount('usulans')
                                       ->latest()
                                       ->paginate(10);

        return view('backend.layouts.admin-univ-usulan.pusat-usulan.index', [
            'periodeUsulans' => $periodeUsulans
        ]);
    }

    public function showPendaftar(PeriodeUsulan $periodeUsulan)
    {
        $usulans = $periodeUsulan->usulans()
                                ->with('pegawai', 'jabatanLama', 'jabatanTujuan')
                                ->latest()
                                ->paginate(15);

        return view('backend.layouts.admin-univ-usulan.pusat-usulan.show-pendaftar', [
            'periode' => $periodeUsulan,
            'usulans' => $usulans,
        ]);
    }

    public function show(Usulan $usulan)
    {
        // Eager load semua relasi yang dibutuhkan untuk halaman detail
        $usulan->load([
            'pegawai.pangkat',
            'pegawai.jabatan',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'jabatanLama',
            'jabatanTujuan',
            'periodeUsulan',
            'dokumens',
            'logs' => function ($query) {
                $query->with('dilakukanOleh')->latest();
            }
        ]);

        // UPDATED: Pass usulan object and role to get dynamic BKD fields
        $validationFields = Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');

        // ADDED: Get BKD labels for display
        $bkdLabels = $usulan->getBkdDisplayLabels();

        // Get existing validation data if any
        $existingValidation = $usulan->getValidasiByRole('admin_universitas');

        // Determine if can edit based on status
        $canEdit = in_array($usulan->status_usulan, [
            'Diusulkan ke Universitas',
            'Sedang Direview Universitas',
        ]);

        // Return view dengan data yang diperlukan
        return view('backend.layouts.admin-univ-usulan.pusat-usulan.detail-usulan', [
            'usulan' => $usulan,
            'canEdit' => $canEdit,
            'validationFields' => $validationFields,
            'existingValidation' => $existingValidation,
            'canEdit' => $canEdit,
            'bkdLabels' => $bkdLabels,
        ]);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        // 1. Validasi field yang diizinkan
        $allowedFields = [
            'pakta_integritas',
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'bukti_syarat_guru_besar',
            // BKD documents (dynamic names)
            'bkd_ganjil_2024_2025',
            'bkd_genap_2023_2024',
            'bkd_ganjil_2023_2024',
            'bkd_genap_2022_2023',
        ];

        // Check if field is BKD document (starts with 'bkd_')
        $isBkdDocument = str_starts_with($field, 'bkd_');
        if (!$isBkdDocument && !in_array($field, $allowedFields, true)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // 2. Get file path from usulan data using model method
        $filePath = $usulan->getDocumentPath($field);

        // 3. Fallback: jika field adalah bkd_semester_N tapi path kosong, map ke key legacy & scan
        if (!$filePath && str_starts_with($field, 'bkd_semester_')) {
            // Bangun label target dengan method model
            $labels = $usulan->getBkdDisplayLabels();
            if (isset($labels[$field])) {
                if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $labels[$field], $m)) {
                    $sem = strtolower($m[1]);
                    $y1  = $m[2];
                    $y2  = $m[3];

                    // a) Coba exact legacy key
                    $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;
                    $filePath = $usulan->getDocumentPath($legacyKey);

                    // b) Scan semua key BKD jika masih kosong
                    if (!$filePath && !empty($usulan->data_usulan['dokumen_usulan'])) {
                        foreach ($usulan->data_usulan['dokumen_usulan'] as $k => $info) {
                            if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                    $filePath = is_array($info) ? ($info['path'] ?? null) : $info;
                                    break;
                                }
                            }
                        }
                    }
                    if (!$filePath && !empty($usulan->data_usulan)) {
                        foreach ($usulan->data_usulan as $k => $info) {
                            if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                    $filePath = is_array($info) ? ($info['path'] ?? null) : $info;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$filePath) {
            Log::warning('Document path not found in data_usulan', [
                'usulan_id' => $usulan->id,
                'field' => $field,
                'data_usulan_keys' => array_keys($usulan->data_usulan ?? [])
            ]);
            abort(404, 'Path dokumen tidak ditemukan dalam data usulan.');
        }

        // 3. Check if file exists in storage
        if (!Storage::disk('local')->exists($filePath)) {
            Log::error('Document file not found in storage', [
                'usulan_id' => $usulan->id,
                'field' => $field,
                'path' => $filePath,
                'full_path' => Storage::disk('local')->path($filePath)
            ]);
            abort(404, 'File dokumen tidak ditemukan di storage.');
        }

        // 4. Log document access
        Log::info('Document accessed', [
            'usulan_id' => $usulan->id,
            'field' => $field,
            'accessed_by' => Auth::id(),
            'user_role' => Auth::user()->getRoleNames()->first(),
            'file_path' => $filePath
        ]);

        // 5. Get full path and serve file
        $fullPath = Storage::disk('local')->path($filePath);

        // Determine mime type
        $mimeType = 'application/pdf'; // Default for PDF
        if (str_ends_with($filePath, '.pdf')) {
            $mimeType = 'application/pdf';
        } elseif (str_ends_with($filePath, '.jpg') || str_ends_with($filePath, '.jpeg')) {
            $mimeType = 'image/jpeg';
        } elseif (str_ends_with($filePath, '.png')) {
            $mimeType = 'image/png';
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function process(Request $request, Usulan $usulan)
    {
        // 1) Guard status
        if (!in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])) {
            return redirect()->back()->with('error', 'Aksi tidak dapat dilakukan karena status usulan saat ini adalah: ' . $usulan->status_usulan);
        }

        // 2) Validasi input (tambahkan action baru)
        $request->validate([
            'action_type'  => 'required|in:save_only,return_to_pegawai,reject_proposal,approve_proposal,recommend_proposal,return_for_revision,not_recommended,send_to_assessor_team,send_to_senate_team',
            'catatan_umum' => 'required_if:action_type,return_to_pegawai,return_for_revision,not_recommended|nullable|string|min:10|max:2000',
            'assessor_ids' => 'required_if:action_type,send_to_assessor_team|array|min:1|max:3',
            'assessor_ids.*' => 'required_if:action_type,send_to_assessor_team|exists:pegawais,id',
        ], [
            'catatan_umum.required_if' => 'Catatan wajib diisi jika Anda mengembalikan usulan ke pegawai.',
            'assessor_ids.required_if' => 'Pilih minimal 1 dan maksimal 3 penilai.',
            'assessor_ids.min' => 'Pilih minimal 1 penilai.',
            'assessor_ids.max' => 'Pilih maksimal 3 penilai.',
            'assessor_ids.*.exists' => 'Penilai yang dipilih tidak valid.',
        ]);

        // 3) Cek prasyarat rekomendasi SENAT & PENILAI lebih dulu (hindari early-return dalam transaksi)
        $actionType = $request->action_type;
        if ($actionType === 'recommend_proposal') {
            $minSetuju = $usulan->getSenateMinSetuju();
            if (!$usulan->isRecommendedByReviewer()) {
                return back()->with('error', 'Belum dapat direkomendasikan: menunggu rekomendasi dari Tim Penilai.');
            }
            if (!$usulan->isSenateApproved($minSetuju)) {
                return back()->with('error', 'Belum dapat direkomendasikan: keputusan Senat belum memenuhi minimal setuju (' . $minSetuju . ').');
            }
        }

        $adminId    = Auth::id();
        $statusLama = $usulan->status_usulan;
        $logMessage = '';

        DB::beginTransaction();
        try {
            // 4) Simpan data validasi by role (pastikan method ini aman)
            if ($request->has('validation')) {
                $usulan->setValidasiByRole('admin_universitas', $request->validation, $adminId);
            }

            // 5) Mutasi status berdasarkan action
            switch ($actionType) {
                case 'return_to_pegawai':
                    $usulan->status_usulan = 'Dikembalikan ke Pegawai';
                    // Pastikan kolom ini ada; jika tidak, pindahkan ke log/validasi
                    $usulan->catatan_verifikator = $request->catatan_umum;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan oleh Admin Universitas.';
                    break;

                case 'return_for_revision':
                    // Langsung ke employee tanpa melalui Faculty Admin
                    $usulan->status_usulan = 'Perlu Perbaikan';
                    $usulan->catatan_verifikator = $request->catatan_umum;
                    $logMessage = 'Usulan dikembalikan langsung ke Pegawai untuk perbaikan oleh Admin Universitas.';
                    break;

                case 'not_recommended':
                    // Return ke employee dan tidak bisa submit lagi di periode tersebut
                    $usulan->status_usulan = 'Tidak Direkomendasikan';
                    $usulan->catatan_verifikator = $request->catatan_umum;
                    $logMessage = 'Usulan tidak direkomendasikan oleh Admin Universitas. Pegawai tidak dapat submit lagi di periode ini.';
                    break;

                case 'send_to_assessor_team':
                    // Kirim ke tim penilai
                    $usulan->status_usulan = 'Sedang Dinilai';
                    
                    // Hapus penilai lama jika ada
                    $usulan->penilais()->detach();
                    
                    // Tambah penilai baru
                    $assessorIds = $request->assessor_ids;
                    $assessorData = [];
                    foreach ($assessorIds as $assessorId) {
                        $assessorData[$assessorId] = [
                            'status_penilaian' => 'Belum Dinilai',
                            'catatan_penilaian' => null,
                        ];
                    }
                    $usulan->penilais()->attach($assessorData);
                    
                    $logMessage = 'Usulan dikirim ke Tim Penilai (' . count($assessorIds) . ' penilai).';
                    break;

                case 'send_to_senate_team':
                    // Kirim ke tim senat (hanya jika penilai sudah memberikan rekomendasi)
                    if (!$usulan->isRecommendedByReviewer()) {
                        return back()->with('error', 'Belum dapat dikirim ke Tim Senat: menunggu rekomendasi dari Tim Penilai.');
                    }
                    
                    $usulan->status_usulan = 'Sedang Direview Senat';
                    $logMessage = 'Usulan dikirim ke Tim Senat untuk review.';
                    break;

                case 'reject_proposal':
                    $usulan->status_usulan = 'Ditolak Universitas';
                    $logMessage = 'Usulan ditolak oleh Admin Universitas. Proses dihentikan.';
                    break;

                case 'approve_proposal':
                    $usulan->status_usulan = 'Direkomendasikan'; // atau 'Disetujui Universitas' sesuai kebijakanmu
                    $logMessage = 'Usulan disetujui dan direkomendasikan oleh Admin Universitas.';
                    break;

                case 'save_only':
                    // Jika masih awal, ubah ke "Sedang Direview Universitas" sesuai komentarmu
                    if ($statusLama === 'Diusulkan ke Universitas') {
                        $usulan->status_usulan = 'Sedang Direview Universitas';
                    }
                    $logMessage = 'Validasi dari Admin Universitas disimpan.';
                    break;

                case 'recommend_proposal':
                    // Sudah lulus prasyarat di atas
                    $usulan->status_usulan = 'Direkomendasikan';
                    $logMessage = 'Usulan direkomendasikan oleh Admin Universitas.';
                    break;
            }

            $usulan->save();

            // 6) Tulis log
            $usulan->createLog($usulan->status_usulan, $statusLama, $logMessage, $adminId);

            DB::commit();

            return redirect()
                ->route('backend.admin-univ-usulan.pusat-usulan.index')
                ->with('success', 'Usulan berhasil diproses.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error processing usulan by Admin Universitas: ' . $e->getMessage(), ['usulan_id' => $usulan->id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses usulan.');
        }
    }

    /**
     * DEBUG METHOD - Analisis struktur data usulan dan link dokumen
     * Hapus setelah debugging selesai
     */
    public function debugDataUsulan($usulanId)
    {
        // Hanya untuk environment local/development
        if (!app()->environment(['local', 'development'])) {
            abort(404);
        }

        $usulan = \App\Models\BackendUnivUsulan\Usulan::findOrFail($usulanId);
        
        // Test helper untuk semua kategori dokumen
        $helper = new \App\Helpers\UsulanFieldHelper($usulan);
        
        $debugData = [
            'basic_info' => [
                'usulan_id' => $usulan->id,
                'pegawai_name' => $usulan->pegawai->nama_lengkap ?? 'N/A',
                'jenis_usulan' => $usulan->jenis_usulan,
                'status_usulan' => $usulan->status_usulan,
                'created_at' => $usulan->created_at->format('Y-m-d H:i:s'),
            ],
            
            'data_usulan_analysis' => [
                'has_data_usulan' => !empty($usulan->data_usulan),
                'main_keys' => array_keys($usulan->data_usulan ?? []),
                'dokumen_usulan_exists' => isset($usulan->data_usulan['dokumen_usulan']),
                'dokumen_usulan_keys' => array_keys($usulan->data_usulan['dokumen_usulan'] ?? []),
            ],
            
            'document_path_tests' => [
                'pakta_integritas' => $usulan->getDocumentPath('pakta_integritas'),
                'bukti_korespondensi' => $usulan->getDocumentPath('bukti_korespondensi'),
                'turnitin' => $usulan->getDocumentPath('turnitin'),
                'upload_artikel' => $usulan->getDocumentPath('upload_artikel'),
                'bkd_semester_1' => $usulan->getDocumentPath('bkd_semester_1'),
                'bkd_ganjil_2024_2025' => $usulan->getDocumentPath('bkd_ganjil_2024_2025'),
            ],
            
            'helper_field_tests' => [],
            'helper_errors' => [],
            
            'bkd_label_tests' => [],
            
            'route_tests' => []
        ];
        
        // Test UsulanFieldHelper untuk kategori dokumen_usulan
        $dokumenUsulanFields = ['pakta_integritas', 'bukti_korespondensi', 'turnitin', 'upload_artikel'];
        foreach ($dokumenUsulanFields as $field) {
            try {
                $debugData['helper_field_tests']['dokumen_usulan'][$field] = $helper->getFieldValue('dokumen_usulan', $field);
            } catch (\Exception $e) {
                $debugData['helper_errors']['dokumen_usulan'][$field] = $e->getMessage();
            }
        }
        
        // Test UsulanFieldHelper untuk kategori dokumen_bkd
        $bkdFields = ['bkd_semester_1', 'bkd_semester_2', 'bkd_semester_3', 'bkd_semester_4'];
        foreach ($bkdFields as $field) {
            try {
                $debugData['helper_field_tests']['dokumen_bkd'][$field] = $helper->getFieldValue('dokumen_bkd', $field);
            } catch (\Exception $e) {
                $debugData['helper_errors']['dokumen_bkd'][$field] = $e->getMessage();
            }
        }
        
        // Test BKD Labels dari model
        try {
            $debugData['bkd_label_tests'] = $usulan->getBkdDisplayLabels();
        } catch (\Exception $e) {
            $debugData['helper_errors']['bkd_labels'] = $e->getMessage();
        }
        
        // Test route generation
        try {
            $debugData['route_tests'] = [
                'pakta_integritas' => route('backend.admin-univ-usulan.pusat-usulan.show-document', [$usulan->id, 'pakta_integritas']),
                'bukti_korespondensi' => route('backend.admin-univ-usulan.pusat-usulan.show-document', [$usulan->id, 'bukti_korespondensi']),
            ];
        } catch (\Exception $e) {
            $debugData['helper_errors']['routes'] = $e->getMessage();
        }
        
        // Detail struktur data_usulan (full dump untuk analisis)
        $debugData['full_data_usulan'] = $usulan->data_usulan;
        
        return response()->json($debugData, 200, [], JSON_PRETTY_PRINT);
}

}

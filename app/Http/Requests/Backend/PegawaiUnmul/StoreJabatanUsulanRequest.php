<?php

namespace App\Http\Requests\Backend\PegawaiUnmul;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\Usulan;


class StoreJabatanUsulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $pegawai = Auth::user();
        $jenisUsulan = $this->determineJenisUsulan($pegawai);

        $rules = [
            // PERIODE USULAN - Keep required
            'periode_usulan_id' => [
                'required',
                'exists:periode_usulans,id',
                function ($attribute, $value, $fail) use ($jenisUsulan) {
                    $periode = PeriodeUsulan::find($value);
                    if (!$periode || $periode->status !== 'Buka') {
                        $fail('Periode usulan tidak valid atau sudah tidak aktif.');
                    }
                    if ($periode->jenis_usulan !== $jenisUsulan) {
                        $fail('Periode usulan tidak sesuai dengan jenis pegawai Anda.');
                    }
                    if ($periode->tanggal_mulai > now() || $periode->tanggal_selesai < now()) {
                        $fail('Periode usulan belum dimulai atau sudah berakhir.');
                    }
                }
            ],

            // KARYA ILMIAH - Make nullable for testing
            'karya_ilmiah' => 'nullable|string|in:Jurnal Nasional Bereputasi,Jurnal Internasional Bereputasi',

            // INFORMASI ARTIKEL - All nullable for testing
            'nama_jurnal' => 'nullable|string|max:500',
            'judul_artikel' => 'nullable|string|max:500',
            'penerbit_artikel' => 'nullable|string|max:255',
            'volume_artikel' => 'nullable|string|max:100',
            'nomor_artikel' => 'nullable|string|max:100',
            'edisi_artikel' => 'nullable|string|max:100',
            'halaman_artikel' => 'nullable|string|max:100',

            // LINKS - All nullable for testing
            'link_artikel' => 'nullable|url|max:500',
            'link_sinta' => 'nullable|url|max:500',
            'link_scopus' => 'nullable|url|max:500',
            'link_scimago' => 'nullable|url|max:500',
            'link_wos' => 'nullable|url|max:500',

            // DOKUMEN - All nullable for testing
            'pakta_integritas' => 'nullable|file|mimes:pdf|max:1024',
            'bukti_korespondensi' => 'nullable|file|mimes:pdf|max:1024',
            'turnitin' => 'nullable|file|mimes:pdf|max:1024',
            'upload_artikel' => 'nullable|file|mimes:pdf|max:1024',

            // SYARAT GURU BESAR - All nullable for testing
            'syarat_guru_besar' => 'nullable|string|in:hibah,bimbingan,pengujian,reviewer',
            'bukti_syarat_guru_besar' => 'nullable|file|mimes:pdf|max:2048',

            // BKD SEMESTER - All nullable for testing
            'bkd_semester_1' => 'nullable|file|mimes:pdf|max:2048',
            'bkd_semester_2' => 'nullable|file|mimes:pdf|max:2048',
            'bkd_semester_3' => 'nullable|file|mimes:pdf|max:2048',
            'bkd_semester_4' => 'nullable|file|mimes:pdf|max:2048',

            // CATATAN - Nullable for testing
            'catatan' => 'nullable|string|max:1000',

            // ACTION - Keep required
            'action' => 'required|string|in:save_draft,submit,submit_to_university,submit_to_fakultas',
        ];

        return $rules;
    }

    /**
     * Validasi setelah rules dasar (untuk logic yang kompleks)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // FIXED: Route parameter name - changed from 'usulanJabatan' to 'usulan'
            // Cek usulan aktif hanya untuk create (bukan update)
            if (!$this->route('usulan')) {
                $pegawai = Auth::user();
                if ($pegawai) {
                    $jenisUsulan = $this->determineJenisUsulan($pegawai);

                    $usulanAktif = Usulan::where('pegawai_id', $pegawai->id)
                        ->where('jenis_usulan', $jenisUsulan)
                        ->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak'])
                        ->exists();

                    if ($usulanAktif) {
                        $validator->errors()->add('general', 'Anda masih memiliki usulan jabatan yang sedang aktif.');
                    }
                }
            }

            // ADDED: Enhanced jenjang-specific validation
            $this->validateJenjangSpecificRules($validator);
        });
    }

    /**
     * ADDED: Jenjang-specific validation rules
     */
    protected function validateJenjangSpecificRules($validator)
    {
        $pegawai = Auth::user();
        $jenjangType = $this->determineJenjangType($pegawai);

        // Validation untuk Guru Besar
        if ($jenjangType === 'lektor-kepala-to-guru-besar') {
            // Karya ilmiah wajib untuk Guru Besar
            if (!$this->filled('karya_ilmiah')) {
                $validator->errors()->add('karya_ilmiah', 'Karya ilmiah wajib untuk pengajuan Guru Besar.');
            }

            if ($this->karya_ilmiah && $this->karya_ilmiah !== 'Jurnal Internasional Bereputasi') {
                $validator->errors()->add('karya_ilmiah',
                    'Untuk pengajuan Guru Besar, karya ilmiah harus berupa Jurnal Internasional Bereputasi.');
            }

            if ($this->filled('karya_ilmiah') && !$this->filled('link_scopus')) {
                $validator->errors()->add('link_scopus',
                    'Link SCOPUS wajib untuk pengajuan Guru Besar.');
            }

            // Syarat khusus wajib untuk Guru Besar
            if (!$this->filled('syarat_guru_besar')) {
                $validator->errors()->add('syarat_guru_besar',
                    'Syarat khusus guru besar harus dipilih.');
            }
        }

        // Validation untuk jenjang lain yang memerlukan karya ilmiah
        if (in_array($jenjangType, ['asisten-ahli-to-lektor', 'lektor-to-lektor-kepala'])) {
            if (!$this->filled('karya_ilmiah')) {
                $validator->errors()->add('karya_ilmiah', 'Karya ilmiah wajib untuk jenjang ini.');
            }

            if ($this->filled('karya_ilmiah')) {
                if (!$this->filled('nama_jurnal')) {
                    $validator->errors()->add('nama_jurnal', 'Nama jurnal harus diisi.');
                }
                if (!$this->filled('judul_artikel')) {
                    $validator->errors()->add('judul_artikel', 'Judul artikel harus diisi.');
                }
                if (!$this->filled('link_artikel')) {
                    $validator->errors()->add('link_artikel', 'Link artikel harus diisi.');
                }
            }
        }

        // Validation untuk Tenaga Pengajar (optional karya ilmiah)
        if ($jenjangType === 'tenaga-pengajar-to-asisten-ahli') {
            // Karya ilmiah optional, tapi jika diisi harus lengkap
            if ($this->filled('karya_ilmiah')) {
                if (!$this->filled('nama_jurnal')) {
                    $validator->errors()->add('nama_jurnal',
                        'Nama jurnal harus diisi jika jenis karya ilmiah dipilih.');
                }
                if (!$this->filled('link_artikel')) {
                    $validator->errors()->add('link_artikel',
                        'Link artikel harus diisi jika jenis karya ilmiah dipilih.');
                }
            }
        }
    }

    /**
     * ADDED: Determine jenjang type for specific validation
     */
    protected function determineJenjangType($pegawai): string
    {
        // Untuk Tenaga Kependidikan
        if ($pegawai->jenis_pegawai === 'Tenaga Kependidikan') {
            return 'tenaga-kependidikan';
        }

        // Untuk Dosen - berdasarkan jabatan tujuan
        $jabatanLama = $pegawai->jabatan;
        if (!$jabatanLama) {
            return 'unknown';
        }

        $jabatanTujuan = \App\Models\KepegawaianUniversitas\Jabatan::where('jenis_pegawai', $pegawai->jenis_pegawai)
                      ->where('jenis_jabatan', $jabatanLama->jenis_jabatan)
                      ->where('id', '>', $jabatanLama->id)
                      ->orderBy('id', 'asc')
                      ->first();

        if (!$jabatanTujuan) {
            return 'unknown';
        }

        return match($jabatanTujuan->jabatan) {
            'Asisten Ahli' => 'tenaga-pengajar-to-asisten-ahli',
            'Lektor' => 'asisten-ahli-to-lektor',
            'Lektor Kepala' => 'lektor-to-lektor-kepala',
            'Guru Besar' => 'lektor-kepala-to-guru-besar',
            default => 'unknown'
        };
    }

    private function determineJenisUsulan($pegawai): string
    {
        if ($pegawai->jenis_pegawai === 'Dosen' && $pegawai->status_kepegawaian === 'Dosen PNS') {
            return 'Usulan Jabatan';
        } elseif ($pegawai->jenis_pegawai === 'Tenaga Kependidikan' && $pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
            return 'Usulan Jabatan';
        }
        return 'Usulan Jabatan'; // Fallback
    }

    private function getFileValidation(string $fieldName, bool $alwaysRequired = false): string
    {
        // Cek apakah ini proses update dengan mengambil data usulan dari route
        $usulan = $this->route('usulan');
        $isUpdating = $usulan !== null;

        // Ambil persyaratan dokumen berdasarkan jenjang jabatan yang dituju
        $pegawai = Auth::user();
        $jenjangType = $this->determineJenjangType($pegawai);
        $documentRequirements = $this->getDocumentRequirements($jenjangType);
        $isDocumentRequiredForJenjang = $documentRequirements[$fieldName] ?? false;

        // Aturan dasar: file harus berupa PDF dengan ukuran maksimal 1MB.
        // Kita akan menambahkan 'required' atau 'nullable' secara dinamis.
        $rules = ['file', 'mimes:pdf', 'max:1024'];

        // 1. Logika untuk file yang SELALU WAJIB (seperti Pakta Integritas)
        if ($alwaysRequired) {
            // Jika sedang update dan file sudah ada, maka tidak wajib upload ulang.
            if ($isUpdating && (!empty($usulan->data_usulan['dokumen_usulan'][$fieldName]['path']) || !empty($usulan->data_usulan[$fieldName]))) {
                array_unshift($rules, 'nullable'); // Tambahkan 'nullable' di awal
            } else {
                array_unshift($rules, 'required'); // Jika tidak, maka wajib.
            }
            return implode('|', $rules);
        }

        // 2. Logika untuk file yang WAJIB BERDASARKAN JENJANG (misal: Turnitin untuk Guru Besar)
        if ($isDocumentRequiredForJenjang) {
            // Khusus untuk 'bukti_syarat_guru_besar', hanya wajib jika syaratnya dipilih.
            if ($fieldName === 'bukti_syarat_guru_besar' && !$this->filled('syarat_guru_besar')) {
                array_unshift($rules, 'nullable');
                return implode('|', $rules);
            }

            // Jika sedang update dan file sudah ada, maka tidak wajib upload ulang.
            if ($isUpdating && (!empty($usulan->data_usulan['dokumen_usulan'][$fieldName]['path']) || !empty($usulan->data_usulan[$fieldName]))) {
                array_unshift($rules, 'nullable');
            } else {
                // Jika ini proses 'create', atau 'update' tapi filenya belum ada, maka WAJIB.
                array_unshift($rules, 'required');
            }
            return implode('|', $rules);
        }

        // 3. Jika file tidak masuk kategori di atas, maka tidak wajib (nullable).
        array_unshift($rules, 'nullable');
        return implode('|', $rules);
    }

    /**
     * ADDED: Get document requirements per jenjang
     */
    protected function getDocumentRequirements(string $jenjangType): array
    {
        $requirements = [
            'tenaga-pengajar-to-asisten-ahli' => [
                'pakta_integritas' => true,
                'bukti_korespondensi' => false,
                'turnitin' => false,
                'upload_artikel' => false,
                'bukti_syarat_guru_besar' => false,
            ],
            'asisten-ahli-to-lektor' => [
                'pakta_integritas' => true,
                'bukti_korespondensi' => true,
                'turnitin' => true,
                'upload_artikel' => true,
                'bukti_syarat_guru_besar' => false,
            ],
            'lektor-to-lektor-kepala' => [
                'pakta_integritas' => true,
                'bukti_korespondensi' => true,
                'turnitin' => true,
                'upload_artikel' => true,
                'bukti_syarat_guru_besar' => false,
            ],
            'lektor-kepala-to-guru-besar' => [
                'pakta_integritas' => true,
                'bukti_korespondensi' => true,
                'turnitin' => true,
                'upload_artikel' => true,
                'bukti_syarat_guru_besar' => true,
            ],
            'tenaga-kependidikan' => [
                'pakta_integritas' => true,
                'bukti_korespondensi' => false,
                'turnitin' => false,
                'upload_artikel' => false,
                'bukti_syarat_guru_besar' => false,
            ],
        ];

        return $requirements[$jenjangType] ?? $requirements['tenaga-pengajar-to-asisten-ahli'];
    }

    public function messages(): array
    {
        return [
            'periode_usulan_id.required' => 'Periode usulan harus dipilih.',
            'periode_usulan_id.exists' => 'Periode usulan tidak valid.',

            'karya_ilmiah.required' => 'Jenis karya ilmiah harus dipilih.',
            'karya_ilmiah.in' => 'Jenis karya ilmiah tidak valid.',

            'nama_jurnal.required' => 'Nama jurnal harus diisi.',
            'nama_jurnal.max' => 'Nama jurnal maksimal 500 karakter.',
            'judul_artikel.required' => 'Judul artikel harus diisi.',
            'judul_artikel.max' => 'Judul artikel maksimal 500 karakter.',
            'penerbit_artikel.max' => 'Penerbit artikel maksimal 255 karakter.',
            'volume_artikel.max' => 'Volume artikel maksimal 100 karakter.',
            'nomor_artikel.max' => 'Nomor artikel maksimal 100 karakter.',
            'edisi_artikel.max' => 'Edisi artikel maksimal 100 karakter.',
            'halaman_artikel.max' => 'Halaman artikel maksimal 100 karakter.',

            'link_artikel.required' => 'Link artikel harus diisi.',
            'link_artikel.url' => 'Format link artikel tidak valid.',
            'link_sinta.url' => 'Format link SINTA tidak valid.',
            'link_scopus.required' => 'Link SCOPUS harus diisi.',
            'link_scopus.url' => 'Format link SCOPUS tidak valid.',
            'link_scimago.url' => 'Format link SCIMAGO tidak valid.',
            'link_wos.url' => 'Format link WoS tidak valid.',

            'pakta_integritas.required' => 'File pakta integritas harus diunggah.',
            'bukti_korespondensi.required' => 'File bukti korespondensi harus diunggah.',
            'turnitin.required' => 'File turnitin harus diunggah.',
            'upload_artikel.required' => 'File artikel harus diunggah.',

            '*.file' => 'File harus berupa file yang valid.',
            '*.mimes' => 'File harus berformat PDF.',
            '*.max' => 'Ukuran file maksimal 1MB.',

            'syarat_guru_besar.required' => 'Syarat guru besar harus dipilih.',
            'syarat_guru_besar.in' => 'Syarat guru besar tidak valid.',
            'bukti_syarat_guru_besar.required' => 'Bukti syarat guru besar harus diunggah.',

            'action.required' => 'Aksi harus dipilih.',
            'action.in' => 'Aksi tidak valid.',

            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}

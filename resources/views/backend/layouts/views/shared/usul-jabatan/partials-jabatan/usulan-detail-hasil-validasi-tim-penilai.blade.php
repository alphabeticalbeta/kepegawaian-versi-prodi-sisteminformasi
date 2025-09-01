{{-- Hasil Validasi Semua Tim Penilai (Khusus Kepegawaian Universitas) --}}
@if($currentRole === 'Kepegawaian Universitas')
    @php
        $allPenilaiInvalidFields = [];
        $allPenilaiGeneralNotes = [];

        // Ambil data penilai individual dari relasi penilais
        $penilais = $usulan->penilais ?? collect();

        // Ambil semua data validasi individual penilai menggunakan method baru
        $allIndividualPenilaiData = $usulan->getAllValidasiIndividualPenilai();

        // Kumpulkan semua field yang tidak sesuai dari Tim Penilai untuk ditampilkan di tabel
        $allPenilaiInvalidFields = [];

        // Proses data penilai individual
        if ($penilais->count() > 0) {
            foreach ($penilais as $index => $penilai) {
                // Anonymize penilai name for non-Kepegawaian Universitas roles
                if ($currentRole !== 'Kepegawaian Universitas') {
                    $penilaiName = 'Penilai ' . ($index + 1);
                } else {
                    $penilaiName = $penilai->nama_lengkap ?? 'Penilai ' . $penilai->id;
                }
                $penilaiInvalidFields = [];
                $penilaiGeneralNotes = [];

                // Cek apakah penilai sudah memberikan hasil penilaian (multiple conditions)
                $hasAssessment = !empty($penilai->pivot->hasil_penilaian) ||
                                !empty($penilai->pivot->status_penilaian) ||
                                !empty($penilai->pivot->catatan_penilaian) ||
                                $penilai->pivot->status_penilaian !== 'Belum Dinilai';

                if ($hasAssessment) {
                    // Cari data untuk penilai ini menggunakan method baru
                    $penilaiData = collect($allIndividualPenilaiData)->firstWhere('penilai_id', $penilai->id);

                    if ($penilaiData && is_array($penilaiData)) {
                        // Proses field yang tidak sesuai untuk penilai ini
                        $processedFields = 0;
                        foreach ($penilaiData as $groupKey => $groupData) {
                            if (is_array($groupData)) {
                                foreach ($groupData as $fieldKey => $fieldData) {
                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                        $processedFields++;

                                        // PERBAIKAN: Mapping field names yang lebih lengkap untuk semua kategori field
                                        $fieldLabelMap = [
                                            // Dokumen Admin Fakultas
                                            'file_berita_senat' => 'File Berita Senat',
                                            'file_surat_usulan' => 'File Surat Usulan',
                                            'nomor_berita_senat' => 'Nomor Berita Senat',
                                            'nomor_surat_usulan' => 'Nomor Surat Usulan',

                                            // Dokumen Usulan
                                            'turnitin' => 'Dokumen Turnitin',
                                            'upload_artikel' => 'Upload Artikel',
                                            'pakta_integritas' => 'Pakta Integritas',
                                            'bukti_korespondensi' => 'Bukti Korespondensi',

                                            // Dokumen Profil
                                            'sk_pns' => 'SK PNS',
                                            'sk_cpns' => 'SK CPNS',
                                            'ijazah_terakhir' => 'Ijazah Terakhir',
                                            'skp_tahun_pertama' => 'SKP Tahun Pertama',
                                            'skp_tahun_kedua' => 'SKP Tahun Kedua',
                                            'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                                            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                                            'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                                            'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                                            'pak_konversi' => 'PAK Konversi',
                                            'pak_integrasi' => 'PAK Integrasi',
                                            'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',

                                            // Syarat Guru Besar
                                            'syarat_guru_besar' => 'Syarat Guru Besar',
                                            'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar',

                                            // Data Pribadi
                                            'jenis_pegawai' => 'Jenis Pegawai',
                                            'status_kepegawaian' => 'Status Kepegawaian',
                                            'nip' => 'NIP',
                                            'nuptk' => 'NUPTK',
                                            'gelar_depan' => 'Gelar Depan',
                                            'nama_lengkap' => 'Nama Lengkap',
                                            'gelar_belakang' => 'Gelar Belakang',
                                            'email' => 'Email',
                                            'tempat_lahir' => 'Tempat Lahir',
                                            'tanggal_lahir' => 'Tanggal Lahir',
                                            'jenis_kelamin' => 'Jenis Kelamin',
                                            'nomor_handphone' => 'Nomor Handphone',

                                            // Data Kepegawaian
                                            'pangkat_saat_usul' => 'Pangkat',
                                            'tmt_pangkat' => 'TMT Pangkat',
                                            'jabatan_saat_usul' => 'Jabatan',
                                            'tmt_jabatan' => 'TMT Jabatan',
                                            'tmt_cpns' => 'TMT CPNS',
                                            'tmt_pns' => 'TMT PNS',
                                            'unit_kerja_saat_usul' => 'Unit Kerja',

                                            // Data Pendidikan
                                            'pendidikan_terakhir' => 'Pendidikan Terakhir',
                                            'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                                            'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                                            'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                                            'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                                            'url_profil_sinta' => 'Profil SINTA',

                                            // Data Kinerja
                                            'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                                            'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                                            'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1),

                                            // Karya Ilmiah
                                            'jenis_karya' => 'Jenis Karya',
                                            'nama_jurnal' => 'Nama Jurnal',
                                            'judul_artikel' => 'Judul Artikel',
                                            'penerbit_artikel' => 'Penerbit Artikel',
                                            'volume_artikel' => 'Volume Artikel',
                                            'nomor_artikel' => 'Nomor Artikel',
                                            'edisi_artikel' => 'Edisi Artikel (Tahun)',
                                            'halaman_artikel' => 'Halaman Artikel',
                                            'link_artikel' => 'Link Artikel',
                                            'link_sinta' => 'Link SINTA',
                                            'link_scopus' => 'Link SCOPUS',
                                            'link_scimago' => 'Link SCIMAGO',
                                            'link_wos' => 'Link WoS'
                                        ];

                                        // PERBAIKAN: Gunakan field mapping yang lebih lengkap untuk semua kategori field
                                        $fieldLabel = '';

                                        // Cek apakah ada di fieldGroups terlebih dahulu
                                        $groupFields = $fieldGroups[$groupKey]['fields'] ?? [];
                                        if (is_callable($groupFields)) {
                                            $groupFields = $groupFields();
                                        }
                                        if (isset($groupFields[$fieldKey])) {
                                            $fieldLabel = $groupFields[$fieldKey];
                                        }
                                        // Jika tidak ada, gunakan fieldLabelMap
                                        elseif (isset($fieldLabelMap[$fieldKey])) {
                                            $fieldLabel = $fieldLabelMap[$fieldKey];
                                        }
                                        // Fallback ke ucwords
                                        else {
                                            $fieldLabel = ucwords(str_replace('_', ' ', $fieldKey));
                                        }

                                        $penilaiInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                    }
                                }
                            }
                        }

                        // Collect keterangan umum untuk penilai ini
                        if (isset($penilaiData['keterangan_umum']) && !empty($penilaiData['keterangan_umum'])) {
                            $penilaiGeneralNotes[] = $penilaiData['keterangan_umum'];
                        }
                    }

                    // PERBAIKAN: Gunakan data dari validasi umum untuk semua field yang tidak sesuai
                    // Tidak hanya jika data individual kosong, tapi juga untuk melengkapi data individual
                    if (!empty($penilaiValidation['validation'])) {
                        foreach ($penilaiValidation['validation'] as $groupKey => $groupData) {
                            if (is_array($groupData)) {
                                foreach ($groupData as $fieldKey => $fieldData) {
                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                        // PERBAIKAN: Cek apakah field ini sudah ada dalam data individual
                                        // Jika belum ada, tambahkan dari data validasi umum
                                        $fieldAlreadyExists = false;
                                        foreach ($penilaiInvalidFields as $existingField) {
                                            if (strpos($existingField, $fieldKey) !== false) {
                                                $fieldAlreadyExists = true;
                                                break;
                                            }
                                        }

                                        // Jika field belum ada, tambahkan dari data validasi umum
                                        if (!$fieldAlreadyExists) {
                                            // PERBAIKAN: Mapping field names yang lebih lengkap untuk semua kategori field (fallback)
                                            $fieldLabelMap = [
                                                // Dokumen Admin Fakultas
                                                'file_berita_senat' => 'File Berita Senat',
                                                'file_surat_usulan' => 'File Surat Usulan',
                                                'nomor_berita_senat' => 'Nomor Berita Senat',
                                                'nomor_surat_usulan' => 'Nomor Surat Usulan',

                                                // Dokumen Usulan
                                                'turnitin' => 'Dokumen Turnitin',
                                                'upload_artikel' => 'Upload Artikel',
                                                'pakta_integritas' => 'Pakta Integritas',
                                                'bukti_korespondensi' => 'Bukti Korespondensi',

                                                // Dokumen Profil
                                                'sk_pns' => 'SK PNS',
                                                'sk_cpns' => 'SK CPNS',
                                                'ijazah_terakhir' => 'Ijazah Terakhir',
                                                'skp_tahun_pertama' => 'SKP Tahun Pertama',
                                                'skp_tahun_kedua' => 'SKP Tahun Kedua',
                                                'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                                                'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                                                'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                                                'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                                                'pak_konversi' => 'PAK Konversi',
                                                'pak_integrasi' => 'PAK Integrasi',
                                                'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',

                                                // Syarat Guru Besar
                                                'syarat_guru_besar' => 'Syarat Guru Besar',
                                                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar',

                                                // Data Pribadi
                                                'jenis_pegawai' => 'Jenis Pegawai',
                                                'status_kepegawaian' => 'Status Kepegawaian',
                                                'nip' => 'NIP',
                                                'nuptk' => 'NUPTK',
                                                'gelar_depan' => 'Gelar Depan',
                                                'nama_lengkap' => 'Nama Lengkap',
                                                'gelar_belakang' => 'Gelar Belakang',
                                                'email' => 'Email',
                                                'tempat_lahir' => 'Tempat Lahir',
                                                'tanggal_lahir' => 'Tanggal Lahir',
                                                'jenis_kelamin' => 'Jenis Kelamin',
                                                'nomor_handphone' => 'Nomor Handphone',

                                                // Data Kepegawaian
                                                'pangkat_saat_usul' => 'Pangkat',
                                                'tmt_pangkat' => 'TMT Pangkat',
                                                'jabatan_saat_usul' => 'Jabatan',
                                                'tmt_jabatan' => 'TMT Jabatan',
                                                'tmt_cpns' => 'TMT CPNS',
                                                'tmt_pns' => 'TMT PNS',
                                                'unit_kerja_saat_usul' => 'Unit Kerja',

                                                // Data Pendidikan
                                                'pendidikan_terakhir' => 'Pendidikan Terakhir',
                                                'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                                                'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                                                'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                                                'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                                                'url_profil_sinta' => 'Profil SINTA',

                                                // Data Kinerja
                                                'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                                                'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                                                'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1),

                                                // Karya Ilmiah
                                                'jenis_karya' => 'Jenis Karya',
                                                'nama_jurnal' => 'Nama Jurnal',
                                                'judul_artikel' => 'Judul Artikel',
                                                'penerbit_artikel' => 'Penerbit Artikel',
                                                'volume_artikel' => 'Volume Artikel',
                                                'nomor_artikel' => 'Nomor Artikel',
                                                'edisi_artikel' => 'Edisi Artikel (Tahun)',
                                                'halaman_artikel' => 'Halaman Artikel',
                                                'link_artikel' => 'Link Artikel',
                                                'link_sinta' => 'Link SINTA',
                                                'link_scopus' => 'Link SCOPUS',
                                                'link_scimago' => 'Link SCIMAGO',
                                                'link_wos' => 'Link WoS'
                                            ];

                                            $fieldLabel = $fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                            $penilaiInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Gunakan data dari pivot jika ada catatan
                    if (!empty($penilai->pivot->catatan_penilaian)) {
                        $penilaiGeneralNotes[] = $penilai->pivot->catatan_penilaian;
                    }
                } else {
                    // Penilai belum memberikan assessment
                    $penilaiGeneralNotes[] = 'Belum memberikan penilaian';
                }

                // PERBAIKAN: Tambahkan ke array utama dengan struktur yang benar
                // Simpan data individual penilai ke dalam struktur yang dapat diakses
                if (!empty($penilaiInvalidFields)) {
                    $allPenilaiInvalidFields[$penilaiName] = $penilaiInvalidFields;
                }
                if (!empty($penilaiGeneralNotes)) {
                    $allPenilaiGeneralNotes[$penilaiName] = $penilaiGeneralNotes;
                }
            }
        } else {
            // Fallback: Jika tidak ada data individual penilai, gunakan data umum
            if (!empty($penilaiValidation)) {
                // Jika tidak ada data individual, cek data umum dari validation
                if (isset($penilaiValidation['validation'])) {
                    $generalInvalidFields = [];
                    $generalGeneralNotes = [];

                    foreach ($penilaiValidation['validation'] as $groupKey => $groupData) {
                        if (is_array($groupData)) {
                            foreach ($groupData as $fieldKey => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    $groupLabel = isset($fieldGroups[$groupKey]['label']) ? $fieldGroups[$groupKey]['label'] : ucwords(str_replace('_', ' ', $groupKey));
                                    // Handle fields that might be closures
                                    $groupFields = $fieldGroups[$groupKey]['fields'] ?? [];
                                    if (is_callable($groupFields)) {
                                        $groupFields = $groupFields();
                                    }
                                    $fieldLabel = $groupFields[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));

                                    $generalInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                }
                            }
                        }
                    }

                    // Collect keterangan umum dari berbagai sumber
                    if (isset($penilaiValidation['keterangan_umum']) && !empty($penilaiValidation['keterangan_umum'])) {
                        $generalGeneralNotes[] = $penilaiValidation['keterangan_umum'];
                    }

                    // Cek keterangan dari perbaikan_usulan
                    if (isset($penilaiValidation['perbaikan_usulan']['catatan']) && !empty($penilaiValidation['perbaikan_usulan']['catatan'])) {
                        $generalGeneralNotes[] = $penilaiValidation['perbaikan_usulan']['catatan'];
                    }

                    if (!empty($generalInvalidFields) || !empty($generalGeneralNotes)) {
                        $allPenilaiInvalidFields['Tim Penilai'] = $generalInvalidFields;
                        $allPenilaiGeneralNotes['Tim Penilai'] = $generalGeneralNotes;
                    }
                }
            }

            // Tambahan: Cek jika ada data dari struktur yang berbeda (seperti di debug)
            if (empty($allPenilaiInvalidFields) && empty($allPenilaiGeneralNotes)) {
                // Coba ambil dari struktur data yang berbeda
                if (isset($penilaiValidation['validation'])) {
                    $generalInvalidFields = [];
                    $generalGeneralNotes = [];

                    // Proses data validation sesuai struktur debug
                    foreach ($penilaiValidation['validation'] as $groupKey => $groupData) {
                        if (is_array($groupData)) {
                            foreach ($groupData as $fieldKey => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    // PERBAIKAN: Mapping field names yang lebih lengkap untuk semua kategori field (fallback ketiga)
                                    $fieldLabelMap = [
                                        // Dokumen Admin Fakultas
                                        'file_berita_senat' => 'File Berita Senat',
                                        'file_surat_usulan' => 'File Surat Usulan',
                                        'nomor_berita_senat' => 'Nomor Berita Senat',
                                        'nomor_surat_usulan' => 'Nomor Surat Usulan',

                                        // Dokumen Usulan
                                        'turnitin' => 'Dokumen Turnitin',
                                        'upload_artikel' => 'Upload Artikel',
                                        'pakta_integritas' => 'Pakta Integritas',
                                        'bukti_korespondensi' => 'Bukti Korespondensi',

                                        // Dokumen Profil
                                        'sk_pns' => 'SK PNS',
                                        'sk_cpns' => 'SK CPNS',
                                        'ijazah_terakhir' => 'Ijazah Terakhir',
                                        'skp_tahun_pertama' => 'SKP Tahun Pertama',
                                        'skp_tahun_kedua' => 'SKP Tahun Kedua',
                                        'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                                        'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                                        'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                                        'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                                        'pak_konversi' => 'PAK Konversi',
                                        'pak_integrasi' => 'PAK Integrasi',
                                        'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',

                                        // Syarat Guru Besar
                                        'syarat_guru_besar' => 'Syarat Guru Besar',
                                        'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar',

                                        // Data Pribadi
                                        'jenis_pegawai' => 'Jenis Pegawai',
                                        'status_kepegawaian' => 'Status Kepegawaian',
                                        'nip' => 'NIP',
                                        'nuptk' => 'NUPTK',
                                        'gelar_depan' => 'Gelar Depan',
                                        'nama_lengkap' => 'Nama Lengkap',
                                        'gelar_belakang' => 'Gelar Belakang',
                                        'email' => 'Email',
                                        'tempat_lahir' => 'Tempat Lahir',
                                        'tanggal_lahir' => 'Tanggal Lahir',
                                        'jenis_kelamin' => 'Jenis Kelamin',
                                        'nomor_handphone' => 'Nomor Handphone',

                                        // Data Kepegawaian
                                        'pangkat_saat_usul' => 'Pangkat',
                                        'tmt_pangkat' => 'TMT Pangkat',
                                        'jabatan_saat_usul' => 'Jabatan',
                                        'tmt_jabatan' => 'TMT Jabatan',
                                        'tmt_cpns' => 'TMT CPNS',
                                        'tmt_pns' => 'TMT PNS',
                                        'unit_kerja_saat_usul' => 'Unit Kerja',

                                        // Data Pendidikan
                                        'pendidikan_terakhir' => 'Pendidikan Terakhir',
                                        'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                                        'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                                        'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                                        'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                                        'url_profil_sinta' => 'Profil SINTA',

                                        // Data Kinerja
                                        'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                                        'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                                        'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1),

                                        // Karya Ilmiah
                                        'jenis_karya' => 'Jenis Karya',
                                        'nama_jurnal' => 'Nama Jurnal',
                                        'judul_artikel' => 'Judul Artikel',
                                        'penerbit_artikel' => 'Penerbit Artikel',
                                        'volume_artikel' => 'Volume Artikel',
                                        'nomor_artikel' => 'Nomor Artikel',
                                        'edisi_artikel' => 'Edisi Artikel (Tahun)',
                                        'halaman_artikel' => 'Halaman Artikel',
                                        'link_artikel' => 'Link Artikel',
                                        'link_sinta' => 'Link SINTA',
                                        'link_scopus' => 'Link SCOPUS',
                                        'link_scimago' => 'Link SCIMAGO',
                                        'link_wos' => 'Link WoS'
                                    ];

                                    $fieldLabel = $fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                    $generalInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                }
                            }
                        }
                    }

                    // Cek keterangan dari perbaikan_usulan
                    if (isset($penilaiValidation['perbaikan_usulan']['catatan']) && !empty($penilaiValidation['perbaikan_usulan']['catatan'])) {
                        $generalGeneralNotes[] = $penilaiValidation['perbaikan_usulan']['catatan'];
                    }

                    if (!empty($generalInvalidFields) || !empty($generalGeneralNotes)) {
                        $allPenilaiInvalidFields['Tim Penilai'] = $generalInvalidFields;
                        $allPenilaiGeneralNotes['Tim Penilai'] = $generalGeneralNotes;
                    }
                }
            }
        }
    @endphp

    @if($penilais->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="users" class="w-6 h-6 mr-3"></i>
                    Hasil Validasi Semua Tim Penilai
                </h2>
            </div>
            <div class="p-6">
                @if(!empty($allPenilaiInvalidFields))
                    @foreach($allPenilaiInvalidFields as $penilaiName => $invalidFields)
                        <div class="mb-6 last:mb-0">
                            <h4 class="font-medium text-red-800 mb-3 flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                {{ $penilaiName }}
                            </h4>

                            @if(!empty($invalidFields))
                                <div class="mb-4">
                                    <h5 class="text-sm font-medium text-red-700 mb-2 flex items-center">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                        Field yang Tidak Sesuai:
                                    </h5>
                                    <div class="space-y-2">
                                        @if(is_array($invalidFields))
                                            @foreach($invalidFields as $field)
                                                @if(is_string($field))
                                                    <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                        <span>{{ $field }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            @if(is_string($invalidFields))
                                                <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                    <span>{{ $invalidFields }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif


                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Validasi</h4>
                        <p class="text-gray-600">Belum ada hasil validasi dari Tim Penilai untuk usulan ini.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif

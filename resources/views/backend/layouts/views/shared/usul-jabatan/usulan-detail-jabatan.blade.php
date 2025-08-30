{{-- SHARED USULAN DETAIL VIEW - Can be used by all roles --}}
{{-- Usage: @include('backend.layouts.views.shared.usul-jabatan.usulan-detail-jabatan', ['usulan' => $usulan, 'role' => $role]) --}}

@php
    // Role detection and configuration
    $currentRole = $role ?? 'Unknown';
    
    // Enhanced error handling for role detection
    if (!in_array($currentRole, ['Admin Fakultas', 'Admin Universitas', 'Penilai Universitas', 'Tim Senat', 'Pegawai', 'Kepegawaian Universitas'])) {
        $currentRole = 'Unknown';
    }
    
    // Configuration based on role
    $routePrefix = 'backend.usulan';
    $documentRoutePrefix = 'backend.usulan';
    
    // Set route prefix based on role
    if ($currentRole === 'Admin Fakultas') {
        $routePrefix = 'admin-fakultas.usulan';
        $documentRoutePrefix = 'admin-fakultas.usulan';
    } elseif ($currentRole === 'Kepegawaian Universitas') {
        $routePrefix = 'backend.kepegawaian-universitas.usulan';
        $documentRoutePrefix = 'backend.kepegawaian-universitas.usulan';
    } elseif ($currentRole === 'Penilai Universitas') {
        $routePrefix = 'penilai-universitas.pusat-usulan';
        $documentRoutePrefix = 'penilai-universitas.pusat-usulan';
    } elseif ($currentRole === 'Tim Senat') {
        $routePrefix = 'backend.tim-senat.usulan';
        $documentRoutePrefix = 'backend.tim-senat.usulan';
    } elseif ($currentRole === 'Pegawai') {
        $routePrefix = 'pegawai-unmul.usulan-jabatan';
        $documentRoutePrefix = 'pegawai-unmul.usulan-jabatan';
    }
    
    $config = [
        'title' => 'Detail Usulan',
        'description' => 'Informasi lengkap usulan kepegawaian',
        'routePrefix' => $routePrefix,
        'documentRoutePrefix' => $documentRoutePrefix,
        'validationFields' => [
            'data_pribadi',
            'data_kepegawaian', 
            'data_pendidikan',
            'data_kinerja',
            'dokumen_profil',
            'bkd',
            'karya_ilmiah',
            'dokumen_usulan',
            'syarat_guru_besar',
            'dokumen_admin_fakultas'
        ]
    ];
    
    // Field groups configuration
    $fieldGroups = [
        'data_pribadi' => [
            'label' => 'Data Pribadi',
            'icon' => 'user',
            'fields' => [
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
                'nomor_handphone' => 'Nomor Handphone'
            ]
        ],
        'data_kepegawaian' => [
            'label' => 'Data Kepegawaian',
            'icon' => 'briefcase',
            'fields' => [
                'pangkat_saat_usul' => 'Pangkat',
                'tmt_pangkat' => 'TMT Pangkat',
                'jabatan_saat_usul' => 'Jabatan',
                'tmt_jabatan' => 'TMT Jabatan',
                'tmt_cpns' => 'TMT CPNS',
                'tmt_pns' => 'TMT PNS',
                'unit_kerja_saat_usul' => 'Unit Kerja'
            ]
        ],
        'data_pendidikan' => [
            'label' => 'Data Pendidikan & Fungsional',
            'icon' => 'graduation-cap',
            'fields' => [
                'pendidikan_terakhir' => 'Pendidikan Terakhir',
                'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                'url_profil_sinta' => 'Profil SINTA'
            ]
        ],
        'data_kinerja' => [
            'label' => 'Data Kinerja',
            'icon' => 'trending-up',
            'fields' => [
                'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
            ]
        ],
        'dokumen_profil' => [
            'label' => 'Dokumen Profil',
            'icon' => 'folder',
            'fields' => [
                'ijazah_terakhir' => 'Ijazah Terakhir',
                'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                'sk_cpns' => 'SK CPNS',
                'sk_pns' => 'SK PNS',
                'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir'
            ]
        ],
        'dokumen_usulan' => [
            'label' => 'Dokumen Usulan',
            'icon' => 'file-plus',
            'fields' => [
                'pakta_integritas' => 'Pakta Integritas',
                'bukti_korespondensi' => 'Bukti Korespondensi',
                'turnitin' => 'Hasil Turnitin',
                'upload_artikel' => 'Upload Artikel'
            ]
        ],
        'karya_ilmiah' => [
            'label' => 'Karya Ilmiah',
            'icon' => 'book-open',
            'fields' => [
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
            ]
        ],
        'bkd' => [
            'label' => 'Beban Kinerja Dosen (BKD)',
            'icon' => 'clipboard-list',
            'fields' => function() use ($usulan) {
                // Generate BKD fields dynamically based on periode
                $fields = [];

                if ($usulan->periodeUsulan) {
                    $startDate = \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai);
                    $month = $startDate->month;
                    $year = $startDate->year;

                    // Determine current semester based on month
                    $currentSemester = '';
                    $currentYear = 0;

                    if ($month >= 1 && $month <= 6) {
                        $currentSemester = 'Genap';
                        $currentYear = $year - 1;
                    } elseif ($month >= 7 && $month <= 12) {
                        $currentSemester = 'Ganjil';
                        $currentYear = $year;
                    }

                    // Mundur 2 semester untuk titik awal BKD
                    $bkdStartSemester = $currentSemester;
                    $bkdStartYear = $currentYear;

                    for ($i = 0; $i < 2; $i++) {
                        if ($bkdStartSemester === 'Ganjil') {
                            $bkdStartSemester = 'Genap';
                            $bkdStartYear--;
                        } else {
                            $bkdStartSemester = 'Ganjil';
                        }
                    }

                    // Generate 4 semester BKD
                    $tempSemester = $bkdStartSemester;
                    $tempYear = $bkdStartYear;

                    for ($i = 0; $i < 4; $i++) {
                        $academicYear = $tempYear . '_' . ($tempYear + 1);
                        $label = "BKD Semester {$tempSemester} {$tempYear}/" . ($tempYear + 1);
                        $slug = 'bkd_' . strtolower($tempSemester) . '_' . $academicYear;

                        $fields[$slug] = $label;

                        // Move to previous semester
                        if ($tempSemester === 'Ganjil') {
                            $tempSemester = 'Genap';
                            $tempYear--;
                        } else {
                            $tempSemester = 'Ganjil';
                        }
                    }
                }

                return $fields;
            }
        ],
        'syarat_guru_besar' => [
            'label' => 'Syarat Guru Besar',
            'icon' => 'award',
            'fields' => [
                'syarat_guru_besar' => 'Syarat Khusus Guru Besar',
                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
            ]
        ],
        'dokumen_admin_fakultas' => [
            'label' => 'Dokumen yang Dikirim ke Universitas',
            'icon' => 'file-text',
            'isEditableForm' => $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Dikirim ke Admin Fakultas', 'Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas', 'Permintaan Perbaikan dari Kepegawaian Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']),
            'fields' => [
                'nomor_surat_usulan' => 'Nomor Surat Usulan',
                'file_surat_usulan' => 'File Surat Usulan',
                'nomor_berita_senat' => 'Nomor Berita Senat',
                'file_berita_senat' => 'File Berita Senat'
            ]
        ]
    ];
    
    // Get existing validation data
    $existingValidation = $usulan->getValidasiByRole($currentRole) ?? ['validation' => [], 'keterangan_umum' => ''];
    
    // Determine if user can edit
    $canEdit = false;
    if ($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Dikirim ke Admin Fakultas', 'Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])) {
        $canEdit = true;
    } elseif ($currentRole === 'Penilai Universitas' && in_array($usulan->status_usulan, ['Usulan Disetujui Kepegawaian Universitas', 'Permintaan Perbaikan dari Penilai Universitas'])) {
        $canEdit = true;
    } elseif ($currentRole === 'Kepegawaian Universitas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Disetujui Kepegawaian Universitas', 'Usulan Direkomendasi dari Penilai Universitas', 'Usulan Direkomendasi Penilai Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])) {
        $canEdit = true;
    } elseif ($currentRole === 'Tim Senat' && in_array($usulan->status_usulan, ['Usulan Direkomendasikan oleh Tim Senat', 'Usulan Sudah Dikirim ke Sister'])) {
        $canEdit = true;
    } elseif ($currentRole === 'Pegawai' && in_array($usulan->status_usulan, ['Permintaan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas', 'Permintaan Perbaikan Usulan dari Tim Sister'])) {
        $canEdit = true;
    }
    
    // Get penilai validation data for Kepegawaian Universitas
    $penilaiValidation = null;
    $allPenilaiInvalidFields = [];
    if ($currentRole === 'Kepegawaian Universitas') {
        $penilaiValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];
        
        // Process penilai data for validation table
        if (!empty($penilaiValidation)) {
            if (isset($penilaiValidation['validation'])) {
                foreach ($penilaiValidation['validation'] as $groupKey => $groupData) {
                    if (is_array($groupData)) {
                        foreach ($groupData as $fieldKey => $fieldData) {
                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                $allPenilaiInvalidFields[$groupKey][$fieldKey] = $fieldData;
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Determine action permissions based on status and role
    $canReturn = false;
    $canForward = false;
    
    if ($currentRole === 'Admin Fakultas') {
        if ($usulan->status_usulan === 'Usulan Dikirim ke Admin Fakultas') {
            $canReturn = true;
            $canForward = true;
        } elseif ($usulan->status_usulan === 'Usulan Disetujui Admin Fakultas') {
            $canForward = true;
        } elseif ($usulan->status_usulan === 'Usulan Perbaikan dari Admin Fakultas') {
            $canReturn = true;
            $canForward = true;
        } elseif (in_array($usulan->status_usulan, ['Permintaan Perbaikan dari Admin Fakultas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])) {
            $canForward = true;
        }
    }
    
    // Create config array for action bar
    $actionConfig = [
        'canReturn' => $canReturn,
        'canForward' => $canForward,
        'routePrefix' => $routePrefix,
        'canEdit' => $canEdit,
        'canView' => true
    ];
@endphp

{{-- Content starts here --}}
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Include Header Partial --}}
    @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-header')

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notification area moved to top --}}
        <div id="action-feedback" class="mb-4 text-sm hidden"></div>

        {{-- Include Status Badge Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-status-badge')

        {{-- Include Tim Penilai Progress Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-tim-penilai-progress')

        {{-- Include Info History Partial --}}
        {{-- @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-info-history') --}}

        {{-- Include Hasil Validasi Admin Fakultas Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-hasil-validasi-admin-fakultas')

                       {{-- Include Perbaikan dari Admin Universitas Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-admin-universitas')

               {{-- Include Perbaikan untuk Role Pegawai Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-pegawai')

               {{-- Include Perbaikan dari Admin Universitas untuk Role Pegawai Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-admin-universitas-pegawai')

               {{-- Include Perbaikan dari Tim Sister Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-tim-sister')

               {{-- Include Perbaikan dari Kepegawaian Universitas Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-kepegawaian-universitas')

                           {{-- Include Perbaikan dari Admin Fakultas untuk Kepegawaian Universitas Partial --}}
            @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-admin-fakultas-kepegawaian-universitas')
            
            {{-- Include Perbaikan dari Kepegawaian Universitas untuk Pegawai Partial --}}
            @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-perbaikan-kepegawaian-universitas-pegawai')

               {{-- Include Hasil Validasi Semua Tim Penilai Partial --}}
               @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-hasil-validasi-tim-penilai')

        {{-- Include Hasil Validasi Saya Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-hasil-validasi-saya')

        {{-- Include Informasi Usulan Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-informasi-usulan')

        {{-- Form wrapper untuk validation table dan action bar --}}
        @if($canEdit)
            <form id="action-form" action="{{ route($routePrefix . '.save-validation', $usulan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" autocomplete="off" novalidate>
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                
                {{-- Include Validation Table Partial --}}
                @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-validation-table')

                {{-- Include Action Bar Partial --}}
                @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-action-bar', [
                    'config' => $actionConfig,
                    'currentRole' => $currentRole
                ])
            </form>
        @else
            {{-- Include Validation Table Partial (read-only) --}}
            @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-validation-table')

            {{-- Include Action Bar Partial (read-only) --}}
            @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-action-bar', [
                'config' => $actionConfig,
                'currentRole' => $currentRole
            ])
        @endif

        {{-- Include JavaScript Partial --}}
        @include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-javascript')
    </div>
</div>

{{-- SHARED USULAN DETAIL VIEW - Can be used by all roles --}}
{{-- Usage: @include('backend.layouts.views.shared.usulan-detail', ['usulan' => $usulan, 'role' => $role]) --}}

@php
    // ENHANCED: Determine current role and permissions with better detection
    $currentRole = $role ?? auth()->user()->roles->first()->name ?? 'admin-fakultas';
    $roleSlug = strtolower(str_replace(' ', '_', $currentRole));

    // ENHANCED: Define statuses that should be view-only (cannot be edited)
    $viewOnlyStatuses = [
        'Diusulkan ke Universitas',  // Already sent to university
        'Sedang Direview',           // Under review
        'Direkomendasikan',          // Recommended
        'Disetujui',                 // Approved
        'Ditolak',                   // Rejected
        'Perbaikan Usulan'           // Under revision
    ];

    // ENHANCED: Determine edit permissions based on role and status with better logic
    $canEdit = false;
    switch ($currentRole) {
        case 'Admin Fakultas':
            // Admin Fakultas can edit if status is "Diajukan" or "Perbaikan Usulan" (for corrections)
            $canEdit = in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan']);
            break;
        case 'Admin Universitas':
            // Admin Universitas can edit for multiple statuses to allow action buttons
            $canEdit = in_array($usulan->status_usulan, [
                'Diusulkan ke Universitas',
                'Sedang Direview',
                'Menunggu Review Admin Univ',
                'Perbaikan Usulan'
            ]);
            break;
        case 'Tim Penilai':
            // Tim Penilai can edit if status is "Sedang Direview" or "Menunggu Review Admin Univ"
            // This allows multiple penilais to provide their reviews independently
            $canEdit = in_array($usulan->status_usulan, ['Sedang Direview', 'Menunggu Review Admin Univ']);
            break;
        case 'Tim Senat':
            // Tim Senat can edit if status is "Direkomendasikan"
            $canEdit = $usulan->status_usulan === 'Direkomendasikan';
            break;
        default:
            $canEdit = false;
    }

    // ENHANCED: Role-specific configurations with better structure
    $roleConfigs = [
        'Admin Fakultas' => [
            'title' => 'Validasi Usulan Fakultas',
            'description' => 'Validasi data usulan sebelum diteruskan ke universitas',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Diusulkan ke Universitas',
            'actionButtons' => ['perbaikan_usulan', 'usulkan_ke_universitas'],
            'canForward' => true,
            'canReturn' => true,
            'routePrefix' => 'admin-fakultas.usulan'
        ],
        'Admin Universitas' => [
            'title' => 'Validasi Usulan Universitas',
            'description' => 'Validasi final usulan sebelum diteruskan ke tim penilai',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Sedang Direview',
            'actionButtons' => ['perbaikan_ke_pegawai', 'perbaikan_ke_fakultas', 'teruskan_ke_penilai', 'teruskan_ke_senat', 'review_penilai'],
            'canForward' => true,
            'canReturn' => true,
            'routePrefix' => 'backend.admin-univ-usulan.usulan',
            'documentRoutePrefix' => 'backend.admin-univ-usulan.usulan'
        ],
        'Tim Penilai' => [
            'title' => 'Penilaian Usulan',
            'description' => 'Penilaian mendalam terhadap usulan',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Menunggu Review Admin Univ',
            'actionButtons' => ['perbaikan_usulan', 'rekomendasikan'],
            'canForward' => true,
            'canReturn' => false,
            'routePrefix' => 'penilai-universitas.pusat-usulan',
            'documentRoutePrefix' => 'penilai-universitas.pusat-usulan'
        ],
        'Tim Senat' => [
            'title' => 'Keputusan Senat',
            'description' => 'Keputusan akhir senat terhadap usulan',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar'],
            'nextStatus' => 'Disetujui',
            'actionButtons' => ['tolak_usulan', 'setujui_usulan'],
            'canForward' => false,
            'canReturn' => true,
            'routePrefix' => 'tim-senat.usulan'
        ]
    ];

    $config = $roleConfigs[$currentRole] ?? $roleConfigs['Admin Fakultas'];

    // ENHANCED: Get existing validation data with role-specific key
    $existingValidation = $existingValidation ?? $usulan->getValidasiByRole($roleSlug) ?? [];

    // ENHANCED: Define recommendation and perbaikan status for Admin Universitas
    $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
    $hasRecommendation = $penilaiReview['recommendation'] ?? false;
    $hasPerbaikan = isset($penilaiReview['perbaikan_usulan']);

    // ENHANCED: Define field groups and their labels (same for all roles)
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
        'syarat_guru_besar' => [
            'label' => 'Syarat Guru Besar',
            'icon' => 'award',
            'fields' => [
                'syarat_guru_besar' => 'Syarat Khusus Guru Besar',
                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
            ]
        ],
        'dokumen_admin_fakultas' => [
            'label' => $currentRole === 'Admin Fakultas' ? 'Dokumen yang Dikirim ke Universitas' : 'Dokumen Admin Fakultas',
            'icon' => 'file-text',
            'fields' => [
                'nomor_surat_usulan' => 'Nomor Surat Usulan',
                'file_surat_usulan' => 'File Surat Usulan',
                'nomor_berita_senat' => 'Nomor Berita Senat',
                'file_berita_senat' => 'File Berita Senat'
            ],
            'isEditableForm' => $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Perbaikan Usulan'])
        ]
    ];
@endphp

{{-- Content starts here --}}
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $config['title'] }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $config['description'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notification area moved to top --}}
        <div id="action-feedback" class="mb-4 text-sm hidden"></div>

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                // Check if current penilai has submitted review (for Tim Penilai role)
                $currentPenilaiHasReviewed = false;
                $displayStatus = $usulan->status_usulan;
                
                if ($currentRole === 'Tim Penilai' && $usulan->status_usulan === 'Menunggu Review Admin Univ') {
                    $currentPenilaiId = auth()->id();
                    $penilaiValidation = $usulan->validasi_data['tim_penilai'] ?? [];
                    
                    // Check new structure first
                    if (isset($penilaiValidation['reviews'][$currentPenilaiId])) {
                        $currentPenilaiHasReviewed = true;
                    }
                    // Fallback to old structure
                    elseif (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $currentPenilaiId) {
                        $currentPenilaiHasReviewed = true;
                    } elseif (isset($penilaiValidation['perbaikan_usulan']['penilai_id']) && $penilaiValidation['perbaikan_usulan']['penilai_id'] == $currentPenilaiId) {
                        $currentPenilaiHasReviewed = true;
                    } elseif (isset($penilaiValidation['penilai_id']) && $penilaiValidation['penilai_id'] == $currentPenilaiId) {
                        $currentPenilaiHasReviewed = true;
                    }
                    
                    // If current penilai hasn't reviewed, show as waiting for penilai
                    if (!$currentPenilaiHasReviewed) {
                        $displayStatus = 'Menunggu Penilaian Tim Penilai';
                    }
                }
                
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Menunggu Penilaian Tim Penilai' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                    'Menunggu Review Admin Univ' => 'bg-purple-100 text-purple-800 border-purple-300',
                ];
                $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $displayStatus }}</span>
            </div>
        </div>

        {{-- Keterangan Perbaikan Penilai (khusus untuk Admin Universitas) --}}
        @if(($currentRole === 'Admin Universitas' || $currentRole === 'Admin Universitas Usulan') && $usulan->status_usulan === 'Menunggu Review Admin Univ')
            

            @php
                $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
                
                // DEBUG: Check tim penilai data structure
                $debugTimPenilai = [
                    'has_tim_penilai_data' => !empty($penilaiReview),
                    'tim_penilai_keys' => !empty($penilaiReview) ? array_keys($penilaiReview) : [],
                    'has_reviews' => isset($penilaiReview['reviews']),
                    'reviews_count' => isset($penilaiReview['reviews']) ? count($penilaiReview['reviews']) : 0,
                    'has_old_structure' => isset($penilaiReview['perbaikan_usulan']) || isset($penilaiReview['penilai_id']),
                ];
                
                // Get assigned penilais
                $assignedPenilais = $usulan->penilais ?? collect();
                $penilaiValidationData = $penilaiReview['validation'] ?? [];
                
                $debugTimPenilai['assigned_penilais_count'] = $assignedPenilais->count();
                $debugTimPenilai['assigned_penilais_ids'] = $assignedPenilais->pluck('id')->toArray();
                
                // Collect all penilai reviews - check both new structure and backward compatibility
                $allPenilaiReviews = [];
                

                

                foreach ($assignedPenilais as $penilai) {
                    $penilaiId = $penilai->id;
                    $penilaiReviewData = [];
                    
                    // Always set basic penilai info
                    $penilaiReviewData['penilai'] = $penilai;
                    $penilaiReviewData['validation'] = [];
                    
                    // Check new structure first (per-penilai reviews)
                    if (isset($penilaiReview['reviews'][$penilaiId])) {
                        $reviewData = $penilaiReview['reviews'][$penilaiId];
                        $penilaiReviewData['type'] = $reviewData['type'];
                        
                        if ($reviewData['type'] === 'perbaikan_usulan') {
                            $penilaiReviewData['catatan'] = $reviewData['catatan'] ?? '';
                            $penilaiReviewData['tanggal'] = $reviewData['tanggal_return'] ?? null;
                            $penilaiReviewData['validation'] = $reviewData['validation'] ?? [];
                        } elseif ($reviewData['type'] === 'rekomendasi') {
                            $penilaiReviewData['catatan'] = $reviewData['catatan_rekomendasi'] ?? '';
                            $penilaiReviewData['tanggal'] = $reviewData['tanggal_rekomendasi'] ?? null;
                            $penilaiReviewData['recommendation'] = $reviewData['recommendation'] ?? '';
                            $penilaiReviewData['validation'] = $reviewData['validation'] ?? [];
                        }
                    }
                    // Fallback to old structure for backward compatibility
                    else {
                        $hasReview = false;
                        
                        // Check if this penilai has submitted review using old structure
                        if (isset($penilaiReview['validated_by']) && $penilaiReview['validated_by'] == $penilaiId) {
                            $penilaiReviewData['type'] = 'general_review';
                            $penilaiReviewData['tanggal'] = $penilaiReview['validated_at'] ?? null;
                            $hasReview = true;
                        }
                        
                        if (isset($penilaiReview['perbaikan_usulan']['penilai_id']) && $penilaiReview['perbaikan_usulan']['penilai_id'] == $penilaiId) {
                            $penilaiReviewData['type'] = 'perbaikan_usulan';
                            $penilaiReviewData['catatan'] = $penilaiReview['perbaikan_usulan']['catatan'] ?? '';
                            $penilaiReviewData['tanggal'] = $penilaiReview['perbaikan_usulan']['tanggal_return'] ?? null;
                            $penilaiReviewData['validation'] = $penilaiValidationData; // Use global validation for old structure
                            $hasReview = true;
                        }
                        
                        if (isset($penilaiReview['penilai_id']) && $penilaiReview['penilai_id'] == $penilaiId) {
                            $penilaiReviewData['type'] = 'rekomendasi';
                            $penilaiReviewData['catatan'] = $penilaiReview['catatan_rekomendasi'] ?? '';
                            $penilaiReviewData['tanggal'] = $penilaiReview['tanggal_rekomendasi'] ?? null;
                            $penilaiReviewData['recommendation'] = $penilaiReview['recommendation'] ?? '';
                            $penilaiReviewData['validation'] = $penilaiValidationData; // Use global validation for old structure
                            $hasReview = true;
                        }
                        
                        // If no review found, set as pending
                        if (!$hasReview) {
                            $penilaiReviewData['type'] = 'pending';
                            $penilaiReviewData['catatan'] = '';
                            $penilaiReviewData['tanggal'] = null;
                        }
                    }
                    
                    // Always add to the list (whether they have review or not)
                    $allPenilaiReviews[] = $penilaiReviewData;
                    

                }
                

                

                

            @endphp
            

            
            @if(!empty($allPenilaiReviews) || !empty($penilaiReview))
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                            Review dari Tim Penilai Universitas
                        </h2>
                    </div>
                    <div class="p-6">
                        
                        {{-- Ringkasan Review --}}
                        @php
                            $totalPenilai = count($allPenilaiReviews);
                            $reviewedCount = 0;
                            $pendingCount = 0;
                            $totalInvalidFields = 0;
                            
                            foreach ($allPenilaiReviews as $rev) {
                                if ($rev['type'] === 'pending') {
                                    $pendingCount++;
                                } else {
                                    $reviewedCount++;
                                    // Count invalid fields for this penilai
                                    if (!empty($rev['validation'])) {
                                        foreach ($rev['validation'] as $category => $fields) {
                                            if (is_array($fields)) {
                                                foreach ($fields as $field => $fieldData) {
                                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                        $totalInvalidFields++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">Ringkasan Review Tim Penilai</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $totalPenilai }}</div>
                                    <div class="text-sm text-blue-700">Total Penilai</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $reviewedCount }}</div>
                                    <div class="text-sm text-green-700">Sudah Review</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                                    <div class="text-sm text-yellow-700">Belum Review</div>
                                </div>
                            </div>
                            @if($totalInvalidFields > 0)
                                <div class="mt-4 p-3 bg-red-100 border border-red-300 rounded-lg">
                                    <p class="text-sm font-medium text-red-800">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                                        Total {{ $totalInvalidFields }} field perlu perbaikan dari semua penilai yang sudah melakukan review
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Perbaikan Usulan dari Setiap Penilai --}}
                        @php $penilaiIndex = 1; @endphp
                        @foreach($allPenilaiReviews as $index => $review)
                                        @if($review['type'] === 'perbaikan_usulan')
                                @php
                                    $penilaiName = $review['penilai']->nama_lengkap ?? "Penilai {$penilaiIndex}";
                                    $catatan = $review['catatan'] ?? 'Tidak ada catatan';
                                    $tanggal = $review['tanggal'] ?? null;
                                    
                                    // Get validation data for this penilai
                                                        $validationData = $review['validation'] ?? [];
                                    $invalidFields = [];
                                                        
                                                        foreach ($validationData as $category => $fields) {
                                                            if (is_array($fields)) {
                                                                foreach ($fields as $field => $fieldData) {
                                                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                    $invalidFields[] = [
                                                        'category' => $category,
                                                                            'field' => $field,
                                                        'keterangan' => $fieldData['keterangan'] ?? 'Perlu perbaikan'
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Fallback: If no validation data found, check global validation data
                                    if (empty($invalidFields) && !empty($usulan->validasi_data['tim_penilai']['validation'])) {
                                        $globalValidation = $usulan->validasi_data['tim_penilai']['validation'];
                                        foreach ($globalValidation as $category => $fields) {
                                            if (is_array($fields)) {
                                                foreach ($fields as $field => $fieldData) {
                                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                        $invalidFields[] = [
                                                            'category' => $category,
                                                            'field' => $field,
                                                            'keterangan' => $fieldData['keterangan'] ?? 'Perlu perbaikan'
                                                        ];
                                                    }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                                    {{-- Header Penilai --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <i data-lucide="edit-3" class="w-5 h-5 mr-2 text-orange-600"></i>
                                            Perbaikan Usulan - Penilai {{ $penilaiIndex }}
                                        </h4>
                                        <span class="text-sm text-gray-600">{{ $penilaiName }}</span>
                                            </div>
                                            
                                    {{-- Catatan Perbaikan --}}
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                                        <div class="flex items-start gap-3">
                                            <i data-lucide="message-square" class="w-4 h-4 text-orange-600 mt-0.5"></i>
                                            <div>
                                                <p class="text-sm font-medium text-orange-800 mb-1">Catatan Perbaikan:</p>
                                                <p class="text-sm text-orange-700">{{ $catatan }}</p>
                                                                </div>
                                                            </div>
                                                    </div>


                                    {{-- Field-Field yang Bermasalah dalam Format Numbered List --}}
                                    @if(!empty($invalidFields))
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <div class="flex items-start gap-3">
                                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mt-0.5"></i>
                                                <div class="w-full">
                                                    <p class="text-sm font-medium text-red-800 mb-3">
                                                        Field-Field yang Bermasalah ({{ count($invalidFields) }} field):
                                                    </p>
                                                    
                                                    {{-- Tampilan Numbered List ke Bawah --}}
                                                    <ol class="space-y-2">
                                                        @foreach($invalidFields as $index => $field)
                                                            <li class="flex items-start gap-3">
                                                                <span class="flex-shrink-0 w-6 h-6 bg-red-100 border border-red-300 rounded-full flex items-center justify-center text-xs font-bold text-red-800">
                                                                    {{ $index + 1 }}
                                                                </span>
                                                                <div class="flex-1">
                                                                    <div class="flex items-center gap-2 mb-1">
                                                                        <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                                                                        <span class="text-sm font-semibold text-red-800">
                                                                            {{ ucwords(str_replace('_', ' ', $field['category'])) }} > 
                                                                            {{ ucwords(str_replace('_', ' ', $field['field'])) }}
                                            </span>
                                        </div>
                                                                    <p class="text-xs text-red-700 ml-6">
                                                                        {{ $field['keterangan'] }}
                                                                    </p>
                                        </div>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                    </div>
                                </div>
                            </div>
                        @else
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <div class="flex items-start gap-3">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 mt-0.5"></i>
                                            <div>
                                                    <p class="text-sm font-medium text-green-800">Status:</p>
                                                    <p class="text-sm text-green-700">Tidak ada field yang bermasalah</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Informasi Review --}}
                                    <div class="mt-3 text-xs text-gray-500">
                                        <span class="font-medium">Tanggal Review:</span>
                                        @if($tanggal)
                                            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y, HH:mm') }}
                                        @else
                                            Tidak tersedia
                                                                @endif
                                        </div>
                                </div>

                                @php $penilaiIndex++; @endphp
                            @endif
                        @endforeach

                        {{-- Jika tidak ada data perbaikan --}}
                        @if($reviewedCount === 0)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="info" class="w-5 h-5 text-gray-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Info:</p>
                                        <p class="text-sm text-gray-700">Belum ada review dari tim penilai universitas</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        {{-- Informasi Usulan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="info" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan
                </h2>
            </div>
            <div class="p-6">
                {{-- Baris Pertama: Pegawai, Periode, Jenis Usulan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Nama pegawai pengusul</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_lengkap ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Periode</label>
                        <p class="text-xs text-gray-600 mb-2">Periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Usulan</label>
                        <p class="text-xs text-gray-600 mb-2">Jenis usulan yang diajukan</p>
                        <input type="text" value="{{ $usulan->jenis_usulan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>

                {{-- Baris Kedua: Jabatan Saat Ini, Jabatan Tujuan, Unit Kerja --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jabatan Saat Ini</label>
                        <p class="text-xs text-gray-600 mb-2">Jabatan yang sedang diemban</p>
                        <input type="text" value="{{ $usulan->jabatanLama->jabatan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jabatan Tujuan</label>
                        <p class="text-xs text-gray-600 mb-2">Jabatan yang diusulkan</p>
                        <input type="text" value="{{ $usulan->jabatanTujuan->jabatan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Unit Kerja</label>
                        <p class="text-xs text-gray-600 mb-2">Jurusan / Prodi</p>
                        <input type="text" value="{{ $usulan->pegawai->unitKerja->nama ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>

                {{-- Baris Ketiga: Unit Kerja Induk --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Unit Kerja Induk</label>
                        <p class="text-xs text-gray-600 mb-2">Unit Kerja Induk (Fakultas)</p>
                        <input type="text" value="{{ $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div class="md:col-span-2">
                        {{-- Kolom kosong untuk menjaga alignment --}}
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- CSRF token for autosave --}}
        @if($canEdit)
            @csrf
        @endif

        {{-- Validation Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
                    Tabel Validasi
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Usulan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validasi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($config['validationFields'] as $groupKey)
                            @if(isset($fieldGroups[$groupKey]))
                                                @if($groupKey === 'dokumen_admin_fakultas' && $currentRole !== 'Admin Universitas' && $currentRole !== 'Admin Fakultas' && $currentRole !== 'Tim Penilai')
                    @continue
                @endif
                @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Admin Fakultas' && !in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview', 'Direkomendasikan', 'Disetujui', 'Ditolak', 'Perbaikan Usulan']))
                    @continue
                @endif
                {{-- Tombol "Dokumen Dikirim ke Universitas" hanya muncul setelah Admin Fakultas menekan "Usulkan ke Universitas" atau saat perbaikan --}}
                @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Diajukan')
                    @continue
                @endif
                                @php $group = $fieldGroups[$groupKey]; @endphp

                                @if($groupKey === 'dokumen_admin_fakultas' && $group['isEditableForm'])
                                    {{-- Tampilan khusus untuk form input dokumen admin fakultas --}}
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-6 py-3">
                                            <div class="flex items-center">
                                                <i data-lucide="{{ $group['icon'] }}" class="w-4 h-4 mr-2 text-gray-600"></i>
                                                <span class="font-semibold text-gray-800">{{ $group['label'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Baris pertama: Nomor Surat Usulan dan File Surat Usulan --}}
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Nomor Surat Usulan
                                                    </label>
                                                    @php
                                                        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                                        $currentValue = $dokumenPendukung['nomor_surat_usulan'] ?? '';
                                                    @endphp
                                                    <input type="text"
                                                           name="dokumen_pendukung[nomor_surat_usulan]"
                                                           value="{{ e($currentValue) }}"
                                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm"
                                                           placeholder="Masukkan nomor surat usulan">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        File Surat Usulan
                                                    </label>
                                                    @php
                                                        $currentPath = $dokumenPendukung['file_surat_usulan_path'] ?? null;
                                                    @endphp
                                                    <div class="space-y-2">
                                                        @if($currentPath)
                                                            <div class="text-sm text-gray-600">
                                                                File saat ini:
                                                                <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>
                                                            </div>
                                                        @endif
                                                        <input type="file"
                                                               name="dokumen_pendukung[file_surat_usulan]"
                                                               accept=".pdf"
                                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm">
                                                        <small class="text-gray-500">Upload file baru untuk mengganti file yang ada</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"></td>
                                        <td class="px-6 py-4"></td>
                                    </tr>
                                    {{-- Baris kedua: Nomor Berita Senat dan File Berita Senat --}}
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Nomor Berita Senat
                                                    </label>
                                                    @php
                                                        $currentValue = $dokumenPendukung['nomor_berita_senat'] ?? '';
                                                    @endphp
                                                    <input type="text"
                                                           name="dokumen_pendukung[nomor_berita_senat]"
                                                           value="{{ e($currentValue) }}"
                                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm"
                                                           placeholder="Masukkan nomor berita senat">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        File Berita Senat
                                                    </label>
                                                    @php
                                                        $currentPath = $dokumenPendukung['file_berita_senat_path'] ?? null;
                                                    @endphp
                                                    <div class="space-y-2">
                                                        @if($currentPath)
                                                            <div class="text-sm text-gray-600">
                                                                File saat ini:
                                                                <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>
                                                            </div>
                                                        @endif
                                                        <input type="file"
                                                               name="dokumen_pendukung[file_berita_senat]"
                                                               accept=".pdf"
                                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm">
                                                        <small class="text-gray-500">Upload file baru untuk mengganti file yang ada</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"></td>
                                        <td class="px-6 py-4"></td>
                                    </tr>
                                @else
                                    {{-- Tampilan normal untuk role lain atau kondisi lain --}}
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-6 py-3">
                                            <div class="flex items-center">
                                                <i data-lucide="{{ $group['icon'] }}" class="w-4 h-4 mr-2 text-gray-600"></i>
                                                <span class="font-semibold text-gray-800">{{ $group['label'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach(is_callable($group['fields']) ? $group['fields']() : $group['fields'] as $fieldKey => $fieldLabel)
                                    @php
                                        $fieldValidation = $existingValidation['validation'][$groupKey][$fieldKey] ?? ['status' => 'sesuai', 'keterangan' => ''];
                                        $isInvalid = $fieldValidation['status'] === 'tidak_sesuai';
                                    @endphp
                                    <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</div>
                                            <div class="text-sm text-gray-500">
                                                @php
                                                    $value = '';
                                                    if ($groupKey === 'data_pribadi') {
                                                        if ($fieldKey === 'tanggal_lahir') {
                                                            $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_kepegawaian') {
                                                        if ($fieldKey === 'pangkat_saat_usul') {
                                                            $value = $usulan->pegawai->pangkat?->pangkat ?? '-';
                                                        } elseif ($fieldKey === 'jabatan_saat_usul') {
                                                            $value = $usulan->pegawai->jabatan?->jabatan ?? '-';
                                                        } elseif ($fieldKey === 'unit_kerja_saat_usul') {
                                                            $value = $usulan->pegawai->unitKerja?->nama ?? '-';
                                                        } elseif (str_starts_with($fieldKey, 'tmt_')) {
                                                            $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_pendidikan') {
                                                        if ($fieldKey === 'url_profil_sinta' && $usulan->pegawai->$fieldKey) {
                                                            $value = '<a href="' . e($usulan->pegawai->$fieldKey) . '" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">Buka Profil SINTA</a>';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_kinerja') {
                                                        if ($fieldKey === 'nilai_konversi' && $usulan->pegawai->$fieldKey) {
                                                            $value = $usulan->pegawai->$fieldKey;
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'dokumen_profil') {
                                                        if ($usulan->pegawai->$fieldKey) {
                                                            $route = route($config['routePrefix'] . '.show-pegawai-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'Dokumen tidak tersedia';
                                                        }
                                                    } elseif ($groupKey === 'dokumen_usulan') {
                                                        // Check multiple possible locations for document path
                                                        $docPath = null;

                                                        // Check new structure first
                                                        if (isset($usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'])) {
                                                            $docPath = $usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'];
                                                        }
                                                        // Check old structure
                                                        elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                            $docPath = $usulan->data_usulan[$fieldKey];
                                                        }
                                                        // Check using getDocumentPath method
                                                        else {
                                                            $docPath = $usulan->getDocumentPath($fieldKey);
                                                        }

                                                        if ($docPath) {
                                                            $route = route($config['routePrefix'] . '.show-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'BKD tidak tersedia';
                                                        }
                                                    } elseif ($groupKey === 'karya_ilmiah') {
                                                        // Handle link fields with proper data structure
                                                        if (str_contains($fieldKey, 'link_')) {
                                                            // Map field names for links
                                                            $fieldMapping = [
                                                                'link_artikel' => 'artikel',
                                                                'link_sinta' => 'sinta',
                                                                'link_scopus' => 'scopus',
                                                                'link_scimago' => 'scimago',
                                                                'link_wos' => 'wos'
                                                            ];
                                                            $mappedField = $fieldMapping[$fieldKey] ?? $fieldKey;

                                                            // Check new structure first (links object)
                                                            if (isset($usulan->data_usulan['karya_ilmiah']['links'][$mappedField])) {
                                                                $karyaValue = $usulan->data_usulan['karya_ilmiah']['links'][$mappedField];
                                                            }
                                                            // Check old structure
                                                            elseif (isset($usulan->data_usulan['karya_ilmiah'][$fieldKey])) {
                                                                $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey];
                                                            }
                                                            // Check direct structure
                                                            elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                                $karyaValue = $usulan->data_usulan[$fieldKey];
                                                            }
                                                            else {
                                                                $karyaValue = '-';
                                                            }

                                                            if ($karyaValue && $karyaValue !== '-') {
                                                                $value = '<a href="' . e($karyaValue) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Buka Link</a>';
                                                            } else {
                                                                $value = 'Link tidak tersedia';
                                                            }
                                                        } else {
                                                            // Handle non-link fields
                                                            $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey] ?? '-';
                                                            $value = $karyaValue;
                                                        }
                                                    } elseif ($groupKey === 'bkd') {
                                                        // Check multiple possible locations for BKD document path
                                                        $docPath = null;

                                                        // Check new structure first
                                                        if (isset($usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'])) {
                                                            $docPath = $usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'];
                                                        }
                                                        // Check old structure
                                                        elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                            $docPath = $usulan->data_usulan[$fieldKey];
                                                        }
                                                        // Check using getDocumentPath method
                                                        else {
                                                            $docPath = $usulan->getDocumentPath($fieldKey);
                                                        }

                                                        if ($docPath) {
                                                            $route = route(($config['documentRoutePrefix'] ?? $config['routePrefix']) . '.show-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'BKD tidak tersedia';
                                                        }
                                                    } elseif ($groupKey === 'syarat_guru_besar') {
                                                        if ($fieldKey === 'syarat_guru_besar') {
                                                            // Map syarat guru besar values to readable labels
                                                            $syaratMapping = [
                                                                'hibah' => 'Pernah mendapatkan hibah penelitian',
                                                                'bimbingan' => 'Pernah membimbing mahasiswa S3',
                                                                'pengujian' => 'Pernah menjadi penguji disertasi',
                                                                'reviewer' => 'Pernah menjadi reviewer jurnal internasional'
                                                            ];
                                                            $syaratValue = $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '-';
                                                            $value = $syaratMapping[$syaratValue] ?? $syaratValue;
                                                        } elseif ($fieldKey === 'bukti_syarat_guru_besar') {
                                                            // Check multiple possible locations for document path
                                                            $docPath = null;

                                                            // Check new structure first
                                                            if (isset($usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path'])) {
                                                                $docPath = $usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path'];
                                                            }
                                                            // Check old structure
                                                            elseif (isset($usulan->data_usulan['bukti_syarat_guru_besar'])) {
                                                                $docPath = $usulan->data_usulan['bukti_syarat_guru_besar'];
                                                            }
                                                            // Check using getDocumentPath method
                                                            else {
                                                                $docPath = $usulan->getDocumentPath($fieldKey);
                                                            }

                                                            if ($docPath) {
                                                                $route = route(($config['documentRoutePrefix'] ?? $config['routePrefix']) . '.show-document', [$usulan->id, $fieldKey]);
                                                                $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                            } else {
                                                                $value = 'Dokumen tidak tersedia';
                                                            }
                                                        }
                                                    } elseif ($groupKey === 'dokumen_admin_fakultas') {
                                                        // Handle dokumen admin fakultas fields untuk tampilan read-only
                                                        // Data disimpan di validasi_data['admin_fakultas']['dokumen_pendukung']
                                                        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];

                                                        if ($fieldKey === 'nomor_surat_usulan') {
                                                            $value = $dokumenPendukung['nomor_surat_usulan'] ?? '-';
                                                        } elseif ($fieldKey === 'file_surat_usulan') {
                                                            $docPath = $dokumenPendukung['file_surat_usulan_path'] ?? null;
                                                            if ($docPath) {
                                                                // Use proper route for Tim Penilai
                                                                if ($currentRole === 'Tim Penilai') {
                                                                    $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$usulan->id, $fieldKey]);
                                                                } else {
                                                                    $url = asset('storage/' . $docPath);
                                                                }
                                                                $value = '<a href="' . e($route ?? $url) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                            } else {
                                                                $value = 'Dokumen tidak tersedia';
                                                            }
                                                        } elseif ($fieldKey === 'nomor_berita_senat') {
                                                            $value = $dokumenPendukung['nomor_berita_senat'] ?? '-';
                                                        } elseif ($fieldKey === 'file_berita_senat') {
                                                            $docPath = $dokumenPendukung['file_berita_senat_path'] ?? null;
                                                            if ($docPath) {
                                                                // Use proper route for Tim Penilai
                                                                if ($currentRole === 'Tim Penilai') {
                                                                    $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$usulan->id, $fieldKey]);
                                                                } else {
                                                                    $url = asset('storage/' . $docPath);
                                                                }
                                                                $value = '<a href="' . e($route ?? $url) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                            } else {
                                                                $value = 'Dokumen tidak tersedia';
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                {!! $value !!}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($canEdit)
                                                <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]"
                                                        class="validation-status block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm"
                                                        data-group="{{ $groupKey }}" data-field="{{ $fieldKey }}">
                                                    <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                                    <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                                </select>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $fieldValidation['status'] === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fieldValidation['status'])) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($canEdit)
                                                <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]"
                                                          class="validation-keterangan block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'bg-gray-100' }}"
                                                          rows="2"
                                                          placeholder="Keterangan Wajib Diisi Jika Tidak Sesuai"
                                                          {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'disabled' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                            @else
                                                <div class="text-sm text-gray-900">
                                                    {{ $fieldValidation['keterangan'] ?? '-' }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Hasil Penilaian Tim Penilai yang Diteruskan oleh Admin Universitas Section --}}
        @php
            $forwardedPenilaiResult = $usulan->validasi_data['admin_universitas']['forward_penilai_result'] ?? null;
            $isForwardedFromPenilai = $forwardedPenilaiResult && $forwardedPenilaiResult['catatan_source'] === 'tim_penilai';
        @endphp
        
        @if(($currentRole === 'Admin Fakultas' || $currentRole === 'Pegawai') && $usulan->status_usulan === 'Perbaikan Usulan' && $isForwardedFromPenilai)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="users" class="w-6 h-6 mr-3"></i>
                        Hasil Penilaian dari Tim Penilai Universitas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Informasi Penyampaian</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Admin Universitas telah menyampaikan hasil penilaian dari Tim Penilai Universitas. Silakan periksa hasil penilaian dan lakukan perbaikan sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Hasil Penilaian Asli dari Tim Penilai --}}
                    <div class="bg-white border border-blue-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Hasil Penilaian Tim Penilai:
                        </h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">{{ $forwardedPenilaiResult['original_catatan'] }}</div>
                    </div>

                    {{-- Field-Field yang Bermasalah dari Tim Penilai --}}
                    @php
                        // Ambil data validasi dari Tim Penilai untuk menampilkan field bermasalah
                        $penilaiValidationData = $usulan->validasi_data['tim_penilai'] ?? [];
                        $invalidFieldsFromPenilai = [];
                        
                        // Check multiple structures untuk field bermasalah
                        if (!empty($penilaiValidationData['reviews'])) {
                            foreach ($penilaiValidationData['reviews'] as $review) {
                                if (!empty($review['validation'])) {
                                    foreach ($review['validation'] as $category => $fields) {
                                        if (is_array($fields)) {
                                            foreach ($fields as $field => $fieldData) {
                                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                    $invalidFieldsFromPenilai[] = [
                                                        'category' => $category,
                                                        'field' => $field,
                                                        'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif (!empty($penilaiValidationData['validation'])) {
                            foreach ($penilaiValidationData['validation'] as $category => $fields) {
                                if (is_array($fields)) {
                                    foreach ($fields as $field => $fieldData) {
                                        if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                            $invalidFieldsFromPenilai[] = [
                                                'category' => $category,
                                                'field' => $field,
                                                'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    @endphp

                    @if(!empty($invalidFieldsFromPenilai))
                        <div class="bg-white border border-red-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 text-red-600"></i>
                                Field-Field yang Perlu Diperbaiki:
                            </h4>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mt-0.5"></i>
                                    <div class="w-full">
                                        <p class="text-sm font-medium text-red-800 mb-3">
                                            Field-Field yang Bermasalah ({{ count($invalidFieldsFromPenilai) }} field):
                                        </p>
                                        <ol class="space-y-2">
                                            @foreach($invalidFieldsFromPenilai as $index => $field)
                                                <li class="flex items-start gap-3">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-red-100 border border-red-300 rounded-full flex items-center justify-center text-xs font-bold text-red-800">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                                                            <span class="text-sm font-semibold text-red-800">
                                                                {{ ucwords(str_replace('_', ' ', $field['category'])) }} > 
                                                                {{ ucwords(str_replace('_', ' ', $field['field'])) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-red-700 ml-6">
                                                            {{ $field['keterangan'] }}
                                                        </p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Catatan Tambahan dari Admin Universitas (jika ada) --}}
                    @if(!empty($forwardedPenilaiResult['admin_catatan']))
                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i data-lucide="message-circle" class="w-4 h-4 mr-2 text-orange-600"></i>
                                Catatan Tambahan dari Admin Universitas:
                            </h4>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap bg-orange-50 p-3 rounded">{{ $forwardedPenilaiResult['admin_catatan'] }}</div>
                        </div>
                    @endif

                    {{-- Informasi Waktu Penyampaian --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span class="flex items-center">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                Diteruskan pada: {{ \Carbon\Carbon::parse($forwardedPenilaiResult['forwarded_at'])->format('d F Y, H:i') }}
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                oleh Admin Universitas
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Perbaikan dari Admin Universitas Section (Direct Review) --}}
        @php
            $directReview = $usulan->validasi_data['admin_universitas']['direct_review'] ?? null;
            $isDirectFromAdmin = $directReview && $directReview['catatan_source'] === 'admin_universitas';
        @endphp
        
        @if(($currentRole === 'Admin Fakultas' || $currentRole === 'Pegawai') && $usulan->status_usulan === 'Perbaikan Usulan' && $isDirectFromAdmin)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Admin Universitas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-orange-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-orange-700 mt-1">
                                    Admin Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Perbaikan:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $directReview['catatan'] }}</div>
                    </div>

                    {{-- Informasi Waktu Review --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span class="flex items-center">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                Direview pada: {{ \Carbon\Carbon::parse($directReview['reviewed_at'])->format('d F Y, H:i') }}
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                oleh Admin Universitas
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Fallback: Tampilan Lama untuk Backward Compatibility --}}
        @if(($currentRole === 'Admin Fakultas' || $currentRole === 'Pegawai') && $usulan->status_usulan === 'Perbaikan Usulan' && !$isForwardedFromPenilai && !$isDirectFromAdmin && !empty($usulan->catatan_verifikator))
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Admin Universitas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-orange-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-orange-700 mt-1">
                                    Admin Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Perbaikan:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usulan->catatan_verifikator }}</div>
                    </div>

                    @if(isset($usulan->validasi_data['admin_universitas']['validation']))
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Item yang Perlu Diperbaiki:</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                @php
                                    $adminUnivValidation = $usulan->validasi_data['admin_universitas']['validation'] ?? [];
                                    $invalidFields = [];
                                    foreach ($adminUnivValidation as $groupKey => $groupData) {
                                        if (is_array($groupData)) {
                                            foreach ($groupData as $fieldKey => $fieldData) {
                                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                    $fieldLabel = $fieldGroups[$groupKey]['fields'][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                                    $invalidFields[] = [
                                                        'group' => $fieldGroups[$groupKey]['label'] ?? ucwords(str_replace('_', ' ', $groupKey)),
                                                        'field' => $fieldLabel,
                                                        'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if(!empty($invalidFields))
                                    <div class="space-y-2">
                                        @foreach($invalidFields as $field)
                                            <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-red-800">{{ $field['group'] }} - {{ $field['field'] }}</div>
                                                    <div class="text-sm text-red-700 mt-1">{{ $field['keterangan'] }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600">Tidak ada item spesifik yang perlu diperbaiki.</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Action Bar: View-only for certain roles, Edit mode for others --}}
        @if($canEdit)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
                    Perubahan validasi tersimpan otomatis. Gunakan tombol berikut untuk melanjutkan proses.
                </div>
                <form id="action-form" action="{{ route($config['routePrefix'] . '.save-validation', $usulan->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3 flex-wrap" autocomplete="off" novalidate>
                    @csrf
                    <input type="hidden" name="action_type" id="action_type" value="save_only">
                    <input type="hidden" name="catatan_umum" id="catatan_umum" value="">

                    @if($currentRole === 'Admin Universitas')
                        {{-- Admin Universitas Action Buttons --}}
                        @php
                            // Cek apakah sudah ada hasil penilaian dari Tim Penilai
                            $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
                            
                            // Deteksi apakah ada review dari penilai (multiple ways)
                            $hasPenilaiReview = false;
                            $catatanPenilai = '';
                            
                            // Check new structure first
                            if (!empty($penilaiReview['reviews'])) {
                                $hasPenilaiReview = true;
                                // Get first review's catatan
                                foreach ($penilaiReview['reviews'] as $review) {
                                    if (!empty($review['perbaikan_usulan']['catatan'])) {
                                        $catatanPenilai = $review['perbaikan_usulan']['catatan'];
                                        break;
                                    }
                                }
                            }
                            // Check old structure
                            elseif (!empty($penilaiReview['perbaikan_usulan']['catatan'])) {
                                $hasPenilaiReview = true;
                                $catatanPenilai = $penilaiReview['perbaikan_usulan']['catatan'];
                            }
                            // Check if there's any validation data from penilai
                            elseif (!empty($penilaiReview['validation'])) {
                                $hasPenilaiReview = true;
                                $catatanPenilai = 'Hasil penilaian dari Tim Penilai Universitas';
                            }
                        @endphp

                        {{-- Button untuk Admin Universitas - selalu tampil --}}
                        <div class="flex gap-2 flex-wrap">
                            @if($hasPenilaiReview)
                                {{-- SKENARIO 2: Sudah dinilai oleh Tim Penilai - Teruskan hasil penilaian --}}
                                <button type="button" id="btn-teruskan-ke-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                    Teruskan ke Pegawai
                                </button>

                                <button type="button" id="btn-teruskan-ke-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                    Teruskan ke Fakultas
                                </button>

                                {{-- Button Teruskan ke Tim Senat - aktif jika penilai merekomendasikan --}}
                                @if($hasRecommendation === 'direkomendasikan')
                                    <button type="button" id="btn-teruskan-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                                        <i data-lucide="crown" class="w-4 h-4"></i>
                                        Teruskan ke Tim Senat
                                    </button>
                                @endif
                            @else
                                {{-- SKENARIO 1: Belum dinilai oleh Tim Penilai - Admin Univ input catatan sendiri --}}
                                <button type="button" id="btn-perbaikan-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                    Perbaikan ke Pegawai
                                </button>

                                <button type="button" id="btn-perbaikan-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                    Perbaikan ke Fakultas
                                </button>
                            @endif

                            {{-- Button Teruskan ke Penilai - selalu tampil --}}
                            <button type="button" id="btn-teruskan-penilai" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                Teruskan ke Penilai
                            </button>
                        </div>

                        {{-- Info messages --}}
                        @if($hasPenilaiReview)
                            {{-- Info: Menampilkan hasil penilaian yang akan diteruskan --}}
                            @if($catatanPenilai)
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                                    <div class="flex items-start gap-2">
                                        <i data-lucide="info" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                                        <div class="text-sm text-blue-800">
                                            <p class="font-medium mb-1">Hasil Penilaian dari Tim Penilai yang akan diteruskan:</p>
                                            <p class="text-xs bg-blue-100 p-2 rounded">{{ $catatanPenilai }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Info: Admin Univ akan input catatan sendiri --}}
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="edit" class="w-4 h-4 text-yellow-600"></i>
                                    <span class="text-sm text-yellow-800">
                                        Belum ada hasil penilaian dari Tim Penilai. Admin Universitas akan memberikan catatan perbaikan sendiri.
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if($usulan->status_usulan === 'Direkomendasikan')
                            {{-- Forward to Senat button --}}
                            <button type="button" id="btn-teruskan-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                                <i data-lucide="crown" class="w-4 h-4"></i>
                                Teruskan ke Senat
                            </button>
                        @endif

                        @if($usulan->status_usulan === 'Sedang Direview')
                            {{-- Return from Penilai button --}}
                            <button type="button" id="btn-kembalikan-dari-penilai" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                Kembalikan dari Tim Penilai
                            </button>
                        @endif

                        {{-- NEW: Handle Review dari Tim Penilai --}}
                        @if($usulan->status_usulan === 'Menunggu Review Admin Univ')
                            @php
                                // Get assigned penilais to check if all have submitted
                                $assignedPenilais = $usulan->penilais ?? collect();
                                $totalPenilais = $assignedPenilais->count();
                                $penilaisWithReview = 0;
                                
                                if ($totalPenilais > 0) {
                                    // Count penilais who have submitted review
                                    foreach ($assignedPenilais as $penilai) {
                                        $penilaiValidation = $usulan->validasi_data['tim_penilai'] ?? [];
                                        $penilaiId = $penilai->id;
                                        
                                        // Check new structure first
                                        if (isset($penilaiValidation['reviews'][$penilaiId])) {
                                            $penilaisWithReview++;
                                        }
                                        // Fallback to old structure
                                        elseif (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $penilaiId) {
                                            $penilaisWithReview++;
                                        } elseif (isset($penilaiValidation['perbaikan_usulan']['penilai_id']) && $penilaiValidation['perbaikan_usulan']['penilai_id'] == $penilaiId) {
                                            $penilaisWithReview++;
                                        } elseif (isset($penilaiValidation['penilai_id']) && $penilaiValidation['penilai_id'] == $penilaiId) {
                                            $penilaisWithReview++;
                                        }
                                    }
                                }
                                
                                $allPenilaisSubmitted = $totalPenilais === 0 || $penilaisWithReview === $totalPenilais;
                            @endphp

                            <div class="flex flex-col gap-2 w-full">
                                <div class="text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                    Review Hasil Tim Penilai
                                    @if($totalPenilais > 0)
                                        <span class="text-xs text-gray-500 ml-2">
                                            ({{ $penilaisWithReview }}/{{ $totalPenilais }} penilai telah submit)
                                        </span>
                                    @endif
                                </div>

                                @if($allPenilaisSubmitted)
                                    @if($hasPerbaikan)
                                        {{-- Note: Perbaikan dari penilai otomatis disetujui, Admin Univ dapat langsung mengambil tindakan --}}
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                                                <span class="text-sm text-blue-800">
                                                    Perbaikan dari Tim Penilai telah diterima. Silakan pilih tindakan selanjutnya.
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hasRecommendation === 'direkomendasikan')
                                        {{-- Note: Rekomendasi dari penilai otomatis disetujui, Admin Univ dapat langsung mengambil tindakan --}}
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                                <span class="text-sm text-green-800">
                                                    Rekomendasi dari Tim Penilai telah diterima. Silakan pilih tindakan selanjutnya.
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Note: Action buttons moved to main section above for better accessibility --}}
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                                            <span class="text-sm text-yellow-800">
                                                Menunggu semua penilai menyelesaikan review ({{ $penilaisWithReview }}/{{ $totalPenilais }})
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @elseif($currentRole === 'Admin Fakultas')
                        {{-- Admin Fakultas Action Buttons --}}
                        @if($usulan->status_usulan === 'Perbaikan Usulan')
                            {{-- Kondisi 2: Admin Univ Usulan meminta perbaikan - hanya tampilkan "Kirim Kembali ke Universitas" --}}
                            <button type="button" id="btn-kirim-ke-universitas" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Kirim Kembali ke Universitas
                            </button>
                        @elseif($usulan->status_usulan === 'Diajukan')
                            {{-- Kondisi 1: Usulan pertama kali dari pegawai - tampilkan 2 button --}}
                            <div class="flex gap-3">
                                <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                    Perbaikan ke Pegawai
                                </button>

                                <button type="button" id="btn-forward" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    Usulkan ke Universitas
                                </button>
                            </div>
                        @endif
                    @elseif($currentRole === 'Tim Penilai')
                        {{-- Tim Penilai Action Buttons --}}
                        @if(in_array($usulan->status_usulan, ['Sedang Direview', 'Menunggu Review Admin Univ']))
                            <div class="flex flex-col gap-2 w-full">
                                <div class="text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="clipboard-check" class="w-4 h-4 inline mr-1"></i>
                                    Penilaian Usulan
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" id="btn-perbaikan" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                        <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                        Perbaikan Usulan
                                    </button>

                                    <button type="button" id="btn-rekomendasikan" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                                        <i data-lucide="thumbs-up" class="w-4 h-4"></i>
                                        Rekomendasikan
                                    </button>
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                    Hasil penilaian akan dikirim ke Admin Universitas untuk review.
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Other Roles Action Buttons (Original Logic) --}}
                        @if($config['canReturn'])
                        <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            Perbaikan Usulan
                        </button>
                        @endif

                        @if($config['canForward'])
                        <button type="button" id="btn-forward" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            {{ $config['nextStatus'] === 'Diusulkan ke Universitas' ? 'Usulkan ke Universitas' :
                               ($config['nextStatus'] === 'Sedang Direview' ? 'Teruskan ke Penilai' :
                               ($config['nextStatus'] === 'Direkomendasikan' ? 'Rekomendasikan' : 'Lanjutkan')) }}
                        </button>
                        @endif
                    @endif
                </form>
            </div>
        </div>

        {{-- Forward Form Component --}}
        @include('backend.components.usulan._forward-form')
        @else
        {{-- View-only mode --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm">
                    @php
                        // Check if current penilai has submitted review (for Tim Penilai role)
                        $currentPenilaiHasReviewed = false;
                        if ($currentRole === 'Tim Penilai' && $usulan->status_usulan === 'Menunggu Review Admin Univ') {
                            $currentPenilaiId = auth()->id();
                            $penilaiValidation = $usulan->validasi_data['tim_penilai'] ?? [];
                            
                            // Check new structure first
                            if (isset($penilaiValidation['reviews'][$currentPenilaiId])) {
                                $currentPenilaiHasReviewed = true;
                            }
                            // Fallback to old structure
                            elseif (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $currentPenilaiId) {
                                $currentPenilaiHasReviewed = true;
                            } elseif (isset($penilaiValidation['perbaikan_usulan']['penilai_id']) && $penilaiValidation['perbaikan_usulan']['penilai_id'] == $currentPenilaiId) {
                                $currentPenilaiHasReviewed = true;
                            } elseif (isset($penilaiValidation['penilai_id']) && $penilaiValidation['penilai_id'] == $currentPenilaiId) {
                                $currentPenilaiHasReviewed = true;
                            }
                        }
                        
                        $statusMessages = [
                            'Diusulkan ke Universitas' => [
                                'icon' => 'send',
                                'color' => 'text-blue-600',
                                'message' => 'Usulan sudah dikirim ke universitas. Data tidak dapat diubah lagi.'
                            ],
                            'Sedang Direview' => [
                                'icon' => 'clock',
                                'color' => 'text-yellow-600',
                                'message' => 'Usulan sedang dalam proses review. Data tidak dapat diubah.'
                            ],
                            'Menunggu Review Admin Univ' => [
                                'icon' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed ? 'clock' : 'eye',
                                'color' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed ? 'text-yellow-600' : 'text-purple-600',
                                'message' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed 
                                    ? 'Menunggu penilaian Tim Penilai Universitas.'
                                    : 'Usulan menunggu review dari Admin Universitas.'
                            ],
                            'Direkomendasikan' => [
                                'icon' => 'thumbs-up',
                                'color' => 'text-green-600',
                                'message' => 'Usulan sudah direkomendasikan. Data tidak dapat diubah.'
                            ],
                            'Disetujui' => [
                                'icon' => 'check-circle',
                                'color' => 'text-green-600',
                                'message' => 'Usulan sudah disetujui. Data tidak dapat diubah.'
                            ],
                            'Ditolak' => [
                                'icon' => 'x-circle',
                                'color' => 'text-red-600',
                                'message' => 'Usulan sudah ditolak. Data tidak dapat diubah.'
                            ],
                            'Perbaikan Usulan' => [
                                'icon' => 'edit-3',
                                'color' => 'text-orange-600',
                                'message' => 'Usulan sedang dalam perbaikan. Silakan tunggu pegawai melakukan revisi.'
                            ]
                        ];

                        $statusInfo = $statusMessages[$usulan->status_usulan] ?? [
                            'icon' => 'eye',
                            'color' => 'text-gray-600',
                            'message' => 'Mode tampilan detail usulan. Tidak dapat mengedit data.'
                        ];
                    @endphp

                    <div class="{{ $statusInfo['color'] }}">
                        <i data-lucide="{{ $statusInfo['icon'] }}" class="w-4 h-4 inline mr-2"></i>
                        {{ $statusInfo['message'] }}
                    </div>

                    @if($usulan->status_usulan === 'Diusulkan ke Universitas')
                        <div class="mt-2 text-xs text-gray-500">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Usulan akan diproses oleh tim universitas selanjutnya.
                        </div>
                    @elseif($usulan->status_usulan === 'Perbaikan Usulan')
                        <div class="mt-2 text-xs text-gray-500">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Pegawai akan menerima notifikasi untuk melakukan perbaikan.
                        </div>
                    @endif
                </div>
                @php
                    // Determine correct back route based on role
                    $backRoute = '';
                    if ($currentRole === 'Admin Fakultas') {
                        $backRoute = route('admin-fakultas.dashboard');
                    } elseif ($currentRole === 'Admin Universitas') {
                        $backRoute = route('backend.admin-univ-usulan.usulan.index');
                    } elseif ($currentRole === 'Tim Penilai') {
                        $backRoute = route('tim-penilai.usulan.index');
                    } elseif ($currentRole === 'Tim Senat') {
                        $backRoute = route('tim-senat.usulan.index');
                    } else {
                        $backRoute = route('admin-fakultas.dashboard'); // fallback
                    }
                @endphp
                <a href="{{ $backRoute }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@if($canEdit)
<script>
// ========================================
// SHARED DETAIL PAGE SCRIPT - MULTI-ROLE COMPATIBLE
// ========================================
console.log('=== SHARED DETAIL PAGE SCRIPT LOADING ({{ $currentRole }}) ===');

// CRITICAL: Set override flag immediately
window.__DETAIL_PAGE_OVERRIDE_ACTIVE = true;

// ENHANCED: Role-specific configurations
const roleConfig = @json($config);
const currentRole = @json($currentRole);

// ENHANCED: Auto-save functionality with role-specific endpoints
let autoSaveTimeout;
const autoSaveDelay = 600; // 600ms delay

function debouncedAutoSave() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        performAutoSave();
    }, autoSaveDelay);
}

async function performAutoSave() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('action_type', 'autosave');

    // Collect all validation data
    const validationData = {};
    document.querySelectorAll('.validation-status').forEach(select => {
        const group = select.dataset.group;
        const field = select.dataset.field;
        const status = select.value;
        const keterangan = select.closest('tr').querySelector('.validation-keterangan').value;

        if (!validationData[group]) validationData[group] = {};
        validationData[group][field] = {
            status: status,
            keterangan: keterangan
        };
    });

    // Format data based on role
    if ('{{ $currentRole }}' === 'Admin Fakultas') {
        // For Admin Fakultas, send validation data directly
        Object.keys(validationData).forEach(groupKey => {
            Object.keys(validationData[groupKey]).forEach(fieldKey => {
                const fieldData = validationData[groupKey][fieldKey];

                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = `validation[${groupKey}][${fieldKey}][status]`;
                statusInput.value = fieldData.status;
                formData.append(statusInput.name, statusInput.value);

                // Add keterangan
                const keteranganInput = document.createElement('input');
                keteranganInput.type = 'hidden';
                keteranganInput.name = `validation[${groupKey}][${fieldKey}][keterangan]`;
                keteranganInput.value = fieldData.keterangan || '';
                formData.append(keteranganInput.name, keteranganInput.value);
            });
        });
        formData.append('action_type', 'save_only');
    } else {
        // For other roles, send as JSON
        formData.append('validation_data', JSON.stringify(validationData));
    }

    try {
        // Use different endpoint based on role
        let endpoint;
        if ('{{ $currentRole }}' === 'Admin Fakultas') {
            endpoint = `/{{ $config['routePrefix'] }}/usulan/${@json($usulan->id)}/autosave`;
        } else {
            endpoint = `/{{ $config['routePrefix'] }}/usulan/${@json($usulan->id)}/save-validation`;
        }

        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            const result = await response.json();
            console.log('✅ Auto-save successful', result);
            showAutoSaveStatus('success');
        } else {
            const errorResult = await response.json().catch(() => ({}));
            console.error('❌ Auto-save failed', errorResult);
            showAutoSaveStatus('error');
        }
    } catch (error) {
        console.error('❌ Auto-save error:', error);
        showAutoSaveStatus('error');
    }
}

function showAutoSaveStatus(status) {
    const feedback = document.getElementById('action-feedback');
    if (status === 'success') {
        feedback.innerHTML = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Data tersimpan otomatis</div>';
    } else {
        feedback.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menyimpan data</div>';
    }
    feedback.classList.remove('hidden');
    setTimeout(() => {
        feedback.classList.add('hidden');
    }, 3000);
}

// ENHANCED: Event listeners for auto-save
document.addEventListener('DOMContentLoaded', function() {
    // Prevent form auto-submission
    const actionForm = document.getElementById('action-form');
    if (actionForm) {
        actionForm.addEventListener('submit', function(e) {
            // Only allow submission if action_type is not 'save_only' (which is the default)
            const actionType = document.getElementById('action_type').value;
            if (actionType === 'save_only') {
                e.preventDefault();
                console.log('Form submission prevented - no action selected');
                return false;
            }
        });

            // Prevent form submission on Enter key
    actionForm.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            console.log('Form submission prevented - Enter key pressed');
            return false;
        }
    });

    // Clear form data on page load to prevent auto-submission
    actionForm.reset();
    document.getElementById('action_type').value = 'save_only';
    document.getElementById('catatan_umum').value = '';
    }

    // Auto-save on validation status change
    document.querySelectorAll('.validation-status').forEach(select => {
        select.addEventListener('change', debouncedAutoSave);
    });

    // Auto-save on keterangan change
    document.querySelectorAll('.validation-keterangan').forEach(textarea => {
        textarea.addEventListener('input', debouncedAutoSave);
    });

    // Enable/disable keterangan based on status
    document.querySelectorAll('.validation-status').forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const keterangan = row.querySelector('.validation-keterangan');
            const isInvalid = this.value === 'tidak_sesuai';

            keterangan.disabled = !isInvalid;
            keterangan.classList.toggle('bg-gray-100', !isInvalid);
        });
    });
});

// ENHANCED: Action button handlers
// Note: btn-perbaikan event listener is handled in the Tim Penilai section below

if (document.getElementById('btn-forward')) {
    document.getElementById('btn-forward').addEventListener('click', function() {
        // Show forward modal
        showForwardModal();
    });
}

// Admin Universitas specific buttons
if (document.getElementById('btn-perbaikan-pegawai')) {
    document.getElementById('btn-perbaikan-pegawai').addEventListener('click', function() {
        showPerbaikanKePegawaiModal();
    });
}

if (document.getElementById('btn-perbaikan-fakultas')) {
    document.getElementById('btn-perbaikan-fakultas').addEventListener('click', function() {
        showPerbaikanKeFakultasModal();
    });
}

if (document.getElementById('btn-teruskan-penilai')) {
    document.getElementById('btn-teruskan-penilai').addEventListener('click', function() {
        showTeruskanKePenilaiModal();
    });
}

if (document.getElementById('btn-teruskan-senat')) {
    document.getElementById('btn-teruskan-senat').addEventListener('click', function() {
        showTeruskanKeSenatModal();
    });
}

// NEW: Button handlers untuk meneruskan hasil penilaian
if (document.getElementById('btn-teruskan-ke-pegawai')) {
    document.getElementById('btn-teruskan-ke-pegawai').addEventListener('click', function() {
        showTeruskanKePegawaiModal();
    });
}

if (document.getElementById('btn-teruskan-ke-fakultas')) {
    document.getElementById('btn-teruskan-ke-fakultas').addEventListener('click', function() {
        showTeruskanKeFakultasModal();
    });
}

if (document.getElementById('btn-kembalikan-dari-penilai')) {
    document.getElementById('btn-kembalikan-dari-penilai').addEventListener('click', function() {
        showKembalikanDariPenilaiModal();
    });
}

// Admin Fakultas specific button for resending to university
if (document.getElementById('btn-kirim-ke-universitas')) {
    document.getElementById('btn-kirim-ke-universitas').addEventListener('click', function() {
        showKirimKembaliKeUniversitasModal();
    });
}

function showKirimKembaliKeUniversitasModal() {
    Swal.fire({
        title: 'Kirim Kembali ke Universitas',
        text: 'Usulan akan dikirim kembali ke universitas setelah perbaikan. Pastikan semua data sudah diperbaiki.',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk universitas (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan untuk universitas'
        },
        showCancelButton: true,
        confirmButtonText: 'Kirim Kembali ke Universitas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        preConfirm: (catatan) => {
            // Check if dokumen pendukung is filled
            const nomorSurat = document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')?.value;
            const fileSurat = document.querySelector('input[name="dokumen_pendukung[file_surat_usulan]"]')?.files[0];
            const nomorBerita = document.querySelector('input[name="dokumen_pendukung[nomor_berita_senat]"]')?.value;
            const fileBerita = document.querySelector('input[name="dokumen_pendukung[file_berita_senat]"]')?.files[0];

            // For resend, we check if files exist (either current or new upload)
            const currentSuratPath = '{{ $usulan->validasi_data["admin_fakultas"]["dokumen_pendukung"]["file_surat_usulan_path"] ?? "" }}';
            const currentBeritaPath = '{{ $usulan->validasi_data["admin_fakultas"]["dokumen_pendukung"]["file_berita_senat_path"] ?? "" }}';

            if (!nomorSurat && !currentSuratPath) {
                Swal.showValidationMessage('Nomor Surat Usulan harus diisi');
                return false;
            }
            if (!fileSurat && !currentSuratPath) {
                Swal.showValidationMessage('File Surat Usulan harus diunggah');
                return false;
            }
            if (!nomorBerita && !currentBeritaPath) {
                Swal.showValidationMessage('Nomor Berita Senat harus diisi');
                return false;
            }
            if (!fileBerita && !currentBeritaPath) {
                Swal.showValidationMessage('File Berita Senat harus diunggah');
                return false;
            }

            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('resend_to_university', result.value);
        }
    });
}

// Function showPerbaikanModal() removed - replaced with specific event listeners

function showForwardModal() {
    // Implementation for forward modal based on role
    const currentRole = '{{ $currentRole ?? "" }}';

    if (currentRole === 'Tim Penilai') {
        Swal.fire({
            title: 'Rekomendasikan Usulan',
            text: 'Usulan akan direkomendasikan ke Tim Senat. Silakan berikan catatan rekomendasi (opsional).',
            input: 'textarea',
            inputPlaceholder: 'Masukkan catatan rekomendasi (opsional)...',
            inputAttributes: {
                'aria-label': 'Catatan rekomendasi'
            },
            showCancelButton: true,
            confirmButtonText: 'Rekomendasikan ke Senat',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#7c3aed'
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('rekomendasikan', result.value || '');
            }
        });
    } else if (currentRole === 'Admin Fakultas') {
        Swal.fire({
            title: 'Usulkan ke Universitas',
            text: 'Usulan akan dikirim ke universitas untuk diproses selanjutnya. Pastikan dokumen pendukung sudah diisi.',
            input: 'textarea',
            inputPlaceholder: 'Catatan untuk universitas (opsional)...',
            inputAttributes: {
                'aria-label': 'Catatan untuk universitas'
            },
            showCancelButton: true,
            confirmButtonText: 'Usulkan ke Universitas',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            preConfirm: (catatan) => {
                // Untuk usulan pertama kali (status "Diajukan"), tidak perlu dokumen pendukung
                // Dokumen pendukung hanya diperlukan saat perbaikan (status "Perbaikan Usulan")
                const currentStatus = '{{ $usulan->status_usulan }}';
                
                if (currentStatus === 'Perbaikan Usulan') {
                    // Check if dokumen pendukung is filled for revision
                    const nomorSurat = document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')?.value;
                    const fileSurat = document.querySelector('input[name="dokumen_pendukung[file_surat_usulan]"]')?.files[0];
                    const nomorBerita = document.querySelector('input[name="dokumen_pendukung[nomor_berita_senat]"]')?.value;
                    const fileBerita = document.querySelector('input[name="dokumen_pendukung[file_berita_senat]"]')?.files[0];

                    // For resend, we check if files exist (either current or new upload)
                    const currentSuratPath = '{{ $usulan->validasi_data["admin_fakultas"]["dokumen_pendukung"]["file_surat_usulan_path"] ?? "" }}';
                    const currentBeritaPath = '{{ $usulan->validasi_data["admin_fakultas"]["dokumen_pendukung"]["file_berita_senat_path"] ?? "" }}';

                    if (!nomorSurat && !currentSuratPath) {
                        Swal.showValidationMessage('Nomor Surat Usulan harus diisi');
                        return false;
                    }
                    if (!fileSurat && !currentSuratPath) {
                        Swal.showValidationMessage('File Surat Usulan harus diunggah');
                        return false;
                    }
                    if (!nomorBerita && !currentBeritaPath) {
                        Swal.showValidationMessage('Nomor Berita Senat harus diisi');
                        return false;
                    }
                    if (!fileBerita && !currentBeritaPath) {
                        Swal.showValidationMessage('File Berita Senat harus diunggah');
                        return false;
                    }
                }

                return catatan || '';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('forward_to_university', result.value);
            }
        });
    } else {
        console.log('Show forward modal for', currentRole);
    }
}

function showPerbaikanKePegawaiModal() {
    Swal.fire({
        title: 'Perbaikan ke Pegawai',
        text: 'Usulan akan dikembalikan ke pegawai untuk perbaikan. Silakan berikan catatan perbaikan.',
        input: 'textarea',
        inputPlaceholder: 'Masukkan catatan perbaikan untuk pegawai...',
        inputAttributes: {
            'aria-label': 'Catatan perbaikan'
        },
        showCancelButton: true,
        confirmButtonText: 'Kembalikan ke Pegawai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        preConfirm: (catatan) => {
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                return false;
            }
            return catatan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_pegawai', result.value);
        }
    });
}

function showPerbaikanKeFakultasModal() {
    Swal.fire({
        title: 'Perbaikan ke Fakultas',
        text: 'Usulan akan dikembalikan ke fakultas untuk perbaikan. Silakan berikan catatan perbaikan.',
        input: 'textarea',
        inputPlaceholder: 'Masukkan catatan perbaikan untuk fakultas...',
        inputAttributes: {
            'aria-label': 'Catatan perbaikan'
        },
        showCancelButton: true,
        confirmButtonText: 'Kembalikan ke Fakultas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        preConfirm: (catatan) => {
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                return false;
            }
            return catatan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_fakultas', result.value);
        }
    });
}

function showTeruskanKePenilaiModal() {
    // Get penilais data from the page
    const penilais = @json($penilais ?? []);

    if (penilais.length === 0) {
        Swal.fire({
            title: 'Tidak Ada Penilai',
            text: 'Tidak ada penilai yang tersedia saat ini. Silakan hubungi administrator.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2563eb'
        });
        return;
    }

    // Create HTML for penilai selection
    let penilaiHtml = '<div class="mb-4">';
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-2">Pilih Penilai (minimal 1):</label>';
    penilaiHtml += '<div class="space-y-2 max-h-40 overflow-y-auto">';

    penilais.forEach(penilai => {
        penilaiHtml += `
            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" name="selected_penilais[]" value="${penilai.id}" class="penilai-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">
                    <strong>${penilai.nama_lengkap}</strong>
                    ${penilai.bidang_keahlian ? `<br><span class="text-xs text-gray-500">${penilai.bidang_keahlian}</span>` : ''}
                </span>
            </label>
        `;
    });

    penilaiHtml += '</div></div>';
    penilaiHtml += '<div class="mb-4">';
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional):</label>';
    penilaiHtml += '<textarea id="catatan-penilai" placeholder="Catatan untuk tim penilai..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>';
    penilaiHtml += '</div>';

    Swal.fire({
        title: 'Teruskan ke Tim Penilai',
        html: penilaiHtml,
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Penilai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        width: '500px',
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm: () => {
            // Check if at least one penilai is selected
            const selectedPenilais = document.querySelectorAll('input[name="selected_penilais[]"]:checked');
            if (selectedPenilais.length === 0) {
                Swal.showValidationMessage('⚠️ Pilih minimal 1 penilai terlebih dahulu!');
                return false;
            }

            // Validate that selected penilais are valid
            const penilaiIds = Array.from(selectedPenilais).map(cb => cb.value);
            if (penilaiIds.length === 0 || penilaiIds.some(id => !id)) {
                Swal.showValidationMessage('⚠️ Data penilai tidak valid!');
                return false;
            }

            const catatan = document.getElementById('catatan-penilai').value;

            return {
                selected_penilais: penilaiIds,
                catatan: catatan
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Add selected penilais to form data
            const form = document.getElementById('action-form');

            // Remove existing selected_penilais inputs
            form.querySelectorAll('input[name="selected_penilais[]"]').forEach(input => input.remove());

            // Add new selected_penilais inputs
            result.value.selected_penilais.forEach(penilaiId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_penilais[]';
                input.value = penilaiId;
                form.appendChild(input);
            });

            submitAction('forward_to_penilai', result.value.catatan || '');
        }
    });
}

function showTeruskanKeSenatModal() {
    Swal.fire({
        title: 'Teruskan ke Tim Senat',
        text: 'Usulan akan diteruskan ke Tim Senat untuk keputusan final. Pastikan rekomendasi dari Tim Penilai sudah lengkap.',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk Tim Senat (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan untuk Tim Senat'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Tim Senat',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#7c3aed',
        preConfirm: (catatan) => {
            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('forward_to_senat', result.value);
        }
    });
}

function showKembalikanDariPenilaiModal() {
    Swal.fire({
        title: 'Kembalikan dari Tim Penilai',
        text: 'Usulan akan dikembalikan dari Tim Penilai ke Admin Universitas. Silakan berikan catatan alasan pengembalian.',
        input: 'textarea',
        inputPlaceholder: 'Masukkan catatan alasan pengembalian...',
        inputAttributes: {
            'aria-label': 'Catatan alasan pengembalian'
        },
        showCancelButton: true,
        confirmButtonText: 'Kembalikan ke Admin Universitas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ea580c',
        preConfirm: (catatan) => {
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan alasan pengembalian wajib diisi');
                return false;
            }
            return catatan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_from_penilai', result.value);
        }
    });
}

// NEW: Function untuk meneruskan hasil penilaian ke Pegawai
function showTeruskanKePegawaiModal() {
    Swal.fire({
        title: 'Teruskan Hasil Penilaian ke Pegawai',
        text: 'Hasil penilaian dari Tim Penilai Universitas akan diteruskan ke Pegawai untuk perbaikan. Anda dapat menambahkan catatan tambahan jika diperlukan.',
        input: 'textarea',
        inputPlaceholder: 'Catatan tambahan (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan tambahan'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Pegawai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        preConfirm: (catatan) => {
            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_pegawai', result.value);
        }
    });
}

// NEW: Function untuk meneruskan hasil penilaian ke Fakultas
function showTeruskanKeFakultasModal() {
    Swal.fire({
        title: 'Teruskan Hasil Penilaian ke Fakultas',
        text: 'Hasil penilaian dari Tim Penilai Universitas akan diteruskan ke Admin Fakultas untuk perbaikan. Anda dapat menambahkan catatan tambahan jika diperlukan.',
        input: 'textarea',
        inputPlaceholder: 'Catatan tambahan (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan tambahan'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Fakultas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        preConfirm: (catatan) => {
            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_fakultas', result.value);
        }
    });
}

function showKirimKembaliKeUniversitasModal() {
    Swal.fire({
        title: 'Kirim Kembali ke Universitas',
        text: 'Usulan akan dikirim kembali ke Admin Universitas setelah perbaikan. Pastikan semua item yang perlu diperbaiki sudah sesuai.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Kirim ke Universitas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#4f46e5',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // Validate that all validation fields are 'sesuai'
            const invalidFields = [];
            document.querySelectorAll('.validation-status').forEach(select => {
                if (select.value === 'tidak_sesuai') {
                    const row = select.closest('tr');
                    const fieldLabel = row.querySelector('.text-sm.font-medium').textContent;
                    invalidFields.push(fieldLabel);
                }
            });

            if (invalidFields.length > 0) {
                Swal.showValidationMessage(`Masih ada ${invalidFields.length} item yang belum sesuai: ${invalidFields.slice(0, 3).join(', ')}${invalidFields.length > 3 ? '...' : ''}`);
                return false;
            }

            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('resend_to_university', '');
        }
    });
}

function submitAction(actionType, catatan) {
    // Additional validation for forward_to_penilai action
    if (actionType === 'forward_to_penilai') {
        const selectedPenilais = document.querySelectorAll('input[name="selected_penilais[]"]:checked');
        if (selectedPenilais.length === 0) {
            Swal.fire({
                title: '❌ Penilai Belum Dipilih',
                text: 'Silakan pilih minimal 1 penilai terlebih dahulu.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
            return;
        }
    }

    const form = document.getElementById('action-form');
    const actionInput = document.getElementById('action_type');
    const catatanInput = document.getElementById('catatan_umum');

    actionInput.value = actionType;
    catatanInput.value = catatan;

    // Collect validation data before submit
    let validationData = {};
    document.querySelectorAll('.validation-status').forEach(select => {
        const group = select.dataset.group;
        const field = select.dataset.field;
        const status = select.value;
        const keterangan = select.closest('tr').querySelector('.validation-keterangan').value;

        if (!validationData[group]) validationData[group] = {};
        validationData[group][field] = {
            status: status,
            keterangan: keterangan
        };
    });

    // Jika tidak ada validation data (karena form input dokumen), buat data kosong
    if (Object.keys(validationData).length === 0) {
        validationData['dokumen_admin_fakultas'] = {
            'nomor_surat_usulan': { 'status': 'sesuai', 'keterangan': '' },
            'file_surat_usulan': { 'status': 'sesuai', 'keterangan': '' },
            'nomor_berita_senat': { 'status': 'sesuai', 'keterangan': '' },
            'file_berita_senat': { 'status': 'sesuai', 'keterangan': '' }
        };
    }

    // Collect dokumen pendukung data if exists
    const dokumenPendukungData = {};
    document.querySelectorAll('input[name^="dokumen_pendukung["], textarea[name^="dokumen_pendukung["]').forEach(input => {
        const name = input.name;
        const value = input.value;

        // Extract field name from name attribute
        const match = name.match(/dokumen_pendukung\[([^\]]+)\]/);
        if (match) {
            const fieldName = match[1];
            dokumenPendukungData[fieldName] = value;
        }
    });

    // Add validation data to form
    Object.keys(validationData).forEach(groupKey => {
        Object.keys(validationData[groupKey]).forEach(fieldKey => {
            const fieldData = validationData[groupKey][fieldKey];

            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = `validation[${groupKey}][${fieldKey}][status]`;
            statusInput.value = fieldData.status;
            form.appendChild(statusInput);

            // Add keterangan
            const keteranganInput = document.createElement('input');
            keteranganInput.type = 'hidden';
            keteranganInput.name = `validation[${groupKey}][${fieldKey}][keterangan]`;
            keteranganInput.value = fieldData.keterangan || '';
            form.appendChild(keteranganInput);
        });
    });

    // Add dokumen pendukung data to form if exists
    if (Object.keys(dokumenPendukungData).length > 0) {
        Object.keys(dokumenPendukungData).forEach(fieldName => {
            // Skip file inputs as they need to be handled differently
            if (fieldName !== 'file_surat_usulan' && fieldName !== 'file_berita_senat') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `dokumen_pendukung[${fieldName}]`;
                input.value = dokumenPendukungData[fieldName];
                form.appendChild(input);
            }
        });
    }

    // Handle file inputs separately
    document.querySelectorAll('input[type="file"][name^="dokumen_pendukung["]').forEach(fileInput => {
        if (fileInput.files.length > 0) {
            // File inputs are already in the form, no need to add them
            console.log('File input found:', fileInput.name, fileInput.files[0].name);
        }
    });

    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memproses aksi Anda',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form with enhanced notification handling
    const formData = new FormData(form);

    // Manually collect file inputs that are outside the form
    document.querySelectorAll('input[type="file"][name^="dokumen_pendukung["]').forEach(fileInput => {
        if (fileInput.files.length > 0) {
            formData.append(fileInput.name, fileInput.files[0]);
            console.log('Added file to FormData:', fileInput.name, fileInput.files[0].name);
        }
    });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification with enhanced styling
            Swal.fire({
                title: '🎉 Berhasil!',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-6xl text-green-500"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800 mb-2">${data.message}</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Usulan telah berhasil diproses dan status telah diperbarui.
                            </p>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#10b981',
                allowOutsideClick: false
            }).then((result) => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Reload halaman setelah aksi perbaikan berhasil
                    // Khusus untuk aksi yang mengubah status usulan
                    const reloadActions = [
                        'return_to_pegawai',      // Admin Fakultas mengembalikan ke pegawai
                        'forward_to_university',  // Admin Fakultas mengirim ke universitas
                        'resend_to_university',   // Admin Fakultas kirim kembali ke universitas
                        'return_to_fakultas',     // Admin Univ mengembalikan ke fakultas
                        'return_to_pegawai',      // Admin Univ mengembalikan ke pegawai
                        'forward_to_penilai',     // Admin Univ mengirim ke penilai
                        'perbaikan_usulan',       // Tim Penilai mengembalikan untuk perbaikan
                        'rekomendasikan',         // Tim Penilai merekomendasikan
                        'approve_perbaikan',      // Admin Univ menyetujui perbaikan
                        'reject_perbaikan',       // Admin Univ menolak perbaikan
                        'approve_rekomendasi',    // Admin Univ menyetujui rekomendasi
                        'reject_rekomendasi',     // Admin Univ menolak rekomendasi
                        'setujui_usulan',         // Tim Senat menyetujui usulan
                        'tolak_usulan'            // Tim Senat menolak usulan
                    ];
                    
                    if (reloadActions.includes(actionType)) {
                        // Reload halaman setelah 1 detik untuk memastikan data tersimpan
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
            });
        } else {
            // Show error notification
            Swal.fire({
                title: '❌ Gagal!',
                text: data.message || 'Terjadi kesalahan saat memproses aksi.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: '❌ Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'Coba Lagi',
            confirmButtonColor: '#ef4444'
        });
    });
}

// REMOVED: Button handlers untuk review dari Tim Penilai - button sudah dihapus sesuai permintaan user

document.addEventListener('DOMContentLoaded', function() {
    // Tim Penilai Button Handlers
    const btnPerbaikan = document.getElementById('btn-perbaikan');
    if (btnPerbaikan) {
        btnPerbaikan.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Tim Penilai') {
                Swal.fire({
                    title: 'Perbaikan Usulan',
                    text: 'Usulan akan dikirim ke Admin Universitas untuk review. Silakan berikan catatan perbaikan.',
                    input: 'textarea',
                    inputPlaceholder: 'Masukkan catatan perbaikan...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim ke Admin Univ',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d97706',
                    preConfirm: (catatan) => {
                        if (!catatan || catatan.trim() === '') {
                            Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                            return false;
                        }
                        return catatan;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitAction('perbaikan_usulan', result.value);
                    }
                });
            } else if (currentRole === 'Admin Fakultas') {
                Swal.fire({
                    title: 'Perbaikan ke Pegawai',
                    text: 'Usulan akan dikembalikan ke pegawai untuk perbaikan. Silakan berikan catatan perbaikan yang detail.',
                    input: 'textarea',
                    inputPlaceholder: 'Masukkan catatan perbaikan untuk pegawai...',
                    inputAttributes: {
                        'aria-label': 'Catatan perbaikan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Kembalikan ke Pegawai',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d97706',
                    preConfirm: (catatan) => {
                        if (!catatan || catatan.trim() === '') {
                            Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                            return false;
                        }
                        return catatan;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitAction('return_to_pegawai', result.value);
                    }
                });
            }
        });
    }

    const btnRekomendasikan = document.getElementById('btn-rekomendasikan');
    if (btnRekomendasikan) {
        btnRekomendasikan.addEventListener('click', function() {
            Swal.fire({
                title: 'Rekomendasikan Usulan',
                text: 'Usulan akan dikirim ke Admin Universitas untuk review. Silakan berikan catatan rekomendasi.',
                input: 'textarea',
                inputPlaceholder: 'Masukkan catatan rekomendasi (opsional)...',
                showCancelButton: true,
                confirmButtonText: 'Kirim ke Admin Univ',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                preConfirm: (catatan) => {
                    return catatan || '';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('rekomendasikan', result.value);
                }
            });
        });
    }
});
</script>
@endif
{{-- SHARED USULAN DETAIL VIEW - Can be used by all roles --}}
{{-- Usage: @include('backend.layouts.views.shared.usulan-detail', ['usulan' => $usulan, 'role' => $role]) --}}

@php
    // ENHANCED ERROR HANDLING: Safe role detection with comprehensive fallbacks
    $currentUser = auth()->user();
    $currentRole = 'Admin Fakultas'; // Default fallback
    
    if ($currentUser) {
        // Safe access to user roles
        $userRoles = $currentUser->roles ?? collect();
        
        // Check if role is explicitly passed
        if (isset($role) && !empty($role)) {
            $currentRole = $role;
        } else {
            // Try to detect role based on current route
            $currentPath = request()->path();
            
            // Priority order: check specific routes first
            if (str_contains($currentPath, 'kepegawaian-universitas')) {
                $currentRole = 'Kepegawaian Universitas';
            } elseif (str_contains($currentPath, 'penilai-universitas')) {
                $currentRole = 'Penilai Universitas';
            } elseif (str_contains($currentPath, 'admin-universitas')) {
                $currentRole = 'Admin Universitas';
            } elseif (str_contains($currentPath, 'admin-fakultas')) {
                $currentRole = 'Admin Fakultas';
            } elseif (str_contains($currentPath, 'tim-senat')) {
                $currentRole = 'Tim Senat';
            } else {
                // Fallback: check if user has specific role and use it
                if ($userRoles->contains('name', 'Kepegawaian Universitas')) {
                    $currentRole = 'Kepegawaian Universitas';
                } elseif ($userRoles->contains('name', 'Penilai Universitas')) {
                    $currentRole = 'Penilai Universitas';
                } elseif ($userRoles->contains('name', 'Admin Universitas')) {
                    $currentRole = 'Admin Universitas';
                } else {
                    // Last fallback to first role
                    $firstRole = $userRoles->first();
                    $currentRole = $firstRole ? ($firstRole->name ?? 'Admin Fakultas') : 'Admin Fakultas';
                }
            }
        }
    }
    
    // ENHANCED ERROR HANDLING: Get role-specific validation data with safe defaults
    $roleSlug = match($currentRole) {
        'Admin Fakultas' => 'admin_fakultas',
        'Admin Universitas' => 'admin_universitas',
        'Penilai Universitas' => 'penilai_universitas',
        'Tim Senat' => 'tim_senat',
        default => strtolower(str_replace(' ', '_', $currentRole))
    };

    // ENHANCED: Define statuses that should be view-only (cannot be edited)
    $viewOnlyStatuses = [
        'Diusulkan ke Universitas',  // Already sent to university
        'Sedang Direview',           // Under review
        'Direkomendasikan',          // Recommended
        'Disetujui',                 // Approved
        'Ditolak',                   // Rejected
        'Perbaikan Usulan'           // Under revision
    ];

    // ENHANCED ERROR HANDLING: Determine edit permissions with safe data access
    $canEdit = false;
    $usulanStatus = $usulan->status_usulan ?? 'Status tidak tersedia';
    
    switch ($currentRole) {
        case 'Admin Fakultas':
            // Admin Fakultas can edit if status is "Diajukan" or "Perbaikan Usulan" (for corrections)
            $canEdit = in_array($usulanStatus, ['Diajukan', 'Perbaikan Usulan']);
            break;
        case 'Admin Universitas':
            // Admin Universitas can edit if status is "Diusulkan ke Universitas", "Menunggu Review Admin Univ", or intermediate status
            $canEdit = in_array($usulanStatus, [
                'Diusulkan ke Universitas', 
                'Menunggu Review Admin Univ',
                'Menunggu Hasil Penilaian Tim Penilai',
                'Perbaikan Dari Tim Penilai',
                'Usulan Direkomendasi Tim Penilai'
            ]);
            break;
        case 'Penilai Universitas':
            // Penilai Universitas can edit if status is "Sedang Direview", "Usulan dikirim ke Tim Penilai", or "Menunggu Hasil Penilaian Tim Penilai"
            $allowedStatuses = [
                'Sedang Direview',
                'Usulan dikirim ke Tim Penilai',
                'Menunggu Hasil Penilaian Tim Penilai'
            ];
            
            // Check if this penilai is assigned or is the original penilai
            $currentPenilaiId = $currentUser ? $currentUser->id : null;
            $isAssigned = false;
            
            if ($currentPenilaiId) {
                // Check assignment (if method exists)
                if (method_exists($usulan, 'isAssignedToPenilai')) {
                    try {
                        $isAssigned = $usulan->isAssignedToPenilai($currentPenilaiId);
                    } catch (Exception $e) {
                        $isAssigned = false;
                    }
                }
                
                // Fallback: Check if this is the original penilai from validasi_data
                if (!$isAssigned) {
                    $validasiData = $usulan->validasi_data ?? [];
                    $timPenilaiData = $validasiData['tim_penilai'] ?? [];
                    $originalPenilaiId = $timPenilaiData['penilai_id'] ?? null;
                    $isAssigned = ($originalPenilaiId == $currentPenilaiId);
                }
            }
            
            // Check if penilai has completed their assessment
            $isCompleted = false;
            if (isset($penilaiIndividualStatus) && is_array($penilaiIndividualStatus)) {
                $isCompleted = $penilaiIndividualStatus['is_completed'] ?? false;
            }
            
            $canEdit = in_array($usulanStatus, $allowedStatuses) && $isAssigned && !$isCompleted;
            break;
        case 'Tim Senat':
            // Tim Senat can edit if status is "Direkomendasikan"
            $canEdit = $usulanStatus === 'Direkomendasikan';
            break;
        case 'Kepegawaian Universitas':
            // Kepegawaian Universitas can edit if status involves penilai assessment
            $canEdit = in_array($usulanStatus, [
                'Sedang Direview',
                'Menunggu Hasil Penilaian Tim Penilai',
                'Perbaikan Dari Tim Penilai',
                'Usulan Direkomendasi Tim Penilai'
            ]);
            break;
        default:
            $canEdit = false;
    }

    // ENHANCED ERROR HANDLING: Role-specific configurations with safe defaults
    $roleConfigs = [
        'Admin Fakultas' => [
            'title' => 'Validasi Usulan Fakultas',
            'description' => 'Validasi data usulan dan isi dokumen pendukung sebelum diteruskan ke universitas',
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
            'routePrefix' => 'backend.kepegawaian-universitas.usulan',
            'documentRoutePrefix' => 'backend.kepegawaian-universitas.usulan'
        ],
        'Penilai Universitas' => [
            'title' => 'Penilaian Usulan Universitas',
            'description' => 'Validasi dan penilaian usulan dari universitas',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Menunggu Review Admin Univ',
            'actionButtons' => ['perbaikan_usulan', 'rekomendasikan'],
            'canForward' => true,
            'canReturn' => false,
            'routePrefix' => 'penilai-universitas.pusat-usulan',
            'documentRoutePrefix' => 'penilai-universitas.pusat-usulan'
        ],
        'Tim Senat' => [
            'title' => 'Penilaian Senat',
            'description' => 'Penilaian final oleh tim senat',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Disetujui',
            'actionButtons' => ['setujui', 'tolak'],
            'canForward' => true,
            'canReturn' => false,
            'routePrefix' => 'tim-senat.usulan',
            'documentRoutePrefix' => 'tim-senat.usulan'
        ],
        'Pegawai' => [
            'title' => 'Detail Usulan',
            'description' => 'Detail usulan yang telah diajukan',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar'],
            'nextStatus' => 'Diajukan',
            'actionButtons' => ['edit_usulan'],
            'canForward' => false,
            'canReturn' => false,
            'routePrefix' => 'pegawai.usulan',
            'documentRoutePrefix' => 'pegawai.usulan'
        ],
        'Kepegawaian Universitas' => [
            'title' => 'Review Usulan Kepegawaian',
            'description' => 'Review dan validasi usulan dari Tim Penilai',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Sedang Direview',
            'actionButtons' => ['review_hasil_penilai'],
            'canForward' => false,
            'canReturn' => false,
            'routePrefix' => 'backend.kepegawaian-universitas.usulan',
            'documentRoutePrefix' => 'backend.kepegawaian-universitas.usulan'
        ]
    ];
    
    // Safe access to role config with fallback
    $config = $roleConfigs[$currentRole] ?? [
        'title' => 'Detail Usulan',
        'description' => 'Detail usulan',
        'validationFields' => [],
        'nextStatus' => 'Status tidak tersedia',
        'actionButtons' => [],
        'canForward' => false,
        'canReturn' => false,
        'routePrefix' => 'admin-fakultas.usulan',
        'documentRoutePrefix' => 'admin-fakultas.usulan'
    ];

    // ENHANCED: Get existing validation data with role-specific key
    $existingValidation = $existingValidation ?? $usulan->getValidasiByRole($roleSlug) ?? [];
    
    // ENHANCED: Get penilai individual status if available
    $penilaiIndividualStatus = $penilaiIndividualStatus ?? null;

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
            'label' => 'Dokumen yang Dikirim ke Universitas',
            'icon' => 'file-text',
            'fields' => [
                'nomor_surat_usulan' => 'Nomor Surat Usulan',
                'file_surat_usulan' => 'File Surat Usulan',
                'nomor_berita_senat' => 'Nomor Berita Senat',
                'file_berita_senat' => 'File Berita Senat'
            ],
            'isEditableForm' => $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan'])
        ]
    ];
@endphp

{{-- Content starts here --}}
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 border-b shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        {{ $config['title'] }}
                    </h1>
                    <p class="mt-1 text-blue-100">
                        {{ $config['description'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2 text-blue-700 bg-white border border-white rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
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
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Menunggu Hasil Penilaian Tim Penilai' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Perbaikan Dari Tim Penilai' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Usulan Direkomendasi Tim Penilai' => 'bg-green-100 text-green-800 border-green-300',
                    'Tidak Direkomendasikan' => 'bg-red-100 text-red-800 border-red-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>

        {{-- Tim Penilai Assessment Progress Section --}}
        @if($currentRole === 'Kepegawaian Universitas' && in_array($usulan->status_usulan, ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai', 'Usulan Direkomendasi Tim Penilai']))
            <div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <i data-lucide="users" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Tim Penilai Assessment</h3>
                                <p class="text-blue-100 text-sm">Kelola dan monitor progress penilaian</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/20 rounded-full px-4 py-2">
                                <span class="text-white text-sm font-medium">Management Panel</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @php
                    // ENHANCED ERROR HANDLING: Use new progress information method
                    $progressInfo = $usulan->getPenilaiAssessmentProgress();
                    $penilais = $usulan->penilais ?? collect();
                    $totalPenilai = $progressInfo['total_penilai'];
                    $completedPenilai = $progressInfo['completed_penilai'];
                    $remainingPenilai = $progressInfo['remaining_penilai'];
                    $progressPercentage = $progressInfo['progress_percentage'];
                    $isComplete = $progressInfo['is_complete'];
                    $isIntermediate = $progressInfo['is_intermediate'];
                    
                    // Safe access to validasi_data with multiple fallbacks
                    $validasiData = $usulan->validasi_data ?? [];
                    $timPenilaiData = $validasiData['tim_penilai'] ?? [];
                    $assessmentSummary = $timPenilaiData['assessment_summary'] ?? null;
                    
                    // Enhanced progress color logic
                    $progressColor = $isComplete ? 'bg-green-600' : 
                                   ($progressPercentage > 0 ? 'bg-blue-600' : 'bg-gray-400');
                @endphp

                @if($totalPenilai > 0)
                    {{-- ENHANCED: Visual Progress Bar and Status Indicators --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-900 flex items-center">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Progress Penilaian Tim Penilai
                            </h4>
                            <div class="text-sm font-medium text-gray-700">
                                {{ $completedPenilai }}/{{ $totalPenilai }} Selesai
                            </div>
                        </div>
                        
                        {{-- ENHANCED: Animated Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-3 overflow-hidden">
                            @php
                                $progressPercentage = $totalPenilai > 0 ? ($completedPenilai / $totalPenilai) * 100 : 0;
                                $progressColor = match(true) {
                                    $progressPercentage === 0 => 'bg-gray-400',
                                    $progressPercentage < 50 => 'bg-yellow-500',
                                    $progressPercentage < 100 => 'bg-blue-500',
                                    $progressPercentage === 100 => 'bg-green-500',
                                    default => 'bg-blue-500'
                                };
                                $progressAnimation = $progressPercentage > 0 ? 'animate-pulse' : '';
                            @endphp
                            <div class="h-3 {{ $progressColor }} {{ $progressAnimation }} transition-all duration-500 ease-out rounded-full relative"
                                 style="width: {{ $progressPercentage }}%">
                                @if($progressPercentage > 0 && $progressPercentage < 100)
                                    <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- ENHANCED: Progress Statistics --}}
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-blue-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-blue-600">{{ $totalPenilai }}</div>
                                <div class="text-xs text-blue-700">Total Penilai</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-green-600">{{ $completedPenilai }}</div>
                                <div class="text-xs text-green-700">Selesai</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-yellow-600">{{ $remainingPenilai }}</div>
                                <div class="text-xs text-yellow-700">Menunggu</div>
                            </div>
                        </div>
                    </div>

                    {{-- ENHANCED: Individual Penilai Status Display --}}
                    @if($currentRole === 'Penilai Universitas' && isset($penilaiIndividualStatus))
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="user-check" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-blue-900">Status Penilaian Anda</h4>
                                    <div class="mt-2">
                                        @php
                                            $statusColor = match($penilaiIndividualStatus['status']) {
                                                'Sesuai' => 'bg-green-100 text-green-800',
                                                'Perlu Perbaikan' => 'bg-red-100 text-red-800',
                                                'Belum Dinilai' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                            {{ $penilaiIndividualStatus['status'] }}
                                        </span>
                                        @if($penilaiIndividualStatus['is_completed'])
                                            <span class="ml-2 text-xs text-blue-600">
                                                <i data-lucide="check-circle" class="w-3 h-3 inline"></i>
                                                Selesai dinilai
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- ENHANCED: Show appropriate message based on completion status --}}
                                    @if($penilaiIndividualStatus['is_completed'])
                                        <div class="mt-2 text-sm text-blue-700">
                                            <strong>Status:</strong> 
                                            @if($penilaiIndividualStatus['status'] === 'Sesuai')
                                                Penilaian Anda telah dikirim ke Admin Universitas untuk review.
                                            @elseif($penilaiIndividualStatus['status'] === 'Perlu Perbaikan')
                                                Penilaian Anda telah dikirim ke Admin Universitas untuk review.
                                            @else
                                                Penilaian Anda telah selesai.
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-2 text-sm text-blue-700">
                                            <strong>Status:</strong> Anda belum menyelesaikan penilaian untuk usulan ini.
                                        </div>
                                    @endif
                                    
                                    @if($penilaiIndividualStatus['catatan'])
                                        <div class="mt-2 text-sm text-blue-700">
                                            <strong>Catatan:</strong> {{ $penilaiIndividualStatus['catatan'] }}
                                        </div>
                                    @endif
                                    @if($penilaiIndividualStatus['updated_at'])
                                        <div class="mt-1 text-xs text-blue-600">
                                            Terakhir diperbarui: {{ \Carbon\Carbon::parse($penilaiIndividualStatus['updated_at'])->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ENHANCED: Set Progress Variables for Penilai Universitas --}}
                    @if($currentRole === 'Penilai Universitas')
                        @php
                            // Set progress variables for Penilai Universitas
                            $progressInfo = $usulan->getPenilaiAssessmentProgress();
                            $totalPenilai = $progressInfo['total_penilai'] ?? 0;
                            $completedPenilai = $progressInfo['completed_penilai'] ?? 0;
                            $remainingPenilai = $progressInfo['remaining_penilai'] ?? 0;
                            $isComplete = $progressInfo['is_complete'] ?? false;
                            $isIntermediate = $progressInfo['is_intermediate'] ?? false;
                        @endphp
                    @endif

                    {{-- ENHANCED: Status Information Cards with Better Visual Design --}}
                    @if($isIntermediate)
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4 mb-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="font-medium text-yellow-800 flex items-center">
                                        <span class="animate-pulse mr-2">⏳</span>
                                        Menunggu Hasil Penilaian Tim Penilai
                                    </h4>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Masih ada <strong>{{ $remainingPenilai }} penilai</strong> yang belum menyelesaikan penilaian.
                                        <br>Status akan berubah otomatis setelah semua penilai selesai.
                                    </p>
                                    @if($completedPenilai > 0)
                                        <div class="mt-2 text-xs text-yellow-600">
                                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                            {{ $completedPenilai }} penilai telah selesai menilai
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($isComplete)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 mb-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="font-medium text-green-800 flex items-center">
                                        <span class="mr-2">✅</span>
                                        Penilaian Tim Penilai Selesai
                                    </h4>
                                    <p class="text-sm text-green-700 mt-1">
                                        Semua <strong>{{ $totalPenilai }} penilai</strong> telah menyelesaikan penilaian.
                                        <br>Status final: <strong class="text-green-800">{{ $usulan->status_usulan }}</strong>
                                    </p>
                                    <div class="mt-2 text-xs text-green-600">
                                        <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                                        Menunggu keputusan Admin Universitas
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($totalPenilai === 0)
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200 rounded-lg p-4 mb-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="font-medium text-gray-800 flex items-center">
                                        <span class="mr-2">👥</span>
                                        Belum Ada Penilai
                                    </h4>
                                    <p class="text-sm text-gray-700 mt-1">
                                        Usulan ini belum ditugaskan kepada Tim Penilai.
                                        <br>Silakan hubungi Admin Universitas untuk menugaskan penilai.
                                    </p>
                                    <div class="mt-2 text-xs text-gray-600">
                                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                        Status saat ini: {{ $usulan->status_usulan }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ENHANCED: Default status information for other conditions --}}
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="font-medium text-blue-800 flex items-center">
                                        <span class="mr-2">ℹ️</span>
                                        Status Penilaian
                                    </h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Progress: <strong>{{ $completedPenilai }}/{{ $totalPenilai }} penilai</strong> selesai.
                                        @if($completedPenilai > 0)
                                            <br>Status saat ini: <strong class="text-blue-800">{{ $usulan->status_usulan }}</strong>
                                        @endif
                                    </p>
                                    @if($completedPenilai > 0)
                                        <div class="mt-2 text-xs text-blue-600">
                                            <i data-lucide="trending-up" class="w-3 h-3 inline mr-1"></i>
                                            {{ number_format(($completedPenilai / $totalPenilai) * 100, 1) }}% progress
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif


                                                @endif
                                            </div>
        @endif

        {{-- Info History Perbaikan Section --}}
        @if($currentRole === 'Admin Fakultas')
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-900">ⓘ Info History Perbaikan</h3>
                </div>
                @if(!empty($existingValidation))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                            <div>
                                <h4 class="font-medium text-yellow-800">Perbaikan dari Admin Universitas</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Usulan ini telah dikembalikan untuk perbaikan. Silakan periksa catatan perbaikan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-gray-600 mr-3 mt-0.5"></i>
                            <div>
                                <h4 class="font-medium text-gray-800">Tidak Ada Data Perbaikan</h4>
                                <p class="text-sm text-gray-700 mt-1">
                                    Belum ada data perbaikan yang dikirim ke Pegawai. History akan muncul setelah Admin Fakultas mengirim perbaikan.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Perbaikan dari Admin Universitas Section --}}
        @if($currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Perbaikan Usulan' && !empty($usulan->catatan_verifikator))
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

        {{-- Hasil Validasi Semua Tim Penilai (Khusus Kepegawaian Universitas) --}}
        @if($currentRole === 'Kepegawaian Universitas')
            @php
                $allPenilaiInvalidFields = [];
                $allPenilaiGeneralNotes = [];
                
                // Ambil data penilai individual dari relasi penilais
                $penilais = $usulan->penilais ?? collect();
                
                // Ambil data validasi dari semua penilai universitas (untuk fallback)
                $penilaiValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];
                
                // Jika tidak ada data dari getValidasiByRole, coba ambil dari validasi_data
                if (empty($penilaiValidation)) {
                    $validasiData = $usulan->validasi_data ?? [];
                    $penilaiValidation = $validasiData['tim_penilai'] ?? [];
                }
                
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
                            // Ambil data validasi individual penilai dari validasi_data
                            $validasiData = $usulan->validasi_data ?? [];
                            $individualPenilaiData = $validasiData['individual_penilai'] ?? [];
                            
                            // Cari data untuk penilai ini
                            $penilaiData = collect($individualPenilaiData)->firstWhere('penilai_id', $penilai->id);
                            
                            if ($penilaiData && is_array($penilaiData)) {
                                // Proses field yang tidak sesuai untuk penilai ini
                                foreach ($penilaiData as $groupKey => $groupData) {
                                    if (is_array($groupData)) {
                                        foreach ($groupData as $fieldKey => $fieldData) {
                                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                // Mapping field names untuk tampilan yang lebih user-friendly
                                                $fieldLabelMap = [
                                                    'file_berita_senat' => 'File Berita Senat',
                                                    'file_surat_usulan' => 'File Surat Usulan',
                                                    'nomor_berita_senat' => 'Nomor Berita Senat',
                                                    'nomor_surat_usulan' => 'Nomor Surat Usulan',
                                                    'turnitin' => 'Dokumen Turnitin',
                                                    'upload_artikel' => 'Upload Artikel',
                                                    'pakta_integritas' => 'Pakta Integritas',
                                                    'bukti_korespondensi' => 'Bukti Korespondensi',
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
                                                    'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                                                    'syarat_guru_besar' => 'Syarat Guru Besar',
                                                    'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
                                                ];
                                                
                                                $fieldLabel = isset($fieldGroups[$groupKey]['fields'][$fieldKey]) ? $fieldGroups[$groupKey]['fields'][$fieldKey] : ($fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)));
                                                
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
                            
                            // Fallback: Jika tidak ada data individual, gunakan data dari validasi umum
                            if (empty($penilaiInvalidFields) && !empty($penilaiValidation['validation'])) {
                                foreach ($penilaiValidation['validation'] as $groupKey => $groupData) {
                                    if (is_array($groupData)) {
                                        foreach ($groupData as $fieldKey => $fieldData) {
                                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                $fieldLabelMap = [
                                                    'file_berita_senat' => 'File Berita Senat',
                                                    'file_surat_usulan' => 'File Surat Usulan',
                                                    'nomor_berita_senat' => 'Nomor Berita Senat',
                                                    'nomor_surat_usulan' => 'Nomor Surat Usulan',
                                                    'turnitin' => 'Dokumen Turnitin',
                                                    'upload_artikel' => 'Upload Artikel',
                                                    'pakta_integritas' => 'Pakta Integritas',
                                                    'bukti_korespondensi' => 'Bukti Korespondensi',
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
                                                    'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                                                    'syarat_guru_besar' => 'Syarat Guru Besar',
                                                    'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
                                                ];
                                                
                                                $fieldLabel = $fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                                $penilaiInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
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
                        
                        // Tambahkan ke array utama (tampilkan semua penilai, termasuk yang belum menilai)
                        $allPenilaiInvalidFields[$penilaiName] = $penilaiInvalidFields;
                        $allPenilaiGeneralNotes[$penilaiName] = $penilaiGeneralNotes;
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
                                            $fieldLabel = isset($fieldGroups[$groupKey]['fields'][$fieldKey]) ? $fieldGroups[$groupKey]['fields'][$fieldKey] : ucwords(str_replace('_', ' ', $fieldKey));
                                            
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
                                            // Mapping field names untuk tampilan yang lebih user-friendly
                                            $fieldLabelMap = [
                                                'file_berita_senat' => 'File Berita Senat',
                                                'file_surat_usulan' => 'File Surat Usulan',
                                                'nomor_berita_senat' => 'Nomor Berita Senat',
                                                'nomor_surat_usulan' => 'Nomor Surat Usulan',
                                                'turnitin' => 'Dokumen Turnitin',
                                                'upload_artikel' => 'Upload Artikel',
                                                'pakta_integritas' => 'Pakta Integritas',
                                                'bukti_korespondensi' => 'Bukti Korespondensi',
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
                                                'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                                                'syarat_guru_besar' => 'Syarat Guru Besar',
                                                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
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
                            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                            Hasil Validasi Semua Tim Penilai
                        </h2>
                    </div>
                    <div class="p-6">
                        @foreach($penilais as $index => $penilai)
                            @php
                                // Anonymize penilai name for non-Kepegawaian Universitas roles
                                if ($currentRole !== 'Kepegawaian Universitas') {
                                    $penilaiName = 'Penilai ' . ($index + 1);
                                } else {
                                    $penilaiName = $penilai->nama_lengkap ?? 'Penilai ' . $penilai->id;
                                }
                                $invalidFields = $allPenilaiInvalidFields[$penilaiName] ?? [];
                                $generalNotes = $allPenilaiGeneralNotes[$penilaiName] ?? [];
                            @endphp
                            
                            <div class="mb-6 last:mb-0">
                                <div class="flex items-center justify-between mb-3 border-b border-gray-200 pb-2">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        <i data-lucide="user-check" class="w-5 h-5 inline mr-2 text-blue-600"></i>
                                        {{ $penilaiName }}
                                    </h3>
                                    @if($penilai->pivot->tanggal_penilaian)
                                        <span class="text-xs text-gray-500">
                                            <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                            {{ \Carbon\Carbon::parse($penilai->pivot->tanggal_penilaian)->format('d/m/Y H:i') }}
                                        </span>
                                    @elseif($penilai->pivot->updated_at)
                                        <span class="text-xs text-gray-500">
                                            <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                            {{ \Carbon\Carbon::parse($penilai->pivot->updated_at)->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if(!empty($invalidFields))
                                    <div class="mb-4">
                                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                            Field yang Tidak Sesuai:
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($invalidFields as $field)
                                                <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                    <span>{{ $field }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($generalNotes))
                                    <div class="border-t border-red-200 pt-4">
                                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                            <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                                            Keterangan Umum:
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($generalNotes as $note)
                                                <div class="text-sm text-red-700 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                    <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                    <span>{{ $note }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @php
                                    $hasAssessment = !empty($penilai->pivot->hasil_penilaian) || 
                                                    !empty($penilai->pivot->status_penilaian) || 
                                                    !empty($penilai->pivot->catatan_penilaian) ||
                                                    ($penilai->pivot->status_penilaian ?? '') !== 'Belum Dinilai';
                                @endphp
                                
                                @if($hasAssessment)
                                    <div class="border-t border-red-200 pt-4">
                                        <h4 class="font-medium text-orange-800 mb-2 flex items-center">
                                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                                            Hasil Penilaian:
                                        </h4>
                                        <div class="text-sm text-orange-700 bg-orange-50 px-3 py-2 rounded border-l-4 border-orange-400">
                                            @if(!empty($penilai->pivot->hasil_penilaian))
                                                {{ ucwords(str_replace('_', ' ', $penilai->pivot->hasil_penilaian)) }}
                                            @elseif(!empty($penilai->pivot->status_penilaian))
                                                {{ $penilai->pivot->status_penilaian }}
                                            @else
                                                Sudah dinilai
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="border-t border-gray-200 pt-4">
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                                            <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                            Status Penilaian:
                                        </h4>
                                        <div class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded border-l-4 border-gray-400">
                                            Belum memberikan penilaian
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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

        {{-- CSRF token for autosave --}}
        @if($canEdit)
            @csrf
        @endif

        {{-- Hasil Validasi Tim Penilai (Hanya untuk Penilai Universitas - Data Sendiri) --}}
        @if($currentRole === 'Penilai Universitas')
            @php
                $invalidFields = [];
                $generalNotes = [];
                $currentPenilaiId = auth()->user()->id;
                $hasCurrentPenilaiData = false;
                
                // Ambil data validasi dari penilai universitas yang sedang login
                $penilaiValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];
                
                if (!empty($penilaiValidation)) {
                    // Cari data validasi dari penilai yang sedang login
                    $currentPenilaiData = null;
                    
                    // Cek apakah ada data individual penilai
                    if (isset($penilaiValidation['individual_penilai']) && is_array($penilaiValidation['individual_penilai'])) {
                        foreach ($penilaiValidation['individual_penilai'] as $penilaiData) {
                            if (isset($penilaiData['penilai_id']) && $penilaiData['penilai_id'] == $currentPenilaiId) {
                                $currentPenilaiData = $penilaiData;
                                $hasCurrentPenilaiData = true;
                                break;
                            }
                        }
                    }
                    
                    // PERBAIKAN: Hanya proses data jika benar-benar ada data untuk penilai yang sedang login
                    // Jangan fallback ke data umum atau data penilai lain
                    if ($hasCurrentPenilaiData && $currentPenilaiData && is_array($currentPenilaiData)) {
                        foreach ($currentPenilaiData as $groupKey => $groupData) {
                            if (is_array($groupData)) {
                                foreach ($groupData as $fieldKey => $fieldData) {
                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                        $groupLabel = isset($fieldGroups[$groupKey]['label']) ? $fieldGroups[$groupKey]['label'] : ucwords(str_replace('_', ' ', $groupKey));
                                        $fieldLabel = isset($fieldGroups[$groupKey]['fields'][$fieldKey]) ? $fieldGroups[$groupKey]['fields'][$fieldKey] : ucwords(str_replace('_', ' ', $fieldKey));
                                        
                                        $invalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                    }
                                }
                            }
                        }
                        
                        // Collect keterangan umum hanya dari data penilai yang sedang login
                        if (isset($currentPenilaiData['keterangan_umum']) && !empty($currentPenilaiData['keterangan_umum'])) {
                            $generalNotes[] = $currentPenilaiData['keterangan_umum'];
                        }
                    }
                }
            @endphp



            @if(!empty($invalidFields) || !empty($generalNotes))
                <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                            Hasil Validasi Saya
                        </h2>
                    </div>
                    <div class="p-6">
                        @if(!empty($invalidFields))
                            <div class="mb-4">
                                <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                    Field yang Tidak Sesuai:
                                </h4>
                                <div class="space-y-2">
                                @foreach($invalidFields as $field)
                                        <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                            <span>{{ $field }}</span>
                                        </div>
                                @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($generalNotes))
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                    <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                                    Keterangan Umum:
                                </h4>
                                <div class="space-y-2">
                                @foreach($generalNotes as $note)
                                        <div class="text-sm text-red-700 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                            <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                            <span>{{ $note }}</span>
                                        </div>
                                @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif







        {{-- Validation Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
                    Tabel Validasi
                </h2>
            </div>
            
            @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
                <div class="bg-orange-50 border-b border-orange-200 px-6 py-3">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-4 h-4 text-orange-600 mr-2"></i>
                        <span class="text-sm text-orange-800">
                            <strong>Info:</strong> Tabel ini menampilkan validasi dari Kepegawaian Universitas dan data validasi dari Tim Penilai (jika ada).
                            Field yang tidak sesuai dari penilai akan ditandai dengan badge oranye.
                        </span>
                    </div>
                </div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Usulan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validasi
                                @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
                                    <div class="text-xs text-orange-600 mt-1">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                        + Penilai
                                    </div>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                                @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
                                    <div class="text-xs text-orange-600 mt-1">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                        + Penilai
                                    </div>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($config['validationFields'] as $groupKey)
                            @if(isset($fieldGroups[$groupKey]))
                                {{-- Skip dokumen_admin_fakultas untuk role yang tidak diizinkan --}}
                                @if($groupKey === 'dokumen_admin_fakultas' && $currentRole !== 'Admin Universitas' && $currentRole !== 'Admin Fakultas' && $currentRole !== 'Penilai Universitas' && $currentRole !== 'Kepegawaian Universitas')
                                    @continue
                                @endif
                                
                                {{-- Skip dokumen_admin_fakultas untuk Admin Fakultas jika status tidak sesuai --}}
                                @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Admin Fakultas' && !in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview', 'Direkomendasikan', 'Disetujui', 'Ditolak', 'Perbaikan Usulan', 'Diajukan']))
                                    @continue
                                @endif
                                
                                {{-- Skip syarat_guru_besar jika jabatan tujuan bukan Guru Besar --}}
                                @if($groupKey === 'syarat_guru_besar')
                                    @php
                                        $jabatanTujuan = $usulan->jabatanTujuan->jabatan ?? '';
                                        $isGuruBesar = stripos($jabatanTujuan, 'guru besar') !== false || stripos($jabatanTujuan, 'professor') !== false;
                                    @endphp
                                    @if(!$isGuruBesar)
                                        @continue
                                    @endif
                                @endif
                                @php $group = $fieldGroups[$groupKey]; @endphp

                                @if($groupKey === 'dokumen_admin_fakultas' && isset($group['isEditableForm']) && $group['isEditableForm'])
                                    {{-- Tampilan khusus untuk form input dokumen admin fakultas --}}
                                    <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                                        <td colspan="3" class="px-6 py-4">
                                            <div class="flex items-center">
                                                <i data-lucide="{{ $group['icon'] }}" class="w-5 h-5 mr-3 text-blue-600"></i>
                                                <span class="font-bold text-blue-800 text-lg">{{ $group['label'] }}</span>
                                            </div>
                                            <p class="text-sm text-blue-600 mt-1 ml-8">
                                                ⓘ Isi semua field dokumen pendukung yang diperlukan sebelum mengirim ke universitas
                                            </p>
                                        </td>
                                    </tr>
                                    {{-- Baris pertama: Nomor Surat Usulan dan File Surat Usulan --}}
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                        Nomor Surat Usulan
                                                    </label>
                                                    @php
                                                        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                                        $currentValue = $dokumenPendukung['nomor_surat_usulan'] ?? '';
                                                    @endphp
                                                    <input type="text"
                                                           name="dokumen_pendukung[nomor_surat_usulan]"
                                                           value="{{ e($currentValue) }}"
                                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm"
                                                           placeholder="Contoh: 001/FK-UNMUL/2025 (Opsional)">
                                                    <small class="text-gray-500 mt-1">Format: Nomor/Fakultas/UNMUL/Tahun (Opsional)</small>
                                                </div>
                                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                        File Surat Usulan
                                                    </label>
                                                    @php
                                                        $currentPath = $dokumenPendukung['file_surat_usulan_path'] ?? null;
                                                    @endphp
                                                    <div class="space-y-3">
                                                        @if($currentPath)
                                                            <div class="text-sm text-gray-600 bg-green-50 p-2 rounded border border-green-200">
                                                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                                File saat ini:
                                                                <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                            </div>
                                                        @endif
                                                        <input type="file"
                                                               name="dokumen_pendukung[file_surat_usulan]"
                                                               accept=".pdf"
                                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm">
                                                        <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6"></td>
                                        <td class="px-6 py-6"></td>
                                    </tr>
                                    {{-- Baris kedua: Nomor Berita Senat dan File Berita Senat --}}
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                        Nomor Berita Senat
                                                    </label>
                                                    @php
                                                        $currentValue = $dokumenPendukung['nomor_berita_senat'] ?? '';
                                                    @endphp
                                                    <input type="text"
                                                           name="dokumen_pendukung[nomor_berita_senat]"
                                                           value="{{ e($currentValue) }}"
                                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm"
                                                           placeholder="Contoh: 001/Berita-Senat/2025 (Opsional)">
                                                    <small class="text-gray-500 mt-1">Format: Nomor/Berita-Senat/Tahun (Opsional)</small>
                                                </div>
                                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                        File Berita Senat
                                                    </label>
                                                    @php
                                                        $currentPath = $dokumenPendukung['file_berita_senat_path'] ?? null;
                                                    @endphp
                                                    <div class="space-y-3">
                                                        @if($currentPath)
                                                            <div class="text-sm text-gray-600 bg-green-50 p-2 rounded border border-green-200">
                                                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                                File saat ini:
                                                                <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                            </div>
                                                        @endif
                                                        <input type="file"
                                                               name="dokumen_pendukung[file_berita_senat]"
                                                               accept=".pdf"
                                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm">
                                                        <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6"></td>
                                        <td class="px-6 py-6"></td>
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
                                        
                                        // Cek apakah field ini ada dalam data validasi penilai yang tidak sesuai
                                        $penilaiInvalidStatus = null;
                                        $penilaiInvalidKeterangan = null;
                                        
                                        // PERBAIKAN: Hanya tampilkan data validasi penilai untuk Kepegawaian Universitas
                                        // Role Penilai Universitas tidak boleh melihat data validasi penilai lain
                                        if ($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation)) {
                                            if (isset($penilaiValidation['validation'][$groupKey][$fieldKey])) {
                                                $penilaiFieldData = $penilaiValidation['validation'][$groupKey][$fieldKey];
                                                if (isset($penilaiFieldData['status']) && $penilaiFieldData['status'] === 'tidak_sesuai') {
                                                    $penilaiInvalidStatus = 'tidak_sesuai';
                                                    $penilaiInvalidKeterangan = $penilaiFieldData['keterangan'] ?? 'Tidak ada keterangan';
                                                }
                                            }
                                        }
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
                                                                // Use proper route for Penilai Universitas
                                                                if ($currentRole === 'Penilai Universitas') {
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
                                                                // Use proper route for Penilai Universitas
                                                                if ($currentRole === 'Penilai Universitas') {
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
                                                <div class="space-y-1">
                                                    {{-- Status validasi kepegawaian universitas --}}
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $fieldValidation['status'] === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fieldValidation['status'])) }}
                                                </span>
                                                    
                                                    {{-- Status validasi penilai (jika ada) --}}
                                                    @if($penilaiInvalidStatus)
                                                        <div class="mt-1">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                                <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                                Penilai: Tidak Sesuai
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
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
                                                <div class="space-y-2">
                                                    {{-- Keterangan validasi kepegawaian universitas --}}
                                                <div class="text-sm text-gray-900">
                                                    {{ $fieldValidation['keterangan'] ?? '-' }}
                                                    </div>
                                                    
                                                    {{-- Keterangan validasi penilai (jika ada) --}}
                                                    @if($penilaiInvalidKeterangan)
                                                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-2">
                                                            <div class="flex items-start">
                                                                <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 mr-2 mt-0.5 flex-shrink-0"></i>
                                                                <div class="text-sm">
                                                                    <div class="font-medium text-orange-800 mb-1">Keterangan Penilai:</div>
                                                                    <div class="text-orange-700">{{ $penilaiInvalidKeterangan }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
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

        {{-- Perbaikan untuk Role Pegawai Section --}}
        @if($currentRole === 'Pegawai' && in_array($usulan->status_usulan, ['Perbaikan Usulan', 'Dikembalikan']) && !empty($usulan->catatan_verifikator))
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Admin Fakultas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-orange-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-orange-700 mt-1">
                                    Admin Fakultas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Keterangan Umum:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usulan->catatan_verifikator }}</div>
                    </div>

                    @if(isset($usulan->validasi_data['admin_fakultas']['validation']))
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Field dan Keterangan yang Tidak Sesuai:</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                @php
                                    $adminFakultasValidation = $usulan->validasi_data['admin_fakultas']['validation'] ?? [];
                                    $invalidFields = [];
                                    foreach ($adminFakultasValidation as $groupKey => $groupData) {
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
                                    <div class="text-sm text-gray-600">Tidak ada field spesifik yang perlu diperbaiki.</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Perbaikan dari Admin Universitas untuk Role Pegawai Section --}}
        @if($currentRole === 'Pegawai' && in_array($usulan->status_usulan, ['Perbaikan Usulan', 'Dikembalikan']) && !empty($usulan->catatan_verifikator) && isset($usulan->validasi_data['admin_universitas']['validation']))
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Admin Universitas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-red-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Admin Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Keterangan Umum:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usulan->catatan_verifikator }}</div>
                    </div>

                    @if(isset($usulan->validasi_data['admin_universitas']['validation']))
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Field dan Keterangan yang Tidak Sesuai:</h4>
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
                                    <div class="text-sm text-gray-600">Tidak ada field spesifik yang perlu diperbaiki.</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Perbaikan dari Tim Sister untuk Role Pegawai Section --}}
        @if($currentRole === 'Pegawai' && $usulan->status_usulan === 'Perbaikan dari Tim Sister' && !empty($usulan->catatan_verifikator))
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Tim Sister
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-purple-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-purple-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-purple-700 mt-1">
                                    Tim Sister telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Keterangan Umum:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usulan->catatan_verifikator }}</div>
                    </div>

                    @if(isset($usulan->validasi_data['tim_sister']['validation']))
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Field dan Keterangan yang Tidak Sesuai:</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                @php
                                    $timSisterValidation = $usulan->validasi_data['tim_sister']['validation'] ?? [];
                                    $invalidFields = [];
                                    foreach ($timSisterValidation as $groupKey => $groupData) {
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
                                    <div class="text-sm text-gray-600">Tidak ada field spesifik yang perlu diperbaiki.</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Perbaikan dari Kepegawaian Universitas untuk Role Admin Fakultas Section --}}
        @if($currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Perbaikan Usulan' && !empty($usulan->catatan_verifikator) && isset($usulan->validasi_data['admin_universitas']['validation']))
            @php
                // Define field categories relevant for Admin Fakultas
                $adminFakultasFieldCategories = [
                    'dokumen_admin_fakultas',
                    'dokumen_pendukung_fakultas',
                    'dokumen_pendukung' // Include general dokumen pendukung
                ];

                // Get validation data from Admin Universitas
                $adminUnivValidation = $usulan->validasi_data['admin_universitas']['validation'] ?? [];
                $filteredInvalidFields = [];

                // Filter only fields relevant to Admin Fakultas
                foreach ($adminUnivValidation as $groupKey => $groupData) {
                    if (in_array($groupKey, $adminFakultasFieldCategories) && is_array($groupData)) {
                        foreach ($groupData as $fieldKey => $fieldData) {
                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                $fieldLabel = $fieldGroups[$groupKey]['fields'][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                $filteredInvalidFields[] = [
                                    'group' => $fieldGroups[$groupKey]['label'] ?? ucwords(str_replace('_', ' ', $groupKey)),
                                    'field' => $fieldLabel,
                                    'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                ];
                            }
                        }
                    }
                }
            @endphp

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                        Perbaikan dari Kepegawaian Universitas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-red-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Catatan Perbaikan</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Kepegawaian Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki field yang relevan dengan tanggung jawab Admin Fakultas di bawah ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Keterangan Umum:</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usulan->catatan_verifikator }}</div>
                    </div>

                    @if(!empty($filteredInvalidFields))
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Field yang Perlu Diperbaiki (Relevan untuk Admin Fakultas):</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="space-y-2">
                                    @foreach($filteredInvalidFields as $field)
                                        <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                            <div>
                                                <div class="text-sm font-medium text-red-800">{{ $field['group'] }} - {{ $field['field'] }}</div>
                                                <div class="text-sm text-red-700 mt-1">{{ $field['keterangan'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800">Tidak Ada Field yang Perlu Diperbaiki</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Semua field yang relevan dengan Admin Fakultas sudah sesuai. Perbaikan mungkin terkait dengan area lain yang bukan tanggung jawab Admin Fakultas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-start">
                            <i data-lucide="lightbulb" class="w-5 h-5 text-amber-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-amber-800">Informasi Penting</h4>
                                <p class="text-sm text-amber-700 mt-1">
                                    Hanya field yang relevan dengan tanggung jawab Admin Fakultas yang ditampilkan di atas. 
                                    Field lain yang tidak ditampilkan mungkin sudah sesuai atau merupakan tanggung jawab role lain.
                                </p>
                            </div>
                        </div>
                    </div>
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

                    @if($currentRole === 'Kepegawaian Universitas')
                        {{-- Admin Universitas Action Buttons --}}
                        

                        
                        {{-- ENHANCED: Specific status-based action buttons with improved logic --}}
                        @if($usulan->status_usulan === 'Diusulkan ke Universitas')
                            {{-- Initial validation buttons - only for new submissions --}}
                            <button type="button" id="btn-perbaikan-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                Perbaikan ke Pegawai
                            </button>

                            <button type="button" id="btn-perbaikan-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                Perbaikan ke Fakultas
                            </button>

                            <button type="button" id="btn-teruskan-penilai" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                Teruskan ke Penilai
                            </button>

                            <button type="button" id="btn-tidak-direkomendasikan" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-colors flex items-center gap-2">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                Tidak Direkomendasikan
                            </button>
                        @endif

                        @if($usulan->status_usulan === 'Direkomendasikan')
                            {{-- Forward to Senat button - only when recommended --}}
                            <button type="button" id="btn-teruskan-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                                <i data-lucide="crown" class="w-4 h-4"></i>
                                Teruskan ke Senat
                            </button>
                        @endif

                        {{-- ENHANCED: Tim Penilai Assessment Status with specific conditions --}}
                        @if(in_array($usulan->status_usulan, ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai', 'Perbaikan Dari Tim Penilai', 'Usulan Direkomendasi Tim Penilai']))
                            @php
                                // ENHANCED ERROR HANDLING: Use new progress information method
                                $progressInfo = $usulan->getPenilaiAssessmentProgress();
                                $totalPenilai = $progressInfo['total_penilai'];
                                $completedPenilai = $progressInfo['completed_penilai'];
                                $remainingPenilai = $progressInfo['remaining_penilai'];
                                $isComplete = $progressInfo['is_complete'];
                                $isIntermediate = $progressInfo['is_intermediate'];
                                
                                // Additional safety checks
                                $progressText = $totalPenilai > 0 ? "{$completedPenilai}/{$totalPenilai}" : "0/0";
                            @endphp

                            <div class="p-6">
                                <div class="w-full">
                                    <!-- Progress Overview Card -->
                                    <div class="w-full">
                                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-lg font-semibold text-slate-800">Progress Overview</h4>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                                    <span class="text-sm text-slate-600">Live Status</span>
                                                </div>
                                </div>

                                @if($isIntermediate)
                                    {{-- Penilai belum semua selesai - enhanced management actions --}}
                                    <div class="space-y-4">
                                        <!-- Progress Bar -->
                                        <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
                                            @php
                                                $progressPercentage = $totalPenilai > 0 ? ($completedPenilai / $totalPenilai) * 100 : 0;
                                            @endphp
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500 ease-out" style="width: {{ $progressPercentage }}%"></div>
                                            </div>
                                        
                                        <!-- Status Info -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $completedPenilai }} Selesai</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-3 h-3 bg-orange-500 rounded-full animate-pulse"></div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $remainingPenilai }} Pending</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-slate-800">{{ $progressText }}</div>
                                                <div class="text-sm text-slate-600">Total Penilai</div>
                                        </div>
                                    </div>

                                        <!-- Action Card -->
                                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-amber-900 mb-1">Penilaian Sedang Berlangsung</h5>
                                                    <p class="text-sm text-amber-800 mb-3">
                                                        Masih ada {{ $remainingPenilai }} penilai yang belum menyelesaikan tugasnya. 
                                                        Anda dapat menambah penilai baru atau menunggu penilai yang ada.
                                                    </p>
                                                    <div class="flex flex-wrap gap-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                            <i data-lucide="plus" class="w-3 h-3 mr-1"></i>
                                                            Tambah Penilai
                                                        </span>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                            Monitor Progress
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="bg-gradient-to-r from-slate-50 to-blue-50 border-t border-slate-200 px-6 py-4">
                                        <div class="flex gap-3 justify-center">
                                            <button type="button" id="btn-tambah-penilai" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                                                <span class="font-medium">Tambah Penilai</span>
                                        </button>
                                            <button type="button" id="btn-simpan-validasi-top" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <i data-lucide="save" class="w-4 h-4"></i>
                                                <span class="font-medium">Simpan Validasi</span>
                                        </button>
                                        </div>
                                    </div>
                                    

                                    </div>
                                @elseif($isComplete)
                                    {{-- Semua penilai sudah selesai - full actions based on final status --}}
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                        <div class="flex items-start">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-green-600 mr-2 mt-0.5"></i>
                                            <div class="text-sm text-green-800">
                                                <strong>Status:</strong> Semua penilai telah menyelesaikan penilaian.
                                                <br>Hasil: {{ $usulan->status_usulan ?? 'Status tidak tersedia' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ENHANCED: Specific actions based on final assessment result --}}
                                    @if($usulan->status_usulan === 'Perbaikan Dari Tim Penilai')
                                        {{-- Actions for correction needed --}}
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" id="btn-perbaikan-ke-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Pegawai
                                            </button>

                                            <button type="button" id="btn-perbaikan-ke-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Fakultas
                                            </button>

                                            <button type="button" id="btn-kirim-perbaikan-ke-penilai" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                                Kirim Perbaikan ke Penilai Universitas
                                            </button>

                                            <button type="button" id="btn-tidak-direkomendasikan" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-colors flex items-center gap-2">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                Tidak Direkomendasikan
                                            </button>
                                        </div>
                                    @elseif($usulan->status_usulan === 'Usulan Direkomendasi Tim Penilai')
                                        {{-- Actions for recommended usulan --}}
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" id="btn-kirim-ke-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="crown" class="w-4 h-4"></i>
                                                Kirim Ke Senat
                                            </button>

                                            <button type="button" id="btn-perbaikan-ke-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Pegawai
                                            </button>

                                            <button type="button" id="btn-perbaikan-ke-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Fakultas
                                            </button>

                                            <button type="button" id="btn-tidak-direkomendasikan" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-colors flex items-center gap-2">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                Tidak Direkomendasikan
                                            </button>
                                        </div>
                                    @else
                                        {{-- Default actions for other complete statuses --}}
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" id="btn-perbaikan-ke-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Pegawai
                                            </button>

                                            <button type="button" id="btn-perbaikan-ke-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                                Teruskan Perbaikan ke Fakultas
                                            </button>

                                            <button type="button" id="btn-tidak-direkomendasikan" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-colors flex items-center gap-2">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                Tidak Direkomendasikan
                                        </button>
                                    </div>
                                @endif
                                @elseif($totalPenilai === 0)
                                    {{-- Belum ada penilai - enhanced assignment actions --}}
                                    <div class="space-y-4">
                                        <!-- Empty State -->
                                        <div class="text-center py-8">
                                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i data-lucide="users" class="w-8 h-8 text-orange-600"></i>
                                            </div>
                                            <h4 class="text-lg font-semibold text-slate-800 mb-2">Belum Ada Tim Penilai</h4>
                                            <p class="text-slate-600 mb-6">Usulan belum memiliki tim penilai yang ditugaskan. Silakan tugaskan penilai untuk memulai proses penilaian.</p>
                                        </div>
                                        
                                        <!-- Action Card -->
                                        <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6">
                                            <div class="flex items-start gap-4">
                                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="user-plus" class="w-6 h-6 text-orange-600"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-orange-900 mb-2">Tugaskan Penilai Pertama</h5>
                                                    <p class="text-sm text-orange-800 mb-4">
                                                        Untuk memulai proses penilaian, Anda perlu menugaskan minimal satu penilai universitas. 
                                                        Penilai akan mengevaluasi usulan berdasarkan kriteria yang telah ditetapkan.
                                                    </p>
                                                    <div class="flex flex-wrap gap-2">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                                            <i data-lucide="info" class="w-4 h-4 mr-1"></i>
                                                            Required Action
                                                        </span>
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                                            Pending Assignment
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="bg-gradient-to-r from-slate-50 to-blue-50 border-t border-slate-200 px-6 py-4">
                                        <div class="flex gap-3 justify-center">
                                            <button type="button" id="btn-tugaskan-penilai" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                                <span class="font-medium">Tugaskan Penilai</span>
                                        </button>
                                            <button type="button" id="btn-simpan-validasi-bottom" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                                                <i data-lucide="save" class="w-4 h-4"></i>
                                                <span class="font-medium">Simpan Validasi</span>
                                        </button>
                                        </div>
                                    </div>
                                @else
                                    {{-- Default status information for other conditions --}}
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-4 shadow-sm">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h4 class="text-sm font-medium text-blue-900 flex items-center">
                                                    <span class="mr-2">ℹ️</span>
                                                    Status Penilaian
                                                </h4>
                                                <div class="mt-2 text-sm text-blue-800">
                                                    <strong>Progress:</strong> {{ $progressText }} penilai selesai.
                                                @if($completedPenilai > 0)
                                                        <br><strong>Status saat ini:</strong> {{ $usulan->status_usulan }}
                                                @endif
                                                    <br><span class="text-blue-600">📊 Monitoring progress penilaian usulan.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-center">
                                        <button type="button" id="btn-tambah-penilai" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                                            <span class="font-medium">Tambah Penilai Universitas</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @elseif($currentRole === 'Admin Fakultas')
                        {{-- Admin Fakultas Action Buttons --}}
                        
                        {{-- Auto Save Button for Admin Fakultas --}}
                        <button type="button" id="btn-autosave-admin-fakultas" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Validasi
                        </button>
                        
                        @if($usulan->status_usulan === 'Perbaikan Usulan')
                            {{-- Admin Fakultas Action Buttons for Perbaikan --}}
                            <button type="button" id="btn-kirim-ke-universitas" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Kirim ke Universitas
                            </button>
                        @elseif($usulan->status_usulan === 'Diajukan')
                            {{-- Admin Fakultas Action Buttons for Initial Validation --}}
                            <div class="flex flex-col sm:flex-row gap-3 w-full">
                            @if($config['canReturn'])
                                <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2 shadow-lg">
                                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                    Perbaikan ke Pegawai
                            </button>
                            @endif

                            @if($config['canForward'])
                                <button type="button" id="btn-forward" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                    Kirim ke Universitas
                            </button>
                            @endif
                                </div>
                        @endif
                    @elseif($currentRole === 'Penilai Universitas')
                        {{-- Penilai Universitas Field-by-Field Validation Section --}}
                        @if($canEdit)
                            {{-- Action Buttons for Penilai Universitas (when can edit) --}}
                            <div class="flex flex-row gap-3 mb-4 overflow-x-auto">
                                <button type="button" id="btn-autosave-penilai" class="flex-shrink-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 text-sm">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Simpan Validasi
                                    </button>
                                <button type="button" id="btn-rekomendasikan-penilai" class="flex-shrink-0 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 text-sm">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Rekomendasikan
                                    </button>
                                <button type="button" id="btn-perbaikan-penilai" class="flex-shrink-0 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2 text-sm">
                                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                    Perbaikan ke Admin Universitas
                                </button>
                                <button type="button" id="btn-kembali-penilai" class="flex-shrink-0 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2 text-sm">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    Kembali
                                    </button>
                                </div>
                        @else
                            {{-- Read-only mode for Penilai Universitas (after validation completed) --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 mr-2"></i>
                                    <span class="text-sm text-blue-800">
                                        <strong>Status:</strong> Anda telah menyelesaikan penilaian untuk usulan ini.
                                        @if(isset($penilaiIndividualStatus) && $penilaiIndividualStatus['status'] !== 'Belum Dinilai')
                                            <br>Hasil penilaian: <strong>{{ $penilaiIndividualStatus['status'] }}</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Only show Kembali button after validation completed --}}
                            <div class="flex flex-row gap-3 mb-4">
                                <button type="button" id="btn-kembali-penilai" class="flex-shrink-0 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2 text-sm">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    Kembali
                                </button>
                            </div>
                        @endif
                    @else
                        {{-- Other Roles Action Buttons (Original Logic) --}}
                        @if($config['canReturn'])
                        <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            Perbaikan ke Pegawai
                        </button>
                        @endif

                        @if($config['canForward'])
                        <button type="button" id="btn-forward-other" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
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
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i data-lucide="eye" class="w-4 h-4 mr-2 text-gray-600"></i>
                            <span class="font-medium text-gray-800">👁️ Mode tampilan detail usulan. Tidak dapat mengedit data.</span>
                        </div>
                    </div>
                </div>
                @php
                    // ENHANCED ERROR HANDLING: Safe status messages with fallbacks
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
                        'Menunggu Hasil Penilaian Tim Penilai' => [
                            'icon' => 'users',
                            'color' => 'text-orange-600',
                            'message' => 'Usulan sedang dalam proses penilaian oleh Tim Penilai. Status akan berubah otomatis setelah semua penilai selesai.'
                        ],
                        'Perbaikan Dari Tim Penilai' => [
                            'icon' => 'alert-triangle',
                            'color' => 'text-yellow-600',
                            'message' => 'Usulan telah dinilai oleh Tim Penilai dan memerlukan perbaikan. Admin Universitas akan melakukan review.'
                        ],
                        'Usulan Direkomendasi Tim Penilai' => [
                            'icon' => 'thumbs-up',
                            'color' => 'text-green-600',
                            'message' => 'Usulan telah direkomendasikan oleh Tim Penilai. Admin Universitas akan melakukan review final.'
                        ],
                        'Tidak Direkomendasikan' => [
                            'icon' => 'x-circle',
                            'color' => 'text-red-600',
                            'message' => 'Usulan tidak direkomendasikan untuk periode berjalan. Tidak dapat diajukan kembali pada periode ini.'
                            ],
                            'Menunggu Review Admin Univ' => [
                                'icon' => 'eye',
                                'color' => 'text-purple-600',
                                'message' => 'Usulan menunggu review dari Admin Universitas.'
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
                            'message' => 'Usulan ditolak. Data tidak dapat diubah.'
                            ],
                            'Perbaikan Usulan' => [
                            'icon' => 'alert-triangle',
                            'color' => 'text-yellow-600',
                            'message' => 'Usulan memerlukan perbaikan. Silakan perbaiki data yang diminta.'
                        ],
                        'Diajukan' => [
                            'icon' => 'file-text',
                            'color' => 'text-blue-600',
                            'message' => 'Usulan sudah diajukan. Menunggu review dari Admin Fakultas.'
                        ]
                    ];
                    
                    // Safe access to current status with fallback
                    $currentStatus = $usulan->status_usulan ?? 'Status tidak tersedia';
                    $statusInfo = $statusMessages[$currentStatus] ?? [
                        'icon' => 'help-circle',
                            'color' => 'text-gray-600',
                        'message' => 'Status usulan tidak dikenali. Silakan hubungi administrator.'
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
                    } elseif ($currentRole === 'Kepegawaian Universitas') {
                        $backRoute = route('backend.kepegawaian-universitas.usulan.index');
                } elseif ($currentRole === 'Penilai Universitas') {
                    $backRoute = route('penilai-universitas.pusat-usulan.index');
                    } elseif ($currentRole === 'Tim Senat') {
                        $backRoute = route('tim-senat.usulan.index');
                    } else {
                        $backRoute = route('admin-fakultas.dashboard'); // fallback
                    }
                @endphp
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
if (document.getElementById('btn-perbaikan')) {
    document.getElementById('btn-perbaikan').addEventListener('click', function() {
        // Show perbaikan modal
        showPerbaikanModal();
    });
}

// Handle btn-forward buttons (Admin Fakultas)
if (document.getElementById('btn-forward')) {
    document.getElementById('btn-forward').addEventListener('click', function() {
        console.log('Admin Fakultas btn-forward clicked');
        showForwardModal();
    });
}

// Handle btn-forward-other buttons (Other roles)
if (document.getElementById('btn-forward-other')) {
    document.getElementById('btn-forward-other').addEventListener('click', function() {
        console.log('Other role btn-forward clicked');
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

// Handler untuk button Tambah Penilai Universitas
if (document.getElementById('btn-tambah-penilai')) {
    document.getElementById('btn-tambah-penilai').addEventListener('click', function() {
        showTambahPenilaiModal();
    });
}

// Handler untuk button Tugaskan Penilai Universitas
if (document.getElementById('btn-tugaskan-penilai')) {
    document.getElementById('btn-tugaskan-penilai').addEventListener('click', function() {
        showTambahPenilaiModal();
    });
}

// Handler untuk button Simpan Validasi (top)
if (document.getElementById('btn-simpan-validasi-top')) {
    document.getElementById('btn-simpan-validasi-top').addEventListener('click', function() {
        submitAction('autosave', '');
    });
}

// Handler untuk button Simpan Validasi (bottom)
if (document.getElementById('btn-simpan-validasi-bottom')) {
    document.getElementById('btn-simpan-validasi-bottom').addEventListener('click', function() {
        submitAction('autosave', '');
    });
}

if (document.getElementById('btn-teruskan-senat')) {
    document.getElementById('btn-teruskan-senat').addEventListener('click', function() {
        if (!this.disabled) {
            showTeruskanKeSenaModal();
        }
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

function showPerbaikanModal() {
    // Implementation for perbaikan modal based on role
    const currentRole = '{{ $currentRole ?? "" }}';

    if (currentRole === 'Penilai Universitas') {
        Swal.fire({
            title: 'Perbaikan ke Admin Universitas',
            text: 'Usulan akan dikirim ke Admin Universitas untuk review. Silakan berikan catatan perbaikan yang detail.',
            input: 'textarea',
            inputPlaceholder: 'Masukkan catatan perbaikan untuk Admin Universitas...',
            inputAttributes: {
                'aria-label': 'Catatan perbaikan'
            },
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
    } else {
        console.log('Show perbaikan modal for', currentRole);
    }
}

function showForwardModal() {
    // Implementation for forward modal based on role
    const currentRole = '{{ $currentRole ?? "" }}';

    if (currentRole === 'Penilai Universitas') {
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
        console.log('Admin Fakultas showForwardModal called');
        
        Swal.fire({
            title: 'Usulkan ke Universitas',
            text: 'Usulan akan dikirim ke universitas untuk diproses selanjutnya. Semua data akan diperiksa secara fleksibel.',
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
                // Completely flexible validation - no blocking at all
                const nomorSurat = document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')?.value;
                const fileSurat = document.querySelector('input[name="dokumen_pendukung[file_surat_usulan]"]')?.files[0];
                const nomorBerita = document.querySelector('input[name="dokumen_pendukung[nomor_berita_senat]"]')?.value;
                const fileBerita = document.querySelector('input[name="dokumen_pendukung[file_berita_senat]"]')?.files[0];

                // Optional info display (not warnings)
                let info = [];
                
                if (document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')) {
                    if (!nomorSurat) {
                        info.push('• Nomor Surat Usulan belum diisi');
                    }
                    if (!fileSurat) {
                        info.push('• File Surat Usulan belum diunggah');
                    }
                    if (!nomorBerita) {
                        info.push('• Nomor Berita Senat belum diisi');
                    }
                    if (!fileBerita) {
                        info.push('• File Berita Senat belum diunggah');
                    }
                }

                // If there are missing items, show info but always allow continuation
                if (info.length > 0) {
                    Swal.showValidationMessage(
                        'Informasi:\n' + info.join('\n') + '\n\nAnda dapat melanjutkan pengiriman usulan.'
                    );
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

function showTambahPenilaiModal() {
    // Get penilais data from the page
    const penilais = @json($penilais ?? []);

    if (penilais.length === 0) {
        Swal.fire({
            title: 'Tidak Ada Penilai Tersedia',
            text: 'Tidak ada penilai yang tersedia saat ini. Silakan hubungi administrator untuk menambah data penilai.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2563eb'
        });
        return;
    }

    // Create HTML for penilai selection with enhanced design
    let penilaiHtml = '<div class="mb-6">';
    penilaiHtml += '<div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 mb-4">';
    penilaiHtml += '<div class="flex items-center">';
    penilaiHtml += '<i data-lucide="info" class="w-5 h-5 text-green-600 mr-2"></i>';
    penilaiHtml += '<span class="text-sm text-green-800"><strong>Info:</strong> Pilih penilai yang akan ditambahkan untuk usulan ini. Anda dapat memilih lebih dari satu penilai.</span>';
    penilaiHtml += '</div></div>';
    
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">';
    penilaiHtml += '<i data-lucide="users" class="w-4 h-4 mr-2 text-blue-600"></i>';
    penilaiHtml += 'Pilih Penilai Universitas (minimal 1):';
    penilaiHtml += '</label>';
    penilaiHtml += '<div class="space-y-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">';

    penilais.forEach((penilai, index) => {
        // Anonymize penilai name for non-Kepegawaian Universitas roles
        let displayName = penilai.nama_lengkap;
        if (currentRole !== 'Kepegawaian Universitas') {
            displayName = `Penilai ${index + 1}`;
        }
        
        penilaiHtml += `
            <label class="flex items-start space-x-3 cursor-pointer hover:bg-white p-3 rounded-lg border border-transparent hover:border-blue-200 transition-all duration-200">
                <input type="checkbox" name="selected_penilais[]" value="${penilai.id}" class="penilai-checkbox mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500 focus:ring-2">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                            ${displayName.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${displayName}</div>
                            ${penilai.bidang_keahlian ? `<div class="text-xs text-gray-500 mt-1"><i data-lucide="award" class="w-3 h-3 inline mr-1"></i>${penilai.bidang_keahlian}</div>` : ''}
                            ${penilai.email ? `<div class="text-xs text-gray-400"><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i>${penilai.email}</div>` : ''}
                        </div>
                    </div>
                </div>
            </label>
        `;
    });

    penilaiHtml += '</div></div>';
    penilaiHtml += '<div class="mb-4">';
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">';
    penilaiHtml += '<i data-lucide="message-square" class="w-4 h-4 mr-2 text-gray-600"></i>';
    penilaiHtml += 'Catatan untuk Tim Penilai (opsional):';
    penilaiHtml += '</label>';
    penilaiHtml += '<textarea id="catatan-penilai" placeholder="Contoh: Mohon review usulan ini dengan teliti sesuai panduan penilaian..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none" rows="3"></textarea>';
    penilaiHtml += '</div>';

    Swal.fire({
        title: '👥 Tambah Penilai Universitas',
        html: penilaiHtml,
        showCancelButton: true,
        confirmButtonText: '✅ Tambah Penilai',
        cancelButtonText: '❌ Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        width: '600px',
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'rounded-xl shadow-2xl',
            confirmButton: 'px-6 py-3 rounded-lg font-medium',
            cancelButton: 'px-6 py-3 rounded-lg font-medium'
        },
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

            submitAction('add_penilai', result.value.catatan || '');
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

    penilais.forEach((penilai, index) => {
        // Anonymize penilai name for non-Kepegawaian Universitas roles
        let displayName = penilai.nama_lengkap;
        if (currentRole !== 'Kepegawaian Universitas') {
            displayName = `Penilai ${index + 1}`;
        }
        
        penilaiHtml += `
            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" name="selected_penilais[]" value="${penilai.id}" class="penilai-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">
                    <strong>${displayName}</strong>
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

function showTeruskanKeSenaModal() {
    Swal.fire({
        title: 'Teruskan ke Tim Senat',
        text: 'Usulan akan diteruskan ke tim senat untuk keputusan final. Pastikan sudah ada rekomendasi dari tim penilai.',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk tim senat (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan untuk tim senat'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Senat',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#7c3aed'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('forward_to_senat', result.value || '');
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
    console.log('submitAction called with:', { actionType, catatan });
    
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
    
    console.log('Submitting form to:', form.action);

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
                } else if (actionType === 'forward_to_university' || actionType === 'resend_to_university') {
                    // Reload halaman setelah Admin Fakultas berhasil mengirim ke universitas
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
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

// NEW: Button handlers untuk review dari Tim Penilai
document.addEventListener('DOMContentLoaded', function() {
    // Approve Perbaikan
    const btnApprovePerbaikan = document.getElementById('btn-approve-perbaikan');
    if (btnApprovePerbaikan) {
        btnApprovePerbaikan.addEventListener('click', function() {
            Swal.fire({
                title: 'Setujui Perbaikan',
                text: 'Apakah Anda yakin ingin menyetujui hasil perbaikan dari Tim Penilai?',
                input: 'textarea',
                inputPlaceholder: 'Catatan tambahan (opsional)...',
                showCancelButton: true,
                confirmButtonText: 'Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                preConfirm: (catatan) => {
                    return catatan || '';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('approve_perbaikan', result.value);
                }
            });
        });
    }

    // Reject Perbaikan
    const btnRejectPerbaikan = document.getElementById('btn-reject-perbaikan');
    if (btnRejectPerbaikan) {
        btnRejectPerbaikan.addEventListener('click', function() {
            Swal.fire({
                title: 'Tolak Perbaikan',
                text: 'Apakah Anda yakin ingin menolak hasil perbaikan dari Tim Penilai?',
                input: 'textarea',
                inputPlaceholder: 'Alasan penolakan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                preConfirm: (catatan) => {
                    if (!catatan || catatan.trim() === '') {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi');
                        return false;
                    }
                    return catatan;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('reject_perbaikan', result.value);
                }
            });
        });
    }

    // Approve Rekomendasi
    const btnApproveRekomendasi = document.getElementById('btn-approve-rekomendasi');
    if (btnApproveRekomendasi) {
        btnApproveRekomendasi.addEventListener('click', function() {
            Swal.fire({
                title: 'Setujui Rekomendasi',
                text: 'Apakah Anda yakin ingin menyetujui rekomendasi dari Tim Penilai?',
                input: 'textarea',
                inputPlaceholder: 'Catatan tambahan (opsional)...',
                showCancelButton: true,
                confirmButtonText: 'Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#7c3aed',
                preConfirm: (catatan) => {
                    return catatan || '';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('approve_rekomendasi', result.value);
                }
            });
        });
    }

    // Reject Rekomendasi
    const btnRejectRekomendasi = document.getElementById('btn-reject-rekomendasi');
    if (btnRejectRekomendasi) {
        btnRejectRekomendasi.addEventListener('click', function() {
            Swal.fire({
                title: 'Tolak Rekomendasi',
                text: 'Apakah Anda yakin ingin menolak rekomendasi dari Tim Penilai?',
                input: 'textarea',
                inputPlaceholder: 'Alasan penolakan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                preConfirm: (catatan) => {
                    if (!catatan || catatan.trim() === '') {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi');
                        return false;
                    }
                    return catatan;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('reject_rekomendasi', result.value);
                }
            });
        });
    }

    // Tim Penilai Button Handlers
    const btnPerbaikan = document.getElementById('btn-perbaikan');
    if (btnPerbaikan) {
        btnPerbaikan.addEventListener('click', function() {
            showPerbaikanModal();
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

    // ============================================================
    // AUTO SAVE BUTTON HANDLERS FOR ALL ROLES
    // ============================================================
    
    // Autosave Button for Penilai Universitas
    const btnAutosavePenilai = document.getElementById('btn-autosave-penilai');
    if (btnAutosavePenilai) {
        btnAutosavePenilai.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Penilai Universitas') {
                // Perform autosave without catatan umum
                submitAction('autosave', '');
            }
        });
    }

    // Autosave Button for Admin Universitas


    // Autosave Button for Admin Fakultas
    const btnAutosaveAdminFakultas = document.getElementById('btn-autosave-admin-fakultas');
    if (btnAutosaveAdminFakultas) {
        btnAutosaveAdminFakultas.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Admin Fakultas') {
                // Perform autosave without catatan umum
                submitAction('autosave', '');
            }
        });
    }
    
    // Rekomendasikan Button for Penilai Universitas  
    const btnRekomendasikanPenilai = document.getElementById('btn-rekomendasikan-penilai');
    if (btnRekomendasikanPenilai) {
        btnRekomendasikanPenilai.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Penilai Universitas') {
            Swal.fire({
                title: 'Rekomendasikan Usulan',
                    text: 'Apakah Anda yakin ingin merekomendasikan usulan ini?',
                input: 'textarea',
                    inputPlaceholder: 'Catatan penilaian (opsional)...',
                showCancelButton: true,
                    confirmButtonText: 'Rekomendasikan',
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
            }
        });
    }
    
    // Perbaikan Button for Penilai Universitas
    const btnPerbaikanPenilai = document.getElementById('btn-perbaikan-penilai');
    if (btnPerbaikanPenilai) {
        btnPerbaikanPenilai.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Penilai Universitas') {
                Swal.fire({
                    title: 'Perbaikan ke Admin Universitas',
                    text: 'Apakah Anda yakin ingin mengirim usulan ini kembali ke Admin Universitas untuk perbaikan?',
                    input: 'textarea',
                    inputPlaceholder: 'Catatan perbaikan yang diperlukan...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim untuk Perbaikan',
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
            }
        });
    }
    
    // Kembali Button for Penilai Universitas
    const btnKembaliPenilai = document.getElementById('btn-kembali-penilai');
    if (btnKembaliPenilai) {
        btnKembaliPenilai.addEventListener('click', function() {
            const currentRole = '{{ $currentRole ?? "" }}';
            
            if (currentRole === 'Penilai Universitas') {
                window.location.href = '{{ route("penilai-universitas.pusat-usulan.index") }}';
            }
        });
    }
});
</script>
@endif

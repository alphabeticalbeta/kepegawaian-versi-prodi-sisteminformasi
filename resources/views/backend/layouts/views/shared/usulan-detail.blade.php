{{-- SHARED USULAN DETAIL VIEW - Can be used by all roles --}}
{{-- Usage: @include('backend.layouts.views.shared.usulan-detail', ['usulan' => $usulan, 'role' => $role]) --}}

@php
    // ENHANCED ERROR HANDLING: Safe role detection with comprehensive fallbacks
    $currentUser = auth()->user();
    $currentRole = 'Admin Fakultas'; // Default fallback
    
    if ($currentUser) {
        // Safe access to user roles
        $userRoles = $currentUser->roles ?? collect();
        $firstRole = $userRoles->first();
        $currentRole = $firstRole ? ($firstRole->name ?? 'Admin Fakultas') : 'Admin Fakultas';
    }
    
    // If role is explicitly passed, use it (with validation)
    if (isset($role) && !empty($role)) {
        $currentRole = $role;
    }
    
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
        case 'Tim Penilai':
            // Tim Penilai can edit if status is "Sedang Direview" or "Menunggu Hasil Penilaian Tim Penilai"
            // Also allow if status is "Menunggu Review Admin Univ" (when returned from Admin Univ)
            $allowedStatuses = ['Sedang Direview', 'Menunggu Hasil Penilaian Tim Penilai'];
            
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
            
            $canEdit = in_array($usulanStatus, $allowedStatuses) && $isAssigned;
            break;
        case 'Tim Senat':
            // Tim Senat can edit if status is "Direkomendasikan"
            $canEdit = $usulanStatus === 'Direkomendasikan';
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
            'description' => 'Validasi data usulan dan isi dokumen pendukung sebelum diteruskan ke penilai',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar', 'dokumen_admin_fakultas'],
            'nextStatus' => 'Sedang Direview',
            'actionButtons' => ['perbaikan_usulan', 'teruskan_ke_penilai'],
            'canForward' => true,
            'canReturn' => true,
            'routePrefix' => 'admin-univ-usulan.usulan'
        ],
        'Tim Penilai' => [
            'title' => 'Penilaian Usulan',
            'description' => 'Lakukan penilaian terhadap usulan yang telah diteruskan',
            'validationFields' => ['penilaian_umum', 'penilaian_dokumen', 'rekomendasi'],
            'nextStatus' => 'Direkomendasikan',
            'actionButtons' => ['submit_penilaian'],
            'canForward' => true,
            'canReturn' => false,
            'routePrefix' => 'tim-penilai.usulan'
        ],
        'Tim Senat' => [
            'title' => 'Validasi Senat',
            'description' => 'Validasi final usulan yang telah direkomendasikan',
            'validationFields' => ['validasi_senat'],
            'nextStatus' => 'Disetujui',
            'actionButtons' => ['approve_senat', 'reject_senat'],
            'canForward' => true,
            'canReturn' => true,
            'routePrefix' => 'tim-senat.usulan'
        ]
    ];

    $config = $roleConfigs[$currentRole] ?? $roleConfigs['Admin Fakultas'];
@endphp

{{-- Status Penilaian Section - Hanya untuk Admin Universitas Usulan --}}
@if(auth()->user()->hasRole('Admin Universitas Usulan') && isset($penilaiProgressData) && $penilaiProgressData['total_penilai'] > 0)
<div class="mb-6">
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i data-lucide="users" class="w-5 h-5 mr-2 text-blue-600"></i>
            Status Penilaian Tim Penilai
        </h3>
        
        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Progress Penilaian</span>
                <span>{{ $penilaiProgressData['completed_penilai'] }}/{{ $penilaiProgressData['total_penilai'] }} Penilai</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($penilaiProgressData['completed_penilai'] / $penilaiProgressData['total_penilai']) * 100 }}%"></div>
            </div>
        </div>

        <!-- Penilai List -->
        <div class="space-y-4">
            @foreach($penilaiProgressData['penilai_details'] as $penilai)
                @if($penilai['status'] === 'completed')
                    <!-- Completed Penilai -->
                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-green-800">{{ $penilai['nama'] }}</h4>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Selesai
                                </span>
                                @if(isset($penilai['tanggal_penilaian']))
                                <span class="text-xs text-green-600">
                                    {{ \Carbon\Carbon::parse($penilai['tanggal_penilaian'])->format('d M Y H:i') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Hasil Penilaian -->
                        @if(isset($penilai['hasil_penilaian']))
                        <div class="mb-3">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $penilai['hasil_penilaian'] === 'rekomendasi' ? 'bg-blue-100 text-blue-800' : 
                                   ($penilai['hasil_penilaian'] === 'perbaikan' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $penilai['hasil_penilaian'])) }}
                            </span>
                        </div>
                        @endif
                        
                        @if(!empty($penilai['field_tidak_sesuai']))
                            <div class="mb-3">
                                <h5 class="text-sm font-medium text-green-700 mb-2">Field yang Tidak Sesuai:</h5>
                                <ul class="text-sm text-green-600 space-y-1">
                                    @foreach($penilai['field_tidak_sesuai'] as $field)
                                        <li class="flex items-start">
                                            <i data-lucide="alert-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-600"></i>
                                            <div>
                                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                {{ $penilai['keterangan_field'][$field] ?? 'Tidak ada keterangan' }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(!empty($penilai['keterangan_umum']))
                            <div class="text-sm text-green-600">
                                <span class="font-medium">Keterangan Umum:</span>
                                {{ $penilai['keterangan_umum'] }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Pending Penilai -->
                    <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium text-yellow-800">{{ $penilai['nama'] }}</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                Masih dalam proses penilaian
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Auto-refresh script - hanya jika ada penilai yang belum selesai -->
@if($penilaiProgressData['completed_penilai'] < $penilaiProgressData['total_penilai'])
<script>
setInterval(function() {
    location.reload();
}, 30000); // 30 detik
</script>
@endif
@endif

{{-- ENHANCED: Consistency Check Visual Feedback --}}
@if(isset($consistencyCheck) && $consistencyCheck['has_issues'] || $consistencyCheck['has_warnings'] || $consistencyCheck['has_corrections'])
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-900 flex items-center">
                    <i data-lucide="shield-check" class="w-4 h-4 mr-2 text-blue-600"></i>
                    Data Integrity Check
                </h3>
                <div class="text-xs text-gray-500">
                    {{ $consistencyCheck['checks_passed'] }}/{{ $consistencyCheck['total_checks'] }} checks passed
                </div>
            </div>

            @if($consistencyCheck['has_corrections'])
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-green-800">Auto-Corrections Applied</h4>
                            <ul class="mt-2 text-sm text-green-700 space-y-1">
                                @foreach($consistencyCheck['corrections'] as $correction)
                                    <li class="flex items-start">
                                        <i data-lucide="check" class="w-3 h-3 mr-2 mt-0.5 text-green-600"></i>
                                        {{ $correction }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if($consistencyCheck['has_warnings'])
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-yellow-800">Warnings Detected</h4>
                            <ul class="mt-2 text-sm text-yellow-700 space-y-1">
                                @foreach($consistencyCheck['warnings'] as $warning)
                                    <li class="flex items-start">
                                        <i data-lucide="info" class="w-3 h-3 mr-2 mt-0.5 text-yellow-600"></i>
                                        {{ $warning }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if($consistencyCheck['has_issues'])
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-red-800">Issues Found</h4>
                            <ul class="mt-2 text-sm text-red-700 space-y-1">
                                @foreach($consistencyCheck['issues'] as $issue)
                                    <li class="flex items-start">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-2 mt-0.5 text-red-600"></i>
                                        {{ $issue }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-3 text-xs text-red-600">
                                <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                Please contact system administrator if issues persist.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif


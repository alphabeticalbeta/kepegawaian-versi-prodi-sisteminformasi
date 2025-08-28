{{-- USULAN DETAIL ACTIONS PARTIAL --}}
{{-- Usage: @include('backend.layouts.views.shared.partials._usulan-detail-actions', ['usulan' => $usulan, 'config' => $config, 'canEdit' => $canEdit, 'currentRole' => $currentRole]) --}}

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
                            </div>
                        </div>
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
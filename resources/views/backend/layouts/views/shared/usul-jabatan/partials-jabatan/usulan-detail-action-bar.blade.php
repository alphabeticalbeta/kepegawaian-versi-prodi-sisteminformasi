{{-- Action Bar: View-only for certain roles, Edit mode for others --}}
@if($canEdit)
<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="text-sm text-gray-600">
            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
            Perubahan validasi tersimpan otomatis. Gunakan tombol berikut untuk melanjutkan proses.
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <input type="hidden" name="action_type" id="action_type" value="save_only">
            <input type="hidden" name="catatan_umum" id="catatan_umum" value="">

            @if($currentRole === 'Kepegawaian Universitas')
                {{-- Admin Universitas Action Buttons --}}
                

                
                {{-- ENHANCED: Specific status-based action buttons with improved logic --}}
                @if($usulan->status_usulan === 'Usulan Disetujui Admin Fakultas')
                    {{-- Initial validation buttons - only for new submissions --}}
                    <div class="flex flex-col lg:flex-row gap-4 w-full">
                        <div class="flex flex-col sm:flex-row gap-3 flex-1">
                            <button type="button" id="btn-perbaikan-pegawai" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                <span class="font-medium">Perbaikan ke Pegawai</span>
                            </button>

                            <button type="button" id="btn-perbaikan-fakultas" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                <span class="font-medium">Perbaikan ke Fakultas</span>
                            </button>

                            <button type="button" id="btn-teruskan-penilai" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                <span class="font-medium">Teruskan ke Penilai</span>
                            </button>

                            <button type="button" id="btn-tidak-direkomendasikan" class="px-6 py-3 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                <span class="font-medium">Tidak Direkomendasikan</span>
                            </button>
                        </div>
                    </div>
                @endif

                @if($usulan->status_usulan === 'Usulan Direkomendasikan oleh Tim Senat')
                    {{-- Forward to Senat button - only when recommended --}}
                    <div class="flex flex-col lg:flex-row gap-4 w-full">
                        <div class="flex flex-col sm:flex-row gap-3 flex-1">
                            <button type="button" id="btn-teruskan-senat" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="crown" class="w-4 h-4"></i>
                                <span class="font-medium">Teruskan ke Senat</span>
                            </button>
                        </div>
                    </div>
                @endif

                @if($usulan->status_usulan === 'Usulan Sudah Dikirim ke Sister')
                    {{-- Sister processing status --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="send" class="w-4 h-4 text-blue-600 mr-2"></i>
                            <span class="text-sm text-blue-800">
                                <strong>Status:</strong> Usulan telah dikirim ke Tim Sister untuk verifikasi final.
                            </span>
                        </div>
                    </div>
                @endif

                @if($usulan->status_usulan === 'Permintaan Perbaikan Usulan dari Tim Sister')
                    {{-- Sister correction request --}}
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mr-2"></i>
                            <span class="text-sm text-red-800">
                                <strong>Status:</strong> Tim Sister meminta perbaikan pada usulan ini.
                            </span>
                        </div>
                    </div>
                @endif

                {{-- ENHANCED: Tim Penilai Assessment Status with specific conditions --}}
                @if(in_array($usulan->status_usulan, ['Usulan Disetujui Kepegawaian Universitas', 'Permintaan Perbaikan dari Penilai Universitas', 'Usulan Direkomendasi dari Penilai Universitas']))
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
                            <div class="flex items-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 mr-2"></i>
                                <div class="text-sm text-green-800">
                                    <strong>Status:</strong> Semua penilai telah menyelesaikan penilaian.
                                    <br>Hasil: {{ $usulan->status_usulan ?? 'Status tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        {{-- ENHANCED: Specific actions based on final assessment result --}}
                        @if($usulan->status_usulan === 'Permintaan Perbaikan dari Penilai Universitas')
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
                        @elseif($usulan->status_usulan === 'Usulan Direkomendasi dari Penilai Universitas')
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
            
            @if($usulan->status_usulan === 'Usulan Perbaikan dari Admin Fakultas' || $usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas')
                {{-- Admin Fakultas Action Buttons for Perbaikan --}}
                @if($usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mr-2"></i>
                            <span class="text-sm text-red-800">
                                <strong>Status:</strong> Kepegawaian Universitas meminta perbaikan pada usulan ini.
                            </span>
                        </div>
                    </div>
                @endif
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-ke-universitas" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim ke Universitas</span>
                        </button>
                    </div>
                    
                    {{-- Auto Save Button positioned on the right --}}
                    <div class="flex justify-center lg:justify-end">
                        <button type="button" id="btn-autosave-admin-fakultas" class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span class="font-medium">Simpan Validasi</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Usulan Dikirim ke Admin Fakultas')
                {{-- Admin Fakultas Action Buttons for Initial Validation --}}
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        @if(isset($config['canReturn']) && $config['canReturn'])
                            <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                <span class="font-medium">Perbaikan ke Pegawai</span>
                            </button>
                        @endif

                        @if(isset($config['canForward']) && $config['canForward'])
                            <button type="button" id="btn-forward" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span class="font-medium">Kirim ke Universitas</span>
                            </button>
                        @endif
                    </div>
                    
                    {{-- Auto Save Button positioned on the right --}}
                    <div class="flex justify-center lg:justify-end">
                        <button type="button" id="btn-autosave-admin-fakultas" class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span class="font-medium">Simpan Validasi</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Penilai Universitas')
                {{-- Admin Fakultas Action Buttons for Penilai Universitas Correction --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 mr-2"></i>
                        <span class="text-sm text-orange-800">
                            <strong>Status:</strong> Penilai Universitas meminta perbaikan pada usulan ini.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-ke-universitas" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim ke Universitas</span>
                        </button>
                    </div>
                    
                    {{-- Auto Save Button positioned on the right --}}
                    <div class="flex justify-center lg:justify-end">
                        <button type="button" id="btn-autosave-admin-fakultas" class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span class="font-medium">Simpan Validasi</span>
                        </button>
                    </div>
                </div>
            @endif
        @elseif($currentRole === 'Tim Senat')
            {{-- Tim Senat Action Buttons --}}
            @if($usulan->status_usulan === 'Usulan Direkomendasikan oleh Tim Senat')
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="crown" class="w-4 h-4 text-purple-600 mr-2"></i>
                        <span class="text-sm text-purple-800">
                            <strong>Status:</strong> Usulan telah direkomendasikan oleh Tim Senat.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-ke-sister" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim ke Sister</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Usulan Sudah Dikirim ke Sister')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="send" class="w-4 h-4 text-blue-600 mr-2"></i>
                        <span class="text-sm text-blue-800">
                            <strong>Status:</strong> Usulan telah dikirim ke Tim Sister untuk verifikasi final.
                        </span>
                    </div>
                </div>
            @endif
        @elseif($currentRole === 'Pegawai')
            {{-- Pegawai Action Buttons --}}
            @if($usulan->status_usulan === 'Usulan Perbaikan dari Admin Fakultas')
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 mr-2"></i>
                        <span class="text-sm text-amber-800">
                            <strong>Status:</strong> Admin Fakultas meminta perbaikan pada usulan ini.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-perbaikan-admin-fakultas" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim Perbaikan</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mr-2"></i>
                        <span class="text-sm text-red-800">
                            <strong>Status:</strong> Kepegawaian Universitas meminta perbaikan pada usulan ini.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-perbaikan-kepegawaian" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim Perbaikan</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Penilai Universitas')
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 mr-2"></i>
                        <span class="text-sm text-orange-800">
                            <strong>Status:</strong> Penilai Universitas meminta perbaikan pada usulan ini.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-perbaikan-penilai" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim Perbaikan</span>
                        </button>
                    </div>
                </div>
            @elseif($usulan->status_usulan === 'Permintaan Perbaikan Usulan dari Tim Sister')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mr-2"></i>
                        <span class="text-sm text-red-800">
                            <strong>Status:</strong> Tim Sister meminta perbaikan pada usulan ini.
                        </span>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kirim-perbaikan-sister" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span class="font-medium">Kirim Perbaikan</span>
                        </button>
                    </div>
                </div>
            @endif
        @elseif($currentRole === 'Penilai Universitas')
            {{-- Penilai Universitas Field-by-Field Validation Section --}}
            @if($canEdit)
                {{-- Action Buttons for Penilai Universitas (when can edit) --}}
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1 overflow-x-auto">
                        <button type="button" id="btn-autosave-penilai" class="flex-shrink-0 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span class="font-medium">Simpan Validasi</span>
                        </button>
                        <button type="button" id="btn-rekomendasikan-penilai" class="flex-shrink-0 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span class="font-medium">Rekomendasikan</span>
                        </button>
                        <button type="button" id="btn-perbaikan-penilai" class="flex-shrink-0 px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            <span class="font-medium">Perbaikan ke Admin Universitas</span>
                        </button>
                        <button type="button" id="btn-tidak-rekomendasikan-penilai" class="flex-shrink-0 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span class="font-medium">Tidak Rekomendasikan</span>
                        </button>
                        <button type="button" id="btn-kembali-penilai" class="flex-shrink-0 px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span class="font-medium">Kembali</span>
                        </button>
                    </div>
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
                
                {{-- Only show Kembali button for completed assessment --}}
                <div class="flex flex-col lg:flex-row gap-4 w-full">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1">
                        <button type="button" id="btn-kembali-penilai-readonly" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span class="font-medium">Kembali</span>
                        </button>
                    </div>
                </div>
            @endif
        @endif
        </div>
    </div>
</div>
@endif

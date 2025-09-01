{{-- Tim Penilai Assessment Progress Section --}}
@if($currentRole === 'Kepegawaian Universitas' && in_array($usulan->status_usulan, [\App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS, \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS, \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS]))
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
                    <button type="button" onclick="showAssessorListModal()" class="bg-blue-50 rounded-lg p-2 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors">
                        <div class="text-lg font-bold text-blue-600">{{ $totalPenilai }}</div>
                        <div class="text-xs text-blue-700 underline">Total Penilai</div>
                    </button>
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
                                {{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS }}
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

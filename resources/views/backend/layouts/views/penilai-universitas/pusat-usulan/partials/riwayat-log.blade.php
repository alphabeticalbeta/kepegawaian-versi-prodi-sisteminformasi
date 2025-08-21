{{-- Riwayat Log Component - Menampilkan history perubahan usulan --}}
<div class="bg-white shadow-md rounded-lg mt-6">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            Riwayat Usulan
        </h3>
    </div>

    <ul class="divide-y divide-gray-200">
        @forelse ($logs as $log)
            <li class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-3">
                    {{-- Timeline Icon --}}
                    <div class="flex-shrink-0">
                        @php
                            $iconColor = match($log->status_baru) {
                                'Diajukan' => 'text-blue-600 bg-blue-100',
                                'Sedang Direview' => 'text-yellow-600 bg-yellow-100',
                                'Perlu Perbaikan' => 'text-orange-600 bg-orange-100',
                                'Dikembalikan' => 'text-red-600 bg-red-100',
                                'Diteruskan Ke Universitas' => 'text-purple-600 bg-purple-100',
                                'Disetujui' => 'text-green-600 bg-green-100',
                                'Direkomendasikan' => 'text-emerald-600 bg-emerald-100',
                                'Ditolak' => 'text-red-600 bg-red-100',
                                default => 'text-gray-600 bg-gray-100'
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-full {{ $iconColor }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd">
                                </path>
                            </svg>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-gray-900">
                                @if($log->status_lama)
                                    {{ $log->status_lama }} → {{ $log->status_baru }}
                                @else
                                    {{ $log->status_baru }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-400" title="{{ $log->created_at->format('d F Y, H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <p class="text-sm text-gray-600 mt-1">
                            Oleh:
                            <span class="font-medium">
                                {{ $log->dilakukanOleh->nama_lengkap ?? 'Sistem' }}
                            </span>
                            @if($log->dilakukanOleh && $log->dilakukanOleh->roles->isNotEmpty())
                                <span class="text-xs text-gray-500">
                                    ({{ $log->dilakukanOleh->roles->first()->name }})
                                </span>
                            @endif
                        </p>

                        @if($log->catatan)
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <p class="text-sm font-medium text-yellow-800 mb-1">Catatan:</p>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $log->catatan }}</p>
                            </div>
                        @endif

                        {{-- Additional metadata if available --}}
                        @if($log->metadata && is_array($log->metadata))
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($log->metadata as $key => $value)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </li>
        @empty
            <li class="p-6 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <p class="text-sm text-gray-500">Belum ada riwayat untuk usulan ini.</p>
                </div>
            </li>
        @endforelse
    </ul>
</div>

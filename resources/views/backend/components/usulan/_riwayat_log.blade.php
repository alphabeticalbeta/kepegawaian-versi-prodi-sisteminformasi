{{-- Shared: Riwayat Perubahan/Log Usulan --}}
{{-- Expect: $usulan (punya relasi logs) --}}
@php
    $logs = $usulan->logs ?? collect();
@endphp

<div class="bg-white shadow-md rounded-lg mt-6">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800">Riwayat Perubahan</h3>
        <p class="text-sm text-gray-500">Jejak status dan catatan perubahan usulan.</p>
    </div>

    <div class="p-6">
        @if($logs->isEmpty())
            <p class="text-gray-500 text-sm">Belum ada riwayat.</p>
        @else
            <ul class="space-y-4">
                @foreach($logs as $log)
                    <li class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800">
                                {{ $log->status ?? '-' }}
                                @if(!empty($log->status_sebelumnya))
                                    <span class="text-xs text-gray-500">
                                        (dari: {{ $log->status_sebelumnya }})
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}
                            </div>
                        </div>
                        @if(!empty($log->catatan))
                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                {{ $log->catatan }}
                            </div>
                        @endif
                        @if(method_exists($log, 'dilakukanOleh') && $log->relationLoaded('dilakukanOleh') || !empty($log->dilakukanOleh))
                            <div class="mt-2 text-xs text-gray-500">
                                Dilakukan oleh:
                                {{ optional($log->dilakukanOleh)->nama_lengkap ?? '-' }}
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Usulan - {{ $usulan->jenis_usulan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
        <style>
        /* Custom styles for text overflow handling without ellipsis */
        .text-field {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
            transition: all 0.2s ease-in-out;
        }

        .text-field::-webkit-scrollbar {
            height: 4px;
        }

        .text-field::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 2px;
        }

        .text-field::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 2px;
        }

        .text-field::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Hover effect for better readability */
        .text-field:hover {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            margin: -0.25rem -0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Ensure icons don't shrink */
        .flex-shrink-0 {
            flex-shrink: 0;
        }

        /* Better overflow handling for status badges */
        .status-badge {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .status-badge::-webkit-scrollbar {
            height: 3px;
        }

        .status-badge::-webkit-scrollbar-track {
            background: transparent;
        }

        .status-badge::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 2px;
        }

        /* Responsive text sizing */
        @media (max-width: 768px) {
            .text-lg {
                font-size: 1rem;
            }
            .status-badge {
                max-width: 150px;
            }
        }

        /* Long text handling */
        .long-text {
            overflow-x: auto;
            white-space: nowrap;
            word-break: keep-all;
        }

        /* Container for better text display */
        .text-container {
            min-width: 0;
            flex: 1;
        }

        /* Auto-fit wrapping without ellipsis for specific fields */
        .auto-fit {
            white-space: normal;
            overflow: visible;
            word-break: break-word;
            width: fit-content;
            min-width: 0;
        }

        /* Container for auto-fit fields */
        .auto-fit-container {
            width: auto;
            min-width: 0;
            flex: 1;
        }

        /* Single-line, auto-width fields (no wrap, no ellipsis) for Data Diri Pegawai */
        .auto-line {
            white-space: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            width: fit-content;
            display: inline-block;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .auto-line::-webkit-scrollbar {
            height: 4px;
        }
        .auto-line::-webkit-scrollbar-track {
            background: #f7fafc;
        }
        .auto-line::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 2px;
        }

        .auto-line-card {
            width: fit-content;
            max-width: 100%;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8 mb-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-indigo-600/5"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Riwayat Log Usulan
                        </h1>
                        <p class="text-gray-600 mt-2 text-lg">
                            {{ $usulan->jenis_usulan }} - {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                        </p>
                    </div>
                    <button onclick="window.close()" class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                        Tutup
                    </button>
                </div>
            </div>

            <!-- Data Diri Pegawai -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8 mb-8 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    Data Diri Pegawai
                </h2>
                <div class="flex flex-wrap gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100 auto-line-card">
                        <p class="text-sm text-blue-700 font-medium mb-1">Nama Lengkap</p>
                        <p class="font-semibold text-gray-900 text-lg auto-line" title="{{ $usulan->pegawai->nama_lengkap }}">{{ $usulan->pegawai->nama_lengkap }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100 auto-line-card">
                        <p class="text-sm text-green-700 font-medium mb-1">NIP</p>
                        <p class="font-semibold text-gray-900 text-lg auto-line" title="{{ $usulan->pegawai->nip }}">{{ $usulan->pegawai->nip }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl p-5 border border-purple-100 auto-line-card">
                        <p class="text-sm text-purple-700 font-medium mb-1">Jenis Pegawai</p>
                        <p class="font-semibold text-gray-900 text-lg auto-line" title="{{ $usulan->pegawai->jenis_pegawai }}">{{ $usulan->pegawai->jenis_pegawai }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-100 auto-line-card">
                        <p class="text-sm text-orange-700 font-medium mb-1">Status Kepegawaian</p>
                        <p class="font-semibold text-gray-900 text-lg auto-line" title="{{ $usulan->pegawai->status_kepegawaian }}">{{ $usulan->pegawai->status_kepegawaian }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100 auto-line-card">
                        <p class="text-sm text-teal-700 font-medium mb-1">Email</p>
                        <p class="font-semibold text-gray-900 text-lg auto-line" title="{{ $usulan->pegawai->email }}">{{ $usulan->pegawai->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Usulan -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8 mb-8 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                    </div>
                    Informasi Usulan
                </h2>
                <div class="flex flex-wrap gap-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-4 border border-indigo-100 auto-fit-container">
                        <p class="text-sm text-indigo-700 font-medium mb-1">Jenis Usulan</p>
                        <p class="font-semibold text-gray-900 text-lg auto-fit" title="{{ $usulan->jenis_usulan }}">{{ $usulan->jenis_usulan }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-4 border border-violet-100 auto-fit-container">
                        <p class="text-sm text-violet-700 font-medium mb-1">Periode Usulan</p>
                        <p class="font-semibold text-gray-900 text-lg auto-fit" title="{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}">{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-xl p-4 border border-rose-100 auto-fit-container">
                        <p class="text-sm text-rose-700 font-medium mb-1">Status Usulan</p>
                        <p class="font-medium">
                            <span class="px-3 py-2 text-sm font-bold rounded-xl shadow-sm inline-flex items-center max-w-full status-badge
                                @if($usulan->status_usulan === 'Draft Usulan') bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300
                                @elseif($usulan->status_usulan === 'Usulan Dikirim ke Admin Fakultas') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                                @elseif($usulan->status_usulan === 'Usulan Disetujui Admin Fakultas') bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                                @elseif($usulan->status_usulan === 'Usulan Tidak Direkomendasi Admin Fakultas') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                @elseif($usulan->status_usulan === 'Permintaan Perbaikan dari Admin Fakultas') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300
                                @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Admin Fakultas') bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300
                                @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Penilai Universitas') bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300
                                @elseif($usulan->status_usulan === 'Usulan Direkomendasikan oleh Tim Senat') bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300
                                @elseif($usulan->status_usulan === 'Usulan Sudah Dikirim ke Sister') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                                @elseif($usulan->status_usulan === 'Permintaan Perbaikan Usulan dari Tim Sister') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300
                                @endif" title="{{ $usulan->status_usulan }}">
                                {{ $usulan->status_usulan }}
                            </span>
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-100 auto-fit-container">
                        <p class="text-sm text-emerald-700 font-medium mb-1">Tanggal Pengajuan</p>
                        <p class="font-semibold text-gray-900 text-lg auto-fit" title="{{ $usulan->created_at->format('d F Y, H:i') }}">{{ $usulan->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Keterangan Usulan dari mana ke mana -->
                @if($usulan->jenis_usulan === 'Usulan Jabatan')
                    <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 border border-blue-200 rounded-2xl shadow-lg">
                        <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="arrow-right-left" class="w-4 h-4 text-white"></i>
                            </div>
                            Keterangan Usulan Jabatan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/70 rounded-xl p-4 border border-blue-100 shadow-sm">
                                <p class="text-sm text-blue-700 font-semibold mb-2">Jabatan Saat Ini</p>
                                <p class="text-lg font-bold text-blue-900" title="{{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}">
                                    {{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}
                                </p>
                            </div>
                            <div class="bg-white/70 rounded-xl p-4 border border-blue-100 shadow-sm">
                                <p class="text-sm text-blue-700 font-semibold mb-2">Jabatan yang Dituju</p>
                                <p class="text-lg font-bold text-blue-900" title="{{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}">
                                    {{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}
                                </p>
                            </div>
                        </div>
                        @if($usulan->jabatanLama && $usulan->jabatanTujuan)
                            <div class="mt-4 text-center">
                                <div class="inline-flex items-center bg-white/80 rounded-full px-6 py-3 shadow-lg border border-blue-200 max-w-full overflow-hidden">
                                    <span class="text-sm text-blue-800 font-bold" title="{{ $usulan->jabatanLama->jabatan }}">
                                        {{ $usulan->jabatanLama->jabatan }}
                                    </span>
                                    <i data-lucide="arrow-right" class="w-5 h-5 text-blue-600 mx-3 flex-shrink-0"></i>
                                    <span class="text-sm text-blue-800 font-bold" title="{{ $usulan->jabatanTujuan->jabatan }}">
                                        {{ $usulan->jabatanTujuan->jabatan }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($usulan->jenis_usulan === 'Usulan Kepangkatan')
                    <div class="mt-8 p-6 bg-gradient-to-r from-green-50 via-emerald-50 to-green-50 border border-green-200 rounded-2xl shadow-lg">
                        <h3 class="text-lg font-bold text-green-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="arrow-right-left" class="w-4 h-4 text-white"></i>
                            </div>
                            Keterangan Usulan Kepangkatan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/70 rounded-xl p-4 border border-green-100 shadow-sm">
                                <p class="text-sm text-green-700 font-semibold mb-2">Pangkat Saat Ini</p>
                                <p class="text-lg font-bold text-green-900" title="{{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}">
                                    {{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}
                                </p>
                            </div>
                            <div class="bg-white/70 rounded-xl p-4 border border-green-100 shadow-sm">
                                <p class="text-sm text-green-700 font-semibold mb-2">Pangkat yang Dituju</p>
                                <p class="text-lg font-bold text-green-900" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}">
                                    {{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}
                                </p>
                            </div>
                        </div>
                        @if(isset($usulan->data_usulan['pangkat_saat_ini']) && isset($usulan->data_usulan['pangkat_yang_dituju']))
                            <div class="mt-4 text-center">
                                <div class="inline-flex items-center bg-white/80 rounded-full px-6 py-3 shadow-lg border border-green-200 max-w-full overflow-hidden">
                                    <span class="text-sm text-green-800 font-bold" title="{{ $usulan->data_usulan['pangkat_saat_ini'] }}">
                                        {{ $usulan->data_usulan['pangkat_saat_ini'] }}
                                    </span>
                                    <i data-lucide="arrow-right" class="w-5 h-5 text-green-600 mx-3 flex-shrink-0"></i>
                                    <span class="text-sm text-green-800 font-bold" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] }}">
                                        {{ $usulan->data_usulan['pangkat_yang_dituju'] }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-8 p-6 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 border border-gray-200 rounded-2xl shadow-lg">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-500 to-slate-500 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="info" class="w-4 h-4 text-white"></i>
                            </div>
                            Informasi Usulan
                        </h3>
                        <p class="text-lg text-gray-700 font-medium">
                            {{ $usulan->jenis_usulan }} - Periode {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Log Content -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all duration-300">
                @if(count($logs) > 0)
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                            </div>
                            {{ count($logs) }} Entri Log
                        </h2>

                        <div class="space-y-6">
                            @foreach($logs as $log)
                                @php
                                    $isStatusChange = $log['status_sebelumnya'] !== null && $log['status_sebelumnya'] !== $log['status_baru'];
                                    $statusChangeClass = $isStatusChange ? 'bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 border-blue-300 shadow-lg' : 'bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 border-gray-300 shadow-md';
                                    $statusIcon = $isStatusChange ? 'refresh-cw' : 'file-text';
                                    $iconBg = $isStatusChange ? 'bg-gradient-to-r from-blue-500 to-indigo-500' : 'bg-gradient-to-r from-gray-500 to-slate-500';

                                    $getStatusBadgeClass = function($status) {
                                        return match($status) {
                                            'Draft' => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-300',
                                            'Diajukan' => 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border-blue-300',
                                            'Diterima' => 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border-green-300',
                                            'Ditolak' => 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border-red-300',
                                            'Perlu Perbaikan' => 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-300',
                                            'Dikembalikan ke Pegawai' => 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border-orange-300',
                                            default => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-300'
                                        };
                                    };
                                @endphp

                                <div class="border rounded-2xl p-6 {{ $statusChangeClass }} hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 {{ $iconBg }} rounded-xl flex items-center justify-center shadow-lg">
                                                    <i data-lucide="{{ $statusIcon }}" class="w-5 h-5 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-lg font-bold text-gray-900 leading-relaxed" title="{{ $log['keterangan'] }}">{{ $log['keterangan'] }}</p>

                                                @if($isStatusChange)
                                                    <div class="mt-4 flex items-center space-x-3">
                                                        <span class="text-sm text-gray-600 font-semibold">Perubahan Status:</span>
                                                        <div class="flex items-center space-x-3 max-w-full overflow-hidden">
                                                            <span class="text-sm px-3 py-2 rounded-xl border font-bold bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 border-gray-300 shadow-sm status-badge" title="{{ $log['status_sebelumnya'] ?? 'N/A' }}">
                                                                {{ $log['status_sebelumnya'] ?? 'N/A' }}
                                                            </span>
                                                            <div class="w-8 h-8 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                                                                <i data-lucide="arrow-right" class="w-4 h-4 text-white"></i>
                                                            </div>
                                                            <span class="text-sm px-3 py-2 rounded-xl border font-bold shadow-sm {{ $getStatusBadgeClass($log['status_baru']) }} status-badge" title="{{ $log['status_baru'] }}">
                                                                {{ $log['status_baru'] }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="mt-4 flex items-center space-x-6 text-sm text-gray-600">
                                                    <div class="flex items-center space-x-2">
                                                        <i data-lucide="user" class="w-4 h-4"></i>
                                                        <span class="font-semibold">{{ $log['user_name'] }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                                        <span class="font-medium">{{ $log['formatted_date'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-16 text-center">
                        <div class="w-20 h-20 bg-gradient-to-r from-gray-300 to-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i data-lucide="file-text" class="w-10 h-10 text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">Belum ada log</h3>
                        <p class="text-lg text-gray-400">Belum ada riwayat log untuk usulan ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Auto close after 30 seconds of inactivity
        let inactivityTimer;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.close();
            }, 30000); // 30 seconds
        }

        // Reset timer on user activity
        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
        document.addEventListener('click', resetInactivityTimer);

        // Start timer
        resetInactivityTimer();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Usulan - {{ $usulan->jenis_usulan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* Reset and Base Styles */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            background: linear-gradient(to bottom right, #f0f4f8, #e6f0fa, #e0e7ff);
            min-height: 100vh;
            color: #1f2937;
        }

        /* Scrollbar Styles */
        .scrollable {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #f1f5f9;
        }

        .scrollable::-webkit-scrollbar {
            height: 6px;
        }

        .scrollable::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .scrollable::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 3px;
        }

        .scrollable::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Card Hover Effects */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 transparent;
        }

        .status-badge::-webkit-scrollbar {
            height: 4px;
        }

        .status-badge::-webkit-scrollbar-track {
            background: transparent;
        }

        .status-badge::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 2px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .text-xl {
                font-size: 1.125rem;
            }

            .status-badge {
                max-width: 120px;
            }

            .grid-cols-5 {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-change {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            0% {
                opacity: 0;
                transform: translateX(-10px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Focus States for Accessibility */
        button:focus, .card:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Utility Classes */
        .auto-fit {
            white-space: normal;
            word-break: break-word;
            width: fit-content;
            max-width: 100%;
        }

        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/5"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="icon-container w-10 h-10 bg-white/20 rounded-lg shadow-md">
                        <i data-lucide="activity" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white drop-shadow-md">
                            Riwayat Log Usulan
                        </h1>
                        <div class="flex flex-wrap gap-3 mt-3">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium text-white">
                                {{ $usulan->jenis_usulan }}
                            </span>
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium text-white">
                                {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <button onclick="window.close()" class="px-6 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-200 shadow-md hover:shadow-lg font-medium border border-white/20" aria-label="Tutup halaman">
                    <i data-lucide="x" class="w-4 h-4 mr-2 inline"></i>
                    Tutup
                </button>
            </div>
        </header>

        <!-- Data Diri Pegawai -->
        <section class="bg-white/90 rounded-2xl shadow-lg p-6 mb-8 card">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="icon-container w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg shadow-md mr-4">
                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                </div>
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Data Diri Pegawai
                </span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach([
                    ['label' => 'Nama Lengkap', 'value' => $usulan->pegawai->nama_lengkap, 'icon' => 'user', 'color' => 'blue'],
                    ['label' => 'NIP', 'value' => $usulan->pegawai->nip, 'icon' => 'id-card', 'color' => 'green'],
                    ['label' => 'Jenis Pegawai', 'value' => $usulan->pegawai->jenis_pegawai, 'icon' => 'briefcase', 'color' => 'purple'],
                    ['label' => 'Status Kepegawaian', 'value' => $usulan->pegawai->status_kepegawaian, 'icon' => 'award', 'color' => 'orange'],
                    ['label' => 'Email', 'value' => $usulan->pegawai->email, 'icon' => 'mail', 'color' => 'teal']
                ] as $field)
                    <div class="card bg-white/80 rounded-lg p-5 border border-{{ $field['color'] }}-100 hover:border-{{ $field['color'] }}-200">
                        <div class="flex items-center mb-3">
                            <div class="icon-container w-8 h-8 bg-gradient-to-r from-{{ $field['color'] }}-500 to-{{ $field['color'] }}-600 rounded-md mr-3">
                                <i data-lucide="{{ $field['icon'] }}" class="w-4 h-4 text-white"></i>
                            </div>
                            <p class="text-sm font-medium text-{{ $field['color'] }}-700">{{ $field['label'] }}</p>
                        </div>
                        <p class="text-base font-semibold text-gray-900 scrollable" title="{{ $field['value'] }}">{{ $field['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Informasi Usulan -->
        <section class="bg-white/90 rounded-2xl shadow-lg p-6 mb-8 card">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="icon-container w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg shadow-md mr-4">
                    <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                </div>
                Informasi Usulan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['label' => 'Jenis Usulan', 'value' => $usulan->jenis_usulan, 'color' => 'indigo'],
                    ['label' => 'Periode Usulan', 'value' => $usulan->periodeUsulan->nama_periode ?? 'N/A', 'color' => 'violet'],
                    ['label' => 'Tanggal Pengajuan', 'value' => $usulan->created_at->format('d F Y, H:i'), 'color' => 'emerald']
                ] as $info)
                    <div class="bg-gradient-to-br from-{{ $info['color'] }}-50 to-{{ $info['color'] }}-100 rounded-lg p-5 border border-{{ $info['color'] }}-100">
                        <p class="text-sm font-medium text-{{ $info['color'] }}-700 mb-1">{{ $info['label'] }}</p>
                        <p class="text-base font-semibold text-gray-900 auto-fit" title="{{ $info['value'] }}">{{ $info['value'] }}</p>
                    </div>
                @endforeach
                <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg p-5 border border-rose-100">
                    <p class="text-sm font-medium text-rose-700 mb-1">Status Usulan</p>
                    <p class="font-medium">
                        <span class="status-badge px-3 py-1 text-sm font-semibold rounded-lg shadow-sm
                            @if($usulan->status_usulan === 'Draft Usulan') bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300
                            @elseif($usulan->status_usulan === 'Usulan Dikirim ke Admin Fakultas') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                            @elseif($usulan->status_usulan === 'Usulan Disetujui Admin Fakultas') bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                            @elseif($usulan->status_usulan === 'Usulan Tidak Direkomendasi Admin Fakultas') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                            @elseif($usulan->status_usulan === 'Permintaan Perbaikan dari Admin Fakultas') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300
                            @elseif($usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas') bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300
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
            </div>

            <!-- Keterangan Usulan -->
            @if($usulan->jenis_usulan === 'Usulan Jabatan')
                <div class="mt-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 card">
                    <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                        <div class="icon-container w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-md mr-3">
                            <i data-lucide="arrow-right-left" class="w-4 h-4 text-white"></i>
                        </div>
                        Keterangan Usulan Jabatan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-lg p-5 border border-blue-100">
                            <p class="text-sm font-medium text-blue-700 mb-2">Jabatan Saat Ini</p>
                            <p class="text-base font-semibold text-blue-900 auto-fit" title="{{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}">
                                {{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}
                            </p>
                        </div>
                        <div class="bg-white/80 rounded-lg p-5 border border-blue-100">
                            <p class="text-sm font-medium text-blue-700 mb-2">Jabatan yang Dituju</p>
                            <p class="text-base font-semibold text-blue-900 auto-fit" title="{{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}">
                                {{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}
                            </p>
                        </div>
                    </div>
                    @if($usulan->jabatanLama && $usulan->jabatanTujuan)
                        <div class="mt-4 text-center">
                            <div class="inline-flex items-center bg-white/80 rounded-full px-4 py-2 shadow-md border border-blue-200">
                                <span class="text-sm font-semibold text-blue-800 scrollable" title="{{ $usulan->jabatanLama->jabatan }}">
                                    {{ $usulan->jabatanLama->jabatan }}
                                </span>
                                <i data-lucide="arrow-right" class="w-5 h-5 text-blue-600 mx-2"></i>
                                <span class="text-sm font-semibold text-blue-800 scrollable" title="{{ $usulan->jabatanTujuan->jabatan }}">
                                    {{ $usulan->jabatanTujuan->jabatan }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($usulan->jenis_usulan === 'Usulan Kepangkatan')
                <div class="mt-6 p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 card">
                    <h3 class="text-lg font-bold text-green-900 mb-4 flex items-center">
                        <div class="icon-container w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-md mr-3">
                            <i data-lucide="arrow-right-left" class="w-4 h-4 text-white"></i>
                        </div>
                        Keterangan Usulan Kepangkatan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-lg p-5 border border-green-100">
                            <p class="text-sm font-medium text-green-700 mb-2">Pangkat Saat Ini</p>
                            <p class="text-base font-semibold text-green-900 auto-fit" title="{{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}">
                                {{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}
                            </p>
                        </div>
                        <div class="bg-white/80 rounded-lg p-5 border border-green-100">
                            <p class="text-sm font-medium text-green-700 mb-2">Pangkat yang Dituju</p>
                            <p class="text-base font-semibold text-green-900 auto-fit" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}">
                                {{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}
                            </p>
                        </div>
                    </div>
                    @if(isset($usulan->data_usulan['pangkat_saat_ini']) && isset($usulan->data_usulan['pangkat_yang_dituju']))
                        <div class="mt-4 text-center">
                            <div class="inline-flex items-center bg-white/80 rounded-full px-4 py-2 shadow-md border border-green-200">
                                <span class="text-sm font-semibold text-green-800 scrollable" title="{{ $usulan->data_usulan['pangkat_saat_ini'] }}">
                                    {{ $usulan->data_usulan['pangkat_saat_ini'] }}
                                </span>
                                <i data-lucide="arrow-right" class="w-5 h-5 text-green-600 mx-2"></i>
                                <span class="text-sm font-semibold text-green-800 scrollable" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] }}">
                                    {{ $usulan->data_usulan['pangkat_yang_dituju'] }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-6 p-5 bg-gradient-to-r from-gray-50 to-slate-50 rounded-lg border border-gray-200 card">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                        <div class="icon-container w-8 h-8 bg-gradient-to-r from-gray-500 to-slate-500 rounded-md mr-3">
                            <i data-lucide="info" class="w-4 h-4 text-white"></i>
                        </div>
                        Informasi Usulan
                    </h3>
                    <p class="text-base font-medium text-gray-700 auto-fit">
                        {{ $usulan->jenis_usulan }} - Periode {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                    </p>
                </div>
            @endif
        </section>

        <!-- Log Content -->
        <section class="bg-white/90 rounded-2xl shadow-lg overflow-hidden card">
            @if(count($logs) > 0)
                <div class="p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <div class="icon-container w-10 h-10 bg-gradient-to-r from-purple-500 to-violet-500 rounded-lg shadow-md mr-4">
                            <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="bg-gradient-to-r from-purple-600 to-violet-600 bg-clip-text text-transparent">
                            {{ count($logs) }} Entri Log Aktivitas
                        </span>
                    </h2>
                    <div class="space-y-4">
                        @foreach($logs as $log)
                            @php
                                $isStatusChange = $log['status_sebelumnya'] !== null && $log['status_sebelumnya'] !== $log['status_baru'];
                                $statusIcon = $isStatusChange ? 'refresh-cw' : 'file-text';
                                $iconBg = $isStatusChange ? 'bg-gradient-to-r from-blue-500 to-indigo-500' : 'bg-gradient-to-r from-gray-500 to-slate-500';
                                $getStatusBadgeClass = function($status) {
                                    switch($status) {
                                        case 'Draft': return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-300';
                                        case 'Diajukan': return 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border-blue-300';
                                        case 'Diterima': return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border-green-300';
                                        case 'Ditolak': return 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border-red-300';
                                        case 'Perlu Perbaikan': return 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-300';
                                        case 'Dikembalikan ke Pegawai': return 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border-orange-300';
                                        default: return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-300';
                                    }
                                };
                            @endphp
                            <div class="card bg-white/80 rounded-lg p-5 border {{ $isStatusChange ? 'border-blue-200' : 'border-gray-200' }} fade-in">
                                <div class="flex items-start gap-4">
                                    <div class="icon-container w-12 h-12 {{ $iconBg }} rounded-lg shadow-md">
                                        <i data-lucide="{{ $statusIcon }}" class="w-6 h-6 text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-base font-semibold text-gray-900 scrollable mb-4" title="{{ $log['keterangan'] }}">{{ $log['keterangan'] }}</p>
                                        @if($isStatusChange)
                                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 status-change">
                                                <div class="flex items-center mb-3">
                                                    <div class="icon-container w-6 h-6 bg-blue-500 rounded-full mr-2">
                                                        <i data-lucide="refresh-cw" class="w-3 h-3 text-white"></i>
                                                    </div>
                                                    <span class="text-sm font-semibold text-blue-700">Perubahan Status Usulan</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1 bg-white rounded-lg p-3 border border-gray-200">
                                                        <p class="text-xs font-medium text-gray-500 mb-1">Status Sebelumnya</p>
                                                        <p class="text-sm font-semibold text-gray-700">{{ $log['status_sebelumnya'] ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="icon-container w-10 h-10 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full">
                                                        <i data-lucide="arrow-right" class="w-5 h-5 text-white"></i>
                                                    </div>
                                                    <div class="flex-1 bg-white rounded-lg p-3 border border-blue-200">
                                                        <p class="text-xs font-medium text-blue-500 mb-1">Status Baru</p>
                                                        <p class="text-sm font-semibold text-blue-700">{{ $log['status_baru'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mt-4 flex flex-wrap gap-4">
                                            <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                                <div class="icon-container w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-md">
                                                    <i data-lucide="user" class="w-4 h-4 text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500">Dilakukan Oleh</p>
                                                    <p class="text-sm font-semibold text-gray-700">{{ $log['user_name'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                                                <div class="icon-container w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-md">
                                                    <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500">Waktu</p>
                                                    <p class="text-sm font-semibold text-gray-700">{{ $log['formatted_date'] }}</p>
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
                <div class="p-12 text-center">
                    <div class="icon-container w-24 h-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-2xl mx-auto mb-6">
                        <i data-lucide="file-text" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-400 mb-3">Belum Ada Log Aktivitas</h3>
                    <p class="text-base text-gray-400 mb-4">Belum ada riwayat log untuk usulan ini.</p>
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 rounded-lg text-gray-600 font-medium">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Log akan muncul setelah ada aktivitas pada usulan
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        lucide.createIcons();
        let inactivityTimer;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.close();
            }, 30000);
        }
        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
        document.addEventListener('click', resetInactivityTimer);
        resetInactivityTimer();
    </script>
</body>
</html>
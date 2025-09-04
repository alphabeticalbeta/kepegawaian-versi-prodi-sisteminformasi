<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Usulan - {{ $usulan->jenis_usulan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-subtle': 'bounceSubtle 2s ease-in-out infinite',
                        'slide-in-left': 'slideInLeft 0.6s ease-out',
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        },
                        slideInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    boxShadow: {
                        'glow': '0 0 30px -5px rgba(99, 102, 241, 0.3)',
                        'glow-lg': '0 0 50px -10px rgba(99, 102, 241, 0.4)',
                        '3xl': '0 35px 60px -12px rgba(0, 0, 0, 0.25)',
                        'inner-lg': 'inset 0 2px 8px 0 rgba(0, 0, 0, 0.06)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Enhanced custom styles */
        .text-field {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .text-field::-webkit-scrollbar {
            height: 4px;
        }

        .text-field::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .text-field::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #cbd5e0, #a0aec0);
            border-radius: 4px;
        }

        .text-field::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #a0aec0, #718096);
        }

        .text-field:hover {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            margin: -0.5rem -1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
        }

        .status-badge {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
            transition: all 0.3s ease;
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

        .auto-fit {
            white-space: normal;
            overflow: visible;
            word-break: break-word;
            width: fit-content;
            min-width: 0;
        }

        .auto-fit-container {
            width: auto;
            min-width: 0;
            flex: 1;
        }

        /* Enhanced card hover effects */
        .card-hover {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .icon-bounce:hover {
            animation: bounceSubtle 0.6s ease-in-out;
        }

        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-strong {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Enhanced animations */
        .log-item {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .log-item:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }

        .status-change-animation {
            animation: slideInLeft 0.6s ease-out;
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Improved responsive design */
        @media (max-width: 768px) {
            .text-lg {
                font-size: 1rem;
            }
            .status-badge {
                max-width: 200px;
            }
            .log-item:hover {
                transform: translateY(-4px) scale(1.01);
            }
        }

        /* Loading shimmer effect */
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform-origin: left;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen relative overflow-x-hidden">
    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator" id="scrollIndicator"></div>
    
    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-400/20 to-cyan-400/20 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-indigo-400/10 to-purple-400/10 rounded-full blur-2xl animate-pulse-slow"></div>
    </div>

    <div class="relative w-full px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
        <div class="w-full max-w-7xl mx-auto">
            <!-- Enhanced Header -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-3xl shadow-2xl border border-white/20 p-6 lg:p-8 mb-8 relative overflow-hidden card-hover animate-fade-in-up">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full blur-2xl animate-float" style="animation-delay: -2s;"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white/5 rounded-full blur-xl animate-pulse-slow"></div>
                
                <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg icon-bounce">
                                <i data-lucide="activity" class="w-7 h-7 text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">
                                    Riwayat Log Usulan
                                </h1>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium border border-white/30 glass">
                                        {{ $usulan->jenis_usulan }}
                                    </span>
                                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium border border-white/30 glass">
                                        {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="window.close()" class="group px-6 lg:px-8 py-3 lg:py-4 bg-white/20 backdrop-blur-sm text-white rounded-2xl hover:bg-white/30 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold border border-white/30 glass-strong">
                        <div class="flex items-center gap-2">
                            <i data-lucide="x" class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300"></i>
                            <span class="hidden sm:inline">Tutup</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Enhanced Data Diri Pegawai - Table Format -->
            <div class="bg-gradient-to-br from-white/95 via-blue-50/90 to-indigo-50/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-blue-100/50 p-6 lg:p-8 mb-8 card-hover animate-fade-in-up" style="animation-delay: 0.1s;">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-8 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mr-4 lg:mr-5 shadow-xl icon-bounce">
                        <i data-lucide="user" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="gradient-text">Data Diri Pegawai</span>
                </h2>
                
                <div class="overflow-x-auto rounded-2xl shadow-lg border border-blue-200/30">
                    <table class="w-full bg-white/90 backdrop-blur-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500">
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center text-white font-bold">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i data-lucide="user" class="w-4 h-4"></i>
                                        </div>
                                        Nama Lengkap
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center text-white font-bold">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i data-lucide="id-card" class="w-4 h-4"></i>
                                        </div>
                                        NIP
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center text-white font-bold">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                                        </div>
                                        Jenis Pegawai
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center text-white font-bold">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i data-lucide="award" class="w-4 h-4"></i>
                                        </div>
                                        Status Kepegawaian
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center text-white font-bold">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </div>
                                        Email
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="group hover:bg-blue-50/50 transition-all duration-300">
                                <td class="px-6 py-6 border-b border-blue-100/50">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                            <span class="text-white font-bold text-sm">{{ substr($usulan->pegawai->nama_lengkap, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base lg:text-lg leading-relaxed" title="{{ $usulan->pegawai->nama_lengkap }}">
                                                {{ $usulan->pegawai->nama_lengkap }}
                                            </p>
                                            <p class="text-sm text-gray-500">Pegawai</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 border-b border-blue-100/50">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                        <span class="font-bold text-gray-900 text-base lg:text-lg font-mono bg-green-50 px-3 py-1 rounded-lg border border-green-200" title="{{ $usulan->pegawai->nip }}">
                                            {{ $usulan->pegawai->nip }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 border-b border-blue-100/50">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300 shadow-sm" title="{{ $usulan->pegawai->jenis_pegawai }}">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                                        {{ $usulan->pegawai->jenis_pegawai }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 border-b border-blue-100/50">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300 shadow-sm" title="{{ $usulan->pegawai->status_kepegawaian }}">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                        {{ $usulan->pegawai->status_kepegawaian }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 border-b border-blue-100/50">
                                    <div class="flex items-center group">
                                        <div class="w-8 h-8 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <i data-lucide="mail" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm lg:text-base break-all hover:text-teal-600 transition-colors duration-300" title="{{ $usulan->pegawai->email }}">
                                                {{ $usulan->pegawai->email }}
                                            </p>
                                            <p class="text-xs text-gray-500">Email Pegawai</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Mobile Card View (Hidden on larger screens) -->
                <div class="lg:hidden mt-6 space-y-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-blue-200/50 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg">
                                <span class="text-white font-bold">{{ substr($usulan->pegawai->nama_lengkap, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $usulan->pegawai->nama_lengkap }}</h3>
                                <p class="text-sm text-gray-500">Data Pegawai</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center">
                                    <i data-lucide="id-card" class="w-4 h-4 text-green-600 mr-2"></i>
                                    <span class="text-sm font-medium text-green-700">NIP</span>
                                </div>
                                <span class="font-bold text-gray-900 font-mono">{{ $usulan->pegawai->nip }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex items-center">
                                    <i data-lucide="briefcase" class="w-4 h-4 text-purple-600 mr-2"></i>
                                    <span class="text-sm font-medium text-purple-700">Jenis</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $usulan->pegawai->jenis_pegawai }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="flex items-center">
                                    <i data-lucide="award" class="w-4 h-4 text-orange-600 mr-2"></i>
                                    <span class="text-sm font-medium text-orange-700">Status</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $usulan->pegawai->status_kepegawaian }}</span>
                            </div>
                            
                            <div class="p-3 bg-teal-50 rounded-lg border border-teal-200">
                                <div class="flex items-center mb-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-teal-600 mr-2"></i>
                                    <span class="text-sm font-medium text-teal-700">Email</span>
                                </div>
                                <p class="font-bold text-gray-900 text-sm break-all">{{ $usulan->pegawai->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Informasi Usulan -->
            <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/30 p-6 lg:p-8 mb-8 card-hover animate-fade-in-up" style="animation-delay: 0.2s;">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg icon-bounce">
                        <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="gradient-text">Informasi Usulan</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl p-5 lg:p-6 border border-indigo-200 auto-fit-container hover:shadow-lg transition-all duration-300 card-hover animate-scale-in">
                        <p class="text-sm text-indigo-700 font-semibold mb-2">Jenis Usulan</p>
                        <p class="font-bold text-gray-900 text-base lg:text-lg auto-fit leading-relaxed break-words" title="{{ $usulan->jenis_usulan }}">{{ $usulan->jenis_usulan }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-violet-50 to-purple-100 rounded-2xl p-5 lg:p-6 border border-violet-200 auto-fit-container hover:shadow-lg transition-all duration-300 card-hover animate-scale-in" style="animation-delay: 0.1s;">
                        <p class="text-sm text-violet-700 font-semibold mb-2">Periode Usulan</p>
                        <p class="font-bold text-gray-900 text-base lg:text-lg auto-fit leading-relaxed break-words" title="{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}">{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-rose-50 to-pink-100 rounded-2xl p-5 lg:p-6 border border-rose-200 auto-fit-container hover:shadow-lg transition-all duration-300 card-hover animate-scale-in" style="animation-delay: 0.2s;">
                        <p class="text-sm text-rose-700 font-semibold mb-2">Status Usulan</p>
                        <p class="font-medium">
                            <span class="px-3 py-2 text-xs lg:text-sm font-bold rounded-xl shadow-sm inline-flex items-center max-w-full status-badge
                                @if($usulan->status_usulan === 'Draft Usulan') bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300
                                @elseif($usulan->status_usulan === 'Usulan Dikirim ke Admin Fakultas') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                                @elseif($usulan->status_usulan === 'Usulan Disetujui Admin Fakultas') bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                                @elseif($usulan->status_usulan === 'Usulan Tidak Direkomendasi Admin Fakultas') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                @elseif($usulan->status_usulan === 'Permintaan Perbaikan dari Admin Fakultas') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300
                                @elseif($usulan->status_usulan === 'Permintaan Perbaikan dari Admin Fakultas') bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300
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
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-100 rounded-2xl p-5 lg:p-6 border border-emerald-200 auto-fit-container hover:shadow-lg transition-all duration-300 card-hover animate-scale-in" style="animation-delay: 0.3s;">
                        <p class="text-sm text-emerald-700 font-semibold mb-2">Tanggal Pengajuan</p>
                        <p class="font-bold text-gray-900 text-base lg:text-lg auto-fit leading-relaxed" title="{{ $usulan->created_at->format('d F Y, H:i') }}">{{ $usulan->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Enhanced Keterangan Usulan -->
                @if($usulan->jenis_usulan === 'Usulan Jabatan')
                    <div class="mt-8 p-6 lg:p-8 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 border border-blue-200 rounded-3xl shadow-lg card-hover animate-slide-in-left" style="animation-delay: 0.4s;">
                        <h3 class="text-lg lg:text-xl font-bold text-blue-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center mr-4 shadow-lg icon-bounce">
                                <i data-lucide="arrow-right-left" class="w-5 h-5 text-white"></i>
                            </div>
                            Keterangan Usulan Jabatan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 lg:p-7 border border-blue-200 shadow-sm hover:shadow-lg transition-all duration-300">
                                <p class="text-sm text-blue-700 font-semibold mb-3">Jabatan Saat Ini</p>
                                <p class="text-base lg:text-lg font-bold text-blue-900 break-words leading-relaxed" title="{{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}">
                                    {{ $usulan->jabatanLama->jabatan ?? 'Tidak ada data' }}
                                </p>
                            </div>
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 lg:p-7 border border-blue-200 shadow-sm hover:shadow-lg transition-all duration-300">
                                <p class="text-sm text-blue-700 font-semibold mb-3">Jabatan yang Dituju</p>
                                <p class="text-base lg:text-lg font-bold text-blue-900 break-words leading-relaxed" title="{{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}">
                                    {{ $usulan->jabatanTujuan->jabatan ?? 'Tidak ada data' }}
                                </p>
                            </div>
                        </div>
                        @if($usulan->jabatanLama && $usulan->jabatanTujuan)
                            <div class="mt-6 text-center">
                                <div class="inline-flex items-center bg-white/90 backdrop-blur-sm rounded-full px-6 py-4 shadow-lg border border-blue-200 max-w-full overflow-hidden hover:shadow-xl transition-all duration-300">
                                    <span class="text-sm font-bold text-blue-800 truncate" title="{{ $usulan->jabatanLama->jabatan }}">
                                        {{ $usulan->jabatanLama->jabatan }}
                                    </span>
                                    <div class="mx-4 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center flex-shrink-0 icon-bounce">
                                        <i data-lucide="arrow-right" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <span class="text-sm font-bold text-blue-800 truncate" title="{{ $usulan->jabatanTujuan->jabatan }}">
                                        {{ $usulan->jabatanTujuan->jabatan }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($usulan->jenis_usulan === 'Usulan Kepangkatan')
                    <div class="mt-8 p-6 lg:p-8 bg-gradient-to-r from-green-50 via-emerald-50 to-green-50 border border-green-200 rounded-3xl shadow-lg card-hover animate-slide-in-left" style="animation-delay: 0.4s;">
                        <h3 class="text-lg lg:text-xl font-bold text-green-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mr-4 shadow-lg icon-bounce">
                                <i data-lucide="arrow-right-left" class="w-5 h-5 text-white"></i>
                            </div>
                            Keterangan Usulan Kepangkatan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 lg:p-7 border border-green-200 shadow-sm hover:shadow-lg transition-all duration-300">
                                <p class="text-sm text-green-700 font-semibold mb-3">Pangkat Saat Ini</p>
                                <p class="text-base lg:text-lg font-bold text-green-900 break-words leading-relaxed" title="{{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}">
                                    {{ $usulan->data_usulan['pangkat_saat_ini'] ?? 'Tidak ada data' }}
                                </p>
                            </div>
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 lg:p-7 border border-green-200 shadow-sm hover:shadow-lg transition-all duration-300">
                                <p class="text-sm text-green-700 font-semibold mb-3">Pangkat yang Dituju</p>
                                <p class="text-base lg:text-lg font-bold text-green-900 break-words leading-relaxed" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}">
                                    {{ $usulan->data_usulan['pangkat_yang_dituju'] ?? 'Tidak ada data' }}
                                </p>
                            </div>
                        </div>
                        @if(isset($usulan->data_usulan['pangkat_saat_ini']) && isset($usulan->data_usulan['pangkat_yang_dituju']))
                            <div class="mt-6 text-center">
                                <div class="inline-flex items-center bg-white/90 backdrop-blur-sm rounded-full px-6 py-4 shadow-lg border border-green-200 max-w-full overflow-hidden hover:shadow-xl transition-all duration-300">
                                    <span class="text-sm font-bold text-green-800 truncate" title="{{ $usulan->data_usulan['pangkat_saat_ini'] }}">
                                        {{ $usulan->data_usulan['pangkat_saat_ini'] }}
                                    </span>
                                    <div class="mx-4 w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 icon-bounce">
                                        <i data-lucide="arrow-right" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <span class="text-sm font-bold text-green-800 truncate" title="{{ $usulan->data_usulan['pangkat_yang_dituju'] }}">
                                        {{ $usulan->data_usulan['pangkat_yang_dituju'] }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-8 p-6 lg:p-8 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 border border-gray-200 rounded-3xl shadow-lg card-hover animate-slide-in-left" style="animation-delay: 0.4s;">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-gray-500 to-slate-500 rounded-xl flex items-center justify-center mr-4 shadow-lg icon-bounce">
                                <i data-lucide="info" class="w-5 h-5 text-white"></i>
                            </div>
                            Informasi Usulan
                        </h3>
                        <p class="text-base lg:text-lg text-gray-700 font-medium leading-relaxed">
                            {{ $usulan->jenis_usulan }} - Periode {{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Enhanced Log Content -->
            <div class="bg-gradient-to-br from-white/95 via-purple-50/90 to-violet-50/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-purple-100/50 overflow-hidden card-hover animate-fade-in-up" style="animation-delay: 0.3s;">
                @if(count($logs) > 0)
                    <div class="p-6 lg:p-8">
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8 flex items-center">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 via-violet-500 to-indigo-500 rounded-2xl flex items-center justify-center mr-4 lg:mr-5 shadow-xl icon-bounce">
                                <i data-lucide="clock" class="w-7 h-7 text-white"></i>
                            </div>
                            <span class="gradient-text">
                                {{ count($logs) }} Entri Log Aktivitas
                            </span>
                        </h2>

                        <div class="space-y-6">
                            @foreach($logs as $index => $log)
                                @php
                                    $isStatusChange = $log['status_sebelumnya'] !== null && $log['status_sebelumnya'] !== $log['status_baru'];
                                    $statusIcon = $isStatusChange ? 'refresh-cw' : 'file-text';
                                    $iconBg = $isStatusChange ? 'bg-gradient-to-r from-blue-500 to-indigo-500' : 'bg-gradient-to-r from-gray-500 to-slate-500';
                                @endphp

                                <div class="group bg-white/95 backdrop-blur-sm rounded-3xl p-6 lg:p-8 border-2 {{ $isStatusChange ? 'border-blue-200 shadow-xl' : 'border-gray-200 shadow-lg' }} hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 hover:scale-[1.02] log-item animate-fade-in-up" style="animation-delay: {{ 0.1 * $index }}s;">
                                    <div class="flex flex-col lg:flex-row items-start justify-between gap-6">
                                        <div class="flex items-start space-x-4 lg:space-x-6 w-full lg:flex-1">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 lg:w-14 h-12 lg:h-14 {{ $iconBg }} rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300 icon-bounce">
                                                    <i data-lucide="{{ $statusIcon }}" class="w-6 lg:w-7 h-6 lg:h-7 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-lg lg:text-xl font-bold text-gray-900 leading-relaxed mb-4 break-words" title="{{ $log['keterangan'] }}">{{ $log['keterangan'] }}</p>

                                                @if($isStatusChange)
                                                    <div class="mt-6 p-6 lg:p-7 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 rounded-2xl border border-blue-200 status-change-animation">
                                                        <div class="flex items-center mb-4">
                                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3 shadow-lg icon-bounce">
                                                                <i data-lucide="refresh-cw" class="w-4 h-4 text-white"></i>
                                                            </div>
                                                            <span class="text-sm font-bold text-blue-700">Perubahan Status Usulan</span>
                                                        </div>
                                                        <div class="flex flex-col sm:flex-row items-center gap-4 max-w-full overflow-hidden">
                                                            <div class="flex-1 w-full bg-white/90 backdrop-blur-sm rounded-xl p-4 lg:p-5 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300">
                                                                <p class="text-xs text-gray-500 font-medium mb-2">Status Sebelumnya</p>
                                                                <p class="text-sm lg:text-base font-bold text-gray-700 break-words">{{ $log['status_sebelumnya'] ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-lg flex-shrink-0 icon-bounce">
                                                                <i data-lucide="arrow-right" class="w-5 h-5 lg:w-6 lg:h-6 text-white"></i>
                                                            </div>
                                                            <div class="flex-1 w-full bg-white/90 backdrop-blur-sm rounded-xl p-4 lg:p-5 border border-blue-200 shadow-sm hover:shadow-md transition-all duration-300">
                                                                <p class="text-xs text-blue-500 font-medium mb-2">Status Baru</p>
                                                                <p class="text-sm lg:text-base font-bold text-blue-700 break-words">{{ $log['status_baru'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                                        <div class="flex items-center gap-3 bg-gray-50/80 backdrop-blur-sm rounded-xl px-4 py-3 shadow-sm hover:shadow-md transition-all duration-300">
                                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 icon-bounce">
                                                                <i data-lucide="user" class="w-4 h-4 text-white"></i>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 font-medium">Dilakukan Oleh</p>
                                                                <p class="text-sm font-bold text-gray-700 break-words">{{ $log['user_name'] }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3 bg-gray-50/80 backdrop-blur-sm rounded-xl px-4 py-3 shadow-sm hover:shadow-md transition-all duration-300">
                                                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0 icon-bounce">
                                                                <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 font-medium">Waktu</p>
                                                                <p class="text-sm font-bold text-gray-700">{{ $log['formatted_date'] }}</p>
                                                            </div>
                                                        </div>
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
                    <div class="p-16 lg:p-28 text-center">
                        <div class="w-24 h-24 lg:w-32 lg:h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl animate-bounce-subtle">
                            <i data-lucide="file-text" class="w-12 h-12 lg:w-16 lg:h-16 text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-gray-400 mb-4">Belum Ada Log Aktivitas</h3>
                        <p class="text-lg lg:text-xl text-gray-400 mb-6 max-w-md mx-auto">Belum ada riwayat log untuk usulan ini.</p>
                        <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl text-gray-600 font-medium shadow-lg hover:shadow-xl transition-all duration-300">
                            <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                            Log akan muncul setelah ada aktivitas pada usulan
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Scroll progress indicator
        window.addEventListener('scroll', () => {
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = (scrollTop / scrollHeight) * 100;
            const indicator = document.getElementById('scrollIndicator');
            if (indicator) {
                indicator.style.transform = `scaleX(${scrollPercent / 100})`;
            }
        });

        // Enhanced auto close functionality
        let inactivityTimer;
        let warningTimer;
        let isWarningShown = false;

        function showWarning() {
            if (isWarningShown) return;
            isWarningShown = true;
            
            const warning = document.createElement('div');
            warning.className = 'fixed top-4 right-4 bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 animate-slide-in-left border border-white/20';
            warning.innerHTML = `
                <div class="flex items-center gap-3">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    <div>
                        <p class="font-bold">Peringatan!</p>
                        <p class="text-sm">Halaman akan tertutup otomatis dalam 10 detik</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove(); resetInactivityTimer(); isWarningShown = false;" class="ml-4 bg-white/20 hover:bg-white/30 rounded-lg p-1 transition-colors duration-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(warning);
            lucide.createIcons();
            
            setTimeout(() => {
                if (warning.parentNode) {
                    warning.remove();
                }
            }, 10000);
        }

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            clearTimeout(warningTimer);
            
            // Show warning after 20 seconds
            warningTimer = setTimeout(showWarning, 20000);
            
            // Close after 30 seconds
            inactivityTimer = setTimeout(() => {
                window.close();
            }, 30000);
        }

        // Enhanced activity detection
        const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart', 'touchmove'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer, { passive: true });
        });

        // Start timer
        resetInactivityTimer();

        // Enhanced animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-scale-in, .animate-slide-in-left');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                observer.observe(el);
            });
        });

        // Add smooth scrolling
        document.documentElement.style.scrollBehavior = 'smooth';

        // Enhanced hover effects for cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
                card.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
                card.style.boxShadow = '';
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Performance optimization: Lazy load animations
        const lazyElements = document.querySelectorAll('[data-lazy-animate]');
        const lazyObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    lazyObserver.unobserve(entry.target);
                }
            });
        });

        lazyElements.forEach(el => lazyObserver.observe(el));
    </script>
</body>
</html>
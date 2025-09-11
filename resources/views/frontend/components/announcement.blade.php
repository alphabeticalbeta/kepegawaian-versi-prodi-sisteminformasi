<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .fade-in-up:nth-child(1) { animation-delay: 0.1s; }
        .fade-in-up:nth-child(2) { animation-delay: 0.2s; }
        .fade-in-up:nth-child(3) { animation-delay: 0.3s; }
        .fade-in-up:nth-child(4) { animation-delay: 0.4s; }
        .fade-in-up:nth-child(5) { animation-delay: 0.5s; }
        .fade-in-up:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .card-hover:hover::before {
            left: 100%;
        }

        .gradient-border {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2px;
            border-radius: 12px;
        }

        .gradient-border-content {
            background: white;
            border-radius: 10px;
            height: 100%;
        }

        .slide-in-title {
            animation: slideInTitle 0.8s ease-out;
        }

        @keyframes slideInTitle {
            0% {
                transform: translateX(-50px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .pulse-border {
            animation: pulseBorder 2s ease-in-out infinite;
        }

        @keyframes pulseBorder {
            0%, 100% {
                border-color: #e5e7eb;
            }
            50% {
                border-color: #667eea;
            }
        }

        .new-badge {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -4px, 0);
            }
            70% {
                transform: translate3d(0, -2px, 0);
            }
            90% {
                transform: translate3d(0, -1px, 0);
            }
        }

        .icon-pulse {
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">

<div class="bg-gradient-to-r from-blue-50 to-indigo-50 py-12 relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse delay-1000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="lg:col-span-2">
            <section>
                <h2 class="border-b-2 border-gradient-to-r from-blue-500 to-purple-600 pb-4 text-3xl font-bold text-gray-800 slide-in-title relative">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Pengumuman</span>
                    <div class="absolute bottom-0 left-0 w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full"></div>
                </h2>

                <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
                    <div class="gradient-border fade-in-up">
                        <div class="gradient-border-content rounded-xl p-6 card-hover">
                            <div class="flex items-start justify-between mb-4">
                                <span class="new-badge text-xs font-bold text-white px-3 py-1 rounded-full">TERBARU</span>
                                <svg class="h-5 w-5 text-blue-500 icon-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                                Surat Edaran Rektor Nomor 8/UN17/KP/2023 Tentang Kenaikan UMP
                            </a>
                            <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                                <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                                <span class="font-medium">21 February 2025</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow-lg card-hover fade-in-up border border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">PENTING</span>
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                            Surat Edaran Rektor tentang Layanan Kenaikan Pangkat
                        </a>
                        <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span class="font-medium">21 January 2025</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow-lg card-hover fade-in-up border border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">INFO</span>
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                            Check-List Pengusulan Perpanjangan Tugas Belajar
                        </a>
                        <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span class="font-medium">24 June 2024</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow-lg card-hover fade-in-up border border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">INFO</span>
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                            Check-List Pengusulan Perpanjangan Tugas Belajar
                        </a>
                        <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span class="font-medium">24 June 2024</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow-lg card-hover fade-in-up border border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">INFO</span>
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                            Check-List Pengusulan Perpanjangan Tugas Belajar
                        </a>
                        <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span class="font-medium">24 June 2024</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow-lg card-hover fade-in-up border border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">INFO</span>
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <a href="#" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300 text-lg leading-relaxed">
                            Check-List Pengusulan Perpanjangan Tugas Belajar
                        </a>
                        <div class="mt-6 flex items-center text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
                            <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0121 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span class="font-medium">24 June 2024</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

</body>
</html>

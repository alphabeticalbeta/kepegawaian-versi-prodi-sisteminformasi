@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Dashboard Kepegawaian Universitas')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-r from-yellow-500 via-yellow-400 to-yellow-300 shadow-2xl">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative px-6 py-16 sm:px-8 sm:py-24">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="mb-8">
                        <div class="mx-auto h-24 w-24 rounded-full bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm overflow-hidden">
                            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-16 w-16 object-contain">
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl mb-6">
                        Selamat Datang
                    </h1>
                    <p class="text-xl text-blue-100 sm:text-2xl font-medium">
                        Website Kepegawaian Universitas
                    </p>
                    <div class="mt-8">
                        <div class="inline-flex items-center px-6 py-3 rounded-full bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30">
                            <svg class="h-5 w-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-white font-medium">Sistem Terintegrasi</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Main Content Section -->
        <div class="relative z-10 -mt-16 px-6 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Welcome Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Card 1: Kepegawaian -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 border-l-4 border-blue-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">Kepegawaian</h3>
                        </div>
                        <p class="text-gray-600 text-sm">Kelola data dan administrasi kepegawaian dengan sistem terintegrasi</p>
                    </div>

                    <!-- Card 2: Validasi -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 border-l-4 border-green-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">Validasi</h3>
                        </div>
                        <p class="text-gray-600 text-sm">Proses validasi usulan yang efisien dan terstruktur</p>
                    </div>

                    <!-- Card 3: Monitoring -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 border-l-4 border-purple-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-purple-100 rounded-full">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-gray-900">Monitoring</h3>
                        </div>
                        <p class="text-gray-600 text-sm">Pemantauan real-time status usulan dan progress</p>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="bg-white rounded-3xl shadow-2xl p-8 text-center mb-12">
                    <div class="max-w-3xl mx-auto">
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">
                            Selamat Datang di Sistem Kepegawaian
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Kami menyediakan platform terintegrasi untuk mengelola semua aspek kepegawaian universitas. 
                            Dari administrasi data hingga validasi usulan, semuanya dapat diakses dengan mudah dan efisien.
                        </p>
                        <div class="mt-8 flex flex-wrap justify-center gap-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sistem Terintegrasi
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Real-time Monitoring
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                User-Friendly Interface
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="text-center pb-12">
                    <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full">
                        <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">Powered by UNMUL Technology</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
@endsection



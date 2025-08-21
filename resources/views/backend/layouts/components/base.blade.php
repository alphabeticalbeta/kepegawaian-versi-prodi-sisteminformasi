<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Kepegawaian UNMUL</title>

    {{-- Vite Integration - CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/' . ($jsModule ?? 'app.js')])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional styles from views --}}
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans">

    {{-- Loading Overlay (Hidden by default) --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] items-center justify-center hidden">
        <div class="relative mx-auto p-5 w-96">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
                <p class="text-center mt-4 text-gray-600">Memproses...</p>
            </div>
        </div>
    </div>

    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include($sidebarComponent ?? 'backend.components.sidebar-default')

        {{-- Main Content Container --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">
            {{-- Header --}}
            @include('backend.components.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-6 sm:p-6 lg:p-8">
                @include('backend.components.flash')
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Additional scripts from child templates --}}
    @stack('scripts')
</body>
</html>

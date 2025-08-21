<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Sistem Kepegawaian UNMUL')">
    <meta name="author" content="UNMUL">

    <title>@yield('title', 'Dashboard') - Kepegawaian UNMUL</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Vite Integration - CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/admin-fakultas/index.js'])

    {{-- External Libraries --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional styles from views --}}
    @stack('styles')

    {{-- Global CSS for sidebar and header --}}
    <style>
        /* Sidebar collapsed state */
        .sidebar.collapsed {
            width: 4rem;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        /* Main content adjustment when sidebar is collapsed */
        #main-content {
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }

        #main-content.ml-16 {
            margin-left: 4rem;
        }

        /* Ensure sidebar is properly positioned */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 30 !important;
        }

        /* Ensure header stays on top */
        header {
            position: sticky !important;
            top: 0 !important;
            z-index: 40 !important;
            background: white !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }

        /* Admin Fakultas specific styles */
        .admin-fakultas-dashboard {
            /* Custom styles for admin fakultas dashboard */
        }

        .usulan-card {
            transition: all 0.3s ease;
        }

        .usulan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Data table styles */
        .data-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .data-table th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6b7280;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .data-table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #e5e7eb;
        }

        .data-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body {
            padding: 1.5rem;
        }
    </style>
</head>
<body class="bg-slate-100">
    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
                <p class="text-center mt-4 text-gray-600" id="loadingText">Memproses...</p>
            </div>
        </div>
    </div>

    {{-- Skip to main content (Accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-indigo-600 text-white px-4 py-2 rounded-md z-50" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">
        Skip to main content
    </a>

    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include('backend.components.sidebar-admin-fakultas')

        {{-- Main Content Container --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">
            {{-- Header with new design --}}
            @include('backend.components.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100" style="padding-top: 1rem;">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4 mx-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 mx-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 mx-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4 mx-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                        {{ session('info') }}
                    </div>
                @endif

                {{-- Main Content --}}
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Additional scripts from child templates --}}
    @stack('scripts')

    {{-- Initialize Lucide icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized');
            } else {
                console.error('Lucide library not loaded');
            }
        });

        // Toggle sidebar function
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.getElementById('main-content');

            if (sidebar) {
                sidebar.classList.toggle('collapsed');
            }

            if (mainContent) {
                mainContent.classList.toggle('ml-16');
            }
        }
    </script>
</body>
</html>

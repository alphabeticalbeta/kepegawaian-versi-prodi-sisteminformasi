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

    {{-- Dark Mode Script - Load Immediately --}}
    <script>
                // Define toggleDarkMode function globally immediately
        window.toggleDarkMode = function() {
            console.log('toggleDarkMode called');
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);

            // Update icon without reinitializing all Lucide icons
            const icon = document.getElementById('dark-mode-icon');
            if (icon) {
                if (isDark) {
                    icon.innerHTML = '<i data-lucide="sun"></i>';
                } else {
                    icon.innerHTML = '<i data-lucide="moon"></i>';
                }
                // Only reinitialize the specific icon, not all icons
                if (window.lucide && icon.querySelector('[data-lucide]')) {
                    lucide.createIcons(icon);
                }
            }

            console.log('Dark mode toggled:', isDark);
        };

        // Initialize dark mode immediately
        (function() {
            const darkMode = localStorage.getItem('darkMode');
            console.log('Dark mode value from localStorage:', darkMode);

            // Only apply dark mode if explicitly set to 'true'
            if (darkMode === 'true') {
                document.documentElement.classList.add('dark');
                console.log('Dark mode applied');
            } else {
                document.documentElement.classList.remove('dark');
                console.log('Light mode applied');
                // Set default to light mode if not set
                if (darkMode === null || darkMode === undefined) {
                    localStorage.setItem('darkMode', 'false');
                    console.log('Default set to light mode');
                }
            }
        })();

        // Add event listener for dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('dark-mode-toggle');
            if (toggleButton) {
                toggleButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof window.toggleDarkMode === 'function') {
                        window.toggleDarkMode();
                    } else {
                        console.log('toggleDarkMode function not available');
                    }
                });
                console.log('Dark mode button event listener added');
            } else {
                console.log('Dark mode button not found');
            }

            // Initialize icon
            const icon = document.getElementById('dark-mode-icon');
            if (icon) {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    icon.innerHTML = '<i data-lucide="sun"></i>';
                } else {
                    icon.innerHTML = '<i data-lucide="moon"></i>';
                }
                // Initialize only the specific icon to avoid infinite loop
                if (window.lucide && icon.querySelector('[data-lucide]')) {
                    lucide.createIcons(icon);
                }
            }
        });

        // Emergency reset function for dark mode issues
        window.resetDarkMode = function() {
            localStorage.removeItem('darkMode');
            localStorage.setItem('darkMode', 'false');
            document.documentElement.classList.remove('dark');
            location.reload();
        };

        // Clear any corrupted dark mode data
        window.clearDarkModeData = function() {
            localStorage.removeItem('darkMode');
            localStorage.removeItem('theme');
            document.documentElement.classList.remove('dark');
            console.log('Dark mode data cleared');
        };
    </script>

    {{-- Vite Integration - CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/' . ($jsModule ?? 'app.js')])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional styles from views --}}
    @stack('styles')

    {{-- Inline Dark Mode Styles --}}
    <style>
        /* Force dark mode styles to work with Vite */
        .dark {
            color-scheme: dark !important;
        }

        /* Dark mode for body and main content */
        .dark body {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }

        .dark #main-content {
            background-color: #111827 !important;
        }

        /* Dark mode for backgrounds */
        .dark .bg-slate-100 {
            background-color: #111827 !important;
        }

        .dark .bg-white {
            background-color: #1f2937 !important;
        }

        /* Dark mode for text colors - All text white */
        .dark .text-gray-900,
        .dark .text-gray-800,
        .dark .text-gray-700,
        .dark .text-gray-600,
        .dark .text-gray-500,
        .dark .text-gray-400,
        .dark .text-gray-300,
        .dark .text-gray-200,
        .dark .text-gray-100,
        .dark .text-black,
        .dark .text-slate-900,
        .dark .text-slate-800,
        .dark .text-slate-700,
        .dark .text-slate-600,
        .dark .text-slate-500,
        .dark .text-slate-400,
        .dark .text-slate-300,
        .dark .text-slate-200,
        .dark .text-slate-100,
        .dark .text-zinc-900,
        .dark .text-zinc-800,
        .dark .text-zinc-700,
        .dark .text-zinc-600,
        .dark .text-zinc-500,
        .dark .text-zinc-400,
        .dark .text-zinc-300,
        .dark .text-zinc-200,
        .dark .text-zinc-100,
        .dark .text-neutral-900,
        .dark .text-neutral-800,
        .dark .text-neutral-700,
        .dark .text-neutral-600,
        .dark .text-neutral-500,
        .dark .text-neutral-400,
        .dark .text-neutral-300,
        .dark .text-neutral-200,
        .dark .text-neutral-100 {
            color: #ffffff !important;
        }

        /* Dark mode for borders */
        .dark .border-gray-200 {
            border-color: #374151 !important;
        }

        .dark .border-gray-300 {
            border-color: #4b5563 !important;
        }

        /* Dark mode for cards and containers */
        .dark .bg-white {
            background-color: #1f2937 !important;
        }

        .dark .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.5) !important;
        }

        /* Dark mode for tables */
        .dark table {
            background-color: #1f2937 !important;
        }

        .dark th {
            background-color: #374151 !important;
            color: #f9fafb !important;
        }

        .dark td {
            background-color: #1f2937 !important;
            color: #e2e8f0 !important;
        }

        /* Dark mode for buttons */
        .dark .btn-primary {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        .dark .btn-secondary {
            background-color: #6b7280 !important;
            color: #ffffff !important;
        }

        /* Dark mode for gradient backgrounds */
        .dark .bg-gradient-to-br {
            background: linear-gradient(to bottom right, #1e293b, #334155, #475569) !important;
        }

        .dark .from-blue-50 {
            background-color: #1e293b !important;
        }

        .dark .via-indigo-50 {
            background-color: #334155 !important;
        }

        .dark .to-purple-50 {
            background-color: #475569 !important;
        }

        /* Dark mode for custom gradient boxes */
        .dark .bg-gradient-to-br.from-blue-50.via-indigo-50.to-purple-50 {
            background: linear-gradient(to bottom right, #1e293b, #334155, #475569) !important;
        }

        /* Dark mode for border colors in gradients */
        .dark .border-blue-100 {
            border-color: #475569 !important;
        }

        .dark .border-indigo-100 {
            border-color: #4c1d95 !important;
        }

        .dark .border-purple-100 {
            border-color: #581c87 !important;
        }

        /* Dark mode for additional background colors */
        .dark .bg-gray-50 {
            background-color: #374151 !important;
        }

        .dark .bg-gray-100 {
            background-color: #4b5563 !important;
        }

        /* Dark mode for shadows */
        .dark .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
        }

        .dark .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.1) !important;
        }

        /* Ensure transitions work */
        body, #main-content, .bg-slate-100, .bg-white, .bg-gray-50, .bg-gray-100 {
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }

        /* Dark mode for status badges */
        .dark .bg-green-100 {
            background-color: #064e3b !important;
        }

        .dark .bg-yellow-100 {
            background-color: #78350f !important;
        }

        .dark .bg-red-100 {
            background-color: #7f1d1d !important;
        }

        .dark .bg-blue-100 {
            background-color: #1e3a8a !important;
        }

        .dark .bg-gray-100 {
            background-color: #374151 !important;
        }

        .dark .text-green-800 {
            color: #10b981 !important;
        }

        .dark .text-yellow-800 {
            color: #f59e0b !important;
        }

        .dark .text-red-800 {
            color: #ef4444 !important;
        }

        .dark .text-blue-800 {
            color: #3b82f6 !important;
        }

        .dark .text-gray-800 {
            color: #d1d5db !important;
        }

        /* Dark mode for form elements */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark textarea,
        .dark select {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }

        .dark input[type="text"]:focus,
        .dark input[type="email"]:focus,
        .dark input[type="password"]:focus,
        .dark textarea:focus,
        .dark select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        /* Dark mode for modals */
        .dark .modal-content {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .modal-header {
            border-bottom-color: #374151 !important;
        }

        /* Dark mode for all text elements */
        .dark p,
        .dark h1,
        .dark h2,
        .dark h3,
        .dark h4,
        .dark h5,
        .dark h6,
        .dark span,
        .dark div,
        .dark a,
        .dark label,
        .dark small,
        .dark strong,
        .dark em,
        .dark b,
        .dark i,
        .dark u,
        .dark mark,
        .dark del,
        .dark ins,
        .dark sub,
        .dark sup,
        .dark code,
        .dark pre,
        .dark blockquote,
        .dark cite,
        .dark abbr,
        .dark address,
        .dark dfn,
        .dark kbd,
        .dark samp,
        .dark var,
        .dark time {
            color: #ffffff !important;
        }

        /* Dark mode for links */
        .dark a {
            color: #60a5fa !important;
        }

        .dark a:hover {
            color: #93c5fd !important;
        }

        /* Dark mode for headings */
        .dark h1,
        .dark h2,
        .dark h3,
        .dark h4,
        .dark h5,
        .dark h6 {
            color: #ffffff !important;
        }

        /* Dark mode hover fixes - Force white text on hover */
        .dark *:hover {
            color: #ffffff !important;
        }

        .dark a:hover {
            color: #93c5fd !important;
        }

        .dark button:hover,
        .dark .btn:hover {
            color: #ffffff !important;
        }

        .dark .nav-link:hover,
        .dark .dropdown-item:hover {
            color: #ffffff !important;
        }

        .dark .table-hover tbody tr:hover td,
        .dark .table-hover tbody tr:hover th {
            color: #ffffff !important;
        }

        .dark .card:hover,
        .dark .card-body:hover {
            color: #ffffff !important;
        }

        .dark .list-group-item:hover {
            color: #ffffff !important;
        }

        .dark .badge:hover {
            color: #ffffff !important;
        }

        .dark .alert:hover {
            color: #ffffff !important;
        }

        .dark .modal:hover,
        .dark .modal-content:hover {
            color: #ffffff !important;
        }

        .dark .form-control:hover,
        .dark .form-select:hover {
            color: #f9fafb !important;
        }

        .dark .sidebar a:hover,
        .dark .sidebar .nav-link:hover {
            color: #ffffff !important;
        }

        .dark .header a:hover,
        .dark .header .nav-link:hover {
            color: #ffffff !important;
        }

        /* Dark mode hover background fixes */
        .dark .btn:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        .dark .btn-primary:hover {
            background-color: #2563eb !important;
            color: #ffffff !important;
        }

        .dark .btn-secondary:hover {
            background-color: #4b5563 !important;
            color: #ffffff !important;
        }

        .dark .btn-success:hover {
            background-color: #059669 !important;
            color: #ffffff !important;
        }

        .dark .btn-danger:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
        }

        .dark .btn-warning:hover {
            background-color: #d97706 !important;
            color: #ffffff !important;
        }

        .dark .btn-info:hover {
            background-color: #0891b2 !important;
            color: #ffffff !important;
        }

        .dark .btn-light:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        .dark .btn-dark:hover {
            background-color: #111827 !important;
            color: #ffffff !important;
        }

        /* Dark mode table hover fixes */
        .dark .table-hover tbody tr:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        .dark .table-hover tbody tr:hover td,
        .dark .table-hover tbody tr:hover th {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        /* Dark mode card hover fixes */
        .dark .card:hover {
            background-color: #1f2937 !important;
            color: #ffffff !important;
        }

        .dark .card-body:hover {
            background-color: #1f2937 !important;
            color: #ffffff !important;
        }

        /* Dark mode list hover fixes */
        .dark .list-group-item:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        /* Dark mode nav hover fixes */
        .dark .nav-link:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        .dark .dropdown-item:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        /* Dark mode sidebar hover fixes */
        .dark .sidebar a:hover,
        .dark .sidebar .nav-link:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        /* Dark mode header hover fixes */
        .dark .header a:hover,
        .dark .header .nav-link:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        /* Smooth transitions for dark mode */
        .dark body,
        .dark #main-content,
        .dark .bg-slate-100,
        .dark .bg-white,
        .dark .bg-gray-50,
        .dark .bg-gray-100 {
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }
    </style>

    {{-- Custom CSS for specific role --}}
    @if(isset($role))
        <style>
            /* Role-specific styles */
            .role-{{ $role }} {
                /* Custom styles for {{ $role }} */
            }
        </style>
    @endif

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

        /* Dark mode sidebar */
        .dark .sidebar {
            background-color: #1f2937 !important;
        }

        /* Dark mode body and main content */
        .dark body {
            background-color: #111827 !important;
        }

        .dark #main-content {
            background-color: #111827 !important;
        }

        /* Dark mode text colors */
        .dark .text-gray-700 {
            color: #d1d5db !important;
        }

        .dark .text-gray-600 {
            color: #9ca3af !important;
        }

        .dark .text-gray-500 {
            color: #6b7280 !important;
        }

        /* Dark mode card backgrounds */
        .dark .bg-white {
            background-color: #1f2937 !important;
        }

        .dark .bg-slate-100 {
            background-color: #111827 !important;
        }

        /* Ensure header stays on top */
        header {
            position: sticky !important;
            top: 0 !important;
            z-index: 40 !important;
            background: white !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }

        /* Dark mode header */
        .dark header {
            background: #1f2937 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
        }

        /* Ensure header is always visible */
        header.bg-white {
            background-color: white !important;
        }

        .dark header.bg-white {
            background-color: #1f2937 !important;
        }

        /* Fix header positioning issues */
        #main-content > header {
            position: sticky !important;
            top: 0 !important;
            z-index: 40 !important;
        }

        /* Ensure dropdowns appear above other elements */
        #role-dropdown-menu,
        #profile-dropdown-menu {
            z-index: 50;
        }

        /* Loading overlay z-index */
        #loadingOverlay {
            z-index: 9999;
        }

        /* Password modal z-index */
        #passwordModal {
            z-index: 60;
        }

        /* Hide accessibility link properly */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        /* Ensure accessibility link is hidden by default */
        a[href="#main-content"] {
            position: absolute !important;
            left: -9999px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
        }

        /* Only show on focus for keyboard navigation */
        a[href="#main-content"]:focus {
            position: absolute !important;
            left: 4px !important;
            top: 4px !important;
            width: auto !important;
            height: auto !important;
            overflow: visible !important;
            background: #4f46e5 !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            z-index: 50 !important;
            text-decoration: none !important;
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-gray-900 font-sans antialiased transition-colors">

    {{-- Loading Overlay (Hidden by default) --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] items-center justify-center hidden">
        <div class="relative mx-auto p-5 w-96">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 transition-colors">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
                <p class="text-center mt-4 text-gray-600 dark:text-gray-300" id="loadingText">Memproses...</p>
            </div>
        </div>
    </div>

    {{-- Skip to main content (Accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-indigo-600 text-white px-4 py-2 rounded-md z-50" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">
        Skip to main content
    </a>

    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include($sidebarComponent ?? 'backend.components.sidebar-default')

        {{-- Main Content Container --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">
            {{-- Header --}}
            @include('backend.components.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 dark:bg-gray-900 transition-colors" style="padding-top: 1rem;">
                {{-- Breadcrumb (if provided) --}}
                @hasSection('breadcrumb')
                    <nav class="mb-6" aria-label="Breadcrumb">
                        @yield('breadcrumb')
                    </nav>
                @endif

                {{-- Page Header (if provided) --}}
                @hasSection('page-header')
                    <div class="mb-6">
                        @yield('page-header')
                    </div>
                @endif

                {{-- Flash Messages --}}
                @include('backend.components.flash')

                {{-- Main Content --}}
                @yield('content')

                {{-- Page Footer (if provided) --}}
                @hasSection('page-footer')
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        @yield('page-footer')
                    </div>
                @endif
            </main>
        </div>
    </div>

    {{-- Additional scripts from child templates --}}
    @stack('scripts')

    {{-- Header Functions Script --}}
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized');

                // Re-initialize icons when new content is added dynamically
                const observer = new MutationObserver(function(mutations) {
                    let shouldReInit = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            // Check if any added nodes contain lucide icons
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) { // Element node
                                    if (node.querySelector && (node.querySelector('[data-lucide]') || node.hasAttribute('data-lucide'))) {
                                        shouldReInit = true;
                                    }
                                }
                            });
                        }
                    });

                    if (shouldReInit) {
                        lucide.createIcons();
                        console.log('Lucide icons re-initialized after DOM changes');
                    }
                });

                // Start observing
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });

            } else {
                console.error('Lucide library not loaded');
            }
        });

        // Ensure header functions are available globally
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Base layout loaded');

            // Initialize header functions if not already done
            if (typeof window.toggleSidebar === 'undefined') {
                console.log('Initializing header functions from base layout');

                // Toggle sidebar function
                window.toggleSidebar = function() {
                    const sidebar = document.querySelector('.sidebar');
                    const mainContent = document.getElementById('main-content');

                    if (sidebar && mainContent) {
                        sidebar.classList.toggle('collapsed');

                        if (sidebar.classList.contains('collapsed')) {
                            mainContent.classList.remove('ml-64');
                            mainContent.classList.add('ml-16');
                        } else {
                            mainContent.classList.remove('ml-16');
                            mainContent.classList.add('ml-64');
                        }

                        console.log('Sidebar toggled:', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
                    } else {
                        console.error('Sidebar or main content not found');
                    }
                };

                // Toggle role dropdown function
                window.toggleRoleDropdown = function() {
                    const dropdown = document.getElementById('role-dropdown-menu');
                    if (dropdown) {
                        dropdown.classList.toggle('hidden');

                        // Close other dropdowns
                        const profileDropdown = document.getElementById('profile-dropdown-menu');
                        if (profileDropdown) {
                            profileDropdown.classList.add('hidden');
                        }
                    }
                };

                // Toggle profile dropdown function
                window.toggleProfileDropdown = function() {
                    const dropdown = document.getElementById('profile-dropdown-menu');
                    if (dropdown) {
                        dropdown.classList.toggle('hidden');

                        // Close other dropdowns
                        const roleDropdown = document.getElementById('role-dropdown-menu');
                        if (roleDropdown) {
                            roleDropdown.classList.add('hidden');
                        }
                    }
                };

                // Open password modal function
                window.openPasswordModal = function() {
                    const modal = document.getElementById('passwordModal');
                    if (modal) {
                        modal.classList.remove('hidden');

                        // Close dropdowns
                        const profileDropdown = document.getElementById('profile-dropdown-menu');
                        if (profileDropdown) {
                            profileDropdown.classList.add('hidden');
                        }
                    }
                };

                // Close password modal function
                window.closePasswordModal = function() {
                    const modal = document.getElementById('passwordModal');
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                };

                console.log('Header functions initialized from base layout');
            }
        });
    </script>

    {{-- Global JavaScript Functions --}}
    <script>
        // Global loading functions
        window.showLoading = function(message = 'Memproses...') {
            const overlay = document.getElementById('loadingOverlay');
            const text = document.getElementById('loadingText');
            if (overlay && text) {
                text.textContent = message;
                overlay.classList.remove('hidden');
            }
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('hidden');
            }
        };

        // Global error handling
        window.handleError = function(error, title = 'Error') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: error.message || error,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(`${title}: ${error.message || error}`);
            }
        };

        // Global success handling
        window.handleSuccess = function(message, title = 'Success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(`${title}: ${message}`);
            }
        };

        // Global confirmation
        window.confirmAction = function(message, callback) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            } else {
                if (confirm(message) && callback) {
                    callback();
                }
            }
        };
    </script>


</body>
</html>

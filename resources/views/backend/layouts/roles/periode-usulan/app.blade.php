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
    @vite(['resources/css/app.css', 'resources/js/admin-universitas/index.js'])

    {{-- Additional CSS for sidebar functionality --}}
    <style>
        /* Ensure sidebar is properly positioned and functional */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 30 !important;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 4rem !important;
        }

        .sidebar.collapsed .sidebar-text {
            display: none !important;
        }

        #main-content {
            transition: margin-left 0.3s ease;
            margin-left: 16rem; /* 256px = 16rem */
        }

        #main-content.ml-16 {
            margin-left: 4rem !important;
        }

        /* Ensure dropdowns work properly */
        .dropdown-menu {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        /* Sidebar dropdown specific styles */
        #sidebar .dropdown-menu {
            max-height: 0;
            opacity: 0;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            overflow: hidden;
        }

        #sidebar .dropdown-menu:not(.hidden) {
            max-height: 2000px; /* Increased max-height to accommodate all menu items */
            opacity: 1;
        }

        /* Ensure all menu items are visible */
        #sidebar .dropdown-menu .relative {
            display: block !important;
            visibility: visible !important;
        }

        /* Ensure sidebar navigation is properly displayed */
        #sidebar nav {
            display: flex !important;
            flex-direction: column !important;
            visibility: visible !important;
        }

        /* Ensure sidebar itself is visible */
        #sidebar {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure dropdown toggles are clickable */
        [data-collapse-toggle] {
            cursor: pointer;
            user-select: none;
        }

        /* Fix for dropdown icons */
        [data-collapse-toggle] [data-lucide="chevron-down"] {
            transition: transform 0.3s ease;
        }

        /* Ensure sidebar dropdowns are above other elements */
        #sidebar .dropdown-menu {
            z-index: 35;
            position: relative;
        }

        /* Ensure sidebar is above all other elements */
        #sidebar {
            z-index: 50 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            overflow-y: auto !important;
        }

        /* Ensure main content doesn't overlap sidebar */
        #main-content {
            margin-left: 16rem !important; /* 256px */
            z-index: 1 !important;
        }

        /* Ensure all menu items are clickable */
        #sidebar a,
        #sidebar button {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        /* Ensure dropdown toggles work properly */
        [data-collapse-toggle] {
            pointer-events: auto !important;
            cursor: pointer !important;
            user-select: none !important;
        }

        /* Force sidebar to be visible */
        #sidebar * {
            visibility: visible !important;
        }

        /* Ensure proper spacing for menu items */
        #sidebar .dropdown-menu .relative {
            margin-bottom: 0.25rem !important;
        }

        /* Ensure proper text visibility */
        #sidebar .sidebar-text {
            color: inherit !important;
            visibility: visible !important;
        }

        /* Fix for mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            #main-content {
                margin-left: 0 !important;
            }
        }
    </style>

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional styles from views --}}
    @stack('styles')

    {{-- Custom CSS for periode usulan --}}
    <style>
        /* Periode Usulan specific styles */
        .periode-usulan-dashboard {
            /* Custom styles for periode usulan dashboard */
        }

        .periode-card {
            transition: all 0.3s ease;
        }

        .periode-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-buka {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-tutup {
            background-color: #fef2f2;
            color: #dc2626;
        }

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

        /* Ensure header is always visible */
        header.bg-white {
            background-color: white !important;
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
            z-index: 10000;
        }
    </style>
</head>
<body class="bg-gray-50">
    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>
    </div>

    <div class="flex h-screen bg-gray-50">
        {{-- Sidebar --}}
        @include('backend.components.sidebar-kepegawaian-universitas')

        {{-- Main Content --}}
        <div id="main-content" class="flex-1 flex flex-col overflow-hidden ml-64">
            {{-- Header --}}
            @include('backend.components.header')

            {{-- Page Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                {{-- Flash Messages --}}
                @include('backend.components.flash')

                @yield('content')
            </main>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing sidebar functionality');

            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('ml-16');
                });
            }

            // Dropdown functionality for sidebar
            const dropdownToggles = document.querySelectorAll('[data-collapse-toggle]');
            console.log('Found dropdown toggles:', dropdownToggles.length);

            dropdownToggles.forEach((toggle, index) => {
                console.log(`Setting up dropdown toggle ${index}:`, toggle);

                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Dropdown toggle clicked:', this);

                    const targetId = this.getAttribute('data-collapse-toggle');
                    const target = document.getElementById(targetId);
                    const icon = this.querySelector('[data-lucide="chevron-down"]');

                    console.log('Target ID:', targetId);
                    console.log('Target element:', target);
                    console.log('Icon element:', icon);

                    if (target) {
                        const isHidden = target.classList.contains('hidden');
                        console.log('Is hidden:', isHidden);

                        // Toggle the dropdown
                        if (isHidden) {
                            target.classList.remove('hidden');
                            console.log('Dropdown opened');
                        } else {
                            target.classList.add('hidden');
                            console.log('Dropdown closed');
                        }

                        // Rotate the icon
                        if (icon) {
                            icon.style.transform = isHidden ? 'rotate(180deg)' : '';
                            console.log('Icon rotated');
                        }

                        // Update aria-expanded
                        this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                    } else {
                        console.error('Target element not found for ID:', targetId);
                    }
                });
            });

            // Profile dropdown
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown-menu');

            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function() {
                    profileDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }

            // Role dropdown
            const roleButton = document.getElementById('role-button');
            const roleDropdown = document.getElementById('role-dropdown-menu');

            if (roleButton && roleDropdown) {
                roleButton.addEventListener('click', function() {
                    roleDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!roleButton.contains(event.target) && !roleDropdown.contains(event.target)) {
                        roleDropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Global loading function
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Global error handling
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            hideLoading();
        });

        // Global unhandled promise rejection
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            hideLoading();
        });

                // Fallback dropdown functionality
        window.addEventListener('load', function() {
            console.log('Window loaded - Setting up fallback dropdown functionality');

            // Ensure sidebar is visible
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.style.display = 'flex';
                sidebar.style.visibility = 'visible';
                sidebar.style.opacity = '1';
                sidebar.style.zIndex = '50';
                console.log('Sidebar visibility ensured');
            }

            // Re-initialize dropdowns if needed
            setTimeout(function() {
                const dropdownToggles = document.querySelectorAll('[data-collapse-toggle]');
                console.log('Fallback: Found dropdown toggles:', dropdownToggles.length);

                dropdownToggles.forEach((toggle, index) => {
                    // Remove existing listeners to avoid duplicates
                    const newToggle = toggle.cloneNode(true);
                    toggle.parentNode.replaceChild(newToggle, toggle);

                    newToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        console.log('Fallback dropdown toggle clicked:', this);

                        const targetId = this.getAttribute('data-collapse-toggle');
                        const target = document.getElementById(targetId);
                        const icon = this.querySelector('[data-lucide="chevron-down"]');

                        if (target) {
                            const isHidden = target.classList.contains('hidden');

                            if (isHidden) {
                                target.classList.remove('hidden');
                                target.style.maxHeight = '2000px';
                                target.style.opacity = '1';
                            } else {
                                target.classList.add('hidden');
                                target.style.maxHeight = '0';
                                target.style.opacity = '0';
                            }

                            if (icon) {
                                icon.style.transform = isHidden ? 'rotate(180deg)' : '';
                            }

                            this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                        }
                    });
                });

                // Ensure all menu items are visible
                const menuItems = document.querySelectorAll('#sidebar .dropdown-menu .relative');
                console.log('Found menu items:', menuItems.length);
                menuItems.forEach((item, index) => {
                    item.style.display = 'block';
                    item.style.visibility = 'visible';
                    console.log(`Menu item ${index} visibility ensured`);
                });
            }, 100);
        });

        // Additional sidebar visibility check
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    // Force sidebar visibility
                    sidebar.style.cssText = `
                        display: flex !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        z-index: 50 !important;
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        height: 100vh !important;
                        width: 16rem !important;
                    `;
                    console.log('Sidebar forced visibility applied');
                }

                // Force dropdown menus to be visible when active
                const activeDropdowns = document.querySelectorAll('#sidebar .dropdown-menu:not(.hidden)');
                activeDropdowns.forEach(dropdown => {
                    dropdown.style.maxHeight = '2000px';
                    dropdown.style.opacity = '1';
                    console.log('Active dropdown visibility ensured');
                });
            }, 200);
        });
    </script>

    {{-- Additional scripts from views --}}
    @stack('scripts')
</body>
</html>

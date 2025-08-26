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
    @vite(['resources/css/app.css', 'resources/js/kepegawaian-universitas/index.js'])

    {{-- External Libraries --}}
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
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Ensure main content container has proper structure */
        #main-content {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh !important;
            margin-left: 16rem !important; /* 256px for sidebar */
            width: calc(100% - 16rem) !important;
        }

        /* Ensure main content area takes remaining space */
        main {
            flex: 1 !important;
            overflow-y: auto !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* Fix sidebar positioning */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 30 !important;
            width: 16rem !important; /* 256px */
            height: 100vh !important;
        }

        /* Ensure content doesn't overlap with sidebar */
        .flex.h-screen {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }

        /* Ensure header is at the very top */
        #main-content > header {
            position: sticky !important;
            top: 0 !important;
            z-index: 50 !important;
            background: white !important;
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* Remove any default body margins */
        body {
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: hidden !important;
        }

        /* Nested dropdown specific styles */
        .nested-dropdown-container {
            position: relative;
            z-index: 35;
        }

        .nested-dropdown {
            margin-left: 1rem;
            border-left: 2px solid #e5e7eb;
            padding-left: 0.5rem;
            z-index: 36;
            position: relative;
            background: white;
            overflow: hidden;
        }

        .nested-dropdown-container button[data-nested="true"] {
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            pointer-events: auto;
            position: relative;
            z-index: 37;
        }

        .nested-dropdown:not(.hidden) {
            max-height: 1000px;
            opacity: 1;
        }

        /* Parent dropdowns */
        #sidebar .dropdown-menu:not(.nested-dropdown) {
            z-index: 34;
            position: relative;
        }

        /* Parent dropdown that contains nested dropdowns */
        #sidebar .dropdown-menu:has(.nested-dropdown-container) {
            z-index: 35;
        }

        /* Prevent event bubbling for nested dropdowns */
        .nested-dropdown-container button[data-nested="true"]:focus {
            outline: none;
        }

        /* Ensure proper spacing for nested dropdown items */
        .nested-dropdown .relative a {
            padding-left: 1rem;
            border-left: 1px solid #f3f4f6;
        }

        /* Prevent nested dropdown from affecting parent dropdown visibility */
        .nested-dropdown-container {
            isolation: isolate;
        }

        /* Ensure nested dropdown stays within parent bounds */
        .nested-dropdown {
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Specific styles for nested dropdown when hidden */
        .nested-dropdown.hidden {
            max-height: 0 !important;
            opacity: 0 !important;
            overflow: hidden;
        }

        /* Specific styles for nested dropdown when visible */
        .nested-dropdown:not(.hidden) {
            max-height: 1000px !important;
            opacity: 1 !important;
        }

        /* Ensure parent dropdown stays open when nested is toggled */
        .dropdown-menu:has(.nested-dropdown-container) {
            overflow: visible !important;
        }

        /* Prevent nested dropdown from closing parent */
        .nested-dropdown-container button[data-nested="true"] {
            pointer-events: auto !important;
        }

        /* Ensure proper z-index stacking */
        .dropdown-menu {
            position: relative;
        }

        .dropdown-menu:has(.nested-dropdown-container) {
            z-index: 35 !important;
        }

        .nested-dropdown-container {
            z-index: 36 !important;
        }

        .nested-dropdown {
            z-index: 37 !important;
        }

        /* Admin Univ Usulan specific styles */
        .kepegawaian-universitas-dashboard {
            /* Custom styles for admin univ usulan dashboard */
        }

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
        @include('backend.components.sidebar-kepegawaian-universitas')

        {{-- Main Content Container --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300">
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

    {{-- Specific JavaScript for Nested Dropdown Fix --}}
    <script>
        // Wait for all other scripts to load
        window.addEventListener('load', function() {
            console.log('=== NESTED DROPDOWN FIX INITIALIZED ===');
            
            // Remove all existing event listeners from dropdown buttons
            const allDropdownButtons = document.querySelectorAll('button[data-collapse-toggle]');
            console.log('Found dropdown buttons:', allDropdownButtons.length);
            
            allDropdownButtons.forEach((button, index) => {
                console.log(`Button ${index}:`, button.getAttribute('data-collapse-toggle'));
                
                // Clone the button to remove all event listeners
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Add our specific event listener
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    const targetId = this.getAttribute('data-collapse-toggle');
                    const target = document.getElementById(targetId);
                    const isNested = this.hasAttribute('data-nested');
                    
                    console.log('Button clicked:', {
                        targetId: targetId,
                        isNested: isNested,
                        target: target
                    });
                    
                    if (!target) {
                        console.error('Target not found:', targetId);
                        return;
                    }
                    
                    const isHidden = target.classList.contains('hidden');
                    
                    if (isNested) {
                        console.log('Handling NESTED dropdown');
                        
                        // For nested dropdowns, ONLY toggle this one
                        if (isHidden) {
                            target.classList.remove('hidden');
                            target.style.maxHeight = '1000px';
                            target.style.opacity = '1';
                            console.log('Nested dropdown OPENED');
                        } else {
                            target.classList.add('hidden');
                            target.style.maxHeight = '0';
                            target.style.opacity = '0';
                            console.log('Nested dropdown CLOSED');
                        }
                        
                        // Update icon
                        const icon = this.querySelector('[data-lucide="chevron-down"]');
                        if (icon) {
                            icon.style.transform = isHidden ? 'rotate(180deg)' : '';
                        }
                        
                        // Update aria
                        this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                        
                    } else {
                        console.log('Handling PARENT dropdown');
                        
                        // For parent dropdowns, close other parent dropdowns but NOT those with nested
                        const allDropdowns = document.querySelectorAll('.dropdown-menu');
                        
                        allDropdowns.forEach(dropdown => {
                            if (dropdown.id !== targetId) {
                                // Check if this dropdown contains nested dropdowns
                                const hasNested = dropdown.querySelector('.nested-dropdown-container') !== null;
                                
                                console.log('Checking dropdown:', {
                                    id: dropdown.id,
                                    hasNested: hasNested,
                                    willClose: !hasNested
                                });
                                
                                if (!hasNested) {
                                    // Close this dropdown
                                    dropdown.classList.add('hidden');
                                    dropdown.style.maxHeight = '0';
                                    dropdown.style.opacity = '0';
                                    
                                    // Reset its button
                                    const otherButton = document.querySelector(`[data-collapse-toggle="${dropdown.id}"]`);
                                    if (otherButton) {
                                        otherButton.setAttribute('aria-expanded', 'false');
                                        const otherIcon = otherButton.querySelector('[data-lucide="chevron-down"]');
                                        if (otherIcon) {
                                            otherIcon.style.transform = '';
                                        }
                                    }
                                    
                                    console.log('CLOSED dropdown:', dropdown.id);
                                } else {
                                    console.log('KEPT OPEN dropdown (has nested):', dropdown.id);
                                }
                            }
                        });
                        
                        // Toggle the current dropdown
                        if (isHidden) {
                            target.classList.remove('hidden');
                            target.style.maxHeight = '2000px';
                            target.style.opacity = '1';
                            console.log('Parent dropdown OPENED');
                        } else {
                            target.classList.add('hidden');
                            target.style.maxHeight = '0';
                            target.style.opacity = '0';
                            console.log('Parent dropdown CLOSED');
                        }
                        
                        // Update icon
                        const icon = this.querySelector('[data-lucide="chevron-down"]');
                        if (icon) {
                            icon.style.transform = isHidden ? 'rotate(180deg)' : '';
                        }
                        
                        // Update aria
                        this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                    }
                });
            });
            
            console.log('=== NESTED DROPDOWN FIX COMPLETE ===');
        });
    </script>
</body>
</html>

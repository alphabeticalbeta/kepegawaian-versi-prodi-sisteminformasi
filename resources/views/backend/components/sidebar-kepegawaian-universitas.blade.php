<aside id="sidebar" class="sidebar w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 flex flex-col">
    <script>
        // Sidebar state management
        document.addEventListener('DOMContentLoaded', function() {
            // Get current active menu
            const currentUrl = window.location.pathname + window.location.search;
            const currentJenis = new URLSearchParams(window.location.search).get('jenis');
            
            // Determine which dropdown should be open
            let activeDropdown = null;
            
            // Check if we're on a usulan page
            if (currentUrl.includes('/periode-usulan') || currentUrl.includes('/usulan/')) {
                activeDropdown = 'dropdown-usulan';
                
                // If it's a jabatan type, also open the nested dropdown
                if (currentJenis && ['jabatan-dosen-regular', 'jabatan-dosen-pengangkatan'].includes(currentJenis)) {
                    localStorage.setItem('sidebar-active-nested', 'dropdown-jabatan-nested');
                }
            }
            // Check if we're on a master data page
            else if (currentUrl.includes('/data-pegawai') || currentUrl.includes('/role-pegawai') || 
                     currentUrl.includes('/unitkerja') || currentUrl.includes('/pangkat') || 
                     currentUrl.includes('/jabatan')) {
                activeDropdown = 'dropdown-master';
            }
            // Check if we're on dashboard
            else if (currentUrl.includes('/dashboard')) {
                activeDropdown = 'dashboard';
            }
            
            // Store active dropdown in localStorage
            if (activeDropdown) {
                localStorage.setItem('sidebar-active-dropdown', activeDropdown);
            }
            
            // Open the active dropdown on page load
            setTimeout(() => {
                const activeDropdownId = localStorage.getItem('sidebar-active-dropdown');
                if (activeDropdownId && activeDropdownId !== 'dashboard') {
                    const dropdown = document.getElementById(activeDropdownId);
                    if (dropdown) {
                        dropdown.classList.remove('hidden');
                        
                        // Update button state
                        const button = document.querySelector(`[data-collapse-toggle="${activeDropdownId}"]`);
                        if (button) {
                            button.setAttribute('aria-expanded', 'true');
                            const chevron = button.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.style.transform = 'rotate(180deg)';
                            }
                        }
                    }
                }
                
                // Open nested dropdown if needed
                const activeNested = localStorage.getItem('sidebar-active-nested');
                if (activeNested) {
                    const nestedDropdown = document.getElementById(activeNested);
                    if (nestedDropdown) {
                        nestedDropdown.classList.remove('hidden');
                        
                        // Update nested button state
                        const nestedButton = document.querySelector(`[data-collapse-toggle="${activeNested}"]`);
                        if (nestedButton) {
                            nestedButton.setAttribute('aria-expanded', 'true');
                            const chevron = nestedButton.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.style.transform = 'rotate(180deg)';
                            }
                        }
                    }
                }
            }, 100);
        });
    </script>
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow-sm flex-shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-center sidebar-text">Admin Usulan Kepegawaian</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-2">

        {{-- Dashboard Semua Usulan Aktif --}}
        <div class="mb-2 px-2">
            <a href="{{ route('backend.kepegawaian-universitas.dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg group transition {{ request()->routeIs('backend.kepegawaian-universitas.dashboard') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="font-medium sidebar-text">Dashboard</span>
            </a>
        </div>

        {{-- Master Data Dropdown --}}
        @php
            $masterMenus = [
                ['route' => 'backend.kepegawaian-universitas.data-pegawai.index', 'icon' => 'users', 'label' => 'Data Pegawai', 'pattern' => 'backend.kepegawaian-universitas.data-pegawai.*'],
                ['route' => 'backend.kepegawaian-universitas.role-pegawai.index', 'icon' => 'user-cog', 'label' => 'Role Pegawai', 'pattern' => 'backend.kepegawaian-universitas.role-pegawai.*'],
                ['route' => 'backend.kepegawaian-universitas.unitkerja.index', 'icon' => 'building-2', 'label' => 'Unit Kerja', 'pattern' => 'backend.kepegawaian-universitas.unitkerja.*'],
                ['route' => 'backend.kepegawaian-universitas.pangkat.index', 'icon' => 'award', 'label' => 'Pangkat', 'pattern' => 'backend.kepegawaian-universitas.pangkat.*'],
                ['route' => 'backend.kepegawaian-universitas.jabatan.index', 'icon' => 'briefcase', 'label' => 'Jabatan', 'pattern' => 'backend.kepegawaian-universitas.jabatan.*'],
            ];
            $isMasterActive = collect($masterMenus)->contains(fn($menu) => request()->routeIs($menu['pattern']));
        @endphp

        <div class="mb-2 px-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isMasterActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-master"
                    data-collapse-toggle="dropdown-master"
                    aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="database" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Master Data</span>
                </div>
                {{-- PERBAIKAN: Kelas .sidebar-text dihapus dari ikon ini --}}
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-master" class="dropdown-menu {{ $isMasterActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                @foreach($masterMenus as $menu)
                <div class="relative">
                    <a href="{{ route($menu['route']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs($menu['pattern']) ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">{{ $menu['label'] }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Usulan Dropdown --}}
        @php $isUsulanActive = request()->is('*/usulan/*') || request()->routeIs('backend.kepegawaian-universitas.usulan.*'); @endphp
        <div class="mb-2 px-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isUsulanActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-usulan"
                    data-collapse-toggle="dropdown-usulan"
                    aria-expanded="{{ $isUsulanActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan</span>
                </div>
                {{-- PERBAIKAN: Kelas .sidebar-text dihapus dari ikon ini --}}
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isUsulanActive ? 'rotate-180' : '' }}"></i>
            </button>
                <div id="dropdown-usulan" class="dropdown-menu {{ $isUsulanActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                {{-- Dropdown untuk Usulan Jabatan --}}
                @php 
                    $isJabatanActive = in_array(request()->get('jenis'), ['jabatan-dosen-regular', 'jabatan-dosen-pengangkatan']);
                @endphp
                <div class="relative nested-dropdown-container">
                    <button type="button"
                            class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg group transition {{ $isJabatanActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                            aria-controls="dropdown-jabatan-nested"
                            data-collapse-toggle="dropdown-jabatan-nested"
                            data-nested="true"
                            aria-expanded="{{ $isJabatanActive ? 'true' : 'false' }}">
                        <div class="flex items-center">
                            <i data-lucide="file-user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                            <span class="font-medium sidebar-text">Usulan Jabatan</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isJabatanActive ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div id="dropdown-jabatan-nested" class="dropdown-menu nested-dropdown {{ $isJabatanActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                        <div class="relative">
                            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'jabatan-dosen-regular']) }}"
                               class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->get('jenis') == 'jabatan-dosen-regular' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i data-lucide="user-graduate" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="font-medium sidebar-text">Jabatan Dosen Regular</span>
                            </a>
                        </div>
                        <div class="relative">
                            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'jabatan-dosen-pengangkatan']) }}"
                               class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->get('jenis') == 'jabatan-dosen-pengangkatan' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="font-medium sidebar-text">Jabatan Dosen Pengangkatan Pertama</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'kepangkatan']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'kepangkatan' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="trending-up" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Kepangkatan</span>
                    </a>
                </div>
                
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'nuptk']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'nuptk' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan NUPTK</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'laporan-lkd']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'laporan-lkd' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan LKD</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'presensi']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'presensi' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="clipboard-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Presensi</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'penyesuaian-masa-kerja']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'penyesuaian-masa-kerja' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="clock" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Penyesuaian Masa Kerja</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'ujian-dinas-ijazah']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'ujian-dinas-ijazah' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="book-marked" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Ujian Dinas & Ijazah</span>
                    </a>
                </div>

                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'laporan-serdos']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'laporan-serdos' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="file-check-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan Serdos</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'pensiun']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pensiun' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-minus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pensiun</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'pencantuman-gelar']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pencantuman-gelar' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pencantuman Gelar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'id-sinta-sister']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'id-sinta-sister' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="link" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan ID SINTA ke SISTER</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'satyalancana']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'satyalancana' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="medal" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Satyalancana</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'tugas-belajar']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'tugas-belajar' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Tugas Belajar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index', ['jenis' => 'pengaktifan-kembali']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pengaktifan-kembali' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pengaktifan Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        // Enhanced dropdown management
        document.addEventListener('DOMContentLoaded', function() {
            // Handle dropdown toggles
            document.querySelectorAll('[data-collapse-toggle]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const targetId = this.getAttribute('data-collapse-toggle');
                    const target = document.getElementById(targetId);
                    const isNested = this.hasAttribute('data-nested');
                    
                    if (target) {
                        const isHidden = target.classList.contains('hidden');
                        
                        if (isHidden) {
                            // Open dropdown
                            target.classList.remove('hidden');
                            this.setAttribute('aria-expanded', 'true');
                            
                            // Rotate chevron
                            const chevron = this.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.style.transform = 'rotate(180deg)';
                            }
                            
                            // Store state
                            if (isNested) {
                                localStorage.setItem('sidebar-active-nested', targetId);
                            } else {
                                localStorage.setItem('sidebar-active-dropdown', targetId);
                                // Clear nested state when parent is opened
                                localStorage.removeItem('sidebar-active-nested');
                            }
                        } else {
                            // Close dropdown
                            target.classList.add('hidden');
                            this.setAttribute('aria-expanded', 'false');
                            
                            // Reset chevron
                            const chevron = this.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.style.transform = 'rotate(0deg)';
                            }
                            
                            // Clear state
                            if (isNested) {
                                localStorage.removeItem('sidebar-active-nested');
                            } else {
                                localStorage.removeItem('sidebar-active-dropdown');
                                // Also clear nested state when parent is closed
                                localStorage.removeItem('sidebar-active-nested');
                            }
                        }
                    }
                });
            });
            
            // Handle navigation clicks to update active state
            document.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    const jenis = new URLSearchParams(href.split('?')[1] || '').get('jenis');
                    
                    // Update active dropdown based on link
                    if (href.includes('/periode-usulan') || href.includes('/usulan/')) {
                        localStorage.setItem('sidebar-active-dropdown', 'dropdown-usulan');
                        
                        if (jenis && ['jabatan-dosen-regular', 'jabatan-dosen-pengangkatan'].includes(jenis)) {
                            localStorage.setItem('sidebar-active-nested', 'dropdown-jabatan-nested');
                        } else {
                            localStorage.removeItem('sidebar-active-nested');
                        }
                    } else if (href.includes('/data-pegawai') || href.includes('/role-pegawai') || 
                              href.includes('/unitkerja') || href.includes('/pangkat') || 
                              href.includes('/jabatan')) {
                        localStorage.setItem('sidebar-active-dropdown', 'dropdown-master');
                        localStorage.removeItem('sidebar-active-nested');
                    } else if (href.includes('/dashboard')) {
                        localStorage.setItem('sidebar-active-dropdown', 'dashboard');
                        localStorage.removeItem('sidebar-active-nested');
                    }
                });
            });
        });
    </script>
</aside>

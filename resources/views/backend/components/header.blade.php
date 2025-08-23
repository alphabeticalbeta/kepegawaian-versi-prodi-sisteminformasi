<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-3 h-16 flex items-center justify-between flex-shrink-0 z-40 sticky top-0 transition-colors">
    {{-- Bagian kiri header tidak berubah --}}
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="p-1.5 rounded-lg hover:bg-gray-100">
            <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
        </button>
    </div>

    <div class="flex items-center gap-4">
        @auth('pegawai')
            @php
                $user = Auth::guard('pegawai')->user();
                $roles = $user->roles->pluck('name');
                $availableDashboards = [];

                // ==========================================================
                // ===== PERBAIKAN LOGIKA DIMULAI DI SINI ===================
                // ==========================================================
                // Periksa setiap role yang mungkin dimiliki pengguna
                if ($roles->contains('Admin Universitas')) {
                    $availableDashboards['Admin Universitas'] = route('admin-universitas.dashboard');
                }
                if ($roles->contains('Kepegawaian Universitas')) {
                    $availableDashboards['Kepegawaian Universitas'] = route('backend.kepegawaian-universitas.dashboard');
                }
                if ($roles->contains('Admin Fakultas')) {
                    $availableDashboards['Admin Fakultas'] = route('admin-fakultas.dashboard');
                }
                if ($roles->contains('Admin Keuangan')) {
                    $availableDashboards['Admin Keuangan'] = route('admin-keuangan.dashboard');
                }
                if ($roles->contains('Tim Senat')) {
                    $availableDashboards['Tim Senat'] = route('tim-senat.dashboard');
                }
                if ($roles->contains('Penilai Universitas')) {
                    $availableDashboards['Penilai'] = route('penilai-universitas.dashboard');
                }

                // Selalu tambahkan dashboard default Pegawai
                $availableDashboards['Pegawai'] = route('pegawai-unmul.dashboard-pegawai-unmul');
                // ==========================================================
                // ======================= AKHIR PERBAIKAN ====================
                // ==========================================================

                // Check user eligibility for usulan (PNS only)
                $canCreateUsulan = in_array($user->status_kepegawaian, ['Dosen PNS', 'Tenaga Kependidikan PNS']);

                // Count documents uploaded
                $documentFields = [
                    'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                    'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
                    'sk_cpns', 'sk_pns', 'pak_konversi', 'sk_penyetaraan_ijazah',
                    'disertasi_thesis_terakhir'
                ];
                $uploadedDocs = collect($documentFields)->filter(fn($field) => !empty($user->$field))->count();
                $totalDocs = count($documentFields);
                $profileCompleteness = round(($uploadedDocs / $totalDocs) * 100);
            @endphp

                        {{-- Dark Mode Toggle --}}
            <button
                id="dark-mode-toggle"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full relative transition-colors"
            >
                <i data-lucide="moon" id="dark-mode-icon" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>

            {{-- Tombol notifikasi --}}
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full relative transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                {{-- Notification badge (if needed) --}}
                {{-- <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span> --}}
            </button>

            <div class="w-px h-6 bg-gray-200 dark:bg-gray-600"></div>

            {{-- MENU PINDAH HALAMAN (TERPISAH) --}}
            @if(count($availableDashboards) > 1)
            <div class="relative">
                <button onclick="toggleRoleDropdown()" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="arrow-right-left" class="w-5 h-5 text-gray-600"></i>
                    <span class="hidden sm:block text-sm font-medium text-gray-700">Pindah Halaman</span>
                </button>
                <div id="role-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg hidden z-50 border">
                    <div class="py-1">
                        <div class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-400">Pindah Ke Dashboard</div>
                        @foreach($availableDashboards as $name => $url)
                            <a href="{{ $url }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Enhanced Dropdown Profil --}}
            <div class="relative">
                <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100">
                    <div class="relative">
                        <img
                            src="{{ $user->foto ? route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&size=32&background=6366f1&color=fff' }}"
                            alt="{{ $user->nama_lengkap }}"
                            class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&size=32&background=6366f1&color=fff'"
                        />
                        {{-- Profile completion indicator --}}
                        @if($profileCompleteness < 100)
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-orange-400 border-2 border-white rounded-full"
                                 title="Profil belum lengkap ({{ $profileCompleteness }}%)"></div>
                        @else
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white rounded-full"
                                 title="Profil lengkap"></div>
                        @endif
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="text-sm font-medium text-gray-700">{{ $user->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $user->nip }}</div>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                </button>

                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg hidden z-50 border">
                    {{-- Profile Header --}}
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                                                <img
                        src="{{ $user->foto ? route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&size=48&background=6366f1&color=fff' }}"
                        alt="{{ $user->nama_lengkap }}"
                        class="w-12 h-12 rounded-full object-cover"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&size=48&background=6366f1&color=fff'"
                    />
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">
                                    {{ $user->gelar_depan ? $user->gelar_depan . ' ' : '' }}{{ $user->nama_lengkap }}{{ $user->gelar_belakang ? ', ' . $user->gelar_belakang : '' }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $user->nip }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ in_array($user->status_kepegawaian, ['Dosen PNS', 'Tenaga Kependidikan PNS']) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $user->status_kepegawaian }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Completeness --}}
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Kelengkapan Profil</span>
                                <span class="font-medium">{{ $profileCompleteness }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300
                                    {{ $profileCompleteness == 100 ? 'bg-green-500' : ($profileCompleteness >= 70 ? 'bg-blue-500' : 'bg-orange-500') }}"
                                     style="width: {{ $profileCompleteness }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-2">
                        {{-- Profile Section --}}
                        <div class="px-3 py-1">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Profil</div>
                        </div>

                        <a href="{{ route('pegawai-unmul.profile.show') }}"
                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Lihat Profil</div>
                                <div class="text-xs text-gray-500">Data pribadi & kepegawaian</div>
                            </div>
                        </a>

                        <a href="{{ route('pegawai-unmul.profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i data-lucide="edit" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Edit Profil</div>
                                <div class="text-xs text-gray-500">Perbarui informasi Anda</div>
                            </div>
                            @if($profileCompleteness < 100)
                                <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                            @endif
                        </a>

                        {{-- Quick Actions --}}
                        <div class="px-3 py-1 mt-2">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi Cepat</div>
                        </div>

                        @if($canCreateUsulan)
                            <a href="{{ route('pegawai-unmul.usulan-jabatan.create') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">Buat Usulan Baru</div>
                                    <div class="text-xs text-gray-500">Usulan jabatan atau pangkat</div>
                                </div>
                            </a>
                        @endif

                        <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i data-lucide="file-text" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Usulan Saya</div>
                                <div class="text-xs text-gray-500">Lihat riwayat usulan</div>
                            </div>
                        </a>

                        {{-- Settings --}}
                        <div class="px-3 py-1 mt-2">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengaturan</div>
                        </div>

                        <button onclick="openPasswordModal()"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full text-left">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i data-lucide="key" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Ubah Password</div>
                                <div class="text-xs text-gray-500">Keamanan akun</div>
                            </div>
                        </button>
                    </div>

                    {{-- Logout Section --}}
                    <div class="border-t border-gray-100 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">Sign Out</div>
                                    <div class="text-xs text-red-500">Keluar dari sistem</div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth

        @guest('pegawai')
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Login</a>
        @endguest
    </div>
</header>

{{-- Quick Password Change Modal --}}
<div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ubah Password</h3>
            <button type="button" onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form action="{{ route('pegawai-unmul.profile.update') }}" method="POST" class="mt-4">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="new_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closePasswordModal()"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>



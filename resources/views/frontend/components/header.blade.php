<header class="bg-white shadow-sm">
    <nav class="bg-black text-white">
        <div class="w-full flex flex-wrap items-center justify-between px-2 py-3">

            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo Universitas Mulawarman" class="h-14">
                <div class="flex flex-col leading-tight">
                    <span class="font-bold">BAGIAN KEPEGAWAIAN</span>
                    <span class="text-sm">BIRO UMUM DAN KEUANGAN</span>
                    <span class="text-sm">UNIVERSITAS MULAWARMAN</span>
                </div>
            </div>

            <ul class="mt-4 flex flex-wrap items-center justify-center gap-1 lg:mt-0 lg:gap-2">
                <li>
                    <a href="{{ route('frontend.home') }}" class="rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        HOME
                    </a>
                </li>
                
                <li class="relative group">
                    <a href="#" class="flex items-center gap-1 rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        PROFIL
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute left-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('profil.visi-misi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Visi dan Misi</a>
                        <a href="{{ route('profil.struktur-organisasi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Struktur Organisasi</a>
                    </div>
                </li>

                <li class="relative group">
                    <a href="#" class="flex items-center gap-1 rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        LAYANAN
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute left-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('layanan.aplikasi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Aplikasi</a>
                        <a href="{{ route('layanan.usulan-kepegawaian') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Usulan Kepegawaian</a>
                    </div>
                </li>

                <li>
                    <a href="#" class="rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        JABATAN
                    </a>
                </li>
                <li>
                    <a href="#" class="rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        STATISTIK
                    </a>
                </li>
                <li>
                    <a href="{{ route('blangko.surat') }}" class="rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        BLANGKO SURAT
                    </a>
                </li>

                <li class="relative group">
                    <a href="#" class="flex items-center gap-1 rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        DASAR HUKUM
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute left-0 w-56 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('keputusan') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Keputusan</a>
                        <a href="{{ route('pedoman') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Pedoman</a>
                        <a href="{{ route('peraturan') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Peraturan</a>
                        <a href="{{ route('surat-edaran') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Surat Edaran</a>
                        <a href="{{ route('surat-kementerian') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Surat Kementerian</a>
                        <a href="{{ route('surat-rektor-unmul') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Surat Rektor Universitas Mulawarman</a>
                        <a href="{{ route('undang-undang') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Undang-Undang</a>
                    </div>
                </li>

                <li class="relative group">
                    <a href="#" class="flex items-center gap-1 rounded px-2 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        INFORMASI
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute right-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('frontend.berita') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Berita</a>
                        <a href="{{ route('frontend.pengumuman') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Pengumuman</a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('login') }}" class="rounded px-3 py-2 text-sm font-semibold bg-yellow-400 hover:bg-yellow-500 transition-colors duration-300 text-black flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        LOGIN
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="bg-yellow-400 p-5">
        <div class="w-full grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 px-2">
            <a href="#" class="flex items-center justify-between rounded-md bg-gray-100 p-4 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-gray-800">
                    <h3 class="mb-1 text-2xl font-bold text-gray-900">SISTER</h3>
                    <p class="text-xs leading-tight">Sistem Informasi Sumberdaya Terintegrasi</p>
                </div>
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-12">
            </a>
            <a href="#" class="flex items-center justify-between rounded-md bg-gray-100 p-4 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-gray-800">
                    <h3 class="mb-1 text-2xl font-bold text-gray-900">DIKBUDHR</h3>
                    <p class="text-xs leading-tight">Direktorat Kepercayaan Terhadap Tuhan YME dan Tradisi</p>
                </div>
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-12">
            </a>
            <a href="#" class="flex items-center justify-between rounded-md bg-gray-100 p-4 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-gray-800">
                    <h3 class="mb-1 text-2xl font-bold text-gray-900">E-SKP</h3>
                    <p class="text-xs leading-tight">Aplikasi Penilaian Kinerja Kementerian Pendidikan...</p>
                </div>
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-12">
            </a>
            <a href="#" class="flex items-center justify-between rounded-md bg-gray-100 p-4 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-gray-800">
                    <h3 class="mb-1 text-2xl font-bold text-gray-900">SIDAK UNMUL</h3>
                    <p class="text-xs leading-tight">Sistem Informasi Database Administrasi Kepegawaian</p>
                </div>
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-12">
            </a>
        </div>
    </div>
    </header>

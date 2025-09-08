<header class="bg-white shadow-sm">
    <nav class="bg-black text-white">
        <div class="container mx-auto flex flex-wrap items-center justify-between px-4 py-3 lg:px-6">

            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo Universitas Mulawarman" class="h-14">
                <div class="flex flex-col leading-tight">
                    <span class="font-bold">BAGIAN KEPEGAWAIAN</span>
                    <span class="text-sm">BIRO UMUM DAN KEUANGAN</span>
                    <span class="text-sm">UNIVERSITAS MULAWARMAN</span>
                </div>
            </div>

            <ul class="mt-4 flex flex-wrap items-center justify-center gap-1 lg:mt-0">
                <li class="relative group">
                    <a href="#" class="flex items-center gap-2 rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        PROFIL
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute left-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('profil.visi-misi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Visi dan Misi</a>
                        <a href="{{ route('profil.struktur-organisasi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Struktur Organisasi</a>
                    </div>
                </li>

                <li class="relative group">
                    <a href="#" class="flex items-center gap-2 rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        LAYANAN
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute left-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('layanan.aplikasi') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Aplikasi</a>
                        <a href="{{ route('layanan.usulan-kepegawaian') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Usulan Kepegawaian</a>
                    </div>
                </li>

                <li><a href="#" class="rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">JABATAN</a></li>
                <li><a href="#" class="rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">STATISTIK</a></li>
                <li><a href="{{ route('blangko.surat') }}" class="rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">BLANGKO SURAT</a></li>

                <li class="relative group">
                    <a href="#" class="flex items-center gap-2 rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
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
                    <a href="#" class="flex items-center gap-2 rounded px-3 py-2 text-sm font-semibold transition-colors duration-300 hover:bg-gray-800">
                        INFORMASI
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </a>
                    <div class="absolute right-0 w-48 bg-white text-black rounded-md shadow-lg hidden group-hover:block z-10 pt-1">
                        <a href="{{ route('berita') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Berita</a>
                        <a href="{{ route('pengumuman') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Pengumuman</a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('login') }}" class="rounded px-4 py-2 text-sm font-semibold bg-yellow-400 hover:bg-yellow-500 transition-colors duration-300 text-black">LOGIN</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="bg-yellow-400 p-5">
        <div class="container mx-auto grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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

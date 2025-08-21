@extends('backend.layouts.roles.admin-fakultas.app')

@section('title', $config['title'])

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                @foreach($config['breadcrumbs'] as $index => $breadcrumb)
                    <li class="inline-flex items-center">
                        @if(!$loop->last && $breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-{{ $config['color'] }}-600">
                                @if($loop->first)
                                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                @endif
                                {{ $breadcrumb['name'] }}
                            </a>
                        @else
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $breadcrumb['name'] }}</span>
                        @endif
                        @if(!$loop->last)
                            <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-gray-400"></i>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- Header Halaman -->
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-{{ $config['color'] }}-100 mr-4">
                    <i data-lucide="{{ $config['icon'] }}" class="w-6 h-6 text-{{ $config['color'] }}-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $config['title'] }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $config['description'] }}</p>
                </div>
            </div>

            @if($unitKerja)
                <div class="mt-4 p-4 bg-{{ $config['color'] }}-50 border border-{{ $config['color'] }}-200 rounded-lg">
                    <div class="flex items-center">
                        <i data-lucide="building" class="w-5 h-5 text-{{ $config['color'] }}-600 mr-2"></i>
                        <span class="text-sm text-{{ $config['color'] }}-700">
                            <strong>{{ $unitKerja->nama }}</strong> |
                            Total periode: {{ $config['data']->total() }} |
                            Total usulan menunggu review: {{ $config['data']->sum('jumlah_pengusul') }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Alert jika tidak ada unit kerja -->
        @if(!$unitKerja)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Konfigurasi Unit Kerja Bermasalah</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Akun Anda belum dikaitkan dengan unit kerja fakultas. Silakan hubungi Administrator.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Utama -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    Daftar Periode {{ $config['title'] }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Angka pada kolom "Review" menunjukkan jumlah usulan yang menunggu verifikasi Anda.
                </p>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($config['columns'] as $key => $label)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $label }}
                                </th>
                            @endforeach
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($config['data'] as $periode)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $periode->nama_periode }}</div>
                                    <div class="text-sm text-gray-500">{{ $periode->tahun_periode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800">
                                        {{ ucfirst($periode->jenis_usulan) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $periode->tanggal_mulai->isoFormat('D MMM') }} - {{ $periode->tanggal_selesai->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->status == 'Buka')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Buka
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Tutup
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->jumlah_pengusul > 0)
                                        <div class="relative group">
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full cursor-help">
                                                {{ $periode->jumlah_pengusul }}
                                            </span>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                {{ $periode->jumlah_pengusul }} usulan menunggu review
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-100 bg-gray-600 rounded-full">
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($periode->jumlah_pengusul > 0)
                                        <a href="{{ route('admin-fakultas.periode.pendaftar', $periode->id) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-{{ $config['color'] }}-600 hover:bg-{{ $config['color'] }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $config['color'] }}-500 transition-colors duration-200">
                                            <i data-lucide="eye" class="h-4 w-4 mr-2"></i>
                                            Review ({{ $periode->jumlah_pengusul }})
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                                            <i data-lucide="minus" class="h-4 w-4 mr-2"></i>
                                            Tidak Ada
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($config['columns']) + 1 }}" class="px-6 py-12 text-center">
                                    <div class="text-center">
                                        <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400 mb-4"></i>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Periode {{ $config['title'] }}</h3>
                                        @if(!$unitKerja)
                                            <p class="mt-1 text-sm text-red-600">Unit kerja tidak ditemukan. Periksa pengaturan akun Anda.</p>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500">
                                                Saat ini tidak ada periode {{ strtolower($config['title']) }} untuk fakultas <strong>{{ $unitKerja->nama }}</strong>.
                                            </p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($config['data']->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $config['data']->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

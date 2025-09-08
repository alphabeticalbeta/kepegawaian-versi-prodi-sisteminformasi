@extends('frontend.layouts.app')

@section('title', 'Visi dan Misi - Universitas Mulawarman')

@push('styles')
<style>
/* Prose styling for content */
.prose {
    color: #374151;
    max-width: none;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose strong {
    color: #111827;
    font-weight: 600;
}

.prose em {
    color: #4B5563;
    font-style: italic;
}

.prose ol {
    list-style-type: decimal;
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose ul {
    list-style-type: disc;
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.prose li::marker {
    color: #6B7280;
}

/* Card hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-16 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Visi dan Misi
                </h1>
                <p class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 -mt-8 px-6 sm:px-8 pb-2">
        <div class="mx-auto max-w-full pt-5 mt-5">
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-12">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-indigo-500 bg-white transition ease-in-out duration-150">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat data...
                </div>
            </div>

            <!-- Content Grid -->
            <div id="contentGrid" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8 -mx-8 pb-2">
                <!-- Visi Card -->
                <div id="visiCard" class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300 mx-8">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                    <i data-lucide="eye" class="h-7 w-7 text-white"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-2xl font-bold text-white">Visi Universitas Mulawarman</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div id="visiContent" class="prose prose-lg max-w-none">
                            <!-- Visi content will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Misi Card -->
                <div id="misiCard" class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300 mx-8">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                    <i data-lucide="list-checks" class="h-7 w-7 text-white"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-2xl font-bold text-white">Misi Universitas Mulawarman</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div id="misiContent" class="prose prose-lg max-w-none">
                            <!-- Misi content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="text-center py-12 hidden">
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                        <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-red-800 mb-2">Gagal Memuat Data</h3>
                    <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat data visi dan misi.</p>
                    <button onclick="loadVisiMisiData()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                        Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load visi misi data from backend
async function loadVisiMisiData() {
    const loadingState = document.getElementById('loadingState');
    const contentGrid = document.getElementById('contentGrid');
    const errorState = document.getElementById('errorState');

    // Show loading state
    loadingState.classList.remove('hidden');
    contentGrid.classList.add('hidden');
    errorState.classList.add('hidden');

    try {
        // Fetch data from backend API
        const response = await fetch('/api/visi-misi', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            // Process and display data
            displayVisiMisiData(result.data);

            // Show content
            loadingState.classList.add('hidden');
            contentGrid.classList.remove('hidden');
        } else {
            throw new Error(result.message || 'Failed to fetch data');
        }

    } catch (error) {
        console.error('Error loading visi misi data:', error);

        // Try to show fallback data first
        const fallbackData = [
            {
                id: 1,
                jenis: 'visi',
                konten: '<p><strong>Menjadi universitas terkemuka di Indonesia</strong> yang unggul dalam pengembangan ilmu pengetahuan, teknologi, dan seni untuk kesejahteraan masyarakat.</p>',
                status: 'aktif'
            },
            {
                id: 2,
                jenis: 'misi',
                konten: '<ol><li>Menyelenggarakan <em>pendidikan tinggi yang berkualitas</em> dan relevan dengan kebutuhan masyarakat</li><li>Mengembangkan penelitian yang inovatif dan bermanfaat</li><li>Melaksanakan pengabdian kepada masyarakat yang berkelanjutan</li><li>Membangun tata kelola universitas yang baik dan akuntabel</li></ol>',
                status: 'aktif'
            }
        ];

        // Display fallback data
        displayVisiMisiData(fallbackData);

        // Show content with fallback data
        loadingState.classList.add('hidden');
        contentGrid.classList.remove('hidden');

        // Show a subtle warning that we're using fallback data
        console.warn('Using fallback data due to API error:', error.message);
    }
}

// Display visi misi data
function displayVisiMisiData(data) {
    const visiContent = document.getElementById('visiContent');
    const misiContent = document.getElementById('misiContent');

    // Find visi and misi data
    const visi = data.find(item => item.jenis === 'visi' && item.status === 'aktif');
    const misi = data.find(item => item.jenis === 'misi' && item.status === 'aktif');

    // Display visi
    if (visi) {
        visiContent.innerHTML = visi.konten;
    } else {
        visiContent.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="eye-off" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                <p>Data visi belum tersedia</p>
            </div>
        `;
    }

    // Display misi
    if (misi) {
        misiContent.innerHTML = misi.konten;
    } else {
        misiContent.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="list-x" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                <p>Data misi belum tersedia</p>
            </div>
        `;
    }

    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadVisiMisiData();
});
</script>
@endpush
@endsection

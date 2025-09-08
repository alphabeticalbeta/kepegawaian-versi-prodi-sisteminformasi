@extends('frontend.layouts.app')

@section('title', 'Struktur Organisasi - Universitas Mulawarman')

@section('content')
<!-- Hero Section -->
<div class="text-black py-16 mb-12 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain drop-shadow-lg">
        </div>
        <h1 class="text-5xl md:text-6xl font-extrabold mb-2 text-black drop-shadow-md">Struktur Organisasi</h1>
        <p class="text-5xl md:text-6xl font-extrabold mb-2 text-black drop-shadow-md">Universitas Mulawarman</p>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 pb-2">
    <!-- Loading State -->
    <div id="loadingState" class="min-h-[400px] flex items-center justify-center">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            <p class="mt-4 text-gray-600">Memuat struktur organisasi...</p>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="min-h-[400px] flex flex-col items-center justify-center text-center hidden">
        <div class="text-red-500 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Gagal Memuat Data</h3>
        <p id="errorMessage" class="text-gray-600 mb-4">Terjadi kesalahan saat memuat struktur organisasi.</p>
        <button onclick="loadStrukturOrganisasi()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
            Coba Lagi
        </button>
    </div>

    <!-- Content State -->
    <div id="contentState" class="hidden">
        <div class="bg-white rounded-2xl p-8 shadow-xl text-center mb-8">
            <div class="flex justify-center">
                <img id="strukturImage" src="" alt="Struktur Organisasi Universitas Mulawarman"
                     class="max-w-full max-h-[600px] w-auto h-auto rounded-lg shadow-2xl transition-transform duration-300 hover:scale-105 object-contain">
            </div>
        </div>
    </div>

    <!-- No Data State -->
    <div id="noDataState" class="min-h-[400px] flex flex-col items-center justify-center text-center hidden">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
        <p class="text-gray-600">Struktur organisasi belum tersedia.</p>
    </div>
</div>

<!-- Responsive adjustments -->
<div class="hidden lg:block">
    <style>
        @media (max-width: 1024px) {
            .max-h-\[600px\] {
                max-height: 500px;
            }
        }
        @media (max-width: 768px) {
            .max-h-\[600px\] {
                max-height: 400px;
            }
            .text-5xl {
                font-size: 2.5rem;
            }
            .text-6xl {
                font-size: 3rem;
            }
            .h-32 {
                height: 6rem;
            }
            .p-8 {
                padding: 1.5rem;
            }
        }
    </style>
</div>
@endsection

@push('scripts')
<script>
// Load struktur organisasi data
async function loadStrukturOrganisasi() {
    try {
        // Show loading state
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('errorState').classList.add('hidden');
        document.getElementById('contentState').classList.add('hidden');
        document.getElementById('noDataState').classList.add('hidden');

        const response = await fetch('/api/struktur-organisasi', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success && result.data) {
            // Show content
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorState').classList.add('hidden');
            document.getElementById('noDataState').classList.add('hidden');
            document.getElementById('contentState').classList.remove('hidden');

            // Update image
            const imageElement = document.getElementById('strukturImage');

            imageElement.src = result.data.image_url;
            imageElement.alt = 'Struktur Organisasi Universitas Mulawarman';

            // Add loading event to image
            imageElement.onload = function() {
                console.log('Struktur organisasi image loaded successfully');
            };

            imageElement.onerror = function() {
                console.error('Failed to load struktur organisasi image');
                showError('Gagal memuat gambar struktur organisasi');
            };

        } else {
            // Show no data state
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorState').classList.add('hidden');
            document.getElementById('contentState').classList.add('hidden');
            document.getElementById('noDataState').classList.remove('hidden');
        }

    } catch (error) {
        console.error('Error loading struktur organisasi:', error);

        // Show error state
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('errorState').classList.remove('hidden');
        document.getElementById('contentState').classList.add('hidden');
        document.getElementById('noDataState').classList.add('hidden');

        document.getElementById('errorMessage').textContent = error.message || 'Terjadi kesalahan saat memuat struktur organisasi.';
    }
}

// Show error message
function showError(message) {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('contentState').classList.add('hidden');
    document.getElementById('noDataState').classList.add('hidden');

    document.getElementById('errorMessage').textContent = message;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadStrukturOrganisasi();
});
</script>
@endpush

<!-- Struktur Organisasi Section -->
<section class="py-16 bg-gradient-to-br from-purple-50 to-pink-100">
    <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Struktur Organisasi</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Hierarki dan struktur organisasi Universitas Mulawarman</p>
        </div>

        <!-- Struktur Organisasi Content -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                @if(isset($strukturOrganisasi) && !empty($strukturOrganisasi))
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $strukturOrganisasi['title'] ?? 'Struktur Organisasi Universitas Mulawarman' }}</h3>
                        
                        @if(isset($strukturOrganisasi['image_url']))
                            <div class="mb-6">
                                <img src="{{ $strukturOrganisasi['image_url'] }}" 
                                     alt="{{ $strukturOrganisasi['title'] ?? 'Struktur Organisasi' }}"
                                     class="mx-auto max-w-full h-auto rounded-lg shadow-lg"
                                     onerror="this.style.display='none'">
                            </div>
                        @endif
                        
                        @if(isset($strukturOrganisasi['description']))
                            <p class="text-lg text-gray-600 max-w-4xl mx-auto leading-relaxed">
                                {{ $strukturOrganisasi['description'] }}
                            </p>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 max-w-md mx-auto">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Struktur Organisasi</h3>
                            <p class="text-gray-600 mb-4">Informasi struktur organisasi akan segera tersedia.</p>
                            <a href="{{ route('profil.struktur-organisasi') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors duration-300">
                                Lihat Detail
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Access Button -->
        <div class="text-center mt-8">
            <a href="{{ route('profil.struktur-organisasi') }}"
               class="inline-flex items-center px-8 py-4 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                <span>Lihat Struktur Lengkap</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
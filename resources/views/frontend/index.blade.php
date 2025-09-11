@extends('frontend.layouts.app')

@section('title', 'Beranda - Universitas Mulawarman')

@section('content')
<!-- Hero Section Carousel -->
<section class="p-5" x-data="heroCarousel()">
    <!-- Carousel Container - Full Width -->
    <div class="relative w-full h-screen overflow-hidden bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-800 rounded-2xl shadow-2xl">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <!-- Carousel Slides -->
        <div class="flex w-full h-full transition-transform duration-700 ease-in-out"
             :style="`transform: translateX(-${currentSlide * 100}%)`">

            <!-- Slide 1: Konsisten (Logo & Welcome) -->
            <div class="w-full h-full flex-shrink-0 flex items-center justify-center">
                <div class="text-center max-w-6xl mx-auto px-8">
                    <!-- Logo -->
                    <div class="mb-8 flex justify-center">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-6 shadow-2xl">
                            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-24 w-auto object-contain">
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 drop-shadow-lg">
                        Universitas Mulawarman
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-xl md:text-3xl text-blue-100 mb-8 max-w-4xl mx-auto leading-relaxed">
                        Selamat datang di portal resmi Universitas Mulawarman. Dapatkan informasi terbaru seputar berita, pengumuman, dan struktur organisasi kami.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="#berita"
                           class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            Lihat Berita
                        </a>

                        <a href="#pengumuman"
                           class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            Lihat Pengumuman
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 2-6: Dinamis (Pengumuman Featured) -->
            @if(isset($pengumumanFeatured) && $pengumumanFeatured->count() > 0)
                @foreach($pengumumanFeatured->take(5) as $index => $item)
                <div class="w-full h-full flex-shrink-0 relative">
                    <!-- Background Image -->
                    @if($item->thumbnail)
                        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                             style="background-image: url('{{ str_starts_with($item->thumbnail, '/storage/') ? $item->thumbnail : Storage::url($item->thumbnail) }}');">
                        </div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-green-600 via-emerald-600 to-teal-800"></div>
                    @endif

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-60"></div>

                    <!-- Content -->
                    <div class="relative h-full flex items-center justify-center">
                        <div class="text-center max-w-6xl mx-auto px-8">
                            <!-- Featured Badge -->
                            <div class="mb-8 flex justify-center">
                                <span class="bg-yellow-500 text-white text-lg font-semibold px-6 py-3 rounded-full shadow-lg">
                                    📢 PENGUMUMAN PENTING
                                </span>
                            </div>

                            <!-- Title -->
                            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-8 drop-shadow-lg line-clamp-3">
                                {{ $item->judul }}
                            </h1>

                            <!-- Content -->
                            <p class="text-lg md:text-2xl text-blue-100 mb-8 max-w-4xl mx-auto leading-relaxed line-clamp-4">
                                {{ Str::limit(strip_tags($item->konten), 300) }}
                            </p>

                            <!-- Date -->
                            <div class="flex items-center justify-center text-lg text-blue-200 mb-8">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($item->tanggal_publish)->format('d F Y') }}
                            </div>

                            <!-- CTA Button -->
                            <a href="{{ route('frontend.pengumuman') }}?highlight={{ $item->id }}"
                               class="inline-flex items-center px-8 py-4 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Navigation Dots -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3">
            <button @click="goToSlide(0)"
                    class="w-4 h-4 rounded-full transition-all duration-300"
                    :class="currentSlide === 0 ? 'bg-white' : 'bg-white bg-opacity-50'">
            </button>
            @if(isset($pengumumanFeatured) && $pengumumanFeatured->count() > 0)
                @foreach($pengumumanFeatured->take(5) as $index => $item)
                <button @click="goToSlide({{ $index + 1 }})"
                        class="w-4 h-4 rounded-full transition-all duration-300"
                        :class="currentSlide === {{ $index + 1 }} ? 'bg-white' : 'bg-white bg-opacity-50'">
                </button>
                @endforeach
            @endif
        </div>

        <!-- Navigation Arrows -->
        <button @click="previousSlide()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-full transition-all duration-300 shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="nextSlide()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-full transition-all duration-300 shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
</section>

<!-- Berita Section -->
@include('frontend.layouts.home.berita-section', ['berita' => $berita ?? collect()])

<!-- Pengumuman Section -->
@include('frontend.layouts.home.pengumuman-section', ['pengumuman' => $pengumuman ?? collect()])

<!-- Struktur Organisasi Section -->
@include('frontend.layouts.home.struktur-section', ['strukturOrganisasi' => $strukturOrganisasi ?? []])

<!-- Quick Links Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Akses Cepat</h2>
            <p class="text-lg text-gray-600">Navigasi mudah ke berbagai layanan dan informasi penting</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Quick Link 1 -->
            <a href="{{ route('profil.visi-misi') }}"
               class="group bg-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-300">Visi & Misi</h3>
                <p class="text-sm text-gray-600">Mengenal visi dan misi universitas</p>
            </a>

            <!-- Quick Link 2 -->
            <a href="{{ route('profil.struktur-organisasi') }}"
               class="group bg-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition-colors duration-300">Struktur Organisasi</h3>
                <p class="text-sm text-gray-600">Hierarki dan struktur universitas</p>
            </a>

            <!-- Quick Link 3 -->
            <a href="{{ route('frontend.berita') }}"
               class="group bg-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors duration-300">Berita Lengkap</h3>
                <p class="text-sm text-gray-600">Semua berita dan informasi terkini</p>
            </a>

            <!-- Quick Link 4 -->
            <a href="{{ route('frontend.pengumuman') }}"
               class="group bg-white rounded-xl p-6 shadow-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors duration-300">Pengumuman Lengkap</h3>
                <p class="text-sm text-gray-600">Semua pengumuman resmi universitas</p>
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-4 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Carousel specific styles */
    .carousel-slide {
        transition: transform 0.5s ease-in-out;
    }

    .carousel-dot {
        transition: all 0.3s ease;
    }

    .carousel-dot:hover {
        transform: scale(1.2);
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Custom animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Hover effects */
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Hero Carousel functionality
    function heroCarousel() {
        return {
            currentSlide: 0,
            totalSlides: {{ 1 + (isset($pengumumanFeatured) ? min($pengumumanFeatured->count(), 5) : 0) }}, // 1 konsisten + max 5 dinamis
            autoPlayInterval: null,

            init() {
                if (this.totalSlides > 1) {
                    this.startAutoPlay();
                }
            },

            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                this.resetAutoPlay();
            },

            previousSlide() {
                this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
                this.resetAutoPlay();
            },

            goToSlide(index) {
                this.currentSlide = index;
                this.resetAutoPlay();
            },

            startAutoPlay() {
                this.autoPlayInterval = setInterval(() => {
                    this.nextSlide();
                }, 6000); // Auto-rotate every 6 seconds
            },

            stopAutoPlay() {
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                    this.autoPlayInterval = null;
                }
            },

            resetAutoPlay() {
                this.stopAutoPlay();
                if (this.totalSlides > 1) {
                    this.startAutoPlay();
                }
            }
        }
    }

    // Smooth scroll for anchor links
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scrolling to all anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation classes when elements come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all sections
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            observer.observe(section);
        });
    });
</script>
@endpush


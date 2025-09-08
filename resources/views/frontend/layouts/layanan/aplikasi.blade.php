{{-- File: resources/views/frontend/layout/app.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-gray-50">

    @include('frontend.components.header')
    <div class="container mx-auto px-4 py-12">
        <div class="relative overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="relative px-6 py-16 sm:px-8 sm:py-20">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                        Daftar Aplikasi
                    </h1>
                    <p class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                        Universitas Mulawarman
                    </p>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="overflow-x-auto animate-fade-in-up delay-100">
                <table class="min-w-full bg-white border border-gray-300 shadow rounded-lg">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 border-b">Aplikasi</th>
                            <th class="py-3 px-4 border-b">Sumber</th>
                            <th class="py-3 px-4 border-b">Keterangan</th>
                            <th class="py-3 px-4 border-b">Link</th>
                        </tr>
                    </thead>
                    <tbody id="aplikasiTableBody" class="text-gray-700">
                        <!-- Loading state -->
                        <tr id="loadingRow">
                            <td colspan="4" class="py-8 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                    <span class="ml-2 text-gray-600">Memuat data...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @include('frontend.components.footer')

    <script>
        // Load aplikasi data from API
        async function loadAplikasiData() {
            try {
                const response = await fetch('/api/aplikasi-kepegawaian', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data) {
                        displayAplikasiData(result.data);
                    } else {
                        displayError('Tidak ada data aplikasi yang tersedia');
                    }
                } else {
                    displayError('Gagal memuat data aplikasi');
                }
            } catch (error) {
                console.error('Error loading aplikasi data:', error);
                displayError('Terjadi kesalahan saat memuat data');
            }
        }

        // Display aplikasi data in table
        function displayAplikasiData(data) {
            const tbody = document.getElementById('aplikasiTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="file-text" class="h-12 w-12 text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data</p>
                                <p class="text-sm">Data aplikasi kepegawaian akan ditampilkan di sini</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(item => `
                <tr class="hover:bg-gray-100 transition duration-200 ease-in-out">
                    <td class="py-2 px-4 border-b font-medium">${item.nama_aplikasi}</td>
                    <td class="py-2 px-4 border-b">${item.sumber}</td>
                    <td class="py-2 px-4 border-b">${item.keterangan}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="${item.link}" target="_blank"
                           class="inline-flex items-center bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-400 transition">
                            <i data-lucide="external-link" class="h-4 w-4 mr-1"></i>
                            Buka Aplikasi
                        </a>
                    </td>
                </tr>
            `).join('');

            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Display error message
        function displayError(message) {
            const tbody = document.getElementById('aplikasiTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-red-500">
                        <div class="flex flex-col items-center">
                            <i data-lucide="alert-circle" class="h-12 w-12 text-red-300 mb-4"></i>
                            <p class="text-lg font-medium">Gagal Memuat Data</p>
                            <p class="text-sm">${message}</p>
                        </div>
                    </td>
                </tr>
            `;

            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadAplikasiData();
        });
    </script>

</body>
</html>

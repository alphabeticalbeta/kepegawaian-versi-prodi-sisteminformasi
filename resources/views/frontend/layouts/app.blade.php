<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kepegawaian UNMUL')</title>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional Styles --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    @include('frontend.components.header')

    <main>
        @yield('content')
    </main>

    @include('frontend.components.footer')

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>

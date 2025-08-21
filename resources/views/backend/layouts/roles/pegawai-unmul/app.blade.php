@extends('backend.layouts.base', [
    'jsModule' => 'pegawai/index.js',
    'sidebarComponent' => 'backend.components.sidebar-pegawai-unmul',
    'role' => 'pegawai-unmul'
])

@section('title', 'Dashboard Pegawai')

@section('description', 'Dashboard untuk Pegawai - Sistem Kepegawaian UNMUL')

@push('styles')
<style>
    /* Pegawai specific styles */
    .pegawai-dashboard {
        /* Custom styles for pegawai dashboard */
    }

    .profile-card {
        transition: all 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .usulan-form {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
    @yield('dashboard-content')
@endsection

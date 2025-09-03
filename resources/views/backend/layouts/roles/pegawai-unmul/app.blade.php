@extends('backend.layouts.base', [
    'jsModule' => 'pegawai/index.js',
    'sidebarComponent' => 'backend.components.sidebar-pegawai-unmul',
    'role' => 'pegawai-unmul'
])

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // SweetAlert2 fallback and configuration for pegawai-unmul
    document.addEventListener('DOMContentLoaded', function() {
        // Check if SweetAlert2 is loaded
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 not loaded, using fallback alert');
            window.Swal = {
                fire: function(options) {
                    if (options.icon === 'success') {
                        alert('Success: ' + (options.text || options.title));
                    } else if (options.icon === 'error') {
                        alert('Error: ' + (options.text || options.title));
                    } else if (options.icon === 'warning') {
                        alert('Warning: ' + (options.text || options.title));
                    } else if (options.icon === 'info') {
                        alert('Info: ' + (options.text || options.title));
                    } else {
                        alert(options.title + ': ' + (options.text || ''));
                    }
                },
                showLoading: function() {
                    // Simple loading indicator
                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'simpleLoading';
                    loadingDiv.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 20px; border-radius: 8px;">Loading...</div></div>';
                    document.body.appendChild(loadingDiv);
                },
                close: function() {
                    const loadingDiv = document.getElementById('simpleLoading');
                    if (loadingDiv) {
                        loadingDiv.remove();
                    }
                }
            };
        }
        
        console.log('SweetAlert2 initialized for pegawai-unmul');
    });
</script>
@endpush

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

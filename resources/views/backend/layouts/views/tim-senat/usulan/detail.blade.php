@extends('backend.layouts.roles.tim-senat.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usul-jabatan.usulan-detail-jabatan', [
        'usulan' => $usulan,
        'role' => 'Tim Senat',
        'existingValidation' => $existingValidation,
        'penilais' => $penilais ?? collect()
    ])
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tim Senat specific functionality
    console.log('Tim Senat Detail Page Loaded');
    
    // Add specific styling for Tim Senat
    const statusBadge = document.querySelector('.status-badge');
    if (statusBadge) {
        statusBadge.classList.add('bg-emerald-100', 'text-emerald-800');
    }
    
    // Highlight Tim Senat specific elements
    const actionButtons = document.querySelectorAll('.action-button');
    actionButtons.forEach(button => {
        if (button.textContent.includes('Senat')) {
            button.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        }
    });
});
</script>
@endsection

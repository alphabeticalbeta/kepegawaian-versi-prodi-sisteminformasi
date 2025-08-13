{{-- resources/views/backend/layouts/shared/usulan-detail/usulan-detail.blade.php --}}
{{-- Shared template untuk detail usulan - Multi-role --}}

@if($currentRole === 'admin_fakultas')
    @include('backend.layouts.admin-fakultas.usulan-detail-wrapper', [
        'usulan' => $usulan,
        'formAction' => $formAction,
        'validationFields' => $validationFields ?? [],
        'existingValidation' => $existingValidation ?? []
    ])
@elseif($currentRole === 'penilai_universitas')  
    @include('backend.layouts.penilai-universitas.usulan-detail-wrapper', [
        'usulan' => $usulan,
        'formAction' => $formAction,
        'validationFields' => $validationFields ?? [],
        'existingValidation' => $existingValidation ?? []
    ])
@else
    {{-- Default: Reuse existing admin universitas template --}}
    @include('backend.admin-univ-usulan.pusat-usulan.usulan-detail', [
        'usulan' => $usulan
    ])
@endif
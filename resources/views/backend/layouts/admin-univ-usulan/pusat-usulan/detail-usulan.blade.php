@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Detail Usulan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- =========================
         FORM VALIDASI ADMIN UNIV
       ========================= --}}
    <form
        action="{{ route('backend.admin-univ-usulan.pusat-usulan.process', $usulan->id) }}"
        method="POST"
        id="validationForm"
        enctype="multipart/form-data"
        class="mt-8 space-y-8"
    >
        @csrf

        {{-- Header Info Card --}}
        @include('backend.components.usulan.detail._header', [
            'usulan' => $usulan
        ])

        {{-- Validation Sections (field dinamis per kategori) --}}
        @php
            // Ambil field validasi dinamis
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
        @endphp

        @if(isset($validationFields) && count($validationFields) > 0)
            @foreach($validationFields as $category => $fields)
                @include('backend.components.usulan.detail._validation-section', [
                    'category' => $category,
                    'fields'   => $fields,
                    'usulan'   => $usulan,
                    'canEdit'  => $canEdit ?? false, 
                ])
            @endforeach
        @endif

        {{-- Tombol aksi (gunakan action_type) --}}
        @include('backend.components.usulan.detail._action-buttons', [
            'usulan' => $usulan,
            'canEdit' => $canEdit ?? false
        ])

        {{-- Shared: Riwayat Perubahan --}}
        @include('backend.components.usulan.detail._riwayat_log', ['usulan' => $usulan])
    </form>

</div>
@endsection

{{-- Script validasi/submit --}}
@push('scripts')
    @include('Backend.components.usulan.detail._validation-scripts', [
        'usulan' => $usulan
    ])
@endpush

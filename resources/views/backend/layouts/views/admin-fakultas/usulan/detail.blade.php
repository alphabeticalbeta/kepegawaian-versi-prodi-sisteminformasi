@extends('backend.layouts.roles.admin-fakultas.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
{{-- Use the shared usulan detail component --}}
@include('backend.layouts.views.shared.usulan-detail', [
    'usulan' => $usulan,
    'role' => 'Admin Fakultas',
    'existingValidation' => $existingValidation ?? null
])
@endsection

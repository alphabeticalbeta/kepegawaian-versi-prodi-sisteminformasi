@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usulan-detail', [
        'usulan' => $usulan,
        'role' => 'Admin Universitas Usulan',
        'existingValidation' => $existingValidation,
        'canEdit' => $canEdit,
        'penilaiProgressData' => $penilaiProgressData ?? null,
        'consistencyCheck' => $consistencyCheck ?? null
    ])
@endsection

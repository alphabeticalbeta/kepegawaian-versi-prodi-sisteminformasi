@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usul-jabatan.usulan-detail-jabatan', [
        'usulan' => $usulan,
        'role' => 'Kepegawaian Universitas',
        'existingValidation' => $existingValidation,
        'canEdit' => $canEdit,
        'penilaiProgressData' => $penilaiProgressData ?? null,
        'consistencyCheck' => $consistencyCheck ?? null
    ])
@endsection

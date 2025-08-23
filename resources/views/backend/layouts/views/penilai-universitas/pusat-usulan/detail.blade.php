@extends('backend.layouts.roles.penilai-universitas.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usulan-detail', [
        'usulan' => $usulan,
        'role' => 'Penilai Universitas',
        'existingValidation' => $existingValidation ?? null,
        'validationFields' => $validationFields ?? [],
        'bkdLabels' => $bkdLabels ?? [],
        'canEdit' => $canEdit ?? false,
        'consistencyCheck' => $consistencyCheck ?? null,
        'validationSummary' => $validationSummary ?? null,
        'penilaiIndividualStatus' => $penilaiIndividualStatus ?? null
    ])
@endsection

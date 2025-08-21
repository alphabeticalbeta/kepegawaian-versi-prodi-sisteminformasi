@extends('backend.layouts.roles.tim-senat.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usulan-detail', [
        'usulan' => $usulan,
        'role' => 'Tim Senat',
        'existingValidation' => $existingValidation
    ])
@endsection

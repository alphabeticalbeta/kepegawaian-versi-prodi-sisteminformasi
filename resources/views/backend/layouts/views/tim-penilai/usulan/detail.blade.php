@extends('backend.layouts.roles.tim-penilai.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@section('content')
    @include('backend.layouts.views.shared.usulan-detail', [
        'usulan' => $usulan,
        'role' => 'Tim Penilai',
        'existingValidation' => $existingValidation
    ])
@endsection

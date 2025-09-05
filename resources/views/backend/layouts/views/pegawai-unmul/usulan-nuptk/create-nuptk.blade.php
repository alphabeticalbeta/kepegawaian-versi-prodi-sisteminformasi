@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan NUPTK')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        
        @include('backend.layouts.views.pegawai-unmul.usulan-nuptk.components.pegawai-action-buttons')
    </div>
</div>
@endsection
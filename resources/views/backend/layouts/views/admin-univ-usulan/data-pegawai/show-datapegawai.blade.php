@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Detail Data Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h2 class="text-2xl font-semibold leading-tight">
                Detail Data Pegawai
            </h2>
            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">
                &larr; Kembali
            </a>
        </div>

        {{-- ... (Bagian Informasi Dasar tidak berubah) ... --}}
        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Dasar</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            {{-- Kolom 1 --}}
            <div class="space-y-4">
                <div>
                    <p class="font-semibold text-gray-500">Nama Lengkap</p>
                    <p class="text-gray-800 text-base font-medium">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-500">NIP</p>
                    <p class="text-gray-800">{{ $pegawai->nip }}</p>
                </div>
                @if($pegawai->nuptk)
                <div>
                    <p class="font-semibold text-gray-500">NUPTK</p>
                    <p class="text-gray-800">{{ $pegawai->nuptk }}</p>
                </div>
                @endif
                <div>
                    <p class="font-semibold text-gray-500">Jenis Pegawai</p>
                    <p class="text-gray-800">{{ $pegawai->jenis_pegawai }}</p>
                </div>
            </div>

            {{-- Kolom 2 --}}
            <div class="space-y-4">
                <div>
                    <p class="font-semibold text-gray-500">Pangkat Terakhir</p>
                    <p class="text-gray-800">{{ $pegawai->pangkat->pangkat ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-500">Jabatan Terakhir</p>
                    <p class="text-gray-800">{{ $pegawai->jabatan->jabatan ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-500">Unit Kerja</p>
                    <p class="text-gray-800">{{ $pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? '' }} &gt; {{ $pegawai->unitKerja->subUnitKerja->nama ?? '' }} &gt; {{ $pegawai->unitKerja->nama ?? 'N/A' }}</p>
                </div>
                 <div>
                    <p class="font-semibold text-gray-500">Pendidikan Terakhir</p>
                    <p class="text-gray-800">{{ $pegawai->pendidikan_terakhir }}</p>
                </div>
            </div>

            {{-- Kolom 3 --}}
            <div class="space-y-4">
                <div>
                    <p class="font-semibold text-gray-500">Tempat, Tanggal Lahir</p>
                    <p class="text-gray-800">{{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir->format('d F Y') }}</p>
                </div>
                 <div>
                    <p class="font-semibold text-gray-500">Jenis Kelamin</p>
                    <p class="text-gray-800">{{ $pegawai->jenis_kelamin }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-500">Nomor Handphone</p>
                    <p class="text-gray-800">{{ $pegawai->nomor_handphone }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-5 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Berkas Dokumen Terlampir</h3>
            @php
                $dokumenList = [
                    'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                    'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                    'ijazah_terakhir' => 'Ijazah Terakhir',
                    'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                    'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah (Luar Negeri)',
                    'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                    'pak_konversi' => 'PAK Konversi',
                    'skp_tahun_pertama' => 'SKP Tahun Pertama',
                    'skp_tahun_kedua' => 'SKP Tahun Kedua',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                @foreach ($dokumenList as $field => $label)
                    @if($pegawai->$field)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            {{-- UBAH TAUTAN DI SINI --}}
                            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => $field]) }}" target="_blank"
                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md text-sm transition">
                                <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>
                                Lihat
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

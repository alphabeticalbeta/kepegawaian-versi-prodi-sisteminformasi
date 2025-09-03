@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Periode Usulan')

@section('content')
@php
    $jenisUsulan = $jenisUsulan ?? null;
@endphp

<!-- FontAwesome CDN untuk icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- CSS Fallback untuk icon -->
<style>
    .fas {
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 14px !important;
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
        text-align: center !important;
        line-height: 16px !important;
    }

    /* Fallback untuk icon yang tidak terlihat */
    .fa-toggle-on::before { content: "🔓"; }
    .fa-toggle-off::before { content: "🔒"; }
    .fa-edit::before { content: "✏️"; }
    .fa-trash::before { content: "🗑️"; }
    .fa-plus::before { content: "➕"; }
    .fa-filter::before { content: "🔍"; }
    .fa-times::before { content: "❌"; }
    .fa-list::before { content: "📋"; }
    .fa-calendar-times::before { content: "📅"; }
    .fa-spinner::before { content: "⏳"; }

    /* Padding hanya untuk main content (card), bukan header */
    .bg-white.rounded-lg.shadow-sm.border.border-gray-200 {
        margin-left: 1.5rem !important;
        margin-right: 1.5rem !important;
        margin-top: 0 !important;
        margin-bottom: 1.5rem !important;
    }

    /* Header tidak perlu margin/padding dari sisi */
    .bg-white.border-b.border-gray-200 {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 1.5rem !important;
    }

    /* Memastikan icon terlihat dengan baik */
    .action-button i {
        font-size: 14px !important;
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
        text-align: center !important;
        line-height: 16px !important;
    }

    /* Memastikan button aktif dan terlihat */
    .action-button {
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        border: none !important;
        outline: none !important;
    }

    .action-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .action-button:disabled {
        cursor: not-allowed !important;
        opacity: 0.5 !important;
    }

    /* Memastikan semua button terlihat dan berfungsi */
    button, a {
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    button:hover, a:hover {
        transform: translateY(-1px) !important;
    }

    button:disabled {
        cursor: not-allowed !important;
        opacity: 0.5 !important;
    }

    /* Debug styling untuk memastikan button terlihat */
    #tambahPeriodeBtn, #tambahPeriodeEmptyBtn {
        position: relative !important;
        z-index: 10 !important;
        pointer-events: auto !important;
    }

    /* Memastikan form delete sejajar dengan button lainnya */
    .action-buttons form {
        display: inline-block !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .action-buttons form button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 0;
        max-width: 400px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.7);
        transition: all 0.3s ease;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-overlay.show .modal-content {
        transform: scale(1);
    }

    .modal-header {
        padding: 1.5rem 1.5rem 0.5rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-body {
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        padding: 0.5rem 1.5rem 1.5rem 1.5rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .modal-message {
        color: #6b7280;
        line-height: 1.5;
        margin: 0;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        outline: none;
    }

    .btn-secondary {
        background-color: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
    }

    .btn-danger {
        background-color: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
    }

    .btn-success {
        background-color: #059669;
        color: white;
    }

    .btn-success:hover {
        background-color: #047857;
    }

    /* Notification Styles */
    .notification {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 10000;
        max-width: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-left: 4px solid;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        border-left-color: #059669;
    }

    .notification.error {
        border-left-color: #dc2626;
    }

    .notification.info {
        border-left-color: #3b82f6;
    }

    .notification-content {
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-message {
        flex: 1;
        color: #374151;
        font-size: 0.875rem;
    }

    .notification-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: color 0.2s ease;
    }

    .notification-close:hover {
        color: #6b7280;
    }
</style>

<!-- Page Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($jenisUsulan && $jenisUsulan !== 'all')
                    Histori Periode {{ ucwords(str_replace('-', ' ', $jenisUsulan)) }}
                @else
                    Data Periode Usulan
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($jenisUsulan && $jenisUsulan !== 'all')
                    Menampilkan semua periode untuk jenis usulan ini
                @else
                    Kelola semua periode usulan kepegawaian
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus" style="font-size: 14px;"></i>
                Tambah Periode
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <label for="filterJenis" class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    <i class="fas fa-filter" style="font-size: 14px; color: #6b7280;"></i>
                    Filter Jenis Usulan:
                </label>
                <select id="filterJenis" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Semua Jenis Usulan</option>
                    <option value="all" {{ $jenisUsulan == 'all' ? 'selected' : '' }}>Semua Usulan Aktif</option>
                    <optgroup label="Usulan Jabatan">
                        <option value="jabatan-dosen-regular" {{ $jenisUsulan == 'jabatan-dosen-regular' ? 'selected' : '' }}>Usulan Jabatan Dosen Reguler</option>
                        <option value="jabatan-dosen-pengangkatan" {{ $jenisUsulan == 'jabatan-dosen-pengangkatan' ? 'selected' : '' }}>Usulan Jabatan Dosen Pengangkatan Pertama</option>
                    </optgroup>
                    <option value="nuptk" {{ $jenisUsulan == 'nuptk' ? 'selected' : '' }}>Usulan NUPTK</option>
                    <option value="laporan-lkd" {{ $jenisUsulan == 'laporan-lkd' ? 'selected' : '' }}>Usulan Laporan LKD</option>
                    <option value="presensi" {{ $jenisUsulan == 'presensi' ? 'selected' : '' }}>Usulan Presensi</option>
                    <option value="id-sinta-sister" {{ $jenisUsulan == 'id-sinta-sister' ? 'selected' : '' }}>Usulan ID SINTA ke SISTER</option>
                    <option value="satyalancana" {{ $jenisUsulan == 'satyalancana' ? 'selected' : '' }}>Usulan Satyalancana</option>
                    <option value="tugas-belajar" {{ $jenisUsulan == 'tugas-belajar' ? 'selected' : '' }}>Usulan Tugas Belajar</option>
                    <option value="pengaktifan-kembali" {{ $jenisUsulan == 'pengaktifan-kembali' ? 'selected' : '' }}>Usulan Pengaktifan Kembali</option>
                    <option value="penyesuaian-masa-kerja" {{ $jenisUsulan == 'penyesuaian-masa-kerja' ? 'selected' : '' }}>Usulan Penyesuaian Masa Kerja</option>
                    <option value="ujian-dinas-ijazah" {{ $jenisUsulan == 'ujian-dinas-ijazah' ? 'selected' : '' }}>Usulan Ujian Dinas Ijazah</option>
                    <option value="laporan-serdos" {{ $jenisUsulan == 'laporan-serdos' ? 'selected' : '' }}>Usulan Laporan SERDOS</option>
                    <option value="pensiun" {{ $jenisUsulan == 'pensiun' ? 'selected' : '' }}>Usulan Pensiun</option>
                    <option value="kepangkatan" {{ $jenisUsulan == 'kepangkatan' ? 'selected' : '' }}>Usulan Kepangkatan</option>
                    <option value="pencantuman-gelar" {{ $jenisUsulan == 'pencantuman-gelar' ? 'selected' : '' }}>Usulan Pencantuman Gelar</option>
                </select>
            </div>

            @if($jenisUsulan && $jenisUsulan !== 'all')
                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <i class="fas fa-times" style="font-size: 12px;"></i>
                    Tampilkan Semua
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Nama Periode (Tahun)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Jenis Usulan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Periode Usulan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Pendaftar
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Lihat Pengusul
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($periodeUsulans as $periode)
                    <tr class="hover:bg-gray-50 transition-colors duration-200" data-jenis="{{ $periode->jenis_usulan }}" data-periode-id="{{ $periode->id }}">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $periode->nama_periode }} ({{ $periode->tahun_periode }})
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($periode->jenis_usulan == 'Semua Usulan Aktif') bg-yellow-100 text-yellow-800
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-regular') bg-indigo-100 text-indigo-800
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-pengangkatan') bg-cyan-100 text-cyan-800
                                @elseif($periode->jenis_usulan == 'usulan-nuptk') bg-green-100 text-green-800
                                @elseif($periode->jenis_usulan == 'usulan-laporan-lkd') bg-blue-100 text-blue-800
                                @elseif($periode->jenis_usulan == 'usulan-presensi') bg-pink-100 text-pink-800
                                @elseif($periode->jenis_usulan == 'usulan-id-sinta-sister') bg-teal-100 text-teal-800
                                @elseif($periode->jenis_usulan == 'usulan-satyalancana') bg-orange-100 text-orange-800
                                @elseif($periode->jenis_usulan == 'usulan-tugas-belajar') bg-cyan-100 text-cyan-800
                                @elseif($periode->jenis_usulan == 'usulan-pengaktifan-kembali') bg-emerald-100 text-emerald-800
                                @elseif($periode->jenis_usulan == 'usulan-penyesuaian-masa-kerja') bg-amber-100 text-amber-800
                                @elseif($periode->jenis_usulan == 'usulan-ujian-dinas-ijazah') bg-lime-100 text-lime-800
                                @elseif($periode->jenis_usulan == 'usulan-laporan-serdos') bg-rose-100 text-rose-800
                                @elseif($periode->jenis_usulan == 'usulan-pensiun') bg-slate-100 text-slate-800
                                @elseif($periode->jenis_usulan == 'usulan-kepangkatan') bg-violet-100 text-violet-800
                                @elseif($periode->jenis_usulan == 'usulan-pencantuman-gelar') bg-fuchsia-100 text-fuchsia-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($periode->jenis_usulan == 'Semua Usulan Aktif')
                                    Semua Usulan Aktif
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-regular')
                                    Usulan Jabatan Dosen Reguler
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-pengangkatan')
                                    Usulan Jabatan Dosen Pengangkatan Pertama
                                @elseif($periode->jenis_usulan == 'usulan-nuptk')
                                    Usulan NUPTK
                                @elseif($periode->jenis_usulan == 'usulan-laporan-lkd')
                                    Usulan Laporan LKD
                                @elseif($periode->jenis_usulan == 'usulan-presensi')
                                    Usulan Presensi
                                @elseif($periode->jenis_usulan == 'usulan-id-sinta-sister')
                                    Usulan ID SINTA ke SISTER
                                @elseif($periode->jenis_usulan == 'usulan-satyalancana')
                                    Usulan Satyalancana
                                @elseif($periode->jenis_usulan == 'usulan-tugas-belajar')
                                    Usulan Tugas Belajar
                                @elseif($periode->jenis_usulan == 'usulan-pengaktifan-kembali')
                                    Usulan Pengaktifan Kembali
                                @elseif($periode->jenis_usulan == 'usulan-penyesuaian-masa-kerja')
                                    Usulan Penyesuaian Masa Kerja
                                @elseif($periode->jenis_usulan == 'usulan-ujian-dinas-ijazah')
                                    Usulan Ujian Dinas Ijazah
                                @elseif($periode->jenis_usulan == 'usulan-laporan-serdos')
                                    Usulan Laporan SERDOS
                                @elseif($periode->jenis_usulan == 'usulan-pensiun')
                                    Usulan Pensiun
                                @elseif($periode->jenis_usulan == 'usulan-kepangkatan')
                                    Usulan Kepangkatan
                                @elseif($periode->jenis_usulan == 'usulan-pencantuman-gelar')
                                    Usulan Pencantuman Gelar
                                @else
                                    {{ ucwords(str_replace('-', ' ', $periode->jenis_usulan)) }}
                                @endif
                            </span>
                        </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="font-medium text-slate-800">
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM YYYY') }}
                                    </div>
                                    <div class="font-medium text-slate-800">
                                        s/d {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($periode->status == 'Buka')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tutup
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $periode->usulans_submitted_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4">
                                @if(($periode->usulans_submitted_count ?? 0) > 0)
                                    @if($jenisUsulan === 'kepangkatan')
                                        <button onclick="openModalLihatPengusulKepangkatan({{ $periode->id }})"
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-full hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                            <i class="fas fa-users mr-1"></i>
                                            Lihat {{ $periode->usulans_submitted_count }} Pengusul
                                        </button>
                                    @else
                                        <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.show', $periode) }}"
                                           class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-full hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                            <i class="fas fa-users mr-1"></i>
                                            Lihat {{ $periode->usulans_submitted_count }} Pengusul
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">Belum ada pengusul yang dikirim</span>
                                @endif
                            </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 action-buttons">
                                <!-- Toggle Status Button -->
                                <button onclick="toggleStatus({{ $periode->id }}, '{{ $periode->status }}', event)"
                                        class="action-button p-2 rounded-lg transition-colors duration-200 {{ $periode->status == 'Buka' ? 'text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100' : 'text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100' }}"
                                        title="{{ $periode->status == 'Buka' ? 'Tutup Periode' : 'Buka Periode' }}">
                                    <i class="fas {{ $periode->status == 'Buka' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>

                                <!-- Edit Button -->
                                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.edit', $periode->id) }}"
                                   class="action-button p-2 rounded-lg text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 transition-colors duration-200"
                                   title="Edit Periode">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete Button - Disabled if has usulans -->
                                @if(($periode->usulans_count ?? 0) > 0)
                                    <button disabled
                                            class="action-button p-2 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed"
                                            title="Tidak dapat dihapus karena ada {{ $periode->usulans_count }} usulan yang masuk">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="confirmDelete({{ $periode->id }}, '{{ $periode->nama_periode }}', event)"
                                            class="action-button p-2 rounded-lg text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 transition-colors duration-200"
                                            title="Hapus Periode">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12">
                            <div class="text-center">
                                <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-times text-gray-400" style="font-size: 2rem;"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    @if($jenisUsulan && $jenisUsulan !== 'all')
                                        Belum ada periode untuk jenis usulan ini
                                    @else
                                        Belum ada data periode usulan
                                    @endif
                                </h3>
                                <p class="text-gray-500 mb-6">
                                    @if($jenisUsulan && $jenisUsulan !== 'all')
                                        Jenis usulan "{{ ucwords(str_replace('-', ' ', $jenisUsulan)) }}" belum memiliki periode yang dibuat.
                                    @else
                                        Mulai dengan membuat periode usulan pertama untuk mengelola usulan kepegawaian.
                                    @endif
                                </p>
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.create') }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                        <i class="fas fa-plus" style="font-size: 14px; margin-right: 0.5rem;"></i>
                                        Tambah Periode Pertama
                                    </a>
                                    @if($jenisUsulan && $jenisUsulan !== 'all')
                                        <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index') }}"
                                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                            <i class="fas fa-list" style="font-size: 14px; margin-right: 0.5rem;"></i>
                                            Lihat Semua Periode
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    @if($periodeUsulans->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $periodeUsulans->firstItem() ?? 0 }} sampai {{ $periodeUsulans->lastItem() ?? 0 }} dari {{ $periodeUsulans->total() }} data
                </div>
                <div class="flex items-center space-x-2">
                    {{ $periodeUsulans->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal Container -->
<div id="confirmModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Konfirmasi</h3>
        </div>
        <div class="modal-body">
            <p class="modal-message" id="modalMessage">Apakah Anda yakin ingin melanjutkan?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="modalCancel">Batal</button>
            <button type="button" class="btn btn-danger" id="modalConfirm">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer"></div>

@endsection

<!-- JavaScript untuk Toggle Status Periode -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle filter berdasarkan jenis usulan
    const filterJenis = document.getElementById('filterJenis');

    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            const selectedValue = this.value;
            const currentUrl = new URL(window.location);

            if (selectedValue && selectedValue !== '') {
                currentUrl.searchParams.set('jenis', selectedValue);
            } else {
                currentUrl.searchParams.delete('jenis');
            }

            window.location.href = currentUrl.toString();
        });
    }




});

// Utility functions untuk modal dan notification
function showModal(title, message, confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', type = 'danger') {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalConfirm = document.getElementById('modalConfirm');
        const modalCancel = document.getElementById('modalCancel');

        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalConfirm.textContent = confirmText;
        modalCancel.textContent = cancelText;

        // Set button type
        modalConfirm.className = `btn btn-${type}`;

        // Show modal
        modal.classList.add('show');

        // Handle confirm
        const handleConfirm = () => {
            modal.classList.remove('show');
            cleanup();
            resolve(true);
        };

        // Handle cancel
        const handleCancel = () => {
            modal.classList.remove('show');
            cleanup();
            resolve(false);
        };

        // Handle escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                handleCancel();
            }
        };

        // Handle click outside
        const handleOutsideClick = (e) => {
            if (e.target === modal) {
                handleCancel();
            }
        };

        const cleanup = () => {
            modalConfirm.removeEventListener('click', handleConfirm);
            modalCancel.removeEventListener('click', handleCancel);
            document.removeEventListener('keydown', handleEscape);
            modal.removeEventListener('click', handleOutsideClick);
        };

        modalConfirm.addEventListener('click', handleConfirm);
        modalCancel.addEventListener('click', handleCancel);
        document.addEventListener('keydown', handleEscape);
        modal.addEventListener('click', handleOutsideClick);
    });
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const icon = type === 'success' ? 'fa-check-circle' :
                 type === 'error' ? 'fa-exclamation-circle' :
                 'fa-info-circle';

    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas ${icon}"></i>
            </div>
            <div class="notification-message">${message}</div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto remove
    if (duration > 0) {
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, duration);
    }
}

// Fungsi untuk toggle status periode - dipindah ke luar agar bisa diakses global
window.toggleStatus = function(periodeId, currentStatus, event) {
    const newStatus = currentStatus === 'Buka' ? 'Tutup' : 'Buka';
    const actionText = currentStatus === 'Buka' ? 'menutup' : 'membuka';

    showModal(
        'Konfirmasi Kirim Usulan',
        `Anda yakin ingin ${actionText} periode ini?`,
        'Iya',
        'Batal',
        'success'
    ).then((confirmed) => {
        if (confirmed) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('periode_id', periodeId);

            // Send request
            fetch('{{ route('backend.kepegawaian-universitas.usulan.toggle-periode') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button appearance
                    if (newStatus === 'Buka') {
                        button.className = 'action-button p-2 rounded-lg transition-colors duration-200 text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100';
                        button.innerHTML = '<i class="fas fa-toggle-on"></i>';
                        button.title = 'Tutup Periode';
                    } else {
                        button.className = 'action-button p-2 rounded-lg transition-colors duration-200 text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100';
                        button.innerHTML = '<i class="fas fa-toggle-off"></i>';
                        button.title = 'Buka Periode';
                    }

                    // Update status cell
                    const statusCell = button.closest('tr').querySelector('td:nth-child(6)'); // Status column
                    if (statusCell) {
                        if (newStatus === 'Buka') {
                            statusCell.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Buka</span>';
                        } else {
                            statusCell.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Tutup</span>';
                        }
                    }

                    // Show success notification
                    showNotification(`Status periode berhasil diubah menjadi ${newStatus}`, 'success');
                } else {
                    showNotification(data.message || 'Gagal mengubah status periode', 'error');
                    button.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan jaringan', 'error');
                button.innerHTML = originalHTML;
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    });
}

// Fungsi untuk konfirmasi delete dengan modal
window.confirmDelete = function(periodeId, periodeName, event) {
    showModal(
        'Konfirmasi Hapus Periode',
        `Anda yakin ingin menghapus periode "${periodeName}"? Tindakan ini tidak dapat dibatalkan.`,
        'Ya, Hapus Periode',
        'Batal',
        'danger'
    ).then((confirmed) => {
        if (confirmed) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            // Send request
            fetch(`{{ route('backend.kepegawaian-universitas.periode-usulan.destroy', ':id') }}`.replace(':id', periodeId), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from table with animation
                    const row = button.closest('tr');
                    row.style.backgroundColor = '#fef2f2';
                    row.style.transition = 'background-color 0.5s ease';

                    setTimeout(() => {
                        row.remove();
                        showNotification('Periode berhasil dihapus', 'success');

                        // Check if table is empty
                        const tbody = document.querySelector('tbody');
                        if (tbody.children.length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    }, 500);
                } else {
                    showNotification(data.message || 'Gagal menghapus periode', 'error');
                    button.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan jaringan', 'error');
                button.innerHTML = originalHTML;
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    });
}

</script>

{{-- Include Modal Kepangkatan --}}
@include('backend.layouts.views.periode-usulan.modal-kepangkatan.modal-kepangkatan')

{{-- Include JavaScript Modal Kepangkatan --}}
<script src="{{ asset('js/modal-kepangkatan.js') }}"></script>



@extends('backend.layouts.roles.tim-senat.app')

@section('title', 'Dashboard - Tim Senat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-orange-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Dashboard Tim Senat
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Selamat datang di panel Tim Senat
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                        Export Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            // Fallback untuk variabel $usulans jika tidak ada
            $usulans = $usulans ?? collect();
        @endphp
        
        {{-- Statistics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Keputusan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Usulan Direkomendasikan oleh Tim Senat')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Usulan yang perlu diputuskan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Disetujui')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Keputusan disetujui</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Ditolak')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Keputusan ditolak</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Diproses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Semua usulan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                <i data-lucide="zap" class="w-5 h-5 text-orange-600"></i>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('tim-senat.usulan.index') }}" 
                   class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                    <div class="bg-orange-100 p-2 rounded-lg mr-3">
                        <i data-lucide="list" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-orange-900">Lihat Semua Usulan</p>
                        <p class="text-sm text-orange-700">Kelola semua usulan yang perlu diputuskan</p>
                    </div>
                </a>
                
                <a href="{{ route('tim-senat.rapat-senat.index') }}" 
                   class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                        <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-purple-900">Rapat Senat</p>
                        <p class="text-sm text-purple-700">Jadwal dan agenda rapat senat</p>
                    </div>
                </a>
                
                <a href="{{ route('tim-senat.keputusan-senat.index') }}" 
                   class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i data-lucide="gavel" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-blue-900">Keputusan Senat</p>
                        <p class="text-sm text-blue-700">Riwayat keputusan senat</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Periode Usulan Section --}}
        @php
            // Filter usulan yang dapat diakses oleh Tim Senat
            $accessibleUsulans = $usulans->filter(function($usulan) {
                return $usulan->canAccessPeriode('Tim Senat');
            });
            
            // Group by periode
            $periodeGroups = $accessibleUsulans->groupBy('periode_usulan_id');
        @endphp

        @if($periodeGroups->count() > 0)
            @foreach($periodeGroups as $periodeId => $periodeUsulans)
                @php
                    $firstUsulan = $periodeUsulans->first();
                    $periode = $firstUsulan->periodeUsulan;
                    $periodeInfo = $firstUsulan->getPeriodeInfo('Tim Senat');
                @endphp
                
                @if($periodeInfo['status'] === 'accessible')
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
                        {{-- Periode Header --}}
                        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-white flex items-center">
                                        <i data-lucide="calendar" class="w-6 h-6 mr-3"></i>
                                        {{ $periode->nama_periode ?? 'Periode Usulan' }}
                                    </h2>
                                    <p class="mt-1 text-orange-100">
                                        Periode: {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM YYYY') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-orange-100 text-sm">Total Usulan</p>
                                    <p class="text-white text-2xl font-bold">{{ $periodeUsulans->count() }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Usulan Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIP
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sub-sub Unit Kerja
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Kerja
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tujuan Jabatan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Hasil Rekomendasi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Penilai
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($periodeUsulans as $index => $usulan)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $usulan->pegawai->nip ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $usulan->pegawai->subSubUnitKerja->nama ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $usulan->pegawai->unitKerja->nama ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($usulan->status_usulan === 'Direkomendasikan')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                        Direkomendasikan
                                                    </span>
                                                @elseif($usulan->status_usulan === 'Perbaikan Usulan')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                                        Perbaikan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $usulan->status_usulan }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $penilais = $usulan->penilais ?? collect();
                                                @endphp
                                                @if($penilais->count() > 0)
                                                    @foreach($penilais->take(3) as $index => $penilai)
                                                        <div class="text-xs text-gray-600">
                                                            Penilai {{ $index + 1 }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-400">Belum ada penilai</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-2">
                                                    @if($usulan->status_usulan === 'Direkomendasikan')
                                                        <button onclick="showRecommendationModal({{ $usulan->id }}, 'setujui')" 
                                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors">
                                                            <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                                                            Rekomendasi
                                                        </button>
                                                        <button onclick="showRecommendationModal({{ $usulan->id }}, 'tolak')" 
                                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                                            <i data-lucide="x" class="w-4 h-4 inline mr-1"></i>
                                                            Tidak Rekomendasi
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('tim-senat.usulan.show', $usulan->id) }}"
                                                       class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                                        Detail
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            {{-- No Accessible Periods --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i data-lucide="calendar-x" class="w-16 h-16 mx-auto"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Periode yang Dapat Diakses</h3>
                <p class="text-gray-500">
                    Periode hanya dapat diakses setelah Admin Univ Usulan mengirimkan usulan ke Tim Senat.
                </p>
            </div>
        @endif

        {{-- Recent Decisions --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="gavel" class="w-6 h-6 mr-3"></i>
                    Keputusan Terbaru
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pegawai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jabatan Tujuan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keputusan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->take(5) as $usulan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <i data-lucide="user" class="w-5 h-5 text-orange-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $usulan->pegawai->nip ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($usulan->status_usulan === 'Disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Disetujui
                                        </span>
                                    @elseif($usulan->status_usulan === 'Ditolak')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usulan->updated_at ? $usulan->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('tim-senat.usulan.show', $usulan->id) }}"
                                       class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 px-3 py-1 rounded-lg transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i data-lucide="gavel" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Belum ada keputusan</p>
                                        <p class="text-sm">Tim Senat belum memberikan keputusan apapun.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Recommendation Modal --}}
<div id="recommendationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-orange-100 rounded-full">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600"></i>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Konfirmasi Keputusan</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">
                        Apakah Anda yakin ingin memberikan keputusan ini?
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmButton" class="px-4 py-2 bg-orange-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Konfirmasi
                    </button>
                    <button onclick="hideRecommendationModal()" class="mt-2 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showRecommendationModal(usulanId, action) {
    const modal = document.getElementById('recommendationModal');
    const title = document.getElementById('modalTitle');
    const message = document.getElementById('modalMessage');
    const confirmButton = document.getElementById('confirmButton');
    
    if (action === 'setujui') {
        title.textContent = 'Konfirmasi Rekomendasi';
        message.textContent = 'Apakah Anda yakin ingin memberikan rekomendasi untuk usulan ini?';
        confirmButton.textContent = 'Rekomendasi';
        confirmButton.className = 'px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300';
    } else {
        title.textContent = 'Konfirmasi Tidak Rekomendasi';
        message.textContent = 'Apakah Anda yakin ingin tidak merekomendasikan usulan ini?';
        confirmButton.textContent = 'Tidak Rekomendasi';
        confirmButton.className = 'px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300';
    }
    
    confirmButton.onclick = function() {
        // Redirect to usulan detail page with action parameter
        window.location.href = `/tim-senat/usulan/${usulanId}?action=${action}`;
    };
    
    modal.classList.remove('hidden');
}

function hideRecommendationModal() {
    const modal = document.getElementById('recommendationModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('recommendationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRecommendationModal();
    }
});
</script>
@endsection

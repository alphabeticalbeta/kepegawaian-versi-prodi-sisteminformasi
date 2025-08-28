{{-- USULAN DETAIL HEADER PARTIAL --}}
{{-- Usage: @include('backend.layouts.views.shared.partials._usulan-detail-header', ['usulan' => $usulan, 'config' => $config]) --}}

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 border-b shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        {{ $config['title'] }}
                    </h1>
                    <p class="mt-1 text-blue-100">
                        {{ $config['description'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2 text-blue-700 bg-white border border-white rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notification area moved to top --}}
        <div id="action-feedback" class="mb-4 text-sm hidden"></div>

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Menunggu Hasil Penilaian Tim Penilai' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Perbaikan Dari Tim Penilai' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Usulan Direkomendasi Tim Penilai' => 'bg-green-100 text-green-800 border-green-300',
                    'Tidak Direkomendasikan' => 'bg-red-100 text-red-800 border-red-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>
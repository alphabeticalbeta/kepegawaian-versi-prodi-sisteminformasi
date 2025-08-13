{{-- resources/views/backend/layouts/admin-fakultas/partials/_status-indicators.blade.php --}}
{{-- Status indicators untuk usulan yang tidak bisa diedit --}}

@if($usulan->status_usulan === 'Perlu Perbaikan')
    <div class="flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <span class="font-medium">Usulan sudah dikembalikan ke pegawai untuk perbaikan</span>
    </div>

@elseif($usulan->status_usulan === 'Belum Direkomendasikan')
    <div class="flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">Usulan belum direkomendasikan - dikembalikan ke pegawai</span>
    </div>

@elseif($usulan->status_usulan === 'Diusulkan ke Universitas')
    <div class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
        </svg>
        <span class="font-medium">Usulan sudah diteruskan ke Admin Universitas</span>
    </div>

@elseif($usulan->status_usulan === 'Direkomendasikan')
    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">Usulan telah selesai diproses - Direkomendasikan</span>
    </div>

@elseif($usulan->status_usulan === 'Ditolak Universitas')
    <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">Usulan telah ditolak oleh Universitas</span>
    </div>

@elseif($usulan->status_usulan === 'Dikembalikan dari Universitas')
    <div class="flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
        </svg>
        <span class="font-medium">Usulan dikembalikan dari Universitas untuk perbaikan</span>
    </div>

@else
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">Status: {{ $usulan->status_usulan }}</span>
    </div>
@endif
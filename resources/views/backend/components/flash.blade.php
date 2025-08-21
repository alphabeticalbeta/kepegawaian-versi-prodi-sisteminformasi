{{-- resources/views/components/flash.blade.php --}}
@if (session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 p-5 flex items-start justify-between">
        <div class="pr-3">
            <strong class="block">Berhasil</strong>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" aria-label="Tutup"
            onclick="this.closest('div').style.display='none'">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 p-5 flex items-start justify-between">
        <div class="pr-3">
            <strong class="block">Gagal</strong>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" aria-label="Tutup"
            onclick="this.closest('div').style.display='none'">&times;</button>
    </div>
@endif

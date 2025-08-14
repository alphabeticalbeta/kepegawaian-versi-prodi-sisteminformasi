
{{-- Partial: Form khusus jenis usulan Jabatan Dosen --}}
{{-- Expect: $usulan (wajib), opsional $bkdLabels --}}
@php
    $bkdLabels = $bkdLabels ?? [];
@endphp

<div class="bg-white shadow-md rounded-lg mt-6">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800">Form Khusus Jabatan Dosen</h3>
        <p class="text-sm text-gray-500">
            Informasi tambahan & form validasi khusus usulan jabatan dosen.
        </p>
    </div>

    <div class="p-6 space-y-6">
        {{-- Contoh: Data BKD --}}
        @if(!empty($bkdLabels))
            <div>
                <p class="text-sm font-medium text-gray-500">BKD</p>
                <ul class="list-disc ml-5 text-gray-800">
                    @foreach($bkdLabels as $label)
                        <li>{{ $label }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contoh: Bidang keahlian --}}
        <div>
            <label for="bidang_keahlian" class="block text-sm font-medium text-gray-700">
                Bidang Keahlian
            </label>
            <input type="text" name="bidang_keahlian" id="bidang_keahlian"
                   value="{{ old('bidang_keahlian', $usulan->data_usulan['bidang_keahlian'] ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        {{-- Contoh: Pengalaman Mengajar --}}
        <div>
            <label for="pengalaman_mengajar" class="block text-sm font-medium text-gray-700">
                Pengalaman Mengajar (tahun)
            </label>
            <input type="number" name="pengalaman_mengajar" id="pengalaman_mengajar"
                   value="{{ old('pengalaman_mengajar', $usulan->data_usulan['pengalaman_mengajar'] ?? '') }}"
                   min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</div>

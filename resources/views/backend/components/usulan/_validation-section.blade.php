{{-- Validation Section Component - Reusable untuk setiap kategori --}}
<div class="bg-white shadow-md rounded-lg mb-6 overflow-x-auto">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
        <h3 class="text-lg font-semibold text-white">
            {{ ucwords(str_replace('_', ' ', $category)) }}
        </h3>
    </div>

    @php
        $counts = $usulan->getSenateDecisionCounts();
        $minSetuju = $usulan->getSenateMinSetuju();
    @endphp

    {{-- Banner ringkas keputusan Senat --}}
    <div class="mb-3">
        <div class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                Setuju: <b>{{ $counts['setuju'] }}</b>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700">
                <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                Menolak: <b>{{ $counts['tolak'] }}</b>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                Total: <b>{{ $counts['total'] }}</b>
            </span>

            <span class="ml-3 text-gray-500">
                Minimal setuju yang dibutuhkan: <b>{{ $minSetuju }}</b>
            </span>
        </div>
    </div>

    <table class="w-full table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-1/3 px-4 py-3 text-left text-sm font-medium text-gray-700">
                    Data Usulan Pegawai
                </th>
                <th class="w-1/3 px-4 py-3 text-center text-sm font-medium text-gray-700">
                    Status Validasi
                </th>
                <th class="w-1/3 px-4 py-3 text-center text-sm font-medium text-gray-700">
                    Keterangan (Jika Tidak Sesuai)
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($fields as $field)
                {{-- FIXED: Case sensitivity - backend huruf kecil --}}
                @include('backend.components.usulan._validation-row', [
                    'category' => $category,
                    'field' => $field,
                    'usulan' => $usulan,
                    'existingValidation' => $existingValidation,
                    'canEdit' => $canEdit
                ])
            @endforeach
        </tbody>
    </table>
</div>
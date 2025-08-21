{{-- Validation Section Component - Reusable untuk setiap kategori --}}
<div class="bg-white shadow-md rounded-lg mb-6 overflow-x-auto">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
        <h3 class="text-lg font-semibold text-white">
            {{ ucwords(str_replace('_', ' ', $category)) }}
        </h3>
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
                @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.validation-row', [
                    'category' => $category,
                    'field' => $field,
                    'usulan' => $usulan,
                    'existingValidation' => $existingValidation
                ])
            @endforeach
        </tbody>
    </table>
</div>

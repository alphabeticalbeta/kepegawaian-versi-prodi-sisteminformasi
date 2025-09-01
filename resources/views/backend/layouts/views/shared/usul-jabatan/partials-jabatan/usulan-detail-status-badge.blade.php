{{-- Status Badge --}}
<div class="mb-6">
    @php
        // Get status directly from database (no more mapping)
        $displayStatus = $usulan->status_usulan;

        // Status colors mapping for standardized status
        $statusColors = [
            // Status standar baru
            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Permintaan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
        'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas' => 'bg-red-100 text-red-800 border-red-300',
            'Usulan Disetujui Admin Fakultas' => 'bg-green-100 text-green-800 border-green-300',
            'Usulan Perbaikan dari Kepegawaian Universitas' => 'bg-red-100 text-red-800 border-red-300',
            'Usulan Disetujui Kepegawaian Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'Permintaan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Usulan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Usulan Direkomendasi dari Penilai Universitas' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Direkomendasi Penilai Universitas' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Direkomendasikan oleh Tim Senat' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Sudah Dikirim ke Sister' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Permintaan Perbaikan Usulan dari Tim Sister' => 'bg-red-100 text-red-800 border-red-300',

            // Draft status constants (untuk Pegawai role)
            'Draft Usulan' => 'bg-gray-100 text-gray-800 border-gray-300',
            'Draft Perbaikan Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Draft Perbaikan Kepegawaian Universitas' => 'bg-red-100 text-red-800 border-red-300',
            'Draft Perbaikan Penilai Universitas' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Draft Perbaikan Tim Sister' => 'bg-red-100 text-red-800 border-red-300',

            // Fallback untuk status lama (akan dihapus setelah migrasi)
            'Usulan Disetujui Kepegawaian Universitas dan Menunggu Penilaian' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
        ];

        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    @endphp
    <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
        <span class="text-sm font-medium">Status: {{ $displayStatus }}</span>
    </div>
</div>

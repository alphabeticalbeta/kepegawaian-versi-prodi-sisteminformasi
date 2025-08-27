{{-- Status Badge --}}
<div class="mb-6">
    @php
        // Get status directly from database (no more mapping)
        $displayStatus = $usulan->status_usulan;
        
        // Status colors mapping for standardized status
        $statusColors = [
            // Status standar baru
            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Usulan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Permintaan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
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
            
            // Fallback untuk status lama (akan dihapus setelah migrasi)
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
            'Perbaikan Usulan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Perbaikan dari Tim Sister' => 'bg-red-100 text-red-800 border-red-300',
        ];
        
        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    @endphp
    <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
        <span class="text-sm font-medium">Status: {{ $displayStatus }}</span>
    </div>
</div>

{{-- Status Badge --}}
<div class="mb-6">
    @php
        // Function to get display status based on current status and role
        function getDisplayStatus($usulan, $currentRole) {
            $status = $usulan->status_usulan;
            
            // Mapping status berdasarkan alur kerja yang diminta
            switch ($status) {
                // Status untuk Pegawai
                case 'Diajukan':
                    return 'Usulan Dikirim ke Admin Fakultas';
                
                case 'Perbaikan Usulan':
                    if ($currentRole === 'Pegawai') {
                        return 'Usulan Perbaikan dari Admin Fakultas';
                    } elseif ($currentRole === 'Admin Fakultas') {
                        return 'Permintaan Perbaikan dari Admin Fakultas';
                    }
                    break;
                
                // Status untuk Admin Fakultas
                case 'Diusulkan ke Universitas':
                    if ($currentRole === 'Admin Fakultas') {
                        return 'Usulan Disetujui Admin Fakultas';
                    } elseif ($currentRole === 'Kepegawaian Universitas') {
                        return 'Usulan Disetujui Admin Fakultas';
                    }
                    break;
                
                // Status untuk Kepegawaian Universitas
                case 'Sedang Direview':
                    if ($currentRole === 'Kepegawaian Universitas') {
                        return 'Usulan Disetujui Kepegawaian Universitas';
                    }
                    break;
                
                case 'Menunggu Hasil Penilaian Tim Penilai':
                    if ($currentRole === 'Kepegawaian Universitas') {
                        return 'Usulan Disetujui Kepegawaian Universitas';
                    }
                    break;
                
                case 'Perbaikan Dari Tim Penilai':
                    if ($currentRole === 'Kepegawaian Universitas') {
                        return 'Permintaan Perbaikan dari Penilai Universitas';
                    } elseif ($currentRole === 'Pegawai') {
                        return 'Usulan Perbaikan dari Penilai Universitas';
                    } elseif ($currentRole === 'Admin Fakultas') {
                        return 'Usulan Perbaikan dari Penilai Universitas';
                    }
                    break;
                
                case 'Usulan Direkomendasi Tim Penilai':
                    if ($currentRole === 'Kepegawaian Universitas') {
                        return 'Usulan Direkomendasi dari Penilai Universitas';
                    }
                    break;
                
                // Status untuk Penilai Universitas
                case 'Perbaikan Dari Tim Penilai':
                    if ($currentRole === 'Penilai Universitas') {
                        return 'Usulan Perbaikan dari Penilai Universitas';
                    }
                    break;
                
                // Status untuk Tim Senat
                case 'Direkomendasikan':
                    if ($currentRole === 'Tim Senat') {
                        return 'Usulan Direkomendasikan oleh Tim Senat';
                    }
                    break;
                
                // Status untuk Tim Sister
                case 'Dikirim ke Sister':
                    return 'Usulan Sudah Dikirim ke Sister';
                
                case 'Perbaikan dari Tim Sister':
                    return 'Permintaan Perbaikan Usulan dari Tim Sister';
                
                // Status untuk perbaikan dari Kepegawaian Universitas
                case 'Perbaikan dari Kepegawaian Universitas':
                    if ($currentRole === 'Pegawai') {
                        return 'Usulan Perbaikan dari Kepegawaian Universitas';
                    } elseif ($currentRole === 'Admin Fakultas') {
                        return 'Usulan Perbaikan dari Kepegawaian Universitas';
                    }
                    break;
                
                default:
                    return $status;
            }
            
            return $status;
        }
        
        // Get display status
        $displayStatus = getDisplayStatus($usulan, $currentRole);
        
        // Status colors mapping
        $statusColors = [
            // Status lama (fallback)
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
            
            // Status baru
            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Usulan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Permintaan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Usulan Disetujui Admin Fakultas' => 'bg-green-100 text-green-800 border-green-300',
            'Usulan Disetujui Kepegawaian Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'Permintaan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Usulan Direkomendasi dari Penilai Universitas' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Usulan Direkomendasi Penilai Universitas' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Direkomendasikan oleh Tim Senat' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Usulan Sudah Dikirim ke Sister' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Permintaan Perbaikan Usulan dari Tim Sister' => 'bg-red-100 text-red-800 border-red-300',
            'Usulan Perbaikan dari Kepegawaian Universitas' => 'bg-red-100 text-red-800 border-red-300',
        ];
        
        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    @endphp
    <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
        <span class="text-sm font-medium">Status: {{ $displayStatus }}</span>
    </div>
</div>

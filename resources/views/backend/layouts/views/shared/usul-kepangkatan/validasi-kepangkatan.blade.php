@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Validasi Usulan Kepangkatan: ' . $usulan->pegawai->nama_lengkap)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Validasi Usulan Kepangkatan
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Validasi field-by-field usulan kepangkatan untuk {{ $usulan->pegawai->nama_lengkap }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('backend.kepegawaian-universitas.usulan.index') }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Daftar Usulan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    // Draft statuses (for Pegawai role)
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN => 'bg-gray-100 text-gray-800 border-gray-300',
                    
                    // Kepegawaian Universitas statuses (for kepangkatan)
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    
                    // BKN statuses (for kepangkatan)
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN => 'bg-orange-100 text-orange-800 border-orange-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN => 'bg-orange-100 text-orange-800 border-orange-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN => 'bg-red-100 text-red-800 border-red-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN => 'bg-green-100 text-green-800 border-green-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                
                // Define view-only statuses
                 $viewOnlyStatuses = [
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN,
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS,
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN,
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
                     \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT,
                 ];
                $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
                @if($isViewOnly)
                    <span class="ml-3 px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-3 h-3 inline mr-1"></i>
                        View Only
                    </span>
                @endif
            </div>
        </div>

        {{-- View Only Info Panel --}}
        @if($isViewOnly)
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                <div class="flex items-center">
                    <i data-lucide="info" class="w-5 h-5 text-orange-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-orange-800 mb-1">
                            Mode View Only
                        </div>
                        <div class="text-sm text-orange-700">
                            <strong>Perhatian:</strong> Usulan saat ini dalam status yang tidak memungkinkan perubahan validasi. 
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit.
                        </div>
                    </div>
                </div>
            </div>
        @endif



        {{-- Informasi Periode Usulan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="calendar-clock" class="w-6 h-6 mr-3"></i>
                    Informasi Periode Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Periode</label>
                        <p class="text-xs text-gray-600 mb-2">Periode usulan yang sedang berlangsung</p>
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? 'Tidak ada periode aktif' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                        <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan ? \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Usulan Kepangkatan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="award" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan Kepangkatan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Usulan Pangkat</label>
                        <p class="text-xs text-gray-600 mb-2">Jenis usulan pangkat yang dipilih</p>
                        <input type="text" value="{{ $usulan->data_usulan['jenis_usulan_pangkat'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pangkat Tujuan</label>
                        <p class="text-xs text-gray-600 mb-2">Pangkat yang ingin diajukan</p>
                        <input type="text" value="{{ $usulan->pangkatTujuan->pangkat ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Pegawai --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                    Informasi Pegawai
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                        <p class="text-xs text-gray-600 mb-2">Nama lengkap dengan gelar</p>
                        @php
                            $gelarDepan = $usulan->pegawai->gelar_depan ?? '';
                            $namaLengkap = $usulan->pegawai->nama_lengkap ?? '';
                            $gelarBelakang = $usulan->pegawai->gelar_belakang ?? '';
                            
                            $namaLengkapDisplay = '';
                            
                            // Tambahkan gelar depan jika ada dan bukan "-"
                            if (!empty($gelarDepan) && $gelarDepan !== '-') {
                                $namaLengkapDisplay .= $gelarDepan . ' ';
                            }
                            
                            // Tambahkan nama lengkap
                            $namaLengkapDisplay .= $namaLengkap;
                            
                            // Tambahkan gelar belakang jika ada dan bukan "-"
                            if (!empty($gelarBelakang) && $gelarBelakang !== '-') {
                                $namaLengkapDisplay .= ' ' . $gelarBelakang;
                            }
                        @endphp
                        <input type="text" value="{{ $namaLengkapDisplay ?: '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">NIP</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nip ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Kategori pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->jenis_pegawai ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Unit Kerja</label>
                        <p class="text-xs text-gray-600 mb-2">Unit kerja pegawai</p>
                        @php
                            $unitKerjaDisplay = '';
                            if ($usulan->pegawai->unitKerja) {
                                $unitKerjaDisplay = $usulan->pegawai->unitKerja->nama;
                                
                                // Tambahkan sub unit kerja jika ada
                                if ($usulan->pegawai->unitKerja->subUnitKerja) {
                                    $unitKerjaDisplay .= ' - ' . $usulan->pegawai->unitKerja->subUnitKerja->nama;
                                    
                                    // Tambahkan unit kerja utama jika ada
                                    if ($usulan->pegawai->unitKerja->subUnitKerja->unitKerja) {
                                        $unitKerjaDisplay = $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama . ' - ' . $unitKerjaDisplay;
                                    }
                                }
                            }
                        @endphp
                        <input type="text" value="{{ $unitKerjaDisplay ?: '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Include Tabel Validasi --}}
        @include('backend.layouts.views.shared.usul-kepangkatan.usulan-detail-validation-table', ['isViewOnly' => $isViewOnly])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success message in session
    @if(session('success') && session('reload'))
        // Show success notification
        showSuccessNotification("{{ session('success') }}");
        
        // Reload page after 1.5 seconds
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    @endif
    
    // Function to show success notification
    function showSuccessNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 p-4 bg-green-50 border border-green-200 rounded-xl shadow-lg animate-fade-in';
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">${message}</span>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
});
</script>

@push('scripts')
<script>
// Standardized submitAction function for kepangkatan validation
function submitAction(actionType, catatan) {
    console.log('submitAction called with:', { actionType, catatan });

    const form = document.getElementById('validationForm');
    const actionInput = document.querySelector('input[name="action_type"]');
    
    if (actionInput) {
        actionInput.value = actionType;
    }

    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyimpan validasi',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form
    const formData = new FormData(form);

    console.log('Submitting form to:', form.action);
    console.log('Action type:', actionType);

    // Debug form data
    console.log('FormData entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            Swal.fire({
                title: '🎉 Berhasil!',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-6xl text-green-500"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800 mb-2">${data.message}</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Data validasi telah berhasil disimpan.
                            </p>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#10b981',
                allowOutsideClick: false
            }).then((result) => {
                // Reload halaman setelah save berhasil
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        } else {
            // Show error notification
            Swal.fire({
                title: '❌ Gagal!',
                text: data.message || 'Terjadi kesalahan saat menyimpan validasi',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error notification
        Swal.fire({
            title: '❌ Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
        });
    });
}

// Check for SweetAlert2
if (typeof Swal === 'undefined') {
    console.warn('SweetAlert2 not loaded, using fallback alert');
    window.Swal = {
        fire: function(options) {
            if (options.icon === 'success') {
                alert('Berhasil: ' + (options.text || options.html || ''));
                if (options.then && typeof options.then === 'function') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                alert('Error: ' + (options.text || options.html || ''));
            }
        }
    };
}

// Function to change usulan status
function changeStatus(newStatus) {
    console.log('Changing status to:', newStatus);
    
    // Show confirmation dialog for ALL status changes
    Swal.fire({
        title: 'Konfirmasi Kirim Usulan',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-6xl text-yellow-500"></i>
                </div>
                <p class="text-lg font-semibold text-gray-800 mb-2">Apakah Anda yakin ingin mengirim usulan?</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                    <p class="text-sm text-blue-800">
                        <strong>Status Usulan:</strong> ${newStatus}
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yakin',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengubah status usulan',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send request to change status
            processStatusChangeRequest(newStatus);
        }
    });
}



// Function to send status change request
function processStatusChangeRequest(newStatus) {
    fetch(`{{ route('backend.kepegawaian-universitas.usulan.change-status', $usulan->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            new_status: newStatus,
            keterangan: 'Status diubah melalui halaman validasi kepangkatan'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            Swal.fire({
                title: '🎉 Berhasil!',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-6xl text-green-500"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800 mb-2">${data.message}</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                ${getStatusChangeMessage(newStatus)}
                            </p>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#10b981',
                allowOutsideClick: false
            }).then((result) => {
                // Reload halaman setelah status berhasil diubah
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        } else {
            // Show error notification
            Swal.fire({
                title: '❌ Gagal!',
                text: data.message || 'Terjadi kesalahan saat mengubah status',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error notification
        Swal.fire({
            title: '❌ Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
        });
    });
}

// Function to get appropriate message based on status
function getStatusChangeMessage(newStatus) {
    const statusMessages = {
        '{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS }}': 'Permintaan perbaikan berhasil dikirim ke pegawai dari Kepegawaian Universitas.',
        '{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN }}': 'Permintaan perbaikan berhasil dikirim ke pegawai dari BKN.',
        '{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_BKN }}': 'Status usulan berhasil diubah ke: Belum Direkomendasikan Dari BKN',
        '{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN }}': 'Status usulan berhasil diubah ke: Usulan Direkomendasikan BKN'
    };
    
    return statusMessages[newStatus] || `Status usulan berhasil diubah ke: ${newStatus}`;
}
</script>
@endpush

@endsection

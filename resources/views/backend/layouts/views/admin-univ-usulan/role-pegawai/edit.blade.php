@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Edit Role Pegawai - ' . $pegawai->nama_lengkap)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('backend.admin-univ-usulan.role-pegawai.index') }}"
                           class="inline-flex items-center text-indigo-600 hover:text-indigo-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 mb-2">
                        Edit Role Pegawai
                    </h1>
                    <p class="text-slate-600 text-lg">
                        Kelola role dan permission untuk {{ $pegawai->nama_lengkap }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Pegawai Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 p-6 sticky top-8">
                    <div class="text-center mb-6">
                        <div class="relative mx-auto w-24 h-24 mb-4">
                            @if($pegawai->foto)
                                                        <img class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg"
                             src="{{ $pegawai->foto ? route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) . '&background=6366f1&color=fff&size=96' }}"
                             alt="{{ $pegawai->nama_lengkap }}"
                             onerror="this.parentElement.innerHTML='<div class=\'w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg\'>{{ substr($pegawai->nama_lengkap, 0, 2) }}</div>'">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg">
                                    {{ substr($pegawai->nama_lengkap, 0, 2) }}
                                </div>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $pegawai->nama_lengkap }}</h2>
                        <p class="text-slate-600">{{ $pegawai->email }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <span class="text-sm text-slate-600">NIP</span>
                            <span class="text-sm font-mono text-slate-800">{{ $pegawai->nip }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <span class="text-sm text-slate-600">Jenis Pegawai</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $pegawai->jenis_pegawai == 'Dosen' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $pegawai->jenis_pegawai }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <span class="text-sm text-slate-600">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                {{ $pegawai->status_kepegawaian }}
                            </span>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-sm text-slate-600 block mb-2">Role Saat Ini</span>
                            <div class="flex flex-wrap gap-1">
                                @forelse($pegawai->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $role->name == 'Admin Universitas Usulan' ? 'bg-red-100 text-red-800' :
                                           ($role->name == 'Admin Fakultas' ? 'bg-green-100 text-green-800' :
                                           ($role->name == 'Penilai Universitas' ? 'bg-purple-100 text-purple-800' :
                                           'bg-blue-100 text-blue-800')) }}">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-sm text-slate-500">Tidak ada role</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Management Form -->
            <div class="lg:col-span-2">
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h3 class="text-xl font-semibold text-slate-800">Kelola Role</h3>
                        <p class="text-slate-600 mt-1">Pilih role yang akan diberikan kepada pegawai ini</p>
                    </div>

                    <form action="{{ route('backend.admin-univ-usulan.role-pegawai.update', $pegawai) }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        <!-- Role Selection -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-medium text-slate-800 mb-4">Pilih Role</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($roles as $role)
                                        <div class="relative">
                                            <input type="checkbox"
                                                   id="role_{{ $role->id }}"
                                                   name="roles[]"
                                                   value="{{ $role->name }}"
                                                   {{ $pegawai->hasRole($role->name, 'pegawai') ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <label for="role_{{ $role->id }}"
                                                   class="block p-4 border-2 border-slate-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-slate-300">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <div class="w-5 h-5 border-2 border-slate-300 rounded peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center transition-all duration-200">
                                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-slate-800 mb-1">{{ $role->name }}</h5>
                                                        <p class="text-sm text-slate-600">
                                                            @switch($role->name)
                                                                @case('Admin Universitas Usulan')
                                                                    Akses penuh ke semua fitur sistem
                                                                    @break
                                                                @case('Admin Universitas')
                                                                    Mengelola data universitas secara umum
                                                                    @break
                                                                @case('Admin Fakultas')
                                                                    Mengelola data pegawai di fakultas tertentu
                                                                    @break
                                                                @case('Admin Keuangan')
                                                                    Mengelola data keuangan dan anggaran
                                                                    @break
                                                                @case('Tim Senat')
                                                                    Mengelola keputusan dan kebijakan senat
                                                                    @break
                                                                @case('Penilai Universitas')
                                                                    Menilai usulan jabatan pegawai
                                                                    @break
                                                                @case('Pegawai Unmul')
                                                                    Akses terbatas untuk data pribadi dan usulan
                                                                    @break
                                                                @default
                                                                    Role standar
                                                            @endswitch
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Role Descriptions -->
                            <div class="bg-slate-50 rounded-xl p-6">
                                <h4 class="text-lg font-medium text-slate-800 mb-4">Deskripsi Role</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-red-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Admin Universitas Usulan</h5>
                                            <p class="text-sm text-slate-600">Super admin dengan akses penuh ke semua fitur sistem, termasuk manajemen user, role, dan data master.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-indigo-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Admin Universitas</h5>
                                            <p class="text-sm text-slate-600">Admin yang mengelola data universitas secara umum dan memiliki akses ke fitur administrasi universitas.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Admin Fakultas</h5>
                                            <p class="text-sm text-slate-600">Admin tingkat fakultas yang dapat mengelola data pegawai di fakultas tertentu.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-purple-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Penilai Universitas</h5>
                                            <p class="text-sm text-slate-600">Penilai yang bertugas menilai usulan jabatan pegawai berdasarkan kriteria yang ditentukan.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Pegawai Unmul</h5>
                                            <p class="text-sm text-slate-600">Pegawai biasa dengan akses terbatas untuk mengelola data pribadi dan mengajukan usulan jabatan.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Admin Keuangan</h5>
                                            <p class="text-sm text-slate-600">Admin yang bertanggung jawab mengelola data keuangan, anggaran, dan laporan keuangan universitas.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-3 h-3 bg-orange-500 rounded-full mt-2"></div>
                                        <div>
                                            <h5 class="font-medium text-slate-800">Tim Senat</h5>
                                            <p class="text-sm text-slate-600">Tim yang bertanggung jawab mengelola keputusan, kebijakan, dan regulasi senat universitas.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-yellow-800">Perhatian</h5>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Perubahan role akan mempengaruhi akses pegawai ke sistem. Pastikan role yang dipilih sesuai dengan tanggung jawab pegawai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-slate-200">
                            <button type="submit"
                                    class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Simpan Perubahan</span>
                            </button>
                            <a href="{{ route('backend.admin-univ-usulan.role-pegawai.index') }}"
                               class="px-6 py-3 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors duration-200 text-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize visual state for checkboxes on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
            updateCheckboxVisualState(checkbox);
        });
    });

    // Add visual feedback for role selection
    document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateCheckboxVisualState(this);
        });
    });

    // Function to update checkbox visual state
    function updateCheckboxVisualState(checkbox) {
        const label = checkbox.nextElementSibling;
        const checkboxDiv = label.querySelector('.w-5.h-5');
        const checkIcon = label.querySelector('svg');

        if (checkbox.checked) {
            label.classList.add('border-indigo-500', 'bg-indigo-50');
            label.classList.remove('border-slate-200');
            checkboxDiv.classList.add('border-indigo-500', 'bg-indigo-500');
            checkboxDiv.classList.remove('border-slate-300');
            checkIcon.classList.add('opacity-100');
            checkIcon.classList.remove('opacity-0');
        } else {
            label.classList.remove('border-indigo-500', 'bg-indigo-50');
            label.classList.add('border-slate-200');
            checkboxDiv.classList.remove('border-indigo-500', 'bg-indigo-500');
            checkboxDiv.classList.add('border-slate-300');
            checkIcon.classList.remove('opacity-100');
            checkIcon.classList.add('opacity-0');
        }
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (checkedRoles.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu role untuk pegawai ini.');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.querySelector('span').textContent;
        const originalIcon = submitBtn.querySelector('svg').outerHTML;

        submitBtn.disabled = true;
        submitBtn.querySelector('span').textContent = 'Menyimpan...';
        submitBtn.querySelector('svg').outerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        `;

        // Re-enable button after 3 seconds if form doesn't submit
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.querySelector('span').textContent = originalText;
                submitBtn.querySelector('svg').outerHTML = originalIcon;
            }
        }, 3000);
    });
</script>
@endpush

{{-- resources/views/backend/components/profile/tabs/security-tab.blade.php --}}
<div x-show="activeTab === 'security'" x-transition class="space-y-8">
    {{-- Password Section --}}
    <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                <i data-lucide="shield-check" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Keamanan Akun</h3>
                <p class="text-sm text-red-700 mb-6">
                    Kelola password dan informasi keamanan akun Anda. Pastikan menggunakan password yang kuat dan unik.
                </p>

                @if($isEditing)
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="key" class="w-4 h-4 inline mr-1"></i>
                                Password Baru
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Masukkan password baru">
                                <button type="button"
                                        onclick="togglePasswordVisibility('new_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400 hover:text-gray-600"></i>
                                </button>
                            </div>
                            <div id="password-strength" class="mt-2 hidden">
                                <div class="flex space-x-1">
                                    <div class="h-1 flex-1 rounded bg-gray-200" id="strength-bar-1"></div>
                                    <div class="h-1 flex-1 rounded bg-gray-200" id="strength-bar-2"></div>
                                    <div class="h-1 flex-1 rounded bg-gray-200" id="strength-bar-3"></div>
                                    <div class="h-1 flex-1 rounded bg-gray-200" id="strength-bar-4"></div>
                                </div>
                                <p id="password-strength-text" class="text-xs mt-1"></p>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 pt-6">
                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Ulangi password baru">
                                <button type="button"
                                        onclick="togglePasswordVisibility('new_password_confirmation')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400 hover:text-gray-600"></i>
                                </button>
                            </div>
                            <div id="password-match" class="mt-1 hidden">
                                <p id="password-match-text" class="text-xs"></p>
                            </div>
                        </div>

                        {{-- Password Requirements --}}
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">
                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                    Syarat Password
                                </h4>
                                <ul class="text-xs text-blue-700 space-y-1">
                                    <li class="flex items-center gap-2" id="req-length">
                                        <i data-lucide="circle" class="w-3 h-3"></i>
                                        Minimal 8 karakter
                                    </li>
                                    <li class="flex items-center gap-2" id="req-uppercase">
                                        <i data-lucide="circle" class="w-3 h-3"></i>
                                        Mengandung huruf besar (A-Z)
                                    </li>
                                    <li class="flex items-center gap-2" id="req-lowercase">
                                        <i data-lucide="circle" class="w-3 h-3"></i>
                                        Mengandung huruf kecil (a-z)
                                    </li>
                                    <li class="flex items-center gap-2" id="req-number">
                                        <i data-lucide="circle" class="w-3 h-3"></i>
                                        Mengandung angka (0-9)
                                    </li>
                                    <li class="flex items-center gap-2" id="req-special">
                                        <i data-lucide="circle" class="w-3 h-3"></i>
                                        Mengandung karakter khusus (!@#$%^&*)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-700 font-medium">Password terakhir diubah:</p>
                            <p class="text-xs text-red-600">{{ $pegawai->updated_at->diffForHumans() ?? 'Tidak diketahui' }}</p>
                        </div>
                        <a href="{{ route('pegawai-unmul.profile.edit') }}"
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            Ubah Password
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Jenis Pegawai Section --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Kategori Pegawai</h3>
                <p class="text-sm text-blue-700 mb-6">
                    Informasi kategori dan status kepegawaian Anda di sistem UNMUL.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jenis Pegawai --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                            Jenis Pegawai
                        </label>
                        @if($isEditing)
                            <select name="jenis_pegawai"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-gray-100"
                                    readonly disabled>
                                <option value="{{ $pegawai->jenis_pegawai }}" selected>{{ $pegawai->jenis_pegawai }}</option>
                            </select>
                            <input type="hidden" name="jenis_pegawai" value="{{ $pegawai->jenis_pegawai }}">
                            <p class="text-xs text-gray-500 mt-1">
                                <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                                Field ini tidak dapat diubah
                            </p>
                        @else
                            <div class="flex items-center gap-2">
                                @php
                                    $jenisIcon = $pegawai->jenis_pegawai === 'Dosen' ? 'graduation-cap' : 'briefcase';
                                    $jenisClass = $pegawai->jenis_pegawai === 'Dosen' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $jenisClass }}">
                                    <i data-lucide="{{ $jenisIcon }}" class="w-3 h-3 mr-1"></i>
                                    {{ $pegawai->jenis_pegawai ?? 'Belum diset' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Status Kepegawaian --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="badge-check" class="w-4 h-4 inline mr-1"></i>
                            Status Kepegawaian
                        </label>
                        @if($isEditing)
                            <select name="status_kepegawaian"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-gray-100"
                                    readonly disabled>
                                <option value="{{ $pegawai->status_kepegawaian }}" selected>{{ $pegawai->status_kepegawaian }}</option>
                            </select>
                            <input type="hidden" name="status_kepegawaian" value="{{ $pegawai->status_kepegawaian }}">
                            <p class="text-xs text-gray-500 mt-1">
                                <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                                Field ini tidak dapat diubah
                            </p>
                        @else
                            <div class="flex items-center gap-2">
                                @php
                                    $statusClass = match($pegawai->status_kepegawaian) {
                                        'Dosen PNS', 'Tenaga Kependidikan PNS' => 'bg-green-100 text-green-700 border-green-300',
                                        'Dosen PPPK', 'Tenaga Kependidikan PPPK' => 'bg-blue-100 text-blue-700 border-blue-300',
                                        default => 'bg-gray-100 text-gray-700 border-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClass }}">
                                    <div class="w-2 h-2 rounded-full bg-current mr-2"></div>
                                    {{ $pegawai->status_kepegawaian ?? 'Belum diset' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Eligibility Info --}}
                    <div class="md:col-span-2">
                        <div class="bg-white border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                Hak Akses Usulan
                            </h4>
                            @php
                                $isPnsEligible = in_array($pegawai->status_kepegawaian, ['Dosen PNS', 'Tenaga Kependidikan PNS']);
                            @endphp
                            @if($isPnsEligible)
                                <div class="flex items-center gap-2 text-sm text-green-700">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    <span>Anda memiliki hak untuk mengajukan usulan jabatan dan pangkat</span>
                                </div>
                                <ul class="text-xs text-green-600 mt-2 ml-6 space-y-1">
                                    @if($pegawai->jenis_pegawai === 'Dosen')
                                        <li>• Usulan Jabatan Dosen (Asisten Ahli, Lektor, Lektor Kepala, Guru Besar)</li>
                                    @else
                                        <li>• Usulan Jabatan Tenaga Kependidikan (Fungsional & Struktural)</li>
                                    @endif
                                    <li>• Usulan Kenaikan Pangkat</li>
                                </ul>
                            @else
                                <div class="flex items-center gap-2 text-sm text-orange-700">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    <span>Hak usulan terbatas sesuai status kepegawaian</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isEditing)
    @push('scripts')
    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                field.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            let requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            Object.values(requirements).forEach(met => {
                if (met) strength++;
            });

            // Update requirement indicators
            Object.keys(requirements).forEach(req => {
                const element = document.getElementById(`req-${req}`);
                const icon = element.querySelector('i');
                if (requirements[req]) {
                    element.classList.add('text-green-700');
                    element.classList.remove('text-blue-700');
                    icon.setAttribute('data-lucide', 'check-circle');
                } else {
                    element.classList.add('text-blue-700');
                    element.classList.remove('text-green-700');
                    icon.setAttribute('data-lucide', 'circle');
                }
            });

            // Update strength bars
            const bars = ['strength-bar-1', 'strength-bar-2', 'strength-bar-3', 'strength-bar-4'];
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];

            bars.forEach((barId, index) => {
                const bar = document.getElementById(barId);
                bar.className = 'h-1 flex-1 rounded';
                if (index < strength) {
                    bar.classList.add(colors[Math.min(strength - 1, 3)]);
                } else {
                    bar.classList.add('bg-gray-200');
                }
            });

            // Update strength text
            const strengthText = document.getElementById('password-strength-text');
            const strengthLabels = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
            const strengthColors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600', 'text-green-700'];

            strengthText.textContent = strengthLabels[strength] || '';
            strengthText.className = `text-xs mt-1 ${strengthColors[strength] || ''}`;

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            return strength;
        }

        function checkPasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            const matchElement = document.getElementById('password-match');
            const matchText = document.getElementById('password-match-text');

            if (confirmPassword.length > 0) {
                matchElement.classList.remove('hidden');
                if (newPassword === confirmPassword) {
                    matchText.textContent = 'Password cocok';
                    matchText.className = 'text-xs text-green-600';
                } else {
                    matchText.textContent = 'Password tidak cocok';
                    matchText.className = 'text-xs text-red-600';
                }
            } else {
                matchElement.classList.add('hidden');
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordField = document.getElementById('new_password');
            const confirmPasswordField = document.getElementById('new_password_confirmation');

            if (newPasswordField) {
                newPasswordField.addEventListener('input', function() {
                    const strengthElement = document.getElementById('password-strength');
                    if (this.value.length > 0) {
                        strengthElement.classList.remove('hidden');
                        checkPasswordStrength(this.value);
                    } else {
                        strengthElement.classList.add('hidden');
                    }
                    checkPasswordMatch();
                });
            }

            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', function() {
                    checkPasswordMatch();
                });
            }
        });
    </script>
    @endpush
@endif

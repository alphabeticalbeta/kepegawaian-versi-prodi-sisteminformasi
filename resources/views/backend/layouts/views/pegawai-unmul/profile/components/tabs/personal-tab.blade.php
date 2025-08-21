{{-- resources/views/backend/components/profile/tabs/personal-tab.blade.php --}}
<div x-show="activeTab === 'personal'" x-transition>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Nama Lengkap --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            @if($isEditing)
                <input type="text" name="nama_lengkap"
                       value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_lengkap') border-red-500 @else border-gray-300 @enderror"
                       required>
                @error('nama_lengkap')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayNamaLengkap($pegawai) }}</p>
            @endif
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="mail" class="w-4 h-4 inline mr-1"></i>
                Email <span class="text-red-500">*</span>
            </label>
            @if($isEditing)
                <input type="email" name="email"
                       value="{{ old('email', $pegawai->email) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @else border-gray-300 @enderror"
                       required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            @else
                <p class="text-gray-900 py-2 flex items-center gap-2">
                    {{ \App\Helpers\ProfileDisplayHelper::displayEmail($pegawai) }}
                    @if($pegawai->email)
                        <a href="mailto:{{ $pegawai->email }}"
                           class="text-indigo-600 hover:text-indigo-800">
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    @endif
                </p>
            @endif
        </div>

        {{-- Gelar Depan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                Gelar Depan
            </label>
            @if($isEditing)
                <input type="text" name="gelar_depan"
                       value="{{ old('gelar_depan', $pegawai->gelar_depan) }}"
                       placeholder="Dr., Prof., dll."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->gelar_depan) }}</p>
            @endif
        </div>

        {{-- Gelar Belakang --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-1"></i>
                Gelar Belakang
            </label>
            @if($isEditing)
                <input type="text" name="gelar_belakang"
                       value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}"
                       placeholder="S.Kom., M.T., Ph.D., dll."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->gelar_belakang) }}</p>
            @endif
        </div>

        {{-- Tempat Lahir --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                Tempat Lahir
            </label>
            @if($isEditing)
                <input type="text" name="tempat_lahir"
                       value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}"
                       placeholder="Samarinda"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->tempat_lahir) }}</p>
            @endif
        </div>

        {{-- Tanggal Lahir --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                Tanggal Lahir
            </label>
            @if($isEditing)
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ formatDate($pegawai->tanggal_lahir) }}</p>
            @endif
        </div>

        {{-- Jenis Kelamin --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                Jenis Kelamin
            </label>
            @if($isEditing)
                <select name="jenis_kelamin"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Laki-Laki' ? 'selected' : '' }}>
                        Laki-Laki
                    </option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>
            @else
                <p class="text-gray-900 py-2 flex items-center gap-2">
                    @if($pegawai->jenis_kelamin)
                        @if($pegawai->jenis_kelamin === 'Laki-Laki')
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        @else
                            <i data-lucide="user" class="w-4 h-4 text-pink-600"></i>
                        @endif
                    @endif
                    {{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->jenis_kelamin) }}
                </p>
            @endif
        </div>

        {{-- Nomor HP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="phone" class="w-4 h-4 inline mr-1"></i>
                Nomor HP
            </label>
            @if($isEditing)
                <input type="text" name="nomor_handphone"
                       value="{{ old('nomor_handphone', $pegawai->nomor_handphone) }}"
                       placeholder="08123456789"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2 flex items-center gap-2">
                    {{ \App\Helpers\ProfileDisplayHelper::displayNomorHandphone($pegawai) }}
                    @if($pegawai->nomor_handphone)
                        <a href="tel:{{ $pegawai->nomor_handphone }}"
                           class="text-indigo-600 hover:text-indigo-800">
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    @endif
                </p>
            @endif
        </div>

        {{-- Pendidikan Terakhir --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-1"></i>
                Pendidikan Terakhir
            </label>
            @if($isEditing)
                <select name="pendidikan_terakhir"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                    @php
                        $pendidikanOptions = [
                            'Sekolah Dasar (SD)',
                            'Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat',
                            'Sekolah Lanjutan Tingkat Menengah (SLTA)',
                            'Diploma I',
                            'Diploma II',
                            'Diploma III',
                            'Sarjana (S1) / Diploma IV / Sederajat',
                            'Magister (S2) / Sederajat',
                            'Doktor (S3) / Sederajat'
                        ];
                    @endphp
                    @foreach($pendidikanOptions as $option)
                        <option value="{{ $option }}"
                                {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            @else
                <p class="text-gray-900 py-2 flex items-center gap-2">
                    @if($pegawai->pendidikan_terakhir)
                        @php
                            $level = '';
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            if(str_contains($pegawai->pendidikan_terakhir, 'S3') || str_contains($pegawai->pendidikan_terakhir, 'Doktor')) {
                                $level = 'S3';
                                $badgeClass = 'bg-purple-100 text-purple-800';
                            } elseif(str_contains($pegawai->pendidikan_terakhir, 'S2') || str_contains($pegawai->pendidikan_terakhir, 'Magister')) {
                                $level = 'S2';
                                $badgeClass = 'bg-blue-100 text-blue-800';
                            } elseif(str_contains($pegawai->pendidikan_terakhir, 'S1') || str_contains($pegawai->pendidikan_terakhir, 'Sarjana')) {
                                $level = 'S1';
                                $badgeClass = 'bg-green-100 text-green-800';
                            } elseif(str_contains($pegawai->pendidikan_terakhir, 'Diploma')) {
                                $level = 'D';
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                            }
                        @endphp
                        @if($level)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $badgeClass }}">
                                {{ $level }}
                            </span>
                        @endif
                    @endif
                    {{ \App\Helpers\ProfileDisplayHelper::displayPendidikanTerakhir($pegawai) }}
                </p>
            @endif
        </div>

        {{-- Nama Universitas/Sekolah --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="building" class="w-4 h-4 inline mr-1"></i>
                Nama Universitas/Sekolah
            </label>
            @if($isEditing)
                <input type="text" name="nama_universitas_sekolah"
                       value="{{ old('nama_universitas_sekolah', $pegawai->nama_universitas_sekolah) }}"
                       placeholder="Universitas Mulawarman"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_universitas_sekolah') border-red-500 @enderror">
                @error('nama_universitas_sekolah')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayNamaUniversitasSekolah($pegawai) }}</p>
            @endif
        </div>

        {{-- Nama Program Studi/Jurusan --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="book-open" class="w-4 h-4 inline mr-1"></i>
                Nama Program Studi/Jurusan
            </label>
            @if($isEditing)
                <input type="text" name="nama_prodi_jurusan"
                       value="{{ old('nama_prodi_jurusan', $pegawai->nama_prodi_jurusan) }}"
                       placeholder="Teknik Informatika"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_prodi_jurusan') border-red-500 @enderror">
                @error('nama_prodi_jurusan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayNamaProdiJurusan($pegawai) }}</p>
            @endif
        </div>
    </div>
</div>

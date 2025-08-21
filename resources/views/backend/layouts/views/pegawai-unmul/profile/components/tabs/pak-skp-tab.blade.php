{{-- resources/views/backend/layouts/pegawai-unmul/profile/components/tabs/pak-skp-tab.blade.php --}}
<div x-show="activeTab === 'pak_skp'" x-transition>
    {{-- Header Info --}}
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 bg-amber-100 rounded-full p-2">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-amber-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800 text-sm">Penilaian Angka Kredit (PAK) & Sasaran Kinerja Pegawai (SKP)</h3>
                <p class="text-amber-700 text-xs mt-1">
                    Data penilaian kinerja dan angka kredit untuk keperluan kenaikan pangkat dan jabatan.
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        {{-- Kinerja & SKP Section --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center gap-2 mb-6">
                <div class="bg-blue-100 rounded-full p-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Penilaian Kinerja & SKP</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Row 1: Tahun Pertama --}}
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            SKP Tahun {{ date('Y') - 1 }}
                        </h4>

                        {{-- Predikat Kinerja Tahun Pertama --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                                Predikat Kinerja SKP Tahun {{ date('Y') - 1 }}
                            </label>
                            @if($isEditing)
                                <select name="predikat_kinerja_tahun_pertama"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Predikat --</option>
                                    @php
                                        $predikatOptions = ['Sangat Baik', 'Baik', 'Perlu Perbaikan'];
                                    @endphp
                                    @foreach($predikatOptions as $option)
                                        <option value="{{ $option }}"
                                                {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="flex items-center gap-2">
                                    @if($pegawai->predikat_kinerja_tahun_pertama)
                                        @php
                                            $predikatClass = match($pegawai->predikat_kinerja_tahun_pertama) {
                                                'Sangat Baik' => 'bg-green-100 text-green-700 border-green-300',
                                                'Baik' => 'bg-blue-100 text-blue-700 border-blue-300',
                                                'Perlu Perbaikan' => 'bg-orange-100 text-orange-700 border-orange-300',
                                                default => 'bg-gray-100 text-gray-700 border-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $predikatClass }}">
                                            <div class="w-2 h-2 rounded-full bg-current mr-2"></div>
                                            {{ $pegawai->predikat_kinerja_tahun_pertama }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Row 2: Tahun Kedua --}}
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 mb-3 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            SKP Tahun {{ date('Y') - 1 }}
                        </h4>

                        {{-- Predikat Kinerja Tahun Kedua --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                                Predikat Kinerja SKP Tahun {{ date('Y') - 1 }}
                            </label>
                            @if($isEditing)
                                <select name="predikat_kinerja_tahun_kedua"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Predikat --</option>
                                    @foreach($predikatOptions as $option)
                                        <option value="{{ $option }}"
                                                {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="flex items-center gap-2">
                                    @if($pegawai->predikat_kinerja_tahun_kedua)
                                        @php
                                            $predikatClass = match($pegawai->predikat_kinerja_tahun_kedua) {
                                                'Sangat Baik' => 'bg-green-100 text-green-700 border-green-300',
                                                'Baik' => 'bg-blue-100 text-blue-700 border-blue-300',
                                                'Perlu Perbaikan' => 'bg-orange-100 text-orange-700 border-orange-300',
                                                default => 'bg-gray-100 text-gray-700 border-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $predikatClass }}">
                                            <div class="w-2 h-2 rounded-full bg-current mr-2"></div>
                                            {{ $pegawai->predikat_kinerja_tahun_kedua }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAK Konversi Section - Conditional Display --}}
        @php
            $showPakSection = false;
            if ($pegawai->jabatan) {
                $showPakSection = in_array($pegawai->jabatan->jenis_jabatan, [
                    'Dosen Fungsional',
                    'Tenaga Kependidikan Fungsional Tertentu'
                ]);
            }
        @endphp

        @if($showPakSection)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-6">
                    <div class="bg-purple-100 rounded-full p-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Penilaian Angka Kredit (PAK)</h3>
                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                        {{ $pegawai->jabatan->jenis_jabatan }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    {{-- Nilai Konversi --}}
                    <div class="bg-gradient-to-r from-purple-50 to-violet-50 border border-purple-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="hash" class="w-4 h-4 inline mr-1"></i>
                            Nilai Konversi Tahun {{ date('Y') - 1 }}
                        </label>
                        @if($isEditing)
                            <input type="number" name="nilai_konversi"
                                   value="{{ old('nilai_konversi', $pegawai->nilai_konversi) }}"
                                   step="0.01"
                                   placeholder="Contoh: 150.25"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Masukkan nilai konversi angka kredit Tahun {{ date('Y') - 1 }}</p>
                        @else
                            <div class="flex items-center gap-2">
                                @if($pegawai->nilai_konversi)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                                        <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                                        {{ number_format($pegawai->nilai_konversi, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-500">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->nilai_konversi, 'Belum diisi') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PAK Info Section --}}
                <div class="mt-4 bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-purple-600 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-purple-800 text-sm">Informasi PAK Konversi</h4>
                            <p class="text-purple-700 text-xs mt-1">
                                PAK Konversi diperlukan untuk {{ $pegawai->jabatan->jenis_jabatan }} sebagai syarat kenaikan pangkat/jabatan.
                                Pastikan nilai konversi sudah sesuai dengan ketentuan yang berlaku.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="bg-gray-200 rounded-full p-3">
                        <i data-lucide="file-x" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-600">PAK Konversi Tidak Diperlukan</h3>
                        <p class="text-gray-500 text-sm mt-1">
                            PAK Konversi hanya diperlukan untuk jabatan Dosen Fungsional dan Tenaga Kependidikan Fungsional Tertentu.
                        </p>
                        @if($pegawai->jabatan)
                            <p class="text-gray-400 text-xs mt-2">
                                Jabatan saat ini: <span class="font-medium">{{ $pegawai->jabatan->jenis_jabatan }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

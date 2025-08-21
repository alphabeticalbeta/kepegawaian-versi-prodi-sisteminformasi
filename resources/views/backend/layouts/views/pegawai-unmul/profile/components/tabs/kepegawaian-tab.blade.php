{{-- resources/views/backend/components/profile/tabs/kepegawaian-tab.blade.php --}}
<div x-show="activeTab === 'kepegawaian'" x-transition>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- NIP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="credit-card" class="w-4 h-4 inline mr-1"></i>
                NIP <span class="text-red-500">*</span>
            </label>
            @if($isEditing)
                <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                       readonly>
                <p class="text-xs text-gray-500 mt-1">
                    <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                    NIP tidak dapat diubah
                </p>
            @else
                <p class="text-gray-900 py-2 font-mono text-lg">{{ $pegawai->nip }}</p>
            @endif
        </div>

        {{-- No Kartu Pegawai --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="id-card" class="w-4 h-4 inline mr-1"></i>
                No. Kartu Pegawai
            </label>
            @if($isEditing)
                <input type="text" name="nomor_kartu_pegawai"
                       value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai) }}"
                       placeholder="Nomor pada kartu pegawai"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->nomor_kartu_pegawai) }}</p>
            @endif
        </div>

        {{-- Jenis Pegawai --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                Jenis Pegawai
            </label>
            @if($isEditing)
                <input type="hidden" name="jenis_pegawai" value="{{ $pegawai->jenis_pegawai }}">
                <input type="text" value="{{ $pegawai->jenis_pegawai }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                       readonly disabled>
                <p class="text-xs text-gray-500 mt-1">
                    <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                    Jenis pegawai tidak dapat diubah
                </p>
            @else
                <div class="flex items-center gap-2">
                    @php
                        $jenisIcon = $pegawai->jenis_pegawai === 'Dosen' ? 'graduation-cap' : 'briefcase';
                        $jenisClass = $pegawai->jenis_pegawai === 'Dosen' ? 'bg-purple-100 text-purple-700 border-purple-300' : 'bg-green-100 text-green-700 border-green-300';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $jenisClass }}">
                        <i data-lucide="{{ $jenisIcon }}" class="w-4 h-4 mr-1"></i>
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
                <input type="hidden" name="status_kepegawaian" value="{{ old('status_kepegawaian', $pegawai->status_kepegawaian) }}">
                <input type="text" value="{{ $pegawai->status_kepegawaian }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                       readonly>
                <p class="text-xs text-gray-500 mt-1">
                    <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                    Status kepegawaian tidak dapat diubah
                </p>
            @else
                <div class="flex items-center gap-2">
                    @php
                        $statusIcon = 'user';
                        $statusClass = 'bg-gray-100 text-gray-700 border-gray-300';

                        if (str_contains($pegawai->status_kepegawaian ?? '', 'PNS')) {
                            $statusIcon = 'user-check';
                            $statusClass = 'bg-green-100 text-green-700 border-green-300';
                        } elseif (str_contains($pegawai->status_kepegawaian ?? '', 'PPPK')) {
                            $statusIcon = 'user-plus';
                            $statusClass = 'bg-blue-100 text-blue-700 border-blue-300';
                        } elseif (str_contains($pegawai->status_kepegawaian ?? '', 'Non ASN')) {
                            $statusIcon = 'user-x';
                            $statusClass = 'bg-orange-100 text-orange-700 border-orange-300';
                        }
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClass }}">
                        <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 mr-1"></i>
                        {{ $pegawai->status_kepegawaian ?? 'Belum diset' }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Pangkat --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                Pangkat/Golongan
            </label>
            @if($isEditing)
                @php
                    // Filter pangkat berdasarkan status kepegawaian
                    $filteredPangkats = collect($pangkats);
                    $statusKepegawaian = old('status_kepegawaian', $pegawai->status_kepegawaian);

                    if ($statusKepegawaian) {
                        if (str_contains($statusKepegawaian, 'PNS')) {
                            // PNS: hanya pangkat dengan status PNS dan memiliki hirarki
                            $filteredPangkats = $filteredPangkats
                                ->where('status_pangkat', 'PNS')
                                ->whereNotNull('hierarchy_level');
                        } elseif (str_contains($statusKepegawaian, 'PPPK')) {
                            // PPPK: hanya pangkat dengan status PPPK dan memiliki hirarki
                            $filteredPangkats = $filteredPangkats
                                ->where('status_pangkat', 'PPPK')
                                ->whereNotNull('hierarchy_level');
                        } else {
                            // Non-ASN: hanya pangkat dengan status Non-ASN atau tanpa hirarki
                            $filteredPangkats = $filteredPangkats
                                ->where('status_pangkat', 'Non-ASN')
                                ->whereNull('hierarchy_level');
                        }
                    }

                    // Urutkan berdasarkan hierarchy_level (ascending = dari bawah ke atas)
                    $filteredPangkats = $filteredPangkats->sortBy('hierarchy_level');
                @endphp

                <select name="pangkat_terakhir_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Pangkat --</option>
                    @foreach($filteredPangkats as $pangkat)
                        <option value="{{ $pangkat->id }}"
                                {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id) == $pangkat->id ? 'selected' : '' }}>
                            {{ $pangkat->pangkat }}
                        </option>
                    @endforeach
                </select>

                {{-- Info Status Pangkat --}}
                @if($statusKepegawaian)
                    <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1 text-blue-600"></i>
                        <span class="text-blue-700">
                            Menampilkan pangkat untuk status:
                            <strong>
                                @if(str_contains($statusKepegawaian, 'PNS'))
                                    PNS (dengan hirarki)
                                @elseif(str_contains($statusKepegawaian, 'PPPK'))
                                    PPPK (dengan hirarki)
                                @else
                                    Non-ASN (tanpa hirarki)
                                @endif
                            </strong>
                        </span>
                    </div>
                @endif
            @else
                {{-- Tampilan View Mode --}}
                <div class="space-y-2">
                    {{-- Badge Status Pangkat dan Level --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($pegawai->pangkat)
                            {{-- Status Pangkat Badge --}}
                            @if($pegawai->pangkat->status_pangkat)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $pegawai->pangkat->status_pangkat_badge_class ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                    <i data-lucide="{{ $pegawai->pangkat->status_icon ?? 'user' }}" class="w-3 h-3 mr-1"></i>
                                    {{ $pegawai->pangkat->status_pangkat }}
                                </span>
                            @endif

                            {{-- Hierarchy Level Badge (jika ada) --}}
                            @if($pegawai->pangkat->hierarchy_level)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pegawai->pangkat->hierarchy_badge_class ?? 'bg-gray-100 text-gray-800' }}">
                                    <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                                    Level {{ $pegawai->pangkat->hierarchy_level }}
                                </span>
                            @endif
                        @endif
                    </div>

                    {{-- Nama Pangkat --}}
                    <p class="text-gray-900 font-medium">
                        {{ $pegawai->pangkat?->pangkat ?? 'Belum diset' }}
                    </p>
                </div>
            @endif
        </div>

        {{-- TMT Pangkat --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1"></i>
                TMT Pangkat
            </label>
            @if($isEditing)
                <input type="date" name="tmt_pangkat"
                       value="{{ old('tmt_pangkat', $pegawai->tmt_pangkat ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ formatDate($pegawai->tmt_pangkat) }}</p>
            @endif
        </div>

        {{-- Jabatan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="briefcase" class="w-4 h-4 inline mr-1"></i>
                Jabatan
            </label>
            @if($isEditing)
                <select name="jabatan_terakhir_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}"
                                {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id) == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->jabatan }}
                            ({{ $jabatan->jenis_jabatan }})
                        </option>
                    @endforeach
                </select>
            @else
                <p class="text-gray-900 py-2 flex items-center gap-2">
                    @if($pegawai->jabatan)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $pegawai->jabatan->jenis_jabatan_badge_class }}">
                            {{ $pegawai->jabatan->jenis_jabatan }}
                        </span>
                    @endif
                    {{ $pegawai->jabatan?->jabatan ?? 'Belum diset' }}
                </p>
            @endif
        </div>

        {{-- TMT Jabatan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="calendar-check" class="w-4 h-4 inline mr-1"></i>
                TMT Jabatan
            </label>
            @if($isEditing)
                <input type="date" name="tmt_jabatan"
                       value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ formatDate($pegawai->tmt_jabatan) }}</p>
            @endif
        </div>

        {{-- Unit Kerja --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="building" class="w-4 h-4 inline mr-1"></i>
                Unit Kerja
            </label>
            @if($isEditing)
                <select name="unit_kerja_terakhir_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach($unitKerjas as $unit)
                        <option value="{{ $unit->id }}"
                                {{ old('unit_kerja_terakhir_id', $pegawai->unit_kerja_terakhir_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama }}
                        </option>
                    @endforeach
                </select>
            @else
                <p class="text-gray-900 py-2">{{ $pegawai->unitKerja?->nama ?? 'Belum diset' }}</p>
            @endif
        </div>

        {{-- TMT CPNS --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="user-plus" class="w-4 h-4 inline mr-1"></i>
                TMT CPNS
            </label>
            @if($isEditing)
                <input type="date" name="tmt_cpns"
                       value="{{ old('tmt_cpns', $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ formatDate($pegawai->tmt_cpns) }}</p>
            @endif
        </div>

        {{-- TMT PNS --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i data-lucide="user-check" class="w-4 h-4 inline mr-1"></i>
                TMT PNS
            </label>
            @if($isEditing)
                <input type="date" name="tmt_pns"
                       value="{{ old('tmt_pns', $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            @else
                <p class="text-gray-900 py-2">{{ formatDate($pegawai->tmt_pns) }}</p>
            @endif
        </div>

        {{-- Quick Link to PAK & SKP --}}
        <div class="md:col-span-2">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-100 rounded-full p-2">
                            <i data-lucide="clipboard-check" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-amber-800">Penilaian Kinerja & PAK</h4>
                            <p class="text-amber-700 text-sm">Kelola predikat kinerja, SKP, dan PAK konversi</p>
                        </div>
                    </div>
                    <button type="button"
                            @click="activeTab = 'pak_skp'"
                            class="px-3 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm flex items-center gap-1">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        Lihat Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Career Timeline (View Only) --}}
    @if(!$isEditing && ($pegawai->tmt_cpns || $pegawai->tmt_pns || $pegawai->tmt_pangkat || $pegawai->tmt_jabatan))
        <div class="mt-8 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center gap-2">
                <i data-lucide="timeline" class="w-5 h-5"></i>
                Timeline Karier
            </h3>
            <div class="space-y-4">
                @php
                    $timeline = collect([
                        ['date' => $pegawai->tmt_cpns, 'event' => 'Diangkat sebagai CPNS', 'icon' => 'user-plus'],
                        ['date' => $pegawai->tmt_pns, 'event' => 'Diangkat sebagai PNS', 'icon' => 'user-check'],
                        ['date' => $pegawai->tmt_pangkat, 'event' => 'Pangkat: ' . ($pegawai->pangkat?->pangkat ?? 'N/A'), 'icon' => 'award'],
                        ['date' => $pegawai->tmt_jabatan, 'event' => 'Jabatan: ' . ($pegawai->jabatan?->jabatan ?? 'N/A'), 'icon' => 'briefcase'],
                    ])->filter(fn($item) => $item['date'])->sortBy('date');
                @endphp

                @foreach($timeline as $index => $item)
                    <div class="flex items-start space-x-4 relative">
                        @if(!$loop->last)
                            <div class="absolute left-4 top-8 w-0.5 h-full bg-indigo-300"></div>
                        @endif
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 border-2 border-indigo-300 flex items-center justify-center">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 text-indigo-600"></i>
                        </div>
                        <div class="flex-1 min-w-0 pb-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-900">{{ $item['event'] }}</p>
                                <p class="text-xs text-indigo-600">{{ formatDate($item['date']) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

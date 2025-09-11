{{-- resources/views/backend/layouts/pegawai-unmul/profile/components/tabs/dosen-tab.blade.php --}}
<div x-show="activeTab === 'dosen' && jenisPegawai === 'Dosen'" x-transition>
    {{-- Header Info --}}
    <div class="mb-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 bg-purple-100 rounded-full p-2">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-purple-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-purple-800 text-sm">Informasi Khusus Dosen</h3>
                <p class="text-purple-700 text-xs mt-1">
                    Data khusus untuk dosen meliputi NUPTK, bidang kepakaran, mata kuliah, dan profil Sinta.
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Main Content --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center gap-2 mb-6">
                <div class="bg-purple-100 rounded-full p-2">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Data Kepegawaian Dosen</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NUPTK --}}
                <div>
                    <label class="block text-sm font-medium text-xl text-gray-700 mb-2">
                        <i data-lucide="credit-card" class="w-4 h-4 inline mr-1"></i>
                        NUPTK
                    </label>
                    @if($isEditing)
                        <input type="text" name="nuptk"
                                value="{{ old('nuptk', $pegawai->nuptk) }}"
                                placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @else
                        <div class="flex items-center gap-2">
                            @if($pegawai->nuptk)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-xl bg-blue-100 text-blue-700">
                                    <i data-lucide="credit-card" class="w-3 h-3 mr-1"></i>
                                    {{ $pegawai->nuptk }}
                                </span>
                            @else
                                <span class="text-gray-500">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->nuptk, 'Belum diisi') }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- URL Sinta --}}
                <div>
                    <label class="block text-sm font-medium text-xl text-gray-700 mb-2">
                        <i data-lucide="link" class="w-4 h-4 inline mr-1"></i>
                        URL Profil Sinta
                    </label>
                    @if($isEditing)
                        <input type="url" name="url_profil_sinta"
                                value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta) }}"
                                placeholder="https://sinta.kemdikbud.go.id/authors/detail?id=..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @else
                        <div class="flex items-center gap-2">
                            @if($pegawai->url_profil_sinta)
                                <a href="{{ $pegawai->url_profil_sinta }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                    <span>Lihat Profil Sinta</span>
                                </a>
                            @else
                                <span class="text-gray-500">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->url_profil_sinta, 'Belum diisi') }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Ranting Ilmu --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-xl text-gray-700 mb-2">
                        <i data-lucide="book-open" class="w-4 h-4 inline mr-1"></i>
                        Ranting Ilmu/Kepakaran
                    </label>
                    @if($isEditing)
                        <textarea name="ranting_ilmu_kepakaran" rows="3"
                                    placeholder="Tuliskan bidang kepakaran Anda"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran) }}</textarea>
                    @else
                        <div class="p-3 bg-gray-50 rounded-lg border">
                            @if($pegawai->ranting_ilmu_kepakaran)
                                <p class="text-gray-900 text-sm leading-relaxed">{{ $pegawai->ranting_ilmu_kepakaran }}</p>
                            @else
                                <p class="text-gray-500 text-sm italic">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->ranting_ilmu_kepakaran, 'Belum diisi') }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Mata Kuliah --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-xl text-gray-700 mb-2">
                        <i data-lucide="book" class="w-4 h-4 inline mr-1"></i>
                        Mata Kuliah Diampu
                    </label>
                    @if($isEditing)
                        <textarea name="mata_kuliah_diampu" rows="3"
                                    placeholder="Daftar mata kuliah yang diampu"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu) }}</textarea>
                    @else
                        <div class="p-3 bg-gray-50 rounded-lg border">
                            @if($pegawai->mata_kuliah_diampu)
                                <p class="text-gray-900 text-sm leading-relaxed">{{ $pegawai->mata_kuliah_diampu }}</p>
                            @else
                                <p class="text-gray-500 text-sm italic">{{ \App\Helpers\ProfileDisplayHelper::displayValue($pegawai->mata_kuliah_diampu, 'Belum diisi') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-purple-600 mt-0.5"></i>
                <div>
                    <h4 class="font-medium text-xl text-purple-800 text-sm">Informasi Penting</h4>
                    <ul class="text-purple-700 text-xs mt-1 space-y-1">
                        <li>• NUPTK diperlukan untuk validasi data dosen di sistem nasional</li>
                        <li>• Profil Sinta membantu meningkatkan visibilitas penelitian Anda</li>
                        <li>• Data kepakaran digunakan untuk penugasan dan penilaian kinerja</li>
                        <li>• Pastikan data mata kuliah sesuai dengan beban mengajar aktual</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- resources/views/backend/layouts/pegawai-unmul/profile/components/tabs/dokumen-tab.blade.php --}}
<div x-show="activeTab === 'dokumen'" x-transition>
    {{-- Header Info --}}
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 bg-blue-100 rounded-full p-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800 text-sm">Informasi Dokumen</h3>
                <p class="text-blue-700 text-xs mt-1">
                    @if($isEditing)
                        Upload dokumen pendukung dalam format PDF dengan ukuran maksimal 2MB per file.
                    @else
                        Dokumen yang telah Anda upload untuk keperluan kepegawaian.
                    @endif
                </p>
                @if($isEditing)
                    <div class="mt-2 flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1 text-green-700">
                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                            {{ count(array_filter($documentFields, fn($field, $key) => $pegawai->$key, ARRAY_FILTER_USE_BOTH)) }} Dokumen tersedia
                        </span>
                        <span class="flex items-center gap-1 text-orange-700">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            {{ count($documentFields) - count(array_filter($documentFields, fn($field, $key) => $pegawai->$key, ARRAY_FILTER_USE_BOTH)) }} Belum diunggah
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Document Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($documentFields as $field => $doc)
            <div class="group border rounded-lg hover:border-indigo-300 transition-all duration-200 {{ $pegawai->$field ? 'bg-green-50 border-green-200 hover:border-green-300' : 'bg-gray-50 hover:bg-gray-100' }}">
                {{-- Document Header --}}
                <div class="p-4 border-b {{ $pegawai->$field ? 'border-green-200' : 'border-gray-200' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $pegawai->$field ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400' }}">
                                <i data-lucide="{{ $doc['icon'] }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">{{ $doc['label'] }}</h4>
                                @if($pegawai->$field)
                                    <div class="flex items-center gap-1 text-xs text-green-600 mt-1">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        <span>File tersedia</span>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">Belum diunggah</p>
                                @endif
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="flex-shrink-0">
                            @if($pegawai->$field)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i data-lucide="file-check" class="w-3 h-3 mr-1"></i>
                                    Uploaded
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    <i data-lucide="file-x" class="w-3 h-3 mr-1"></i>
                                    Missing
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Document Actions --}}
                <div class="p-4">
                    @if($pegawai->$field)
                        {{-- View Document --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-xs text-gray-600">
                                <p>Dokumen telah diunggah</p>
                                <p class="text-gray-500">{{ $pegawai->updated_at->diffForHumans() ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>

                        {{-- FIXED: Route untuk pegawai menggunakan route profile --}}
                        <a href="{{ route('pegawai-unmul.profile.show-document', ['field' => $field]) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center w-full gap-2 px-3 py-2 text-sm text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 transition-colors"
                           onclick="handleDocumentAccess(event, this)">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat Dokumen
                        </a>
                    @endif

                    @if($isEditing)
                        {{-- Upload/Replace Section --}}
                        <div class="{{ $pegawai->$field ? 'mt-3 pt-3 border-t border-gray-200' : '' }}">
                            <label for="{{ $field }}"
                                class="block w-full text-center px-4 py-3 bg-white border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group-hover:border-indigo-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="upload" class="w-5 h-5 text-gray-400 group-hover:text-indigo-500"></i>
                                    <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-600">
                                        {{ $pegawai->{$field} ? 'Ganti File' : 'Upload File' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Klik untuk memilih file
                                    </span>
                                </div>
                            </label>

                            <input type="file"
                                name="{{ $field }}"
                                id="{{ $field }}"
                                class="hidden"
                                accept=".pdf"
                                data-max-size="2"
                                onchange="previewUploadedFile(this, 'preview-{{ $field }}')">

                            {{-- File Requirements --}}
                            <div class="mt-2 text-xs text-gray-500 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="file-type" class="w-3 h-3"></i>
                                        PDF Only
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="hard-drive" class="w-3 h-3"></i>
                                        Max: 1 MB
                                    </span>
                                </div>
                            </div>

                            {{-- Preview area untuk file yang baru diupload --}}
                            <div id="preview-{{ $field }}" class="hidden mt-3"></div>
                        </div>
                    @endif
                </div>

                {{-- Progress Indicator for Upload --}}
                @if($isEditing)
                    <div class="px-4 pb-4">
                        <div id="progress-{{ $field }}" class="hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 text-center">Mengupload...</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>



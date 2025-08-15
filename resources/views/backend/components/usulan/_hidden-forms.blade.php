{{-- Hidden Forms untuk berbagai aksi --}}

{{-- Form Kembalikan untuk Revisi --}}
<div id="returnForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kembalikan untuk Revisi</h3>
            <p class="text-sm text-gray-600 mb-4">
                Usulan akan dikembalikan ke pegawai untuk perbaikan.
            </p>
            
            <form action="{{ $formAction }}" method="POST" id="returnFormSubmit">
                @csrf
                <input type="hidden" name="action_type" value="return_to_pegawai">
                
                <div class="mb-4">
                    <label for="catatan_umum_return" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Perbaikan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="catatan_umum_return"
                        name="catatan_umum" 
                        rows="4" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Berikan catatan detail tentang hal yang perlu diperbaiki (minimal 10 karakter)"
                        required
                    ></textarea>
                    <div class="text-sm text-gray-500 mt-1">
                        <span id="charCount_return">0</span> / 2000 karakter
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideReturnForm()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Kembalikan untuk Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form Belum Direkomendasikan --}}
<div id="notRecommendedForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Belum Direkomendasikan</h3>
            <p class="text-sm text-gray-600 mb-4">
                Usulan tidak direkomendasikan. Pegawai tidak dapat submit lagi di periode ini.
            </p>
            
            <form action="{{ $formAction }}" method="POST" id="notRecommendedFormSubmit">
                @csrf
                <input type="hidden" name="action_type" value="not_recommended">
                
                <div class="mb-4">
                    <label for="catatan_umum_not_recommended" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Tidak Direkomendasikan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="catatan_umum_not_recommended"
                        name="catatan_umum" 
                        rows="4" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Berikan alasan mengapa usulan tidak direkomendasikan (minimal 10 karakter)"
                        required
                    ></textarea>
                    <div class="text-sm text-gray-500 mt-1">
                        <span id="charCount_not_recommended">0</span> / 2000 karakter
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideNotRecommendedForm()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Belum Direkomendasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form Send to Assessor Team --}}
<div id="sendToAssessorForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kirim Usulan ke Tim Penilai</h3>
            <p class="text-sm text-gray-600 mb-4">
                Pilih penilai untuk mengevaluasi usulan ini (minimal 1, maksimal 3 penilai).
            </p>
            
            <form action="{{ $formAction }}" method="POST" id="sendToAssessorFormSubmit">
                @csrf
                <input type="hidden" name="action_type" value="send_to_assessor_team">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Penilai <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @php
                            // Get all assessors (pegawai with Penilai Universitas role)
                            $assessors = \App\Models\BackendUnivUsulan\Pegawai::whereHas('roles', function($query) {
                                $query->where('name', 'Penilai Universitas');
                            })->orderBy('nama_lengkap')->get();
                        @endphp
                        
                        @foreach($assessors as $assessor)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="assessor_ids[]" 
                                       value="{{ $assessor->id }}"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       onchange="validateAssessorSelection()">
                                <span class="ml-2 text-sm text-gray-700">{{ $assessor->nama_lengkap }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        <span id="assessorCount">0</span> penilai dipilih (min: 1, max: 3)
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideSendToAssessorForm()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" id="submitAssessorBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled>
                        Kirim ke Tim Penilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form Send to Senate Team --}}
<div id="sendToSenateForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kirim ke Tim Senat</h3>
            <p class="text-sm text-gray-600 mb-4">
                Usulan akan dikirim ke Tim Senat untuk review final. Pastikan Tim Penilai sudah memberikan rekomendasi.
            </p>
            
            <form action="{{ $formAction }}" method="POST" id="sendToSenateFormSubmit">
                @csrf
                <input type="hidden" name="action_type" value="send_to_senate_team">
                
                <div class="mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Status Tim Penilai:</h4>
                        @if($usulan->isRecommendedByReviewer())
                            <div class="flex items-center text-green-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm">Tim Penilai telah memberikan rekomendasi</span>
                            </div>
                        @else
                            <div class="flex items-center text-red-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-sm">Tim Penilai belum memberikan rekomendasi</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideSendToSenateForm()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 {{ !$usulan->isRecommendedByReviewer() ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$usulan->isRecommendedByReviewer() ? 'disabled' : '' }}>
                        Kirim ke Tim Senat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

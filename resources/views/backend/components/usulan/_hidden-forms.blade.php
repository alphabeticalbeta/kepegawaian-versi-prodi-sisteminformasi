{{-- Hidden Forms untuk berbagai aksi --}}

{{-- Modal Daftar Penilai --}}
<div id="assessorListModal" class="hidden fixed inset-0 bg-black/45 overflow-y-auto h-full w-full z-50">
    <div class="relative top-16 mx-auto w-full max-w-lg p-4">
        <div class="bg-white rounded-xl shadow-xl ring-1 ring-black/5 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-700">
                <div class="flex items-start gap-3 text-white">
                    <div class="p-2 rounded-lg bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m10 0H7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold leading-tight">Daftar Penilai</h3>
                        <p class="text-xs text-white/80">Usulan ID: {{ $usulan->id }} • Total: {{ $usulan->penilais->count() }}</p>
                    </div>
                </div>
                <button type="button" onclick="hideAssessorListModal()" class="text-white/90 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-4">
                @php $assignedPenilais = $usulan->penilais; @endphp
                @if($assignedPenilais->isEmpty())
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.674-2.64"/></svg>
                        <p>Tidak ada penilai yang ditugaskan.</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @foreach($assignedPenilais as $penilai)
                            <div class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-800">{{ $penilai->nama_lengkap ?? ($penilai->name ?? 'Penilai') }}</span>
                                        @php $status = $penilai->pivot->status_penilaian ?? 'Belum Dinilai'; @endphp
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $status === 'Sesuai' ? 'bg-green-100 text-green-800' : ($status === 'Perlu Perbaikan' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">{{ $status }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-3">
                                        @php $hasil = $penilai->pivot->hasil_penilaian ?? '-'; @endphp
                                        <span>Hasil: <strong class="text-gray-700">{{ $hasil }}</strong></span>
                                        @php $tgl = $penilai->pivot->tanggal_penilaian ? \Carbon\Carbon::parse($penilai->pivot->tanggal_penilaian)->format('d M Y H:i') : '-'; @endphp
                                        <span>Tanggal: <strong class="text-gray-700">{{ $tgl }}</strong></span>
                                    </div>
                                    @if(!empty($penilai->pivot->catatan_penilaian))
                                        <p class="text-xs text-gray-600 mt-2">Catatan: {{ $penilai->pivot->catatan_penilaian }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 text-right">
                <button type="button" onclick="hideAssessorListModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Tutup</button>
            </div>
        </div>
    </div>
    <script>
    function showAssessorListModal() { document.getElementById('assessorListModal').classList.remove('hidden'); }
    function hideAssessorListModal() { document.getElementById('assessorListModal').classList.add('hidden'); }
    </script>
</div>

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
<div id="sendToAssessorForm" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
    <div class="relative top-16 mx-auto w-full max-w-lg p-4">
        <div class="bg-white rounded-xl shadow-2xl ring-1 ring-black/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-700">
                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-white/20 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 10-6 0 3 3 0 006 0zm6 0a3 3 0 11-6 0 3 3 0 016 0zM9 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Kirim Usulan ke Tim Penilai</h3>
                        <p class="text-sm text-blue-100">Pilih 1–3 penilai untuk mengevaluasi usulan ini</p>
                    </div>
                </div>
                <button type="button" onclick="hideSendToAssessorForm()" class="p-2 rounded-full hover:bg-white/10 text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 pt-4 pb-2">
                <div class="relative mb-4">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="assessor-filter" type="text" placeholder="Cari penilai berdasarkan nama..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm" oninput="filterAssessorList()">
                </div>
                <p class="text-sm text-gray-600 mb-3">Pilih penilai untuk mengevaluasi usulan ini (minimal 1, maksimal 3 penilai).</p>

            <form action="{{ $formAction }}" method="POST" id="sendToAssessorFormSubmit">
                @csrf
                <input type="hidden" name="action_type" value="send_to_assessor_team">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Pilih Penilai <span class="text-red-500">*</span>
                    </label>
                    <div id="assessor-list" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50/50">
                        @php
                            // Get all assessors (pegawai with Penilai Universitas role)
                            $assessors = \App\Models\KepegawaianUniversitas\Pegawai::whereHas('roles', function($query) {
                                $query->where('name', 'Penilai Universitas');
                            })->orderBy('nama_lengkap')->get();
                        @endphp

                        @foreach($assessors as $assessor)
                            @php
                                $isAssigned = in_array($assessor->id, $assignedPenilaiIds ?? []);
                                $borderClass = $isAssigned ? 'border-green-300 bg-green-50' : 'border-gray-200';
                                $hoverClass = $isAssigned ? 'hover:border-green-400' : 'hover:border-blue-300';
                            @endphp
                            <label class="assessor-item flex items-center gap-3 p-3 {{ $borderClass }} rounded-lg hover:bg-white {{ $hoverClass }} hover:shadow-sm transition-all duration-200 group cursor-pointer" data-name="{{ \Illuminate\Support\Str::lower($assessor->nama_lengkap) }}">
                                <input type="checkbox"
                                       name="assessor_ids[]"
                                       value="{{ $assessor->id }}"
                                       class="peer w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       onchange="validateAssessorSelection()"
                                       {{ $isAssigned ? 'checked' : '' }}>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center group-hover:from-blue-200 group-hover:to-indigo-200 transition-colors">
                                    @if($isAssigned)
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-800 peer-checked:text-blue-700 group-hover:text-blue-700 transition-colors">{{ $assessor->nama_lengkap }}</span>
                                        @if($isAssigned)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Sudah Ditugaskan
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Penilai Universitas</p>
                                </div>
                            </label>
                        @endforeach
                        <div id="assessor-empty" class="hidden text-sm text-gray-500 py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.674-2.64"/></svg>
                            <p>Tidak ada penilai yang cocok dengan pencarian Anda.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-blue-800">
                                <span id="assessorCount">0</span> penilai dipilih
                            </span>
                        </div>
                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Min: 1, Max: 3</span>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="hideSendToAssessorForm()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium shadow-sm">
                        Batal
                    </button>
                    <button type="submit" id="submitAssessorBtn" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none" disabled>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Kirim ke Tim Penilai
                        </span>
                    </button>
                </div>
            </form>
            </div>
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

<script>
// Function to show send to assessor form with proper initialization
function showSendToAssessorForm() {
    document.getElementById('sendToAssessorForm').classList.remove('hidden');

    // Initialize assessor count based on pre-checked checkboxes
    setTimeout(() => {
        validateAssessorSelection();
    }, 100);
}

// Enhanced validateAssessorSelection function
function validateAssessorSelection() {
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
    const count = checkboxes.length;
    const submitBtn = document.getElementById('submitAssessorBtn');
    const countSpan = document.getElementById('assessorCount');

    // Update counter
    if (countSpan) {
        countSpan.textContent = count;
    }

    // Enable/disable submit button based on selection
    if (submitBtn) {
        if (count >= 1 && count <= 3) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('disabled:bg-gray-300', 'disabled:to-gray-400');
            submitBtn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled:bg-gray-300', 'disabled:to-gray-400');
            submitBtn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600');
        }
    }
}

// Function to hide send to assessor form
function hideSendToAssessorForm() {
    document.getElementById('sendToAssessorForm').classList.add('hidden');
}

// Additional modal functions for other forms
function showReturnForm() {
    document.getElementById('returnForm').classList.remove('hidden');
}

function hideReturnForm() {
    document.getElementById('returnForm').classList.add('hidden');
}

function showNotRecommendedForm() {
    document.getElementById('notRecommendedForm').classList.remove('hidden');
}

function hideNotRecommendedForm() {
    document.getElementById('notRecommendedForm').classList.add('hidden');
}

function showSendToSenateForm() {
    document.getElementById('sendToSenateForm').classList.remove('hidden');
}

function hideSendToSenateForm() {
    document.getElementById('sendToSenateForm').classList.add('hidden');
}

// Filter function for assessor list
function filterAssessorList() {
    const filter = document.getElementById('assessor-filter').value.toLowerCase();
    const items = document.querySelectorAll('.assessor-item');
    const emptyMessage = document.getElementById('assessor-empty');
    let visibleCount = 0;

    items.forEach(item => {
        const name = item.getAttribute('data-name') || '';
        if (name.includes(filter)) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Show/hide empty message
    if (visibleCount === 0 && filter.length > 0) {
        emptyMessage.classList.remove('hidden');
    } else {
        emptyMessage.classList.add('hidden');
    }
}

// Enhanced form submission with better UI feedback
document.addEventListener('DOMContentLoaded', function() {
    // Handle Send to Assessor form submission with AJAX
    const sendToAssessorForm = document.getElementById('sendToAssessorFormSubmit');
    if (sendToAssessorForm) {
        sendToAssessorForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
            const submitBtn = document.getElementById('submitAssessorBtn');

            // Validation
            if (checkboxes.length < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Penilai',
                    text: 'Minimal harus memilih 1 penilai.',
                    confirmButtonColor: '#3B82F6'
                });
                return false;
            }

            if (checkboxes.length > 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Terlalu Banyak Penilai',
                    text: 'Maksimal hanya boleh memilih 3 penilai.',
                    confirmButtonColor: '#3B82F6'
                });
                return false;
            }

            // Get selected assessor names
            const assessorNames = Array.from(checkboxes).map(cb => {
                const label = cb.closest('label').querySelector('span');
                return label ? label.textContent.trim() : 'Unknown';
            });

            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Pengiriman',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Apakah Anda yakin ingin mengirim usulan ini ke Tim Penilai?</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h4 class="font-medium text-blue-800 mb-2">Penilai yang dipilih:</h4>
                            <ul class="text-sm text-blue-700">
                                ${assessorNames.map(name => `<li class="flex items-center"><span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>${name}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3B82F6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Kirim ke Penilai',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Disable submit button
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Mengirim...
                        </span>
                    `;

                    // Submit form via fetch
                    const formData = new FormData(sendToAssessorForm);

                    return fetch(sendToAssessorForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        throw error;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kirim ke Tim Penilai
                    </span>
                `;

                if (result.isConfirmed) {
                    const response = result.value;

                    if (response && response.success) {
                        // Success response
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                                <div class="text-center">
                                    <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700">${response.message || 'Usulan berhasil dikirim ke Tim Penilai.'}</p>
                                    <div class="mt-4 text-sm text-gray-600">
                                        <p>Status usulan telah diperbarui dan penilai akan segera menerima notifikasi.</p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            confirmButtonText: 'Tutup',
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            // Hide modal and refresh page
                            hideSendToAssessorForm();

                            // Add a small delay before reload for better UX
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        });
                    } else {
                        // Error response
                        Swal.fire({
                            title: 'Gagal!',
                            text: response?.message || 'Terjadi kesalahan saat mengirim usulan ke Tim Penilai.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                }
            });
        });
    }

    // Handle other form submissions with auto-reload on success
    const forms = [
        { id: 'returnFormSubmit', name: 'Return Form' },
        { id: 'notRecommendedFormSubmit', name: 'Not Recommended Form' },
        { id: 'sendToSenateFormSubmit', name: 'Send to Senate Form' }
    ];

    forms.forEach(formConfig => {
        const form = document.getElementById(formConfig.id);
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');

                if (submitBtn) {
                    // Show loading state
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Memproses...
                        </span>
                    `;

                    // Add success handler after form submission
                    setTimeout(() => {
                        // Check if form submission was successful (no validation errors)
                        const hasErrors = document.querySelector('.alert-danger, .error, .invalid-feedback');

                        if (!hasErrors) {
                            // Show success message and reload
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data berhasil disimpan.',
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                                confirmButtonText: 'Tutup',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            // Restore button if there are errors
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }, 1000);
                }
            });
        }
    });

    // Global success handler for any successful form submission
    window.addEventListener('beforeunload', function(e) {
        // Check if there's a success message in session storage or URL params
        const urlParams = new URLSearchParams(window.location.search);
        const hasSuccess = urlParams.get('success') || sessionStorage.getItem('form_success');

        if (hasSuccess) {
            sessionStorage.removeItem('form_success');
        }
    });

    // Check for success messages on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check for Laravel flash messages
        const successAlert = document.querySelector('.alert-success, .bg-green-100');
        if (successAlert) {
            const message = successAlert.textContent.trim();

            // Show SweetAlert for success
            Swal.fire({
                title: 'Berhasil!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#10B981',
                confirmButtonText: 'Tutup',
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
});
</script>

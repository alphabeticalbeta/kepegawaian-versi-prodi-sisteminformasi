@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Detail Usulan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- =========================
         FORM VALIDASI ADMIN UNIV
       ========================= --}}
    <form
        action="{{ route('backend.kepegawaian-universitas.pusat-usulan.process', $usulan->id) }}"
        method="POST"
        id="validationForm"
        enctype="multipart/form-data"
        class="mt-8 space-y-8"
    >
        @csrf

        {{-- Header Info Card --}}
        @include('backend.components.usulan._header', [
            'usulan' => $usulan
        ])

        {{-- Validation Sections (field dinamis per kategori) --}}
        @if(isset($validationFields) && count($validationFields) > 0)
            @foreach($validationFields as $category => $fields)
                @include('backend.components.usulan._validation-section', [
                    'category' => $category,
                    'fields'   => $fields,
                    'usulan'   => $usulan,
                    'canEdit'  => $canEdit ?? false,
                ])
            @endforeach
        @endif

        {{-- Tombol aksi (gunakan action_type) --}}
        @include('backend.components.usulan._action-buttons', [
            'usulan' => $usulan,
            'canEdit' => $canEdit ?? false
        ])

        {{-- Hidden Forms untuk modal --}}
        @include('backend.components.usulan._hidden-forms', [
            'usulan' => $usulan,
            'formAction' => route('backend.kepegawaian-universitas.pusat-usulan.process', $usulan->id)
        ])

        {{-- Shared: Riwayat Perubahan --}}
        @include('backend.components.usulan._riwayat_log', ['usulan' => $usulan])
    </form>

</div>
@endsection

{{-- Script validasi/submit --}}
@push('scripts')
<script>
        // XSS Protection Function
        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

// =====================================
// FORM DISPLAY/HIDE FUNCTIONS
// =====================================

// Return Form Functions
function showReturnForm() {
    const form = document.getElementById('returnForm');
    const textarea = document.getElementById('catatan_umum_return');

    if (!form) {
        return;
    }

    form.classList.remove('hidden');
    if (textarea) {
        textarea.focus();
    }
}

function hideReturnForm() {
    const form = document.getElementById('returnForm');
    const textarea = document.getElementById('catatan_umum_return');
    const charCount = document.getElementById('charCount_return');

    if (!form) {
        return;
    }

    form.classList.add('hidden');
    if (textarea) {
        textarea.value = '';
    }
    if (charCount) {
        charCount.textContent = '0';
    }
}

// Not Recommended Form Functions
function showNotRecommendedForm() {
    const form = document.getElementById('notRecommendedForm');
    const textarea = document.getElementById('catatan_umum_not_recommended');

    if (!form) {
        return;
    }

    form.classList.remove('hidden');
    if (textarea) {
        textarea.focus();
    }
}

function hideNotRecommendedForm() {
    const form = document.getElementById('notRecommendedForm');
    const textarea = document.getElementById('catatan_umum_not_recommended');
    const charCount = document.getElementById('charCount_not_recommended');

    if (!form) {
        return;
    }

    form.classList.add('hidden');
    if (textarea) {
        textarea.value = '';
    }
    if (charCount) {
        charCount.textContent = '0';
    }
}

// Send to Assessor Team Form Functions
function showSendToAssessorForm() {
    const form = document.getElementById('sendToAssessorForm');

    if (!form) {
        return;
    }

    form.classList.remove('hidden');
}

function hideSendToAssessorForm() {
    const form = document.getElementById('sendToAssessorForm');

    if (!form) {
        return;
    }

    form.classList.add('hidden');

    // Reset checkboxes
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    validateAssessorSelection();
}

// Send to Senate Team Form Functions
function showSendToSenateForm() {
    const form = document.getElementById('sendToSenateForm');

    if (!form) {
        return;
    }

    form.classList.remove('hidden');
}

function hideSendToSenateForm() {
    const form = document.getElementById('sendToSenateForm');

    if (!form) {
        return;
    }

    form.classList.add('hidden');
}

// =====================================
// VALIDATION FUNCTIONS
// =====================================

function validateAssessorSelection() {
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
    const count = checkboxes.length;
    const submitBtn = document.getElementById('submitAssessorBtn');
    const countDisplay = document.getElementById('assessorCount');

    if (countDisplay) {
        countDisplay.textContent = count;
    }

    if (submitBtn) {
        if (count >= 1 && count <= 3) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        }
    }
}

// Filter assessor list by name
function filterAssessorList() {
    const input = document.getElementById('assessor-filter');
    const term = (input?.value || '').toLowerCase();
    const items = document.querySelectorAll('#assessor-list .assessor-item');
    let visible = 0;
    items.forEach(el => {
        const name = el.getAttribute('data-name') || '';
        const match = name.includes(term);
        el.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const empty = document.getElementById('assessor-empty');
    if (empty) empty.classList.toggle('hidden', visible !== 0);
}

// =====================================
// FORM SUBMISSION HANDLERS
// =====================================

document.addEventListener('DOMContentLoaded', function() {

    // Return Form Submission
    const returnForm = document.getElementById('returnFormSubmit');
    if (returnForm) {
        returnForm.addEventListener('submit', function(e) {
            const textarea = document.getElementById('catatan_umum_return');
            if (!textarea) {
                return;
            }

            const value = textarea.value.trim();

            if (value.length < 10) {
                e.preventDefault();
                alert('Catatan perbaikan harus minimal 10 karakter.');
                textarea.focus();
                return false;
            }

            if (value.length > 2000) {
                e.preventDefault();
                alert('Catatan perbaikan maksimal 2000 karakter.');
                textarea.focus();
                return false;
            }

            return confirm('Apakah Anda yakin ingin mengembalikan usulan ini ke pegawai untuk perbaikan?');
        });
    } else {
    }

    // Not Recommended Form Submission
    const notRecommendedForm = document.getElementById('notRecommendedFormSubmit');
    if (notRecommendedForm) {
        notRecommendedForm.addEventListener('submit', function(e) {
            const textarea = document.getElementById('catatan_umum_not_recommended');
            if (!textarea) {
                return;
            }

            const value = textarea.value.trim();

            if (value.length < 10) {
                e.preventDefault();
                alert('Alasan tidak direkomendasikan harus minimal 10 karakter.');
                textarea.focus();
                return false;
            }

            if (value.length > 2000) {
                e.preventDefault();
                alert('Alasan tidak direkomendasikan maksimal 2000 karakter.');
                textarea.focus();
                return false;
            }

            return confirm('Apakah Anda yakin ingin menandai usulan ini sebagai tidak direkomendasikan? Pegawai tidak dapat submit lagi di periode ini.');
        });
    } else {
    }

    // Send to Assessor Team Form Submission
    const sendToAssessorForm = document.getElementById('sendToAssessorFormSubmit');
    if (sendToAssessorForm) {
        sendToAssessorForm.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');

            if (checkboxes.length < 1) {
                e.preventDefault();
                alert('Pilih minimal 1 penilai.');
                return false;
            }

            if (checkboxes.length > 3) {
                e.preventDefault();
                alert('Pilih maksimal 3 penilai.');
                return false;
            }

            const assessorNames = Array.from(checkboxes).map(cb => {
                return cb.nextElementSibling ? cb.nextElementSibling.textContent : 'Unknown';
            }).join(', ');

            return confirm(`Apakah Anda yakin ingin mengirim usulan ini ke Tim Penilai?\n\nPenilai yang dipilih:\n${escapeHtml(assessorNames)}`);
        });
    } else {
    }


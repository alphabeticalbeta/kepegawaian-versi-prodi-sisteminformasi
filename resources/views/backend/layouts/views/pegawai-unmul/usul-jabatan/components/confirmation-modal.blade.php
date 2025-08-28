{{-- Confirmation Modal Component --}}
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div id="modalIcon" class="w-8 h-8 rounded-full flex items-center justify-center mr-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Konfirmasi Aksi</h3>
                </div>
                <button onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-4">
            <p id="modalMessage" class="text-gray-700 mb-4">
                Apakah Anda yakin ingin melanjutkan aksi ini?
            </p>
            
            {{-- Additional Info --}}
            <div id="modalAdditionalInfo" class="bg-gray-50 rounded-lg p-3 mb-4 hidden">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-gray-600">
                        <p id="additionalInfoText"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeConfirmationModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Batal
            </button>
            <button id="confirmButton" 
                    class="px-4 py-2 text-white rounded-lg transition-colors">
                Konfirmasi
            </button>
        </div>
    </div>
</div>

<script>
// Modal state
let currentAction = null;
let currentForm = null;

// Show confirmation modal
function showConfirmationModal(action, form, options = {}) {
    console.log('showConfirmationModal called');
    console.log('action:', action);
    console.log('form:', form);
    
    currentAction = action;
    currentForm = form;
    
    const modal = document.getElementById('confirmationModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalAdditionalInfo = document.getElementById('modalAdditionalInfo');
    const additionalInfoText = document.getElementById('additionalInfoText');
    const confirmButton = document.getElementById('confirmButton');
    
    // Default configurations
    const configs = {
        'save_draft': {
            icon: 'save',
            iconBg: 'bg-green-500',
            title: 'Simpan Usulan',
            message: 'Apakah Anda yakin ingin menyimpan usulan ini?',
            buttonText: 'Simpan',
            buttonClass: 'bg-green-600 hover:bg-green-700',
            additionalInfo: 'Usulan akan disimpan sebagai draft dan dapat diedit kembali.'
        },
        'submit_perbaikan_fakultas': {
            icon: 'send',
            iconBg: 'bg-amber-500',
            title: 'Kirim Perbaikan ke Admin Fakultas',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Admin Fakultas?',
            buttonText: 'Kirim',
            buttonClass: 'bg-amber-600 hover:bg-amber-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim kembali ke Admin Fakultas untuk validasi ulang.'
        },
        'submit_perbaikan_university': {
            icon: 'send',
            iconBg: 'bg-blue-500',
            title: 'Kirim Perbaikan ke Universitas',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Universitas?',
            buttonText: 'Kirim',
            buttonClass: 'bg-blue-600 hover:bg-blue-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Universitas untuk proses selanjutnya.'
        },
        'submit_perbaikan_penilai': {
            icon: 'send',
            iconBg: 'bg-purple-500',
            title: 'Kirim Perbaikan ke Penilai',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Penilai Universitas?',
            buttonText: 'Kirim',
            buttonClass: 'bg-purple-600 hover:bg-purple-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Penilai Universitas untuk penilaian ulang.'
        },
        'submit_perbaikan_tim_sister': {
            icon: 'send',
            iconBg: 'bg-orange-500',
            title: 'Kirim Perbaikan ke Tim Sister',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Tim Sister?',
            buttonText: 'Kirim',
            buttonClass: 'bg-orange-600 hover:bg-orange-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Tim Sister untuk verifikasi ulang.'
        },
        'submit_to_fakultas': {
            icon: 'send',
            iconBg: 'bg-indigo-500',
            title: 'Kirim ke Admin Fakultas',
            message: 'Apakah Anda yakin ingin mengirim usulan ke Admin Fakultas?',
            buttonText: 'Kirim',
            buttonClass: 'bg-indigo-600 hover:bg-indigo-700',
            additionalInfo: 'Usulan akan dikirim ke Admin Fakultas untuk validasi dan persetujuan.'
        }
    };
    
    const config = configs[action] || {
        icon: 'alert-triangle',
        iconBg: 'bg-gray-500',
        title: 'Konfirmasi Aksi',
        message: 'Apakah Anda yakin ingin melanjutkan aksi ini?',
        buttonText: 'Konfirmasi',
        buttonClass: 'bg-gray-600 hover:bg-gray-700',
        additionalInfo: ''
    };
    
    // Update modal content
    modalIcon.className = `w-8 h-8 rounded-full flex items-center justify-center mr-3 ${config.iconBg}`;
    modalIcon.innerHTML = `<i data-lucide="${config.icon}" class="w-5 h-5 text-white"></i>`;
    modalTitle.textContent = config.title;
    modalMessage.textContent = config.message;
    confirmButton.textContent = config.buttonText;
    confirmButton.className = `px-4 py-2 text-white rounded-lg transition-colors ${config.buttonClass}`;
    
    // Show/hide additional info
    if (config.additionalInfo) {
        additionalInfoText.textContent = config.additionalInfo;
        modalAdditionalInfo.classList.remove('hidden');
    } else {
        modalAdditionalInfo.classList.add('hidden');
    }
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Close confirmation modal
function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('hidden');
    currentAction = null;
    currentForm = null;
}

// Confirm action
function confirmAction() {
    console.log('confirmAction called');
    console.log('currentAction:', currentAction);
    console.log('currentForm:', currentForm);
    
    if (currentForm && currentAction) {
        // Create hidden input for action
        let actionInput = currentForm.querySelector('input[name="action"]');
        if (!actionInput) {
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            currentForm.appendChild(actionInput);
        }
        actionInput.value = currentAction;
        
        console.log('Action input created/updated:', actionInput.value);
        console.log('Form action:', currentForm.action);
        console.log('Form method:', currentForm.method);
        
        // Submit form
        console.log('Submitting form...');
        currentForm.submit();
    } else {
        console.error('Missing currentForm or currentAction');
    }
    
    closeConfirmationModal();
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmationModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeConfirmationModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeConfirmationModal();
        }
    });
    
    // Set confirm button action
    document.getElementById('confirmButton').addEventListener('click', confirmAction);
});
</script>

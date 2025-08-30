{{-- Enhanced Confirmation Modal Component --}}
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        {{-- Modal Header with Gradient --}}
        <div class="relative overflow-hidden rounded-t-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-10"></div>
            <div class="relative px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div id="modalIcon" class="w-12 h-12 rounded-full flex items-center justify-center mr-4 shadow-lg">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-1">Konfirmasi Aksi</h3>
                            <p class="text-sm text-gray-500">Silakan konfirmasi aksi yang akan dilakukan</p>
                        </div>
                    </div>
                    <button onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="px-8 py-6">
            <div class="mb-6">
                <p id="modalMessage" class="text-gray-700 text-lg leading-relaxed">
                    Apakah Anda yakin ingin melanjutkan aksi ini?
                </p>
            </div>
            
            {{-- Enhanced Additional Info --}}
            <div id="modalAdditionalInfo" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6 hidden">
                <div class="flex items-start">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div class="text-sm text-gray-700">
                        <p id="additionalInfoText" class="leading-relaxed"></p>
                    </div>
                </div>
            </div>

            {{-- Action Preview --}}
            <div id="actionPreview" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                        <i data-lucide="file-text" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Usulan Jabatan</p>
                        <p class="text-xs text-gray-500">Status akan diperbarui setelah konfirmasi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enhanced Modal Footer --}}
        <div class="px-8 py-6 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            <div class="flex justify-end gap-4">
                <button onclick="closeConfirmationModal()" 
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                    <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                    Batal
                </button>
                <button id="confirmButton" 
                        class="px-6 py-3 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>
                    <span id="confirmButtonText">Konfirmasi</span>
                </button>
            </div>
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
    const modalContent = document.getElementById('modalContent');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalAdditionalInfo = document.getElementById('modalAdditionalInfo');
    const additionalInfoText = document.getElementById('additionalInfoText');
    const confirmButton = document.getElementById('confirmButton');
    const confirmButtonText = document.getElementById('confirmButtonText');
    
    // Default configurations
    const configs = {
        'save_draft': {
            icon: 'save',
            iconBg: 'bg-gradient-to-r from-green-500 to-emerald-500',
            title: 'Simpan Usulan',
            message: 'Apakah Anda yakin ingin menyimpan usulan ini?',
            buttonText: 'Simpan Draft',
            buttonClass: 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700',
            additionalInfo: 'Usulan akan disimpan sebagai draft dan dapat diedit kembali kapan saja.'
        },
        'submit_perbaikan_fakultas': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-amber-500 to-orange-500',
            title: 'Kirim Perbaikan ke Admin Fakultas',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Admin Fakultas?',
            buttonText: 'Kirim Perbaikan',
            buttonClass: 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim kembali ke Admin Fakultas untuk validasi ulang.'
        },
        'submit_perbaikan_university': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-blue-500 to-cyan-500',
            title: 'Kirim Perbaikan ke Universitas',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Universitas?',
            buttonText: 'Kirim ke Universitas',
            buttonClass: 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Universitas untuk proses selanjutnya.'
        },
        'submit_perbaikan_penilai': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-purple-500 to-violet-500',
            title: 'Kirim Perbaikan ke Penilai',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Penilai Universitas?',
            buttonText: 'Kirim ke Penilai',
            buttonClass: 'bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Penilai Universitas untuk penilaian ulang.'
        },
        'submit_perbaikan_tim_sister': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-orange-500 to-red-500',
            title: 'Kirim Perbaikan ke Tim Sister',
            message: 'Apakah Anda yakin ingin mengirim perbaikan usulan ke Tim Sister?',
            buttonText: 'Kirim ke Sister',
            buttonClass: 'bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700',
            additionalInfo: 'Usulan yang sudah diperbaiki akan dikirim ke Tim Sister untuk verifikasi final.'
        },
        'submit_to_fakultas': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-indigo-500 to-purple-500',
            title: 'Kirim ke Admin Fakultas',
            message: 'Apakah Anda yakin ingin mengirim usulan ke Admin Fakultas?',
            buttonText: 'Kirim ke Fakultas',
            buttonClass: 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700',
            additionalInfo: 'Usulan akan dikirim ke Admin Fakultas untuk validasi dan persetujuan awal.'
        },
        'submit_university': {
            icon: 'send',
            iconBg: 'bg-gradient-to-r from-blue-500 to-indigo-500',
            title: 'Kirim Usulan Ke Kepegawaian Universitas',
            message: 'Apakah Anda yakin ingin mengirim usulan ke Kepegawaian Universitas?',
            buttonText: 'Kirim Ke Kepegawaian Universitas',
            buttonClass: 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700',
            additionalInfo: 'Usulan akan dikirim ke Kepegawaian Universitas untuk proses selanjutnya.'
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
    modalIcon.className = `w-12 h-12 rounded-full flex items-center justify-center mr-4 shadow-lg ${config.iconBg}`;
    modalIcon.innerHTML = `<i data-lucide="${config.icon}" class="w-6 h-6 text-white"></i>`;
    modalTitle.textContent = config.title;
    modalMessage.textContent = config.message;
    confirmButtonText.textContent = config.buttonText;
    confirmButton.className = `px-6 py-3 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 ${config.buttonClass}`;
    
    // Show/hide additional info
    if (config.additionalInfo) {
        additionalInfoText.textContent = config.additionalInfo;
        modalAdditionalInfo.classList.remove('hidden');
    } else {
        modalAdditionalInfo.classList.add('hidden');
    }
    
    // Show modal with animation
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Close confirmation modal
function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    const modalContent = document.getElementById('modalContent');
    
    // Trigger close animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    // Hide modal after animation
    setTimeout(() => {
        modal.classList.add('hidden');
        currentAction = null;
        currentForm = null;
    }, 200);
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

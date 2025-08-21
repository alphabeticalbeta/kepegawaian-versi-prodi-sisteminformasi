// Documents JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // File upload preview function
    window.previewImage = function(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);

        if (file && preview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    };

    // File validation function
    window.validateFile = function(input, maxSize = 2) {
        const file = input.files[0];
        const maxSizeInBytes = maxSize * 1024 * 1024; // Convert MB to bytes

        if (file) {
            // Check file size
            if (file.size > maxSizeInBytes) {
                showToast(`File terlalu besar. Maksimal ${maxSize}MB.`, 'error');
                input.value = '';
                return false;
            }

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.', 'error');
                input.value = '';
                return false;
            }

            return true;
        }

        return false;
    };

    // Combined file upload and validation
    window.handleFileUpload = function(input, previewId, maxSize = 2) {
        if (validateFile(input, maxSize)) {
            previewImage(input, previewId);
            showToast('File berhasil dipilih.', 'success');
        }
    };

    // Remove file function
    window.removeFile = function(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input) {
            input.value = '';
        }

        if (preview) {
            preview.classList.add('hidden');
        }

        showToast('File berhasil dihapus.', 'info');
    };

    // Toast notification function (if not already defined)
    if (typeof window.showToast === 'undefined') {
        window.showToast = function(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        };
    }

    // Initialize file input event listeners
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const maxSize = this.getAttribute('data-max-size') || 2;

            if (previewId) {
                handleFileUpload(this, previewId, maxSize);
            }
        });
    });

    // Initialize remove file buttons
    const removeButtons = document.querySelectorAll('[data-remove-file]');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-input');
            const previewId = this.getAttribute('data-preview');

            if (inputId && previewId) {
                removeFile(inputId, previewId);
            }
        });
    });
});

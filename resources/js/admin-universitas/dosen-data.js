// Dosen Data JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced URL validation for Sinta profile with better feedback
    window.validateSintaUrl = function(input) {
        const url = input.value.trim();
        const urlPattern = /^https?:\/\/sinta\.kemdikbud\.go\.id\/.*$/;
        const feedbackElement = input.parentElement.querySelector('.url-feedback');

        if (!url) {
            if (feedbackElement) {
                feedbackElement.textContent = '';
                feedbackElement.className = 'url-feedback text-xs mt-1';
            }
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200', 'border-green-500', 'focus:border-green-500', 'focus:ring-green-200');
            return true; // Allow empty for optional fields
        }

        if (!urlPattern.test(url)) {
            if (feedbackElement) {
                feedbackElement.textContent = 'URL harus dari domain sinta.kemdikbud.go.id';
                feedbackElement.className = 'url-feedback text-xs mt-1 text-red-500';
            }
            input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            input.classList.remove('border-green-500', 'focus:border-green-500', 'focus:ring-green-200');
            return false;
        } else {
            if (feedbackElement) {
                feedbackElement.textContent = 'URL Sinta valid';
                feedbackElement.className = 'url-feedback text-xs mt-1 text-green-500';
            }
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            input.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-200');
            return true;
        }
    };

    // Enhanced character counter with better visual feedback
    window.updateCharacterCount = function(textarea, maxLength = 500) {
        const currentLength = textarea.value.length;
        const counter = textarea.parentElement.querySelector('.char-counter');
        const progressBar = textarea.parentElement.querySelector('.char-progress');

        if (counter) {
            counter.textContent = `${currentLength}/${maxLength}`;

            // Update progress bar
            if (progressBar) {
                const percentage = Math.min((currentLength / maxLength) * 100, 100);
                progressBar.style.width = `${percentage}%`;
            }

            // Color coding based on usage
            if (currentLength > maxLength) {
                counter.classList.add('text-red-500', 'font-semibold');
                counter.classList.remove('text-orange-500', 'text-slate-500');
                textarea.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                if (progressBar) {
                    progressBar.classList.add('bg-red-500');
                    progressBar.classList.remove('bg-orange-500', 'bg-green-500');
                }
            } else if (currentLength > maxLength * 0.8) {
                counter.classList.add('text-orange-500');
                counter.classList.remove('text-red-500', 'text-slate-500', 'font-semibold');
                textarea.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                if (progressBar) {
                    progressBar.classList.add('bg-orange-500');
                    progressBar.classList.remove('bg-red-500', 'bg-green-500');
                }
            } else {
                counter.classList.add('text-slate-500');
                counter.classList.remove('text-orange-500', 'text-red-500', 'font-semibold');
                textarea.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                if (progressBar) {
                    progressBar.classList.add('bg-green-500');
                    progressBar.classList.remove('bg-orange-500', 'bg-red-500');
                }
            }
        }
    };

    // Auto-save functionality for better user experience
    window.autoSaveDosenData = function() {
        const form = document.querySelector('form');
        if (!form) return;

        const formData = new FormData(form);
        const autoSaveIndicator = document.getElementById('auto-save-indicator');

        // Show auto-save indicator
        if (autoSaveIndicator) {
            autoSaveIndicator.classList.remove('hidden');
            autoSaveIndicator.textContent = 'Menyimpan...';
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (autoSaveIndicator) {
                if (data.success) {
                    autoSaveIndicator.textContent = 'Tersimpan otomatis';
                    autoSaveIndicator.classList.add('text-green-500');
                    setTimeout(() => {
                        autoSaveIndicator.classList.add('hidden');
                        autoSaveIndicator.classList.remove('text-green-500');
                    }, 2000);
                } else {
                    autoSaveIndicator.textContent = 'Gagal menyimpan';
                    autoSaveIndicator.classList.add('text-red-500');
                    setTimeout(() => {
                        autoSaveIndicator.classList.add('hidden');
                        autoSaveIndicator.classList.remove('text-red-500');
                    }, 3000);
                }
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
            if (autoSaveIndicator) {
                autoSaveIndicator.textContent = 'Gagal menyimpan';
                autoSaveIndicator.classList.add('text-red-500');
                setTimeout(() => {
                    autoSaveIndicator.classList.add('hidden');
                    autoSaveIndicator.classList.remove('text-red-500');
                }, 3000);
            }
        });
    };

    // Enhanced toast notification function
    if (typeof window.showToast === 'undefined') {
        window.showToast = function(message, type = 'info', duration = 3000) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-300 translate-x-full backdrop-blur-sm border ${
                type === 'success' ? 'bg-green-500 text-white border-green-400' :
                type === 'error' ? 'bg-red-500 text-white border-red-400' :
                type === 'warning' ? 'bg-yellow-500 text-white border-yellow-400' :
                'bg-blue-500 text-white border-blue-400'
            }`;

            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        ${type === 'success' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                          type === 'error' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>' :
                          type === 'warning' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>' :
                          '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, duration);
        };
    }

    // Initialize event listeners with enhanced functionality
    const sintaUrlInput = document.getElementById('url_profil_sinta');
    const mataKuliahTextarea = document.getElementById('mata_kuliah_diampu');
    const rantingIlmuTextarea = document.getElementById('ranting_ilmu_kepakaran');

    // Add feedback elements for URL validation
    if (sintaUrlInput) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'url-feedback text-xs mt-1';
        sintaUrlInput.parentElement.appendChild(feedbackDiv);

        sintaUrlInput.addEventListener('blur', function() {
            validateSintaUrl(this);
        });

        sintaUrlInput.addEventListener('input', function() {
            if (this.classList.contains('border-red-500')) {
                this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            }
        });

        // Auto-save on URL change
        let urlSaveTimeout;
        sintaUrlInput.addEventListener('input', function() {
            clearTimeout(urlSaveTimeout);
            urlSaveTimeout = setTimeout(() => {
                if (validateSintaUrl(this)) {
                    autoSaveDosenData();
                }
            }, 2000);
        });
    }

    // Enhanced character counter with progress bar
    if (mataKuliahTextarea) {
        const counter = document.createElement('div');
        counter.className = 'char-counter text-xs text-slate-500 mt-1 text-right';
        counter.textContent = '0/1000';

        const progressBar = document.createElement('div');
        progressBar.className = 'char-progress h-1 bg-green-500 rounded-full mt-1 transition-all duration-300';
        progressBar.style.width = '0%';

        mataKuliahTextarea.parentElement.appendChild(counter);
        mataKuliahTextarea.parentElement.appendChild(progressBar);

        mataKuliahTextarea.addEventListener('input', function() {
            updateCharacterCount(this, 1000);
        });

        // Auto-save on content change
        let mataKuliahSaveTimeout;
        mataKuliahTextarea.addEventListener('input', function() {
            clearTimeout(mataKuliahSaveTimeout);
            mataKuliahSaveTimeout = setTimeout(() => {
                autoSaveDosenData();
            }, 3000);
        });
    }

    if (rantingIlmuTextarea) {
        const counter = document.createElement('div');
        counter.className = 'char-counter text-xs text-slate-500 mt-1 text-right';
        counter.textContent = '0/500';

        const progressBar = document.createElement('div');
        progressBar.className = 'char-progress h-1 bg-green-500 rounded-full mt-1 transition-all duration-300';
        progressBar.style.width = '0%';

        rantingIlmuTextarea.parentElement.appendChild(counter);
        rantingIlmuTextarea.parentElement.appendChild(progressBar);

        rantingIlmuTextarea.addEventListener('input', function() {
            updateCharacterCount(this, 500);
        });

        // Auto-save on content change
        let rantingIlmuSaveTimeout;
        rantingIlmuTextarea.addEventListener('input', function() {
            clearTimeout(rantingIlmuSaveTimeout);
            rantingIlmuSaveTimeout = setTimeout(() => {
                autoSaveDosenData();
            }, 3000);
        });
    }

    // Enhanced form validation before submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errorMessages = [];

            // Validate Sinta URL if present
            if (sintaUrlInput && sintaUrlInput.value) {
                if (!validateSintaUrl(sintaUrlInput)) {
                    isValid = false;
                    errorMessages.push('URL Profil Sinta tidak valid');
                }
            }

            // Validate required fields for Dosen
            const jenisPegawai = document.querySelector('select[name="jenis_pegawai"]')?.value;
            if (jenisPegawai === 'Dosen') {
                if (mataKuliahTextarea && !mataKuliahTextarea.value.trim()) {
                    mataKuliahTextarea.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    isValid = false;
                    errorMessages.push('Mata Kuliah yang Diampu wajib diisi untuk Dosen');
                } else if (mataKuliahTextarea) {
                    mataKuliahTextarea.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                }

                if (rantingIlmuTextarea && !rantingIlmuTextarea.value.trim()) {
                    rantingIlmuTextarea.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    isValid = false;
                    errorMessages.push('Ranting Ilmu/Kepakaran wajib diisi untuk Dosen');
                } else if (rantingIlmuTextarea) {
                    rantingIlmuTextarea.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                }
            }

            if (!isValid) {
                e.preventDefault();
                showToast(errorMessages.join(', '), 'error');

                // Scroll to first error
                const firstError = document.querySelector('.border-red-500');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // Add auto-save indicator to the form
    const autoSaveIndicator = document.createElement('div');
    autoSaveIndicator.id = 'auto-save-indicator';
    autoSaveIndicator.className = 'hidden text-xs text-slate-500 mt-2 text-center';
    autoSaveIndicator.textContent = 'Tersimpan otomatis';

    const formContainer = form?.parentElement;
    if (formContainer) {
        formContainer.appendChild(autoSaveIndicator);
    }

    // Initialize character counts on page load
    if (mataKuliahTextarea) {
        updateCharacterCount(mataKuliahTextarea, 1000);
    }
    if (rantingIlmuTextarea) {
        updateCharacterCount(rantingIlmuTextarea, 500);
    }
});


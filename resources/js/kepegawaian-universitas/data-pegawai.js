// Data Pegawai JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {

    // Initialize global data
    window.dataPegawaiData = {
        activeTab: 'personal',
        totalTabs: 4,
        progress: 0,
        jenisPegawai: document.querySelector('#jenis_pegawai')?.value || '',
        formData: {}
    };

    // Tab switching functionality
    window.switchTab = function(tabName) {
        const currentTab = window.dataPegawaiData.activeTab;

        // Validate current tab before switching
        if (!validateCurrentTab(currentTab)) {
            return;
        }

        // Update active tab
        window.dataPegawaiData.activeTab = tabName;

        // Update Alpine.js data if available
        if (window.Alpine) {
            const component = document.querySelector('[x-data]').__x.$data;
            component.activeTab = tabName;
            component.updateProgress();
        }

        // Show success message
        showToast(`Berpindah ke tab ${getTabDisplayName(tabName)}`, 'success');
    };

    // Validate current tab
    function validateCurrentTab(tabName) {
        const requiredFields = {
            'personal': ['nama_lengkap', 'gelar_belakang'],
            'employment': ['jenis_pegawai', 'status_kepegawaian', 'pangkat_terakhir_id'],
            'dosen': ['mata_kuliah_diampu', 'ranting_ilmu_kepakaran'],
            'documents': []
        };

        const fields = requiredFields[tabName] || [];
        let isValid = true;
        let errorMessage = '';

        fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
                errorMessage = `Mohon lengkapi field ${fieldName.replace(/_/g, ' ')}`;
            } else if (field) {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            showToast(errorMessage, 'error');
        }

        return isValid;
    }

    // Get tab display name
    function getTabDisplayName(tabName) {
        const names = {
            'personal': 'Data Pribadi',
            'employment': 'Data Kepegawaian',
            'dosen': 'Data Dosen',
            'documents': 'Dokumen'
        };
        return names[tabName] || tabName;
    }

    // Update progress
    window.updateProgress = function() {
        const tabs = ['personal', 'employment', 'dosen', 'documents'];
        const activeIndex = tabs.indexOf(window.dataPegawaiData.activeTab);

        // Calculate progress based on filled fields
        let filledFields = 0;
        let totalFields = 0;

        // Count required fields across all tabs
        tabs.forEach(tab => {
            const requiredFields = {
                'personal': ['nama_lengkap', 'gelar_belakang'],
                'employment': ['jenis_pegawai', 'status_kepegawaian', 'pangkat_terakhir_id'],
                'dosen': ['mata_kuliah_diampu', 'ranting_ilmu_kepakaran'],
                'documents': []
            };

            const fields = requiredFields[tab] || [];
            totalFields += fields.length;

            fields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && field.value.trim()) {
                    filledFields++;
                }
            });
        });

        // Calculate percentage
        const progress = totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;
        window.dataPegawaiData.progress = progress;

        // Update progress bar
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        // Update progress text
        const progressText = document.querySelector('.progress-text');
        if (progressText) {
            progressText.textContent = `${progress}%`;
        }

        return progress;
    };

    // Show toast notification
    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-black',
            info: 'bg-blue-500 text-white'
        };

        toast.className += ` ${colors[type] || colors.info}`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    };

    // Employment Data Filtering Functions
    function filterJabatan() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jabatanSelect = document.getElementById('jabatan_terakhir_id');

        if (!jenisPegawaiSelect || !jabatanSelect) {
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;

        if (!selectedJenisPegawai) {
            return;
        }

        const options = jabatanSelect.querySelectorAll('option');
        let visibleOptions = [];

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const jabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (jabatanJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions.push(option);
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        });

        // Reset jabatan selection if current selection is not visible
        const currentJabatan = jabatanSelect.value;
        const currentOption = jabatanSelect.querySelector(`option[value="${currentJabatan}"]`);
        if (currentOption && currentOption.style.display === 'none') {
            jabatanSelect.value = '';
        }
    }

    function filterStatusKepegawaian() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');

        if (!jenisPegawaiSelect || !statusKepegawaianSelect) {
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;

        if (!selectedJenisPegawai) {
            return;
        }

        const options = statusKepegawaianSelect.querySelectorAll('option');
        let visibleOptions = [];

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const statusJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (statusJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions.push(option);
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        });

        // Reset status selection if current selection is not visible
        const currentStatus = statusKepegawaianSelect.value;
        const currentOption = statusKepegawaianSelect.querySelector(`option[value="${currentStatus}"]`);
        if (currentOption && currentOption.style.display === 'none') {
            statusKepegawaianSelect.value = '';
        }
    }

    function filterJenisJabatan() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        const selectedJenisJabatan = jenisJabatanSelect.value;

        if (!selectedJenisPegawai) {
            return;
        }

        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleOptions = 0;
        let hiddenOptions = 0;
        let shouldResetSelection = false;

        options.forEach((option, index) => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const jenisJabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (jenisJabatanJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleOptions++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenOptions++;

                if (option.value === selectedJenisJabatan) {
                    shouldResetSelection = true;
                }
            }
        });

        if (shouldResetSelection) {
            jenisJabatanSelect.value = '';
        }

        const visibleOptionsAfterFilter = jenisJabatanSelect.querySelectorAll('option:not([style*="display: none"])');
    }

    function filterPangkat() {
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');
        const pangkatSelect = document.getElementById('pangkat_terakhir_id');

        if (!statusKepegawaianSelect || !pangkatSelect) {
            return;
        }

        const selectedStatusKepegawaian = statusKepegawaianSelect.value;

        if (!selectedStatusKepegawaian) {
            return;
        }

        // Map status kepegawaian to pangkat status
        let targetPangkatStatus;
        switch (selectedStatusKepegawaian) {
            case 'Dosen PNS':
            case 'Tenaga Kependidikan PNS':
                targetPangkatStatus = 'PNS';
                break;
            case 'Dosen PPPK':
            case 'Tenaga Kependidikan PPPK':
                targetPangkatStatus = 'PPPK';
                break;
            case 'Dosen Non ASN':
            case 'Tenaga Kependidikan Non ASN':
                targetPangkatStatus = 'Non ASN';
                break;
            default:
                targetPangkatStatus = null;
        }

        const options = pangkatSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const optgroup = option.parentElement;
            const optgroupLabel = optgroup.label || '';

            if (targetPangkatStatus && optgroupLabel.includes(targetPangkatStatus)) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });
    }

    // Initialize employment data filters
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const statusKepegawaianSelect = document.getElementById('status_kepegawaian');

    if (jenisPegawaiSelect && statusKepegawaianSelect) {
        const currentJenisPegawai = jenisPegawaiSelect.value;

        if (currentJenisPegawai) {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        }

        // Add event listener for jenis pegawai
        jenisPegawaiSelect.addEventListener('change', function() {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        });

        // Add event listener for status kepegawaian
        if (statusKepegawaianSelect) {
            statusKepegawaianSelect.addEventListener('change', function() {
                filterPangkat();
            });
        }
    }

    // Function to check and fix filtering
    window.checkAndFixFiltering = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            return false;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleCount = 0;
        let shouldBeVisible = 0;

        options.forEach(option => {
            if (option.value === '') return;

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');
            const isVisible = option.style.display !== 'none';

            if (dataJenisPegawai === selectedJenisPegawai) {
                shouldBeVisible++;
                if (isVisible) visibleCount++;
            }
        });

        if (visibleCount !== shouldBeVisible) {
            filterJenisJabatan();
            return true;
        }

        return false;
    };

    // Manual filter trigger function for debugging
    window.triggerFilters = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        if (jenisPegawaiSelect) {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        }
    };

    // Initialize filters on page load with delay to ensure DOM is ready
    setTimeout(() => {
        if (jenisPegawaiSelect) {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        }
    }, 100);

    setTimeout(() => {
        if (jenisPegawaiSelect) {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();
        }
    }, 500);

    setTimeout(() => {
        if (jenisPegawaiSelect) {
            filterJabatan();
            filterStatusKepegawaian();
            filterJenisJabatan();
            filterPangkat();

            // Check and fix if needed
            window.checkAndFixFiltering();
        }
    }, 1000);

    // Periodic check every 2 seconds for the first 10 seconds
    let checkCount = 0;
    const checkInterval = setInterval(() => {
        checkCount++;

        if (jenisPegawaiSelect && jenisPegawaiSelect.value) {
            window.checkAndFixFiltering();
        }

        if (checkCount >= 5) {
            clearInterval(checkInterval);
        }
    }, 2000);

    // Form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            // Validate all tabs before submission
            const tabs = ['personal', 'employment', 'dosen', 'documents'];
            let hasErrors = false;

            tabs.forEach(tab => {
                if (!validateCurrentTab(tab)) {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                e.preventDefault();
                showToast('Mohon lengkapi semua field wajib sebelum menyimpan', 'error');

                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });
    }

    // Real-time progress updates
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            updateProgress();
        });

        input.addEventListener('change', function() {
            updateProgress();
        });
    });

    // Initialize progress on page load
    setTimeout(() => {
        updateProgress();
    }, 100);

    // Show success message if available (disabled to prevent duplicate with Laravel flash message)
    // if (window.successMessage) {
    //     showToast(window.successMessage, 'success');
    // }

});

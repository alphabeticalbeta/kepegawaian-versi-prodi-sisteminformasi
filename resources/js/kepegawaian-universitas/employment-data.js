// Employment Data Filtering JavaScript
console.log('=== EMPLOYMENT DATA FILTER SCRIPT STARTING ===');

// Check if script is loaded
if (typeof window !== 'undefined') {
    console.log('Window object available');
} else {
    console.log('Window object not available');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== EMPLOYMENT DATA FILTER SCRIPT LOADED ===');
    console.log('DOM Content Loaded event fired');

    // Check if elements exist
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const jenisJabatanSelect = document.getElementById('jenis_jabatan');
    const statusKepegawaianSelect = document.getElementById('status_kepegawaian');
    const jabatanTerakhirSelect = document.getElementById('jabatan_terakhir_id');

    console.log('Elements found:', {
        jenisPegawai: !!jenisPegawaiSelect,
        jenisJabatan: !!jenisJabatanSelect,
        statusKepegawaian: !!statusKepegawaianSelect,
        jabatanTerakhir: !!jabatanTerakhirSelect
    });

    if (jenisJabatanSelect) {
        console.log('Jenis jabatan options count:', jenisJabatanSelect.options.length);
        console.log('Jenis jabatan options:', Array.from(jenisJabatanSelect.options).map(opt => ({
            value: opt.value,
            text: opt.textContent,
            dataJenisPegawai: opt.getAttribute('data-jenis-pegawai')
        })));
    }

    function updateFilterStatus(elementId, message, isActive = true) {
        const statusEl = document.getElementById(elementId);
        if (statusEl) {
            statusEl.textContent = message;
            statusEl.className = isActive ? 'filter-status' : 'filter-status inactive';
            console.log(`Updated filter status for ${elementId}: ${message}`);
        } else {
            console.log(`Filter status element not found: ${elementId}`);
        }
    }

    function filterJenisJabatan() {
        console.log('=== FILTERING JENIS JABATAN ===');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            console.log('Jenis jabatan elements not found');
            updateFilterStatus('jenis-jabatan-filter-status', 'Elements Not Found', false);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('Filtering jenis jabatan for jenis pegawai:', selectedJenisPegawai);

        if (!selectedJenisPegawai) {
            updateFilterStatus('jenis-jabatan-filter-status', 'No Selection');
            return;
        }

        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                console.log(`Keeping placeholder option: ${option.textContent}`);
                return;
            }

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');
            console.log(`Option ${index}: ${option.textContent} - data-jenis-pegawai: ${dataJenisPegawai}`);

            if (dataJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
                console.log(`JENIS JABATAN SHOW: ${option.textContent}`);
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
                console.log(`JENIS JABATAN HIDE: ${option.textContent}`);
            }
        });

        console.log(`Jenis jabatan filter complete: ${visibleCount} visible, ${hiddenCount} hidden`);
        updateFilterStatus('jenis-jabatan-filter-status', `${visibleCount} Options Visible`);

        // Force browser to re-render the select
        jenisJabatanSelect.style.display = 'none';
        setTimeout(() => {
            jenisJabatanSelect.style.display = '';
            console.log('Forced re-render of jenis jabatan select');
        }, 10);
    }

    function filterStatusKepegawaian() {
        console.log('=== FILTERING STATUS KEPEGAWAIAN ===');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');

        if (!jenisPegawaiSelect || !statusKepegawaianSelect) {
            console.log('Status kepegawaian elements not found');
            updateFilterStatus('status-kepegawaian-filter-status', 'Elements Not Found', false);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('Filtering status kepegawaian for jenis pegawai:', selectedJenisPegawai);

        if (!selectedJenisPegawai) {
            updateFilterStatus('status-kepegawaian-filter-status', 'No Selection');
            return;
        }

        const options = statusKepegawaianSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (dataJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
                console.log(`STATUS KEPEGAWAIAN SHOW: ${option.textContent}`);
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
                console.log(`STATUS KEPEGAWAIAN HIDE: ${option.textContent}`);
            }
        });

        console.log(`Status kepegawaian filter complete: ${visibleCount} visible, ${hiddenCount} hidden`);
        updateFilterStatus('status-kepegawaian-filter-status', `${visibleCount} Options Visible`);

        // Force browser to re-render the select
        statusKepegawaianSelect.style.display = 'none';
        setTimeout(() => {
            statusKepegawaianSelect.style.display = '';
        }, 10);
    }

    function filterJabatanTerakhir() {
        console.log('=== FILTERING JABATAN TERAKHIR ===');
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jabatanTerakhirSelect = document.getElementById('jabatan_terakhir_id');

        if (!jenisPegawaiSelect || !jabatanTerakhirSelect) {
            console.log('Jabatan terakhir elements not found');
            updateFilterStatus('jabatan-terakhir-filter-status', 'Elements Not Found', false);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;
        console.log('Filtering jabatan terakhir for jenis pegawai:', selectedJenisPegawai);

        if (!selectedJenisPegawai) {
            updateFilterStatus('jabatan-terakhir-filter-status', 'No Selection');
            return;
        }

        const options = jabatanTerakhirSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (dataJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
                console.log(`JABATAN TERAKHIR SHOW: ${option.textContent}`);
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
                console.log(`JABATAN TERAKHIR HIDE: ${option.textContent}`);
            }
        });

        console.log(`Jabatan terakhir filter complete: ${visibleCount} visible, ${hiddenCount} hidden`);
        updateFilterStatus('jabatan-terakhir-filter-status', `${visibleCount} Options Visible`);

        // Force browser to re-render the select
        jabatanTerakhirSelect.style.display = 'none';
        setTimeout(() => {
            jabatanTerakhirSelect.style.display = '';
        }, 10);
    }

    function filterAllEmploymentData() {
        console.log('=== FILTERING ALL EMPLOYMENT DATA ===');
        filterJenisJabatan();
        filterStatusKepegawaian();
        filterJabatanTerakhir();
        console.log('=== ALL EMPLOYMENT DATA FILTERED ===');
    }

    // Apply filters immediately
    console.log('Applying filters immediately...');
    filterAllEmploymentData();

    // Add event listener for jenis pegawai
    if (jenisPegawaiSelect) {
        console.log('Adding event listener to jenis pegawai select');
        jenisPegawaiSelect.addEventListener('change', function() {
            console.log('Jenis pegawai changed to:', this.value);
            filterAllEmploymentData();
        });
    } else {
        console.log('Jenis pegawai select not found for event listener');
    }

    // Apply filters again after delays
    console.log('Setting up delayed filter applications...');
    setTimeout(() => {
        console.log('Applying filters after 100ms delay...');
        filterAllEmploymentData();
    }, 100);
    setTimeout(() => {
        console.log('Applying filters after 500ms delay...');
        filterAllEmploymentData();
    }, 500);
    setTimeout(() => {
        console.log('Applying filters after 1000ms delay...');
        filterAllEmploymentData();
    }, 1000);

    // Make functions globally available
    window.directFilterJenisJabatan = filterJenisJabatan;
    window.directFilterStatusKepegawaian = filterStatusKepegawaian;
    window.directFilterJabatanTerakhir = filterJabatanTerakhir;
    window.directFilterAllEmploymentData = filterAllEmploymentData;

    // Add manual trigger button
    const triggerButton = document.createElement('button');
    triggerButton.textContent = 'Force All Filters';
    triggerButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #dc2626;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        z-index: 9999;
        font-weight: bold;
    `;
    triggerButton.onclick = function() {
        console.log('Manual filter trigger clicked');
        filterAllEmploymentData();
    };
    document.body.appendChild(triggerButton);
    console.log('Manual trigger button added');

    console.log('=== EMPLOYMENT DATA FILTER SCRIPT COMPLETED ===');
});

// Also try to run immediately if DOM is already loaded
if (document.readyState === 'loading') {
    console.log('DOM still loading, waiting for DOMContentLoaded...');
} else {
    console.log('DOM already loaded, running script immediately...');
    // Trigger the script manually
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
}

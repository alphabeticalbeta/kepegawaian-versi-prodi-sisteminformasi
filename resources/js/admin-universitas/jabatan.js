// Jabatan JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // Jenis Jabatan data
    const jenisJabatanData = {
        'Dosen': [
            'Dosen Fungsional',
            'Dosen Fungsi Tambahan'
        ],
        'Tenaga Kependidikan': [
            'Tenaga Kependidikan Fungsional Tertentu',
            'Tenaga Kependidikan Fungsional Umum',
            'Tenaga Kependidikan Struktural',
            'Tenaga Kependidikan Tugas Tambahan'
        ]
    };

    // Populate Jenis Jabatan based on Jenis Pegawai
    window.populateJenisJabatan = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');
        const namaJabatanInput = document.getElementById('nama_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) return;

        const selectedJenisPegawai = jenisPegawaiSelect.value;

        // Clear existing options
        jenisJabatanSelect.innerHTML = '<option value="">Pilih Jenis Jabatan</option>';

        if (jenisJabatanData[selectedJenisPegawai]) {
            jenisJabatanData[selectedJenisPegawai].forEach(jenis => {
                const option = document.createElement('option');
                option.value = jenis;
                option.textContent = jenis;
                jenisJabatanSelect.appendChild(option);
            });
        }

        // Clear nama jabatan when jenis pegawai changes
        if (namaJabatanInput) {
            namaJabatanInput.value = '';
            updatePreview();
        }
    };

    // Update preview function
    window.updatePreview = function() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');
        const namaJabatanInput = document.getElementById('nama_jabatan');
        const previewDiv = document.getElementById('jabatan-preview');

        if (!previewDiv) return;

        const jenisPegawai = jenisPegawaiSelect ? jenisPegawaiSelect.value : '';
        const jenisJabatan = jenisJabatanSelect ? jenisJabatanSelect.value : '';
        const namaJabatan = namaJabatanInput ? namaJabatanInput.value : '';

        if (jenisPegawai && jenisJabatan && namaJabatan) {
            previewDiv.innerHTML = `
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Preview Jabatan
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Jenis Pegawai:</span>
                            <span class="text-sm text-slate-800 font-semibold">${jenisPegawai}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Jenis Jabatan:</span>
                            <span class="text-sm text-slate-800 font-semibold">${jenisJabatan}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600 w-24">Nama Jabatan:</span>
                            <span class="text-sm text-slate-800 font-semibold">${namaJabatan}</span>
                        </div>
                    </div>
                </div>
            `;
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }
    };

    // Initialize event listeners
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const jenisJabatanSelect = document.getElementById('jenis_jabatan');
    const namaJabatanInput = document.getElementById('nama_jabatan');

    if (jenisPegawaiSelect) {
        jenisPegawaiSelect.addEventListener('change', function() {
            populateJenisJabatan();
            updatePreview();
        });
    }

    if (jenisJabatanSelect) {
        jenisJabatanSelect.addEventListener('change', updatePreview);
    }

    if (namaJabatanInput) {
        namaJabatanInput.addEventListener('input', updatePreview);
    }

    // Initialize on page load
    if (jenisPegawaiSelect) {
        populateJenisJabatan();
        updatePreview();
    }
});

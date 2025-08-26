// ========================================
// PERIODE USULAN JAVASCRIPT
// ========================================

class PeriodeUsulan {
    constructor() {
        this.init();
    }

    init() {
        this.initializeDateValidation();
        this.initializeJenisUsulanInfo();
    }

    // Date validation functionality
    initializeDateValidation() {
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');
        const tanggalMulaiPerbaikan = document.getElementById('tanggal_mulai_perbaikan');
        const tanggalSelesaiPerbaikan = document.getElementById('tanggal_selesai_perbaikan');

        // Validasi tanggal selesai harus setelah tanggal mulai
        if (tanggalMulai) {
            tanggalMulai.addEventListener('change', function() {
                if (tanggalSelesai) {
                    tanggalSelesai.min = this.value;
                    if (tanggalSelesai.value && tanggalSelesai.value < this.value) {
                        tanggalSelesai.value = this.value;
                    }
                }
            });
        }

        // Validasi tanggal perbaikan
        if (tanggalSelesai) {
            tanggalSelesai.addEventListener('change', function() {
                if (tanggalMulaiPerbaikan) {
                    tanggalMulaiPerbaikan.min = this.value;
                    if (tanggalMulaiPerbaikan.value && tanggalMulaiPerbaikan.value < this.value) {
                        tanggalMulaiPerbaikan.value = this.value;
                    }
                }
            });
        }

        if (tanggalMulaiPerbaikan) {
            tanggalMulaiPerbaikan.addEventListener('change', function() {
                if (tanggalSelesaiPerbaikan) {
                    tanggalSelesaiPerbaikan.min = this.value;
                    if (tanggalSelesaiPerbaikan.value && tanggalSelesaiPerbaikan.value < this.value) {
                        tanggalSelesaiPerbaikan.value = this.value;
                    }
                }
            });
        }
    }

    // Jenis usulan info functionality
    initializeJenisUsulanInfo() {
        const jenisUsulanSelect = document.getElementById('jenis_usulan');

        if (jenisUsulanSelect) {
            // Update info berdasarkan jenis usulan saat halaman load
            this.updateJenisUsulanInfo();

            // Add change event listener
            jenisUsulanSelect.addEventListener('change', () => {
                this.updateJenisUsulanInfo();
            });
        }
    }

    // Update jenis usulan info
    updateJenisUsulanInfo() {
        const jenisUsulan = document.getElementById('jenis_usulan');
        if (!jenisUsulan) return;

        const value = jenisUsulan.value;
        const infoBoxes = document.querySelectorAll('.info-box');

        // Sembunyikan semua info box
        infoBoxes.forEach(box => {
            box.classList.add('hidden');
        });

        // Tampilkan info box yang sesuai
        if (value === 'usulan-jabatan-dosen') {
            const infoDosen = document.getElementById('info-dosen');
            const jenjangDosen = document.getElementById('jenjang-dosen');

            if (infoDosen) infoDosen.classList.remove('hidden');
            if (jenjangDosen) jenjangDosen.classList.remove('hidden');
        } else if (value === 'usulan-jabatan-tendik') {
            const infoTendik = document.getElementById('info-tendik');
            const warningTendik = document.getElementById('warning-tendik');
            const jenjangTendik = document.getElementById('jenjang-tendik');

            if (infoTendik) infoTendik.classList.remove('hidden');
            if (warningTendik) warningTendik.classList.remove('hidden');
            if (jenjangTendik) jenjangTendik.classList.remove('hidden');
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new PeriodeUsulan();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PeriodeUsulan;
}

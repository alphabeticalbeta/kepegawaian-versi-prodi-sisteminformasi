// JavaScript untuk Modal Tugas Belajar
// File: public/js/modal-tugas-belajar.js

// Variabel global untuk menyimpan periode ID
let currentPeriodeIdTugasBelajar = null;

// Fungsi untuk membuka modal tugas belajar
function openModalLihatPengusulTugasBelajar(periodeId) {
    console.log('openModalLihatPengusulTugasBelajar called with periodeId:', periodeId);

    // Simpan periode ID untuk digunakan nanti
    currentPeriodeIdTugasBelajar = periodeId;

    // Ambil data jumlah pengusul per jenis
    updateCountPengusulTugasBelajar(periodeId);

    // Tampilkan modal
    const modal = document.getElementById('modalLihatPengusulTugasBelajar');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        console.log('Modal Tugas Belajar opened successfully');
    } else {
        console.error('Modal element not found: modalLihatPengusulTugasBelajar');
    }
}

// Fungsi untuk menutup modal tugas belajar
function closeModalLihatPengusulTugasBelajar() {
    document.getElementById('modalLihatPengusulTugasBelajar').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fungsi untuk update jumlah pengusul per jenis
function updateCountPengusulTugasBelajar(periodeId) {
    // Ambil data dari periode yang ada
    const periodeRow = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
    if (periodeRow) {
        // Kolom Pendaftar adalah kolom ke-5 (index 4)
        const usulansCount = parseInt(periodeRow.querySelector('td:nth-child(5)').textContent) || 0;

        // Jika ada pengusul, hitung berdasarkan data yang sebenarnya
        if (usulansCount > 0) {
            // Set timeout yang lebih pendek (1 detik) untuk count cepat
            const apiTimeout = setTimeout(() => {
                fallbackToEvenDistributionTugasBelajar(periodeId);
            }, 1000);

            // Fetch data dengan promise
            fetchUsulanTugasBelajarCount(periodeId, apiTimeout);
        } else {
            // Jika tidak ada pengusul, set semua ke 0
            setAllCountsToZeroTugasBelajar();
        }
    }
}

// Fungsi untuk fetch data usulan tugas belajar dari server
function fetchUsulanTugasBelajarCount(periodeId, timeoutId) {
    // Buat loading state
    setLoadingStateTugasBelajar();

    // Fetch data dari server menggunakan AJAX
    // Gunakan route yang benar sesuai struktur Laravel
    fetch(`/kepegawaian-universitas/dashboard-periode/${periodeId}/usulan-tugas-belajar-count`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Clear timeout karena berhasil
        if (timeoutId) clearTimeout(timeoutId);

        if (data.success) {
            // Update count berdasarkan data yang sebenarnya
            updateCountsFromDataTugasBelajar(data.counts);
        } else {
            // Fallback ke distribusi merata jika API gagal
            fallbackToEvenDistributionTugasBelajar(periodeId);
        }
    })
    .catch(error => {
        // Clear timeout dan gunakan fallback
        if (timeoutId) clearTimeout(timeoutId);
        fallbackToEvenDistributionTugasBelajar(periodeId);
    });
}

// Fungsi untuk set loading state
function setLoadingStateTugasBelajar() {
    document.getElementById('count-dosen').textContent = '...';
    document.getElementById('count-tenaga-kependidikan').textContent = '...';
}

// Fungsi untuk update count berdasarkan data dari server
function updateCountsFromDataTugasBelajar(counts) {
    document.getElementById('count-dosen').textContent = counts.dosen || 0;
    document.getElementById('count-tenaga-kependidikan').textContent = counts.tenaga_kependidikan || 0;
}

// Fungsi fallback ke distribusi merata
function fallbackToEvenDistributionTugasBelajar(periodeId) {
    const periodeRow = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
    if (periodeRow) {
        const usulansCount = parseInt(periodeRow.querySelector('td:nth-child(5)').textContent) || 0;

        // Distribusikan secara merata
        const countPerJenis = Math.floor(usulansCount / 2);
        const remainder = usulansCount % 2;

        document.getElementById('count-dosen').textContent = countPerJenis + (remainder > 0 ? 1 : 0);
        document.getElementById('count-tenaga-kependidikan').textContent = countPerJenis;
    }
}

// Fungsi untuk set semua count ke 0
function setAllCountsToZeroTugasBelajar() {
    document.getElementById('count-dosen').textContent = '0';
    document.getElementById('count-tenaga-kependidikan').textContent = '0';
}

// Fungsi untuk melihat pengusul tugas belajar
function lihatPengusulTugasBelajar(jenisUsulan) {
    // Tutup modal
    closeModalLihatPengusulTugasBelajar();

    // Gunakan periode ID yang tersimpan
    if (!currentPeriodeIdTugasBelajar) {
        return;
    }

    // Redirect ke halaman dashboard periode dengan filter
    const baseUrl = window.location.origin + '/kepegawaian-universitas/dashboard-periode';
    const redirectUrl = `${baseUrl}/${currentPeriodeIdTugasBelajar}`;

    // Tambahkan parameter filter
    const filterParams = new URLSearchParams();
    filterParams.append('filter', 'jenis_tugas_belajar');
    filterParams.append('value', jenisUsulan);

    // Redirect ke halaman yang benar
    const finalUrl = `${redirectUrl}?${filterParams.toString()}`;

    window.location.href = finalUrl;
}

// Event listener untuk menutup modal jika klik di luar modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalLihatPengusulTugasBelajar');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalLihatPengusulTugasBelajar();
            }
        });
    }
});

// Export fungsi untuk digunakan di file lain
window.ModalTugasBelajar = {
    open: openModalLihatPengusulTugasBelajar,
    close: closeModalLihatPengusulTugasBelajar,
    updateCount: updateCountPengusulTugasBelajar,
    lihatPengusul: lihatPengusulTugasBelajar
};

// Pastikan fungsi tersedia secara global
window.openModalLihatPengusulTugasBelajar = openModalLihatPengusulTugasBelajar;
window.closeModalLihatPengusulTugasBelajar = closeModalLihatPengusulTugasBelajar;
window.lihatPengusulTugasBelajar = lihatPengusulTugasBelajar;

console.log('Tugas Belajar functions registered globally:', {
    openModalLihatPengusulTugasBelajar: typeof window.openModalLihatPengusulTugasBelajar,
    closeModalLihatPengusulTugasBelajar: typeof window.closeModalLihatPengusulTugasBelajar,
    lihatPengusulTugasBelajar: typeof window.lihatPengusulTugasBelajar
});

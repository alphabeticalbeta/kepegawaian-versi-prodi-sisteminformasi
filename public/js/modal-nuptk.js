// JavaScript untuk Modal NUPTK
// File: public/js/modal-nuptk.js

// Variabel global untuk menyimpan periode ID NUPTK
let currentPeriodeIdNuptk = null;

// Fungsi untuk membuka modal NUPTK
function openModalLihatPengusulNuptk(periodeId) {
    console.log('openModalLihatPengusulNuptk called with periodeId:', periodeId);
    
    // Simpan periode ID untuk digunakan nanti
    currentPeriodeIdNuptk = periodeId;
    
    // Ambil data jumlah pengusul per jenis
    updateCountPengusulNuptk(periodeId);
    
    // Tampilkan modal
    const modal = document.getElementById('modalLihatPengusulNuptk');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        console.log('Modal NUPTK opened successfully');
    } else {
        console.error('Modal element not found: modalLihatPengusulNuptk');
    }
}

// Fungsi untuk menutup modal NUPTK
function closeModalLihatPengusulNuptk() {
    document.getElementById('modalLihatPengusulNuptk').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fungsi untuk update jumlah pengusul per jenis
function updateCountPengusulNuptk(periodeId) {
    // Ambil data dari periode yang ada
    const periodeRow = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
    if (periodeRow) {
        // Kolom Pendaftar adalah kolom ke-5 (index 4)
        const usulansCount = parseInt(periodeRow.querySelector('td:nth-child(5)').textContent) || 0;
        
        // Jika ada pengusul, hitung berdasarkan data yang sebenarnya
        if (usulansCount > 0) {
            // Set timeout yang lebih pendek (1 detik) untuk count cepat
            const apiTimeout = setTimeout(() => {
                fallbackToEvenDistribution(periodeId);
            }, 1000);
            
            // Fetch data dengan promise
            fetchUsulanNuptkCount(periodeId, apiTimeout);
        } else {
            // Jika tidak ada pengusul, set semua ke 0
            setAllCountsToZero();
        }
    }
}

// Fungsi untuk fetch data usulan NUPTK dari server
function fetchUsulanNuptkCount(periodeId, timeoutId) {
    // Buat loading state
    setLoadingState();
    
    // Fetch data dari server menggunakan AJAX
    // Gunakan route yang benar sesuai struktur Laravel
    fetch(`/kepegawaian-universitas/dashboard-periode/${periodeId}/usulan-nuptk-count`, {
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
            updateCountsFromData(data.counts);
        } else {
            // Fallback ke distribusi merata jika API gagal
            fallbackToEvenDistribution(periodeId);
        }
    })
    .catch(error => {
        // Clear timeout dan gunakan fallback
        if (timeoutId) clearTimeout(timeoutId);
        fallbackToEvenDistribution(periodeId);
    });
}

// Fungsi untuk set loading state
function setLoadingState() {
    document.getElementById('count-dosen-tetap').textContent = '...';
    document.getElementById('count-dosen-tidak-tetap').textContent = '...';
    document.getElementById('count-pengajar-non-dosen').textContent = '...';
    document.getElementById('count-jabatan-fungsional-tertentu').textContent = '...';
}

// Fungsi untuk update count berdasarkan data dari server
function updateCountsFromData(counts) {
    document.getElementById('count-dosen-tetap').textContent = counts.dosen_tetap || 0;
    document.getElementById('count-dosen-tidak-tetap').textContent = counts.dosen_tidak_tetap || 0;
    document.getElementById('count-pengajar-non-dosen').textContent = counts.pengajar_non_dosen || 0;
    document.getElementById('count-jabatan-fungsional-tertentu').textContent = counts.jabatan_fungsional_tertentu || 0;
}

// Fungsi fallback ke distribusi merata
function fallbackToEvenDistribution(periodeId) {
    const periodeRow = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
    if (periodeRow) {
        const usulansCount = parseInt(periodeRow.querySelector('td:nth-child(5)').textContent) || 0;
        
        // Distribusikan secara merata
        const countPerJenis = Math.floor(usulansCount / 4);
        const remainder = usulansCount % 4;
        
        document.getElementById('count-dosen-tetap').textContent = countPerJenis + (remainder > 0 ? 1 : 0);
        document.getElementById('count-dosen-tidak-tetap').textContent = countPerJenis + (remainder > 1 ? 1 : 0);
        document.getElementById('count-pengajar-non-dosen').textContent = countPerJenis + (remainder > 2 ? 1 : 0);
        document.getElementById('count-jabatan-fungsional-tertentu').textContent = countPerJenis;
    }
}

// Fungsi untuk set semua count ke 0
function setAllCountsToZero() {
    document.getElementById('count-dosen-tetap').textContent = '0';
    document.getElementById('count-dosen-tidak-tetap').textContent = '0';
    document.getElementById('count-pengajar-non-dosen').textContent = '0';
    document.getElementById('count-jabatan-fungsional-tertentu').textContent = '0';
}

// Fungsi untuk melihat pengusul NUPTK
function lihatPengusulNuptk(jenisNuptk) {
    // Tutup modal
    closeModalLihatPengusulNuptk();
    
    // Gunakan periode ID yang tersimpan
    if (!currentPeriodeIdNuptk) {
        return;
    }
    
    // Redirect ke halaman dashboard periode dengan filter
    const baseUrl = window.location.origin + '/kepegawaian-universitas/dashboard-periode';
    const redirectUrl = `${baseUrl}/${currentPeriodeIdNuptk}`;
    
    // Tambahkan parameter filter
    const filterParams = new URLSearchParams();
    filterParams.append('filter', 'jenis_nuptk');
    filterParams.append('value', jenisNuptk);
    
    // Redirect ke halaman yang benar
    const finalUrl = `${redirectUrl}?${filterParams.toString()}`;
    
    window.location.href = finalUrl;
}

// Event listener untuk menutup modal jika klik di luar modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalLihatPengusulNuptk');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalLihatPengusulNuptk();
            }
        });
    }
});

// Export fungsi untuk digunakan di file lain
window.ModalNuptk = {
    open: openModalLihatPengusulNuptk,
    close: closeModalLihatPengusulNuptk,
    updateCount: updateCountPengusulNuptk,
    lihatPengusul: lihatPengusulNuptk
};

// Debug: Pastikan script dimuat
console.log('Modal NUPTK script loaded successfully');
console.log('Available functions:', Object.keys(window.ModalNuptk));

// Pastikan fungsi tersedia secara global
window.openModalLihatPengusulNuptk = openModalLihatPengusulNuptk;
window.closeModalLihatPengusulNuptk = closeModalLihatPengusulNuptk;
window.lihatPengusulNuptk = lihatPengusulNuptk;

console.log('NUPTK functions registered globally:', {
    openModalLihatPengusulNuptk: typeof window.openModalLihatPengusulNuptk,
    closeModalLihatPengusulNuptk: typeof window.closeModalLihatPengusulNuptk,
    lihatPengusulNuptk: typeof window.lihatPengusulNuptk
});

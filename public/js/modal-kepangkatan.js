// JavaScript untuk Modal Kepangkatan
// File: public/js/modal-kepangkatan.js

// Variabel global untuk menyimpan periode ID
let currentPeriodeId = null;

// Fungsi untuk membuka modal kepangkatan
function openModalLihatPengusulKepangkatan(periodeId) {
    // Simpan periode ID untuk digunakan nanti
    currentPeriodeId = periodeId;
    
    // Ambil data jumlah pengusul per jenis
    updateCountPengusulKepangkatan(periodeId);
    
    // Tampilkan modal
    document.getElementById('modalLihatPengusulKepangkatan').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Fungsi untuk menutup modal kepangkatan
function closeModalLihatPengusulKepangkatan() {
    document.getElementById('modalLihatPengusulKepangkatan').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fungsi untuk update jumlah pengusul per jenis
function updateCountPengusulKepangkatan(periodeId) {
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
            fetchUsulanKepangkatanCount(periodeId, apiTimeout);
        } else {
            // Jika tidak ada pengusul, set semua ke 0
            setAllCountsToZero();
        }
    }
}

// Fungsi untuk fetch data usulan kepangkatan dari server
function fetchUsulanKepangkatanCount(periodeId, timeoutId) {
    // Buat loading state
    setLoadingState();
    
    // Fetch data dari server menggunakan AJAX
    // Gunakan route yang benar sesuai struktur Laravel
    fetch(`/kepegawaian-universitas/periode-usulan/${periodeId}/usulan-kepangkatan-count`, {
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
        console.error('Error fetching usulan count:', error);
        // Clear timeout dan gunakan fallback
        if (timeoutId) clearTimeout(timeoutId);
        fallbackToEvenDistribution(periodeId);
    });
}

// Fungsi untuk set loading state
function setLoadingState() {
    document.getElementById('count-dosen-pns').textContent = '...';
    document.getElementById('count-jabatan-administrasi').textContent = '...';
    document.getElementById('count-jabatan-fungsional').textContent = '...';
    document.getElementById('count-jabatan-struktural').textContent = '...';
}

// Fungsi untuk update count berdasarkan data dari server
function updateCountsFromData(counts) {
    document.getElementById('count-dosen-pns').textContent = counts.dosen_pns || 0;
    document.getElementById('count-jabatan-administrasi').textContent = counts.jabatan_administrasi || 0;
    document.getElementById('count-jabatan-fungsional').textContent = counts.jabatan_fungsional || 0;
    document.getElementById('count-jabatan-struktural').textContent = counts.jabatan_struktural || 0;
}

// Fungsi fallback ke distribusi merata
function fallbackToEvenDistribution(periodeId) {
    const periodeRow = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
    if (periodeRow) {
        const usulansCount = parseInt(periodeRow.querySelector('td:nth-child(5)').textContent) || 0;
        
        // Distribusikan secara merata
        const countPerJenis = Math.floor(usulansCount / 4);
        const remainder = usulansCount % 4;
        
        document.getElementById('count-dosen-pns').textContent = countPerJenis + (remainder > 0 ? 1 : 0);
        document.getElementById('count-jabatan-administrasi').textContent = countPerJenis + (remainder > 1 ? 1 : 0);
        document.getElementById('count-jabatan-fungsional').textContent = countPerJenis + (remainder > 2 ? 1 : 0);
        document.getElementById('count-jabatan-struktural').textContent = countPerJenis;
    }
}

// Fungsi untuk set semua count ke 0
function setAllCountsToZero() {
    document.getElementById('count-dosen-pns').textContent = '0';
    document.getElementById('count-jabatan-administrasi').textContent = '0';
    document.getElementById('count-jabatan-fungsional').textContent = '0';
    document.getElementById('count-jabatan-struktural').textContent = '0';
}

// Fungsi untuk melihat pengusul kepangkatan
function lihatPengusulKepangkatan(jenisUsulan) {
    // Tutup modal
    closeModalLihatPengusulKepangkatan();
    
    // Gunakan periode ID yang tersimpan
    if (!currentPeriodeId) {
        console.error('Periode ID tidak tersedia');
        return;
    }
    
    // Redirect ke halaman dashboard periode dengan filter
    const baseUrl = window.location.origin + '/kepegawaian-universitas/dashboard-periode';
    const redirectUrl = `${baseUrl}/${currentPeriodeId}`;
    
    // Tambahkan parameter filter
    const filterParams = new URLSearchParams();
    filterParams.append('filter', 'jenis_usulan_pangkat');
    filterParams.append('value', jenisUsulan);
    
    // Redirect ke halaman yang benar
    const finalUrl = `${redirectUrl}?${filterParams.toString()}`;
    console.log('Redirecting to:', finalUrl);
    
    window.location.href = finalUrl;
}

// Event listener untuk menutup modal jika klik di luar modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalLihatPengusulKepangkatan');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalLihatPengusulKepangkatan();
            }
        });
    }
});

// Export fungsi untuk digunakan di file lain
window.ModalKepangkatan = {
    open: openModalLihatPengusulKepangkatan,
    close: closeModalLihatPengusulKepangkatan,
    updateCount: updateCountPengusulKepangkatan,
    lihatPengusul: lihatPengusulKepangkatan
};

# Jenis Jabatan Filtering - Debugging Tools Guide

## 🚨 **Masalah Saat Ini**
Jenis jabatan masih menampilkan semua opsi meskipun seharusnya ter-filter berdasarkan jenis pegawai yang dipilih.

## 🛠️ **Debugging Tools yang Telah Ditambahkan**

### 1. **Debug Panel Visual**
Panel debug akan muncul di pojok kanan atas halaman dengan informasi real-time:
- Jenis Pegawai saat ini
- Jenis Jabatan yang dipilih
- Jumlah opsi yang visible
- Status filter

### 2. **Console Logging Komprehensif**
Setiap langkah filtering akan di-log ke console dengan detail lengkap.

### 3. **Multiple Initialization Attempts**
- Immediate (100ms)
- Delayed (500ms) 
- Final (1000ms)
- Periodic checks (setiap 2 detik selama 10 detik)

## 🔧 **Cara Menggunakan Debugging Tools**

### 1. **Buka Form Edit Pegawai**
1. Navigate ke form edit pegawai
2. Buka tab "Data Kepegawaian"
3. Buka browser console (F12)

### 2. **Perhatikan Debug Panel**
Panel debug akan muncul di pojok kanan atas dengan informasi:
```
Debug Panel
Jenis Pegawai: [nilai saat ini]
Jenis Jabatan: [nilai saat ini]  
Visible Options: [jumlah opsi yang tampil]
Filter Status: Active

[Force Filter] [Test Filter]
```

### 3. **Gunakan Console Commands**

#### A. Quick Status Check
```javascript
// Cek status elemen
console.log('Elements found:', {
    jenisPegawai: !!document.getElementById('jenis_pegawai'),
    jenisJabatan: !!document.getElementById('jenis_jabatan')
});

// Cek nilai saat ini
console.log('Current values:', {
    jenisPegawai: document.getElementById('jenis_pegawai')?.value,
    jenisJabatan: document.getElementById('jenis_jabatan')?.value
});
```

#### B. Manual Filter Testing
```javascript
// Test filtering manual
window.testJenisJabatanFilter();

// Force apply filtering
window.forceFilterJenisJabatan();

// Check and fix filtering
window.checkAndFixFiltering();
```

#### C. Trigger All Filters
```javascript
// Trigger semua filter
window.triggerFilters();
```

### 4. **Test Perubahan Jenis Pegawai**

#### A. Test dengan Dosen
```javascript
// Set jenis pegawai ke Dosen
document.getElementById('jenis_pegawai').value = 'Dosen';

// Trigger change event
document.getElementById('jenis_pegawai').dispatchEvent(new Event('change'));

// Force filter
window.forceFilterJenisJabatan();
```

#### B. Test dengan Tenaga Kependidikan
```javascript
// Set jenis pegawai ke Tenaga Kependidikan
document.getElementById('jenis_pegawai').value = 'Tenaga Kependidikan';

// Trigger change event
document.getElementById('jenis_pegawai').dispatchEvent(new Event('change'));

// Force filter
window.forceFilterJenisJabatan();
```

## 📊 **Expected Console Output**

### Saat Halaman Dimuat
```
=== DATA PEGAWAI JS INITIALIZED ===
Initializing employment data filters...
jenisPegawaiSelect found: true
statusKepegawaianSelect found: true
Current jenis pegawai value: 
=== IMMEDIATE INITIALIZATION (100ms) ===
Immediate init - Current jenis pegawai: 
=== DELAYED INITIALIZATION (500ms) ===
Delayed init - Current jenis pegawai: 
=== FINAL INITIALIZATION (1000ms) ===
Final init - Current jenis pegawai: 
=== PERIODIC CHECK 1 ===
```

### Saat Jenis Pegawai Berubah
```
jenis_pegawai changed to: Dosen
Applying filters...
=== FILTER JENIS JABATAN CALLED ===
filterJenisJabatan: Filtering for jenis pegawai: Dosen
filterJenisJabatan: Total options found: 7
filterJenisJabatan: Option 1 "Dosen Fungsional" has jenis_pegawai: "Dosen"
filterJenisJabatan: Option 1 "Dosen Fungsional" - SHOWING (matches "Dosen")
filterJenisJabatan: Option 2 "Dosen dengan Tugas Tambahan" has jenis_pegawai: "Dosen"
filterJenisJabatan: Option 2 "Dosen dengan Tugas Tambahan" - SHOWING (matches "Dosen")
filterJenisJabatan: Option 3 "Tenaga Kependidikan Fungsional Umum" has jenis_pegawai: "Tenaga Kependidikan"
filterJenisJabatan: Option 3 "Tenaga Kependidikan Fungsional Umum" - HIDING (doesn't match "Dosen")
...
filterJenisJabatan: Summary - Visible options: 2, Hidden options: 4
filterJenisJabatan: Final visible options count: 3
=== FILTER JENIS JABATAN COMPLETED ===
```

## 🔍 **Troubleshooting Steps**

### 1. **Jika Debug Panel Tidak Muncul**
```javascript
// Force add debug panel
if (typeof addDebugPanel === 'function') {
    addDebugPanel();
    updateDebugPanel();
}
```

### 2. **Jika Filtering Tidak Bekerja**
```javascript
// Check if elements exist
console.log('Elements:', {
    jenisPegawai: !!document.getElementById('jenis_pegawai'),
    jenisJabatan: !!document.getElementById('jenis_jabatan')
});

// Check data attributes
document.querySelectorAll('#jenis_jabatan option').forEach((opt, i) => {
    console.log(`Option ${i}:`, {
        text: opt.textContent,
        value: opt.value,
        dataJenisPegawai: opt.getAttribute('data-jenis-pegawai')
    });
});

// Force apply filtering
window.forceFilterJenisJabatan();
```

### 3. **Jika Event Listeners Tidak Terpasang**
```javascript
// Check if event listeners are attached
const jenisPegawaiEl = document.getElementById('jenis_pegawai');
if (jenisPegawaiEl) {
    // Manually trigger change event
    jenisPegawaiEl.value = 'Dosen';
    jenisPegawaiEl.dispatchEvent(new Event('change'));
}
```

### 4. **Jika CSS Display Tidak Berubah**
```javascript
// Check CSS display properties
document.querySelectorAll('#jenis_jabatan option').forEach((opt, i) => {
    console.log(`Option ${i} display:`, opt.style.display);
});

// Force hide/show
document.querySelectorAll('#jenis_jabatan option').forEach(opt => {
    if (opt.value && opt.getAttribute('data-jenis-pegawai') !== 'Dosen') {
        opt.style.display = 'none';
        opt.disabled = true;
    }
});
```

## 🎯 **Quick Fix Commands**

### Jika Filtering Sama Sekali Tidak Bekerja:
```javascript
// 1. Force add event listener
const jenisPegawaiEl = document.getElementById('jenis_pegawai');
if (jenisPegawaiEl) {
    jenisPegawaiEl.addEventListener('change', function() {
        console.log('Manual change event triggered');
        window.forceFilterJenisJabatan();
    });
}

// 2. Force apply current filtering
window.forceFilterJenisJabatan();

// 3. Test with manual value change
jenisPegawaiEl.value = 'Dosen';
jenisPegawaiEl.dispatchEvent(new Event('change'));
```

### Jika Hanya Beberapa Opsi Tidak Ter-filter:
```javascript
// Check specific options
document.querySelectorAll('#jenis_jabatan option').forEach((opt, i) => {
    const dataAttr = opt.getAttribute('data-jenis-pegawai');
    const display = opt.style.display;
    console.log(`Option ${i}: ${opt.textContent} - data: "${dataAttr}", display: "${display}"`);
});
```

## 📋 **Verification Checklist**

- [ ] Debug panel muncul di pojok kanan atas
- [ ] Console menampilkan log inisialisasi
- [ ] Jenis pegawai dropdown berfungsi
- [ ] Saat pilih "Dosen", hanya opsi Dosen yang tampil
- [ ] Saat pilih "Tenaga Kependidikan", hanya opsi Tenaga Kependidikan yang tampil
- [ ] Console menampilkan log filtering yang detail
- [ ] Debug panel menampilkan informasi yang akurat

## 🚀 **Next Steps**

Jika setelah menggunakan semua tools ini filtering masih tidak bekerja:

1. **Cek Network Tab**: Pastikan JavaScript files ter-load dengan benar
2. **Cek Sources Tab**: Pastikan JavaScript yang benar yang dijalankan
3. **Clear Cache**: Clear browser cache dan reload
4. **Test di Browser Lain**: Pastikan bukan masalah browser-specific
5. **Check Laravel Mix**: Pastikan asset compilation sudah benar

---

*Gunakan debugging tools ini untuk mengidentifikasi dan memperbaiki masalah filtering jenis jabatan dengan cepat dan akurat.*

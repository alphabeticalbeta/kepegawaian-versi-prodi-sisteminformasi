# Quick Start Guide - Role Pegawai

## 🚀 **Setup Cepat Role Pegawai**

### **1. Jalankan Setup Script:**
```bash
php setup_pegawai_role.php
```

### **2. Atau Jalankan Seeder:**
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PegawaiSeeder
```

## 🔑 **Login Credentials**

### **Sample Users:**
| Nama | NIP | Email | Jenis | Status |
|------|-----|-------|-------|--------|
| Budi Santoso | 199001012015011001 | budi.santoso@unmul.ac.id | Dosen | PNS |
| Citra Lestari | 199202022016022002 | citra.lestari@unmul.ac.id | Tenaga Kependidikan | PNS |
| Ahmad Fauzi | 199503032017033003 | ahmad.fauzi@unmul.ac.id | Dosen | PNS |
| Siti Nurhaliza | 199604042018044004 | siti.nurhaliza@unmul.ac.id | Tenaga Kependidikan | PNS |
| Rizki Pratama | 199705052019055005 | rizki.pratama@unmul.ac.id | Dosen | PPPK |

### **Login Info:**
- **Username:** NIP (contoh: 199001012015011001)
- **Password:** NIP (contoh: 199001012015011001)

## 🎯 **Fitur Role Pegawai Unmul**

### **✅ Akses yang Diberikan:**
- **View Own Documents** - Melihat dokumen pribadi
- **Edit Own Profile** - Mengedit profil pribadi
- **Submit Usulan** - Mengajukan usulan jabatan
- **View Own Usulan Status** - Melihat status usulan pribadi

### **❌ Akses yang Dibatasi:**
- **View All Documents** - Tidak bisa melihat dokumen pegawai lain
- **Admin Functions** - Tidak bisa mengakses fungsi admin
- **User Management** - Tidak bisa mengelola user lain
- **System Settings** - Tidak bisa mengubah pengaturan sistem

## 📋 **Workflow Pegawai**

### **1. Login & Dashboard:**
1. Login dengan NIP dan password
2. Akses dashboard pegawai
3. Lihat overview profil dan aktivitas

### **2. Profile Management:**
1. Edit data pribadi
2. Update data kepegawaian
3. Upload dokumen pribadi
4. Simpan perubahan

### **3. Usulan Submission:**
1. Buat usulan jabatan baru
2. Isi form usulan
3. Upload dokumen pendukung
4. Submit usulan
5. Track status usulan

### **4. Document Management:**
1. Upload dokumen baru
2. View dokumen yang sudah ada
3. Download dokumen
4. Update dokumen jika diperlukan

## 🔧 **Troubleshooting**

### **Jika Database Error:**
```bash
# Cek koneksi database
php artisan config:clear
php artisan cache:clear

# Atau gunakan setup script manual
php setup_pegawai_role.php
```

### **Jika Role Tidak Muncul:**
```bash
# Cek role yang tersedia
php artisan tinker
>>> Spatie\Permission\Models\Role::all()->pluck('name')
```

### **Jika Login Gagal:**
1. Pastikan user sudah dibuat
2. Cek NIP dan password
3. Pastikan role sudah di-assign
4. Cek guard authentication

## 📊 **Verification Checklist**

- [ ] Role "Pegawai Unmul" sudah dibuat
- [ ] Permission sudah di-assign ke role
- [ ] Sample users sudah dibuat
- [ ] Login berhasil dengan NIP
- [ ] Dashboard pegawai bisa diakses
- [ ] Profile management berfungsi
- [ ] Document upload berfungsi
- [ ] Usulan submission berfungsi

## 🎉 **Success Indicators**

✅ **Setup Berhasil Jika:**
- Bisa login dengan NIP
- Dashboard pegawai muncul
- Menu profile dan usulan tersedia
- Tidak ada error di console
- Role dan permission terdeteksi

---

*Role Pegawai Unmul siap digunakan untuk manajemen profil, usulan, dan dokumen pribadi.*

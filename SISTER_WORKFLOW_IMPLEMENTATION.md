# IMPLEMENTASI WORKFLOW "DIUSULKAN KE SISTER" UNTUK ROLE PEGAWAI

## **OVERVIEW**
Implementasi workflow untuk menangani status "Diusulkan ke Sister" dan "Perbaikan dari Tim Sister" pada role Pegawai.

## **WORKFLOW YANG DIIMPLEMENTASIKAN**

### **1. Flow Utama**
```
Tim Senat (Direkomendasikan) → Admin Univ Usulan → "Diusulkan ke Sister"
                                                      ↓
                                              "Perbaikan dari Tim Sister" → Pegawai → "Diusulkan ke Universitas"
```

### **2. Status Baru yang Ditambahkan**
- `Diusulkan ke Sister` - Status setelah Admin Univ Usulan mengirim ke Sister
- `Perbaikan dari Tim Sister` - Status jika ada perbaikan dari Sister

## **PERUBAHAN YANG DILAKUKAN**

### **1. MODEL USULAN (Usulan.php)**

#### **A. Status Badge Class**
```php
'Diusulkan ke Sister' => 'bg-indigo-100 text-indigo-800',
'Perbaikan dari Tim Sister' => 'bg-orange-100 text-orange-800',
```

#### **B. Can Edit Attribute**
```php
return in_array($this->status_usulan, [
    'Draft',
    'Perlu Perbaikan',
    'Dikembalikan',
    'Perbaikan dari Tim Sister'  // DITAMBAHKAN
]);
```

#### **C. Is Read Only Attribute**
```php
return in_array($this->status_usulan, [
    'Diajukan',
    'Sedang Direview',
    'Disetujui',
    'Direkomendasikan',
    'Diusulkan ke Sister'  // DITAMBAHKAN
]);
```

### **2. CONTROLLER PEGAWAI (UsulanJabatanController.php)**

#### **A. Action Baru**
```php
'submit_perbaikan_sister' => 'Diusulkan ke Universitas', // Submit perbaikan dari Tim Sister ke Admin Univ Usulan
```

#### **B. Message Baru**
```php
'submit_perbaikan_sister' => 'Perbaikan dari Tim Sister berhasil dikirim ke Admin Universitas untuk validasi kembali.',
```

### **3. VIEW PEGAWAI**

#### **A. Create/Edit View (create-jabatan.blade.php)**
- **Tombol Baru**: "Kirim Perbaikan ke Universitas" untuk status "Perbaikan dari Tim Sister"
- **Kondisi**: `@elseif($usulan->status_usulan === 'Perbaikan dari Tim Sister')`

#### **B. Index View (index.blade.php)**
- **Update Kondisi Edit**: Menambahkan 'Perbaikan dari Tim Sister' ke daftar status yang bisa diedit

### **4. VALIDATION SERVICE (ValidationService.php)**

#### **A. Required Documents**
```php
'pegawai' => [
    // Pegawai tidak memerlukan dokumen pendukung untuk perbaikan sister
]
```

#### **B. Method Validasi Baru**
```php
public function canSubmitToSister(Usulan $usulan): bool
{
    return in_array($usulan->status_usulan, [
        'Direkomendasikan',
        'Disetujui'
    ]);
}

public function canSubmitPerbaikanFromSister(Usulan $usulan): bool
{
    return $usulan->status_usulan === 'Perbaikan dari Tim Sister';
}

public function canSubmitToAdminUnivUsulan(Usulan $usulan): bool
{
    return in_array($usulan->status_usulan, [
        'Perbaikan dari Tim Sister',
        'Diajukan'
    ]);
}
```

## **FUNGSIONALITAS YANG TERSEDIA**

### **1. Untuk Pegawai**
- ✅ Melihat status "Diusulkan ke Sister" (read-only)
- ✅ Melihat status "Perbaikan dari Tim Sister" (editable)
- ✅ Mengirim perbaikan dari Tim Sister ke Admin Univ Usulan untuk validasi
- ✅ Tombol "Kirim Perbaikan ke Universitas" muncul otomatis

### **2. Status Badge**
- ✅ "Diusulkan ke Sister" - Badge indigo
- ✅ "Perbaikan dari Tim Sister" - Badge orange

### **3. Validasi**
- ✅ Validasi status sebelum submit
- ✅ Validasi dokumen (tidak diperlukan untuk perbaikan sister)
- ✅ Validasi akses edit

## **TESTING SCENARIOS**

### **1. Scenario 1: Usulan Direkomendasikan**
1. Admin Univ Usulan mengubah status ke "Diusulkan ke Sister"
2. Pegawai melihat status "Diusulkan ke Sister" (read-only)
3. Tidak ada tombol edit yang tersedia

### **2. Scenario 2: Perbaikan dari Sister**
1. Admin Univ Usulan mengubah status ke "Perbaikan dari Tim Sister"
2. Pegawai melihat status "Perbaikan dari Tim Sister" (editable)
3. Tombol "Kirim Perbaikan ke Universitas" tersedia
4. Pegawai dapat mengirim perbaikan ke Admin Univ Usulan untuk validasi

### **3. Scenario 3: Submit Perbaikan**
1. Pegawai mengisi perbaikan
2. Klik "Kirim Perbaikan ke Universitas"
3. Status berubah menjadi "Diusulkan ke Universitas"
4. Pesan sukses ditampilkan

## **NEXT STEPS**

### **1. Admin Univ Usulan**
- Implementasi tombol "Diusulkan ke Sister"
- Implementasi tombol "Perbaikan dari Tim Sister"
- Form catatan perbaikan

### **2. Testing**
- Test semua scenario di atas
- Test validasi dan error handling
- Test UI/UX untuk semua status

## **NOTES**
- Status "Diusulkan ke Sister" bersifat read-only untuk Pegawai
- Status "Perbaikan dari Tim Sister" bersifat editable untuk Pegawai
- Tidak ada role Sister yang terlibat, hanya perubahan status
- Workflow ini terintegrasi dengan sistem validasi yang ada

# Perbaikan Alur Admin Fakultas

## Masalah yang Ditemukan

Berdasarkan analisis kode, ditemukan beberapa masalah pada alur Admin Fakultas:

1. **Tombol "Dokumen Dikirim ke Universitas"** muncul pada kondisi yang salah
2. **Alur Admin Fakultas** tidak sesuai dengan yang diharapkan
3. **Kondisi tampilan dokumen** perlu diperbaiki
4. **Kondisi button** tidak sesuai dengan alur yang diharapkan
5. **Modal "Perbaikan Usulan"** menampilkan teks yang salah untuk Admin Fakultas

## Alur yang Diharapkan

```
Pegawai → Admin Fakultas (validasi awal)
    ↓
Admin Fakultas → Admin Univ Usulan (jika valid)
    ↓
Admin Univ Usulan → Admin Fakultas/Pegawai (jika perlu perbaikan)
    ↓
Tombol "Dokumen Dikirim ke Universitas" hanya muncul setelah Admin Fakultas menekan "Usulkan ke Universitas"
```

## Perbaikan yang Dilakukan

### 1. Perbaikan Kondisi Tampilan Dokumen Admin Fakultas

**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

**Perubahan:**
- Menambahkan kondisi untuk menyembunyikan dokumen admin fakultas saat status `Diajukan`
- Memperbaiki kondisi `isEditableForm` agar hanya muncul saat `Perbaikan Usulan`

```php
// Sebelum
'isEditableForm' => $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan'])

// Sesudah  
'isEditableForm' => $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Perbaikan Usulan'])
```

### 2. Perbaikan Controller Admin Fakultas

**File:** `app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php`

**Perubahan:**
- Memperbaiki kondisi `canEdit` untuk status yang benar
- Menambahkan `resend_to_university` ke `submitFunctions`
- Memperbaiki validasi dokumen pendukung berdasarkan status

```php
// Sebelum
'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview'])

// Sesudah
'canEdit' => in_array($usulan->status_usulan, ['Diajukan', 'Perbaikan Usulan'])
```

### 3. Perbaikan JavaScript untuk Tombol Action

**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

**Perubahan:**
- Menambahkan fungsi `showKirimKembaliKeUniversitasModal()` untuk tombol "Kirim Kembali ke Universitas"
- Memperbaiki validasi dokumen pendukung saat resend
- Memperbaiki validasi untuk usulan pertama kali vs perbaikan
- **Memperbaiki modal "Perbaikan ke Pegawai"** untuk Admin Fakultas

### 4. Perbaikan Kondisi Button Admin Fakultas

**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

**Perubahan:**
- **Kondisi 1 (Status "Diajukan")**: Menampilkan 2 button
  - "Perbaikan ke Pegawai"
  - "Usulkan ke Universitas"
- **Kondisi 2 (Status "Perbaikan Usulan")**: Menampilkan 1 button
  - "Kirim Kembali ke Universitas"

```php
@if($usulan->status_usulan === 'Perbaikan Usulan')
    {{-- Kondisi 2: Admin Univ Usulan meminta perbaikan - hanya tampilkan "Kirim Kembali ke Universitas" --}}
    <button type="button" id="btn-kirim-ke-universitas">Kirim Kembali ke Universitas</button>
@elseif($usulan->status_usulan === 'Diajukan')
    {{-- Kondisi 1: Usulan pertama kali dari pegawai - tampilkan 2 button --}}
    <div class="flex gap-3">
        <button type="button" id="btn-perbaikan">Perbaikan ke Pegawai</button>
        <button type="button" id="btn-forward">Usulkan ke Universitas</button>
    </div>
@endif
```

### 5. Perbaikan Validasi Dokumen Pendukung

**File:** `app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php`

**Perubahan:**
- **Usulan pertama kali (Status "Diajukan")**: Tidak memerlukan dokumen pendukung
- **Perbaikan (Status "Perbaikan Usulan")**: Memerlukan dokumen pendukung

```php
// Jika status "Perbaikan Usulan", maka perlu dokumen pendukung
if ($usulan->status_usulan === 'Perbaikan Usulan') {
    $rules['dokumen_pendukung.nomor_surat_usulan'] = 'required|string|max:255';
    $rules['dokumen_pendukung.nomor_berita_senat'] = 'required|string|max:255';
    // ... validasi file
}
```

### 6. Perbaikan Modal "Perbaikan ke Pegawai"

**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

**Masalah:** Modal "Perbaikan Usulan" menampilkan teks yang salah untuk Admin Fakultas
- **Sebelum:** "Usulan akan dikirim ke Admin Universitas untuk review"
- **Sesudah:** "Usulan akan dikembalikan ke pegawai untuk perbaikan"

**Perubahan:**
- Menambahkan kondisi role pada event listener button "Perbaikan Usulan"
- Admin Fakultas: Modal dengan teks "Perbaikan ke Pegawai"
- Tim Penilai: Modal dengan teks "Perbaikan Usulan" (ke Admin Universitas)

```javascript
if (currentRole === 'Tim Penilai') {
    // Modal untuk Tim Penilai - kirim ke Admin Universitas
    Swal.fire({
        title: 'Perbaikan Usulan',
        text: 'Usulan akan dikirim ke Admin Universitas untuk review.',
        confirmButtonText: 'Kirim ke Admin Univ'
    });
} else if (currentRole === 'Admin Fakultas') {
    // Modal untuk Admin Fakultas - kirim ke Pegawai
    Swal.fire({
        title: 'Perbaikan ke Pegawai',
        text: 'Usulan akan dikembalikan ke pegawai untuk perbaikan.',
        confirmButtonText: 'Kembalikan ke Pegawai'
    });
}
```

## Alur yang Sudah Diperbaiki

### 1. Alur Normal (Pertama Kali)
1. **Pegawai** mengirim usulan ke **Admin Fakultas** (status: `Diajukan`)
2. **Admin Fakultas** melakukan validasi awal:
   - **Button yang muncul**: "Perbaikan ke Pegawai" dan "Usulkan ke Universitas"
   - Jika perlu perbaikan → tekan "Perbaikan ke Pegawai" → kembalikan ke **Pegawai** (status: `Perbaikan Usulan`)
   - Jika valid → tekan "Usulkan ke Universitas" (status: `Diusulkan ke Universitas`)
3. **Tombol "Dokumen Dikirim ke Universitas"** muncul setelah status berubah ke `Diusulkan ke Universitas`

### 2. Alur Perbaikan dari Admin Univ Usulan
1. **Admin Univ Usulan** meminta perbaikan
   - Bisa dikembalikan ke **Admin Fakultas** (status: `Perbaikan Usulan`)
   - Bisa dikembalikan ke **Pegawai** (status: `Perbaikan Usulan`)
2. **Admin Fakultas** memperbaiki:
   - **Button yang muncul**: Hanya "Kirim Kembali ke Universitas"
   - Tekan "Kirim Kembali ke Universitas" (status: `Diusulkan ke Universitas`)

### 3. Kondisi Tampilan Dokumen
- **Status `Diajukan`**: Dokumen admin fakultas TIDAK ditampilkan
- **Status `Perbaikan Usulan`**: Dokumen admin fakultas DITAMPILKAN dan BISA DEDIT
- **Status `Diusulkan ke Universitas`**: Dokumen admin fakultas DITAMPILKAN tapi TIDAK BISA DEDIT

### 4. Kondisi Button Admin Fakultas

| Status | Button yang Muncul | Keterangan |
|--------|-------------------|------------|
| `Diajukan` | • Perbaikan ke Pegawai<br>• Usulkan ke Universitas | Usulan pertama kali dari pegawai |
| `Perbaikan Usulan` | • Kirim Kembali ke Universitas | Usulan dikembalikan dari Admin Univ Usulan |
| `Diusulkan ke Universitas` | Tidak ada button | Usulan sudah dikirim ke universitas |

### 5. Modal yang Diperbaiki

| Role | Button | Modal Title | Modal Text | Action |
|------|--------|-------------|------------|--------|
| Admin Fakultas | Perbaikan ke Pegawai | "Perbaikan ke Pegawai" | "Usulan akan dikembalikan ke pegawai untuk perbaikan" | `return_to_pegawai` |
| Tim Penilai | Perbaikan Usulan | "Perbaikan Usulan" | "Usulan akan dikirim ke Admin Universitas untuk review" | `perbaikan_usulan` |

## Status Usulan yang Didukung

### Admin Fakultas
- `Diajukan`: Usulan baru dari pegawai
- `Perbaikan Usulan`: Usulan yang dikembalikan untuk perbaikan
- `Diusulkan ke Universitas`: Usulan yang sudah divalidasi dan dikirim ke universitas

### Admin Univ Usulan  
- `Diusulkan ke Universitas`: Usulan dari fakultas
- `Perbaikan Usulan`: Usulan yang dikembalikan untuk perbaikan
- `Sedang Direview`: Usulan yang sedang direview tim penilai

## Validasi yang Ditambahkan

### Saat "Usulkan ke Universitas" (Status "Diajukan")
- **TIDAK MEMERLUKAN** dokumen pendukung
- Hanya validasi data usulan

### Saat "Kirim Kembali ke Universitas" (Status "Perbaikan Usulan")
- **MEMERLUKAN** dokumen pendukung:
  - Nomor Surat Usulan wajib diisi
  - File Surat Usulan wajib diunggah
  - Nomor Berita Senat wajib diisi  
  - File Berita Senat wajib diunggah

## Testing

Untuk memastikan perbaikan berfungsi dengan baik, test skenario berikut:

1. **Skenario 1**: Pegawai → Admin Fakultas → Admin Univ Usulan
2. **Skenario 2**: Admin Univ Usulan → Admin Fakultas (perbaikan)
3. **Skenario 3**: Admin Fakultas → Admin Univ Usulan (resend)
4. **Skenario 4**: Admin Univ Usulan → Pegawai (perbaikan)

## Catatan Penting

- **Usulan pertama kali**: Admin Fakultas hanya perlu validasi data, tidak perlu dokumen pendukung
- **Usulan perbaikan**: Admin Fakultas harus mengisi dokumen pendukung sebelum kirim kembali
- Tombol "Dokumen Dikirim ke Universitas" hanya muncul setelah Admin Fakultas menekan "Usulkan ke Universitas"
- Dokumen admin fakultas hanya bisa diedit saat status `Perbaikan Usulan`
- Alur return dari Admin Univ Usulan sudah mendukung return ke Admin Fakultas atau Pegawai
- **Modal "Perbaikan ke Pegawai"** sekarang menampilkan teks yang benar untuk Admin Fakultas

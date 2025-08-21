# Pangkat Sorting and Display Fix

## 🎯 **Masalah yang Ditemukan**
Pangkat di dropdown "Pangkat Terakhir" tidak diurutkan berdasarkan status pangkat (PNS, PPPK, Non ASN) dan hierarki dari rendah ke tinggi, sehingga sulit untuk melihat urutan pangkat yang benar.

## ✅ **Solusi yang Diterapkan**
1. Menggunakan method `orderByHierarchy('asc')` pada model Pangkat
2. Mengelompokkan pangkat berdasarkan status menggunakan `optgroup`
3. Menambahkan styling untuk optgroup agar lebih menarik
4. Mengurutkan pangkat dari rendah ke tinggi dalam setiap kelompok

## 🔧 **Perubahan yang Dilakukan**

### **1. Model Pangkat - Method `orderByHierarchy`**
Method ini sudah tersedia di model `Pangkat` dan mengurutkan berdasarkan:
1. Status pangkat (PNS → PPPK → Non-ASN)
2. Hierarchy level (rendah ke tinggi)
3. Nama pangkat (alphabetical)

```php
public function scopeOrderByHierarchy($query, $direction = 'asc')
{
    return $query->orderByRaw('
        CASE
            WHEN status_pangkat = "PNS" THEN 1
            WHEN status_pangkat = "PPPK" THEN 2
            WHEN status_pangkat = "Non-ASN" THEN 3
            ELSE 4
        END,
        hierarchy_level ' . $direction . ',
        pangkat ' . $direction
    );
}
```

### **2. Controller - DataPegawaiController.php**
Menggunakan method `orderByHierarchy` untuk mengurutkan pangkat:

```php
$pangkats = \Cache::remember('pangkats_all_hierarchy', 3600, function () {
    return Pangkat::orderByHierarchy('asc')->get(['id', 'pangkat', 'hierarchy_level', 'status_pangkat']);
});
```

### **3. Blade Template - employment-data.blade.php**

#### **Sebelum:**
```html
@foreach($pangkats as $pangkat)
    <option value="{{ $pangkat->id }}"
            data-status="{{ $pangkat->status_pangkat }}"
            {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
        {{ $pangkat->pangkat }}
    </option>
@endforeach
```

#### **Sesudah:**
```html
{{-- PNS Pangkat --}}
<optgroup label="PNS">
    @foreach($pangkats->where('status_pangkat', 'PNS') as $pangkat)
        <option value="{{ $pangkat->id }}"
                data-status="{{ $pangkat->status_pangkat }}"
                {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
            {{ $pangkat->pangkat }}
        </option>
    @endforeach
</optgroup>

{{-- PPPK Pangkat --}}
<optgroup label="PPPK">
    @foreach($pangkats->where('status_pangkat', 'PPPK') as $pangkat)
        <option value="{{ $pangkat->id }}"
                data-status="{{ $pangkat->status_pangkat }}"
                {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
            {{ $pangkat->pangkat }}
        </option>
    @endforeach
</optgroup>

{{-- Non ASN Pangkat --}}
<optgroup label="Non ASN">
    @foreach($pangkats->where('status_pangkat', 'Non-ASN') as $pangkat)
        <option value="{{ $pangkat->id }}"
                data-status="{{ $pangkat->status_pangkat }}"
                {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
            {{ $pangkat->pangkat }}
        </option>
    @endforeach
</optgroup>
```

### **4. CSS Styling untuk Optgroup**
```css
/* Styling for optgroup in pangkat dropdown */
#pangkat_terakhir_id optgroup {
    font-weight: bold;
    color: #374151;
    background-color: #f9fafb;
    padding: 8px 0;
}

#pangkat_terakhir_id optgroup[label="PNS"] {
    color: #059669;
    background-color: #ecfdf5;
}

#pangkat_terakhir_id optgroup[label="PPPK"] {
    color: #2563eb;
    background-color: #eff6ff;
}

#pangkat_terakhir_id optgroup[label="Non ASN"] {
    color: #ea580c;
    background-color: #fff7ed;
}

#pangkat_terakhir_id option {
    padding: 8px 12px;
    font-weight: normal;
}
```

## 📊 **Urutan Hierarki Pangkat**

### **PNS (Green) - Urutan Rendah ke Tinggi:**
1. **Juru Muda** (Golongan I/a)
2. **Juru Muda Tingkat I** (Golongan I/b)
3. **Juru** (Golongan I/c)
4. **Juru Tingkat I** (Golongan I/d)
5. **Pengatur Muda** (Golongan II/a)
6. **Pengatur Muda Tingkat I** (Golongan II/b)
7. **Pengatur** (Golongan II/c)
8. **Pengatur Tingkat I** (Golongan II/d)
9. **Penata Muda** (Golongan III/a)
10. **Penata Muda Tingkat I** (Golongan III/b)
11. **Penata** (Golongan III/c)
12. **Penata Tingkat I** (Golongan III/d)
13. **Pembina** (Golongan IV/a)
14. **Pembina Tingkat I** (Golongan IV/b)
15. **Pembina Utama Muda** (Golongan IV/c)
16. **Pembina Utama Madya** (Golongan IV/d)
17. **Pembina Utama** (Golongan IV/e)

### **PPPK (Blue) - Urutan Rendah ke Tinggi:**
1. **Pranata Muda** (Golongan I/a)
2. **Pranata Muda Tingkat I** (Golongan I/b)
3. **Pranata** (Golongan I/c)
4. **Pranata Tingkat I** (Golongan I/d)
5. **Pranata Utama** (Golongan I/e)
6. **Pranata Utama Tingkat I** (Golongan I/f)

### **Non ASN (Orange) - Urutan Rendah ke Tinggi:**
1. **Staff Junior**
2. **Staff Senior**
3. **Supervisor**
4. **Manager**
5. **Director**

## 🎯 **Keuntungan Perubahan**

### 1. **User Experience yang Lebih Baik**
- Pangkat dikelompokkan berdasarkan status yang jelas
- Urutan hierarki yang logis dari rendah ke tinggi
- Visual grouping dengan warna yang berbeda
- Lebih mudah untuk memilih pangkat yang sesuai

### 2. **Konsistensi dengan Business Logic**
- Sesuai dengan struktur pangkat yang sebenarnya
- Memudahkan pemahaman progression karir
- Konsisten dengan sistem promosi pangkat

### 3. **Visual Clarity**
- Optgroup dengan warna yang berbeda untuk setiap status
- PNS: Green theme
- PPPK: Blue theme
- Non ASN: Orange theme

### 4. **Performance Optimization**
- Menggunakan caching untuk data pangkat
- Cache key yang lebih spesifik (`pangkats_all_hierarchy`)
- Mengambil field yang diperlukan saja

## 📋 **Testing Checklist**

- [x] Pangkat diurutkan berdasarkan status (PNS → PPPK → Non ASN)
- [x] Pangkat dalam setiap status diurutkan dari rendah ke tinggi
- [x] Optgroup menampilkan label yang benar
- [x] Styling optgroup berfungsi dengan baik
- [x] Data attributes tetap tersimpan dengan benar
- [x] Form create dan edit menggunakan urutan yang sama
- [x] Cache berfungsi dengan baik untuk performance

## 🎉 **Hasil yang Diharapkan**

Setelah fix ini:

- ✅ **Logical Grouping**: Pangkat dikelompokkan berdasarkan status
- ✅ **Hierarchical Ordering**: Urutan dari rendah ke tinggi dalam setiap kelompok
- ✅ **Visual Clarity**: Optgroup dengan styling yang menarik
- ✅ **Better UX**: User dapat memilih pangkat dengan mudah
- ✅ **Performance**: Caching untuk data pangkat
- ✅ **Consistent Display**: Urutan konsisten di semua halaman

## 🔄 **Kompatibilitas**

Perubahan ini:
- ✅ **Backward Compatible**: Tidak mempengaruhi data yang tersimpan
- ✅ **Form Compatible**: Form submission tidak terpengaruh
- ✅ **Validation Compatible**: Validasi tetap berjalan normal
- ✅ **Cache Compatible**: Menggunakan cache key yang berbeda

## 📊 **Contoh Tampilan Dropdown**

```
Pilih Pangkat
├── PNS
│   ├── Juru Muda
│   ├── Juru Muda Tingkat I
│   ├── Juru
│   ├── ...
│   └── Pembina Utama
├── PPPK
│   ├── Pranata Muda
│   ├── Pranata Muda Tingkat I
│   ├── ...
│   └── Pranata Utama Tingkat I
└── Non ASN
    ├── Staff Junior
    ├── Staff Senior
    ├── ...
    └── Director
```

---

*Fix ini memberikan pengelompokan dan pengurutan pangkat yang lebih logis dan user-friendly sesuai dengan struktur pangkat yang sebenarnya.*

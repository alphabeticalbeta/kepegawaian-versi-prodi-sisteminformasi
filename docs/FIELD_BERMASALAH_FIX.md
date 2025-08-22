# 🔧 FIX: Field-Field yang Bermasalah - Tampilan Satu Baris per Penilai

## 🎯 **MASALAH:**
Field-field yang bermasalah tidak tampil pada halaman usulan detail. User ingin tampilan dibuat dalam satu baris untuk penilai 1 dan penilai 2.

## ✅ **SOLUSI:**

### **1. Struktur Data yang Diperlukan:**
```php
$usulan->validasi_data = [
    'tim_penilai' => [
        'penilai_1' => [
            'validation' => [
                'data_pribadi' => [
                    'nama_lengkap' => [
                        'status' => 'tidak_sesuai',
                        'keterangan' => 'Nama tidak sesuai dengan dokumen'
                    ]
                ]
            ],
            'perbaikan_usulan' => [
                'catatan' => 'Perbaiki ini',
                'penilai_id' => 1,
                'tanggal_return' => '2025-08-21 05:00:00'
            ]
        ],
        'penilai_2' => [
            'validation' => [
                'data_pendidikan' => [
                    'ijazah_terakhir' => [
                        'status' => 'tidak_sesuai',
                        'keterangan' => 'Ijazah tidak lengkap'
                    ]
                ]
            ],
            'perbaikan_usulan' => [
                'catatan' => 'perbaiki usulan ini',
                'penilai_id' => 2,
                'tanggal_return' => '2025-08-21 05:05:00'
            ]
        ]
    ]
];
```

### **2. Kode View yang Diperbaiki:**

```blade
{{-- Review dari Tim Penilai Universitas --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
            Review dari Tim Penilai Universitas
        </h2>
    </div>
    <div class="p-6">
        
        {{-- Ringkasan Review --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Ringkasan Review Tim Penilai</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">2</div>
                    <div class="text-sm text-blue-700">Total Penilai</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">2</div>
                    <div class="text-sm text-green-700">Sudah Review</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">0</div>
                    <div class="text-sm text-yellow-700">Belum Review</div>
                </div>
            </div>
        </div>

        {{-- Perbaikan Usulan dari Setiap Penilai --}}
        @php
            $validasiData = $usulan->validasi_data ?? [];
            $timPenilaiData = $validasiData['tim_penilai'] ?? [];
            $penilaiIndex = 1;
        @endphp

        @foreach($timPenilaiData as $penilaiKey => $penilaiData)
            @if(isset($penilaiData['perbaikan_usulan']))
                @php
                    $penilaiId = $penilaiData['perbaikan_usulan']['penilai_id'] ?? null;
                    $penilai = $assignedPenilais->firstWhere('id', $penilaiId);
                    $penilaiName = $penilai ? $penilai->nama_lengkap : "Penilai {$penilaiIndex}";
                    $catatan = $penilaiData['perbaikan_usulan']['catatan'] ?? 'Tidak ada catatan';
                    $tanggal = $penilaiData['perbaikan_usulan']['tanggal_return'] ?? null;
                    
                    // Get validation data for this penilai
                    $validationData = $penilaiData['validation'] ?? [];
                    $invalidFields = [];
                    
                    foreach ($validationData as $category => $fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $field => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    $invalidFields[] = [
                                        'category' => $category,
                                        'field' => $field,
                                        'keterangan' => $fieldData['keterangan'] ?? 'Perlu perbaikan'
                                    ];
                                }
                            }
                        }
                    }
                @endphp

                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                    {{-- Header Penilai --}}
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="edit-3" class="w-5 h-5 mr-2 text-orange-600"></i>
                            Perbaikan Usulan - Penilai {{ $penilaiIndex }}
                        </h4>
                        <span class="text-sm text-gray-600">{{ $penilaiName }}</span>
                    </div>

                    {{-- Catatan Perbaikan --}}
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                        <div class="flex items-start gap-3">
                            <i data-lucide="message-square" class="w-4 h-4 text-orange-600 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-orange-800 mb-1">Catatan Perbaikan:</p>
                                <p class="text-sm text-orange-700">{{ $catatan }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Field-Field yang Bermasalah dalam Satu Baris --}}
                    @if(!empty($invalidFields))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 mt-0.5"></i>
                                <div class="w-full">
                                    <p class="text-sm font-medium text-red-800 mb-2">
                                        Field-Field yang Bermasalah ({{ count($invalidFields) }} field):
                                    </p>
                                    
                                    {{-- Tampilan Satu Baris --}}
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($invalidFields as $field)
                                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 border border-red-300 rounded-full">
                                                <i data-lucide="x-circle" class="w-3 h-3 text-red-700"></i>
                                                <span class="text-xs font-medium text-red-800">
                                                    {{ ucwords(str_replace('_', ' ', $field['category'])) }} > 
                                                    {{ ucwords(str_replace('_', ' ', $field['field'])) }}
                                                </span>
                                                <span class="text-xs text-red-700">
                                                    ({{ $field['keterangan'] }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-800">Status:</p>
                                    <p class="text-sm text-green-700">Tidak ada field yang bermasalah</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Informasi Review --}}
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="font-medium">Tanggal Review:</span>
                        @if($tanggal)
                            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y, HH:mm') }}
                        @else
                            Tidak tersedia
                        @endif
                    </div>
                </div>

                @php $penilaiIndex++; @endphp
            @endif
        @endforeach

        {{-- Jika tidak ada data perbaikan --}}
        @if(empty($timPenilaiData))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-gray-600"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Info:</p>
                        <p class="text-sm text-gray-700">Belum ada review dari tim penilai universitas</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
```

### **3. Controller Update (jika diperlukan):**

```php
// Di controller yang menangani usulan detail
public function show($id)
{
    $usulan = Usulan::with(['pegawai', 'assignedPenilais'])->findOrFail($id);
    
    // Pastikan data validasi tersedia
    $validasiData = $usulan->validasi_data ?? [];
    
    // Debug untuk memeriksa struktur data
    if (config('app.debug')) {
        \Log::info('Validasi Data Structure:', $validasiData);
    }
    
    return view('backend.layouts.views.shared.usulan-detail', compact('usulan', 'validasiData'));
}
```

### **4. Model Update (jika diperlukan):**

```php
// Di model Usulan, pastikan method getValidasiByRole berfungsi dengan benar
public function getValidasiByRole($role)
{
    $validasiData = $this->validasi_data ?? [];
    
    if ($role === 'tim_penilai') {
        return $validasiData['tim_penilai'] ?? [];
    }
    
    return $validasiData[$role] ?? [];
}

public function getInvalidFields($role)
{
    $validationData = $this->getValidasiByRole($role);
    $invalidFields = [];
    
    if (is_array($validationData)) {
        foreach ($validationData as $category => $fields) {
            if (is_array($fields)) {
                foreach ($fields as $field => $fieldData) {
                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                        $invalidFields[] = [
                            'category' => $category,
                            'field' => $field,
                            'keterangan' => $fieldData['keterangan'] ?? 'Perlu perbaikan'
                        ];
                    }
                }
            }
        }
    }
    
    return $invalidFields;
}
```

## 🎯 **HASIL YANG DIHARAPKAN:**

```
Review dari Tim Penilai Universitas
Ringkasan Review Tim Penilai
2 Total Penilai | 2 Sudah Review | 0 Belum Review

Perbaikan Usulan - Penilai 1
Penilai: Muhammad Rivani Ibrahim
Catatan Perbaikan: Perbaiki ini

Field-Field yang Bermasalah (2 field):
❌ Data Pribadi > Nama Lengkap (Nama tidak sesuai dengan dokumen)
❌ Data Pribadi > NIP (NIP tidak valid)

Tanggal Review: 21 August 2025, 05:00

Perbaikan Usulan - Penilai 2  
Penilai: Ahmad Fauzi
Catatan Perbaikan: perbaiki usulan ini

Field-Field yang Bermasalah (1 field):
❌ Data Pendidikan > Ijazah Terakhir (Ijazah tidak lengkap)

Tanggal Review: 21 August 2025, 05:05
```

## 🔧 **LANGKAH IMPLEMENTASI:**

### **1. Update View:**
- Ganti kode di `usulan-detail.blade.php` dengan kode di atas
- Pastikan struktur data sesuai dengan yang diharapkan

### **2. Test Data:**
- Pastikan data validasi tersimpan dengan format yang benar
- Periksa apakah field status 'tidak_sesuai' ada

### **3. Debug:**
- Tambahkan debug info untuk memeriksa struktur data
- Pastikan variable scope benar

## 📝 **CATATAN:**

### **1. Struktur Data:**
- Data harus tersimpan dalam format yang benar
- Setiap penilai harus memiliki section sendiri
- Field status harus 'tidak_sesuai' untuk ditampilkan

### **2. Tampilan:**
- Field bermasalah ditampilkan dalam satu baris dengan flex-wrap
- Setiap field ditampilkan sebagai badge dengan informasi lengkap
- Warna dan icon yang konsisten

### **3. Responsivitas:**
- Tampilan responsive untuk mobile dan desktop
- Flex-wrap memastikan field tidak overflow

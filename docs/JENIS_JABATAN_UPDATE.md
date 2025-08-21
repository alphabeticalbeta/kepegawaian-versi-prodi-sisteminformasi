# Jenis Jabatan Dropdown Update Documentation

## Problem Description
The jenis jabatan (job type) dropdown needed to be updated to reflect the correct categorization. The requirement was to have:
- **Dosen**: Only "Dosen Fungsional" and "Dosen dengan Tugas Tambahan"
- **Tenaga Kependidikan**: All other options (Fungsional Umum, Fungsional Tertentu, Struktural, Tugas Tambahan)

## Changes Made

### 1. Frontend Update (`employment-data.blade.php`)

#### Updated Dropdown Options
**Before:**
```html
<option value="Dosen Fungsional" data-jenis-pegawai="Dosen">Dosen Fungsional</option>
<option value="Dosen Fungsi Tambahan" data-jenis-pegawai="Dosen">Dosen Fungsi Tambahan</option>
```

**After:**
```html
<option value="Dosen Fungsional" data-jenis-pegawai="Dosen">Dosen Fungsional</option>
<option value="Dosen dengan Tugas Tambahan" data-jenis-pegawai="Dosen">Dosen dengan Tugas Tambahan</option>
```

### 2. Backend Validation Update (`DataPegawaiController.php`)

#### Updated Validation Rules
**Before:**
```php
'jenis_jabatan' => 'required|string|in:Dosen Fungsional,Dosen Fungsi Tambahan,Tenaga Kependidikan Fungsional Umum,Tenaga Kependidikan Fungsional Tertentu,Tenaga Kependidikan Struktural,Tenaga Kependidikan Tugas Tambahan',
```

**After:**
```php
'jenis_jabatan' => 'required|string|in:Dosen Fungsional,Dosen dengan Tugas Tambahan,Tenaga Kependidikan Fungsional Umum,Tenaga Kependidikan Fungsional Tertentu,Tenaga Kependidikan Struktural,Tenaga Kependidikan Tugas Tambahan',
```

### 3. Model Update (`Jabatan.php`)

#### Updated Badge Class Mapping
**Before:**
```php
'Dosen Fungsi Tambahan' => 'bg-purple-100 text-purple-800',
```

**After:**
```php
'Dosen dengan Tugas Tambahan' => 'bg-purple-100 text-purple-800',
```

#### Updated Comment
**Before:**
```php
// Dosen Fungsi Tambahan tidak ada usulan (sudah pindah manual)
```

**After:**
```php
// Dosen dengan Tugas Tambahan tidak ada usulan (sudah pindah manual)
```

### 4. Database Seeder Update (`JabatanSeeder.php`)

#### Updated Seeder Data
**Before:**
```php
// DOSEN FUNGSI TAMBAHAN (Non-Hierarki)
[
    'jabatan' => 'Ketua Jurusan',
    'jenis_pegawai' => 'Dosen',
    'jenis_jabatan' => 'Dosen Fungsi Tambahan',
    'hierarchy_level' => null
],
// ... more entries
```

**After:**
```php
// DOSEN DENGAN TUGAS TAMBAHAN (Non-Hierarki)
[
    'jabatan' => 'Ketua Jurusan',
    'jenis_pegawai' => 'Dosen',
    'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
    'hierarchy_level' => null
],
// ... more entries
```

### 5. Test Update (`JabatanHierarchyTest.php`)

#### Updated Test Data
**Before:**
```php
// Dosen Fungsi Tambahan
['jabatan' => 'Dekan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsi Tambahan', 'hierarchy_level' => null],
```

**After:**
```php
// Dosen dengan Tugas Tambahan
['jabatan' => 'Dekan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen dengan Tugas Tambahan', 'hierarchy_level' => null],
```

## Final Jenis Jabatan Options

### For Dosen (Employee Type: Dosen)
1. **Dosen Fungsional** - For functional lecturer positions with hierarchy levels
2. **Dosen dengan Tugas Tambahan** - For lecturers with additional administrative duties

### For Tenaga Kependidikan (Employee Type: Tenaga Kependidikan)
1. **Tenaga Kependidikan Fungsional Umum** - General functional staff positions
2. **Tenaga Kependidikan Fungsional Tertentu** - Specific functional staff positions
3. **Tenaga Kependidikan Struktural** - Structural staff positions
4. **Tenaga Kependidikan Tugas Tambahan** - Staff with additional duties

## Data Flow

### 1. User Selection
1. User selects "Dosen" as Jenis Pegawai
2. Jenis Jabatan dropdown shows only:
   - Dosen Fungsional
   - Dosen dengan Tugas Tambahan

2. User selects "Tenaga Kependidikan" as Jenis Pegawai
3. Jenis Jabatan dropdown shows only:
   - Tenaga Kependidikan Fungsional Umum
   - Tenaga Kependidikan Fungsional Tertentu
   - Tenaga Kependidikan Struktural
   - Tenaga Kependidikan Tugas Tambahan

### 2. Form Submission
- Backend validation ensures only valid combinations are accepted
- Data is stored with the correct jenis_jabatan value

### 3. Display
- Badge colors and styling reflect the new naming
- All UI components show the updated labels

## File Structure

```
resources/views/backend/layouts/views/admin-univ-usulan/data-pegawai/partials/
└── employment-data.blade.php
    └── Jenis Jabatan dropdown (updated options)

app/Http/Controllers/Backend/AdminUnivUsulan/
└── DataPegawaiController.php
    └── Validation rules (updated)

app/Models/BackendUnivUsulan/
└── Jabatan.php
    ├── Badge class mapping (updated)
    └── Comments (updated)

database/seeders/
└── JabatanSeeder.php
    └── Seeder data (updated)

tests/Feature/
└── JabatanHierarchyTest.php
    └── Test data (updated)
```

## Testing

### Manual Testing Steps
1. Navigate to edit pegawai form
2. Go to "Data Kepegawaian" tab
3. Select "Dosen" as Jenis Pegawai
4. Verify Jenis Jabatan shows only:
   - Dosen Fungsional
   - Dosen dengan Tugas Tambahan
5. Select "Tenaga Kependidikan" as Jenis Pegawai
6. Verify Jenis Jabatan shows only:
   - Tenaga Kependidikan Fungsional Umum
   - Tenaga Kependidikan Fungsional Tertentu
   - Tenaga Kependidikan Struktural
   - Tenaga Kependidikan Tugas Tambahan
7. Test form submission with various combinations
8. Verify data is saved correctly

### Database Testing
1. Run database seeder to ensure new data is consistent
2. Check existing records for any inconsistencies
3. Verify validation rules work correctly

## Migration Considerations

### Existing Data
- Existing records with "Dosen Fungsi Tambahan" will need to be updated to "Dosen dengan Tugas Tambahan"
- Consider creating a database migration to update existing records

### Backward Compatibility
- Ensure any existing integrations or APIs handle the new naming
- Update any hardcoded references to the old naming

## Error Handling

### Validation Errors
- Form validation will reject invalid jenis_jabatan values
- Clear error messages will guide users to select valid options

### Data Consistency
- Seeder ensures consistent data structure
- Model methods handle the new naming correctly

## Performance Considerations

### No Performance Impact
- Changes are primarily cosmetic and naming-related
- No additional database queries or processing required
- Existing filtering logic remains the same

## Future Enhancements

### Potential Improvements
1. **Database Migration**: Create migration to update existing records
2. **API Documentation**: Update API documentation to reflect new naming
3. **Audit Trail**: Track changes to jenis_jabatan values
4. **Validation Enhancement**: Add more sophisticated validation rules

### Maintenance
- Monitor for any remaining references to old naming
- Update documentation as needed
- Ensure consistency across all modules

---

*This update ensures that the jenis jabatan dropdown correctly reflects the intended categorization with "Dosen dengan Tugas Tambahan" instead of "Dosen Fungsi Tambahan", maintaining consistency across the entire application.*

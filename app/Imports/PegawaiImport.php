<?php

namespace App\Imports;

use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Models\KepegawaianUniversitas\Jabatan;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importMode;
    protected $errors = [];
    protected $successCount = 0;
    protected $updateCount = 0;
    protected $createCount = 0;

    public function __construct($importMode = 'create_update')
    {
        $this->importMode = $importMode;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip if required fields are empty
            if (empty($row['nip']) || empty($row['jenis_pegawai']) || empty($row['status_kepegawaian'])) {
                $this->errors[] = "Baris dengan NIP: {$row['nip']} - Field required kosong";
                return null;
            }

            // Clean and validate NIP
            $nip = $this->cleanNip($row['nip']);
            if (!$this->validateNip($nip)) {
                $this->errors[] = "Baris dengan NIP: {$row['nip']} - NIP tidak valid (harus tepat 18 karakter dan berupa angka)";
                return null;
            }

            // Check if pegawai already exists
            $existingPegawai = Pegawai::where('nip', $nip)->first();

            // Handle based on import mode
            if ($existingPegawai && $this->importMode === 'create_only') {
                // Skip existing records in create_only mode
                return null;
            }

            if (!$existingPegawai && $this->importMode === 'update_only') {
                // Skip new records in update_only mode
                return null;
            }

            // Prepare data
            $data = $this->prepareData($row);

            // Validate prepared data
            if (!$this->validatePreparedData($data, $nip)) {
                return null;
            }

            if ($existingPegawai) {
                // Update existing pegawai
                $existingPegawai->update($data);
                $this->updateCount++;
                return null; // Don't create new model
            } else {
                // Create new pegawai
                $this->createCount++;
                return new Pegawai($data);
            }

        } catch (\Exception $e) {
            Log::error('Error importing pegawai row: ' . $e->getMessage(), [
                'row' => $row,
                'error' => $e->getTraceAsString()
            ]);
            $this->errors[] = "Error pada NIP: {$row['nip']} - {$e->getMessage()}";
            return null;
        }
    }

    /**
     * Prepare data for import
     */
    protected function prepareData(array $row)
    {
        // Get foreign key IDs - now using ID fields directly
        $pangkatId = $this->getPangkatIdFromField($row['pangkat_terakhir_id'] ?? '');
        $jabatanId = $this->getJabatanIdFromField($row['jabatan_terakhir_id'] ?? '', $row['jenis_pegawai'] ?? '');
        $unitKerjaId = $this->getUnitKerjaIdFromField($row['unit_kerja_id'] ?? '');

        // Clean and validate data
        $nip = $this->cleanNip($row['nip'] ?? '');
        
        // Validate NIP
        if (!$this->validateNip($nip)) {
            throw new \Exception("NIP tidak valid: {$row['nip']} - harus tepat 18 karakter dan berupa angka");
        }
        
        $email = $this->cleanEmail($row['email'] ?? '');
        $namaLengkap = $this->cleanString($row['nama_lengkap'] ?? '');

        $data = [
            'nip' => $nip,
            'nama_lengkap' => $namaLengkap,
            'email' => $email,
            'jenis_pegawai' => $this->cleanString($row['jenis_pegawai']),
            'status_kepegawaian' => $this->cleanString($row['status_kepegawaian']),
            'gelar_depan' => $this->cleanString($row['gelar_depan'] ?? ''),
            'gelar_belakang' => $this->cleanString($row['gelar_belakang'] ?? ''),
            'tempat_lahir' => $this->cleanString($row['tempat_lahir'] ?? ''),
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
            'jenis_kelamin' => $this->cleanString($row['jenis_kelamin'] ?? 'Laki-Laki'),
            'nomor_handphone' => $this->cleanPhoneNumber($row['nomor_handphone'] ?? ''),
            'pangkat_terakhir_id' => $pangkatId,
            'jabatan_terakhir_id' => $jabatanId,
            'unit_kerja_id' => $unitKerjaId,
            'pendidikan_terakhir' => $this->cleanString($row['pendidikan_terakhir'] ?? ''),
            'nama_universitas_sekolah' => $this->cleanString($row['nama_universitas_sekolah'] ?? ''),
            'nama_prodi_jurusan' => $this->cleanString($row['nama_prodi_jurusan'] ?? ''),
            'tmt_cpns' => $this->parseDate($row['tmt_cpns'] ?? null),
            'tmt_pns' => $this->parseDate($row['tmt_pns'] ?? null),
            'tmt_pangkat' => $this->parseDate($row['tmt_pangkat'] ?? null),
            'tmt_jabatan' => $this->parseDate($row['tmt_jabatan'] ?? null),
            'nomor_kartu_pegawai' => $this->cleanString($row['nomor_kartu_pegawai'] ?? ''),
            'nuptk' => $this->cleanString($row['nuptk'] ?? ''),
            'mata_kuliah_diampu' => $this->cleanString($row['mata_kuliah_diampu'] ?? ''),
            'ranting_ilmu_kepakaran' => $this->cleanString($row['ranting_ilmu_kepakaran'] ?? ''),
            'url_profil_sinta' => $this->cleanUrl($row['url_profil_sinta'] ?? ''),
            'predikat_kinerja_tahun_pertama' => $this->cleanString($row['predikat_kinerja_tahun_pertama'] ?? null),
            'predikat_kinerja_tahun_kedua' => $this->cleanString($row['predikat_kinerja_tahun_kedua'] ?? null),
            'nilai_konversi' => $this->parseNumeric($row['nilai_konversi'] ?? null),
            'password' => Hash::make($nip), // Default password is NIP
        ];
        
        return $data;
    }

    /**
     * Get pangkat ID from field (new method for ID-based import)
     */
    protected function getPangkatIdFromField($pangkatId)
    {
        if (empty($pangkatId)) {
            return Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1;
        }

        // Convert to integer and validate
        $id = (int) $this->convertScientificNotation($pangkatId);
        
        // Check if pangkat exists
        $pangkat = Pangkat::find($id);
        return $pangkat ? $pangkat->id : (Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1);
    }

    /**
     * Get jabatan ID from field (new method for ID-based import)
     */
    protected function getJabatanIdFromField($jabatanId, $jenisPegawai)
    {
        if (empty($jabatanId)) {
            return Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1;
        }

        // Convert to integer and validate
        $id = (int) $this->convertScientificNotation($jabatanId);
        
        // Check if jabatan exists and matches jenis pegawai
        $jabatan = Jabatan::where('id', $id)
                         ->where('jenis_pegawai', $jenisPegawai)
                         ->first();

        return $jabatan ? $jabatan->id : (Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1);
    }

    /**
     * Get unit kerja ID from field (new method for ID-based import)
     */
    protected function getUnitKerjaIdFromField($unitKerjaId)
    {
        if (empty($unitKerjaId)) {
            return SubSubUnitKerja::first()?->id ?? 1;
        }

        // Convert to integer and validate
        $id = (int) $this->convertScientificNotation($unitKerjaId);
        
        // Check if unit kerja exists
        $unitKerja = SubSubUnitKerja::find($id);
        return $unitKerja ? $unitKerja->id : (SubSubUnitKerja::first()?->id ?? 1);
    }

    /**
     * Get pangkat ID by name (legacy method - kept for backward compatibility)
     */
    protected function getPangkatId($pangkatName)
    {
        if (empty($pangkatName)) {
            return Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1;
        }

        $pangkat = Pangkat::where('pangkat', $pangkatName)->first();
        return $pangkat ? $pangkat->id : (Pangkat::where('status_pangkat', 'PNS')->first()?->id ?? 1);
    }

    /**
     * Get jabatan ID by name and jenis pegawai (legacy method - kept for backward compatibility)
     */
    protected function getJabatanId($jabatanName, $jenisPegawai)
    {
        if (empty($jabatanName)) {
            return Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1;
        }

        $jabatan = Jabatan::where('jabatan', $jabatanName)
                         ->where('jenis_pegawai', $jenisPegawai)
                         ->first();

        return $jabatan ? $jabatan->id : (Jabatan::where('jenis_pegawai', $jenisPegawai)->first()?->id ?? 1);
    }

    /**
     * Get unit kerja ID by name (legacy method - kept for backward compatibility)
     */
    protected function getUnitKerjaId($unitKerjaName)
    {
        if (empty($unitKerjaName)) {
            return SubSubUnitKerja::first()?->id ?? 1;
        }

        $unitKerja = SubSubUnitKerja::whereHas('subUnitKerja.unitKerja', function($q) use ($unitKerjaName) {
            $q->where('nama', $unitKerjaName);
        })->first();

        return $unitKerja ? $unitKerja->id : (SubSubUnitKerja::first()?->id ?? 1);
    }

    /**
     * Parse date string
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === null || $dateString === '') {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($dateString);
            
            // Check if date is reasonable (not too far in the past or future)
            $minDate = \Carbon\Carbon::create(1900, 1, 1);
            $maxDate = \Carbon\Carbon::now()->addYears(10);
            
            if ($date->lt($minDate) || $date->gt($maxDate)) {
                return null;
            }
            
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse numeric value
     */
    protected function parseNumeric($value)
    {
        if (empty($value) || $value === null) {
            return null;
        }

        try {
            // Convert to float and round to 2 decimal places
            $numeric = (float) str_replace(',', '.', $value);
            return round($numeric, 2);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean NIP data
     */
    protected function cleanNip($nip)
    {
        if (empty($nip)) {
            return '';
        }

        // Convert scientific notation and remove non-numeric characters except spaces
        $nip = $this->convertScientificNotation($nip);
        $nip = preg_replace('/[^0-9\s]/', '', $nip);
        $nip = trim($nip);

        // Remove spaces only - let validation handle length checking
        $nip = str_replace(' ', '', $nip);
        return $nip;
    }

    /**
     * Validate NIP (must be exactly 18 characters and numeric)
     */
    protected function validateNip($nip)
    {
        if (empty($nip)) {
            return false;
        }

        // Check if exactly 18 characters
        if (strlen($nip) !== 18) {
            return false;
        }

        // Check if only numeric
        if (!is_numeric($nip)) {
            return false;
        }

        return true;
    }

    /**
     * Clean email data
     */
    protected function cleanEmail($email)
    {
        if (empty($email)) {
            return '';
        }

        $email = trim($email);
        $email = strtolower($email);

        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        return $email;
    }

    /**
     * Clean string data
     */
    protected function cleanString($string)
    {
        if (empty($string)) {
            return '';
        }

        // Convert scientific notation and remove prefix if present
        $string = $this->convertScientificNotation($string);
        
        // Trim whitespace and normalize spaces
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string);

        return $string;
    }

    /**
     * Clean phone number data
     */
    protected function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Convert scientific notation and remove non-numeric characters
        $phone = $this->convertScientificNotation($phone);
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Add +62 prefix if it starts with 08
        if (preg_match('/^08/', $phone)) {
            $phone = '+62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Clean URL data
     */
    protected function cleanUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        $url = trim($url);

        // Add protocol if missing
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        return $url;
    }

    /**
     * Validate prepared data before saving
     */
    protected function validatePreparedData($data, $nip)
    {
        // Check required fields
        if (empty($data['nama_lengkap'])) {
            $this->errors[] = "NIP: {$nip} - Nama Lengkap tidak boleh kosong";
            return false;
        }

        if (empty($data['email'])) {
            $this->errors[] = "NIP: {$nip} - Email tidak boleh kosong";
            return false;
        }

        // Check email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "NIP: {$nip} - Format email tidak valid";
            return false;
        }

        // Check jenis pegawai
        if (!in_array($data['jenis_pegawai'], ['Dosen', 'Tenaga Kependidikan'])) {
            $this->errors[] = "NIP: {$nip} - Jenis Pegawai tidak valid";
            return false;
        }

        // Check status kepegawaian
        $validStatuses = [
            'Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN',
            'Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN'
        ];
        if (!in_array($data['status_kepegawaian'], $validStatuses)) {
            $this->errors[] = "NIP: {$nip} - Status Kepegawaian tidak valid";
            return false;
        }

        // Check foreign key relationships
        if (!empty($data['pangkat_terakhir_id']) && !Pangkat::find($data['pangkat_terakhir_id'])) {
            $this->errors[] = "NIP: {$nip} - Pangkat ID tidak ditemukan";
            return false;
        }

        if (!empty($data['jabatan_terakhir_id']) && !Jabatan::find($data['jabatan_terakhir_id'])) {
            $this->errors[] = "NIP: {$nip} - Jabatan ID tidak ditemukan";
            return false;
        }

        if (!empty($data['unit_kerja_id']) && !SubSubUnitKerja::find($data['unit_kerja_id'])) {
            $this->errors[] = "NIP: {$nip} - Unit Kerja ID tidak ditemukan";
            return false;
        }

        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nip' => 'required|max:19', // +1 untuk prefix single quote
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'status_kepegawaian' => 'required|string|in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
            'nomor_handphone' => 'nullable|max:20',
            'pangkat_terakhir_id' => 'nullable',
            'jabatan_terakhir_id' => 'nullable',
            'unit_kerja_id' => 'nullable',
            'pendidikan_terakhir' => 'nullable|string|max:100|in:Sekolah Dasar (SD),Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat,Sekolah Lanjutan Tingkat Menengah (SLTA),Diploma I,Diploma II,Diploma III,Sarjana (S1) / Diploma IV / Sederajat,Magister (S2) / Sederajat,Doktor (S3) / Sederajat',
            'nama_universitas_sekolah' => 'nullable|string|max:255',
            'nama_prodi_jurusan' => 'nullable|string|max:255',
            'tmt_cpns' => 'nullable|string',
            'tmt_pns' => 'nullable|string',
            'tmt_pangkat' => 'nullable|string',
            'tmt_jabatan' => 'nullable|string',
            'nomor_kartu_pegawai' => 'nullable|max:50',
            'nuptk' => 'nullable|max:20',
            'mata_kuliah_diampu' => 'nullable|string',
            'ranting_ilmu_kepakaran' => 'nullable|string',
            'url_profil_sinta' => 'nullable|string|max:500',
            'predikat_kinerja_tahun_pertama' => 'nullable|in:Sangat Baik,Baik,Perlu Perbaikan',
            'predikat_kinerja_tahun_kedua' => 'nullable|in:Sangat Baik,Baik,Perlu Perbaikan',
            'nilai_konversi' => 'nullable',
        ];
    }

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import statistics
     */
    public function getStatistics()
    {
        return [
            'created' => $this->createCount,
            'updated' => $this->updateCount,
            'errors' => count($this->errors),
            'failures' => count($this->failures()),
        ];
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Convert scientific notation to string
     */
    private function convertScientificNotation($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Convert to string first
        $value = (string) $value;

        // Check if it's in scientific notation
        if (preg_match('/^[\d,]+\.?\d*E[+-]\d+$/i', $value)) {
            // Convert scientific notation to decimal
            $number = (float) str_replace(',', '.', $value);
            $formatted = number_format($number, 0, '', '');
            return $formatted;
        }

        // Remove apostrophe prefix if present
        if (strpos($value, "'") === 0) {
            $value = substr($value, 1);
        }

        return $value;
    }
}

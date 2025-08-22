<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\UsulanJabatanSenat;
use App\Models\BackendUnivUsulan\UsulanDokumen;
use Carbon\Carbon;

class Usulan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulans';

    /**
     * PERBAIKAN 1: Definisi Primary Key Eksplisit
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pegawai_id',
        'periode_usulan_id',
        'jenis_usulan',
        'jabatan_lama_id',
        'jabatan_tujuan_id',
        'status_usulan',
        'status_kepegawaian',
        'data_usulan',
        'validasi_data',
        'catatan_verifikator',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_usulan' => 'array',
        'validasi_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_badge_class',
        'can_edit',
        'is_read_only',
        'formatted_created_date',
        'days_since_created'
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    /**
     * Relasi ke Pegawai yang mengajukan usulan.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Relasi ke Periode Usulan.
     */
    public function periodeUsulan(): BelongsTo
    {
        return $this->belongsTo(PeriodeUsulan::class);
    }

    /**
     * Check if periode can be accessed by specific role
     * Periode hanya dapat dilihat oleh Tim Senat dan Penilai Universitas 
     * apabila Admin Univ Usulan sudah mengirimkan usulan ke masing-masing role tersebut.
     */
    public function canAccessPeriode($role): bool
    {
        // Admin Univ Usulan selalu dapat mengakses periode
        if ($role === 'Admin Univ Usulan') {
            return true;
        }

        // Tim Senat dapat mengakses periode jika usulan sudah direkomendasikan
        if ($role === 'Tim Senat') {
            return in_array($this->status_usulan, [
                'Direkomendasikan',
                'Disetujui',
                'Ditolak',
                'Diusulkan ke Sister',
                'Perbaikan dari Tim Sister'
            ]);
        }

        // Penilai Universitas dapat mengakses periode jika usulan sudah dikirim ke penilai
        if ($role === 'Penilai Universitas') {
            return in_array($this->status_usulan, [
                'Sedang Direview',
                'Direkomendasikan',
                'Perbaikan Usulan',
                'Sedang Dinilai'
            ]);
        }

        // Tim Penilai dapat mengakses periode jika usulan sudah dikirim ke penilai
        if ($role === 'Tim Penilai') {
            return in_array($this->status_usulan, [
                'Sedang Direview',
                'Direkomendasikan',
                'Perbaikan Usulan',
                'Sedang Dinilai'
            ]);
        }

        // Role lain tidak dapat mengakses periode
        return false;
    }

    /**
     * Get periode information with access control
     */
    public function getPeriodeInfo($role): array
    {
        if (!$this->canAccessPeriode($role)) {
            return [
                'nama_periode' => 'Tidak dapat diakses',
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'status' => 'restricted',
                'message' => 'Periode hanya dapat diakses oleh Tim Senat dan Penilai Universitas setelah usulan dikirim'
            ];
        }

        return [
            'nama_periode' => $this->periodeUsulan?->nama_periode ?? 'N/A',
            'tanggal_mulai' => $this->periodeUsulan?->tanggal_mulai,
            'tanggal_selesai' => $this->periodeUsulan?->tanggal_selesai,
            'status' => 'accessible',
            'message' => null
        ];
    }

    /**
     * Relasi ke Jabatan (jabatan lama).
     */
    public function jabatanLama(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_lama_id');
    }

    /**
     * Relasi ke Jabatan (jabatan tujuan).
     */
    public function jabatanTujuan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_tujuan_id');
    }

    /**
     * Relasi ke dokumen-dokumen pendukung.
     */
    public function dokumens(): HasMany
    {
        return $this->hasMany(UsulanDokumen::class);
    }

    /**
     * Relasi ke log riwayat usulan.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(UsulanLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relasi many-to-many ke Penilai.
     */
    public function penilais(): BelongsToMany
    {
        return $this->belongsToMany(Penilai::class, 'usulan_penilai', 'usulan_id', 'penilai_id')
                    ->withPivot('status_penilaian', 'catatan_penilaian')
                    ->withTimestamps();
    }

    /**
     * Check if usulan is assigned to specific penilai.
     */
    public function isAssignedToPenilai($penilaiId): bool
    {
        return $this->penilais()->where('penilai_id', $penilaiId)->exists();
    }

    /**
     * Scope to get usulans assigned to specific penilai.
     */
    public function scopeAssignedToPenilai($query, $penilaiId)
    {
        return $query->whereHas('penilais', function ($penilaiQuery) use ($penilaiId) {
            $penilaiQuery->where('penilai_id', $penilaiId);
        });
    }

    // =====================================
    // ACCESSORS (Computed Properties)
    // =====================================

    /**
     * Get status badge CSS class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status_usulan) {
            'Draft' => 'bg-gray-100 text-gray-800',
            'Diajukan' => 'bg-blue-100 text-blue-800',
            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
            'Sedang Direview Universitas' => 'bg-yellow-100 text-yellow-800',
            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
            'Dikembalikan' => 'bg-red-100 text-red-800',
            'Dikembalikan ke Pegawai' => 'bg-red-100 text-red-800',
            'Disetujui' => 'bg-green-100 text-green-800',
            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
            'Tidak Direkomendasikan' => 'bg-red-100 text-red-800',
            'Sedang Dinilai' => 'bg-indigo-100 text-indigo-800',
            'Sedang Direview Senat' => 'bg-purple-100 text-purple-800',
            'Diusulkan ke Universitas' => 'bg-blue-100 text-blue-800',
            'Diusulkan ke Sister' => 'bg-indigo-100 text-indigo-800',
            'Perbaikan dari Tim Sister' => 'bg-orange-100 text-orange-800',
            'Ditolak' => 'bg-red-100 text-red-800',
            'Ditolak Universitas' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if usulan can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return in_array($this->status_usulan, [
            'Draft',
            'Perlu Perbaikan',
            'Dikembalikan',
            'Perbaikan dari Tim Sister'
        ]);
    }

    /**
     * Check if usulan is read-only
     */
    public function getIsReadOnlyAttribute(): bool
    {
        return in_array($this->status_usulan, [
            'Diajukan',
            'Sedang Direview',
            'Disetujui',
            'Direkomendasikan',
            'Diusulkan ke Sister'
        ]);
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedCreatedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->isoFormat('D MMMM YYYY') : '-';
    }

    /**
     * Get days since created
     */
    public function getDaysSinceCreatedAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }



    // =====================================
    // DATA USULAN ACCESSORS
    // =====================================

    /**
     * Get karya ilmiah data from data_usulan
     */


    // =====================================
    // DOCUMENT HELPER METHODS
    // =====================================

    /**
     * Check if specific document exists (supports both old and new structure)
     */
    public function hasDocument(string $documentName): bool
    {
        // Check new structure
        if (!empty($this->data_usulan['dokumen_usulan'][$documentName]['path'])) {
            return true;
        }

        // Check old structure (fallback)
        if (!empty($this->data_usulan[$documentName])) {
            return true;
        }

        return false;
    }

    /**
     * Get document path for a given document name
     * ENHANCED: Better handling for BKD documents and legacy mapping
     */
    public function getDocumentPath(string $documentName): ?string
    {
        // Check new structure first
        if (!empty($this->data_usulan['dokumen_usulan'][$documentName]['path'])) {
            return $this->data_usulan['dokumen_usulan'][$documentName]['path'];
        }

        // Check if it's a BKD semester field and try legacy mapping
        if (str_starts_with($documentName, 'bkd_semester_') && $this->periodeUsulan) {
            $num = (int) str_replace('bkd_semester_', '', $documentName);
            if ($num >= 1 && $num <= 4) {
                // Get BKD labels to find the corresponding legacy key
                $labels = $this->getBkdDisplayLabels();
                if (isset($labels[$documentName])) {
                    $label = $labels[$documentName];
                    // Parse label to get semester and year
                    if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $label, $m)) {
                        $sem = strtolower($m[1]); // ganjil|genap
                        $y1 = $m[2];
                        $y2 = $m[3];

                        // Try legacy key format
                        $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;

                        // Check new structure with legacy key
                        if (!empty($this->data_usulan['dokumen_usulan'][$legacyKey]['path'])) {
                            return $this->data_usulan['dokumen_usulan'][$legacyKey]['path'];
                        }

                        // Check old structure with legacy key
                        if (!empty($this->data_usulan[$legacyKey])) {
                            return $this->data_usulan[$legacyKey];
                        }

                        // Scan all BKD keys in dokumen_usulan
                        if (!empty($this->data_usulan['dokumen_usulan'])) {
                            foreach ($this->data_usulan['dokumen_usulan'] as $k => $info) {
                                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                        return is_array($info) ? ($info['path'] ?? null) : $info;
                                    }
                                }
                            }
                        }

                        // Scan all BKD keys in flat structure
                        if (!empty($this->data_usulan)) {
                            foreach ($this->data_usulan as $k => $info) {
                                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                        return is_array($info) ? ($info['path'] ?? null) : $info;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Fallback to old structure
        if (!empty($this->data_usulan[$documentName])) {
            return $this->data_usulan[$documentName];
        }

        return null;
    }

    /**
     * Get all document names that exist
     */
    public function getExistingDocuments(): array
    {
        $documents = [];
        $documentKeys = [
            'pakta_integritas',
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'bukti_syarat_guru_besar'
        ];

        foreach ($documentKeys as $key) {
            if ($this->hasDocument($key)) {
                $documents[] = $key;
            }
        }

        return $documents;
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status_usulan', $status);
    }

    /**
     * Scope untuk filter by jenis usulan
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_usulan', $jenis);
    }

    /**
     * Scope untuk filter by pegawai
     */
    public function scopeByPegawai($query, int $pegawaiId)
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    /**
     * Scope untuk filter by periode
     */
    public function scopeByPeriode($query, int $periodeId)
    {
        return $query->where('periode_usulan_id', $periodeId);
    }

    /**
     * Scope untuk usulan yang bisa diedit
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('status_usulan', ['Draft', 'Perlu Perbaikan', 'Dikembalikan']);
    }

    /**
     * Scope untuk usulan yang read-only
     */
    public function scopeReadOnly($query)
    {
        return $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview', 'Disetujui', 'Direkomendasikan']);
    }

    /**
     * Scope untuk usulan yang masih aktif (belum selesai)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak']);
    }

    /**
     * Scope untuk usulan yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status_usulan', ['Direkomendasikan', 'Ditolak']);
    }

    /**
     * Scope untuk recent usulan
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if usulan is for jabatan promotion
     */
    public function isJabatanPromotion(): bool
    {
        return $this->jenis_usulan === 'jabatan';
    }

    /**
     * Check if usulan is for pangkat promotion
     */
    public function isPangkatPromotion(): bool
    {
        return $this->jenis_usulan === 'pangkat';
    }

    /**
     * Check if usulan is draft
     */
    public function isDraft(): bool
    {
        return $this->status_usulan === 'Draft';
    }

    /**
     * Check if usulan is submitted
     */
    public function isSubmitted(): bool
    {
        return !in_array($this->status_usulan, ['Draft']);
    }

    /**
     * Check if usulan is approved
     */
    public function isApproved(): bool
    {
        return in_array($this->status_usulan, ['Disetujui', 'Direkomendasikan']);
    }

    /**
     * Check if usulan is rejected
     */
    public function isRejected(): bool
    {
        return $this->status_usulan === 'Ditolak';
    }

    /**
     * Check if usulan needs revision
     */
    public function needsRevision(): bool
    {
        return in_array($this->status_usulan, ['Perlu Perbaikan', 'Dikembalikan']);
    }

    /**
     * Get latest log entry
     */
    public function getLatestLog(): ?UsulanLog
    {
        return $this->logs()->first();
    }

    /**
     * Get submission summary for display
     */
    public function getSubmissionSummary(): array
    {
        return [
            'id' => $this->id,
            'jenis_usulan' => $this->jenis_usulan,
            'status_usulan' => $this->status_usulan,
            'status_badge_class' => $this->status_badge_class,
            'can_edit' => $this->can_edit,
            'is_read_only' => $this->is_read_only,
            'formatted_created_date' => $this->formatted_created_date,
            'days_since_created' => $this->days_since_created,
            'pegawai_name' => $this->pegawai?->nama_lengkap,
            'jabatan_lama' => $this->jabatanLama?->jabatan,
            'jabatan_tujuan' => $this->jabatanTujuan?->jabatan,
            'periode_nama' => $this->periodeUsulan?->nama_periode,
            'existing_documents' => $this->getExistingDocuments(),
            'latest_log' => $this->getLatestLog()?->getFormattedEntry(),
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // PERBAIKAN: Tambahkan pengecekan untuk memastikan model sudah persisted
        static::created(function ($usulan) {
            // Pastikan model sudah di-save dan memiliki ID sebelum membuat log
            if ($usulan && $usulan->exists && $usulan->getKey()) {
                try {
                    UsulanLog::create([
                        'usulan_id' => $usulan->getKey(), // Gunakan getKey() lebih aman
                        'status_sebelumnya' => null,
                        'status_baru' => $usulan->status_usulan,
                        'catatan' => 'Usulan dibuat dengan status ' . $usulan->status_usulan,
                        'dilakukan_oleh_id' => $usulan->pegawai_id,
                    ]);
                } catch (\Throwable $e) {
                    // Log error tapi jangan stop proses utama
                    \Log::error('Gagal membuat usulan log: ' . $e->getMessage(), [
                        'usulan_id' => $usulan->getKey(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }

    /**
     * Create log entry for this usulan
     */
    public function createLog(string $statusBaru, ?string $statusSebelumnya = null, ?string $catatan = null, ?int $userId = null): UsulanLog
    {
        // PERBAIKAN: Pastikan model sudah memiliki ID
        if (!$this->exists || !$this->getKey()) {
            throw new \RuntimeException('Cannot create log for usulan that hasn\'t been saved yet');
        }

        $userId = $userId ?? optional(auth()->guard('pegawai')->user())->id ?? $this->pegawai_id;

        if (!$catatan) {
            if ($statusSebelumnya && $statusSebelumnya !== $statusBaru) {
                $catatan = "Status diubah dari {$statusSebelumnya} ke {$statusBaru}";
            } else {
                $catatan = "Status diperbarui menjadi {$statusBaru}";
            }
        }

        return UsulanLog::create([
            'usulan_id' => $this->getKey(), // Gunakan getKey() lebih aman daripada $this->id
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'dilakukan_oleh_id' => $userId,
        ]);
    }

    // =====================================
    // DEBUG METHOD - PERBAIKAN 4
    // =====================================

    /**
     * Debug method untuk membantu troubleshooting
     */
    public function debugModelState(): array
    {
        return [
            'exists' => $this->exists,
            'primary_key_name' => $this->getKeyName(),
            'primary_key_value' => $this->getKey(),
            'table_name' => $this->getTable(),
            'attributes' => $this->getAttributes(),
            'original' => $this->getOriginal(),
            'dirty' => $this->getDirty(),
            'was_recently_created' => $this->wasRecentlyCreated,
        ];
    }
    // =====================================
    // VALIDASI DATA HELPER METHODS
    // =====================================

    /**
     * Mendapatkan data validasi untuk role tertentu
     */
    public function getValidasiByRole(string $role): array
    {
        $roleData = $this->validasi_data[$role] ?? [];

        // If the role data exists but doesn't have 'validation' key, return empty array
        // This handles both old and new data structures
        if (isset($roleData['validation'])) {
            return $roleData;
        }

        // For backward compatibility, if data exists but no 'validation' key, return as is
        return $roleData;
    }

    /**
     * Set data validasi untuk role tertentu
     */
    public function setValidasiByRole(string $role, array $validasiData, int $validatedBy): void
    {
        $currentValidasi = $this->validasi_data ?? [];

        // Preserve existing validation data and merge with new data
        $existingValidation = $currentValidasi[$role] ?? [];
        $existingValidationData = $existingValidation['validation'] ?? [];

        // Deep merge validation data
        foreach ($validasiData as $category => $fields) {
            if (!isset($existingValidationData[$category])) {
                $existingValidationData[$category] = [];
            }

            foreach ($fields as $field => $fieldData) {
                $existingValidationData[$category][$field] = $fieldData;
            }
        }

        // Update validation structure - preserve existing data like dokumen_pendukung
        $currentValidasi[$role] = array_merge($existingValidation, [
            'validation' => $existingValidationData,
            'validated_by' => $validatedBy,
            'validated_at' => now()->toISOString()
        ]);

        $this->validasi_data = $currentValidasi;
    }

    /**
     * Cek apakah usulan sudah divalidasi oleh role tertentu
     */
    public function isValidatedByRole(string $role): bool
    {
        return !empty($this->validasi_data[$role]['validated_by']);
    }

    /**
     * Mendapatkan status validasi untuk field tertentu dari role tertentu
     */
    public function getFieldValidationStatus(string $role, string $category, string $field): string
    {
        return $this->validasi_data[$role][$category][$field]['status'] ?? 'sesuai';
    }

    /**
     * Mendapatkan keterangan validasi untuk field tertentu dari role tertentu
     */
    public function getFieldValidationKeterangan(string $role, string $category, string $field): string
    {
        return $this->validasi_data[$role][$category][$field]['keterangan'] ?? '';
    }

    /**
     * Cek apakah ada field yang tidak sesuai dari role tertentu
     */
    public function hasInvalidFields(string $role): bool
    {
        $validasi = $this->getValidasiByRole($role);

        foreach ($validasi as $category => $fields) {
            if (in_array($category, ['validated_by', 'validated_at', 'dokumen_pendukung'])) {
                continue;
            }

            foreach ($fields as $field => $data) {
                if (($data['status'] ?? 'sesuai') === 'tidak_sesuai') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Mendapatkan daftar field yang tidak sesuai dari role tertentu
     */
    public function getInvalidFields(string $role): array
    {
        $invalidFields = [];
        $validasi = $this->getValidasiByRole($role);

        foreach ($validasi as $category => $fields) {
            if (in_array($category, ['validated_by', 'validated_at'])) {
                continue;
            }

            foreach ($fields as $field => $data) {
                if (($data['status'] ?? 'sesuai') === 'tidak_sesuai') {
                    $invalidFields[] = [
                        'category' => $category,
                        'field' => $field,
                        'keterangan' => $data['keterangan'] ?? ''
                    ];
                }
            }
        }

        return $invalidFields;
    }

    /**
     * Mendapatkan informasi validator dari role tertentu
     */
    public function getValidatorInfo(string $role): ?array
    {
        $validasi = $this->getValidasiByRole($role);

        if (empty($validasi['validated_by'])) {
            return null;
        }

        return [
            'validated_by' => $validasi['validated_by'],
            'validated_at' => $validasi['validated_at'] ?? null,
            'validator_name' => null // Bisa ditambah query ke tabel pegawai jika perlu
        ];
    }

    /**
     * Reset validasi untuk role tertentu
     */
    public function resetValidasiByRole(string $role): void
    {
        $currentValidasi = $this->validasi_data ?? [];
        unset($currentValidasi[$role]);
        $this->validasi_data = $currentValidasi;
    }

    /**
     * Mendapatkan semua kategori dan field yang perlu divalidasi
     */
    public static function getValidationFields(): array
    {
        return [
            'data_pribadi' => [
                'jenis_pegawai', 'status_kepegawaian', 'nip', 'nuptk', 'gelar_depan',
                'nama_lengkap', 'gelar_belakang', 'email', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'nomor_handphone'
            ],
            'data_kepegawaian' => [
                'pangkat_saat_usul', 'tmt_pangkat', 'jabatan_saat_usul', 'tmt_jabatan',
                'tmt_cpns', 'tmt_pns', 'unit_kerja_saat_usul'
            ],
            'data_pendidikan' => [
                'pendidikan_terakhir', 'mata_kuliah_diampu', 'ranting_ilmu_kepakaran', 'url_profil_sinta'
            ],
            'data_kinerja' => [
                'predikat_kinerja_tahun_pertama', 'predikat_kinerja_tahun_kedua', 'nilai_konversi'
            ],
            'dokumen_profil' => [
                'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua', 'pak_konversi',
                'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir'
            ],
            'karya_ilmiah' => [
                'karya_ilmiah', 'nama_jurnal', 'judul_artikel', 'penerbit_artikel', 'volume_artikel',
                'nomor_artikel', 'edisi_artikel', 'halaman_artikel', 'link_artikel', 'link_sinta',
                'link_scopus', 'link_scimago', 'link_wos'
            ],
            'dokumen_usulan' => [
                'pakta_integritas', 'bukti_korespondensi', 'turnitin', 'upload_artikel', 'bukti_syarat_guru_besar'
            ],
            'dokumen_bkd' => [
                'bkd_semester_1',
                'bkd_semester_2',
                'bkd_semester_3',
                'bkd_semester_4'
            ],
            'dokumen_pendukung' => [
                'nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'
            ]
        ];
    }

    /**
     * Get validation fields with dynamic BKD
     * FIXED: Dokumen pendukung hanya untuk admin_universitas, penilai, dan senat
     */
    public static function getValidationFieldsWithDynamicBkd($usulan = null, $userRole = null): array
    {
        // Get base fields
        $fields = self::getValidationFields();

        // If usulan provided, check for additional BKD fields in data_usulan
        if ($usulan && $usulan->periodeUsulan) {
            // Generate BKD fields based on periode
            $bkdFields = $usulan->generateBkdFieldNames();

            // Update dokumen_bkd with dynamic fields
            $fields['dokumen_bkd'] = $bkdFields;
        }

        // FIXED: Dokumen pendukung hanya untuk role tertentu
        if (in_array($userRole, ['admin_universitas', 'penilai', 'senat'])) {
            // Keep dokumen_pendukung for admin universitas, penilai, dan senat
            // It's already included in getValidationFields()
        } else {
            // Remove dokumen_pendukung for admin fakultas and other roles
            unset($fields['dokumen_pendukung']);
        }

        return $fields;
    }

    /**
     * Generate BKD field names based on periode
     * FIXED: Use same logic as controller to ensure consistency
     */
    public function generateBkdFieldNames(): array
    {
        if (!$this->periodeUsulan) {
            return ['bkd_semester_1', 'bkd_semester_2', 'bkd_semester_3', 'bkd_semester_4'];
        }

        $startDate = \Carbon\Carbon::parse($this->periodeUsulan->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester based on month
        $currentSemester = '';
        $currentYear = 0;

        if ($month >= 1 && $month <= 6) {
            // Januari - Juni: Semester Genap sedang berjalan
            $currentSemester = 'Genap';
            $currentYear = $year - 1; // Tahun akademik dimulai tahun sebelumnya
        } elseif ($month >= 7 && $month <= 12) {
            // Juli - Desember: Semester Ganjil sedang berjalan
            $currentSemester = 'Ganjil';
            $currentYear = $year;
        }

        // NEW LOGIC: Mundur 2 semester dari periode saat ini untuk titik awal BKD
        $bkdStartSemester = $currentSemester;
        $bkdStartYear = $currentYear;

        // Mundur 2 semester
        for ($i = 0; $i < 2; $i++) {
            if ($bkdStartSemester === 'Ganjil') {
                $bkdStartSemester = 'Genap';
                $bkdStartYear--;
            } else {
                $bkdStartSemester = 'Ganjil';
            }
        }

        // Generate 4 semester BKD mulai dari titik awal (mundur)
        $fields = [];
        $tempSemester = $bkdStartSemester;
        $tempYear = $bkdStartYear;

        for ($i = 1; $i <= 4; $i++) {
            $fields[] = 'bkd_semester_' . $i;

            // Move to previous semester (mundur)
            if ($tempSemester === 'Ganjil') {
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                $tempSemester = 'Ganjil';
            }
        }

        return $fields;
    }

    /**
     * Get BKD display labels based on periode
     * FIXED: Use same logic as controller to ensure consistency
     */
    public function getBkdDisplayLabels(): array
    {
        if (!$this->periodeUsulan) {
            return [
                'bkd_semester_1' => 'BKD Semester 1',
                'bkd_semester_2' => 'BKD Semester 2',
                'bkd_semester_3' => 'BKD Semester 3',
                'bkd_semester_4' => 'BKD Semester 4',
            ];
        }

        $startDate = \Carbon\Carbon::parse($this->periodeUsulan->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester based on month
        $currentSemester = '';
        $currentYear = 0;

        if ($month >= 1 && $month <= 6) {
            // Januari - Juni: Semester Genap sedang berjalan
            $currentSemester = 'Genap';
            $currentYear = $year - 1; // Tahun akademik dimulai tahun sebelumnya
        } elseif ($month >= 7 && $month <= 12) {
            // Juli - Desember: Semester Ganjil sedang berjalan
            $currentSemester = 'Ganjil';
            $currentYear = $year;
        }

        // NEW LOGIC: Mundur 2 semester dari periode saat ini untuk titik awal BKD
        $bkdStartSemester = $currentSemester;
        $bkdStartYear = $currentYear;

        // Mundur 2 semester
        for ($i = 0; $i < 2; $i++) {
            if ($bkdStartSemester === 'Ganjil') {
                $bkdStartSemester = 'Genap';
                $bkdStartYear--;
            } else {
                $bkdStartSemester = 'Ganjil';
            }
        }

        // Generate 4 semester BKD mulai dari titik awal (mundur)
        $labels = [];
        $tempSemester = $bkdStartSemester;
        $tempYear = $bkdStartYear;

        for ($i = 1; $i <= 4; $i++) {
            $academicYear = $tempYear . '/' . ($tempYear + 1);
            $labels['bkd_semester_' . $i] = "BKD Semester {$tempSemester} {$academicYear}";

            // Move to previous semester (mundur)
            if ($tempSemester === 'Ganjil') {
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                $tempSemester = 'Ganjil';
            }
        }

        return $labels;
    }

    public function senatDecisions()
{
    return $this->hasMany(UsulanJabatanSenat::class, 'usulan_id');
}

/** Ambang minimal setuju dari periode (fallback 1) */
public function getSenateMinSetuju(): int
{
    return (int) ($this->periodeUsulan->senat_min_setuju ?? 1);
}

/** Hitung keputusan Senat (direkomendasikan = setuju, belum_direkomendasikan = tolak) */
public function getSenateDecisionCounts(): array
{
    $total = $this->senatDecisions()->count();
    $setuju = $this->senatDecisions()->where('keputusan', 'direkomendasikan')->count();
    $tolak  = $this->senatDecisions()->where('keputusan', 'belum_direkomendasikan')->count();

    return [
        'total' => $total,
        'setuju' => $setuju,
        'tolak'  => $tolak,
        'belum'  => max(0, $total - $setuju - $tolak),
    ];
}

    /** Lulus Senat berdasarkan minimal setuju (default ambil dari periode) */
    public function isSenateApproved(?int $minSetuju = null): bool
    {
        $min = $minSetuju ?? $this->getSenateMinSetuju();
        $counts = $this->getSenateDecisionCounts();

        // Wajib ada data senat terlebih dulu (total > 0), lalu cek ambang setuju
        return $counts['total'] > 0 && $counts['setuju'] >= $min;
    }

    /** Sudah direkomendasikan oleh tim penilai? (cek di tabel usulan_penilai) */
    public function isRecommendedByReviewer(): bool
    {
        // OPTIMASI: Gunakan Eloquent relationship instead of raw DB query
        $total = $this->penilais()->count();

        if ($total === 0) {
            return false;
        }

        $setuju = $this->penilais()
            ->where('status_penilaian', 'Sesuai')
            ->count();

        // Hitung ambang minimal: 1 penilai -> min 1 setuju, 2 penilai -> min 2 setuju, 3 penilai -> min 2 setuju
        $threshold = (int) ceil(($total + 1) / 2);

        return $setuju >= $threshold;
    }

    // =====================================
    // QUERY SCOPES OPTIMIZATION
    // =====================================

    /**
     * Scope untuk usulan yang ditugaskan ke penilai tertentu
     */
    public function scopeAssignedToReviewer($query, int $reviewerId)
    {
        return $query->whereHas('penilais', function ($q) use ($reviewerId) {
            $q->where('penilai_id', $reviewerId);
        });
    }

    /**
     * Scope untuk usulan berdasarkan fakultas
     */
    public function scopeByFakultas($query, int $fakultasId)
    {
        return $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($q) use ($fakultasId) {
            $q->where('id', $fakultasId);
        });
    }

    /**
     * Scope untuk usulan dengan eager loading yang optimal
     */
    public function scopeWithOptimalRelations($query)
    {
        return $query->with([
            'pegawai:id,nama_lengkap,email,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id',
            'pegawai.pangkat:id,pangkat',
            'pegawai.jabatan:id,jabatan',
            'pegawai.unitKerja:id,nama',
            'jabatanLama:id,jabatan',
            'jabatanTujuan:id,jabatan',
            'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai',
            'dokumens:id,usulan_id,nama_dokumen,path',
            'logs:id,usulan_id,status_baru,catatan,created_at,dilakukan_oleh_id',
            'logs.dilakukanOleh:id,nama_lengkap'
        ]);
    }

    /**
     * Scope untuk usulan yang memerlukan validasi
     */
    public function scopeNeedsValidation($query)
    {
        return $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview']);
    }

    /**
     * Scope untuk usulan berdasarkan periode aktif
     */
    public function scopeActivePeriod($query)
    {
        return $query->whereHas('periodeUsulan', function ($q) {
            $q->where('status', 'Buka')
              ->where('tanggal_mulai', '<=', now())
              ->where('tanggal_selesai', '>=', now());
        });
    }

    // =====================================
    // CACHE OPTIMIZATION
    // =====================================

    /**
     * Cache key untuk usulan
     */
    public function getCacheKey(): string
    {
        return "usulan_{$this->id}_v" . $this->updated_at->timestamp;
    }

    /**
     * Clear cache when usulan is updated
     */
    protected static function booted()
    {
        static::updated(function ($usulan) {
            \Cache::forget($usulan->getCacheKey());
        });

        static::deleted(function ($usulan) {
            \Cache::forget($usulan->getCacheKey());
        });
    }

    // Status constants for Tim Penilai assessment
    const STATUS_PERBAIKAN_DARI_TIM_PENILAI = 'Perbaikan Dari Tim Penilai';
    const STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI = 'Usulan Direkomendasi Tim Penilai';
    const STATUS_TIDAK_DIREKOMENDASI = 'Tidak Direkomendasikan';
    const STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI = 'Menunggu Hasil Penilaian Tim Penilai';

    /**
     * Determine final status based on Tim Penilai assessment results
     */
    public function determinePenilaiFinalStatus()
    {
        $penilais = $this->penilais;
        $totalPenilai = $penilais->count();
        
        if ($totalPenilai === 0) {
            return null; // No penilai assigned
        }
        
        // Check if all penilai have completed their assessment
        $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
        
        // If not all penilai have completed, return intermediate status
        if ($completedPenilai < $totalPenilai) {
            return self::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;
        }
        
        // If any penilai gives 'perbaikan', result is perbaikan
        $hasPerbaikan = $penilais->where('pivot.hasil_penilaian', 'perbaikan')->count() > 0;
        if ($hasPerbaikan) {
            return self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
        }
        
        // Count recommendations
        $rekomendasiCount = $penilais->where('pivot.hasil_penilaian', 'rekomendasi')->count();
        $tidakRekomendasiCount = $penilais->where('pivot.hasil_penilaian', 'tidak_rekomendasi')->count();
        
        // Logic based on number of penilai
        switch ($totalPenilai) {
            case 1:
                return $rekomendasiCount > 0 ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
                
            case 2:
                return ($rekomendasiCount == 2) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
                
            case 3:
                return ($rekomendasiCount >= 2) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
                
            default:
                // For more than 3 penilai, use majority vote
                return ($rekomendasiCount > $tidakRekomendasiCount) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
        }
    }

    /**
     * Auto-update status based on current penilai assessment progress
     * This method ensures status transitions correctly when penilai assessment changes
     */
    public function autoUpdateStatusBasedOnPenilaiProgress()
    {
        // Only auto-update if usulan is in penilai assessment phase
        $penilaiAssessmentStatuses = [
            'Sedang Direview',
            'Menunggu Hasil Penilaian Tim Penilai',
            'Perbaikan Dari Tim Penilai',
            'Usulan Direkomendasi Tim Penilai'
        ];

        if (!in_array($this->status_usulan, $penilaiAssessmentStatuses)) {
            return false; // Not in penilai assessment phase
        }

        $penilais = $this->penilais;
        $totalPenilai = $penilais->count();
        
        if ($totalPenilai === 0) {
            return false; // No penilai assigned
        }

        $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
        $newStatus = $this->determinePenilaiFinalStatus();

        // Only update if status has changed
        if ($newStatus && $newStatus !== $this->status_usulan) {
            $oldStatus = $this->status_usulan;
            $this->status_usulan = $newStatus;
            
            // Log the status transition
            \Log::info('Auto status transition for usulan', [
                'usulan_id' => $this->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'total_penilai' => $totalPenilai,
                'completed_penilai' => $completedPenilai,
                'is_intermediate' => ($completedPenilai < $totalPenilai)
            ]);

            return true; // Status was updated
        }

        return false; // No status change needed
    }

    /**
     * Check if usulan is in intermediate penilai assessment status
     */
    public function isInIntermediatePenilaiStatus()
    {
        return $this->status_usulan === self::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;
    }

    /**
     * Check if usulan is in final penilai assessment status
     */
    public function isInFinalPenilaiStatus()
    {
        return in_array($this->status_usulan, [
            self::STATUS_PERBAIKAN_DARI_TIM_PENILAI,
            self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI
        ]);
    }

    /**
     * Get penilai assessment progress information
     */
    public function getPenilaiAssessmentProgress()
    {
        $penilais = $this->penilais ?? collect();
        $totalPenilai = $penilais->count();
        $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
        
        return [
            'total_penilai' => $totalPenilai,
            'completed_penilai' => $completedPenilai,
            'remaining_penilai' => max(0, $totalPenilai - $completedPenilai),
            'progress_percentage' => $totalPenilai > 0 ? ($completedPenilai / $totalPenilai) * 100 : 0,
            'is_complete' => ($totalPenilai > 0) && ($completedPenilai === $totalPenilai),
            'is_intermediate' => ($totalPenilai > 0) && ($completedPenilai < $totalPenilai),
            'current_status' => $this->status_usulan
        ];
    }

    /**
     * Check if usulan can be submitted in current period
     */
    public function canBeSubmittedInCurrentPeriod()
    {
        // If usulan was not recommended, it cannot be submitted in current period
        if ($this->status_usulan === self::STATUS_TIDAK_DIREKOMENDASI) {
            return false;
        }
        
        // Check if current period is still open
        if ($this->periodeUsulan && $this->periodeUsulan->status === 'Buka') {
            return true;
        }
        
        return false;
    }
}

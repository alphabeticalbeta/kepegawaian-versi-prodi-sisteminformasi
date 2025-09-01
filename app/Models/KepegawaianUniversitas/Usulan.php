<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\KepegawaianUniversitas\UsulanJabatanSenat;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
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
        'pangkat_tujuan_id',
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
                self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
                self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
                self::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER,
                self::STATUS_PERBAIKAN_SUDAH_DIKIRIM_KE_SISTER,
                self::STATUS_DIREKOMENDASIKAN,
                self::STATUS_TIDAK_DIREKOMENDASIKAN,
                // Legacy status
                'Disetujui',
                'Ditolak',
                'Diusulkan ke Sister',
                'Perbaikan dari Tim Sister'
            ]);
        }

        // Penilai Universitas dapat mengakses periode jika usulan sudah dikirim ke penilai
        if ($role === 'Penilai Universitas') {
            return in_array($this->status_usulan, [
                self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_PERBAIKAN_KE_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                // Status yang memungkinkan penilai mengakses
                self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                // Legacy status
                'Sedang Direview',
                'Direkomendasikan',
                'Perbaikan Usulan',
                'Sedang Dinilai',
                'Menunggu Hasil Penilaian Tim Penilai'
            ]);
        }

        // Tim Penilai dapat mengakses periode jika usulan sudah dikirim ke penilai
        if ($role === 'Tim Penilai') {
            return in_array($this->status_usulan, [
                self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
                self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
                // Status yang memungkinkan penilai mengakses
                self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS,
                self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                // Legacy status
                'Sedang Direview',
                'Direkomendasikan',
                'Perbaikan Usulan',
                'Sedang Dinilai',
                'Menunggu Hasil Penilaian Tim Penilai'
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
     * Relasi ke Pangkat (pangkat tujuan).
     */
    public function pangkatTujuan(): BelongsTo
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_tujuan_id');
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
                    ->withPivot('status_penilaian', 'catatan_penilaian', 'hasil_penilaian', 'tanggal_penilaian')
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
            // Status standar baru
            self::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS => 'bg-blue-100 text-blue-800',
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS => 'bg-amber-100 text-amber-800',
            self::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800',
            self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800',
            self::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-blue-100 text-blue-800',
            self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS => 'bg-green-100 text-green-800',
            self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800',
            self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS => 'bg-indigo-100 text-indigo-800',
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800',
            self::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800',
            self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS => 'bg-purple-100 text-purple-800',
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS => 'bg-purple-100 text-purple-800',
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT => 'bg-purple-100 text-purple-800',
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER => 'bg-blue-100 text-blue-800',
            self::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER => 'bg-red-100 text-red-800',

            // Draft status constants
            self::STATUS_DRAFT_USULAN => 'bg-gray-100 text-gray-800',
            self::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS => 'bg-amber-100 text-amber-800',
            self::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800',
            self::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800',
            self::STATUS_DRAFT_PERBAIKAN_TIM_SISTER => 'bg-red-100 text-red-800',

            // Legacy status constants (fallback)
            self::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI => 'bg-orange-100 text-orange-800',

            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if usulan can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_DRAFT_USULAN,
            self::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS,
            self::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_TIM_SISTER,
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER
        ]);
    }

    /**
     * Check if usulan is read-only
     */
    public function getIsReadOnlyAttribute(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER
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
     * ENHANCED: Also check validasi_data for admin_fakultas dokumen_pendukung
     */
    public function getDocumentPath(string $documentName): ?string
    {
        // Check validasi_data for admin_fakultas dokumen_pendukung first
        if (!empty($this->validasi_data['admin_fakultas']['dokumen_pendukung'][$documentName])) {
            return $this->validasi_data['admin_fakultas']['dokumen_pendukung'][$documentName];
        }

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

    /**
     * Get all documents organized by category
     */
    public function getAllDocuments(): array
    {
        return [
            'dokumen_profil' => $this->getProfilDocuments(),
            'dokumen_usulan' => $this->getUsulanDocuments(),
            'dokumen_bkd' => $this->getBkdDocuments(),
            'dokumen_pendukung' => $this->getPendukungDocuments(),
        ];
    }

    /**
     * Get dokumen profil pegawai
     */
    public function getProfilDocuments(): array
    {
        $documents = [];
        $profilFields = [
            'ijazah_terakhir' => 'Ijazah Terakhir',
            'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
            'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
            'skp_tahun_pertama' => 'SKP Tahun Pertama',
            'skp_tahun_kedua' => 'SKP Tahun Kedua',
            'pak_konversi' => 'PAK/PAK Konversi',
            'sk_cpns' => 'SK CPNS',
            'sk_pns' => 'SK PNS',
            'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
            'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
            'foto' => 'Foto'
        ];

        foreach ($profilFields as $field => $label) {
            $documents[$field] = [
                'label' => $label,
                'exists' => !empty($this->pegawai->$field),
                'path' => $this->pegawai->$field
            ];
        }

        return $documents;
    }

    /**
     * Get dokumen usulan
     */
    public function getUsulanDocuments(): array
    {
        $documents = [];
        $usulanFields = [
            'pakta_integritas' => 'Pakta Integritas',
            'bukti_korespondensi' => 'Bukti Korespondensi',
            'turnitin' => 'Turnitin',
            'upload_artikel' => 'Upload Artikel',
            'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
        ];

        foreach ($usulanFields as $field => $label) {
            $documents[$field] = [
                'label' => $label,
                'exists' => $this->hasDocument($field),
                'path' => $this->getDocumentPath($field)
            ];
        }

        return $documents;
    }

    /**
     * Get dokumen BKD
     */
    public function getBkdDocuments(): array
    {
        $documents = [];
        $bkdLabels = $this->getBkdDisplayLabels();

        foreach ($bkdLabels as $field => $label) {
            $documents[$field] = [
                'label' => $label,
                'exists' => $this->hasDocument($field),
                'path' => $this->getDocumentPath($field)
            ];
        }

        return $documents;
    }

    /**
     * Get dokumen pendukung
     */
    public function getPendukungDocuments(): array
    {
        $documents = [];
        $pendukungFields = [
            'file_surat_usulan' => 'File Surat Usulan',
            'file_berita_senat' => 'File Berita Senat'
        ];

        foreach ($pendukungFields as $field => $label) {
            $documents[$field] = [
                'label' => $label,
                'exists' => $this->hasDocument($field),
                'path' => $this->getDocumentPath($field)
            ];
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
        return $query->whereIn('status_usulan', [
            self::STATUS_DRAFT_USULAN,
            self::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS,
            self::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_TIM_SISTER,
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER
        ]);
    }

    /**
     * Scope untuk usulan yang read-only
     */
    public function scopeReadOnly($query)
    {
        return $query->whereIn('status_usulan', [
            self::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER
        ]);
    }

    /**
     * Scope untuk usulan yang masih aktif (belum selesai)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status_usulan', [
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
            self::STATUS_DIREKOMENDASIKAN,
            self::STATUS_TIDAK_DIREKOMENDASIKAN
        ]);
    }

    /**
     * Scope untuk usulan yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status_usulan', [
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
            self::STATUS_DIREKOMENDASIKAN,
            self::STATUS_TIDAK_DIREKOMENDASIKAN
        ]);
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
        return in_array($this->status_usulan, [
            self::STATUS_DRAFT_USULAN,
            self::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS,
            self::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_TIM_SISTER
        ]);
    }

    /**
     * Check if usulan is submitted
     */
    public function isSubmitted(): bool
    {
        return !in_array($this->status_usulan, [
            self::STATUS_DRAFT_USULAN,
            self::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS,
            self::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS,
            self::STATUS_DRAFT_PERBAIKAN_TIM_SISTER
        ]);
    }

    /**
     * Check if usulan is approved
     */
    public function isApproved(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            self::STATUS_DIREKOMENDASIKAN
        ]);
    }

    /**
     * Check if usulan is rejected
     */
    public function isRejected(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_TIDAK_DIREKOMENDASIKAN
        ]);
    }

    /**
     * Check if usulan needs revision
     */
    public function needsRevision(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER
        ]);
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
        // FIXED: Handle role name mapping for case sensitivity
        $roleMapping = [
            'Admin Fakultas' => 'admin_fakultas',
            'Admin Universitas' => 'admin_universitas',
            'Penilai Universitas' => 'penilai_universitas',
            'Kepegawaian Universitas' => 'kepegawaian_universitas',
            'Tim Senat' => 'tim_senat',
            'Pegawai' => 'pegawai'
        ];

        // Map role name to database key
        $dbRole = $roleMapping[$role] ?? strtolower(str_replace(' ', '_', $role));

        $roleData = $this->validasi_data[$dbRole] ?? [];

        // FIXED: Ensure consistent structure - always return with validation key
        if (isset($roleData['validation'])) {
            return $roleData;
        }

        // For backward compatibility, if data exists but no 'validation' key,
        // wrap it in the new structure
        if (!empty($roleData)) {
            return [
                'validation' => $roleData,
                'validated_by' => null,
                'validated_at' => null
            ];
        }

        // Return empty structure with validation key
        return [
            'validation' => [],
            'validated_by' => null,
            'validated_at' => null
        ];
    }

    /**
     * Set data validasi untuk role tertentu
     */
    public function setValidasiByRole(string $role, array $validasiData, int $validatedBy): void
    {
        $currentValidasi = $this->validasi_data ?? [];

        // FIXED: Preserve existing validation data and merge with new data
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

        // FIXED: Update validation structure - preserve existing data like dokumen_pendukung
        // Ensure we don't lose existing data that's not in validation
        $currentValidasi[$role] = array_merge($existingValidation, [
            'validation' => $existingValidationData,
            'validated_by' => $validatedBy,
            'validated_at' => now()->toISOString()
        ]);

        $this->validasi_data = $currentValidasi;

        // FIXED: Add logging for debugging
        \Log::info('setValidasiByRole called', [
            'role' => $role,
            'validated_by' => $validatedBy,
            'input_data' => $validasiData,
            'merged_data' => $existingValidationData,
            'final_structure' => $currentValidasi[$role]
        ]);
    }

    /**
     * Set data validasi individual penilai dengan struktur yang terpisah
     */
    public function setValidasiIndividualPenilai(int $penilaiId, array $validasiData, string $keteranganUmum = ''): void
    {
        $currentValidasi = $this->validasi_data ?? [];

        // Initialize individual_penilai array if not exists
        if (!isset($currentValidasi['individual_penilai'])) {
            $currentValidasi['individual_penilai'] = [];
        }

        // Find existing data for this penilai
        $existingIndex = null;
        foreach ($currentValidasi['individual_penilai'] as $index => $penilaiData) {
            if (isset($penilaiData['penilai_id']) && $penilaiData['penilai_id'] == $penilaiId) {
                $existingIndex = $index;
                break;
            }
        }

        // Prepare new penilai data
        $newPenilaiData = [
            'penilai_id' => $penilaiId,
            'validated_at' => now()->toISOString(),
            'keterangan_umum' => $keteranganUmum
        ];

        // Deep merge validation data
        foreach ($validasiData as $category => $fields) {
            if (!isset($newPenilaiData[$category])) {
                $newPenilaiData[$category] = [];
            }

            foreach ($fields as $field => $fieldData) {
                $newPenilaiData[$category][$field] = $fieldData;
            }
        }

        // Update or add penilai data
        if ($existingIndex !== null) {
            // Update existing penilai data
            $currentValidasi['individual_penilai'][$existingIndex] = $newPenilaiData;
        } else {
            // Add new penilai data
            $currentValidasi['individual_penilai'][] = $newPenilaiData;
        }

        $this->validasi_data = $currentValidasi;


    }

    /**
     * Get data validasi individual penilai berdasarkan penilai_id
     */
        public function getValidasiIndividualPenilai(int $penilaiId): ?array
    {
        $currentValidasi = $this->validasi_data ?? [];
        $individualPenilai = $currentValidasi['individual_penilai'] ?? [];



        // Check in new structure first
        foreach ($individualPenilai as $penilaiData) {
            if (isset($penilaiData['penilai_id']) && $penilaiData['penilai_id'] == $penilaiId) {

                return $penilaiData;
            }
        }

        // MIGRATION LOGIC: Check old structure for backward compatibility
        if (isset($currentValidasi['penilai_universitas']['validation']) &&
            isset($currentValidasi['penilai_universitas']['validated_by']) &&
            $currentValidasi['penilai_universitas']['validated_by'] == $penilaiId) {



            // Convert old structure to new format
            $oldData = $currentValidasi['penilai_universitas'];
            $convertedData = [
                'penilai_id' => $penilaiId,
                'validation' => $oldData['validation'],
                'validated_at' => $oldData['validated_at'] ?? now()->toISOString()
            ];

            // Add keterangan_umum if exists and is not an error message
            if (isset($oldData['perbaikan_usulan']['catatan'])) {
                $catatan = $oldData['perbaikan_usulan']['catatan'];

                // Exclude error messages from display
                if (!str_contains($catatan, 'SQLSTATE') && !str_contains($catatan, 'Terjadi kesalahan')) {
                    $convertedData['keterangan_umum'] = $catatan;
                }
            }



            return $convertedData;
        }



        return null;
    }

    /**
     * Get semua data validasi individual penilai
     */
    public function getAllValidasiIndividualPenilai(): array
    {
        $currentValidasi = $this->validasi_data ?? [];
        return $currentValidasi['individual_penilai'] ?? [];
    }

    /**
     * Cek apakah penilai tertentu sudah memberikan validasi
     */
    public function isPenilaiValidated(int $penilaiId): bool
    {
        $penilaiData = $this->getValidasiIndividualPenilai($penilaiId);
        return $penilaiData !== null && !empty($penilaiData);
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
        // FIXED: Access validation data with correct structure
        $roleData = $this->validasi_data[$role] ?? [];
        $validationData = $roleData['validation'] ?? [];
        return $validationData[$category][$field]['status'] ?? 'sesuai';
    }

    /**
     * Mendapatkan keterangan validasi untuk field tertentu dari role tertentu
     */
    public function getFieldValidationKeterangan(string $role, string $category, string $field): string
    {
        // FIXED: Access validation data with correct structure
        $roleData = $this->validasi_data[$role] ?? [];
        $validationData = $roleData['validation'] ?? [];
        return $validationData[$category][$field]['keterangan'] ?? '';
    }

    /**
     * Cek apakah ada field yang tidak sesuai dari role tertentu
     */
    public function hasInvalidFields(string $role): bool
    {
        $validasi = $this->getValidasiByRole($role);
        $validationData = $validasi['validation'] ?? [];

        foreach ($validationData as $category => $fields) {
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
        $validationData = $validasi['validation'] ?? [];

        foreach ($validationData as $category => $fields) {
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
            'pegawai:id,nama_lengkap,email,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_id',
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
        return $query->whereIn('status_usulan', [
            self::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            self::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
        ]);
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

    // Status constants for standardized status
    const STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS = 'Usulan Dikirim ke Admin Fakultas';
    const STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS = 'Usulan Perbaikan dari Admin Fakultas';
    const STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS = 'Permintaan Perbaikan dari Admin Fakultas';
    const STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS = 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas';
    const STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS = 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas';
    const STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS = 'Usulan Disetujui Admin Fakultas';
    const STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS = 'Usulan Perbaikan Dari Pegawai Ke Kepegawaian Universitas';
    const STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS = 'Permintaan Perbaikan Ke Pegawai Dari Kepegawaian Universitas';
    const STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS = 'Usulan Disetujui Kepegawaian Universitas dan Menunggu Penilaian';
    const STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS = 'Permintaan Perbaikan dari Penilai Universitas';
    const STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS = 'Usulan Perbaikan dari Penilai Universitas';
    const STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_PENILAI = 'Permintaan Perbaikan Ke Pegawai Dari Penilai';
    const STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_PENILAI = 'Permintaan Perbaikan Ke Admin Fakultas Dari Penilai';
    const STATUS_USULAN_PERBAIKAN_KE_PENILAI_UNIVERSITAS = 'Usulan Perbaikan Ke Penilai Universitas';
    const STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS = 'Usulan Direkomendasi dari Penilai Universitas';
    const STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS = 'Usulan Direkomendasi Penilai Universitas';
    const STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT = 'Usulan Direkomendasikan oleh Tim Senat';
    const STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER = 'Usulan Sudah Dikirim ke Sister';
    const STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER = 'Permintaan Perbaikan Usulan dari Tim Sister';
    const STATUS_PERBAIKAN_SUDAH_DIKIRIM_KE_SISTER = 'Perbaikan Sudah Dikirim ke Sister';
    const STATUS_DIREKOMENDASIKAN = 'Direkomendasikan';
    const STATUS_TIDAK_DIREKOMENDASIKAN = 'Tidak Direkomendasikan';

    // =====================================================
    // STATUS CONSTANTS FOR USULAN KEPANGKATAN (PEGAWAI & KEPEGAWAIAN UNIVERSITAS ONLY)
    // =====================================================
    const STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS = 'Usulan Dikirim ke Kepegawaian Universitas';
    const STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN = 'Usulan Sudah Dikirim ke BKN';
    const STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN = 'Usulan Perbaikan Dari Pegawai Ke BKN';
    const STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN = 'Permintaan Perbaikan Ke Pegawai Dari BKN';
    const STATUS_DIREKOMENDASIKAN_BKN = 'Usulan Direkomendasikan BKN';

    // Draft status constants (for Pegawai role)
    const STATUS_DRAFT_USULAN = 'Draft Usulan';
    const STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS = 'Draft Perbaikan Admin Fakultas';
    const STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS = 'Draft Perbaikan Kepegawaian Universitas';
    const STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS = 'Draft Perbaikan Penilai Universitas';
    const STATUS_DRAFT_PERBAIKAN_TIM_SISTER = 'Draft Perbaikan Tim Sister';

    // Legacy status constants (for backward compatibility) - HAPUS YANG TIDAK DIGUNAKAN
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
        $completedPenilai = $penilais->whereNotIn('pivot.status_penilaian', ['Belum Dinilai'])->count();

        // If not all penilai have completed, return intermediate status
        if ($completedPenilai < $totalPenilai) {
            return self::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;
        }

        // If any penilai gives 'Perlu Perbaikan', result is perbaikan
        $hasPerbaikan = $penilais->where('pivot.status_penilaian', 'Perlu Perbaikan')->count() > 0;
        if ($hasPerbaikan) {
            return self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS;
        }

        // Count recommendations (Sesuai = rekomendasi)
        $rekomendasiCount = $penilais->where('pivot.status_penilaian', 'Sesuai')->count();

        // Logic based on number of penilai
        switch ($totalPenilai) {
            case 1:
                return $rekomendasiCount > 0 ? self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS : self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS;

            case 2:
                return ($rekomendasiCount == 2) ? self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS : self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS;

            case 3:
                return ($rekomendasiCount >= 2) ? self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS : self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS;

            default:
                // For more than 3 penilai, use majority vote
                return ($rekomendasiCount > ($totalPenilai - $rekomendasiCount)) ? self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS : self::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS;
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
            'Usulan Disetujui Kepegawaian Universitas',
            'Permintaan Perbaikan dari Penilai Universitas',
            'Usulan Direkomendasi dari Penilai Universitas',
            'Usulan Perbaikan Ke Penilai Universitas'
        ];

        if (!in_array($this->status_usulan, $penilaiAssessmentStatuses)) {
            return false; // Not in penilai assessment phase
        }

        // Check if Kepegawaian Universitas has set a flag to prevent auto-update
        $currentValidasi = $this->validasi_data ?? [];
        if (isset($currentValidasi['kepegawaian_universitas']['kirim_perbaikan_ke_penilai']['prevent_auto_update']) &&
            $currentValidasi['kepegawaian_universitas']['kirim_perbaikan_ke_penilai']['prevent_auto_update'] === true) {
            return false; // Prevent auto-update when flag is set
        }

        $penilais = $this->penilais;
        $totalPenilai = $penilais->count();

        if ($totalPenilai === 0) {
            return false; // No penilai assigned
        }

        $completedPenilai = $penilais->whereNotIn('pivot.status_penilaian', ['Belum Dinilai'])->count();
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
            self::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS,
            self::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
        ]);
    }

    /**
     * Get penilai assessment progress information
     */
    public function getPenilaiAssessmentProgress()
    {
        $penilais = $this->penilais ?? collect();
        $totalPenilai = $penilais->count();

        // Count completed penilai based on status_penilaian (not just hasil_penilaian)
        $completedPenilai = $penilais->filter(function($penilai) {
            $status = $penilai->pivot->status_penilaian ?? 'Belum Dinilai';
            $catatan = $penilai->pivot->catatan_penilaian ?? '';
            $hasil = $penilai->pivot->hasil_penilaian ?? '';

            // Consider completed if:
            // 1. status_penilaian is not 'Belum Dinilai'
            // 2. OR has catatan_penilaian
            // 3. OR has hasil_penilaian
            return $status !== 'Belum Dinilai' || !empty($catatan) || !empty($hasil);
        })->count();

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
     * Get individual penilai status details
     */
    public function getPenilaiStatusDetails()
    {
        $penilais = $this->penilais ?? collect();
        $details = [];

        foreach ($penilais as $penilai) {
            $details[] = [
                'penilai_id' => $penilai->id,
                'penilai_name' => $penilai->name ?? 'Unknown',
                'status_penilaian' => $penilai->pivot->status_penilaian ?? 'Belum Dinilai',
                'catatan_penilaian' => $penilai->pivot->catatan_penilaian ?? '',
                'updated_at' => $penilai->pivot->updated_at ?? null,
                'is_completed' => !in_array($penilai->pivot->status_penilaian ?? 'Belum Dinilai', ['Belum Dinilai'])
            ];
        }

        return $details;
    }

    /**
     * Check if usulan can be submitted in current period
     */
    public function canBeSubmittedInCurrentPeriod()
    {
        // If usulan was not recommended, it cannot be submitted in current period
        if ($this->status_usulan === self::STATUS_TIDAK_DIREKOMENDASIKAN) {
            return false;
        }

        // Check if current period is still open
        if ($this->periodeUsulan && $this->periodeUsulan->status === 'Buka') {
            return true;
        }

        return false;
    }

    // =====================================================
    // HELPER METHODS FOR USULAN KEPANGKATAN
    // =====================================================

    /**
     * Get valid statuses for usulan kepangkatan (Pegawai & Kepegawaian Universitas only)
     */
    public static function getKepangkatanValidStatuses(): array
    {
        return [
            self::STATUS_DRAFT_USULAN,
            self::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
            self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN,
            self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN,
            self::STATUS_DIREKOMENDASIKAN_BKN
        ];
    }

    /**
     * Check if current status is valid for usulan kepangkatan
     */
    public function isKepangkatanValidStatus(): bool
    {
        return in_array($this->status_usulan, self::getKepangkatanValidStatuses());
    }

    /**
     * Get next possible statuses for usulan kepangkatan based on current status and role
     */
    public function getKepangkatanNextStatuses(string $role): array
    {
        $currentStatus = $this->status_usulan;
        $nextStatuses = [];

        switch ($role) {
            case 'Pegawai':
                switch ($currentStatus) {
                    case self::STATUS_DRAFT_USULAN:
                        $nextStatuses = [self::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS];
                        break;
                    case self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS:
                        $nextStatuses = [self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS];
                        break;
                    case self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN:
                        $nextStatuses = [self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN];
                        break;
                }
                break;

            case 'Kepegawaian Universitas':
                switch ($currentStatus) {
                    case self::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS:
                        $nextStatuses = [
                            self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN
                        ];
                        break;
                    case self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS:
                        $nextStatuses = [
                            self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                            self::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN
                        ];
                        break;
                    case self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN:
                        $nextStatuses = [self::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN];
                        break;
                }
                break;
        }

        return $nextStatuses;
    }

    /**
     * Check if usulan kepangkatan can be transitioned to a specific status
     */
    public function canTransitionToKepangkatanStatus(string $targetStatus, string $role): bool
    {
        $nextStatuses = $this->getKepangkatanNextStatuses($role);
        return in_array($targetStatus, $nextStatuses);
    }

    /**
     * Get status badge class for usulan kepangkatan statuses
     */
    public function getKepangkatanStatusBadgeClass(): string
    {
        switch ($this->status_usulan) {
            case self::STATUS_DRAFT_USULAN:
                return 'bg-gray-100 text-gray-800';
            case self::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS:
                return 'bg-orange-100 text-orange-800';
            case self::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN:
                return 'bg-purple-100 text-purple-800';
            case self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN:
                return 'bg-red-100 text-red-800';
            case self::STATUS_DIREKOMENDASIKAN_BKN:
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Check if usulan kepangkatan is in final status
     */
    public function isKepangkatanFinalStatus(): bool
    {
        return in_array($this->status_usulan, [
            self::STATUS_DIREKOMENDASIKAN_BKN
        ]);
    }

    /**
     * Check if usulan kepangkatan requires action from specific role
     */
    public function requiresKepangkatanActionFrom(string $role): bool
    {
        switch ($role) {
            case 'Pegawai':
                return in_array($this->status_usulan, [
                    self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                    self::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN
                ]);
            case 'Kepegawaian Universitas':
                return in_array($this->status_usulan, [
                    self::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                    self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                    self::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
                ]);
            default:
                return false;
        }
    }

    /**
     * Accessor untuk status (menggunakan status_usulan)
     */
    public function getStatusAttribute()
    {
        return $this->status_usulan;
    }

    /**
     * Mutator untuk status (menggunakan status_usulan)
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status_usulan'] = $value;
    }
}

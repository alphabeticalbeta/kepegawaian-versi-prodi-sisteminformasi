<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Penggunaan trait ini sudah benar

    protected $guarded = ['id'];

    /**
     * Guard yang digunakan untuk authentication dan permissions
     */
    protected $guard_name = 'pegawai';

    protected $fillable = [
        // Data Utama & Autentikasi
        'jenis_pegawai',
        'jenis_jabatan',
        'status_kepegawaian',
        'nip',
        'nuptk',
        'gelar_depan',
        'nama_lengkap',
        'gelar_belakang',
        'email',
        'password',
        'username',
        'nomor_kartu_pegawai',

        // Data Pribadi
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_handphone',

        // Relasi ke Tabel Lain
        'pangkat_terakhir_id',
        'jabatan_terakhir_id',
        'unit_kerja_terakhir_id',
        'unit_kerja_id', // Untuk admin fakultas

        // Data Kepegawaian (TMT)
        'tmt_cpns',
        'tmt_pns',
        'tmt_pangkat',
        'tmt_jabatan',

        // Data Pendidikan
        'pendidikan_terakhir',
        'nama_universitas_sekolah',
        'nama_prodi_jurusan',
        'nama_prodi_jurusan_s2',

        // Data Fungsional (Khusus Dosen)
        'mata_kuliah_diampu',
        'ranting_ilmu_kepakaran',
        'url_profil_sinta',

        // Data Kinerja & PAK
        'predikat_kinerja_tahun_pertama',
        'predikat_kinerja_tahun_kedua',
        'nilai_konversi',

        // Path Dokumen (File)
        'foto',
        'sk_cpns',
        'sk_pns',
        'sk_pangkat_terakhir',
        'sk_jabatan_terakhir',
        'ijazah_terakhir',
        'transkrip_nilai_terakhir',
        'sk_penyetaraan_ijazah',
        'disertasi_thesis_terakhir',
        'skp_tahun_pertama',
        'skp_tahun_kedua',
        'pak_konversi',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tmt_pangkat'       => 'date',
        'tanggal_lahir'     => 'date',
        'tmt_jabatan'       => 'date',
        'tmt_pns'           => 'date',
        'tmt_cpns'          => 'date',
    ];

    // [PERBAIKAN UTAMA]
    // Method 'roles()' di bawah ini telah dihapus.
    // Kita tidak memerlukannya lagi karena sudah ditangani oleh trait 'HasRoles'.

    public function usulans(): HasMany
    {
        return $this->hasMany(Usulan::class, 'pegawai_id', 'id');
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_terakhir_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_terakhir_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(SubSubUnitKerja::class, 'unit_kerja_terakhir_id');
    }

    public function unitKerjaPengelola()
    {
        // Relasi ini menghubungkan kolom 'unit_kerja_id' di tabel 'pegawais'
        // dengan 'id' di tabel 'unit_kerjas'.
        return $this->belongsTo(\App\Models\BackendUnivUsulan\UnitKerja::class, 'unit_kerja_id');
    }

    // =====================================
    // QUERY SCOPES OPTIMIZATION
    // =====================================

    /**
     * Scope untuk eager loading yang optimal
     */
    public function scopeWithOptimalRelations($query)
    {
        return $query->with([
            'pangkat:id,pangkat',
            'jabatan:id,jabatan,jenis_pegawai',
            'unitKerja:id,nama,sub_unit_kerja_id',
            'unitKerja.subUnitKerja:id,nama,unit_kerja_id',
            'unitKerja.unitKerja:id,nama'
        ]);
    }

    /**
     * Scope untuk filter berdasarkan jenis pegawai
     */
    public function scopeByJenisPegawai($query, string $jenisPegawai)
    {
        return $query->where('jenis_pegawai', $jenisPegawai);
    }

    /**
     * Scope untuk pencarian berdasarkan nama atau NIP
     */
    public function scopeSearchByNameOrNip($query, string $search)
    {
        return $query->where(function ($subQuery) use ($search) {
            $subQuery->where('nama_lengkap', 'like', "%{$search}%")
                     ->orWhere('nip', 'like', "%{$search}%");
        });
    }

    /**
     * Scope untuk pegawai berdasarkan fakultas
     */
    public function scopeByFakultas($query, int $fakultasId)
    {
        return $query->whereHas('unitKerja.unitKerja', function ($q) use ($fakultasId) {
            $q->where('id', $fakultasId);
        });
    }

    /**
     * Scope untuk pegawai aktif (memiliki data lengkap)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('nama_lengkap')
                     ->whereNotNull('email')
                     ->whereNotNull('nip');
    }

    // =====================================
    // ACCESSOR OPTIMIZATION
    // =====================================

    /**
     * Get full name with titles
     */
    public function getFullNameAttribute(): string
    {
        $name = trim($this->nama_lengkap);

        if ($this->gelar_depan) {
            $name = $this->gelar_depan . ' ' . $name;
        }

        if ($this->gelar_belakang) {
            $name = $name . ', ' . $this->gelar_belakang;
        }

        return $name;
    }

    /**
     * Get current position info
     */
    public function getCurrentPositionAttribute(): array
    {
        return [
            'pangkat' => $this->pangkat?->pangkat,
            'jabatan' => $this->jabatan?->jabatan,
            'unit_kerja' => $this->unitKerja?->nama,
            'fakultas' => $this->unitKerja?->unitKerja?->nama
        ];
    }

    // =====================================
    // CACHE OPTIMIZATION
    // =====================================

    /**
     * Cache key untuk pegawai
     */
    public function getCacheKey(): string
    {
        return "pegawai_{$this->id}_v" . $this->updated_at->timestamp;
    }

    /**
     * Clear cache when pegawai is updated
     */
    protected static function booted()
    {
        static::updated(function ($pegawai) {
            \Cache::forget($pegawai->getCacheKey());
            \Cache::forget('pangkats_all');
            \Cache::forget('jabatans_all');
            \Cache::forget('unit_kerjas_with_relations');
        });

        static::deleted(function ($pegawai) {
            \Cache::forget($pegawai->getCacheKey());
        });
    }
}

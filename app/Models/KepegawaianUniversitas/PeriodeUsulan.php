<?php

namespace App\Models\KepegawaianUniversitas; // <-- LOKASI NAMESPACE DIPERBARUI

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeUsulan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periode_usulans';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_periode',
        'jenis_usulan',
        'status_kepegawaian',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_perbaikan',
        'tanggal_selesai_perbaikan',
        'senat_min_setuju',
        'status',
        'tahun_periode',
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_mulai_perbaikan' => 'date',
        'tanggal_selesai_perbaikan' => 'date',
        'status_kepegawaian' => 'array',
    ];

    /**
     * Mendefinisikan relasi "one-to-many" ke model Usulan.
     * Satu periode bisa memiliki banyak usulan.
     */
    public function usulans(): HasMany
    {
        // Kita akan membuat model Usulan di namespace yang sama
        return $this->hasMany(Usulan::class);
    }

    /**
     * Mendapatkan usulan yang sudah dikirim ke Admin Fakultas atau Kepegawaian Universitas
     */
    public function usulansSubmitted(): HasMany
    {
        return $this->hasMany(Usulan::class)->whereIn('status_usulan', [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_SK_TERBIT
        ]);
    }



    /**
     * Scope untuk memfilter periode berdasarkan status kepegawaian
     */
    public function scopeByStatusKepegawaian($query, $statusKepegawaian)
    {
        return $query->whereJsonContains('status_kepegawaian', $statusKepegawaian);
    }

    /**
     * Scope untuk memfilter periode yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Buka');
    }

    /**
     * Scope untuk memfilter periode berdasarkan jenis usulan
     */
    public function scopeByJenisUsulan($query, $jenisUsulan)
    {
        return $query->where('jenis_usulan', $jenisUsulan);
    }

    /**
     * Mendapatkan daftar status kepegawaian yang tersedia
     */
    public static function getAvailableStatusKepegawaian()
    {
        return [
            // Dosen
            'Dosen PNS' => 'Dosen PNS',
            'Dosen PPPK' => 'Dosen PPPK',
            'Dosen Non ASN' => 'Dosen Non ASN',

            // Tenaga Kependidikan
            'Tenaga Kependidikan PNS' => 'Tenaga Kependidikan PNS',
            'Tenaga Kependidikan PPPK' => 'Tenaga Kependidikan PPPK',
            'Tenaga Kependidikan Non ASN' => 'Tenaga Kependidikan Non ASN',
        ];
    }

    /**
     * Mendapatkan daftar status kepegawaian yang diizinkan untuk periode ini
     */
    public function getAllowedStatusKepegawaian()
    {
        return $this->status_kepegawaian ?? [];
    }

        /**
     * Mengecek apakah status kepegawaian tertentu diizinkan
     */
    public function isStatusKepegawaianAllowed($statusKepegawaian)
    {
        return in_array($statusKepegawaian, $this->status_kepegawaian ?? []);
    }
}

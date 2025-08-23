<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use App\Models\KepegawaianUniversitas\UnitKerja;

class Penilai extends Model
{
    use HasFactory, HasRoles;

    /**
     * Guard yang digunakan untuk authentication dan permissions
     */
    protected $guard_name = 'pegawai';

    // Gunakan tabel pegawais yang sudah ada
    protected $table = 'pegawais';

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'email',
        'jenis_pegawai',
        'status_kepegawaian',
        'created_at',
        'updated_at'
    ];

    /**
     * Get usulans that this penilai is assigned to
     */
    public function usulans()
    {
        return $this->belongsToMany(Usulan::class, 'usulan_penilai', 'penilai_id', 'usulan_id')
                    ->withPivot('status_penilaian', 'catatan_penilaian', 'hasil_penilaian', 'tanggal_penilaian')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Unit Kerja
     */
    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    /**
     * Relasi ke Roles (menggunakan Spatie Permission)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->where('model_type', \App\Models\KepegawaianUniversitas\Pegawai::class);
    }

    /**
     * Get active penilais (pegawai dengan role Penilai Universitas)
     */
    public static function getActivePenilais()
    {
        return self::whereHas('roles', function($query) {
            $query->where('name', 'Penilai Universitas');
        })
        ->orderBy('nama_lengkap')
        ->get();
    }

    /**
     * Get penilais by bidang keahlian (berdasarkan unit kerja)
     */
    public static function getPenilaisByBidang($bidang)
    {
        return self::where('status_kepegawaian', 'Aktif')
                   ->whereIn('jenis_pegawai', ['Dosen', 'Tendik'])
                   ->whereHas('unitKerja', function($query) use ($bidang) {
                       $query->where('nama_unit_kerja', 'like', "%{$bidang}%");
                   })
                   ->orderBy('nama_lengkap')
                   ->get();
    }
}

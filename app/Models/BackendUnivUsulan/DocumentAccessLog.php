<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'accessor_id',
        'document_field',
        'ip_address',
        'user_agent',
        'accessed_at'
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    /**
     * Relasi ke pegawai yang dokumennya diakses
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke pegawai yang mengakses dokumen
     */
    public function accessor(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'accessor_id');
    }

    /**
     * Scope untuk log akses hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('accessed_at', today());
    }

    /**
     * Scope untuk log akses pegawai tertentu
     */
    public function scopeForPegawai($query, $pegawaiId)
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    /**
     * Scope untuk log akses oleh accessor tertentu
     */
    public function scopeByAccessor($query, $accessorId)
    {
        return $query->where('accessor_id', $accessorId);
    }
}

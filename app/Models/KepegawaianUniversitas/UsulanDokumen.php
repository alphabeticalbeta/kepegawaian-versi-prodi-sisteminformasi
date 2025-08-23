<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UsulanDokumen extends Model
{
    use HasFactory;

    protected $table = 'usulan_dokumens';

    /**
     * Mass assignable attributes
     * FIXED: Nama field konsisten dengan database
     */
    protected $fillable = [
        'usulan_id',
        'diupload_oleh_id',  // <-- KONSISTEN dengan migration
        'nama_dokumen',
        'path',
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = ['url', 'file_size_formatted'];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    /**
     * Get the usulan that owns the document
     */
    public function usulan(): BelongsTo
    {
        return $this->belongsTo(Usulan::class);
    }

    /**
     * Get the pegawai who uploaded the document
     * FIXED: Nama relasi dan field konsisten
     */
    public function diuploadOleh(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'diupload_oleh_id');
    }

    // =====================================
    // ACCESSORS (Computed Properties)
    // =====================================

    /**
     * Get full URL for the document
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (!Storage::disk('public')->exists($this->path)) {
            return 'N/A';
        }

        $bytes = Storage::disk('public')->size($this->path);

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Check if document is PDF
     */
    public function isPdf(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->path);
    }

    // =====================================
    // METHODS
    // =====================================

    /**
     * Delete the physical file when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($dokumen) {
            if ($dokumen->fileExists()) {
                Storage::disk('public')->delete($dokumen->path);
            }
        });
    }
}

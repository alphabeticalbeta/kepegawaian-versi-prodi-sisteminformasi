<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Informasi extends Model
{
    use HasFactory;

    protected $table = 'informasi';

    protected $fillable = [
        'judul',
        'konten',
        'jenis',
        'nomor_surat',
        'tanggal_surat',
        'status',
        'tanggal_publish',
        'tanggal_berakhir',
        'penulis',
        'tags',
        'thumbnail',
        'lampiran',
        'view_count',
        'is_featured',
        'is_pinned'
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_publish' => 'datetime',
        'tanggal_berakhir' => 'datetime',
        'tags' => 'array',
        'lampiran' => 'array',
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk data yang dipublikasi
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('tanggal_publish')
                          ->orWhere('tanggal_publish', '<=', now());
                    });
    }

    // Scope untuk berita
    public function scopeBerita($query)
    {
        return $query->where('jenis', 'berita');
    }

    // Scope untuk pengumuman
    public function scopePengumuman($query)
    {
        return $query->where('jenis', 'pengumuman');
    }

    // Scope untuk featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope untuk pinned
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    // Scope untuk pengumuman yang belum expired
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('tanggal_berakhir')
              ->orWhere('tanggal_berakhir', '>=', now());
        });
    }

    // Scope untuk prioritas (menggunakan is_pinned dan is_featured)
    public function scopeByPriority($query)
    {
        return $query->orderByRaw("CASE
            WHEN is_pinned = 1 THEN 1
            WHEN is_featured = 1 THEN 2
            ELSE 3
        END");
    }

    // Scope untuk search
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $searchTerm = "%{$search}%";
            $q->where('judul', 'like', $searchTerm)
              ->orWhere('konten', 'like', $searchTerm)
              ->orWhere('nomor_surat', 'like', $searchTerm)
              ->orWhere('penulis', 'like', $searchTerm)
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(tags, '$')) LIKE ?", [$searchTerm]);
        });
    }

    // Accessor untuk format tanggal surat
    public function getTanggalSuratFormattedAttribute()
    {
        return $this->tanggal_surat ? $this->tanggal_surat->format('d F Y') : null;
    }

    // Accessor untuk format tanggal publish
    public function getTanggalPublishFormattedAttribute()
    {
        return $this->tanggal_publish ? $this->tanggal_publish->format('d F Y, H:i') : null;
    }

    // Method untuk generate nomor surat otomatis
    public static function generateNomorSurat($unit = 'KEU', $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        $lastNumber = self::where('jenis', 'pengumuman')
                          ->whereYear('tanggal_surat', $tahun)
                          ->where('nomor_surat', 'like', "%/{$unit}/{$tahun}")
                          ->count();

        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return "{$nextNumber}/UNMUL/{$unit}/{$tahun}";
    }

    // Method untuk increment view count
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    // Method untuk check apakah pengumuman expired
    public function isExpired()
    {
        return $this->tanggal_berakhir && $this->tanggal_berakhir < now();
    }

    // Method untuk check apakah sudah dipublish
    public function isPublished()
    {
        return $this->status === 'published' &&
               (!$this->tanggal_publish || $this->tanggal_publish <= now());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DasarHukum extends Model
{
    use HasFactory;

    protected $table = 'dasar_hukum';

    protected $fillable = [
        'judul',
        'konten',
        'jenis_dasar_hukum',
        'sub_jenis',
        'nomor_dokumen',
        'tanggal_dokumen',
        'nama_instansi',
        'masa_berlaku',
        'penulis',
        'tags',
        'thumbnail',
        'lampiran',
        'status',
        'tanggal_publish',
        'tanggal_berakhir',
        'is_featured',
        'is_pinned'
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'masa_berlaku' => 'date',
        'tanggal_publish' => 'datetime',
        'tanggal_berakhir' => 'datetime',
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'tags' => 'array',
        'lampiran' => 'array'
    ];

    // Accessors
    public function getJenisLabelAttribute()
    {
        $labels = [
            'keputusan' => 'Keputusan',
            'pedoman' => 'Pedoman',
            'peraturan' => 'Peraturan',
            'surat_edaran' => 'Surat Edaran',
            'surat_kementerian' => 'Surat Kementerian',
            'surat_rektor' => 'Surat Rektor Universitas Mulawarman',
            'undang_undang' => 'Undang-Undang'
        ];
        return $labels[$this->jenis_dasar_hukum] ?? $this->jenis_dasar_hukum;
    }

    public function getSubJenisLabelAttribute()
    {
        if (!$this->sub_jenis) return null;

        $labels = [
            'peraturan' => 'Peraturan',
            'surat_keputusan' => 'Surat Keputusan',
            'sk_non_pns' => 'Surat Keputusan (SK) Non PNS'
        ];
        return $labels[$this->sub_jenis] ?? $this->sub_jenis;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getTingkatPrioritasLabelAttribute()
    {
        $labels = [
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi'
        ];
        return $labels[$this->tingkat_prioritas] ?? $this->tingkat_prioritas;
    }

    public function getFormattedTanggalDokumenAttribute()
    {
        return $this->tanggal_dokumen ? $this->tanggal_dokumen->format('d/m/Y') : null;
    }

    public function getFormattedMasaBerlakuAttribute()
    {
        return $this->masa_berlaku ? $this->masa_berlaku->format('d/m/Y') : null;
    }

    public function getFormattedTanggalPublishAttribute()
    {
        return $this->tanggal_publish ? $this->tanggal_publish->format('d/m/Y H:i') : null;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_dasar_hukum', $jenis);
    }


    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $searchTerm = "%{$search}%";
            $q->where('judul', 'like', $searchTerm)
              ->orWhere('konten', 'like', $searchTerm)
              ->orWhere('nomor_dokumen', 'like', $searchTerm)
              ->orWhere('nama_instansi', 'like', $searchTerm)
              ->orWhere('penulis', 'like', $searchTerm)
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(tags, '$')) LIKE ?", [$searchTerm]);
        });
    }

    // Methods
    public function isExpired()
    {
        if (!$this->masa_berlaku) return false;
        return $this->masa_berlaku->isPast();
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }

    public function isFeatured()
    {
        return $this->is_featured;
    }

    public function isPinned()
    {
        return $this->is_pinned;
    }

    public function getThumbnailUrl()
    {
        if (!$this->thumbnail) return null;
        return asset('storage/' . $this->thumbnail);
    }

    public function getLampiranUrls()
    {
        if (!$this->lampiran || !is_array($this->lampiran)) return [];

        return array_map(function($file) {
            return asset('storage/' . $file);
        }, $this->lampiran);
    }
}

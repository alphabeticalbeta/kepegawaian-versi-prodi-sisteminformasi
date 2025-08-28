<?php

namespace App\Models\KepegawaianUniversitas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UsulanLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulan_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usulan_id',
        'status_sebelumnya',
        'status_baru',
        'catatan',
        'dilakukan_oleh_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status',
        'keterangan',
        'user_name',
        'formatted_date',
        'status_badge_class'
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    /**
     * Relasi ke usulan induk.
     */
    public function usulan(): BelongsTo
    {
        return $this->belongsTo(Usulan::class);
    }

    /**
     * Relasi ke pegawai yang melakukan aksi.
     */
    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'dilakukan_oleh_id');
    }

    // =====================================
    // ACCESSORS (Computed Properties)
    // =====================================

    /**
     * Get status attribute (prioritize status_baru over status_sebelumnya)
     */
    public function getStatusAttribute(): ?string
    {
        return $this->status_baru ?? $this->status_sebelumnya;
    }

    /**
     * Get keterangan attribute (alias for catatan)
     */
    public function getKeteranganAttribute(): ?string
    {
        return $this->catatan;
    }

    /**
     * Get user_name attribute
     */
    public function getUserNameAttribute(): string
    {
        return $this->dilakukanOleh ? $this->dilakukanOleh->nama_lengkap : 'System';
    }

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-';
    }

    /**
     * Get relative time (human readable)
     */
    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }

    /**
     * Get status badge CSS class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $status = $this->status;

        return match($status) {
            // Status standar baru
            Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS => 'bg-blue-100 text-blue-800 border-blue-300',
            Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS => 'bg-amber-100 text-amber-800 border-amber-300',
            Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS => 'bg-amber-100 text-amber-800 border-amber-300',
            Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS => 'bg-green-100 text-green-800 border-green-300',
            Usulan::STATUS_USULAN_PERBAIKAN_DARI_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800 border-red-300',
            Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800 border-orange-300',
            Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800 border-orange-300',
            Usulan::STATUS_USULAN_DIREKOMENDASI_DARI_PENILAI_UNIVERSITAS => 'bg-purple-100 text-purple-800 border-purple-300',
            Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS => 'bg-purple-100 text-purple-800 border-purple-300',
            Usulan::STATUS_USULAN_DIREKOMENDASIKAN_OLEH_TIM_SENAT => 'bg-purple-100 text-purple-800 border-purple-300',
            Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER => 'bg-blue-100 text-blue-800 border-blue-300',
            Usulan::STATUS_PERMINTAAN_PERBAIKAN_USULAN_DARI_TIM_SISTER => 'bg-red-100 text-red-800 border-red-300',
            
            // Draft status constants
            Usulan::STATUS_DRAFT_USULAN => 'bg-gray-100 text-gray-800 border-gray-300',
            Usulan::STATUS_DRAFT_PERBAIKAN_ADMIN_FAKULTAS => 'bg-amber-100 text-amber-800 border-amber-300',
            Usulan::STATUS_DRAFT_PERBAIKAN_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800 border-red-300',
            Usulan::STATUS_DRAFT_PERBAIKAN_PENILAI_UNIVERSITAS => 'bg-orange-100 text-orange-800 border-orange-300',
            Usulan::STATUS_DRAFT_PERBAIKAN_TIM_SISTER => 'bg-red-100 text-red-800 border-red-300',
            
            // Legacy status constants (fallback)
            Usulan::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI => 'bg-orange-100 text-orange-800 border-orange-300',
            Usulan::STATUS_DIREKOMENDASIKAN => 'bg-purple-100 text-purple-800 border-purple-300',
            Usulan::STATUS_TIDAK_DIREKOMENDASIKAN => 'bg-red-100 text-red-800 border-red-300',
            
            default => 'bg-gray-100 text-gray-800 border-gray-300'
        };
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute(): string
    {
        $status = $this->status;

        return match($status) {
            'Draft' => 'file-edit',
            'Diajukan' => 'send',
            'Sedang Direview' => 'clock',
            'Perlu Perbaikan' => 'alert-triangle',
            'Dikembalikan' => 'rotate-ccw',
            'Disetujui' => 'check-circle',
            'Direkomendasikan' => 'star',
            'Ditolak' => 'x-circle',
            default => 'help-circle'
        };
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where(function($q) use ($status) {
            $q->where('status_baru', $status)
              ->orWhere('status_sebelumnya', $status);
        });
    }

    /**
     * Scope untuk filter by usulan
     */
    public function scopeForUsulan($query, int $usulanId)
    {
        return $query->where('usulan_id', $usulanId);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('dilakukan_oleh_id', $userId);
    }

    /**
     * Scope untuk recent logs
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope untuk logs dalam rentang waktu tertentu
     */
    public function scopeInDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // =====================================
    // UTILITY METHODS
    // =====================================

    /**
     * Check if this log represents a status change
     */
    public function isStatusChange(): bool
    {
        return $this->status_sebelumnya !== $this->status_baru;
    }

    /**
     * Check if this is an initial log (no previous status)
     */
    public function isInitialLog(): bool
    {
        return is_null($this->status_sebelumnya);
    }

    /**
     * Get the action description
     */
    public function getActionDescription(): string
    {
        if ($this->isInitialLog()) {
            return "Usulan dibuat dengan status {$this->status_baru}";
        }

        if ($this->isStatusChange()) {
            return "Status diubah dari {$this->status_sebelumnya} ke {$this->status_baru}";
        }

        return "Update pada usulan dengan status {$this->status}";
    }

    /**
     * Get formatted log entry for display
     */
    public function getFormattedEntry(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_sebelumnya' => $this->status_sebelumnya,
            'status_baru' => $this->status_baru,
            'keterangan' => $this->keterangan,
            'user_name' => $this->user_name,
            'formatted_date' => $this->formatted_date,
            'relative_time' => $this->relative_time,
            'action_description' => $this->getActionDescription(),
            'status_badge_class' => $this->status_badge_class,
            'status_icon' => $this->status_icon,
            'is_status_change' => $this->isStatusChange(),
            'is_initial_log' => $this->isInitialLog(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }

    // =====================================
    // BOOT METHOD
    // =====================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-load relationships when accessed
        static::with(['dilakukanOleh']);
    }
}

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
            'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
            'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800 border-orange-300',
            'Dikembalikan' => 'bg-red-100 text-red-800 border-red-300',
            'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
            'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
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

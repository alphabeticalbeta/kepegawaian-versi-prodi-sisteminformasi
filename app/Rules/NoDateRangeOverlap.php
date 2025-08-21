<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NoDateRangeOverlap implements ValidationRule, DataAwareRule
{
    protected string $table;
    protected string $startColumn;
    protected string $endColumn;
    protected array $filters;
    protected ?int $excludeId;
    protected string $excludeColumn;

    protected array $data = [];

    public function __construct(
        string $table,
        string $startColumn = 'tanggal_mulai',
        string $endColumn = 'tanggal_selesai',
        array $filters = [],
        ?int $excludeId = null,
        string $excludeColumn = 'id',
    ) {
        $this->table = $table;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
        $this->filters = $filters;
        $this->excludeId = $excludeId;
        $this->excludeColumn = $excludeColumn;
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $inputStart = $this->data[$this->startColumn] ?? null;
        $inputEnd   = $this->data[$this->endColumn] ?? null;

        if ($attribute === $this->startColumn) {
            $inputStart = $value;
        } elseif ($attribute === $this->endColumn) {
            $inputEnd = $value;
        }

        // Jika salah satu tanggal kosong, skip validasi
        if (blank($inputStart) || blank($inputEnd)) {
            return;
        }

        try {
            $start = Carbon::parse($inputStart)->startOfDay();
            $end   = Carbon::parse($inputEnd)->endOfDay();
        } catch (\Throwable $e) {
            \Log::warning('NoDateRangeOverlap: Invalid date format', [
                'inputStart' => $inputStart,
                'inputEnd' => $inputEnd,
                'error' => $e->getMessage()
            ]);
            return; // biar rule 'date' / format lain yang nembak error
        }

        if ($end->lt($start)) {
            $fail('Tanggal selesai harus sesudah atau sama dengan tanggal mulai.');
            return;
        }

        $query = DB::table($this->table)
            ->where($this->startColumn, '<=', $end)
            ->where($this->endColumn, '>=', $start);

        foreach ($this->filters as $col => $val) {
            if (!blank($val)) {
                $query->where($col, $val);
            }
        }

        if (!is_null($this->excludeId)) {
            $query->where($this->excludeColumn, '!=', $this->excludeId);
        }

        // Debug logging
        \Log::info('NoDateRangeOverlap Debug', [
            'input_start' => $start,
            'input_end' => $end,
            'exclude_id' => $this->excludeId,
            'exclude_column' => $this->excludeColumn,
            'filters' => $this->filters,
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings()
        ]);

        if ($query->exists()) {
            $fail('Rentang tanggal bertabrakan dengan periode lain.');
        }
    }
}

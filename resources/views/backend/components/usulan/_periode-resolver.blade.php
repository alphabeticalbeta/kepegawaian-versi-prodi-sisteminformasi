{{-- Periode Resolver - Optimized version without database queries in view --}}
@php
    // OPTIMASI: Gunakan data yang sudah di-pass dari controller
    // Jika $periodeUsulans tidak ada, gunakan empty collection
    if (!isset($periodeUsulans) || (method_exists($periodeUsulans, 'isEmpty') && $periodeUsulans->isEmpty())) {
        $periodeUsulans = collect();
        
        // OPTIMASI: Log warning untuk developer
        if (config('app.debug')) {
            \Log::warning('Periode data not passed from controller', [
                'view' => 'periode-resolver',
                'suggestion' => 'Pass periodeUsulans from controller instead of querying in view'
            ]);
        }
    }
@endphp

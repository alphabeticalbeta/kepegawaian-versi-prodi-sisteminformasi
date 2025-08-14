{{-- Periode Resolver - Auto-query periode jika tidak ada dari controller --}}
@php
    // Jika $periodeUsulans tidak ada atau kosong, coba query manual
    if (!isset($periodeUsulans) || (method_exists($periodeUsulans, 'isEmpty') && $periodeUsulans->isEmpty())) {

        $currentAdmin = Auth::user();

        if ($currentAdmin && $currentAdmin->unit_kerja_id && isset($unitKerja)) {
            try {
                $periodeUsulans = \App\Models\BackendUnivUsulan\PeriodeUsulan::withCount([
                    'usulans as jumlah_pengusul' => function ($query) use ($currentAdmin) {
                        $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($currentAdmin) {
                                $subQuery->where('id', $currentAdmin->unit_kerja_id);
                            });
                    }
                ])->latest()->paginate(10);

                if (config('app.debug')) {
                    \Log::debug('Periode Auto-Resolved', [
                        'admin_id' => $currentAdmin->id,
                        'unit_kerja_id' => $currentAdmin->unit_kerja_id,
                        'total_periode' => $periodeUsulans->total()
                    ]);
                }
            } catch (\Exception $e) {
                $periodeUsulans = collect();
                \Log::error('Periode Auto-Resolve Error', [
                    'admin_id' => $currentAdmin->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $periodeUsulans = collect();
        }
    }
@endphp

{{-- Unit Kerja Resolver - Handle hierarki unit kerja yang benar --}}
@php
    // PERBAIKAN: Pastikan $unitKerja selalu terdefinisi
    if (!isset($unitKerja)) {
        $unitKerja = null;
    }

    // Cek apakah $unitKerja sudah ada dari controller
    if (!$unitKerja) {
        $currentAdmin = Auth::user();
        $debugInfo = [];

        if ($currentAdmin) {
            try {
                // METHOD 1: Admin Fakultas - Direct unit_kerja_id
                if ($currentAdmin->unit_kerja_id) {
                    $debugInfo['method_1'] = 'Testing Admin Fakultas (unit_kerja_id)';
                    $unitKerja = \App\Models\KepegawaianUniversitas\UnitKerja::find($currentAdmin->unit_kerja_id);

                    if ($unitKerja) {
                        $debugInfo['method_1_result'] = 'SUCCESS: ' . $unitKerja->nama;
                    } else {
                        $debugInfo['method_1_result'] = 'FAILED: unit_kerja_id tidak ditemukan';
                    }
                }

                // METHOD 2: Pegawai Biasa - Hierarki melalui unit_kerja_id (HANYA jika method 1 gagal)
                if (!$unitKerja && $currentAdmin->unit_kerja_id) {
                    $debugInfo['method_2'] = 'Testing Pegawai Hierarki (unit_kerja_id)';

                    // Ambil SubSubUnitKerja dengan relasi lengkap
                    $subSubUnitKerja = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with([
                        'subUnitKerja.unitKerja'
                    ])->find($currentAdmin->unit_kerja_id);

                    if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                        $unitKerja = $subSubUnitKerja->subUnitKerja->unitKerja;
                        $debugInfo['method_2_result'] = 'SUCCESS: ' . $unitKerja->nama . ' (via hierarki)';
                        $debugInfo['method_2_path'] = $subSubUnitKerja->nama . ' → ' . $subSubUnitKerja->subUnitKerja->nama . ' → ' . $unitKerja->nama;
                    } else {
                        $debugInfo['method_2_result'] = 'FAILED: Hierarki tidak lengkap';
                    }
                }

                // PERBAIKAN: Log debugging info
                if (config('app.debug')) {
                    \Log::debug('Unit Kerja Resolver (Hierarki)', array_merge([
                        'admin_id' => $currentAdmin->id,
                        'unit_kerja_id' => $currentAdmin->unit_kerja_id,
                        'unit_kerja_id' => $currentAdmin->unit_kerja_id,
                        'final_result' => $unitKerja ? $unitKerja->nama : 'FAILED'
                    ], $debugInfo));
                }

            } catch (\Exception $e) {
                $unitKerja = null;
                \Log::error('Unit Kerja Resolver Exception (Hierarki)', [
                    'admin_id' => $currentAdmin->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // PERBAIKAN: Store debug info untuk ditampilkan di debug panel
        $unitKerjaResolverDebug = $debugInfo ?? [];
    }

    // FINAL CHECK: Pastikan $unitKerja tidak null atau buat object kosong
    if (!$unitKerja) {
        $unitKerja = (object) ['nama' => null, 'id' => null];
    }
@endphp

{{-- Variable $unitKerja dan $unitKerjaResolverDebug sekarang tersedia --}}
{{-- Unit Kerja Resolver - Handle hierarki unit kerja yang benar --}}
@php
    // Cek apakah $unitKerja sudah ada dari controller
    if (!isset($unitKerja) || !$unitKerja) {
        $currentAdmin = Auth::user();
        $unitKerja = null;
        $debugInfo = [];

        if ($currentAdmin) {
            try {
                // METHOD 1: Admin Fakultas - Direct unit_kerja_id
                if ($currentAdmin->unit_kerja_id) {
                    $debugInfo['method_1'] = 'Testing Admin Fakultas (unit_kerja_id)';
                    $unitKerja = \App\Models\BackendUnivUsulan\UnitKerja::find($currentAdmin->unit_kerja_id);

                    if ($unitKerja) {
                        $debugInfo['method_1_result'] = 'SUCCESS: ' . $unitKerja->nama;
                    } else {
                        $debugInfo['method_1_result'] = 'FAILED: unit_kerja_id tidak ditemukan';
                    }
                }

                // METHOD 2: Pegawai Biasa - Hierarki melalui unit_kerja_terakhir_id
                if (!$unitKerja && $currentAdmin->unit_kerja_terakhir_id) {
                    $debugInfo['method_2'] = 'Testing Pegawai Hierarki (unit_kerja_terakhir_id)';

                    // Ambil SubSubUnitKerja dengan relasi lengkap
                    $subSubUnitKerja = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with([
                        'subUnitKerja.unitKerja'
                    ])->find($currentAdmin->unit_kerja_terakhir_id);

                    if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                        $unitKerja = $subSubUnitKerja->subUnitKerja->unitKerja;
                        $debugInfo['method_2_result'] = 'SUCCESS: ' . $unitKerja->nama . ' (via hierarki)';
                        $debugInfo['method_2_path'] = $subSubUnitKerja->nama . ' → ' . $subSubUnitKerja->subUnitKerja->nama . ' → ' . $unitKerja->nama;
                    } else {
                        $debugInfo['method_2_result'] = 'FAILED: Hierarki tidak lengkap';
                    }
                }

                // METHOD 3: Raw query jika semua gagal
                if (!$unitKerja) {
                    $debugInfo['method_3'] = 'Testing Raw Query Fallback';

                    if ($currentAdmin->unit_kerja_id) {
                        $rawResult = \DB::table('unit_kerjas')
                            ->where('id', $currentAdmin->unit_kerja_id)
                            ->first();
                    } elseif ($currentAdmin->unit_kerja_terakhir_id) {
                        // Raw query untuk hierarki
                        $rawResult = \DB::table('sub_sub_unit_kerjas as ssuk')
                            ->join('sub_unit_kerjas as suk', 'ssuk.sub_unit_kerja_id', '=', 'suk.id')
                            ->join('unit_kerjas as uk', 'suk.unit_kerja_id', '=', 'uk.id')
                            ->where('ssuk.id', $currentAdmin->unit_kerja_terakhir_id)
                            ->select('uk.id', 'uk.nama')
                            ->first();
                    }

                    if (isset($rawResult) && $rawResult) {
                        $unitKerja = new \App\Models\BackendUnivUsulan\UnitKerja();
                        $unitKerja->id = $rawResult->id;
                        $unitKerja->nama = $rawResult->nama;
                        $unitKerja->exists = true;
                        $debugInfo['method_3_result'] = 'SUCCESS: ' . $unitKerja->nama . ' (raw query)';
                    } else {
                        $debugInfo['method_3_result'] = 'FAILED: Raw query tidak ada hasil';
                    }
                }

                // Log debugging info
                if (config('app.debug')) {
                    \Log::debug('Unit Kerja Resolver (Hierarki)', array_merge([
                        'admin_id' => $currentAdmin->id,
                        'unit_kerja_id' => $currentAdmin->unit_kerja_id,
                        'unit_kerja_terakhir_id' => $currentAdmin->unit_kerja_terakhir_id,
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

        // Store debug info untuk ditampilkan di debug panel
        $unitKerjaResolverDebug = $debugInfo;
    }
@endphp

{{-- Variable $unitKerja dan $unitKerjaResolverDebug sekarang tersedia --}}

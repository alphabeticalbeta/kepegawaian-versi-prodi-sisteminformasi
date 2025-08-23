
{{-- Session Flash Messages --}}
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 animate-fade-in" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Sukses!</p>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 animate-fade-in" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('warning'))
<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6 animate-fade-in" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 110-12 6 6 0 010 12zm0-9a1 1 0 011 1v4a1 1 0 01-2 0V8a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Peringatan!</p>
            <p class="text-sm">{{ session('warning') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('info'))
<div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 animate-fade-in" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Informasi</p>
            <p class="text-sm">{{ session('info') }}</p>
        </div>
    </div>
</div>
@endif

{{-- Unit Kerja Alert for Admin Fakultas --}}
@if(request()->is('admin-fakultas/*'))
    @php
        $unitKerja = $unitKerja ?? null;
        $showDebug = $showDebug ?? config('app.debug');
        $totalPeriode = $totalPeriode ?? (isset($periodeUsulans) ? $periodeUsulans->total() : 0);
        $totalPengusul = $totalPengusul ?? (isset($periodeUsulans) ? $periodeUsulans->sum('jumlah_pengusul') : 0);
    @endphp

    @if(!$unitKerja)
        {{-- Alert Error untuk Unit Kerja --}}
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 animate-fade-in" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 9.414l1.293-1.293a1 1 0 111.414 1.414L11.414 10l1.293 1.293a1 1 0 01-1.414 1.414L10 10.586l-1.293 1.293a1 1 0 01-1.414-1.414L9.586 10 8.293 8.707z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-red-800">Konfigurasi Unit Kerja Bermasalah</p>
                    <p class="text-sm text-red-700">Akun Anda belum dikaitkan dengan unit kerja fakultas. Data usulan tidak akan muncul.</p>
                    <details class="mt-2">
                        <summary class="text-sm text-red-700 cursor-pointer hover:text-red-900">
                            <span class="underline">Lihat langkah perbaikan →</span>
                        </summary>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Hubungi Administrator untuk mengatur field <code class="bg-red-100 px-1 rounded font-mono">unit_kerja_id</code> pada akun Anda</li>
                                <li>Pastikan unit kerja tersedia di master data</li>
                                <li>Logout dan login kembali setelah perubahan</li>
                                <li>Jika masih bermasalah, periksa relasi tabel: <code class="bg-red-100 px-1 rounded font-mono">pegawais.unit_kerja_id → unit_kerjas.id</code></li>
                            </ul>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    @else
        {{-- Alert Success untuk Unit Kerja --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-blue-800">{{ $unitKerja->nama }}</p>
                        <p class="text-sm text-blue-700">Unit kerja aktif | {{ $totalPeriode }} periode | {{ $totalPengusul }} pengusul menunggu</p>
                    </div>
                </div>
                <div class="text-right text-blue-800">
                    <div class="text-lg font-bold">{{ $totalPengusul }}</div>
                    <div class="text-xs">Menunggu Review</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Manual Test untuk Hierarki --}}
    @if($showDebug)
        @php
            // Quick manual test dengan hierarki
            $manualTest = [];
            $currentAdmin = Auth::user();

            if ($currentAdmin) {
                // Test 1: Admin Fakultas (unit_kerja_id)
                if ($currentAdmin->unit_kerja_id) {
                    try {
                        $adminUnitKerja = \App\Models\KepegawaianUniversitas\UnitKerja::find($currentAdmin->unit_kerja_id);
                        $manualTest['admin_direct'] = $adminUnitKerja ? $adminUnitKerja->nama : 'NULL';
                    } catch (\Exception $e) {
                        $manualTest['admin_direct'] = 'ERROR: ' . $e->getMessage();
                    }
                }

                // Test 2: Pegawai Hierarki (unit_kerja_id)
                if ($currentAdmin->unit_kerja_id) {
                    try {
                        $subSubUnit = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with(['subUnitKerja.unitKerja'])
                            ->find($currentAdmin->unit_kerja_id);

                        if ($subSubUnit && $subSubUnit->subUnitKerja && $subSubUnit->subUnitKerja->unitKerja) {
                            $manualTest['pegawai_hierarki'] = $subSubUnit->subUnitKerja->unitKerja->nama;
                            $manualTest['hierarki_path'] = $subSubUnit->nama . ' → ' . $subSubUnit->subUnitKerja->nama . ' → ' . $subSubUnit->subUnitKerja->unitKerja->nama;
                        } else {
                            $manualTest['pegawai_hierarki'] = 'HIERARKI BROKEN';
                        }
                    } catch (\Exception $e) {
                        $manualTest['pegawai_hierarki'] = 'ERROR: ' . $e->getMessage();
                    }
                }

                // Test 3: Raw query hierarki
                if ($currentAdmin->unit_kerja_id) {
                    try {
                        $rawHierarki = \DB::table('sub_sub_unit_kerjas as ssuk')
                            ->join('sub_unit_kerjas as suk', 'ssuk.sub_unit_kerja_id', '=', 'suk.id')
                            ->join('unit_kerjas as uk', 'suk.unit_kerja_id', '=', 'uk.id')
                            ->where('ssuk.id', $currentAdmin->unit_kerja_id)
                            ->select('uk.nama as unit_kerja', 'suk.nama as sub_unit', 'ssuk.nama as sub_sub_unit')
                            ->first();

                        $manualTest['raw_hierarki'] = $rawHierarki ? $rawHierarki->unit_kerja : 'NULL';
                    } catch (\Exception $e) {
                        $manualTest['raw_hierarki'] = 'ERROR: ' . $e->getMessage();
                    }
                }
            }
        @endphp

        <div class="bg-orange-50 border border-orange-200 rounded p-3 mb-4 text-xs">
            <strong class="text-orange-800">🧪 Manual Test (Hierarki Support):</strong><br>
            @foreach($manualTest as $test => $result)
                <div class="mt-1">
                    <span class="font-mono text-orange-700">{{ $test }}:</span>
                    <span class="text-orange-900">{{ $result }}</span>
                </div>
            @endforeach
        </div>
    @endif
@endif

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Details/Summary Enhancement */
details summary {
    list-style: none;
}
details summary::-webkit-details-marker {
    display: none;
}
details[open] summary::after {
    content: ' ↑';
}
details summary::after {
    content: ' ↓';
}
</style>

{{-- Debug Panel (tampil jika ada parameter ?debug) --}}
{{-- @include('backend.components.usulan._debug-panel') --}}

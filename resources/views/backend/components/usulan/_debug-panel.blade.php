{{-- Debug Panel Khusus untuk Troubleshooting Unit Kerja --}}
@if(config('app.debug') && request()->has('debug'))
<div class="bg-gray-900 text-green-400 p-4 rounded-lg mb-6 font-mono text-xs">
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-green-300 font-bold">🔧 DEBUG PANEL - UNIT KERJA ISSUE</h3>
        <a href="{{ request()->url() }}" class="text-red-400 hover:text-red-300">✕ Tutup Debug</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
       {{-- Panel 1: Data User & Hierarki --}}
        <div>
            <h4 class="text-yellow-400 font-bold mb-2">👤 Data User & Unit Kerja:</h4>
            <div class="space-y-1">
                <div>ID: <span class="text-white">{{ Auth::id() }}</span></div>
                <div>NIP: <span class="text-white">{{ Auth::user()->nip ?? 'NULL' }}</span></div>
                <div>Nama: <span class="text-white">{{ Auth::user()->nama_lengkap ?? 'NULL' }}</span></div>
                <div>Email: <span class="text-white">{{ Auth::user()->email ?? 'NULL' }}</span></div>

                {{-- Unit Kerja Fields --}}
                <div class="border-t border-gray-700 pt-2 mt-2">
                    <div class="text-cyan-300 font-bold">Unit Kerja Fields:</div>
                    <div>unit_kerja_id (Admin): <span class="text-white font-bold">{{ Auth::user()->unit_kerja_id ?? 'NULL' }}</span></div>
                    <div>unit_kerja_terakhir_id (Pegawai): <span class="text-white font-bold">{{ Auth::user()->unit_kerja_terakhir_id ?? 'NULL' }}</span></div>
                </div>

                {{-- Hierarki Test --}}
                @php
                    $hierarkiTest = null;
                    if (Auth::user()->unit_kerja_terakhir_id) {
                        try {
                            $subSubUnit = \App\Models\BackendUnivUsulan\SubSubUnitKerja::with(['subUnitKerja.unitKerja'])->find(Auth::user()->unit_kerja_terakhir_id);
                            if ($subSubUnit && $subSubUnit->subUnitKerja && $subSubUnit->subUnitKerja->unitKerja) {
                                $hierarkiTest = $subSubUnit->nama . ' → ' . $subSubUnit->subUnitKerja->nama . ' → ' . $subSubUnit->subUnitKerja->unitKerja->nama;
                            }
                        } catch (\Exception $e) {
                            $hierarkiTest = 'ERROR: ' . $e->getMessage();
                        }
                    }
                @endphp

                @if($hierarkiTest)
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <div class="text-green-300 font-bold">Hierarki Path:</div>
                        <div class="text-xs text-white">{{ $hierarkiTest }}</div>
                    </div>
                @endif

                <div class="border-t border-gray-700 pt-2 mt-2">
                    <div>Roles: <span class="text-white">{{ Auth::user()->getRoleNames()->implode(', ') }}</span></div>
                </div>
            </div>
        </div>

        {{-- Panel 2: Database Check --}}
        <div>
            <h4 class="text-yellow-400 font-bold mb-2">🗄️ Database Check:</h4>
            @php
                $pegawaiRecord = \App\Models\BackendUnivUsulan\Pegawai::find(Auth::id());
                $unitKerjaCount = \App\Models\BackendUnivUsulan\UnitKerja::count();
                $periodeCount = \App\Models\BackendUnivUsulan\PeriodeUsulan::count();
                $usulanCount = \App\Models\BackendUnivUsulan\Usulan::count();

                // Check if unitKerja is resolved
                $isUnitKerjaResolved = isset($unitKerja) && $unitKerja;
            @endphp
            <div class="space-y-1">
                <div>Pegawai exists: <span class="text-white">{{ $pegawaiRecord ? 'YES' : 'NO' }}</span></div>
                <div>Total UnitKerja: <span class="text-white">{{ $unitKerjaCount }}</span></div>
                <div>Total Periode: <span class="text-white">{{ $periodeCount }}</span></div>
                <div>Total Usulan: <span class="text-white">{{ $usulanCount }}</span></div>

                {{-- Unit Kerja Resolution Status --}}
                <div class="border-t border-gray-700 pt-2 mt-2">
                    <div class="text-yellow-300 font-bold">Unit Kerja Resolution:</div>
                    @php
                        $isUnitKerjaResolved = isset($unitKerja) && $unitKerja;
                        $currentUser = Auth::user();

                        // Test relasi secara langsung
                        $testRelation = null;
                        $relationWorks = false;
                        if ($currentUser) {
                            try {
                                $testUser = \App\Models\BackendUnivUsulan\Pegawai::with('unitKerjaPengelola')->find($currentUser->id);
                                $testRelation = $testUser ? $testUser->unitKerjaPengelola : null;
                                $relationWorks = $testRelation !== null;
                            } catch (\Exception $e) {
                                $relationWorks = false;
                            }
                        }
                    @endphp
                    <div>Resolved: <span class="text-white">{{ $isUnitKerjaResolved ? 'YES ('.$unitKerja->nama.')' : 'NO' }}</span></div>
                    <div>Relation Works: <span class="text-white">{{ $relationWorks ? 'YES' : 'NO' }}</span></div>
                    <div>Test Relation: <span class="text-white">{{ $testRelation ? $testRelation->nama : 'NULL' }}</span></div>
                    @if($pegawaiRecord)
                        <div>unit_kerja_id: <span class="text-white">{{ $pegawaiRecord->unit_kerja_id ?? 'NULL' }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Panel 3: SQL Queries Debug --}}
    <div class="mt-4">
        <h4 class="text-yellow-400 font-bold mb-2">🔍 Query Debug:</h4>
        <div class="bg-gray-800 p-3 rounded">
            <div class="text-gray-300 mb-2">Query untuk mengambil periode usulan:</div>
            <div class="text-green-200 text-xs">
                @php
                    $adminId = Auth::id();
                    $admin = \App\Models\BackendUnivUsulan\Pegawai::find($adminId);
                    $unitKerjaId = $admin ? $admin->unit_kerja_id : null;
                @endphp
                <div>SELECT * FROM periode_usulans WHERE...</div>
                <div>AdminID: {{ $adminId }}</div>
                <div>UnitKerjaID dari Pegawai: {{ $unitKerjaId ?? 'NULL' }}</div>
                @if($unitKerjaId)
                    @php
                        $queryCount = \App\Models\BackendUnivUsulan\PeriodeUsulan::withCount([
                            'usulans as jumlah_pengusul' => function ($query) use ($unitKerjaId) {
                                $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                                    ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                                        $subQuery->where('id', $unitKerjaId);
                                    });
                            }
                        ])->count();
                    @endphp
                    <div>Hasil Query Count: {{ $queryCount }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Panel 4: Unit Kerja Resolver Debug --}}
    @if(isset($unitKerjaResolverDebug))
    <div class="mt-4">
        <h4 class="text-yellow-400 font-bold mb-2">🔧 Unit Kerja Resolver Debug:</h4>
        <div class="bg-gray-800 p-3 rounded">
            <div class="text-xs space-y-1">
                @foreach($unitKerjaResolverDebug as $step => $result)
                    <div>
                        <span class="text-cyan-300">{{ $step }}:</span>
                        <span class="text-white">{{ $result }}</span>
                    </div>
                @endforeach
                <div class="border-t border-gray-600 pt-2 mt-2">
                    <span class="text-green-300">Final Status:</span>
                    <span class="text-white font-bold">{{ isset($unitKerja) && $unitKerja ? 'RESOLVED (' . $unitKerja->nama . ')' : 'FAILED' }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Panel 4: Solusi --}}
    <div class="mt-4 bg-blue-900 p-3 rounded">
        <h4 class="text-blue-300 font-bold mb-2">💡 Solusi Cepat:</h4>
        <div class="text-blue-200 text-xs space-y-1">
            @if(!Auth::user()->unit_kerja_id)
                <div>1. UPDATE pegawais SET unit_kerja_id = [ID_FAKULTAS] WHERE id = {{ Auth::id() }};</div>
                <div>2. Atau minta admin update melalui interface admin</div>
            @endif
            <div>3. Cek apakah ada data di tabel unit_kerjas</div>
            <div>4. Pastikan relasi unitKerjaPengelola() di model Pegawai sudah benar</div>
        </div>
    </div>
</div>
@endif

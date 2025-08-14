{{-- Shared: Daftar Dokumen Usulan (dengan grouping + status) --}}
@php
    // 1) Dokumen profil pengusul (mengacu field yang sudah dipakai di project)
    $profilDocs = [
        'ijazah_terakhir'           => 'Ijazah Terakhir',
        'transkrip_nilai_terakhir'  => 'Transkrip Nilai Terakhir',
        'sk_pangkat_terakhir'       => 'SK Pangkat Terakhir',
        'sk_jabatan_terakhir'       => 'SK Jabatan Terakhir',
        'skp_tahun_pertama'         => 'SKP Tahun Pertama',
        'skp_tahun_kedua'           => 'SKP Tahun Kedua',
        'sk_cpns'                   => 'SK CPNS',
        'sk_pns'                    => 'SK PNS',
        'pak_konversi'              => 'PAK/PAK Konversi',
        'sk_penyetaraan_ijazah'     => 'SK Penyetaraan Ijazah',
        'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
    ];

    // 2) Dokumen BKD (label dinamis dari model)
    $bkdLabels = $bkdLabels ?? ($usulan->getBkdDisplayLabels() ?? []);
    // Keys: bkd_semester_1 .. bkd_semester_4

    // Helper untuk ambil path & url lihat dokumen
    $docRow = function(string $field, string $label) use ($usulan) {
        $path = $usulan->getDocumentPath($field); // dukung struktur lama/baru (sudah ada di model)
        $exists = !empty($path);

        $url = $exists
            ? route('backend.admin-univ-usulan.pusat-usulan.show-document', ['usulan' => $usulan->id, 'field' => $field])
            : '#';

        return [
            'label'  => $label,
            'field'  => $field,
            'exists' => $exists,
            'url'    => $url,
        ];
    };

    $rowsProfil = collect($profilDocs)->map(fn($label, $field) => $docRow($field, $label))->values();
    $rowsBKD    = collect($bkdLabels)->map(fn($label, $field) => $docRow($field, $label))->values();
@endphp

<div class="bg-white shadow-md rounded-lg mt-6">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800">Dokumen Usulan</h3>
        <p class="text-sm text-gray-500">Dokumen profil pengusul & BKD yang harus ada pada usulan ini.</p>
    </div>

    <div class="p-6 space-y-8">
        {{-- Grup: Dokumen Profil Pengusul --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-base font-semibold text-gray-800">Dokumen Profil Pengusul</h4>
                @php
                    $ok = $rowsProfil->where('exists', true)->count();
                    $total = $rowsProfil->count();
                    $pct = $total ? round(($ok/$total)*100) : 0;
                @endphp
                <div class="text-sm text-gray-500">{{ $ok }} / {{ $total }} ({{ $pct }}%) lengkap</div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($rowsProfil as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $row['label'] }}</td>
                                <td class="px-4 py-2">
                                    @if($row['exists'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Tidak Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ $row['url'] }}"
                                       @class([
                                           'inline-flex items-center px-3 py-1.5 text-sm rounded-md',
                                           'bg-indigo-600 text-white hover:bg-indigo-700' => $row['exists'],
                                           'bg-gray-200 text-gray-500 cursor-not-allowed' => !$row['exists'],
                                       ])
                                       @if(!$row['exists']) aria-disabled="true" tabindex="-1" @else target="_blank" @endif>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Grup: Dokumen BKD --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-base font-semibold text-gray-800">Dokumen BKD</h4>
                @php
                    $ok = $rowsBKD->where('exists', true)->count();
                    $total = $rowsBKD->count();
                    $pct = $total ? round(($ok/$total)*100) : 0;
                @endphp
                <div class="text-sm text-gray-500">{{ $ok }} / {{ $total }} ({{ $pct }}%) lengkap</div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($rowsBKD as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $row['label'] }}</td>
                                <td class="px-4 py-2">
                                    @if($row['exists'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Tidak Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ $row['url'] }}"
                                       @class([
                                           'inline-flex items-center px-3 py-1.5 text-sm rounded-md',
                                           'bg-indigo-600 text-white hover:bg-indigo-700' => $row['exists'],
                                           'bg-gray-200 text-gray-500 cursor-not-allowed' => !$row['exists'],
                                       ])
                                       @if(!$row['exists']) aria-disabled="true" tabindex="-1" @else target="_blank" @endif>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- (Opsional) Sisa dokumen dari tabel usulan_dokumens untuk pelengkap --}}
        @php $dokumens = $usulan->dokumens ?? collect(); @endphp
        @if($dokumens->isNotEmpty())
            <div>
                <h4 class="text-base font-semibold text-gray-800 mb-3">Lampiran Lainnya</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Diunggah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($dokumens as $doc)
                                <tr>
                                    <td class="px-4 py-2">
                                        <div class="font-medium text-gray-800">{{ $doc->nama_file ?? basename($doc->path) }}</div>
                                        <div class="text-xs text-gray-500 break-all">{{ $doc->path }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $doc->file_size_formatted ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ optional($doc->created_at)->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ $doc->url ?? (isset($doc->path) ? asset('storage/'.$doc->path) : '#') }}"
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

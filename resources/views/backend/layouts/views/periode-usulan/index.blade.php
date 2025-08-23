<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Data Periode Usulan</h3>

            <!-- Dropdown untuk memilih jenis usulan -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <select id="jenisUsulanSelect" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Jenis Usulan</option>
                        <option value="usulan-jabatan-dosen">Usulan Jabatan Dosen</option>
                        <option value="usulan-jabatan-tendik">Usulan Jabatan Tenaga Kependidikan</option>
                    </select>
                </div>

                <button id="tambahPeriodeBtn" disabled class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                    Tambah Periode
                </button>
            </div>
        </div>

        <!-- Filter berdasarkan jenis usulan -->
        <div class="mt-4 flex items-center gap-4">
            <label for="filterJenis" class="text-sm font-medium text-gray-700">Filter:</label>
            <select id="filterJenis" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Jenis Usulan</option>
                <option value="usulan-jabatan-dosen">Usulan Jabatan Dosen</option>
                <option value="usulan-jabatan-tendik">Usulan Jabatan Tenaga Kependidikan</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Periode</th>
                    <th scope="col" class="px-6 py-3">Jenis Usulan</th>
                    <th scope="col" class="px-6 py-3">Tahun</th>
                    <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3">Tanggal Selesai</th>
                    <th scope="col" class="px-6 py-3">Min Setuju Senat</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Pendaftar</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periodeUsulans as $periode)
                    <tr class="bg-white border-b hover:bg-gray-50" data-jenis="{{ $periode->jenis_usulan }}">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $periode->nama_periode }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($periode->jenis_usulan == 'usulan-jabatan-dosen') bg-blue-100 text-blue-800
                                @elseif($periode->jenis_usulan == 'usulan-jabatan-tendik') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucwords(str_replace('-', ' ', $periode->jenis_usulan)) }}
                            </span>
                        </td>
                            <td class="px-6 py-4">{{ $periode->tahun_periode }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM YYYY') }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}</td>
                            <td class="px-6 py-4">{{ $periode->senat_min_setuju }}</td>
                            <td class="px-6 py-4">
                            @if($periode->status == 'Buka')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Buka</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tutup</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('backend.kepegawaian-universitas.pusat-usulan.show-pendaftar', $periode->id) }}"
                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ $periode->usulans_count ?? 0 }} orang
                            </a>
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.edit', $periode->id) }}"
                               class="font-medium text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('backend.kepegawaian-universitas.periode-usulan.destroy', $periode->id) }}"
                                  method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Data periode usulan belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">
       {{ $periodeUsulans->links() }}
    </div>
</div>



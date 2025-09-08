{{-- Component Modal Tugas Belajar - Hanya untuk jenis usulan tugas belajar --}}
@if($jenisUsulan === 'tugas-belajar' || $jenisUsulan === 'usulan-tugas-belajar')
<!-- Modal Popup untuk Lihat Pengusul Tugas Belajar -->
<div id="modalLihatPengusulTugasBelajar" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border w-11/12 max-w-4xl shadow-2xl rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-t-lg">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i class="fas fa-book-open mr-3"></i>
                Daftar Pengusul Tugas Belajar
            </h3>
            <button type="button" onclick="closeModalLihatPengusulTugasBelajar()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Berikut adalah daftar jenis usulan tugas belajar yang tersedia untuk periode ini.
                </p>
            </div>

            <!-- Tabel Jenis Usulan Tugas Belajar -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center">No</th>
                            <th scope="col" class="px-6 py-4">Jenis Usulan Tugas Belajar</th>
                            <th scope="col" class="px-6 py-4 text-center">Jumlah Pengusul</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dosen -->
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-center font-medium text-gray-900">1</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-chalkboard-teacher text-blue-600 mr-3"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Dosen</div>
                                        <div class="text-xs text-gray-500">Usulan Tugas Belajar dan Perpanjangan Tugas Belajar untuk Dosen</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <span id="count-dosen">0</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="lihatPengusulTugasBelajar('dosen')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-users mr-1"></i>
                                    Lihat Pengusul
                                </button>
                            </td>
                        </tr>

                        <!-- Tenaga Kependidikan -->
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-center font-medium text-gray-900">2</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie text-green-600 mr-3"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Tenaga Kependidikan</div>
                                        <div class="text-xs text-gray-500">Usulan Tugas Belajar dan Perpanjangan Tugas Belajar untuk Tenaga Kependidikan</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span id="count-tenaga-kependidikan">0</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="lihatPengusulTugasBelajar('tenaga_kependidikan')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-users mr-1"></i>
                                    Lihat Pengusul
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
            <button type="button" onclick="closeModalLihatPengusulTugasBelajar()"
                    class="px-6 py-3 text-base font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Tutup
            </button>
        </div>
    </div>
</div>
@endif

@push('scripts')
    <script>
        // Pass data to JavaScript
        window.unitKerjaOptions = @json($unitKerjaOptions);
        window.subUnitKerjaOptions = @json($subUnitKerjaOptions);
        window.subSubUnitKerjaOptions = @json($subSubUnitKerjaOptions);
        window.selectedUnitKerjaId = @json($selectedUnitKerjaId);
        window.selectedSubUnitKerjaId = @json($selectedSubUnitKerjaId);
        window.selectedSubSubUnitKerjaId = @json($selectedSubSubUnitKerjaId);

        // Unit Kerja cascading dropdowns - INLINE SCRIPT
        document.addEventListener('DOMContentLoaded', function() {

            // Unit Kerja cascading dropdowns
            const unitKerjaSelect = document.getElementById('unit_kerja_id');
            const subUnitKerjaSelect = document.getElementById('sub_unit_kerja_id');
            const subSubUnitKerjaSelect = document.getElementById('sub_sub_unit_kerja_id');
            const unitKerjaTerakhirInput = document.getElementById('unit_kerja_terakhir_id');
            const hierarchyDisplay = document.getElementById('unit_kerja_hierarchy_display');



            // Check if elements exist
            if (!unitKerjaSelect || !subUnitKerjaSelect || !subSubUnitKerjaSelect) {
                return;
            }

            // Populate Sub Unit Kerja based on Unit Kerja selection
            window.populateSubUnitKerja = function() {
                const selectedUnitKerjaId = unitKerjaSelect.value;

                // Clear sub unit kerja options
                subUnitKerjaSelect.innerHTML = '<option value="">Pilih Sub Unit Kerja</option>';
                subSubUnitKerjaSelect.innerHTML = '<option value="">Pilih Sub-sub Unit Kerja</option>';

                if (!selectedUnitKerjaId) {
                    subUnitKerjaSelect.disabled = true;
                    subSubUnitKerjaSelect.disabled = true;
                    unitKerjaTerakhirInput.value = '';
                    hierarchyDisplay.classList.add('hidden');
                    return;
                }

                // Enable sub unit kerja select
                subUnitKerjaSelect.disabled = false;

                // Get sub unit kerja data from the page
                const subUnitKerjaData = window.subUnitKerjaOptions || {};
                const availableSubUnits = subUnitKerjaData[selectedUnitKerjaId] || {};



                // Convert the object format to array format for easier handling
                Object.keys(availableSubUnits).forEach(subUnitId => {
                    const option = document.createElement('option');
                    option.value = subUnitId;
                    option.textContent = availableSubUnits[subUnitId];
                    subUnitKerjaSelect.appendChild(option);
                });

                // Reset hierarchy display
                unitKerjaTerakhirInput.value = '';
                hierarchyDisplay.classList.add('hidden');

            };

            // Populate Sub-sub Unit Kerja based on Sub Unit Kerja selection
            window.populateSubSubUnitKerja = function() {
                const selectedSubUnitKerjaId = subUnitKerjaSelect.value;
                console.log('🔄 populateSubSubUnitKerja called with:', selectedSubUnitKerjaId);

                // Clear sub-sub unit kerja options
                subSubUnitKerjaSelect.innerHTML = '<option value="">Pilih Sub-sub Unit Kerja</option>';

                if (!selectedSubUnitKerjaId) {
                    subSubUnitKerjaSelect.disabled = true;
                    unitKerjaTerakhirInput.value = '';
                    hierarchyDisplay.classList.add('hidden');
                    console.log('❌ No sub unit kerja selected, disabling sub-sub dropdown');
                    return;
                }

                // Enable sub-sub unit kerja select
                subSubUnitKerjaSelect.disabled = false;
                console.log('✅ Sub Sub Unit Kerja dropdown enabled');

                // Get sub-sub unit kerja data from the page
                const subSubUnitKerjaData = window.subSubUnitKerjaOptions || {};
                const availableSubSubUnits = subSubUnitKerjaData[selectedSubUnitKerjaId] || {};

                console.log('📊 Available sub-sub units for sub unit kerja', selectedSubUnitKerjaId, ':', availableSubSubUnits);

                // Convert the object format to array format for easier handling
                Object.keys(availableSubSubUnits).forEach(subSubUnitId => {
                    const option = document.createElement('option');
                    option.value = subSubUnitId;
                    option.textContent = availableSubSubUnits[subSubUnitId];
                    subSubUnitKerjaSelect.appendChild(option);
                    console.log('➕ Added option:', subSubUnitId, '-', availableSubSubUnits[subSubUnitId]);
                });

                // Reset hierarchy display
                unitKerjaTerakhirInput.value = '';
                hierarchyDisplay.classList.add('hidden');

                console.log('✅ Sub-sub Unit Kerja populated with', Object.keys(availableSubSubUnits).length, 'options');
            };

            // Display hierarchy when Sub-sub Unit Kerja is selected
            window.displayHierarchy = function() {
                const selectedSubSubUnitId = subSubUnitKerjaSelect.value;
                console.log('🔄 displayHierarchy called with:', selectedSubSubUnitId);

                if (!selectedSubSubUnitId) {
                    unitKerjaTerakhirInput.value = '';
                    hierarchyDisplay.classList.add('hidden');
                    console.log('❌ No sub-sub unit kerja selected, hiding hierarchy');
                    return;
                }

                // Get the selected values from all dropdowns
                const selectedUnitKerjaId = unitKerjaSelect.value;
                const selectedSubUnitKerjaId = subUnitKerjaSelect.value;

                // Get the names from the options data
                const unitKerjaOptions = window.unitKerjaOptions || {};
                const subUnitKerjaOptions = window.subUnitKerjaOptions || {};
                const subSubUnitKerjaOptions = window.subSubUnitKerjaOptions || {};

                const unitKerjaName = unitKerjaOptions[selectedUnitKerjaId] || 'Unknown';
                const subUnitKerjaName = subUnitKerjaOptions[selectedUnitKerjaId]?.[selectedSubUnitKerjaId] || 'Unknown';
                const subSubUnitKerjaName = subSubUnitKerjaOptions[selectedSubUnitKerjaId]?.[selectedSubSubUnitId] || 'Unknown';

                // Build the hierarchy path
                const pathParts = [unitKerjaName, subUnitKerjaName, subSubUnitKerjaName];
                const displayText = pathParts.join(' → ');

                // Update the hierarchy display
                hierarchyDisplay.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <div class="font-semibold text-indigo-800 mb-1">Hierarki Unit Kerja yang Dipilih:</div>
                            <div class="text-indigo-700">${displayText}</div>
                        </div>
                    </div>
                `;
                hierarchyDisplay.classList.remove('hidden');
                unitKerjaTerakhirInput.value = selectedSubSubUnitId;

                console.log('✅ Hierarchy displayed:', displayText);
            };

            // Initialize event listeners with better error handling
            console.log('🔧 Setting up event listeners...');

            if (unitKerjaSelect) {
                console.log('✅ Adding event listener to unit kerja select');
                unitKerjaSelect.addEventListener('change', function(e) {
                    console.log('🎯 Unit Kerja changed to:', this.value);
                    populateSubUnitKerja();
                });

                // Test if event listener is working
                console.log('🧪 Testing unit kerja event listener...');
                const testEvent = new Event('change');
                unitKerjaSelect.dispatchEvent(testEvent);
            } else {
                console.error('❌ Unit Kerja select not found for event listener');
            }

            if (subUnitKerjaSelect) {
                console.log('✅ Adding event listener to sub unit kerja select');
                subUnitKerjaSelect.addEventListener('change', function(e) {
                    console.log('🎯 Sub Unit Kerja changed to:', this.value);
                    populateSubSubUnitKerja();
                });
            } else {
                console.error('❌ Sub Unit Kerja select not found for event listener');
            }

            if (subSubUnitKerjaSelect) {
                console.log('✅ Adding event listener to sub-sub unit kerja select');
                subSubUnitKerjaSelect.addEventListener('change', function(e) {
                    console.log('🎯 Sub Sub Unit Kerja changed to:', this.value);
                    displayHierarchy();
                });
            } else {
                console.error('❌ Sub Sub Unit Kerja select not found for event listener');
            }

            // Initialize with existing data if available
            console.log('🔄 Initializing with existing data...');
            console.log('📊 Selected IDs:', {
                unitKerja: window.selectedUnitKerjaId,
                subUnitKerja: window.selectedSubUnitKerjaId,
                subSubUnitKerja: window.selectedSubSubUnitKerjaId
            });

            if (window.selectedUnitKerjaId && unitKerjaSelect) {
                console.log('🎯 Setting unit kerja to:', window.selectedUnitKerjaId);
                // Set the selected value
                unitKerjaSelect.value = window.selectedUnitKerjaId;
                // Trigger the change event to populate sub units
                populateSubUnitKerja();

                // Set sub unit if available
                if (window.selectedSubUnitKerjaId && subUnitKerjaSelect) {
                    setTimeout(() => {
                        console.log('🎯 Setting sub unit kerja to:', window.selectedSubUnitKerjaId);
                        subUnitKerjaSelect.value = window.selectedSubUnitKerjaId;
                        populateSubSubUnitKerja();

                        // Set sub-sub unit if available
                        if (window.selectedSubSubUnitKerjaId && subSubUnitKerjaSelect) {
                            setTimeout(() => {
                                console.log('🎯 Setting sub-sub unit kerja to:', window.selectedSubSubUnitKerjaId);
                                subSubUnitKerjaSelect.value = window.selectedSubSubUnitKerjaId;
                                displayHierarchy();
                            }, 100);
                        }
                    }, 100);
                }
            }

            // Debug logging to help troubleshoot
            console.log('📊 Data availability check:');
            console.log('Unit Kerja Options:', window.unitKerjaOptions);
            console.log('Sub Unit Kerja Options:', window.subUnitKerjaOptions);
            console.log('Sub Sub Unit Kerja Options:', window.subSubUnitKerjaOptions);

            // Test if data is available
            if (window.unitKerjaOptions && Object.keys(window.unitKerjaOptions).length > 0) {
                console.log('✅ Unit Kerja data is available');
            } else {
                console.log('❌ Unit Kerja data is missing or empty');
            }

            if (window.subUnitKerjaOptions && Object.keys(window.subUnitKerjaOptions).length > 0) {
                console.log('✅ Sub Unit Kerja data is available');
            } else {
                console.log('❌ Sub Unit Kerja data is missing or empty');
            }

            if (window.subSubUnitKerjaOptions && Object.keys(window.subSubUnitKerjaOptions).length > 0) {
                console.log('✅ Sub Sub Unit Kerja data is available');
            } else {
                console.log('❌ Sub Sub Unit Kerja data is missing or empty');
            }

            // Add manual test functions
            window.testUnitKerjaDropdowns = function() {
                console.log('🧪 Manual testing unit kerja dropdowns...');

                // Test with first available unit kerja
                const unitKerjaOptions = window.unitKerjaOptions || {};
                const firstUnitKerjaId = Object.keys(unitKerjaOptions)[0];

                if (firstUnitKerjaId) {
                    console.log('🧪 Testing with unit kerja ID:', firstUnitKerjaId);
                    unitKerjaSelect.value = firstUnitKerjaId;
                    populateSubUnitKerja();
                } else {
                    console.log('❌ No unit kerja options available for testing');
                }
            };

            console.log('=== PERSONAL DATA INLINE SCRIPT COMPLETED ===');
            console.log('💡 Use window.testUnitKerjaDropdowns() to manually test the dropdowns');
        });
    </script>
@endpush

{{-- Personal Data Tab --}}
<div class="space-y-6 lg:space-y-8">
    {{-- Basic Information Section --}}
    <div class="space-y-4 lg:space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-3 lg:gap-4 pb-4 lg:pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Informasi Dasar</h3>
                <p class="text-slate-600">Data pribadi dan identitas pegawai</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            {{-- Nama Lengkap --}}
            <div class="xl:col-span-3 group">
                <label for="nama_lengkap" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                        <span class="break-words">Nama Lengkap (Tanpa Gelar) <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="nama_lengkap"
                           id="nama_lengkap"
                           value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Masukkan nama lengkap tanpa gelar">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Gelar Depan dan Belakang dalam satu baris --}}
            <div class="xl:col-span-3 flex gap-4">
                {{-- Gelar Depan --}}
                <div class="flex-1 group">
                                    <label for="gelar_depan" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                        <div class="flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                            Gelar Depan
                        </div>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="gelar_depan"
                               id="gelar_depan"
                               value="{{ old('gelar_depan', $pegawai->gelar_depan ?? '') }}"
                               class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                               placeholder="Contoh: Dr., Prof.">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Gelar Belakang --}}
                <div class="flex-1 group">
                                    <label for="gelar_belakang" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                        <div class="flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                            Gelar Belakang <span class="text-red-500">*</span>
                        </div>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="gelar_belakang"
                               id="gelar_belakang"
                               value="{{ old('gelar_belakang', $pegawai->gelar_belakang ?? '') }}"
                               class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                               placeholder="Contoh: S.Kom., M.Kom.">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="xl:col-span-3 group">
                <label for="email" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                        Alamat Email <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $pegawai->email ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="contoh@email.com">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- NIP --}}
            <div class="group">
                <label for="nip" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        NIP <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="nip"
                           id="nip"
                           value="{{ old('nip', $pegawai->nip ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="18 Karakter Numerik"
                           maxlength="18">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Nomor Kartu Pegawai --}}
            <div class="group">
                <label for="nomor_kartu_pegawai" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Nomor Kartu Pegawai <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="nomor_kartu_pegawai"
                           id="nomor_kartu_pegawai"
                           value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Nomor kartu pegawai">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal Details Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Data Pribadi</h3>
                <p class="text-slate-600">Informasi tempat lahir, tanggal lahir, dan kontak</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tempat Lahir --}}
            <div class="group">
                <label for="tempat_lahir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Tempat Lahir <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="tempat_lahir"
                           id="tempat_lahir"
                           value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Kota tempat lahir">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tanggal Lahir --}}
            <div class="group">
                <label for="tanggal_lahir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="date"
                           name="tanggal_lahir"
                           id="tanggal_lahir"
                           value="{{ old('tanggal_lahir', isset($pegawai) ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="group">
                <label for="jenis_kelamin" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <select name="jenis_kelamin"
                            id="jenis_kelamin"
                            class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Nomor Handphone --}}
            <div class="group">
                <label for="nomor_handphone" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Nomor Handphone <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="nomor_handphone"
                           id="nomor_handphone"
                           value="{{ old('nomor_handphone', $pegawai->nomor_handphone ?? '') }}"
                           class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="081234567890">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Unit Kerja Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Unit Kerja</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Unit Kerja --}}
            <div class="group">
                <label for="unit_kerja_id" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Unit Kerja <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <select name="unit_kerja_id"
                            id="unit_kerja_id"
                            class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach($unitKerjaOptions as $id => $nama)
                            <option value="{{ $id }}" {{ old('unit_kerja_id', $selectedUnitKerjaId ?? '') == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Sub Unit Kerja --}}
            <div class="group">
                <label for="sub_unit_kerja_id" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Sub Unit Kerja <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <select name="sub_unit_kerja_id"
                            id="sub_unit_kerja_id"
                            class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none"
                            disabled>
                        <option value="">Pilih Sub Unit Kerja</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Sub-sub Unit Kerja --}}
            <div class="group">
                <label for="sub_sub_unit_kerja_id" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Sub-sub Unit Kerja <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <select name="sub_sub_unit_kerja_id"
                            id="sub_sub_unit_kerja_id"
                            class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none"
                            disabled>
                        <option value="">Pilih Sub-sub Unit Kerja</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden field untuk menyimpan ID Sub-sub Unit Kerja yang dipilih --}}
        <input type="hidden" name="unit_kerja_terakhir_id" id="unit_kerja_terakhir_id" value="{{ old('unit_kerja_terakhir_id', $pegawai->unit_kerja_terakhir_id ?? '') }}">

        {{-- Display hierarki yang dipilih --}}
        <div id="unit_kerja_hierarchy_display" class="text-sm text-slate-700 mt-3 font-medium bg-indigo-50/50 p-4 rounded-xl hidden border border-indigo-200/50">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                <div class="flex-1">
                    <div class="font-semibold text-indigo-800 mb-1">Hierarki Unit Kerja yang Dipilih:</div>
                    <div id="hierarchy_text" class="text-indigo-700"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Education Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Pendidikan</h3>
                <p class="text-slate-600">Informasi pendidikan terakhir</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Pendidikan Terakhir --}}
            <div class="group">
                <label for="pendidikan_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                        Pendidikan Terakhir <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    @php
                        $pendidikanOptions =
                        [
                            'Sekolah Dasar (SD)',
                            'Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat',
                            'Sekolah Lanjutan Tingkat Menengah (SLTA)',
                            'Diploma I',
                            'Diploma II',
                            'Diploma III',
                            'Sarjana (S1) / Diploma IV / Sederajat',
                            'Magister (S2) / Sederajat',
                            'Doktor (S3) / Sederajat'
                        ];
                    @endphp
                    <select name="pendidikan_terakhir"
                            id="pendidikan_terakhir"
                            class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none">
                        <option value="">Pilih Pendidikan Terakhir</option>
                        @foreach($pendidikanOptions as $option)
                            <option value="{{ $option }}" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Nama Universitas/Sekolah --}}
            <div class="group">
                <label for="nama_universitas_sekolah" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="building" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                        Nama Universitas/Sekolah
                    </div>
                </label>
                <input type="text"
                       name="nama_universitas_sekolah"
                       id="nama_universitas_sekolah"
                       value="{{ old('nama_universitas_sekolah', $pegawai->nama_universitas_sekolah ?? '') }}"
                       placeholder="Contoh: Universitas Mulawarman, Universitas Indonesia, SMAN 1 Samarinda"
                       class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            {{-- Nama Prodi/Jurusan S2 --}}
            <div class="group">
                <label for="nama_prodi_jurusan_s2" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="book-open" class="w-4 h-4 text-indigo-600 flex-shrink-0"></i>
                        Nama Prodi/Jurusan
                    </div>
                </label>
                <input type="text"
                       name="nama_prodi_jurusan"
                       id="nama_prodi_jurusan"
                       value="{{ old('nama_prodi_jurusan', $pegawai->nama_prodi_jurusan ?? '') }}"
                       placeholder="Contoh: Magister Teknologi Informasi, Magister Manajemen, Magister Pendidikan"
                       class="w-full px-4 py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md">
            </div>
        </div>
    </div>

    {{-- Password Change Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Ubah Password</h3>
                <p class="text-slate-600">Perbarui password akun pegawai</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6">
            {{-- Password Baru --}}
            <div class="group">
                <label for="password" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span class="break-words">Password Baru</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="password"
                           name="password"
                           id="password"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base"
                           placeholder="Minimal 8 karakter"
                           minlength="8">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4">
                                                        <button type="button"
                                        data-toggle="password"
                                        class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg id="password-eye" class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                    </div>
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="group">
                <label for="password_confirmation" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span class="break-words">Konfirmasi Password</span>
                    </div>
                </label>
                <div class="relative">
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base"
                           placeholder="Ulangi password baru"
                           minlength="8">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4">
                                                        <button type="button"
                                        data-toggle="password_confirmation"
                                        class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg id="password_confirmation-eye" class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password Strength Indicator --}}
        <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-200">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-slate-700">Kekuatan Password</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div id="password-strength" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <div id="password-feedback" class="text-xs text-slate-600 mt-2"></div>
        </div>
    </div>
</div>

<style>
    /* Custom animations for form elements */
    .group:hover input,
    .group:hover select {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1);
    }

    .group input:focus,
    .group select:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.2);
    }

    /* Smooth transitions for all form elements */
    input, select, label {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom select styling */
    select {
        background-image: none !important;
    }

    /* Hover effects for labels */
    .group:hover label {
        transform: translateX(5px);
    }

    /* Animation for path display */
    #unit_kerja_path_display {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>


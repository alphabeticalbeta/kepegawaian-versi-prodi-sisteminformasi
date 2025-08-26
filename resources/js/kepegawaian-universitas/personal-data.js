// Personal Data JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== PERSONAL DATA SCRIPT LOADED ===');

    // Unit Kerja cascading dropdowns
    const unitKerjaSelect = document.getElementById('unit_kerja_id');
    const subUnitKerjaSelect = document.getElementById('sub_unit_kerja_id');
    const subSubUnitKerjaSelect = document.getElementById('sub_sub_unit_kerja_id');
    const unitKerjaTerakhirInput = document.getElementById('unit_kerja_id');
    const hierarchyDisplay = document.getElementById('unit_kerja_hierarchy_display');

    console.log('Elements found:', {
        unitKerjaSelect: !!unitKerjaSelect,
        subUnitKerjaSelect: !!subUnitKerjaSelect,
        subSubUnitKerjaSelect: !!subSubUnitKerjaSelect,
        unitKerjaTerakhirInput: !!unitKerjaTerakhirInput,
        hierarchyDisplay: !!hierarchyDisplay
    });

    // Check if elements exist
    if (!unitKerjaSelect) {
        console.error('❌ Unit Kerja select element not found!');
        return;
    }
    if (!subUnitKerjaSelect) {
        console.error('❌ Sub Unit Kerja select element not found!');
        return;
    }
    if (!subSubUnitKerjaSelect) {
        console.error('❌ Sub Sub Unit Kerja select element not found!');
        return;
    }

    // Populate Sub Unit Kerja based on Unit Kerja selection
    window.populateSubUnitKerja = function() {
        const selectedUnitKerjaId = unitKerjaSelect.value;
        console.log('🔄 populateSubUnitKerja called with:', selectedUnitKerjaId);

        // Clear sub unit kerja options
        subUnitKerjaSelect.innerHTML = '<option value="">Pilih Sub Unit Kerja</option>';
        subSubUnitKerjaSelect.innerHTML = '<option value="">Pilih Sub-sub Unit Kerja</option>';

        if (!selectedUnitKerjaId) {
            subUnitKerjaSelect.disabled = true;
            subSubUnitKerjaSelect.disabled = true;
            unitKerjaTerakhirInput.value = '';
            hierarchyDisplay.classList.add('hidden');
            console.log('❌ No unit kerja selected, disabling dropdowns');
            return;
        }

        // Enable sub unit kerja select
        subUnitKerjaSelect.disabled = false;
        console.log('✅ Sub Unit Kerja dropdown enabled');

        // Get sub unit kerja data from the page
        const subUnitKerjaData = window.subUnitKerjaOptions || {};
        const availableSubUnits = subUnitKerjaData[selectedUnitKerjaId] || {};

        console.log('📊 Available sub units for unit kerja', selectedUnitKerjaId, ':', availableSubUnits);

        // Convert the object format to array format for easier handling
        Object.keys(availableSubUnits).forEach(subUnitId => {
            const option = document.createElement('option');
            option.value = subUnitId;
            option.textContent = availableSubUnits[subUnitId];
            subUnitKerjaSelect.appendChild(option);
            console.log('➕ Added option:', subUnitId, '-', availableSubUnits[subUnitId]);
        });

        // Reset hierarchy display
        unitKerjaTerakhirInput.value = '';
        hierarchyDisplay.classList.add('hidden');

        console.log('✅ Sub Unit Kerja populated with', Object.keys(availableSubUnits).length, 'options');
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

    // Password visibility toggle
    window.togglePasswordVisibility = function(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleButton = document.querySelector(`[data-toggle="${fieldId}"]`);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                </svg>
            `;
        } else {
            passwordField.type = 'password';
            toggleButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            `;
        }
    };

    // Password strength checker
    window.checkPasswordStrength = function(fieldId) {
        const password = document.getElementById(fieldId).value;
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        if (!strengthBar || !strengthText) return;

        let strength = 0;
        let feedback = '';

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        switch (strength) {
            case 0:
            case 1:
                strengthBar.className = 'h-2 bg-red-500 rounded-full transition-all duration-300';
                strengthText.textContent = 'Sangat Lemah';
                strengthText.className = 'text-red-600 font-medium';
                break;
            case 2:
                strengthBar.className = 'h-2 bg-orange-500 rounded-full transition-all duration-300';
                strengthText.textContent = 'Lemah';
                strengthText.className = 'text-orange-600 font-medium';
                break;
            case 3:
                strengthBar.className = 'h-2 bg-yellow-500 rounded-full transition-all duration-300';
                strengthText.textContent = 'Sedang';
                strengthText.className = 'text-yellow-600 font-medium';
                break;
            case 4:
                strengthBar.className = 'h-2 bg-blue-500 rounded-full transition-all duration-300';
                strengthText.textContent = 'Kuat';
                strengthText.className = 'text-blue-600 font-medium';
                break;
            case 5:
                strengthBar.className = 'h-2 bg-green-500 rounded-full transition-all duration-300';
                strengthText.textContent = 'Sangat Kuat';
                strengthText.className = 'text-green-600 font-medium';
                break;
        }

        const width = (strength / 5) * 100;
        strengthBar.style.width = width + '%';
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

    // Initialize password strength checker
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', () => checkPasswordStrength('password'));
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

    console.log('=== PERSONAL DATA SCRIPT COMPLETED ===');
    console.log('💡 Use window.testUnitKerjaDropdowns() to manually test the dropdowns');
});

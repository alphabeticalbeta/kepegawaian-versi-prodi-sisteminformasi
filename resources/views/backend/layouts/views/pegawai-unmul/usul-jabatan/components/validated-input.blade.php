{{-- components/validated-input.blade.php --}}
{{-- Reusable component untuk input field dengan validasi --}}

@php
    $fieldName = $fieldName ?? '';
    $label = $label ?? '';
    $value = $value ?? '';
    $type = $type ?? 'text';
    $placeholder = $placeholder ?? '';
    $required = $required ?? false;
    $readonly = $readonly ?? false;
    $disabled = $disabled ?? false;
    $fieldGroup = $fieldGroup ?? '';
    $validationData = $validationData ?? [];
    $catatanPerbaikan = $catatanPerbaikan ?? [];
    $isEditMode = $isEditMode ?? false;
    
    // Check validation status
    $isFieldInvalid = isFieldInvalid($fieldGroup, $fieldName, $validationData, $catatanPerbaikan);
    $allValidationNotes = [];
    
    if ($isEditMode) {
        $allValidationNotes = getFieldValidationNotes($fieldGroup, $fieldName, $validationData, $catatanPerbaikan);
    }
@endphp

<div class="form-field-group">
    <label for="{{ $fieldName }}" class="block text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-gray-700' }} mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
        @if($isFieldInvalid)
            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                Perlu Perbaikan
            </span>
        @endif
    </label>
    
    @if($type === 'textarea')
        <textarea 
            id="{{ $fieldName }}" 
            name="{{ $fieldName }}" 
            rows="4"
            placeholder="{{ $placeholder }}"
            class="block w-full border rounded-lg shadow-sm py-3 px-4
                @if($readonly || $disabled) bg-gray-50 text-gray-600 @else bg-white @endif
                @if($isFieldInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            data-field-group="{{ $fieldGroup }}"
            data-field-name="{{ $fieldName }}"
        >{{ old($fieldName, $value) }}</textarea>
    @elseif($type === 'select')
        <select 
            id="{{ $fieldName }}" 
            name="{{ $fieldName }}"
            class="block w-full border rounded-lg shadow-sm py-3 px-4
                @if($readonly || $disabled) bg-gray-50 text-gray-600 @else bg-white @endif
                @if($isFieldInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
            @if($required) required @endif
            @if($readonly) disabled @endif
            @if($disabled) disabled @endif
            data-field-group="{{ $fieldGroup }}"
            data-field-name="{{ $fieldName }}"
        >
            <option value="">-- Pilih {{ $label }} --</option>
            @foreach($options ?? [] as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ old($fieldName, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @else
        <input 
            id="{{ $fieldName }}" 
            name="{{ $fieldName }}" 
            type="{{ $type }}"
            value="{{ old($fieldName, $value) }}"
            placeholder="{{ $placeholder }}"
            class="block w-full border rounded-lg shadow-sm py-3 px-4
                @if($readonly || $disabled) bg-gray-50 text-gray-600 @else bg-white @endif
                @if($isFieldInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            data-field-group="{{ $fieldGroup }}"
            data-field-name="{{ $fieldName }}"
        >
    @endif
    
    {{-- Error messages from Laravel validation --}}
    @error($fieldName)
        <div class="field-error text-sm text-red-600 mt-1 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            {{ $message }}
        </div>
    @enderror
    
    {{-- Validation notes from admin --}}
    @if($isFieldInvalid && !empty($allValidationNotes))
        <div class="mt-2 space-y-2">
            @foreach($allValidationNotes as $note)
                <div class="text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                    <div class="flex items-start gap-1">
                        <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                        <div>
                            <strong>{{ $note['role'] }}:</strong><br>
                            {{ $note['note'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($isFieldInvalid && isset($fieldValidation['keterangan']))
        <div class="text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400 mt-2">
            <div class="flex items-start gap-1">
                <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                <div>
                    <strong>Catatan Perbaikan:</strong><br>
                    {{ $fieldValidation['keterangan'] }}
                </div>
            </div>
        </div>
    @endif
    
    {{-- Help text --}}
    @if(isset($helpText))
        <p class="text-xs text-gray-500 mt-1">{{ $helpText }}</p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const field = document.getElementById('{{ $fieldName }}');
    if (field) {
        // Add validation attributes based on field type
        @if($type === 'email')
            field.setAttribute('pattern', '^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$');
        @elseif($fieldName === 'nip')
            field.setAttribute('pattern', '^\\d{18,20}$');
        @elseif($fieldName === 'nomor_handphone')
            field.setAttribute('pattern', '^(\\+62|62|0)8[1-9][0-9]{6,9}$');
        @elseif($fieldName === 'url_profil_sinta')
            field.setAttribute('pattern', '^https?://.+$');
        @endif
        
        // Real-time validation
        field.addEventListener('input', function() {
            validateField(this);
        });
        
        field.addEventListener('blur', function() {
            validateField(this);
        });
    }
});
</script>

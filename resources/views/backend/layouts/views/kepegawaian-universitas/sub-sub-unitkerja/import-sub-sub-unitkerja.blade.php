@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Import Sub-Sub Unit Kerja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('backend.kepegawaian-universitas.sub-sub-unitkerja.index') }}" 
                   class="p-2 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">Import Sub-Sub Unit Kerja</h1>
                    <p class="text-slate-600">Upload file Excel untuk import data Sub-Sub Unit Kerja</p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Download Template Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Download Template
                    </h2>
                </div>
                <div class="p-8">
                    <p class="text-slate-600 mb-6">
                        Sebelum upload data, silakan download template Excel terlebih dahulu untuk memastikan format data sesuai.
                    </p>
                    <a href="{{ route('backend.kepegawaian-universitas.sub-sub-unitkerja.download-template') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-8 5h16l-7-7-7 7z"></path>
                        </svg>
                        Download Template Excel
                    </a>
                </div>
            </div>

            <!-- Upload Form Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload File Excel
                    </h2>
                </div>
                
                <form action="{{ route('backend.kepegawaian-universitas.sub-sub-unitkerja.import') }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="p-8">
                    @csrf

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="file" class="block text-sm font-medium text-slate-700 mb-2">
                            Pilih File Excel <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-green-500 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600">
                                    <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                        <span>Upload file</span>
                                        <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls" required>
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">Excel files up to 10MB</p>
                            </div>
                        </div>
                        @error('file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-medium text-yellow-800 mb-3">Petunjuk Upload:</h3>
                        <ul class="text-sm text-yellow-700 space-y-2">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-5 h-5 text-yellow-600 mr-2">•</span>
                                File harus berformat Excel (.xlsx atau .xls)
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-5 h-5 text-yellow-600 mr-2">•</span>
                                Gunakan template yang telah disediakan
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-5 h-5 text-yellow-600 mr-2">•</span>
                                Pastikan semua kolom wajib telah diisi
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-5 h-5 text-yellow-600 mr-2">•</span>
                                Nama Sub Unit Kerja harus sudah ada di sistem
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('backend.kepegawaian-universitas.sub-sub-unitkerja.index') }}" 
                           class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const uploadArea = fileInput.closest('.border-dashed');
    
    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            updateUploadArea(file.name);
        }
    });
    
    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-green-500', 'bg-green-50');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-green-500', 'bg-green-50');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-green-500', 'bg-green-50');
        
        const file = e.dataTransfer.files[0];
        if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
            fileInput.files = e.dataTransfer.files;
            updateUploadArea(file.name);
        }
    });
    
    function updateUploadArea(fileName) {
        uploadArea.innerHTML = `
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-slate-600">
                    <span class="font-medium text-green-600">${fileName}</span>
                </div>
                <p class="text-xs text-slate-500">File siap untuk diupload</p>
            </div>
        `;
    }
});
</script>
@endsection

<!-- Default Usulan Form -->
<div class="space-y-6">
    <!-- Basic Information -->
    <div>
        <h3 class="text-lg font-medium text-slate-900 mb-4">Informasi Dasar</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="pegawai_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Pegawai <span class="text-red-500">*</span>
                </label>
                <select name="pegawai_id"
                        id="pegawai_id"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pegawai_id') border-red-300 @else border-slate-200 @enderror"
                        required>
                    <option value="">Pilih Pegawai</option>
                    {{-- Options will be populated via AJAX or server-side --}}
                </select>
                @error('pegawai_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jenis_usulan_detail" class="block text-sm font-medium text-slate-700 mb-2">
                    Detail Jenis Usulan
                </label>
                <input type="text"
                       name="jenis_usulan_detail"
                       id="jenis_usulan_detail"
                       value="{{ old('jenis_usulan_detail') }}"
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_usulan_detail') border-red-300 @else border-slate-200 @enderror"
                       placeholder="Contoh: {{ $namaUsulan }} Tahap 1">
                @error('jenis_usulan_detail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Description -->
    <div>
        <h3 class="text-lg font-medium text-slate-900 mb-4">Deskripsi Usulan</h3>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-2">
                Deskripsi/Keterangan
            </label>
            <textarea name="deskripsi"
                      id="deskripsi"
                      rows="4"
                      class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deskripsi') border-red-300 @else border-slate-200 @enderror"
                      placeholder="Jelaskan detail usulan yang diajukan...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Documents Upload -->
    <div>
        <h3 class="text-lg font-medium text-slate-900 mb-4">Dokumen Pendukung</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="dokumen_utama" class="block text-sm font-medium text-slate-700 mb-2">
                    Dokumen Utama <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       name="dokumen_utama"
                       id="dokumen_utama"
                       accept=".pdf,.doc,.docx"
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dokumen_utama') border-red-300 @else border-slate-200 @enderror"
                       required>
                <p class="mt-1 text-xs text-slate-500">Format: PDF, DOC, DOCX. Maksimal 10MB</p>
                @error('dokumen_utama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="dokumen_pendukung" class="block text-sm font-medium text-slate-700 mb-2">
                    Dokumen Pendukung
                </label>
                <input type="file"
                       name="dokumen_pendukung[]"
                       id="dokumen_pendukung"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       multiple
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dokumen_pendukung') border-red-300 @else border-slate-200 @enderror">
                <p class="mt-1 text-xs text-slate-500">Format: PDF, DOC, DOCX, JPG, PNG. Maksimal 5MB per file</p>
                @error('dokumen_pendukung')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Priority and Notes -->
    <div>
        <h3 class="text-lg font-medium text-slate-900 mb-4">Informasi Tambahan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="prioritas" class="block text-sm font-medium text-slate-700 mb-2">
                    Prioritas
                </label>
                <select name="prioritas"
                        id="prioritas"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('prioritas') border-red-300 @enderror">
                    <option value="Normal" {{ old('prioritas') == 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="Tinggi" {{ old('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="Mendesak" {{ old('prioritas') == 'Mendesak' ? 'selected' : '' }}>Mendesak</option>
                </select>
                @error('prioritas')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="target_selesai" class="block text-sm font-medium text-slate-700 mb-2">
                    Target Selesai
                </label>
                <input type="date"
                       name="target_selesai"
                       id="target_selesai"
                       value="{{ old('target_selesai') }}"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('target_selesai') border-red-300 @enderror">
                @error('target_selesai')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    <div>
        <label for="catatan_tambahan" class="block text-sm font-medium text-slate-700 mb-2">
            Catatan Tambahan
        </label>
        <textarea name="catatan_tambahan"
                  id="catatan_tambahan"
                  rows="3"
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('catatan_tambahan') border-red-300 @enderror"
                  placeholder="Catatan tambahan atau informasi khusus lainnya...">{{ old('catatan_tambahan') }}</textarea>
        @error('catatan_tambahan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

    <script>
        // XSS Protection Function
        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate pegawai dropdown via AJAX
    // This would be implemented based on your existing pegawai data structure

    // File validation
    const dokumenUtama = document.getElementById('dokumen_utama');
    const dokumenPendukung = document.getElementById('dokumen_pendukung');

    function validateFileSize(input, maxSize) {
        const files = input.files;
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                alert(`File ${escapeHtml(files[i].name)} terlalu besar. Maksimal ${maxSize / (1024 * 1024)}MB`);
                input.value = '';
                return false;
            }
        }
        return true;
    }

    if (dokumenUtama) {
        dokumenUtama.addEventListener('change', function() {
            validateFileSize(this, 10 * 1024 * 1024); // 10MB
        });
    }

    if (dokumenPendukung) {
        dokumenPendukung.addEventListener('change', function() {
            validateFileSize(this, 5 * 1024 * 1024); // 5MB
        });
    }
});
</script>

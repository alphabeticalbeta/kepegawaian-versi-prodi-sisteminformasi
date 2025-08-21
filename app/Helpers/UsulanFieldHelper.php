<?php

namespace App\Helpers;

use App\Models\BackendUnivUsulan\Usulan;
use Carbon\Carbon;

class UsulanFieldHelper
{
    protected $usulan;

    public function __construct(Usulan $usulan)
    {
        $this->usulan = $usulan;
    }

    public function getFieldValue(string $category, string $field): string
    {
        switch ($category) {
            case 'data_pribadi':
            case 'data_kepegawaian':
            case 'data_pendidikan':
            case 'data_kinerja':
                return $this->getPegawaiData($field);

            case 'dokumen_profil':
                return $this->getDokumenProfilLink($field);

            case 'karya_ilmiah':
                return $this->getKaryaIlmiahData($field);

            case 'dokumen_usulan':
                return $this->getDokumenUsulanLink($field);

            // ADDED: Handle BKD documents
            case 'dokumen_bkd':
                return $this->getDokumenUsulanLink($field); // Use same method as dokumen_usulan

            case 'dokumen_pendukung':
                return $this->getDokumenPendukungValue($field);

            case 'dokumen_admin_fakultas':
                return $this->getDokumenAdminFakultasValue($field);

            default:
                return '-';
        }
    }

    protected function getPegawaiData(string $field): string
    {
        $pegawai = $this->usulan->pegawai;
        if (!$pegawai) return '-';

        // Menangani kasus khusus
        switch ($field) {
            case 'pangkat_saat_usul':
                return $pegawai->pangkat->pangkat ?? '-';
            case 'jabatan_saat_usul':
                return $pegawai->jabatan->jabatan ?? '-';
            case 'unit_kerja_saat_usul':
                return $pegawai->unitKerja->nama ?? '-';
            case 'url_profil_sinta':
                $urlValue = $pegawai->{$field} ?? null;
                if ($urlValue && filter_var($urlValue, FILTER_VALIDATE_URL)) {
                    return '<a href="' . $urlValue . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View Link</a>';
                }
                return $urlValue ?: '-';
            case 'tanggal_lahir':
            case 'tmt_pangkat':
            case 'tmt_jabatan':
            case 'tmt_cpns':
            case 'tmt_pns':
                $rawValue = $pegawai->{$field} ?? null;
                return $rawValue ? Carbon::parse($rawValue)->isoFormat('D MMMM YYYY') : '-';
            default:
                return $pegawai->{$field} ?? '-';
        }
    }

    protected function getDokumenProfilLink(string $field): string
    {
        $dokumenPath = $this->usulan->pegawai->{$field} ?? null;
        if (!empty($dokumenPath)) {
            // DETECT ROUTE berdasarkan konteks user
            if (request()->is('admin-fakultas/*')) {
                $route = route('admin-fakultas.usulan.show-pegawai-document', [$this->usulan->id, $field]);
            } else {
                $route = route('backend.admin-univ-usulan.data-pegawai.show-document', [$this->usulan->pegawai_id, $field]);
            }
            return '<a href="' . $route . '" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">✓ Lihat Dokumen</a>';
        }
        return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ Belum diunggah</span>';
    }

    protected function getDokumenUsulanLink(string $field): string
    {
        // 1) Coba ambil path langsung dari key yang diminta (baru & lama)
        $docPath = $this->usulan->getDocumentPath($field);

        // 2) Jika kosong dan ini BKD kanonis (bkd_semester_N), map ke key lama (bkd_{ganjil|genap}_YYYY_YYYY)
        if (empty($docPath) && str_starts_with($field, 'bkd_semester_') && $this->usulan->periodeUsulan) {
            $num = (int) str_replace('bkd_semester_', '', $field);
            if ($num >= 1 && $num <= 4) {
                // Label semester ke-N (mundur) dari periode berjalan
                $labels = $this->generateBkdLabelsFromPeriode($this->usulan->periodeUsulan);
                if (isset($labels[$num - 1])) {
                    // Contoh: "BKD Semester Genap 2023/2024"
                    if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $labels[$num - 1], $m)) {
                        $sem = strtolower($m[1]); // ganjil|genap
                        $y1  = $m[2];
                        $y2  = $m[3];

                        // 2a) Coba exact legacy key
                        $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;
                        $docPath = $this->usulan->getDocumentPath($legacyKey);

                        // 2b) Fallback scan semua key BKD (kalau penamaannya sedikit berbeda)
                        if (empty($docPath)) {
                            // Struktur baru: data_usulan['dokumen_usulan'][key]['path']
                            $bucket = $this->usulan->data_usulan['dokumen_usulan'] ?? [];
                            foreach ($bucket as $k => $info) {
                                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                        $docPath = is_array($info) ? ($info['path'] ?? null) : $info;
                                        break;
                                    }
                                }
                            }
                        }
                        if (empty($docPath)) {
                            // Struktur lama (flat): data_usulan[key] = path
                            $flat = $this->usulan->data_usulan ?? [];
                            foreach ($flat as $k => $info) {
                                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                                        $docPath = is_array($info) ? ($info['path'] ?? null) : $info;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // 3) Jika ada path → buat link; kalau tidak → info belum diunggah
        if (!empty($docPath)) {
            // DETECT ROUTE berdasarkan konteks user
            if (request()->is('pegawai-unmul/*')) {
                $route = route('pegawai-unmul.usulan-jabatan.show-document', [$this->usulan->id, $field]);
            } elseif (request()->is('admin-fakultas/*')) {
                $route = route('admin-fakultas.usulan.show-document', [$this->usulan->id, $field]);
            } else {
                $route = route('backend.admin-univ-usulan.pusat-usulan.show-document', [$this->usulan->id, $field]);
            }
            // Judul baris sudah menampilkan label semesternya, jadi link cukup "Lihat Dokumen"
            $label = '✓ Lihat Dokumen';
            return '<a href="' . $route . '" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">' . $label . '</a>';
        }

        return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ Belum diunggah</span>';
    }

    protected function getKaryaIlmiahData(string $field): string
    {
        $data = $this->usulan->data_usulan['karya_ilmiah'] ?? [];

        if (str_starts_with($field, 'link_')) {
            $linkKey = str_replace('link_', '', $field);
            $linkValue = $data['links'][$linkKey] ?? $data[$field] ?? null;

            if ($linkValue && filter_var($linkValue, FILTER_VALIDATE_URL)) {
                // Special handling for Sinta link
                if ($field === 'link_sinta') {
                    return '<a href="' . $linkValue . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View Link</a>';
                }
                return '<a href="' . $linkValue . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat Link</a>';
            }
            return $linkValue ?: '-';
        }

        if ($field === 'karya_ilmiah') {
            return $data['jenis_karya'] ?? $data['karya_ilmiah'] ?? '-';
        }

        return $data[$field] ?? '-';
    }

    private function formatBkdLabel(string $field): string
    {
        // Jika format baru (bkd_semester_1, bkd_semester_2, dll)
        if (preg_match('/^bkd_semester_(\d+)$/', $field, $matches)) {
            $semesterNumber = (int) $matches[1];

            // Get periode usulan untuk determine tahun akademik
            if ($this->usulan->periodeUsulan) {
                $labels = $this->generateBkdLabelsFromPeriode($this->usulan->periodeUsulan);

                // Return label berdasarkan nomor semester (1-4)
                if (isset($labels[$semesterNumber - 1])) {
                    return $labels[$semesterNumber - 1];
                }
            }

            // Fallback jika tidak ada periode
            return "BKD Semester " . $semesterNumber;
        }

        // Jika format lama (bkd_ganjil_2024_2025)
        if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/', $field, $matches)) {
            $semester = ucfirst($matches[1]);
            $tahun1 = $matches[2];
            $tahun2 = $matches[3];
            return "BKD Semester {$semester} {$tahun1}/{$tahun2}";
        }

        // Default fallback
        return str_replace(['bkd_', '_'], ['BKD ', ' '], ucwords($field));
    }

    /**
     * Generate BKD labels based on periode usulan
     * ADDED: Helper untuk generate 4 semester labels
     */
    private function generateBkdLabelsFromPeriode($periode): array
    {
        $startDate = \Carbon\Carbon::parse($periode->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester based on month
        if ($month >= 1 && $month <= 6) {
            // Januari - Juni: Semester Genap sedang berjalan
            $currentSemester = 'Genap';
            $currentYear = $year - 1; // Tahun akademik dimulai tahun sebelumnya
        } elseif ($month >= 7 && $month <= 12) {
            // Juli - Desember: Semester Ganjil sedang berjalan
            $currentSemester = 'Ganjil';
            $currentYear = $year;
        }

        $labels = [];
        $tempSemester = $currentSemester;
        $tempYear = $currentYear;

        // Generate 4 semester terakhir (mundur dari semester saat ini)
        for ($i = 0; $i < 4; $i++) {
            // Move to previous semester
            if ($tempSemester === 'Ganjil') {
                // Ganjil ke Genap tahun sebelumnya
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                // Genap ke Ganjil tahun yang sama
                $tempSemester = 'Ganjil';
            }

            $academicYear = $tempYear . '/' . ($tempYear + 1);
            $labels[] = "BKD Semester {$tempSemester} {$academicYear}";
        }

        return $labels;
    }

    private function getDokumenPendukungValue(string $field): string
    {
        // Data disimpan oleh Admin Fakultas ketika "forward_to_university"
        $data = $this->usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];

        // Field nomor → tampilkan teks
        if (in_array($field, ['nomor_surat_usulan', 'nomor_berita_senat'], true)) {
            $rawValue = $data[$field] ?? '';

            // FIXED: Handle array values gracefully
            if (is_array($rawValue)) {
                // If it's an array, try to get meaningful value
                $val = $rawValue['value'] ?? $rawValue['nomor'] ?? $rawValue[0] ?? '';
                if (is_array($val)) {
                    // If still array, convert to JSON for debugging
                    \Log::warning('Unexpected array in dokumen_pendukung field', [
                        'field' => $field,
                        'usulan_id' => $this->usulan->id,
                        'raw_value' => $rawValue
                    ]);
                    return '<span class="text-yellow-600">⚠ Data format error</span>';
                }
            } else {
                $val = $rawValue;
            }

            $val = trim((string)$val);
            return $val !== '' ? e($val) : '-';
        }

        // Field file → baca path lalu buat link (disk: public)
        if ($field === 'file_surat_usulan' || $field === 'file_berita_senat') {
            // Nama key path di data: file_*_path (lihat controller AdminFakultas)
            $pathKey = $field . '_path';
            $path = $data[$pathKey] ?? $data[$field] ?? null;

            // FIXED: Handle array paths
            if (is_array($path)) {
                $path = $path['path'] ?? $path['value'] ?? $path[0] ?? null;
                if (is_array($path)) {
                    \Log::warning('Unexpected array in dokumen_pendukung file path', [
                        'field' => $field,
                        'usulan_id' => $this->usulan->id,
                        'raw_path' => $data[$pathKey] ?? $data[$field]
                    ]);
                    return '<span class="text-yellow-600">⚠ Path format error</span>';
                }
            }

            if (!$path) {
                return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ Belum diunggah</span>';
            }

            if (\Storage::disk('public')->exists($path)) {
                // Use proper route for Tim Penilai
                if (request()->is('penilai-universitas/*')) {
                    $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$this->usulan->id, $field]);
                } else {
                    $url = \Storage::disk('public')->url($path);
                }
                return '<a href="' . ($route ?? $url) . '" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">✓ Lihat Dokumen</a>';
            }

            return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ File tidak ditemukan</span>';
        }

        // Default
        return '-';
    }

    /**
     * Get dokumen admin fakultas value for display
     */
    private function getDokumenAdminFakultasValue(string $field): string
    {
        // Data disimpan oleh Admin Fakultas ketika "forward_to_university"
        $data = $this->usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];

        // Field nomor → tampilkan teks
        if (in_array($field, ['nomor_surat_usulan', 'nomor_berita_senat'], true)) {
            $rawValue = $data[$field] ?? '';

            // Handle array values gracefully
            if (is_array($rawValue)) {
                $val = $rawValue['value'] ?? $rawValue['nomor'] ?? $rawValue[0] ?? '';
                if (is_array($val)) {
                    \Log::warning('Unexpected array in dokumen_admin_fakultas field', [
                        'field' => $field,
                        'usulan_id' => $this->usulan->id,
                        'raw_value' => $rawValue
                    ]);
                    return '<span class="text-yellow-600">⚠ Data format error</span>';
                }
            } else {
                $val = $rawValue;
            }

            $val = trim((string)$val);
            return $val !== '' ? e($val) : '-';
        }

        // Field file → baca path lalu buat link (disk: public)
        if ($field === 'file_surat_usulan' || $field === 'file_berita_senat') {
            // Nama key path di data: file_*_path
            $pathKey = $field . '_path';
            $path = $data[$pathKey] ?? $data[$field] ?? null;

            // Handle array paths
            if (is_array($path)) {
                $path = $path['path'] ?? $path['value'] ?? $path[0] ?? null;
                if (is_array($path)) {
                    \Log::warning('Unexpected array in dokumen_admin_fakultas file path', [
                        'field' => $field,
                        'usulan_id' => $this->usulan->id,
                        'raw_path' => $data[$pathKey] ?? $data[$field]
                    ]);
                    return '<span class="text-yellow-600">⚠ Path format error</span>';
                }
            }

            if (!$path) {
                return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ Belum diunggah</span>';
            }

            if (\Storage::disk('public')->exists($path)) {
                // Use proper route for Tim Penilai
                if (request()->is('penilai-universitas/*')) {
                    $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$this->usulan->id, $field]);
                } else {
                    $url = \Storage::disk('public')->url($path);
                }
                return '<a href="' . ($route ?? $url) . '" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">✓ Lihat Dokumen</a>';
            }

            return '<span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-500 rounded-md">✗ File tidak ditemukan</span>';
        }

        // Default
        return '-';
    }

    /**
     * Get validation label for display
     * ENHANCED: Uppercase field names except for article links and document links
     */
    public function getValidationLabel(string $category, string $field, array $bkdLabels = []): string
    {
        // Special handling for BKD documents
        if ($category === 'dokumen_bkd' && \Str::startsWith($field, 'bkd_')) {
            return strtoupper($bkdLabels[$field] ?? ucwords(str_replace('_', ' ', $field)));
        }

        // Special handling for dokumen pendukung
        elseif ($category === 'dokumen_pendukung') {
            $labels = [
                'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
                'file_surat_usulan'  => 'Dokumen Surat Usulan Fakultas',
                'nomor_berita_senat' => 'Nomor Surat Senat',
                'file_berita_senat'  => 'Dokumen Surat Senat',
            ];
            return strtoupper($labels[$field] ?? ucwords(str_replace('_', ' ', $field)));
        }

        // Special handling for dokumen admin fakultas
        elseif ($category === 'dokumen_admin_fakultas') {
            $labels = [
                'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
                'file_surat_usulan'  => 'Dokumen Surat Usulan Fakultas',
                'nomor_berita_senat' => 'Nomor Berita Senat',
                'file_berita_senat'  => 'Dokumen Berita Senat',
            ];
            return strtoupper($labels[$field] ?? ucwords(str_replace('_', ' ', $field)));
        }

        // Special handling for article links - keep as is
        elseif ($category === 'karya_ilmiah' && in_array($field, ['link_artikel', 'link_sinta', 'link_scopus', 'link_scimago', 'link_wos'])) {
            return ucwords(str_replace('_', ' ', $field));
        }

        // Special handling for document links - keep as is
        elseif (in_array($category, ['dokumen_profil', 'dokumen_usulan']) && str_contains($field, 'link_')) {
            return ucwords(str_replace('_', ' ', $field));
        }

        // Default: uppercase for all other fields
        return strtoupper(ucwords(str_replace('_', ' ', $field)));
    }

}

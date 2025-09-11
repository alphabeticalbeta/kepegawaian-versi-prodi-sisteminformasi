<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PegawaiTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            // Contoh data untuk baris pertama
            [
                "'199405242024061001", // NIP (dengan prefix ' untuk mencegah Excel mengkonversi ke angka)
                'Dr. John Doe, S.T., M.T.', // Nama Lengkap
                'john.doe@unmul.ac.id', // Email
                'Dosen', // Jenis Pegawai
                'Dosen PNS', // Status Kepegawaian
                'Dr.', // Gelar Depan
                'S.T., M.T.', // Gelar Belakang
                'Samarinda', // Tempat Lahir
                '1994-05-24', // Tanggal Lahir
                'Laki-Laki', // Jenis Kelamin
                "'081234567890", // Nomor Handphone (dengan prefix ' untuk mencegah Excel mengkonversi ke angka)
                '1', // Pangkat Terakhir ID (ID dari tabel pangkat)
                '1', // Jabatan Terakhir ID (ID dari tabel jabatan)
                '1', // Unit Kerja ID (ID dari tabel unit_kerja)
                'Doktor (S3) / Sederajat', // Pendidikan Terakhir
                'Universitas Mulawarman', // Nama Universitas/Sekolah
                'Teknik Informatika', // Nama Program Studi/Jurusan
                '2020-01-01', // TMT CPNS
                '2021-01-01', // TMT PNS
                '2022-01-01', // TMT Pangkat
                '2023-01-01', // TMT Jabatan
                "'1234567890123456", // Nomor Kartu Pegawai (dengan prefix ' untuk mencegah Excel mengkonversi ke angka)
                "'1234567890123456", // NUPTK (dengan prefix ' untuk mencegah Excel mengkonversi ke angka)
                'Pemrograman Web, Basis Data', // Mata Kuliah Diampu
                'Teknik Informatika', // Ranting Ilmu Kepakaran
                'https://sinta.kemdikbud.go.id/authors/profile/123456', // URL Profil SINTA
                'Sangat Baik', // Predikat Kinerja Tahun Pertama
                'Sangat Baik', // Predikat Kinerja Tahun Kedua
                '90.5', // Nilai Konversi
            ],
            // Contoh data untuk baris kedua
            [
                '199405242024061002', // NIP
                'Dra. Jane Smith, M.Pd.', // Nama Lengkap
                'jane.smith@unmul.ac.id', // Email
                'Tenaga Kependidikan', // Jenis Pegawai
                'Tenaga Kependidikan PNS', // Status Kepegawaian
                'Dra.', // Gelar Depan
                'M.Pd.', // Gelar Belakang
                'Balikpapan', // Tempat Lahir
                '1995-03-15', // Tanggal Lahir
                'Perempuan', // Jenis Kelamin
                '081234567891', // Nomor Handphone
                '2', // Pangkat Terakhir ID (ID dari tabel pangkat)
                '2', // Jabatan Terakhir ID (ID dari tabel jabatan)
                '2', // Unit Kerja ID (ID dari tabel unit_kerja)
                'Magister (S2) / Sederajat', // Pendidikan Terakhir
                'Universitas Mulawarman', // Nama Universitas/Sekolah
                'Manajemen', // Nama Program Studi/Jurusan
                '2019-01-01', // TMT CPNS
                '2020-01-01', // TMT PNS
                '2021-01-01', // TMT Pangkat
                '2022-01-01', // TMT Jabatan
                '1234567890123457', // Nomor Kartu Pegawai
                '', // NUPTK (kosong untuk tenaga kependidikan)
                '', // Mata Kuliah Diampu (kosong untuk tenaga kependidikan)
                '', // Ranting Ilmu Kepakaran (kosong untuk tenaga kependidikan)
                '', // URL Profil SINTA (kosong untuk tenaga kependidikan)
                'Baik', // Predikat Kinerja Tahun Pertama
                'Baik', // Predikat Kinerja Tahun Kedua
                '75.0', // Nilai Konversi
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIP*',
            'Nama Lengkap*',
            'Email*',
            'Jenis Pegawai*',
            'Status Kepegawaian*',
            'Gelar Depan',
            'Gelar Belakang',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Nomor Handphone',
            'Pangkat Terakhir ID',
            'Jabatan Terakhir ID',
            'Unit Kerja ID',
            'Pendidikan Terakhir',
            'Nama Universitas/Sekolah',
            'Nama Program Studi/Jurusan',
            'TMT CPNS',
            'TMT PNS',
            'TMT Pangkat',
            'TMT Jabatan',
            'Nomor Kartu Pegawai',
            'NUPTK',
            'Mata Kuliah Diampu',
            'Ranting Ilmu Kepakaran',
            'URL Profil SINTA',
            'Predikat Kinerja Tahun Pertama',
            'Predikat Kinerja Tahun Kedua',
            'Nilai Konversi',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Set format untuk kolom numerik sebagai text
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@'); // NIP
        $sheet->getStyle('K:K')->getNumberFormat()->setFormatCode('@'); // Nomor Handphone
        $sheet->getStyle('L:L')->getNumberFormat()->setFormatCode('@'); // Pangkat Terakhir ID
        $sheet->getStyle('M:M')->getNumberFormat()->setFormatCode('@'); // Jabatan Terakhir ID
        $sheet->getStyle('N:N')->getNumberFormat()->setFormatCode('@'); // Unit Kerja ID
        $sheet->getStyle('V:V')->getNumberFormat()->setFormatCode('@'); // Nomor Kartu Pegawai
        $sheet->getStyle('W:W')->getNumberFormat()->setFormatCode('@'); // NUPTK

        return [
            // Style the first row as bold text with background color
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ]
            ],
            // Style the example data rows
            2 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E6E6']
                ]
            ],
            3 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // NIP
            'B' => 30, // Nama Lengkap
            'C' => 30, // Email
            'D' => 20, // Jenis Pegawai
            'E' => 25, // Status Kepegawaian
            'F' => 15, // Gelar Depan
            'G' => 15, // Gelar Belakang
            'H' => 20, // Tempat Lahir
            'I' => 15, // Tanggal Lahir
            'J' => 15, // Jenis Kelamin
            'K' => 20, // Nomor Handphone
            'L' => 15, // Pangkat Terakhir ID
            'M' => 15, // Jabatan Terakhir ID
            'N' => 15, // Unit Kerja ID
            'O' => 30, // Pendidikan Terakhir
            'P' => 30, // Nama Universitas/Sekolah
            'Q' => 30, // Nama Program Studi/Jurusan
            'R' => 15, // TMT CPNS
            'S' => 15, // TMT PNS
            'T' => 15, // TMT Pangkat
            'U' => 15, // TMT Jabatan
            'V' => 20, // Nomor Kartu Pegawai
            'W' => 20, // NUPTK
            'X' => 30, // Mata Kuliah Diampu
            'Y' => 30, // Ranting Ilmu Kepakaran
            'Z' => 30, // URL Profil SINTA
            'AA' => 30, // Predikat Kinerja Tahun Pertama
            'AB' => 30, // Predikat Kinerja Tahun Kedua
            'AC' => 15, // Nilai Konversi
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Template Import Pegawai';
    }
}

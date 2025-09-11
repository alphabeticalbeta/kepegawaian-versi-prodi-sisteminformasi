<?php

namespace App\Exports;

use App\Models\KepegawaianUniversitas\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PegawaiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja']);

        // Apply filters if provided
        if (isset($this->filters['jenis_pegawai']) && $this->filters['jenis_pegawai']) {
            $query->where('jenis_pegawai', $this->filters['jenis_pegawai']);
        }

        if (isset($this->filters['status_kepegawaian']) && $this->filters['status_kepegawaian']) {
            $query->where('status_kepegawaian', $this->filters['status_kepegawaian']);
        }

        if (isset($this->filters['unit_kerja']) && $this->filters['unit_kerja']) {
            $query->whereHas('unitKerja.subUnitKerja.unitKerja', function($q) {
                $q->where('nama', $this->filters['unit_kerja']);
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIP',
            'Nama Lengkap',
            'Email',
            'Jenis Pegawai',
            'Status Kepegawaian',
            'Gelar Depan',
            'Gelar Belakang',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Nomor Handphone',
            'Pangkat Terakhir',
            'Jabatan Terakhir',
            'Unit Kerja',
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
        ];
    }

    /**
     * @param mixed $pegawai
     * @return array
     */
    public function map($pegawai): array
    {
        return [
            $this->addPrefixIfNumeric($pegawai->nip),
            $pegawai->nama_lengkap,
            $pegawai->email,
            $pegawai->jenis_pegawai,
            $pegawai->status_kepegawaian,
            $pegawai->gelar_depan,
            $pegawai->gelar_belakang,
            $pegawai->tempat_lahir,
            $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '',
            $pegawai->jenis_kelamin,
            $this->addPrefixIfNumeric($pegawai->nomor_handphone),
            $pegawai->pangkat?->pangkat ?? '',
            $pegawai->jabatan?->jabatan ?? '',
            $pegawai->unitKerja?->subUnitKerja?->unitKerja?->nama ?? '',
            $pegawai->pendidikan_terakhir,
            $pegawai->nama_universitas_sekolah,
            $pegawai->nama_prodi_jurusan,
            $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '',
            $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '',
            $pegawai->tmt_pangkat ? $pegawai->tmt_pangkat->format('Y-m-d') : '',
            $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('Y-m-d') : '',
            $this->addPrefixIfNumeric($pegawai->nomor_kartu_pegawai),
            $this->addPrefixIfNumeric($pegawai->nuptk),
            $pegawai->mata_kuliah_diampu,
            $pegawai->ranting_ilmu_kepakaran,
            $pegawai->url_profil_sinta,
            $pegawai->predikat_kinerja_tahun_pertama,
            $pegawai->predikat_kinerja_tahun_kedua,
        ];
    }

    /**
     * Add single quote prefix to numeric values to prevent Excel from converting to scientific notation
     */
    private function addPrefixIfNumeric($value)
    {
        if (empty($value)) {
            return '';
        }

        $stringValue = (string) $value;
        
        // Remove any existing single quote prefix first
        $stringValue = ltrim($stringValue, "'");
        
        // Add single quote prefix for all numeric values
        if (is_numeric($stringValue)) {
            return "'" . $stringValue;
        }
        
        return $stringValue;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Set number format to text for columns that need to preserve leading zeros
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@'); // NIP
        $sheet->getStyle('K:K')->getNumberFormat()->setFormatCode('@'); // Nomor Handphone
        $sheet->getStyle('V:V')->getNumberFormat()->setFormatCode('@'); // Nomor Kartu Pegawai
        $sheet->getStyle('W:W')->getNumberFormat()->setFormatCode('@'); // NUPTK

        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
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
            'L' => 30, // Pangkat Terakhir
            'M' => 30, // Jabatan Terakhir
            'N' => 30, // Unit Kerja
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
        ];
    }
}

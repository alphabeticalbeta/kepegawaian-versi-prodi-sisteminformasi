<?php

namespace App\Helpers;

class UsulanHelper
{
    /**
     * Format jenis usulan dari kebab-case ke format yang user-friendly
     *
     * @param string $jenisUsulan
     * @return string
     */
    public static function formatJenisUsulan($jenisUsulan)
    {
        $jenisUsulanLabels = [
            'jabatan-dosen-regular' => 'Jabatan Dosen Regular',
            'jabatan-dosen-pengangkatan' => 'Jabatan Dosen Pengangkatan',
            'jabatan-tenaga-kependidikan' => 'Jabatan Tenaga Kependidikan',
            'pangkat-dosen' => 'Pangkat Dosen',
            'pangkat-tenaga-kependidikan' => 'Pangkat Tenaga Kependidikan',
            'guru-besar' => 'Guru Besar',
            'usulan-nuptk' => 'Usulan NUPTK',
            'usulan-laporan-lkd' => 'Usulan Laporan LKD',
            'usulan-presensi' => 'Usulan Presensi',
            'usulan-id-sinta-sister' => 'Usulan ID SINTA/SISTER',
            'usulan-satyalancana' => 'Usulan Satyalancana',
            'usulan-tugas-belajar' => 'Usulan Tugas Belajar',
            'usulan-pengaktifan-kembali' => 'Usulan Pengaktifan Kembali',
            'usulan-penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
            'usulan-ujian-dinas-ijazah' => 'Usulan Ujian Dinas/Ijazah',
            'usulan-laporan-serdos' => 'Usulan Laporan SERDOS',
            'usulan-pensiun' => 'Usulan Pensiun',
            'usulan-kepangkatan' => 'Usulan Kepangkatan',
            'usulan-pencantuman-gelar' => 'Usulan Pencantuman Gelar'
        ];
        
        return $jenisUsulanLabels[$jenisUsulan] ?? ucwords(str_replace('-', ' ', $jenisUsulan));
    }

    /**
     * Get badge color class berdasarkan jenis usulan
     *
     * @param string $jenisUsulan
     * @return string
     */
    public static function getJenisUsulanBadgeClass($jenisUsulan)
    {
        $badgeClasses = [
            'jabatan-dosen-regular' => 'bg-blue-100 text-blue-800',
            'jabatan-dosen-pengangkatan' => 'bg-purple-100 text-purple-800',
            'jabatan-tenaga-kependidikan' => 'bg-orange-100 text-orange-800',
            'pangkat-dosen' => 'bg-green-100 text-green-800',
            'pangkat-tenaga-kependidikan' => 'bg-teal-100 text-teal-800',
            'guru-besar' => 'bg-indigo-100 text-indigo-800',
            'usulan-nuptk' => 'bg-green-100 text-green-800',
            'usulan-laporan-lkd' => 'bg-blue-100 text-blue-800',
            'usulan-presensi' => 'bg-pink-100 text-pink-800',
            'usulan-id-sinta-sister' => 'bg-teal-100 text-teal-800',
            'usulan-satyalancana' => 'bg-orange-100 text-orange-800',
            'usulan-tugas-belajar' => 'bg-cyan-100 text-cyan-800',
            'usulan-pengaktifan-kembali' => 'bg-emerald-100 text-emerald-800',
            'usulan-penyesuaian-masa-kerja' => 'bg-amber-100 text-amber-800',
            'usulan-ujian-dinas-ijazah' => 'bg-lime-100 text-lime-800',
            'usulan-laporan-serdos' => 'bg-rose-100 text-rose-800',
            'usulan-pensiun' => 'bg-slate-100 text-slate-800',
            'usulan-kepangkatan' => 'bg-violet-100 text-violet-800',
            'usulan-pencantuman-gelar' => 'bg-fuchsia-100 text-fuchsia-800'
        ];
        
        return $badgeClasses[$jenisUsulan] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get icon untuk jenis usulan
     *
     * @param string $jenisUsulan
     * @return string
     */
    public static function getJenisUsulanIcon($jenisUsulan)
    {
        $icons = [
            'jabatan-dosen-regular' => 'trending-up',
            'jabatan-dosen-pengangkatan' => 'user-plus',
            'jabatan-tenaga-kependidikan' => 'users',
            'pangkat-dosen' => 'award',
            'pangkat-tenaga-kependidikan' => 'medal',
            'guru-besar' => 'star',
            'usulan-nuptk' => 'id-card',
            'usulan-laporan-lkd' => 'file-text',
            'usulan-presensi' => 'clock',
            'usulan-id-sinta-sister' => 'database',
            'usulan-satyalancana' => 'trophy',
            'usulan-tugas-belajar' => 'graduation-cap',
            'usulan-pengaktifan-kembali' => 'refresh-cw',
            'usulan-penyesuaian-masa-kerja' => 'calendar',
            'usulan-ujian-dinas-ijazah' => 'book-open',
            'usulan-laporan-serdos' => 'file-check',
            'usulan-pensiun' => 'home',
            'usulan-kepangkatan' => 'chevron-up',
            'usulan-pencantuman-gelar' => 'type'
        ];
        
        return $icons[$jenisUsulan] ?? 'file-text';
    }
}

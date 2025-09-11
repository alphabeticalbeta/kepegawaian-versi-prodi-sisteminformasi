<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AplikasiKepegawaian;

class AplikasiKepegawaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aplikasiData = [
            [
                'nama_aplikasi' => 'SIDAK',
                'sumber' => 'Universitas Mulawarman',
                'keterangan' => 'Sistem Informasi Database Administrasi Kepegawaian',
                'link' => 'https://sidak.unmul.ac.id',
                'status' => 'aktif'
            ],
            [
                'nama_aplikasi' => 'Simkinerja',
                'sumber' => 'Universitas Mulawarman',
                'keterangan' => 'Sistem Informasi Kinerja (Remunerasi)',
                'link' => 'https://simkinerja.unmul.ac.id',
                'status' => 'aktif'
            ],
            [
                'nama_aplikasi' => 'SIMPEG',
                'sumber' => 'Universitas Mulawarman',
                'keterangan' => 'Sistem Informasi Manajemen Pegawai',
                'link' => 'https://simpeg.unmul.ac.id',
                'status' => 'aktif'
            ]
        ];

        foreach ($aplikasiData as $data) {
            AplikasiKepegawaian::create($data);
        }
    }
}

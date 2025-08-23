<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KepegawaianUniversitas\Usulan;

class KeputusanSenatController extends Controller
{
    public function index()
    {
        // Get usulans that have been decided by Tim Senat
        $usulans = Usulan::with([
            'pegawai:id,nama_lengkap,nip,unit_kerja_id',
            'pegawai.unitKerja:id,nama',
            'jabatanLama:id,jabatan',
            'jabatanTujuan:id,jabatan',
            'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai'
        ])
        ->whereIn('status_usulan', ['Disetujui', 'Ditolak'])
        ->latest()
        ->get();

        return view('backend.layouts.views.tim-senat.keputusan-senat.index', [
            'title' => 'Keputusan Senat',
            'description' => 'Kelola Keputusan Senat',
            'usulans' => $usulans
        ]);
    }
}

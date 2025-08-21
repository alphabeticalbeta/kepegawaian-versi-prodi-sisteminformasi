<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKTunjanganSertifikasiController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-tunjangan-sertifikasi.index', [
            'title' => 'SK Tunjangan Sertifikasi',
            'description' => 'Kelola Surat Keputusan Tunjangan Sertifikasi'
        ]);
    }
}

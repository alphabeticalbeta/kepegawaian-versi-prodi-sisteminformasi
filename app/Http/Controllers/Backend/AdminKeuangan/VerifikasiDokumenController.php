<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifikasiDokumenController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.verifikasi-dokumen.index', [
            'title' => 'Verifikasi Dokumen',
            'description' => 'Kelola Verifikasi Dokumen'
        ]);
    }
}

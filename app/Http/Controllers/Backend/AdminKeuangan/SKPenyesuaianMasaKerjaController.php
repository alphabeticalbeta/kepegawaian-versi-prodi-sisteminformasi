<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPenyesuaianMasaKerjaController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-penyesuaian-masa-kerja.index', [
            'title' => 'SK Penyesuaian Masa Kerja',
            'description' => 'Kelola Surat Keputusan Penyesuaian Masa Kerja'
        ]);
    }
}

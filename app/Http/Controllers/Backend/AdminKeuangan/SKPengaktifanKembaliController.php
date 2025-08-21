<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPengaktifanKembaliController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pengaktifan-kembali.index', [
            'title' => 'SK Pengaktifan Kembali',
            'description' => 'Kelola Surat Keputusan Pengaktifan Kembali'
        ]);
    }
}

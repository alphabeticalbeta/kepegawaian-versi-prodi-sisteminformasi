<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.laporan-keuangan.index', [
            'title' => 'Laporan Keuangan',
            'description' => 'Kelola Laporan Keuangan'
        ]);
    }
}

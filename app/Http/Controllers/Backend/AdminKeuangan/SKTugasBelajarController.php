<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKTugasBelajarController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-tugas-belajar.index', [
            'title' => 'SK Tugas Belajar',
            'description' => 'Kelola Surat Keputusan Tugas Belajar'
        ]);
    }
}

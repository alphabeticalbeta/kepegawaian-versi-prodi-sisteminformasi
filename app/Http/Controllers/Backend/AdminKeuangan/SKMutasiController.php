<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKMutasiController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-mutasi.index', [
            'title' => 'SK Mutasi',
            'description' => 'Kelola Surat Keputusan Mutasi'
        ]);
    }
}

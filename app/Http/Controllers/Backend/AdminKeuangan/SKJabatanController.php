<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKJabatanController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-jabatan.index', [
            'title' => 'SK Jabatan',
            'description' => 'Kelola Surat Keputusan Jabatan'
        ]);
    }
}

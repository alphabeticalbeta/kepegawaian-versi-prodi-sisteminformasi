<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPensiunController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pensiun.index', [
            'title' => 'SK Pensiun',
            'description' => 'Kelola Surat Keputusan Pensiun'
        ]);
    }
}

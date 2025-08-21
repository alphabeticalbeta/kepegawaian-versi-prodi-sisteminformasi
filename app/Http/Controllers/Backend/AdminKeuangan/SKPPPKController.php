<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPPPKController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pppk.index', [
            'title' => 'SK PPPK',
            'description' => 'Kelola Surat Keputusan PPPK'
        ]);
    }
}

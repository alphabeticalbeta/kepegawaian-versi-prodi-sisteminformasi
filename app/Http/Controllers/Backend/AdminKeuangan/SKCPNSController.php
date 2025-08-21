<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKCPNSController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-cpns.index', [
            'title' => 'SK CPNS',
            'description' => 'Kelola Surat Keputusan CPNS'
        ]);
    }
}

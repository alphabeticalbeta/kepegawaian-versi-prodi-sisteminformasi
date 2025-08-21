<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPNSController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pns.index', [
            'title' => 'SK PNS',
            'description' => 'Kelola Surat Keputusan PNS'
        ]);
    }
}

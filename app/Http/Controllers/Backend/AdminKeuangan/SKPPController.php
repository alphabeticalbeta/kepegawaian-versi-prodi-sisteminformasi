<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPPController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.skpp.index', [
            'title' => 'SKPP',
            'description' => 'Kelola Surat Keputusan Pemberhentian'
        ]);
    }
}

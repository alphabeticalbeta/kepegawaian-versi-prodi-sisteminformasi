<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPemberhentianSementaraController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pemberhentian-sementara.index', [
            'title' => 'SK Pemberhentian Sementara',
            'description' => 'Kelola Surat Keputusan Pemberhentian Sementara'
        ]);
    }
}

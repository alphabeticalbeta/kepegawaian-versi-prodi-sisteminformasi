<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPemberhentianMeninggalController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pemberhentian-meninggal.index', [
            'title' => 'SK Pemberhentian Meninggal',
            'description' => 'Kelola Surat Keputusan Pemberhentian Meninggal'
        ]);
    }
}

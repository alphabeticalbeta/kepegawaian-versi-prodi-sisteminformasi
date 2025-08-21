<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKPangkatController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-pangkat.index', [
            'title' => 'SK Pangkat',
            'description' => 'Kelola Surat Keputusan Pangkat'
        ]);
    }
}

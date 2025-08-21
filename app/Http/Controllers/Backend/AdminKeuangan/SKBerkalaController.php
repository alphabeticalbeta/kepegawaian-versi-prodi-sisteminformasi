<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKBerkalaController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.sk-berkala.index', [
            'title' => 'SK Berkala',
            'description' => 'Kelola Surat Keputusan Berkala'
        ]);
    }
}

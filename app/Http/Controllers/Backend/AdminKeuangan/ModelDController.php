<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModelDController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.admin-keuangan.model-d.index', [
            'title' => 'Model D',
            'description' => 'Kelola Model D'
        ]);
    }
}

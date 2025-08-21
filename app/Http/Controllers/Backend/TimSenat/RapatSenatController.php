<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RapatSenatController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.tim-senat.rapat-senat.index', [
            'title' => 'Rapat Senat',
            'description' => 'Kelola Rapat Senat'
        ]);
    }
}

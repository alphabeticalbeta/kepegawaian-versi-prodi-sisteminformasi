<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeputusanSenatController extends Controller
{
    public function index()
    {
        return view('backend.layouts.views.tim-senat.keputusan-senat.index', [
            'title' => 'Keputusan Senat',
            'description' => 'Kelola Keputusan Senat'
        ]);
    }
}

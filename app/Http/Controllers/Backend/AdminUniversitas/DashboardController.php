<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin universitas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.layouts.views.admin-universitas.dashboard', [
            'user' => Auth::user()
        ]);
    }
}

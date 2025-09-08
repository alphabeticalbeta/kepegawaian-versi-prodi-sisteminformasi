<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Display visi and misi page
     *
     * @return View
     */
    public function visiMisi(): View
    {
        return view('frontend.layouts.profil.visi-misi');
    }

    /**
     * Display struktur organisasi page
     *
     * @return View
     */
    public function strukturOrganisasi(): View
    {
        return view('frontend.layouts.profil.struktur-organisasi');
    }
}

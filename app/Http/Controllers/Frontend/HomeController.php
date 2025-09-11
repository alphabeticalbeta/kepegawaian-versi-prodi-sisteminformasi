<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 4 berita terbaru
        $berita = Informasi::berita()
            ->published()
            ->notExpired()
            ->byPriority()
            ->latest('tanggal_publish')
            ->limit(4)
            ->get();

        // Ambil 4 pengumuman terbaru
        $pengumuman = Informasi::pengumuman()
            ->published()
            ->notExpired()
            ->byPriority()
            ->latest('tanggal_publish')
            ->limit(4)
            ->get();

        // Ambil pengumuman featured untuk carousel
        $pengumumanFeatured = Informasi::pengumuman()
            ->published()
            ->notExpired()
            ->featured()
            ->latest('tanggal_publish')
            ->limit(5)
            ->get();

        // Ambil data struktur organisasi (jika ada model/table khusus)
        // Untuk sementara kita akan menggunakan data dummy atau dari config
        $strukturOrganisasi = [
            'title' => 'Struktur Organisasi Universitas Mulawarman',
            'image_url' => asset('images/struktur-organisasi.jpg'), // Ganti dengan path yang sesuai
            'description' => 'Struktur organisasi Universitas Mulawarman yang menunjukkan hierarki dan hubungan antar unit kerja.'
        ];

        return view('frontend.index', compact('berita', 'pengumuman', 'pengumumanFeatured', 'strukturOrganisasi'));
    }
}

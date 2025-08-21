<?php

// Script untuk membuat semua controller dan view usulan pegawai secara otomatis

$usulanTypes = [
    'penyesuaian-masa-kerja' => [
        'name' => 'Penyesuaian Masa Kerja',
        'icon' => 'clock',
        'description' => 'Pengajuan Penyesuaian Masa Kerja'
    ],
    'ujian-dinas-ijazah' => [
        'name' => 'Ujian Dinas & Ijazah',
        'icon' => 'book-marked',
        'description' => 'Pengajuan Ujian Dinas & Ijazah'
    ],
    'laporan-serdos' => [
        'name' => 'Laporan Serdos',
        'icon' => 'file-check-2',
        'description' => 'Pengajuan Laporan Serdos'
    ],
    'pensiun' => [
        'name' => 'Pensiun',
        'icon' => 'user-minus',
        'description' => 'Pengajuan Pensiun'
    ],
    'kepangkatan' => [
        'name' => 'Kepangkatan',
        'icon' => 'trending-up',
        'description' => 'Pengajuan Kepangkatan'
    ],
    'pencantuman-gelar' => [
        'name' => 'Pencantuman Gelar',
        'icon' => 'graduation-cap',
        'description' => 'Pengajuan Pencantuman Gelar'
    ],
    'id-sinta-sister' => [
        'name' => 'ID SINTA ke SISTER',
        'icon' => 'link',
        'description' => 'Pengajuan ID SINTA ke SISTER'
    ],
    'satyalancana' => [
        'name' => 'Satyalancana',
        'icon' => 'medal',
        'description' => 'Pengajuan Satyalancana'
    ],
    'tugas-belajar' => [
        'name' => 'Tugas Belajar',
        'icon' => 'book-open',
        'description' => 'Pengajuan Tugas Belajar'
    ],
    'pengaktifan-kembali' => [
        'name' => 'Pengaktifan Kembali',
        'icon' => 'user-plus',
        'description' => 'Pengajuan Pengaktifan Kembali'
    ]
];

echo "=== CREATING ALL USULAN PAGES ===\n\n";

foreach ($usulanTypes as $key => $type) {
    $controllerName = 'Usulan' . str_replace('-', '', ucwords($key, '-')) . 'Controller';
    $viewPath = $key;

    echo "Creating: {$type['name']}\n";

    // Create controller
    $controllerContent = generateControllerContent($controllerName, $key, $type);
    $controllerPath = "app/Http/Controllers/Backend/PegawaiUnmul/{$controllerName}.php";

    if (!file_exists($controllerPath)) {
        file_put_contents($controllerPath, $controllerContent);
        echo "✅ Controller created: {$controllerPath}\n";
    } else {
        echo "⚠️  Controller already exists: {$controllerPath}\n";
    }

    // Create view directory
    $viewDir = "resources/views/backend/layouts/views/pegawai-unmul/usulan-{$viewPath}";
    if (!is_dir($viewDir)) {
        mkdir($viewDir, 0755, true);
        echo "✅ View directory created: {$viewDir}\n";
    }

    // Create view
    $viewContent = generateViewContent($key, $type);
    $viewPath = "{$viewDir}/index.blade.php";

    if (!file_exists($viewPath)) {
        file_put_contents($viewPath, $viewContent);
        echo "✅ View created: {$viewPath}\n";
    } else {
        echo "⚠️  View already exists: {$viewPath}\n";
    }

    echo "\n";
}

echo "=== GENERATING ROUTES ===\n\n";

foreach ($usulanTypes as $key => $type) {
    $controllerName = 'Usulan' . str_replace('-', '', ucwords($key, '-')) . 'Controller';
    $routePrefix = 'pegawai-unmul.usulan-' . str_replace('-', '-', $key);
    echo "Route::resource('usulan-{$key}', App\\Http\\Controllers\\Backend\\PegawaiUnmul\\{$controllerName}::class)->names('{$routePrefix}');\n";
}

echo "\n=== GENERATING SIDEBAR UPDATES ===\n\n";

foreach ($usulanTypes as $key => $type) {
    $routePrefix = 'pegawai-unmul.usulan-' . str_replace('-', '-', $key);
    echo "['route' => route('{$routePrefix}.index'), 'icon' => '{$type['icon']}', 'label' => 'Usulan {$type['name']}', 'pattern' => '{$routePrefix}.*'],\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total usulan types processed: " . count($usulanTypes) . "\n";
echo "Controllers created: " . count($usulanTypes) . "\n";
echo "Views created: " . count($usulanTypes) . "\n\n";

echo "=== NEXT STEPS ===\n";
echo "1. Add the generated routes to routes/backend.php\n";
echo "2. Update sidebar-pegawai-unmul.blade.php with the new menu items\n";
echo "3. Test all pages\n\n";

function generateControllerContent($controllerName, $key, $type) {
    $usulanType = 'usulan-' . str_replace('-', '-', $key);
    $routePrefix = 'pegawai-unmul.usulan-' . str_replace('-', '-', $key);

    return "<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class {$controllerName} extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \$pegawai = Auth::user();

        \$usulans = \$pegawai->usulans()
                          ->where('jenis_usulan', '{$usulanType}')
                          ->with(['periodeUsulan'])
                          ->latest()
                          ->paginate(10);

        return view('backend.layouts.views.pegawai-unmul.usulan-{$key}.index', compact('usulans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Redirect ke halaman yang sesuai atau tampilkan form
        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                         ->with('info', 'Fitur Usulan {$type['name']} akan segera tersedia.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request)
    {
        // Implementation akan ditambahkan nanti
        return redirect()->route('{$routePrefix}.index')
                         ->with('success', 'Usulan {$type['name']} berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan \$usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if (\$usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-{$key}.show', compact('usulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan \$usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if (\$usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-{$key}.edit', compact('usulan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, Usulan \$usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if (\$usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('{$routePrefix}.index')
                         ->with('success', 'Usulan {$type['name']} berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan \$usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if (\$usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('{$routePrefix}.index')
                         ->with('success', 'Usulan {$type['name']} berhasil dihapus.');
    }
}";
}

function generateViewContent($key, $type) {
    $routePrefix = 'pegawai-unmul.usulan-' . str_replace('-', '-', $key);

    return "@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Usulan {$type['name']} Saya')

@section('content')
<div class=\"container mx-auto px-4 sm:px-6 lg:px-8 py-8\">
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class=\"bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md\" role=\"alert\">
            <strong class=\"font-bold\">Sukses!</strong>
            <span class=\"block sm:inline\">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class=\"bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md\" role=\"alert\">
            <strong class=\"font-bold\">Gagal!</strong>
            <span class=\"block sm:inline\">{{ session('error') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div class=\"bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded-lg relative mb-6 shadow-md\" role=\"alert\">
            <strong class=\"font-bold\">Perhatian!</strong>
            <span class=\"block sm:inline\">{{ session('warning') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class=\"bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded-lg relative mb-6 shadow-md\" role=\"alert\">
            <strong class=\"font-bold\">Informasi!</strong>
            <span class=\"block sm:inline\">{{ session('info') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class=\"mb-8\">
        <div class=\"flex items-center justify-between\">
            <div>
                <h1 class=\"text-3xl font-bold text-gray-900\">
                    Usulan {$type['name']} Saya
                </h1>
                <p class=\"mt-2 text-gray-600\">
                    Pantau status dan riwayat usulan {$type['name']} yang telah Anda ajukan.
                </p>
            </div>
            <a href=\"{{ route('{$routePrefix}.create') }}\"
               class=\"inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150\">
                <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                </svg>
                Buat Usulan Baru
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div class=\"bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden\">
        <div class=\"px-6 py-5 border-b border-gray-200 bg-gray-50\">
            <h3 class=\"text-lg font-semibold text-gray-800\">
                Daftar Usulan {$type['name']}
            </h3>
            <p class=\"text-sm text-gray-500 mt-1\">
                Berikut adalah semua usulan {$type['name']} yang pernah Anda buat.
            </p>
        </div>

        <div class=\"overflow-x-auto\">
            @if(\$usulans->count() > 0)
                <table class=\"w-full text-sm text-left text-gray-600\">
                    <thead class=\"text-xs text-gray-700 uppercase bg-gray-100\">
                        <tr>
                            <th scope=\"col\" class=\"px-6 py-4\">Periode</th>
                            <th scope=\"col\" class=\"px-6 py-4\">Tanggal Pengajuan</th>
                            <th scope=\"col\" class=\"px-6 py-4 text-center\">Status</th>
                            <th scope=\"col\" class=\"px-6 py-4 text-center\">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\$usulans as \$usulan)
                            <tr class=\"bg-white border-b hover:bg-gray-50 transition-colors duration-200\">
                                <td class=\"px-6 py-4\">{{ \$usulan->periodeUsulan->nama_periode ?? 'N/A' }}</td>
                                <td class=\"px-6 py-4\">{{ \$usulan->created_at->isoFormat('D MMMM YYYY') }}</td>
                                <td class=\"px-6 py-4 text-center\">
                                    @php
                                        \$statusClass = match(\$usulan->status_usulan) {
                                            'Draft' => 'bg-gray-100 text-gray-800',
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
                                            'Dikembalikan' => 'bg-red-100 text-red-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class=\"px-2 py-1 text-xs font-medium rounded-full {{ \$statusClass }}\">
                                        {{ \$usulan->status_usulan }}
                                    </span>
                                </td>
                                <td class=\"px-6 py-4 text-center\">
                                    <div class=\"flex items-center justify-center space-x-2\">
                                        <a href=\"{{ route('{$routePrefix}.show', \$usulan->id) }}\"
                                           class=\"text-indigo-600 hover:text-indigo-900 font-medium text-sm\">
                                            Detail
                                        </a>
                                        @if(\$usulan->status_usulan === 'Draft')
                                            <a href=\"{{ route('{$routePrefix}.edit', \$usulan->id) }}\"
                                               class=\"text-blue-600 hover:text-blue-900 font-medium text-sm\">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class=\"px-6 py-4 border-t border-gray-200\">
                    {{ \$usulans->links() }}
                </div>
            @else
                <div class=\"px-6 py-12 text-center\">
                    <svg class=\"mx-auto h-12 w-12 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\" />
                    </svg>
                    <h3 class=\"mt-2 text-sm font-medium text-gray-900\">Belum ada usulan</h3>
                    <p class=\"mt-1 text-sm text-gray-500\">
                        Anda belum pernah membuat usulan {$type['name']}.
                    </p>
                    <div class=\"mt-6\">
                        <a href=\"{{ route('{$routePrefix}.create') }}\"
                           class=\"inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                            </svg>
                            Buat Usulan Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection";
}

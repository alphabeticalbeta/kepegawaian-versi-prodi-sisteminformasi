<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Pegawai;
use Illuminate\Http\Request;
// Gunakan model Role dari Spatie
use Spatie\Permission\Models\Role;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class RolePegawaiController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    public function index(Request $request)
    {
        // Query builder dengan eager loading roles
        $query = Pegawai::with('roles')->orderBy('nama_lengkap');

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan jenis pegawai
        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        $pegawais = $query->paginate(15)->withQueryString();
        return view('backend.layouts.views.admin-univ-usulan.role-pegawai.master-rolepegawai', compact('pegawais'));
    }

    public function edit(Pegawai $pegawai)
    {
        // Ambil semua peran yang ada untuk ditampilkan di form dengan guard 'pegawai'
        $roles = Role::where('guard_name', 'pegawai')->orderBy('name')->get();
        return view('backend.layouts.views.admin-univ-usulan.role-pegawai.edit', compact('pegawai', 'roles'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'roles' => 'nullable|array' // Memvalidasi bahwa 'roles' adalah sebuah array
        ]);

        // syncRoles() untuk guard 'pegawai'
        // Menggunakan array role names dan guard eksplisit
        $roleNames = $request->input('roles', []);

        // Clear existing roles terlebih dahulu untuk guard pegawai
        $pegawai->roles()->detach();

        // Assign role baru dengan guard yang benar
        if (!empty($roleNames)) {
            foreach ($roleNames as $roleName) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)
                    ->where('guard_name', 'pegawai')
                    ->first();

                if ($role) {
                    $pegawai->assignRole($role);
                }
            }
        }

        return redirect()->route('backend.admin-univ-usulan.role-pegawai.index')
            ->with('success', 'Peran untuk ' . $pegawai->nama_lengkap . ' berhasil diperbarui.');
    }
}

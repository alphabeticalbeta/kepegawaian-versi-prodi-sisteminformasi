<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class PegawaiController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
        
        // Terapkan Gate: hanya yang lolos 'manage-pegawai' bisa akses
        $this->middleware('can:manage-pegawai');
    }

    /**
     * Menampilkan daftar akun pegawai untuk manajemen.
     */
    public function index()
    {
        $pegawais = Pegawai::orderBy('nama_lengkap')->paginate(15);
        return view('backend.layouts.views.kepegawaian-universitas.pegawai.index', compact('pegawais'));
    }

    /**
     * Menampilkan form untuk mengedit akun pegawai.
     */
    public function edit(Pegawai $pegawai)
    {
        return view('backend.layouts.views.kepegawaian-universitas.pegawai.edit', compact('pegawai'));
    }

    /**
     * Memperbarui data akun pegawai.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pegawais,email,' . $pegawai->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $pegawai->nama_lengkap = $request->nama_lengkap;
        $pegawai->email = $request->email;

        if ($request->filled('password')) {
            $pegawai->password = Hash::make($request->password);
        }

        $pegawai->save();

        return redirect()->route('backend.kepegawaian-universitas.pegawai.index')->with('success', 'Akun pegawai berhasil diperbarui.');
    }
}

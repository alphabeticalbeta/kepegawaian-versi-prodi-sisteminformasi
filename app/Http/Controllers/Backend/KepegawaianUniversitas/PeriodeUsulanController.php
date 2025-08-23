<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Http\Request;
use App\Rules\NoDateRangeOverlap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class PeriodeUsulanController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    /**
     * Menampilkan daftar semua resource.
     * (Metode ini tidak lagi digunakan karena daftar ditampilkan di PusatUsulanController)
     */
    public function index()
    {
        return redirect()->route('backend.kepegawaian-universitas.pusat-usulan.index');
    }

    /**
     * Menampilkan form untuk membuat resource baru.
     */
    public function create(Request $request)
    {
        $jenisUsulan = $request->query('jenis', 'jabatan');

        // Mapping dari parameter URL ke jenis usulan yang benar
        $jenisMapping = [
            'jabatan' => 'Usulan Jabatan',
            'nuptk' => 'Usulan NUPTK',
            'laporan-lkd' => 'Usulan Laporan LKD',
            'presensi' => 'Usulan Presensi',
            'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
            'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah',
            'laporan-serdos' => 'Usulan Laporan Serdos',
            'pensiun' => 'Usulan Pensiun',
            'kepangkatan' => 'Usulan Kepangkatan',
            'pencantuman-gelar' => 'Usulan Pencantuman Gelar',
            'id-sinta-sister' => 'Usulan ID SINTA ke SISTER',
            'satyalancana' => 'Usulan Satyalancana',
            'tugas-belajar' => 'Usulan Tugas Belajar',
            'pengaktifan-kembali' => 'Usulan Pengaktifan Kembali'
        ];

        $jenisUsulanOtomatis = $jenisMapping[$jenisUsulan] ?? 'Usulan Jabatan';

        // Tentukan view berdasarkan jenis usulan
        $viewMapping = [
            'usulan-jabatan-dosen' => 'backend.layouts.views.periode-usulan.form-jabatan-dosen',
            'usulan-jabatan-tendik' => 'backend.layouts.views.periode-usulan.form-jabatan-tendik',
        ];

        $view = $viewMapping[$jenisUsulan] ?? 'backend.layouts.views.periode-usulan.form';

        return view($view, [
            'jenis_usulan_otomatis' => $jenisUsulanOtomatis,
            'jenis_usulan_key' => $jenisUsulan,
            'nama_usulan' => $jenisUsulanOtomatis
        ]);
    }

    /**
     * Menyimpan resource yang baru dibuat.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'nama_periode'            => ['required', 'string', 'max:255'],
            'jenis_usulan'            => ['required', 'string', 'max:255'],
            'status_kepegawaian'      => ['required', 'array', 'min:1'],
            'status_kepegawaian.*'    => ['string', 'in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN'],
            'tanggal_mulai'           => [
                'required', 'date',
                new NoDateRangeOverlap(
                    table: 'periode_usulans',
                    startColumn: 'tanggal_mulai',
                    endColumn: 'tanggal_selesai',
                    filters: ['jenis_usulan' => $request->input('jenis_usulan')],
                    excludeId: null
                ),
            ],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_mulai_perbaikan' => ['nullable', 'date'],
            'tanggal_selesai_perbaikan' => ['nullable', 'date'],
            'status'                  => ['required', 'in:Buka,Tutup'],
            'senat_min_setuju'        => ['nullable', 'integer', 'min:0'],
        ];

        // Conditional validation untuk tanggal perbaikan
        if ($request->filled('tanggal_mulai_perbaikan')) {
            $validationRules['tanggal_mulai_perbaikan'][] = 'after_or_equal:tanggal_selesai';
        }

        if ($request->filled('tanggal_selesai_perbaikan')) {
            $validationRules['tanggal_selesai_perbaikan'][] = 'after_or_equal:tanggal_mulai_perbaikan';
        }

        $validated = $request->validate($validationRules);

        // Log untuk debugging
        \Log::info('Periode Usulan Store Request', [
            'request_data' => $request->all(),
            'validated_data' => $validated,
            'jenis_usulan' => $request->input('jenis_usulan')
        ]);

        // Hitung tahun_periode dari tanggal_mulai
        $validated['tahun_periode'] = Carbon::parse($validated['tanggal_mulai'])->year;

        // (Opsional) Jika ingin selalu buka saat buat:
        // $validated['status'] = 'Buka';

        // Defaultkan senat_min_setuju bila kosong
        $validated['senat_min_setuju'] = (int) ($validated['senat_min_setuju'] ?? 0);

        try {
            DB::transaction(function () use ($validated) {
                $periode = new PeriodeUsulan();
                $periode->fill($validated);   // boleh fill karena kita kontrol $validated
                $periode->save();
            });

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Usulan Jabatan' => 'jabatan',
                'usulan-jabatan-dosen' => 'jabatan',
                'usulan-jabatan-tendik' => 'jabatan',
                'Usulan NUPTK' => 'nuptk',
                'Usulan Laporan LKD' => 'laporan-lkd',
                'Usulan Presensi' => 'presensi',
                'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
                'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
                'Usulan Laporan Serdos' => 'laporan-serdos',
                'Usulan Pensiun' => 'pensiun',
                'Usulan Kepangkatan' => 'kepangkatan',
                'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
                'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
                'Usulan Satyalancana' => 'satyalancana',
                'Usulan Tugas Belajar' => 'tugas-belajar',
                'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$validated['jenis_usulan']] ?? 'jabatan';

            return redirect()->route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $validated['nama_periode'] . '" berhasil dibuat!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', '❌ Validasi gagal. Silakan periksa kembali data yang dimasukkan.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Gagal menyimpan periode usulan. Silakan coba lagi.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Menampilkan form untuk mengedit resource.
     */
    public function edit(PeriodeUsulan $periodeUsulan)
    {
        return view('backend.layouts.views.periode-usulan.form', [
            'periode' => $periodeUsulan,
            'nama_usulan' => $periodeUsulan->jenis_usulan
        ]);
    }

    /**
     * Memperbarui resource yang ada di storage.
     */
    public function update(Request $request, PeriodeUsulan $periode_usulan)
    {
        $validationRules = [
            'nama_periode'            => ['required', 'string', 'max:255'],
            'jenis_usulan'            => ['required', 'string', 'max:255'],
            'status_kepegawaian'      => ['required', 'array', 'min:1'],
            'status_kepegawaian.*'    => ['string', 'in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN'],
            'tanggal_mulai' => [
                'required',
                'date',
                new NoDateRangeOverlap(
                    table: 'periode_usulans',
                    startColumn: 'tanggal_mulai',
                    endColumn: 'tanggal_selesai',
                    filters: ['jenis_usulan' => $request->input('jenis_usulan')],
                    excludeId: $periode_usulan->id
                ),
            ],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_mulai_perbaikan' => ['nullable', 'date'],
            'tanggal_selesai_perbaikan' => ['nullable', 'date'],
            'status'                  => ['required', 'in:Buka,Tutup'],
            'senat_min_setuju' => ['nullable', 'integer', 'min:0'],
        ];

        // Conditional validation untuk tanggal perbaikan
        if ($request->filled('tanggal_mulai_perbaikan')) {
            $validationRules['tanggal_mulai_perbaikan'][] = 'after_or_equal:tanggal_selesai';
        }

        if ($request->filled('tanggal_selesai_perbaikan')) {
            $validationRules['tanggal_selesai_perbaikan'][] = 'after_or_equal:tanggal_mulai_perbaikan';
        }

        $request->validate($validationRules);

        try {
            DB::transaction(function () use ($request, $periode_usulan) {
                $periode_usulan->nama_periode      = $request->input('nama_periode');
                $periode_usulan->jenis_usulan      = $request->input('jenis_usulan');
                $periode_usulan->status_kepegawaian = $request->input('status_kepegawaian');
                $periode_usulan->tanggal_mulai     = $request->input('tanggal_mulai');
                $periode_usulan->tanggal_selesai   = $request->input('tanggal_selesai');
                $periode_usulan->tanggal_mulai_perbaikan = $request->input('tanggal_mulai_perbaikan');
                $periode_usulan->tanggal_selesai_perbaikan = $request->input('tanggal_selesai_perbaikan');
                $periode_usulan->status            = $request->input('status');
                $periode_usulan->senat_min_setuju  = (int) $request->input('senat_min_setuju', $periode_usulan->senat_min_setuju ?? 0);
                $periode_usulan->save();
            });

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Usulan Jabatan' => 'jabatan',
                'usulan-jabatan-dosen' => 'jabatan',
                'usulan-jabatan-tendik' => 'jabatan',
                'Usulan NUPTK' => 'nuptk',
                'Usulan Laporan LKD' => 'laporan-lkd',
                'Usulan Presensi' => 'presensi',
                'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
                'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
                'Usulan Laporan Serdos' => 'laporan-serdos',
                'Usulan Pensiun' => 'pensiun',
                'Usulan Kepangkatan' => 'kepangkatan',
                'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
                'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
                'Usulan Satyalancana' => 'satyalancana',
                'Usulan Tugas Belajar' => 'tugas-belajar',
                'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$periode_usulan->jenis_usulan] ?? 'jabatan';

            return redirect()->route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $request->input('nama_periode') . '" berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', '❌ Validasi gagal. Silakan periksa kembali data yang dimasukkan.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Gagal memperbarui periode usulan. Silakan coba lagi.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Menghapus resource dari storage.
     */
    public function destroy(PeriodeUsulan $periodeUsulan)
    {
        try {
            if ($periodeUsulan->usulans()->count() > 0) {
                return back()->with('error', '❌ Gagal menghapus! Periode "' . $periodeUsulan->nama_periode . '" sudah memiliki pendaftar.');
            }

            // Simpan jenis usulan sebelum dihapus untuk redirect
            $jenisUsulan = $periodeUsulan->jenis_usulan;
            $namaPeriode = $periodeUsulan->nama_periode;

            $periodeUsulan->delete();

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Usulan Jabatan' => 'jabatan',
                'usulan-jabatan-dosen' => 'jabatan',
                'usulan-jabatan-tendik' => 'jabatan',
                'Usulan NUPTK' => 'nuptk',
                'Usulan Laporan LKD' => 'laporan-lkd',
                'Usulan Presensi' => 'presensi',
                'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
                'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
                'Usulan Laporan Serdos' => 'laporan-serdos',
                'Usulan Pensiun' => 'pensiun',
                'Usulan Kepangkatan' => 'kepangkatan',
                'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
                'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
                'Usulan Satyalancana' => 'satyalancana',
                'Usulan Tugas Belajar' => 'tugas-belajar',
                'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$jenisUsulan] ?? 'jabatan';

            return redirect()->route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => $jenisKey])
                ->with('success', '✅ Periode usulan "' . $namaPeriode . '" berhasil dihapus!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', '❌ Terjadi kesalahan saat menghapus periode usulan. Silakan coba lagi.');
        }
    }

    /**
     * Validasi overlap berdasarkan jenis usulan yang sama
     */
    private function validateOverlapByJenisUsulan(Request $request, $excludeId = null)
    {
        $jenisUsulan = $request->jenis_usulan;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $query = PeriodeUsulan::where('jenis_usulan', $jenisUsulan)
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhere(function ($subQ) use ($tanggalMulai, $tanggalSelesai) {
                      $subQ->where('tanggal_mulai', '<=', $tanggalMulai)
                           ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $overlappingPeriode = $query->first();

        if ($overlappingPeriode) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal_mulai' => [
                    "Tanggal periode overlapping dengan periode '{$overlappingPeriode->nama_periode}' yang memiliki jenis usulan yang sama ({$jenisUsulan})"
                ]
            ]);
        }
    }
}

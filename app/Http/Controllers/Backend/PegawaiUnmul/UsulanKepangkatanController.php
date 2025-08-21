<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\UsulanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsulanKepangkatanController extends Controller
{
    /**
     * Display a listing of usulan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

        // Debug information
        Log::info('UsulanKepangkatanController@index Debug', [
            'pegawai_id' => $pegawai->id,
            'pegawai_nip' => $pegawai->nip,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'jenis_usulan_periode' => $jenisUsulanPeriode
        ]);

        // Get periode usulan yang sesuai dengan status kepegawaian
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Debug query results
        Log::info('Periode Usulan Query Results', [
            'total_periode_found' => $periodeUsulans->count(),
            'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
            'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray()
        ]);

        // Alternative query if no results
        if ($periodeUsulans->count() == 0) {
            // Try without JSON contains
            $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            Log::info('Alternative Query Results (without JSON contains)', [
                'total_periode_found' => $altPeriodeUsulans->count(),
                'periode_ids' => $altPeriodeUsulans->pluck('id')->toArray(),
                'periode_names' => $altPeriodeUsulans->pluck('nama_periode')->toArray()
            ]);

            // Use alternative results if found
            if ($altPeriodeUsulans->count() > 0) {
                $periodeUsulans = $altPeriodeUsulans;
            }
        }

        // Get usulan yang sudah dibuat oleh pegawai
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', $jenisUsulanPeriode)
                          ->with(['periodeUsulan'])
                          ->get();

        // Debug usulan yang ditemukan
        Log::info('Usulan yang ditemukan untuk pegawai', [
            'pegawai_id' => $pegawai->id,
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray()
        ]);

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.index', compact('periodeUsulans', 'usulans', 'pegawai'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Redirect ke halaman yang sesuai atau tampilkan form
        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                         ->with('info', 'Fitur Usulan Kepangkatan akan segera tersedia.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                         ->with('success', 'Usulan Kepangkatan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.show', compact('usulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.edit', compact('usulan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                         ->with('success', 'Usulan Kepangkatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                         ->with('success', 'Usulan Kepangkatan berhasil dihapus.');
    }

    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        return 'Usulan Kepangkatan';
    }

    /**
     * Get logs for a specific usulan
     */
    public function getLogs(Usulan $usulan)
    {
        // Authorization check
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki akses untuk melihat log usulan ini.');
        }

        try {
            // Get logs for this usulan, ordered by latest first
            $logs = UsulanLog::where('usulan_id', $usulan->id)
                ->with(['dilakukanOleh'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->getActionDescription(),
                        'status_sebelumnya' => $log->status_sebelumnya,
                        'status_baru' => $log->status_baru,
                        'catatan' => $log->catatan,
                        'user_name' => $log->user_name,
                        'created_at' => $log->formatted_date,
                        'relative_time' => $log->relative_time,
                        'status_badge_class' => $log->status_badge_class,
                        'status_icon' => $log->status_icon,
                        'is_status_change' => $log->isStatusChange(),
                        'is_initial_log' => $log->isInitialLog(),
                    ];
                });

            Log::info('Logs retrieved successfully', [
                'usulan_id' => $usulan->id,
                'total_logs' => $logs->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'total' => $logs->count()
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to retrieve logs', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat log aktivitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

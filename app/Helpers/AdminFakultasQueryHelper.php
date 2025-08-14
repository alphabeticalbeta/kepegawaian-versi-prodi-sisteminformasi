<?php

namespace App\Helpers;

use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\Log;

class AdminFakultasQueryHelper
{
    /**
     * Get periode usulan untuk admin fakultas dengan debugging
     */
    public static function getPeriodeUsulanForAdmin($adminId, $enableDebug = false)
    {
        try {
            // 1. Ambil data admin
            $admin = Pegawai::find($adminId);

            if (!$admin) {
                if ($enableDebug) {
                    Log::debug("AdminFakultasQueryHelper: Admin tidak ditemukan", ['admin_id' => $adminId]);
                }
                return collect();
            }

            // 2. Ambil unit_kerja_id
            $unitKerjaId = $admin->unit_kerja_id;

            if (!$unitKerjaId) {
                if ($enableDebug) {
                    Log::debug("AdminFakultasQueryHelper: unit_kerja_id NULL", [
                        'admin_id' => $adminId,
                        'admin_data' => $admin->toArray()
                    ]);
                }
                return collect();
            }

            // 3. Query periode usulan dengan count
            $query = PeriodeUsulan::withCount([
                'usulans as jumlah_pengusul' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ]);

            $result = $query->latest()->paginate(10);

            if ($enableDebug) {
                Log::debug("AdminFakultasQueryHelper: Query berhasil", [
                    'admin_id' => $adminId,
                    'unit_kerja_id' => $unitKerjaId,
                    'total_periode' => $result->total(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("AdminFakultasQueryHelper: Error", [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return collect();
        }
    }

    /**
     * Get unit kerja untuk admin fakultas
     */
    public static function getUnitKerjaForAdmin($adminId, $enableDebug = false)
    {
        try {
            $admin = Pegawai::with('unitKerjaPengelola')->find($adminId);

            if (!$admin) {
                return null;
            }

            $unitKerja = $admin->unitKerjaPengelola;

            if ($enableDebug) {
                Log::debug("AdminFakultasQueryHelper: Unit Kerja Check", [
                    'admin_id' => $adminId,
                    'unit_kerja_id' => $admin->unit_kerja_id,
                    'unit_kerja_loaded' => $unitKerja ? $unitKerja->nama : 'NULL',
                    'relation_exists' => method_exists($admin, 'unitKerjaPengelola')
                ]);
            }

            return $unitKerja;

        } catch (\Exception $e) {
            Log::error("AdminFakultasQueryHelper: Error getting unit kerja", [
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}

<?php

namespace App\Helpers;

use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminFakultasQueryHelper
{
    /**
     * Get periode usulan untuk admin fakultas dengan caching
     */
    public static function getPeriodeUsulanForAdmin($adminId, $enableDebug = false)
    {
        // OPTIMASI: Gunakan cache untuk mengurangi query berulang
        $cacheKey = "admin_fakultas_periode_{$adminId}";
        
        return Cache::remember($cacheKey, 300, function () use ($adminId, $enableDebug) {
            try {
                // OPTIMASI: Gunakan select() untuk mengambil kolom yang diperlukan saja
                $admin = Pegawai::select(['id', 'unit_kerja_id', 'nama_lengkap'])
                    ->find($adminId);

                if (!$admin) {
                    if ($enableDebug) {
                        Log::debug("AdminFakultasQueryHelper: Admin tidak ditemukan", ['admin_id' => $adminId]);
                    }
                    return collect();
                }

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

                // OPTIMASI: Gunakan query yang lebih efisien dengan select()
                $query = PeriodeUsulan::select(['id', 'nama_periode', 'status', 'tanggal_mulai', 'tanggal_selesai'])
                    ->withCount([
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
        });
    }

    /**
     * Get unit kerja untuk admin fakultas dengan caching
     */
    public static function getUnitKerjaForAdmin($adminId, $enableDebug = false)
    {
        // OPTIMASI: Gunakan cache untuk data yang jarang berubah
        $cacheKey = "admin_fakultas_unit_kerja_{$adminId}";
        
        return Cache::remember($cacheKey, 600, function () use ($adminId, $enableDebug) {
            try {
                $admin = Pegawai::select(['id', 'unit_kerja_id', 'nama_lengkap'])
                    ->with(['unitKerjaPengelola:id,nama'])
                    ->find($adminId);

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
        });
    }

    /**
     * Clear cache untuk admin tertentu
     */
    public static function clearCache($adminId): void
    {
        Cache::forget("admin_fakultas_periode_{$adminId}");
        Cache::forget("admin_fakultas_unit_kerja_{$adminId}");
    }

    /**
     * Clear all admin fakultas cache
     */
    public static function clearAllCache(): void
    {
        // Note: In production, you might want to use cache tags for better cache management
        // For now, we'll clear specific cache keys
        $admins = Pegawai::whereNotNull('unit_kerja_id')->pluck('id');
        
        foreach ($admins as $adminId) {
            self::clearCache($adminId);
        }
    }
}

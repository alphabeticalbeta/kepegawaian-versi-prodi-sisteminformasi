<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Usulan;

class CheckPenilaiAssignment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Only apply this middleware for penilai routes
            if (!Auth::user()) {
                return $next($request);
            }

            // Check if user has Penilai Universitas role
            $user = Auth::user();
            $hasRole = false;

            try {
                $hasRole = $user->hasRole('Penilai Universitas');
            } catch (\Exception $e) {
                \Log::error('Error checking role: ' . $e->getMessage());
                return $next($request);
            }

            if (!$hasRole) {
                return $next($request);
            }

            // Get usulan from route parameter
            $usulanId = $request->route('usulan');

            if ($usulanId) {
                $usulan = Usulan::find($usulanId);

                if (!$usulan) {
                    abort(404, 'Usulan tidak ditemukan.');
                }

                // Check if usulan is assigned to current penilai
                if (!$usulan->isAssignedToPenilai($user->id)) {
                    abort(403, 'Anda tidak memiliki akses untuk usulan ini. Usulan ini tidak ditugaskan kepada Anda.');
                }
            }

            return $next($request);
        } catch (\Exception $e) {
            \Log::error('Middleware error: ' . $e->getMessage());
            return $next($request);
        }
    }
}

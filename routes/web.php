<?php

// EMERGENCY BYPASS ROUTES FOR TESTING
Route::get('/test-emergency/{role}', function ($role) {
    return view('backend.layouts.views.simple-dashboard', [
        'title' => 'Emergency Test - ' . ucfirst($role),
        'user' => (object)['nama_lengkap' => 'Test User', 'getRoleNames' => function() use ($role) { return collect([$role]); }]
    ]);
});

Route::get('/test-static', function () {
    return '<h1>Static Route Test</h1><p>Time: ' . now() . '</p><p>If you see this, routing works but dashboard has issues.</p>';
});

// DIRECT CONTROLLER TEST ROUTES (NO AUTH)
Route::get('/test-admin-fakultas', [App\Http\Controllers\Backend\AdminFakultas\DashboardController::class, 'index']);
Route::get('/test-admin-keuangan', [App\Http\Controllers\Backend\AdminKeuangan\DashboardController::class, 'index']);
Route::get('/test-penilai', [App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController::class, 'index']);
Route::get('/test-tim-senat', [App\Http\Controllers\Backend\TimSenat\DashboardController::class, 'index']);


require __DIR__.'/frontend.php';
require __DIR__.'/backend.php';

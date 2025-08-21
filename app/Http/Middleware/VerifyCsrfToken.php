<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'pegawai-unmul/usulan-jabatan', // Temporary for testing
        'pegawai-unmul/usulan-jabatan/test', // Test route
        'test-usulan-submission', // Test route
    ];
}

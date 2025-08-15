<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // OPTIMASI: Enable lazy loading prevention in production
        Model::preventLazyLoading(!app()->isProduction());

        // OPTIMASI: Log slow queries in development
        if (app()->isLocal()) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log queries taking more than 100ms
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                        'connection' => $query->connection->getName(),
                    ]);
                }
            });
        }

        // OPTIMASI: Set default string length for MySQL
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // OPTIMASI: Configure cache for better performance
        $this->configureCache();
    }

    /**
     * Configure cache settings for optimization
     */
    private function configureCache(): void
    {
        // Set cache TTL based on environment
        $defaultTtl = app()->isProduction() ? 3600 : 300; // 1 hour in prod, 5 min in dev
        
        config([
            'cache.ttl.default' => $defaultTtl,
            'cache.ttl.short' => 300,
            'cache.ttl.medium' => 1800,
            'cache.ttl.long' => 86400,
        ]);
    }
}

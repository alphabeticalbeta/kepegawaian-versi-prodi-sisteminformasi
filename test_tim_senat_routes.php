<?php

require_once 'vendor/autoload.php';

// Test Tim Senat Routes
echo "=== TEST TIM SENAT ROUTES ===\n\n";

try {
    // Get all routes
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes());
    
    // Filter tim-senat routes
    $timSenatRoutes = $routes->filter(function ($route) {
        return str_contains($route->getName() ?? '', 'tim-senat');
    });
    
    echo "1. Available Tim Senat Routes:\n";
    foreach ($timSenatRoutes as $route) {
        $methods = implode('|', $route->methods());
        $name = $route->getName() ?? 'No Name';
        $uri = $route->uri();
        
        echo "   {$methods} | {$name} | {$uri}\n";
    }
    
    echo "\n2. Routes Used in Sidebar:\n";
    $sidebarRoutes = [
        'tim-senat.dashboard',
        'tim-senat.rapat-senat.index',
        'tim-senat.keputusan-senat.index',
        'tim-senat.usulan.index'
    ];
    
    foreach ($sidebarRoutes as $routeName) {
        $routeExists = $timSenatRoutes->contains(function ($route) use ($routeName) {
            return $route->getName() === $routeName;
        });
        
        $status = $routeExists ? '✅ EXISTS' : '❌ MISSING';
        echo "   {$routeName}: {$status}\n";
    }
    
    echo "\n3. Removed Routes (should not exist):\n";
    $removedRoutes = [
        'tim-senat.usulan-dosen.index',
        'tim-senat.review-akademik.index',
        'tim-senat.laporan-senat.index'
    ];
    
    foreach ($removedRoutes as $routeName) {
        $routeExists = $timSenatRoutes->contains(function ($route) use ($routeName) {
            return $route->getName() === $routeName;
        });
        
        $status = $routeExists ? '❌ STILL EXISTS' : '✅ REMOVED';
        echo "   {$routeName}: {$status}\n";
    }
    
    echo "\n4. Route Structure Analysis:\n";
    echo "   Total Tim Senat Routes: " . $timSenatRoutes->count() . "\n";
    echo "   Routes Used in Sidebar: " . count($sidebarRoutes) . "\n";
    echo "   Routes Removed: " . count($removedRoutes) . "\n";
    
    // Check if all sidebar routes exist
    $allSidebarRoutesExist = collect($sidebarRoutes)->every(function ($routeName) use ($timSenatRoutes) {
        return $timSenatRoutes->contains(function ($route) use ($routeName) {
            return $route->getName() === $routeName;
        });
    });
    
    echo "\n5. Sidebar Route Status:\n";
    if ($allSidebarRoutesExist) {
        echo "   ✅ All sidebar routes are properly defined\n";
    } else {
        echo "   ❌ Some sidebar routes are missing\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

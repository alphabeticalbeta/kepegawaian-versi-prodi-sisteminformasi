<?php

echo "=== PHP TEST ===\n";
echo "PHP is working!\n";

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';

if (file_exists($file)) {
    echo "File exists: $file\n";
    
    $content = file_get_contents($file);
    $ifCount = preg_match_all('/@if\s*\(/', $content);
    $endifCount = preg_match_all('/@endif/', $content);
    
    echo "Total @if: $ifCount\n";
    echo "Total @endif: $endifCount\n";
    echo "Difference: " . ($ifCount - $endifCount) . "\n";
    
    $lines = file($file);
    echo "Total lines: " . count($lines) . "\n";
    
    echo "Last line: " . trim(end($lines)) . "\n";
} else {
    echo "File does not exist: $file\n";
}

echo "=== END TEST ===\n";

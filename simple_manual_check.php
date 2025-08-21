<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';

echo "=== SIMPLE MANUAL CHECK ===\n";

if (!file_exists($file)) {
    echo "File does not exist!\n";
    exit;
}

$content = file_get_contents($file);
$lines = file($file);

echo "File exists\n";
echo "Total lines: " . count($lines) . "\n";
echo "File size: " . strlen($content) . " bytes\n";

// Simple count
$ifCount = substr_count($content, '@if(');
$endifCount = substr_count($content, '@endif');

echo "Simple count:\n";
echo "@if( count: $ifCount\n";
echo "@endif count: $endifCount\n";
echo "Difference: " . ($ifCount - $endifCount) . "\n";

// Check last few lines
echo "\nLast 5 lines:\n";
$lastLines = array_slice($lines, -5);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 4 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

echo "\nDone.\n";

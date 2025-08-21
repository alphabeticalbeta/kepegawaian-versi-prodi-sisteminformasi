<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';

echo "=== DIRECT FILE CHECK ===\n";

if (!file_exists($file)) {
    echo "File does not exist!\n";
    exit;
}

$content = file_get_contents($file);
$lines = file($file);

echo "File exists\n";
echo "Total lines: " . count($lines) . "\n";

// Count using different methods
$ifCount1 = substr_count($content, '@if(');
$endifCount1 = substr_count($content, '@endif');

$ifCount2 = preg_match_all('/@if\s*\(/', $content);
$endifCount2 = preg_match_all('/@endif/', $content);

echo "Method 1 (substr_count):\n";
echo "@if( count: $ifCount1\n";
echo "@endif count: $endifCount1\n";
echo "Difference: " . ($ifCount1 - $endifCount1) . "\n\n";

echo "Method 2 (preg_match_all):\n";
echo "@if count: $ifCount2\n";
echo "@endif count: $endifCount2\n";
echo "Difference: " . ($ifCount2 - $endifCount2) . "\n\n";

// Check last lines
echo "Last 10 lines:\n";
$lastLines = array_slice($lines, -10);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 9 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

echo "\nDone.\n";

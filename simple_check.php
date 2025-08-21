<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';
$content = file_get_contents($file);

echo "=== SIMPLE BLADE CHECK ===\n";
echo "File: $file\n\n";

// Count @if statements
$ifCount = preg_match_all('/@if\s*\(/', $content);
$endifCount = preg_match_all('/@endif/', $content);

echo "Total @if statements: $ifCount\n";
echo "Total @endif statements: $endifCount\n";
echo "Difference: " . ($ifCount - $endifCount) . "\n\n";

if ($ifCount === $endifCount) {
    echo "✅ Counts are balanced\n";
} else {
    echo "❌ Counts are NOT balanced\n";
}

// Check if file ends with @endif
$lastLines = array_slice(file($file), -5);
echo "\nLast 5 lines of file:\n";
foreach ($lastLines as $i => $line) {
    $lineNum = count(file($file)) - 4 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';

echo "=== FINAL BLADE CHECK ===\n";
echo "File: $file\n\n";

// Read file content
$content = file_get_contents($file);
$lines = file($file);

echo "Total lines: " . count($lines) . "\n";

// Count @if and @endif
$ifCount = preg_match_all('/@if\s*\(/', $content);
$endifCount = preg_match_all('/@endif/', $content);

echo "Total @if: $ifCount\n";
echo "Total @endif: $endifCount\n";
echo "Difference: " . ($ifCount - $endifCount) . "\n\n";

// Check last few lines
echo "Last 5 lines:\n";
$lastLines = array_slice($lines, -5);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 4 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

echo "\n=== MANUAL CHECK ===\n";

// Manual stack check
$stack = [];
$lineNum = 0;

foreach ($lines as $line) {
    $lineNum++;
    
    if (preg_match('/@if\s*\(/', $line)) {
        $stack[] = $lineNum;
        echo "Line $lineNum: PUSH @if - " . trim($line) . "\n";
    }
    
    if (preg_match('/@endif/', $line)) {
        if (empty($stack)) {
            echo "Line $lineNum: ERROR - Extra @endif without @if\n";
        } else {
            $lastIf = array_pop($stack);
            echo "Line $lineNum: POP @endif - matches @if from line $lastIf\n";
        }
    }
}

if (!empty($stack)) {
    echo "\n❌ UNMATCHED @if statements:\n";
    foreach ($stack as $line) {
        echo "  Line $line\n";
    }
} else {
    echo "\n✅ All @if statements have matching @endif\n";
}

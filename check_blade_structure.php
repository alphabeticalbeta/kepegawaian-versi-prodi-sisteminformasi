<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';
$lines = file($file);

echo "=== DETAILED BLADE STRUCTURE ANALYSIS ===\n";
echo "File: $file\n\n";

$stack = [];
$lineNumbers = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Convert to 1-based indexing
    
    // Check for @if statements
    if (preg_match('/@if\s*\(/', $line)) {
        $stack[] = ['type' => 'if', 'line' => $lineNum, 'content' => trim($line)];
        echo "Line {$lineNum}: Found @if - " . trim($line) . "\n";
    }
    
    // Check for @endif statements
    if (preg_match('/@endif/', $line)) {
        if (empty($stack)) {
            echo "❌ Line {$lineNum}: Extra @endif found without matching @if\n";
        } else {
            $lastIf = array_pop($stack);
            echo "Line {$lineNum}: Found @endif - matches @if from line {$lastIf['line']}\n";
        }
    }
}

echo "\n=== UNMATCHED @if STATEMENTS ===\n";
if (!empty($stack)) {
    echo "❌ Found " . count($stack) . " unmatched @if statements:\n";
    foreach ($stack as $item) {
        echo "  Line {$item['line']}: {$item['content']}\n";
    }
} else {
    echo "✅ All @if statements have matching @endif\n";
}

echo "\n=== SUMMARY ===\n";
$ifCount = preg_match_all('/@if\s*\(/', file_get_contents($file));
$endifCount = preg_match_all('/@endif/', file_get_contents($file));
echo "Total @if: $ifCount\n";
echo "Total @endif: $endifCount\n";
echo "Balance: " . ($ifCount - $endifCount) . "\n";

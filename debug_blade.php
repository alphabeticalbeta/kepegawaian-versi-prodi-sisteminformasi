<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';
$lines = file($file);

echo "=== BLADE DEBUG ANALYSIS ===\n";
echo "File: $file\n";
echo "Total lines: " . count($lines) . "\n\n";

$stack = [];
$issues = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Convert to 1-based indexing
    
    // Check for @if statements
    if (preg_match('/@if\s*\(/', $line)) {
        $stack[] = ['type' => 'if', 'line' => $lineNum, 'content' => trim($line)];
    }
    
    // Check for @endif statements
    if (preg_match('/@endif/', $line)) {
        if (empty($stack)) {
            $issues[] = "Line {$lineNum}: Extra @endif found without matching @if";
        } else {
            $lastIf = array_pop($stack);
        }
    }
}

echo "=== ISSUES FOUND ===\n";
if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "❌ $issue\n";
    }
} else {
    echo "✅ No extra @endif found\n";
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

echo "\n=== LAST 10 LINES ===\n";
$lastLines = array_slice($lines, -10);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 9 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

echo "\n=== SUMMARY ===\n";
$ifCount = preg_match_all('/@if\s*\(/', file_get_contents($file));
$endifCount = preg_match_all('/@endif/', file_get_contents($file));
echo "Total @if: $ifCount\n";
echo "Total @endif: $endifCount\n";
echo "Balance: " . ($ifCount - $endifCount) . "\n";

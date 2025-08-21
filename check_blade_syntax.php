<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';
$content = file_get_contents($file);

// Count @if statements
$ifCount = preg_match_all('/@if\s*\(/', $content);
$endifCount = preg_match_all('/@endif/', $content);

echo "=== BLADE SYNTAX CHECK ===\n";
echo "File: $file\n";
echo "Total @if statements: $ifCount\n";
echo "Total @endif statements: $endifCount\n";

if ($ifCount === $endifCount) {
    echo "✅ Syntax OK - @if and @endif are balanced\n";
} else {
    echo "❌ Syntax ERROR - @if and @endif are NOT balanced\n";
    echo "Difference: " . ($ifCount - $endifCount) . " more @if than @endif\n";
}

// Find all @if and @endif with line numbers
echo "\n=== DETAILED ANALYSIS ===\n";
$lines = file($file);
$ifLines = [];
$endifLines = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Convert to 1-based indexing
    if (preg_match('/@if\s*\(/', $line)) {
        $ifLines[] = $lineNum;
    }
    if (preg_match('/@endif/', $line)) {
        $endifLines[] = $lineNum;
    }
}

echo "Line numbers with @if:\n";
foreach ($ifLines as $lineNum) {
    echo "  Line $lineNum\n";
}

echo "\nLine numbers with @endif:\n";
foreach ($endifLines as $lineNum) {
    echo "  Line $lineNum\n";
}

echo "\n=== CHECKING FOR UNMATCHED @if ===\n";
$stack = [];
$unmatched = [];

foreach ($lines as $lineNum => $line) {
    $lineNum++; // Convert to 1-based indexing
    if (preg_match('/@if\s*\(/', $line)) {
        $stack[] = $lineNum;
    }
    if (preg_match('/@endif/', $line)) {
        if (empty($stack)) {
            echo "❌ Extra @endif found at line $lineNum\n";
        } else {
            array_pop($stack);
        }
    }
}

if (!empty($stack)) {
    echo "❌ Unmatched @if statements found at lines:\n";
    foreach ($stack as $lineNum) {
        echo "  Line $lineNum\n";
    }
} else {
    echo "✅ All @if statements have matching @endif\n";
}

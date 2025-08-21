<?php

$file = 'resources/views/backend/layouts/views/shared/usulan-detail.blade.php';

echo "=== COMPREHENSIVE BLADE STRUCTURE CHECK ===\n";
echo "File: $file\n\n";

if (!file_exists($file)) {
    echo "❌ File does not exist!\n";
    exit;
}

$content = file_get_contents($file);
$lines = file($file);

echo "Total lines: " . count($lines) . "\n";

// Count all Blade directives
$ifCount = preg_match_all('/@if\s*\(/', $content);
$endifCount = preg_match_all('/@endif/', $content);
$elseifCount = preg_match_all('/@elseif\s*\(/', $content);
$elseCount = preg_match_all('/@else/', $content);

echo "\n=== BLADE DIRECTIVE COUNTS ===\n";
echo "Total @if: $ifCount\n";
echo "Total @endif: $endifCount\n";
echo "Total @elseif: $elseifCount\n";
echo "Total @else: $elseCount\n";

// Check balance
$totalOpenings = $ifCount;
$totalClosings = $endifCount;
$balance = $totalOpenings - $totalClosings;

echo "\nBalance: $balance\n";
if ($balance == 0) {
    echo "✅ @if and @endif are balanced\n";
} else {
    echo "❌ @if and @endif are NOT balanced\n";
}

// Detailed analysis
echo "\n=== DETAILED STRUCTURE ANALYSIS ===\n";
$stack = [];
$issues = [];
$lineNum = 0;

foreach ($lines as $line) {
    $lineNum++;
    $trimmedLine = trim($line);
    
    // Check for @if
    if (preg_match('/@if\s*\(/', $trimmedLine)) {
        $stack[] = [
            'type' => 'if',
            'line' => $lineNum,
            'content' => $trimmedLine
        ];
        echo "Line $lineNum: PUSH @if - $trimmedLine\n";
    }
    
    // Check for @elseif
    if (preg_match('/@elseif\s*\(/', $trimmedLine)) {
        if (empty($stack)) {
            $issues[] = "Line $lineNum: @elseif without @if";
            echo "❌ Line $lineNum: @elseif without @if - $trimmedLine\n";
        } else {
            $last = end($stack);
            if ($last['type'] !== 'if' && $last['type'] !== 'elseif') {
                $issues[] = "Line $lineNum: @elseif in wrong context";
                echo "❌ Line $lineNum: @elseif in wrong context - $trimmedLine\n";
            } else {
                $stack[] = [
                    'type' => 'elseif',
                    'line' => $lineNum,
                    'content' => $trimmedLine
                ];
                echo "Line $lineNum: PUSH @elseif - $trimmedLine\n";
            }
        }
    }
    
    // Check for @else
    if (preg_match('/@else\b/', $trimmedLine)) {
        if (empty($stack)) {
            $issues[] = "Line $lineNum: @else without @if";
            echo "❌ Line $lineNum: @else without @if - $trimmedLine\n";
        } else {
            $last = end($stack);
            if ($last['type'] !== 'if' && $last['type'] !== 'elseif') {
                $issues[] = "Line $lineNum: @else in wrong context";
                echo "❌ Line $lineNum: @else in wrong context - $trimmedLine\n";
            } else {
                $stack[] = [
                    'type' => 'else',
                    'line' => $lineNum,
                    'content' => $trimmedLine
                ];
                echo "Line $lineNum: PUSH @else - $trimmedLine\n";
            }
        }
    }
    
    // Check for @endif
    if (preg_match('/@endif/', $trimmedLine)) {
        if (empty($stack)) {
            $issues[] = "Line $lineNum: Extra @endif without @if";
            echo "❌ Line $lineNum: Extra @endif without @if - $trimmedLine\n";
        } else {
            $last = array_pop($stack);
            echo "Line $lineNum: POP @endif - matches {$last['type']} from line {$last['line']}\n";
        }
    }
}

echo "\n=== UNMATCHED STATEMENTS ===\n";
if (!empty($stack)) {
    echo "❌ Found " . count($stack) . " unmatched statements:\n";
    foreach ($stack as $item) {
        echo "  Line {$item['line']}: {$item['type']} - {$item['content']}\n";
    }
} else {
    echo "✅ All statements have matching @endif\n";
}

echo "\n=== ISSUES FOUND ===\n";
if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "❌ $issue\n";
    }
} else {
    echo "✅ No structural issues found\n";
}

echo "\n=== LAST 10 LINES ===\n";
$lastLines = array_slice($lines, -10);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 9 + $i;
    echo "Line $lineNum: " . trim($line) . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "File size: " . strlen($content) . " bytes\n";
echo "Total lines: " . count($lines) . "\n";
echo "Balance: $balance\n";

if ($balance == 0 && empty($stack) && empty($issues)) {
    echo "✅ File structure appears to be correct\n";
} else {
    echo "❌ File has structural issues\n";
}

<?php

echo "=== SIMPLE DEBUG VALIDATION ===\n";

// Connect to database directly
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kepegawaian_unmul', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected\n\n";
    
    // Get usulan 16
    $stmt = $pdo->prepare("SELECT id, status_usulan, validasi_data FROM usulans WHERE id = 16");
    $stmt->execute();
    $usulan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usulan) {
        echo "❌ Usulan 16 tidak ditemukan\n";
        exit(1);
    }
    
    echo "✅ Usulan 16 ditemukan\n";
    echo "Status: " . ($usulan['status_usulan'] ?? 'N/A') . "\n\n";
    
    // Check validasi_data
    $validasiData = json_decode($usulan['validasi_data'], true);
    
    echo "=== VALIDASI DATA ===\n";
    if ($validasiData) {
        echo "Validasi data exists:\n";
        print_r($validasiData);
        
        // Check tim_penilai
        if (isset($validasiData['tim_penilai'])) {
            echo "\n=== TIM PENILAI DATA ===\n";
            $timPenilai = $validasiData['tim_penilai'];
            
            if (isset($timPenilai['reviews'])) {
                echo "Reviews found: " . count($timPenilai['reviews']) . "\n";
                foreach ($timPenilai['reviews'] as $i => $review) {
                    echo "Review " . ($i + 1) . ":\n";
                    echo "  Type: " . ($review['type'] ?? 'N/A') . "\n";
                    echo "  Has validation: " . (isset($review['validation']) ? 'Yes' : 'No') . "\n";
                    
                    if (isset($review['validation'])) {
                        $invalidCount = 0;
                        foreach ($review['validation'] as $category => $fields) {
                            if (is_array($fields)) {
                                foreach ($fields as $field => $data) {
                                    if (isset($data['status']) && $data['status'] === 'tidak_sesuai') {
                                        $invalidCount++;
                                        echo "    ⚠️ Invalid: $category > $field\n";
                                    }
                                }
                            }
                        }
                        echo "  Total invalid fields: $invalidCount\n";
                    }
                }
            } else {
                echo "❌ No reviews found\n";
            }
        } else {
            echo "❌ No tim_penilai data found\n";
        }
    } else {
        echo "❌ No validasi_data found or invalid JSON\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

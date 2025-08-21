<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\UnitKerja;
use App\Models\BackendUnivUsulan\SubUnitKerja;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

echo "=== UNIT KERJA DATA TEST ===\n\n";

try {
    // Test 1: Check if models can be loaded
    echo "1. Testing model loading...\n";
    echo "   UnitKerja model: " . (class_exists(UnitKerja::class) ? "✅ OK" : "❌ FAILED") . "\n";
    echo "   SubUnitKerja model: " . (class_exists(SubUnitKerja::class) ? "✅ OK" : "❌ FAILED") . "\n";
    echo "   SubSubUnitKerja model: " . (class_exists(SubSubUnitKerja::class) ? "✅ OK" : "❌ FAILED") . "\n\n";

    // Test 2: Check database connection
    echo "2. Testing database connection...\n";
    try {
        $unitKerjaCount = UnitKerja::count();
        echo "   Database connection: ✅ OK\n";
        echo "   Unit Kerja count: {$unitKerjaCount}\n";
    } catch (Exception $e) {
        echo "   Database connection: ❌ FAILED - " . $e->getMessage() . "\n";
        exit(1);
    }

    // Test 3: Check data availability
    echo "\n3. Testing data availability...\n";

    $unitKerjas = UnitKerja::all();
    $subUnitKerjas = SubUnitKerja::all();
    $subSubUnitKerjas = SubSubUnitKerja::all();

    echo "   Unit Kerja: " . $unitKerjas->count() . " records\n";
    echo "   Sub Unit Kerja: " . $subUnitKerjas->count() . " records\n";
    echo "   Sub Sub Unit Kerja: " . $subSubUnitKerjas->count() . " records\n";

    if ($unitKerjas->count() == 0) {
        echo "   ⚠️  WARNING: No Unit Kerja data found!\n";
    }
    if ($subUnitKerjas->count() == 0) {
        echo "   ⚠️  WARNING: No Sub Unit Kerja data found!\n";
    }
    if ($subSubUnitKerjas->count() == 0) {
        echo "   ⚠️  WARNING: No Sub Sub Unit Kerja data found!\n";
    }

    // Test 4: Check relationships
    echo "\n4. Testing relationships...\n";

    if ($unitKerjas->count() > 0) {
        $firstUnitKerja = $unitKerjas->first();
        echo "   First Unit Kerja: {$firstUnitKerja->nama} (ID: {$firstUnitKerja->id})\n";

        $subUnits = $firstUnitKerja->subUnitKerjas;
        echo "   Sub Units for first Unit Kerja: " . $subUnits->count() . " records\n";

        if ($subUnits->count() > 0) {
            $firstSubUnit = $subUnits->first();
            echo "   First Sub Unit: {$firstSubUnit->nama} (ID: {$firstSubUnit->id})\n";

            $subSubUnits = $firstSubUnit->subSubUnitKerjas;
            echo "   Sub Sub Units for first Sub Unit: " . $subSubUnits->count() . " records\n";

            if ($subSubUnits->count() > 0) {
                $firstSubSubUnit = $subSubUnits->first();
                echo "   First Sub Sub Unit: {$firstSubSubUnit->nama} (ID: {$firstSubSubUnit->id})\n";
            }
        }
    }

    // Test 5: Check data structure for dropdowns
    echo "\n5. Testing dropdown data structure...\n";

    // Simulate controller logic
    $unitKerjaOptions = [];
    $subUnitKerjaOptions = [];
    $subSubUnitKerjaOptions = [];

    // Build unit kerja options
    foreach ($unitKerjas as $unitKerja) {
        $unitKerjaOptions[$unitKerja->id] = $unitKerja->nama;
    }

    // Build sub unit kerja options
    foreach ($subUnitKerjas as $subUnitKerja) {
        if ($subUnitKerja->unitKerja) {
            $unitKerjaId = $subUnitKerja->unit_kerja_id;
            $subUnitKerjaOptions[$unitKerjaId][$subUnitKerja->id] = $subUnitKerja->nama;
        }
    }

    // Build sub sub unit kerja options
    foreach ($subSubUnitKerjas as $subSubUnitKerja) {
        if ($subSubUnitKerja->subUnitKerja) {
            $subUnitKerjaId = $subSubUnitKerja->sub_unit_kerja_id;
            $subSubUnitKerjaOptions[$subUnitKerjaId][$subSubUnitKerja->id] = $subSubUnitKerja->nama;
        }
    }

    echo "   Unit Kerja Options: " . count($unitKerjaOptions) . " items\n";
    echo "   Sub Unit Kerja Options: " . count($subUnitKerjaOptions) . " groups\n";
    echo "   Sub Sub Unit Kerja Options: " . count($subSubUnitKerjaOptions) . " groups\n";

    // Show sample data
    if (count($unitKerjaOptions) > 0) {
        $firstUnitId = array_keys($unitKerjaOptions)[0];
        echo "   Sample Unit Kerja: ID {$firstUnitId} = '{$unitKerjaOptions[$firstUnitId]}'\n";

        if (isset($subUnitKerjaOptions[$firstUnitId])) {
            $firstSubId = array_keys($subUnitKerjaOptions[$firstUnitId])[0];
            echo "   Sample Sub Unit Kerja for Unit {$firstUnitId}: ID {$firstSubId} = '{$subUnitKerjaOptions[$firstUnitId][$firstSubId]}'\n";

            if (isset($subSubUnitKerjaOptions[$firstSubId])) {
                $firstSubSubId = array_keys($subSubUnitKerjaOptions[$firstSubId])[0];
                echo "   Sample Sub Sub Unit Kerja for Sub Unit {$firstSubId}: ID {$firstSubSubId} = '{$subSubUnitKerjaOptions[$firstSubId][$firstSubSubId]}'\n";
            }
        }
    }

    // Test 6: Check for potential issues
    echo "\n6. Checking for potential issues...\n";

    $issues = [];

    // Check for orphaned records
    $orphanedSubUnits = SubUnitKerja::whereNotIn('unit_kerja_id', $unitKerjas->pluck('id'))->count();
    if ($orphanedSubUnits > 0) {
        $issues[] = "Found {$orphanedSubUnits} orphaned Sub Unit Kerja records";
    }

    $orphanedSubSubUnits = SubSubUnitKerja::whereNotIn('sub_unit_kerja_id', $subUnitKerjas->pluck('id'))->count();
    if ($orphanedSubSubUnits > 0) {
        $issues[] = "Found {$orphanedSubSubUnits} orphaned Sub Sub Unit Kerja records";
    }

    // Check for empty names
    $emptyUnitNames = UnitKerja::whereNull('nama')->orWhere('nama', '')->count();
    if ($emptyUnitNames > 0) {
        $issues[] = "Found {$emptyUnitNames} Unit Kerja with empty names";
    }

    $emptySubUnitNames = SubUnitKerja::whereNull('nama')->orWhere('nama', '')->count();
    if ($emptySubUnitNames > 0) {
        $issues[] = "Found {$emptySubUnitNames} Sub Unit Kerja with empty names";
    }

    $emptySubSubUnitNames = SubSubUnitKerja::whereNull('nama')->orWhere('nama', '')->count();
    if ($emptySubSubUnitNames > 0) {
        $issues[] = "Found {$emptySubSubUnitNames} Sub Sub Unit Kerja with empty names";
    }

    if (empty($issues)) {
        echo "   ✅ No issues found\n";
    } else {
        echo "   ⚠️  Issues found:\n";
        foreach ($issues as $issue) {
            echo "      - {$issue}\n";
        }
    }

    echo "\n=== TEST COMPLETED ===\n";

    if ($unitKerjas->count() == 0 || $subUnitKerjas->count() == 0 || $subSubUnitKerjas->count() == 0) {
        echo "\n🔧 RECOMMENDATION: You need to add data to the master unit kerja tables.\n";
        echo "   Please go to the master unit kerja page and add some test data.\n";
    } else {
        echo "\n✅ Data looks good. The issue might be in the JavaScript or view logic.\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

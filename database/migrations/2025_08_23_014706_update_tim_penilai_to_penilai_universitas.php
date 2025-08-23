<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update role name from 'Tim Penilai' to 'Penilai Universitas'
        $timPenilaiRole = Role::where('name', 'Tim Penilai')->first();
        
        if ($timPenilaiRole) {
            $timPenilaiRole->update(['name' => 'Penilai Universitas']);
            
            // Update display name if exists
            if (Schema::hasColumn('roles', 'display_name')) {
                $timPenilaiRole->update(['display_name' => 'Penilai Universitas']);
            }
            
            // Update description if exists
            if (Schema::hasColumn('roles', 'description')) {
                $timPenilaiRole->update(['description' => 'Penilai untuk usulan universitas']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert role name from 'Penilai Universitas' to 'Tim Penilai'
        $penilaiUniversitasRole = Role::where('name', 'Penilai Universitas')->first();
        
        if ($penilaiUniversitasRole) {
            $penilaiUniversitasRole->update(['name' => 'Tim Penilai']);
            
            // Revert display name if exists
            if (Schema::hasColumn('roles', 'display_name')) {
                $penilaiUniversitasRole->update(['display_name' => 'Tim Penilai']);
            }
            
            // Revert description if exists
            if (Schema::hasColumn('roles', 'description')) {
                $penilaiUniversitasRole->update(['description' => 'Tim Penilai untuk usulan universitas']);
            }
        }
    }
};

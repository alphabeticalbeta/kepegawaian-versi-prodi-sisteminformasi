<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clear any existing data that might be too long
        DB::table('usulans')->whereNotNull('jenis_nuptk')->update(['jenis_nuptk' => null]);
        
        Schema::table('usulans', function (Blueprint $table) {
            $table->string('jenis_nuptk', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->string('jenis_nuptk', 255)->change();
        });
    }
};

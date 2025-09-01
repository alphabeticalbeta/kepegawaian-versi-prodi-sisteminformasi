<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->foreignId('pangkat_tujuan_id')->nullable()->constrained('pangkats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->dropForeign(['pangkat_tujuan_id']);
            $table->dropColumn('pangkat_tujuan_id');
        });
    }
};

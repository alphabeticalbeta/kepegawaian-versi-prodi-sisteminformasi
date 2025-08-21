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
        // Check if username column already exists
        if (!Schema::hasColumn('pegawais', 'username')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->string('username')->nullable()->after('email');
            });

            // Update existing records to use NIP as username
            DB::statement('UPDATE pegawais SET username = nip WHERE username IS NULL');

            // Add unique constraint
            Schema::table('pegawais', function (Blueprint $table) {
                $table->unique('username');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pegawais', 'username')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }
    }
};

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
        // Add indexes for slow queries detected in logs
        
        // Index for pegawais table - NIP lookup
        if (!$this->indexExists('pegawais', 'idx_pegawais_nip')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->index('nip', 'idx_pegawais_nip');
            });
        }
        
        // Index for pegawais table - ID lookup
        if (!$this->indexExists('pegawais', 'idx_pegawais_id')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->index('id', 'idx_pegawais_id');
            });
        }
        
        // Index for usulans table - ID lookup
        if (!$this->indexExists('usulans', 'idx_usulans_id')) {
            Schema::table('usulans', function (Blueprint $table) {
                $table->index('id', 'idx_usulans_id');
            });
        }
        
        // Composite index for usulans table - status and jenis
        if (!$this->indexExists('usulans', 'idx_usulans_status_jenis_created')) {
            Schema::table('usulans', function (Blueprint $table) {
                $table->index(['status_usulan', 'jenis_usulan', 'created_at'], 'idx_usulans_status_jenis_created');
            });
        }
        
        // Index for unit_kerja_id in pegawais
        if (!$this->indexExists('pegawais', 'idx_pegawais_unit_kerja_terakhir')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->index('unit_kerja_id', 'idx_pegawais_unit_kerja_terakhir');
            });
        }
        
        // Index for pangkat_terakhir_id in pegawais
        if (!$this->indexExists('pegawais', 'idx_pegawais_pangkat_terakhir')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->index('pangkat_terakhir_id', 'idx_pegawais_pangkat_terakhir');
            });
        }
        
        // Index for jabatan_terakhir_id in pegawais
        if (!$this->indexExists('pegawais', 'idx_pegawais_jabatan_terakhir')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->index('jabatan_terakhir_id', 'idx_pegawais_jabatan_terakhir');
            });
        }
        
        // Index for unit_kerja_id in sub_unit_kerjas (correct column name)
        if (!$this->indexExists('sub_unit_kerjas', 'idx_sub_unit_kerjas_unit')) {
            Schema::table('sub_unit_kerjas', function (Blueprint $table) {
                $table->index('unit_kerja_id', 'idx_sub_unit_kerjas_unit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if ($this->indexExists('pegawais', 'idx_pegawais_nip')) {
                $table->dropIndex('idx_pegawais_nip');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_id')) {
                $table->dropIndex('idx_pegawais_id');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_unit_kerja_terakhir')) {
                $table->dropIndex('idx_pegawais_unit_kerja_terakhir');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_pangkat_terakhir')) {
                $table->dropIndex('idx_pegawais_pangkat_terakhir');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_jabatan_terakhir')) {
                $table->dropIndex('idx_pegawais_jabatan_terakhir');
            }
        });
        
        Schema::table('usulans', function (Blueprint $table) {
            if ($this->indexExists('usulans', 'idx_usulans_id')) {
                $table->dropIndex('idx_usulans_id');
            }
            if ($this->indexExists('usulans', 'idx_usulans_status_jenis_created')) {
                $table->dropIndex('idx_usulans_status_jenis_created');
            }
        });
        
        Schema::table('sub_unit_kerjas', function (Blueprint $table) {
            if ($this->indexExists('sub_unit_kerjas', 'idx_sub_unit_kerjas_unit')) {
                $table->dropIndex('idx_sub_unit_kerjas_unit');
            }
        });
    }
    
    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        return collect($indexes)->contains('Key_name', $indexName);
    }
};

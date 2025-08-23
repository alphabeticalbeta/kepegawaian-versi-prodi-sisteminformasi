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
        // OPTIMASI: Tambah composite indexes untuk query yang sering digunakan
        
        // Index untuk tabel usulans
        Schema::table('usulans', function (Blueprint $table) {
            // Composite index untuk status dan jenis usulan
            if (!$this->indexExists('usulans', 'idx_usulans_status_jenis')) {
                $table->index(['status_usulan', 'jenis_usulan'], 'idx_usulans_status_jenis');
            }
            
            // Index untuk pegawai dan periode
            if (!$this->indexExists('usulans', 'idx_usulans_pegawai_periode')) {
                $table->index(['pegawai_id', 'periode_usulan_id'], 'idx_usulans_pegawai_periode');
            }
            
            // Index untuk created_at untuk sorting
            if (!$this->indexExists('usulans', 'idx_usulans_created_at')) {
                $table->index('created_at', 'idx_usulans_created_at');
            }
        });

        // Index untuk tabel pegawais
        Schema::table('pegawais', function (Blueprint $table) {
            // Composite index untuk jenis pegawai dan status
            if (!$this->indexExists('pegawais', 'idx_pegawais_jenis_status')) {
                $table->index(['jenis_pegawai', 'status_kepegawaian'], 'idx_pegawais_jenis_status');
            }
            
            // Index untuk nama dan NIP untuk pencarian
            if (!$this->indexExists('pegawais', 'idx_pegawais_nama_nip')) {
                $table->index(['nama_lengkap', 'nip'], 'idx_pegawais_nama_nip');
            }
            
            // Index untuk unit kerja
            if (!$this->indexExists('pegawais', 'idx_pegawais_unit_kerja')) {
                $table->index(['unit_kerja_id', 'unit_kerja_id'], 'idx_pegawais_unit_kerja');
            }
        });

        // Index untuk tabel usulan_penilai
        Schema::table('usulan_penilai', function (Blueprint $table) {
            // Composite index untuk usulan dan penilai
            if (!$this->indexExists('usulan_penilai', 'idx_usulan_penilai')) {
                $table->index(['usulan_id', 'penilai_id'], 'idx_usulan_penilai');
            }
            
            // Index untuk status penilaian
            if (!$this->indexExists('usulan_penilai', 'idx_usulan_status_penilaian')) {
                $table->index(['usulan_id', 'status_penilaian'], 'idx_usulan_status_penilaian');
            }
        });

        // Index untuk tabel usulan_logs
        Schema::table('usulan_logs', function (Blueprint $table) {
            // Index untuk usulan dan created_at
            if (!$this->indexExists('usulan_logs', 'idx_usulan_logs_usulan_created')) {
                $table->index(['usulan_id', 'created_at'], 'idx_usulan_logs_usulan_created');
            }
            
            // Index untuk dilakukan_oleh
            if (!$this->indexExists('usulan_logs', 'idx_usulan_logs_dilakukan_oleh')) {
                $table->index('dilakukan_oleh_id', 'idx_usulan_logs_dilakukan_oleh');
            }
        });

        // Index untuk tabel document_access_logs
        Schema::table('document_access_logs', function (Blueprint $table) {
            // Composite index untuk pegawai dan field
            if (!$this->indexExists('document_access_logs', 'idx_doc_access_pegawai_field')) {
                $table->index(['pegawai_id', 'document_field'], 'idx_doc_access_pegawai_field');
            }
            
            // Index untuk accessor dan waktu
            if (!$this->indexExists('document_access_logs', 'idx_doc_access_accessor_time')) {
                $table->index(['accessor_id', 'accessed_at'], 'idx_doc_access_accessor_time');
            }
        });

        // Index untuk tabel periode_usulans
        Schema::table('periode_usulans', function (Blueprint $table) {
            // Index untuk status dan tanggal
            if (!$this->indexExists('periode_usulans', 'idx_periode_status_dates')) {
                $table->index(['status', 'tanggal_mulai', 'tanggal_selesai'], 'idx_periode_status_dates');
            }
        });

        // Index untuk tabel jabatans
        Schema::table('jabatans', function (Blueprint $table) {
            // Composite index untuk jenis pegawai, jenis jabatan, dan hierarchy
            if (!$this->indexExists('jabatans', 'idx_jabatan_hierarchy')) {
                $table->index(['jenis_pegawai', 'jenis_jabatan', 'hierarchy_level'], 'idx_jabatan_hierarchy');
            }
        });

        // Index untuk tabel pangkats
        Schema::table('pangkats', function (Blueprint $table) {
            // Index untuk hierarchy level
            if (!$this->indexExists('pangkats', 'idx_pangkat_hierarchy')) {
                $table->index('hierarchy_level', 'idx_pangkat_hierarchy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes safely
        Schema::table('usulans', function (Blueprint $table) {
            if ($this->indexExists('usulans', 'idx_usulans_status_jenis')) {
                $table->dropIndex('idx_usulans_status_jenis');
            }
            if ($this->indexExists('usulans', 'idx_usulans_pegawai_periode')) {
                $table->dropIndex('idx_usulans_pegawai_periode');
            }
            if ($this->indexExists('usulans', 'idx_usulans_created_at')) {
                $table->dropIndex('idx_usulans_created_at');
            }
        });

        Schema::table('pegawais', function (Blueprint $table) {
            if ($this->indexExists('pegawais', 'idx_pegawais_jenis_status')) {
                $table->dropIndex('idx_pegawais_jenis_status');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_nama_nip')) {
                $table->dropIndex('idx_pegawais_nama_nip');
            }
            if ($this->indexExists('pegawais', 'idx_pegawais_unit_kerja')) {
                $table->dropIndex('idx_pegawais_unit_kerja');
            }
        });

        Schema::table('usulan_penilai', function (Blueprint $table) {
            if ($this->indexExists('usulan_penilai', 'idx_usulan_penilai')) {
                $table->dropIndex('idx_usulan_penilai');
            }
            if ($this->indexExists('usulan_penilai', 'idx_usulan_status_penilaian')) {
                $table->dropIndex('idx_usulan_status_penilaian');
            }
        });

        Schema::table('usulan_logs', function (Blueprint $table) {
            if ($this->indexExists('usulan_logs', 'idx_usulan_logs_usulan_created')) {
                $table->dropIndex('idx_usulan_logs_usulan_created');
            }
            if ($this->indexExists('usulan_logs', 'idx_usulan_logs_dilakukan_oleh')) {
                $table->dropIndex('idx_usulan_logs_dilakukan_oleh');
            }
        });

        Schema::table('document_access_logs', function (Blueprint $table) {
            if ($this->indexExists('document_access_logs', 'idx_doc_access_pegawai_field')) {
                $table->dropIndex('idx_doc_access_pegawai_field');
            }
            if ($this->indexExists('document_access_logs', 'idx_doc_access_accessor_time')) {
                $table->dropIndex('idx_doc_access_accessor_time');
            }
        });

        Schema::table('periode_usulans', function (Blueprint $table) {
            if ($this->indexExists('periode_usulans', 'idx_periode_status_dates')) {
                $table->dropIndex('idx_periode_status_dates');
            }
        });

        Schema::table('jabatans', function (Blueprint $table) {
            if ($this->indexExists('jabatans', 'idx_jabatan_hierarchy')) {
                $table->dropIndex('idx_jabatan_hierarchy');
            }
        });

        Schema::table('pangkats', function (Blueprint $table) {
            if ($this->indexExists('pangkats', 'idx_pangkat_hierarchy')) {
                $table->dropIndex('idx_pangkat_hierarchy');
            }
        });
    }

    /**
     * Check if index exists on table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};

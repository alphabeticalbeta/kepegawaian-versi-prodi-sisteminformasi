<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BackendUnivUsulan\Jabatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JabatanHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data jabatan untuk testing
        $this->seedJabatanData();
    }

    /** @test */
    public function dosen_fungsional_hierarchy_works_correctly()
    {
        // Test Dosen Fungsional Hierarchy
        $tenagaPengajar = Jabatan::where('jabatan', 'Tenaga Pengajar')->first();
        $asistenAhli = Jabatan::where('jabatan', 'Asisten Ahli')->first();
        $lektor = Jabatan::where('jabatan', 'Lektor')->first();
        $lektorKepala = Jabatan::where('jabatan', 'Lektor Kepala')->first();
        $guruBesar = Jabatan::where('jabatan', 'Guru Besar')->first();

        // Test hasHierarchy()
        $this->assertTrue($tenagaPengajar->hasHierarchy());
        $this->assertTrue($asistenAhli->hasHierarchy());

        // Test getNextLevel()
        $this->assertEquals($asistenAhli->id, $tenagaPengajar->getNextLevel()->id);
        $this->assertEquals($lektor->id, $asistenAhli->getNextLevel()->id);
        $this->assertEquals($lektorKepala->id, $lektor->getNextLevel()->id);
        $this->assertEquals($guruBesar->id, $lektorKepala->getNextLevel()->id);
        $this->assertNull($guruBesar->getNextLevel()); // Sudah tertinggi

        // Test getPreviousLevel()
        $this->assertNull($tenagaPengajar->getPreviousLevel()); // Sudah terendah
        $this->assertEquals($tenagaPengajar->id, $asistenAhli->getPreviousLevel()->id);
        $this->assertEquals($asistenAhli->id, $lektor->getPreviousLevel()->id);

        echo "✅ Dosen Fungsional Hierarchy Test: PASSED\n";
    }

    /** @test */
    public function tenaga_kependidikan_fungsional_tertentu_hierarchy_works()
    {
        // Test TK Fungsional Tertentu Sample Hierarchy
        $arsiparisPertama = Jabatan::where('jabatan', 'Arsiparis Ahli Pertama')->first();
        $arsiparisMuda = Jabatan::where('jabatan', 'Arsiparis Ahli Muda')->first();

        // Test hasHierarchy()
        $this->assertTrue($arsiparisPertama->hasHierarchy());
        $this->assertTrue($arsiparisMuda->hasHierarchy());

        // Test getNextLevel()
        $this->assertEquals($arsiparisMuda->id, $arsiparisPertama->getNextLevel()->id);
        $this->assertNull($arsiparisMuda->getNextLevel()); // Sample hanya 2 level

        // Test getPreviousLevel()
        $this->assertNull($arsiparisPertama->getPreviousLevel());
        $this->assertEquals($arsiparisPertama->id, $arsiparisMuda->getPreviousLevel()->id);

        echo "✅ TK Fungsional Tertentu Hierarchy Test: PASSED\n";
    }

    /** @test */
    public function non_hierarchy_jabatan_works_correctly()
    {
        // Test Non-Hierarchy Jabatan
        $dekan = Jabatan::where('jabatan', 'Dekan')->first();
        $stafAdmin = Jabatan::where('jabatan', 'Staf Administrasi')->first();
        $kepalaBagian = Jabatan::where('jabatan', 'Kepala Bagian')->first();

        // Test hasHierarchy()
        $this->assertFalse($dekan->hasHierarchy());
        $this->assertFalse($stafAdmin->hasHierarchy());
        $this->assertFalse($kepalaBagian->hasHierarchy());

        // Test getNextLevel() returns null
        $this->assertNull($dekan->getNextLevel());
        $this->assertNull($stafAdmin->getNextLevel());
        $this->assertNull($kepalaBagian->getNextLevel());

        echo "✅ Non-Hierarchy Jabatan Test: PASSED\n";
    }

    /** @test */
    public function dosen_promotion_targets_work_correctly()
    {
        // Test Dosen Promotion Logic
        $tenagaPengajar = Jabatan::where('jabatan', 'Tenaga Pengajar')->first();
        $asistenAhli = Jabatan::where('jabatan', 'Asisten Ahli')->first();
        $guruBesar = Jabatan::where('jabatan', 'Guru Besar')->first();
        $dekan = Jabatan::where('jabatan', 'Dekan')->first();

        // Tenaga Pengajar bisa ke Asisten Ahli
        $targets = $tenagaPengajar->getValidPromotionTargets();
        $this->assertCount(1, $targets);
        $this->assertEquals($asistenAhli->id, $targets->first()->id);

        // Asisten Ahli bisa ke Lektor
        $targets = $asistenAhli->getValidPromotionTargets();
        $this->assertCount(1, $targets);
        $this->assertEquals('Lektor', $targets->first()->jabatan);

        // Guru Besar tidak ada target (sudah tertinggi)
        $targets = $guruBesar->getValidPromotionTargets();
        $this->assertCount(0, $targets);

        // Dekan (Fungsi Tambahan) tidak ada usulan
        $targets = $dekan->getValidPromotionTargets();
        $this->assertCount(0, $targets);

        echo "✅ Dosen Promotion Targets Test: PASSED\n";
    }

    /** @test */
    public function tenaga_kependidikan_promotion_targets_work_correctly()
    {
        // Test TK Promotion Logic
        $stafAdmin = Jabatan::where('jabatan', 'Staf Administrasi')->first();
        $arsiparisPertama = Jabatan::where('jabatan', 'Arsiparis Ahli Pertama')->first();
        $koordinatorProgram = Jabatan::where('jabatan', 'Koordinator Program')->first();
        $kepalaBagian = Jabatan::where('jabatan', 'Kepala Bagian')->first();

        // Staf Admin (Fungsional Umum) bisa ke Fungsional Tertentu & Tugas Tambahan
        $targets = $stafAdmin->getValidPromotionTargets();
        $this->assertGreaterThan(0, $targets->count());

        // Harus ada Arsiparis dan Koordinator Program
        $targetNames = $targets->pluck('jabatan')->toArray();
        $this->assertContains('Arsiparis Ahli Pertama', $targetNames);
        $this->assertContains('Koordinator Program', $targetNames);
        // Tidak boleh ada Kepala Bagian (Struktural)
        $this->assertNotContains('Kepala Bagian', $targetNames);

        // Arsiparis (Fungsional Tertentu) bisa naik level + lintas jenis
        $targets = $arsiparisPertama->getValidPromotionTargets();
        $targetNames = $targets->pluck('jabatan')->toArray();
        $this->assertContains('Arsiparis Ahli Muda', $targetNames); // Next level
        $this->assertContains('Staf Administrasi', $targetNames); // Cross movement

        // Kepala Bagian (Struktural) tidak ada usulan
        $targets = $kepalaBagian->getValidPromotionTargets();
        $this->assertCount(0, $targets);

        echo "✅ TK Promotion Targets Test: PASSED\n";
    }

    /** @test */
    public function can_promote_to_validation_works()
    {
        $tenagaPengajar = Jabatan::where('jabatan', 'Tenaga Pengajar')->first();
        $asistenAhli = Jabatan::where('jabatan', 'Asisten Ahli')->first();
        $lektor = Jabatan::where('jabatan', 'Lektor')->first();
        $dekan = Jabatan::where('jabatan', 'Dekan')->first();

        // Tenaga Pengajar bisa ke Asisten Ahli
        $this->assertTrue($tenagaPengajar->canPromoteTo($asistenAhli));

        // Tenaga Pengajar TIDAK bisa lompat ke Lektor
        $this->assertFalse($tenagaPengajar->canPromoteTo($lektor));

        // Tenaga Pengajar TIDAK bisa ke Dekan (beda jenis jabatan)
        $this->assertFalse($tenagaPengajar->canPromoteTo($dekan));

        echo "✅ Promotion Validation Test: PASSED\n";
    }

    /** @test */
    public function eligible_for_usulan_works_correctly()
    {
        $dosen = Jabatan::where('jabatan', 'Asisten Ahli')->first();
        $tkFungsional = Jabatan::where('jabatan', 'Staf Administrasi')->first();
        $tkStruktural = Jabatan::where('jabatan', 'Kepala Bagian')->first();

        // Dosen dan TK Fungsional bisa usulan
        $this->assertTrue($dosen->isEligibleForUsulan());
        $this->assertTrue($tkFungsional->isEligibleForUsulan());

        // TK Struktural tidak bisa usulan
        $this->assertFalse($tkStruktural->isEligibleForUsulan());

        // Test scope
        $eligibleJabatan = Jabatan::eligibleForUsulan()->get();
        $structuralJabatan = $eligibleJabatan->where('jenis_jabatan', 'Tenaga Kependidikan Struktural');
        $this->assertCount(0, $structuralJabatan);

        echo "✅ Eligible for Usulan Test: PASSED\n";
    }

    private function seedJabatanData()
    {
        $jabatans = [
            // Dosen Fungsional
            ['jabatan' => 'Tenaga Pengajar', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional', 'hierarchy_level' => 1],
            ['jabatan' => 'Asisten Ahli', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional', 'hierarchy_level' => 2],
            ['jabatan' => 'Lektor', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional', 'hierarchy_level' => 3],
            ['jabatan' => 'Lektor Kepala', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional', 'hierarchy_level' => 4],
            ['jabatan' => 'Guru Besar', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional', 'hierarchy_level' => 5],

                    // Dosen dengan Tugas Tambahan
        ['jabatan' => 'Dekan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen dengan Tugas Tambahan', 'hierarchy_level' => null],

            // TK Fungsional Tertentu
            ['jabatan' => 'Arsiparis Ahli Pertama', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu', 'hierarchy_level' => 1],
            ['jabatan' => 'Arsiparis Ahli Muda', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu', 'hierarchy_level' => 2],

            // TK Fungsional Umum
            ['jabatan' => 'Staf Administrasi', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum', 'hierarchy_level' => null],

            // TK Struktural
            ['jabatan' => 'Kepala Bagian', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Struktural', 'hierarchy_level' => null],

            // TK Tugas Tambahan
            ['jabatan' => 'Koordinator Program', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan', 'hierarchy_level' => null],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::create($jabatan);
        }
    }
}

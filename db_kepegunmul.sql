-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.6 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_kepegunmul.document_access_logs
DROP TABLE IF EXISTS `document_access_logs`;
CREATE TABLE IF NOT EXISTS `document_access_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pegawai_id` bigint unsigned NOT NULL,
  `accessor_id` bigint unsigned NOT NULL,
  `document_field` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `accessed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_access_logs_pegawai_id_document_field_index` (`pegawai_id`,`document_field`),
  KEY `document_access_logs_accessor_id_accessed_at_index` (`accessor_id`,`accessed_at`),
  KEY `idx_doc_access_pegawai_field` (`pegawai_id`,`document_field`),
  KEY `idx_doc_access_accessor_time` (`accessor_id`,`accessed_at`),
  CONSTRAINT `document_access_logs_accessor_id_foreign` FOREIGN KEY (`accessor_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_access_logs_pegawai_id_foreign` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.jabatans
DROP TABLE IF EXISTS `jabatans`;
CREATE TABLE IF NOT EXISTS `jabatans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_pegawai` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hierarchy_level` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jabatans_jenis_pegawai_jenis_jabatan_hierarchy_level_index` (`jenis_pegawai`,`jenis_jabatan`,`hierarchy_level`),
  KEY `idx_jabatan_hierarchy` (`jenis_pegawai`,`jenis_jabatan`,`hierarchy_level`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.pangkats
DROP TABLE IF EXISTS `pangkats`;
CREATE TABLE IF NOT EXISTS `pangkats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pangkat` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hierarchy_level` int DEFAULT NULL,
  `status_pangkat` enum('PNS','PPPK','Non-ASN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PNS' COMMENT 'Status pangkat: PNS, PPPK, atau Non-ASN',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pangkats_pangkat_unique` (`pangkat`),
  KEY `pangkats_hierarchy_level_index` (`hierarchy_level`),
  KEY `idx_pangkat_hierarchy` (`hierarchy_level`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.pegawais
DROP TABLE IF EXISTS `pegawais`;
CREATE TABLE IF NOT EXISTS `pegawais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned DEFAULT NULL,
  `pangkat_terakhir_id` bigint unsigned NOT NULL,
  `jabatan_terakhir_id` bigint unsigned NOT NULL,
  `unit_kerja_id` bigint unsigned NOT NULL,
  `jenis_pegawai` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jenis jabatan sesuai konfigurasi jabatan',
  `status_kepegawaian` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nuptk` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_depan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_lengkap` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gelar_belakang` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_kartu_pegawai` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_handphone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `sk_cpns` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pns` date DEFAULT NULL,
  `sk_pns` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pangkat` date DEFAULT NULL,
  `sk_pangkat_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_jabatan` date DEFAULT NULL,
  `sk_jabatan_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_universitas_sekolah` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_prodi_jurusan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ijazah_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transkrip_nilai_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_penyetaraan_ijazah` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disertasi_thesis_terakhir` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_kuliah_diampu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ranting_ilmu_kepakaran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url_profil_sinta` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_pertama` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_pertama` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_kedua` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_kedua` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_konversi` double DEFAULT NULL,
  `pak_konversi` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawais_nip_unique` (`nip`),
  UNIQUE KEY `pegawais_email_unique` (`email`),
  UNIQUE KEY `pegawais_username_unique` (`username`),
  KEY `pegawais_unit_kerja_id_foreign` (`unit_kerja_id`),
  KEY `idx_pegawais_jenis_status` (`jenis_pegawai`,`status_kepegawaian`),
  KEY `idx_pegawais_nama_nip` (`nama_lengkap`,`nip`),
  KEY `idx_pegawais_unit_kerja` (`unit_kerja_id`,`unit_kerja_id`),
  KEY `idx_pegawais_nip` (`nip`),
  KEY `idx_pegawais_id` (`id`),
  KEY `idx_pegawais_unit_kerja_terakhir` (`unit_kerja_id`),
  KEY `idx_pegawais_pangkat_terakhir` (`pangkat_terakhir_id`),
  KEY `idx_pegawais_jabatan_terakhir` (`jabatan_terakhir_id`),
  CONSTRAINT `pegawais_jabatan_terakhir_id_foreign` FOREIGN KEY (`jabatan_terakhir_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `pegawais_pangkat_terakhir_id_foreign` FOREIGN KEY (`pangkat_terakhir_id`) REFERENCES `pangkats` (`id`),
  CONSTRAINT `pegawais_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pegawais_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `sub_sub_unit_kerjas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.penilais
DROP TABLE IF EXISTS `penilais`;
CREATE TABLE IF NOT EXISTS `penilais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bidang_keahlian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penilais_nip_unique` (`nip`),
  UNIQUE KEY `penilais_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.periode_usulans
DROP TABLE IF EXISTS `periode_usulans`;
CREATE TABLE IF NOT EXISTS `periode_usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_usulan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kepegawaian` json DEFAULT NULL COMMENT 'Status kepegawaian yang diizinkan untuk mengakses periode ini',
  `tahun_periode` year NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `senat_min_setuju` int unsigned NOT NULL DEFAULT '1',
  `tanggal_mulai_perbaikan` date DEFAULT NULL,
  `tanggal_selesai_perbaikan` date DEFAULT NULL,
  `status` enum('Buka','Tutup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tutup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_periode_status_dates` (`status`,`tanggal_mulai`,`tanggal_selesai`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.sub_sub_unit_kerjas
DROP TABLE IF EXISTS `sub_sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` (`sub_unit_kerja_id`),
  CONSTRAINT `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` FOREIGN KEY (`sub_unit_kerja_id`) REFERENCES `sub_unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.sub_unit_kerjas
DROP TABLE IF EXISTS `sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sub_unit_kerjas_unit` (`unit_kerja_id`),
  CONSTRAINT `sub_unit_kerjas_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.unit_kerjas
DROP TABLE IF EXISTS `unit_kerjas`;
CREATE TABLE IF NOT EXISTS `unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_kerjas_nama_unique` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulans
DROP TABLE IF EXISTS `usulans`;
CREATE TABLE IF NOT EXISTS `usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status_kepegawaian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pegawai_id` bigint unsigned NOT NULL,
  `periode_usulan_id` bigint unsigned NOT NULL,
  `jenis_usulan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_lama_id` bigint unsigned DEFAULT NULL,
  `jabatan_tujuan_id` bigint unsigned DEFAULT NULL,
  `status_usulan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `data_usulan` json DEFAULT NULL,
  `validasi_data` json DEFAULT NULL,
  `catatan_verifikator` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulans_periode_usulan_id_foreign` (`periode_usulan_id`),
  KEY `usulans_jabatan_lama_id_foreign` (`jabatan_lama_id`),
  KEY `usulans_jabatan_tujuan_id_foreign` (`jabatan_tujuan_id`),
  KEY `usulans_status_usulan_index` (`status_usulan`),
  KEY `usulans_jenis_usulan_index` (`jenis_usulan`),
  KEY `idx_usulans_status_jenis` (`status_usulan`,`jenis_usulan`),
  KEY `idx_usulans_pegawai_periode` (`pegawai_id`,`periode_usulan_id`),
  KEY `idx_usulans_created_at` (`created_at`),
  KEY `idx_usulans_id` (`id`),
  KEY `idx_usulans_status_jenis_created` (`status_usulan`,`jenis_usulan`,`created_at`),
  CONSTRAINT `usulans_jabatan_lama_id_foreign` FOREIGN KEY (`jabatan_lama_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `usulans_jabatan_tujuan_id_foreign` FOREIGN KEY (`jabatan_tujuan_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `usulans_pegawai_id_foreign` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulans_periode_usulan_id_foreign` FOREIGN KEY (`periode_usulan_id`) REFERENCES `periode_usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulan_dokumens
DROP TABLE IF EXISTS `usulan_dokumens`;
CREATE TABLE IF NOT EXISTS `usulan_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `diupload_oleh_id` bigint unsigned NOT NULL,
  `nama_dokumen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_dokumens_usulan_id_created_at_index` (`usulan_id`,`created_at`),
  KEY `usulan_dokumens_diupload_oleh_id_index` (`diupload_oleh_id`),
  CONSTRAINT `usulan_dokumens_diupload_oleh_id_foreign` FOREIGN KEY (`diupload_oleh_id`) REFERENCES `pegawais` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `usulan_dokumens_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulan_jabatan_senat
DROP TABLE IF EXISTS `usulan_jabatan_senat`;
CREATE TABLE IF NOT EXISTS `usulan_jabatan_senat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `anggota_senat_id` bigint unsigned NOT NULL,
  `keputusan` enum('direkomendasikan','belum_direkomendasikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `diputuskan_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usulan_jabatan_senat_usulan_id_anggota_senat_id_unique` (`usulan_id`,`anggota_senat_id`),
  KEY `usulan_jabatan_senat_anggota_senat_id_foreign` (`anggota_senat_id`),
  CONSTRAINT `usulan_jabatan_senat_anggota_senat_id_foreign` FOREIGN KEY (`anggota_senat_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_jabatan_senat_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulan_logs
DROP TABLE IF EXISTS `usulan_logs`;
CREATE TABLE IF NOT EXISTS `usulan_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `status_sebelumnya` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dilakukan_oleh_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usulan_logs_usulan_created` (`usulan_id`,`created_at`),
  KEY `idx_usulan_logs_dilakukan_oleh` (`dilakukan_oleh_id`),
  CONSTRAINT `usulan_logs_dilakukan_oleh_id_foreign` FOREIGN KEY (`dilakukan_oleh_id`) REFERENCES `pegawais` (`id`),
  CONSTRAINT `usulan_logs_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulan_penilai
DROP TABLE IF EXISTS `usulan_penilai`;
CREATE TABLE IF NOT EXISTS `usulan_penilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `penilai_id` bigint unsigned NOT NULL,
  `status_penilaian` enum('Belum Dinilai','Sesuai','Perlu Perbaikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dinilai',
  `catatan_penilaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_penilai_penilai_id_foreign` (`penilai_id`),
  KEY `idx_usulan_penilai` (`usulan_id`,`penilai_id`),
  KEY `idx_usulan_status_penilaian` (`usulan_id`,`status_penilaian`),
  CONSTRAINT `usulan_penilai_penilai_id_foreign` FOREIGN KEY (`penilai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_penilai_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_kepegunmul.usulan_validasi
DROP TABLE IF EXISTS `usulan_validasi`;
CREATE TABLE IF NOT EXISTS `usulan_validasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usulan_validasi_usulan_id_role_unique` (`usulan_id`,`role`),
  CONSTRAINT `usulan_validasi_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

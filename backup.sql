-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.4.6 - MySQL Community Server - GPL
-- OS Server:                    Linux
-- HeidiSQL Versi:               12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- membuang struktur untuk table db_kepegunmul.document_access_logs
DROP TABLE IF EXISTS `document_access_logs`;
CREATE TABLE IF NOT EXISTS `document_access_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pegawai_id` bigint unsigned NOT NULL,
  `accessor_id` bigint unsigned NOT NULL,
  `document_field` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.document_access_logs: ~40 rows (lebih kurang)
REPLACE INTO `document_access_logs` (`id`, `pegawai_id`, `accessor_id`, `document_field`, `ip_address`, `user_agent`, `accessed_at`, `created_at`, `updated_at`) VALUES
	(1, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-17 23:40:45', '2025-08-17 23:40:45', '2025-08-17 23:40:45'),
	(2, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-17 23:41:23', '2025-08-17 23:41:23', '2025-08-17 23:41:23'),
	(3, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-17 23:46:06', '2025-08-17 23:46:06', '2025-08-17 23:46:06'),
	(4, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:00:15', '2025-08-18 00:00:15', '2025-08-18 00:00:15'),
	(5, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:07:03', '2025-08-18 00:07:03', '2025-08-18 00:07:03'),
	(6, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:10:19', '2025-08-18 00:10:19', '2025-08-18 00:10:19'),
	(7, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:13:45', '2025-08-18 00:13:45', '2025-08-18 00:13:45'),
	(8, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:41:35', '2025-08-18 00:41:35', '2025-08-18 00:41:35'),
	(9, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:41:35', '2025-08-18 00:41:35', '2025-08-18 00:41:35'),
	(10, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 00:58:49', '2025-08-18 00:58:49', '2025-08-18 00:58:49'),
	(11, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:07:46', '2025-08-18 01:07:46', '2025-08-18 01:07:46'),
	(12, 10, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:07:56', '2025-08-18 01:07:56', '2025-08-18 01:07:56'),
	(13, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:22:58', '2025-08-18 01:22:58', '2025-08-18 01:22:58'),
	(14, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:37:01', '2025-08-18 01:37:01', '2025-08-18 01:37:01'),
	(15, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:51:25', '2025-08-18 01:51:25', '2025-08-18 01:51:25'),
	(16, 10, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 01:56:11', '2025-08-18 01:56:11', '2025-08-18 01:56:11'),
	(17, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 02:02:22', '2025-08-18 02:02:22', '2025-08-18 02:02:22'),
	(18, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 02:17:35', '2025-08-18 02:17:35', '2025-08-18 02:17:35'),
	(19, 10, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 02:24:36', '2025-08-18 02:24:36', '2025-08-18 02:24:36'),
	(20, 1, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 02:35:32', '2025-08-18 02:35:32', '2025-08-18 02:35:32'),
	(21, 10, 1, 'foto', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 02:41:45', '2025-08-18 02:41:45', '2025-08-18 02:41:45'),
	(22, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 05:13:12', '2025-08-18 05:13:12', '2025-08-18 05:13:12'),
	(23, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 05:46:34', '2025-08-18 05:46:34', '2025-08-18 05:46:34'),
	(24, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 05:46:45', '2025-08-18 05:46:45', '2025-08-18 05:46:45'),
	(25, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 06:25:15', '2025-08-18 06:25:15', '2025-08-18 06:25:15'),
	(26, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 06:53:15', '2025-08-18 06:53:15', '2025-08-18 06:53:15'),
	(27, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 07:58:19', '2025-08-18 07:58:19', '2025-08-18 07:58:19'),
	(28, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 08:49:32', '2025-08-18 08:49:32', '2025-08-18 08:49:32'),
	(29, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 08:52:35', '2025-08-18 08:52:35', '2025-08-18 08:52:35'),
	(30, 1, 1, 'ijazah_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:24:22', '2025-08-18 09:24:22', '2025-08-18 09:24:22'),
	(31, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:50:03', '2025-08-18 09:50:03', '2025-08-18 09:50:03'),
	(32, 1, 1, 'ijazah_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:57:32', '2025-08-18 09:57:32', '2025-08-18 09:57:32'),
	(33, 1, 1, 'ijazah_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:57:35', '2025-08-18 09:57:35', '2025-08-18 09:57:35'),
	(34, 1, 1, 'ijazah_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:57:36', '2025-08-18 09:57:36', '2025-08-18 09:57:36'),
	(35, 1, 1, 'ijazah_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 09:57:36', '2025-08-18 09:57:36', '2025-08-18 09:57:36'),
	(36, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 10:21:13', '2025-08-18 10:21:13', '2025-08-18 10:21:13'),
	(37, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 11:03:04', '2025-08-18 11:03:04', '2025-08-18 11:03:04'),
	(38, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 12:22:14', '2025-08-18 12:22:14', '2025-08-18 12:22:14'),
	(39, 1, 1, 'disertasi_thesis_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:24:22', '2025-08-18 13:24:22', '2025-08-18 13:24:22'),
	(40, 1, 1, 'disertasi_thesis_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:24:23', '2025-08-18 13:24:23', '2025-08-18 13:24:23'),
	(41, 1, 1, 'disertasi_thesis_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:24:23', '2025-08-18 13:24:23', '2025-08-18 13:24:23'),
	(42, 1, 1, 'disertasi_thesis_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:24:23', '2025-08-18 13:24:23', '2025-08-18 13:24:23'),
	(43, 1, 1, 'disertasi_thesis_terakhir', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:24:24', '2025-08-18 13:24:24', '2025-08-18 13:24:24'),
	(44, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 13:43:02', '2025-08-18 13:43:02', '2025-08-18 13:43:02'),
	(45, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 21:35:18', '2025-08-18 21:35:18', '2025-08-18 21:35:18'),
	(46, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 23:39:26', '2025-08-18 23:39:26', '2025-08-18 23:39:26'),
	(47, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-18 23:48:14', '2025-08-18 23:48:14', '2025-08-18 23:48:14'),
	(48, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 01:57:17', '2025-08-19 01:57:17', '2025-08-19 01:57:17'),
	(49, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 12:05:46', '2025-08-19 12:05:46', '2025-08-19 12:05:46'),
	(50, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 15:48:27', '2025-08-19 15:48:27', '2025-08-19 15:48:27'),
	(51, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 21:55:13', '2025-08-19 21:55:13', '2025-08-19 21:55:13'),
	(52, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 02:10:26', '2025-08-20 02:10:26', '2025-08-20 02:10:26'),
	(53, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 02:38:25', '2025-08-20 02:38:25', '2025-08-20 02:38:25'),
	(54, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 09:47:57', '2025-08-20 09:47:57', '2025-08-20 09:47:57'),
	(55, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 21:56:03', '2025-08-20 21:56:03', '2025-08-20 21:56:03'),
	(56, 10, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 22:24:25', '2025-08-20 22:24:25', '2025-08-20 22:24:25'),
	(57, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 23:05:53', '2025-08-20 23:05:53', '2025-08-20 23:05:53'),
	(58, 1, 1, 'foto', '172.19.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 23:06:05', '2025-08-20 23:06:05', '2025-08-20 23:06:05');

-- membuang struktur untuk table db_kepegunmul.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.failed_jobs: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.jabatans
DROP TABLE IF EXISTS `jabatans`;
CREATE TABLE IF NOT EXISTS `jabatans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_pegawai` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hierarchy_level` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jabatans_jenis_pegawai_jenis_jabatan_hierarchy_level_index` (`jenis_pegawai`,`jenis_jabatan`,`hierarchy_level`),
  KEY `idx_jabatan_hierarchy` (`jenis_pegawai`,`jenis_jabatan`,`hierarchy_level`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.jabatans: ~39 rows (lebih kurang)
REPLACE INTO `jabatans` (`id`, `jenis_pegawai`, `jenis_jabatan`, `jabatan`, `hierarchy_level`, `created_at`, `updated_at`) VALUES
	(1, 'Dosen', 'Dosen Fungsional', 'Tenaga Pengajar', 1, '2025-08-17 23:13:43', '2025-08-17 23:17:11'),
	(2, 'Dosen', 'Dosen Fungsional', 'Asisten Ahli', 2, '2025-08-17 23:13:43', '2025-08-17 23:17:11'),
	(3, 'Dosen', 'Dosen Fungsional', 'Lektor', 3, '2025-08-17 23:13:43', '2025-08-17 23:17:11'),
	(4, 'Dosen', 'Dosen Fungsional', 'Lektor Kepala', 4, '2025-08-17 23:13:43', '2025-08-17 23:17:11'),
	(5, 'Dosen', 'Dosen Fungsional', 'Guru Besar', 5, '2025-08-17 23:13:43', '2025-08-17 23:17:11'),
	(6, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Pelaksana', NULL, '2025-08-17 23:13:43', '2025-08-17 23:13:43'),
	(7, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pustakawan', NULL, '2025-08-17 23:13:43', '2025-08-17 23:13:43'),
	(8, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pranata Laboratorium', NULL, '2025-08-17 23:13:43', '2025-08-17 23:13:43'),
	(9, 'Tenaga Kependidikan', 'Tenaga Kependidikan Struktural', 'Kepala Bagian', NULL, '2025-08-17 23:13:43', '2025-08-17 23:13:43'),
	(10, 'Tenaga Kependidikan', 'Tenaga Kependidikan Struktural', 'Kepala Sub Bagian', NULL, '2025-08-17 23:13:43', '2025-08-17 23:13:43'),
	(11, 'Dosen', 'Dosen dengan Tugas Tambahan', 'Ketua Jurusan', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(12, 'Dosen', 'Dosen dengan Tugas Tambahan', 'Wakil Dekan', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(13, 'Dosen', 'Dosen dengan Tugas Tambahan', 'Dekan', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(14, 'Dosen', 'Dosen dengan Tugas Tambahan', 'Wakil Rektor', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(15, 'Dosen', 'Dosen dengan Tugas Tambahan', 'Rektor', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(16, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Arsiparis Ahli Pertama', 1, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(17, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Arsiparis Ahli Muda', 2, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(18, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Arsiparis Ahli Madya', 3, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(19, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pustakawan Ahli Pertama', 1, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(20, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pustakawan Ahli Muda', 2, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(21, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pustakawan Ahli Madya', 3, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(22, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pranata Laboratorium Pendidikan Ahli Pertama', 1, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(23, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pranata Laboratorium Pendidikan Ahli Muda', 2, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(24, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Tertentu', 'Pranata Laboratorium Pendidikan Ahli Madya', 3, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(25, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Administrasi', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(26, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Koordinator Administrasi', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(27, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Keuangan', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(28, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Kepegawaian', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(29, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Akademik', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(30, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Kemahasiswaan', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(31, 'Tenaga Kependidikan', 'Tenaga Kependidikan Fungsional Umum', 'Staf Umum', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(32, 'Tenaga Kependidikan', 'Tenaga Kependidikan Struktural', 'Kepala Biro', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(33, 'Tenaga Kependidikan', 'Tenaga Kependidikan Struktural', 'Kepala Sub Direktorat', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(34, 'Tenaga Kependidikan', 'Tenaga Kependidikan Struktural', 'Kepala Direktorat', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(35, 'Tenaga Kependidikan', 'Tenaga Kependidikan Tugas Tambahan', 'Koordinator Program', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(36, 'Tenaga Kependidikan', 'Tenaga Kependidikan Tugas Tambahan', 'Sekretaris Fakultas', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(37, 'Tenaga Kependidikan', 'Tenaga Kependidikan Tugas Tambahan', 'Wakil Sekretaris Fakultas', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(38, 'Tenaga Kependidikan', 'Tenaga Kependidikan Tugas Tambahan', 'Koordinator Bidang', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(39, 'Tenaga Kependidikan', 'Tenaga Kependidikan Tugas Tambahan', 'Koordinator Unit', NULL, '2025-08-17 23:17:11', '2025-08-17 23:17:11');

-- membuang struktur untuk table db_kepegunmul.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.jobs: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.job_batches: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.migrations: ~32 rows (lebih kurang)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2025_07_24_222253_create_unit_kerjas_table', 1),
	(2, '2025_07_25_012556_create_sub_unit_kerjas_table', 1),
	(3, '2025_07_25_100346_create_sub_sub_unit_kerjas_table', 1),
	(4, '2025_07_25_231137_create_pangkats_table', 1),
	(5, '2025_07_26_001145_create_jabatans_table', 1),
	(6, '2025_07_28_231905_create_pegawais_table', 1),
	(7, '2025_08_02_221132_create_periode_usulans_table', 1),
	(8, '2025_08_02_221252_create_usulans_table', 1),
	(9, '2025_08_02_221739_create_usulan_penilai_jabatan_table', 1),
	(10, '2025_08_02_221833_create_usulan_logs_table', 1),
	(11, '2025_08_05_025424_create_usulan_dokumens_table', 1),
	(12, '2025_08_05_150128_create_permission_tables', 1),
	(13, '2025_08_05_233150_add_jenis_usulan_to_periode_usulans_table', 1),
	(14, '2025_08_05_233459_add_perbaikan_dates_to_periode_usulans_table', 1),
	(15, '2025_08_07_084654_create_jobs_table', 1),
	(16, '2025_08_08_111838_add_unit_kerja_id_to_pegawais_table', 1),
	(17, '2025_08_08_144207_add_validasi_data_to_usulans_table', 1),
	(18, '2025_08_09_012636_add_hierarchy_level_to_jabatans_table', 1),
	(19, '2025_08_09_020106_add_hierarchy_level_to_pangkats_table', 1),
	(20, '2025_08_09_050801_create_document_access_logs_table', 1),
	(21, '2025_08_09_093407_add_status_pangkat_to_pangkats_table', 1),
	(22, '2025_08_09_105847_populate_jabatan_data_for_usulan', 1),
	(23, '2025_08_10_000000_optimize_database_indexes', 1),
	(24, '2025_08_11_144745_add_senat_min_setuju_to_periode_usulans_table', 1),
	(25, '2025_08_11_144829_create_usulan_jabatan_senat_table', 1),
	(26, '2025_08_12_021125_create_usulan_validasi_table', 1),
	(27, '2025_08_12_064546_create_sessions_table', 1),
	(28, '2025_08_15_033417_add_additional_performance_indexes', 1),
	(29, '2025_08_15_233151_add_username_field_to_pegawais_table', 1),
	(30, '2025_08_16_063809_add_jenis_jabatan_to_pegawais_table', 1),
	(31, '2025_08_16_082959_add_pendidikan_s2_fields_to_pegawais_table', 1),
	(32, '2025_08_17_155915_add_status_kepegawaian_to_periode_usulans_table', 1),
	(33, '2025_08_17_231515_add_status_kepegawaian_to_usulans_table', 2),
	(34, '2025_08_18_085821_add_nama_prodi_jurusan_to_pegawais_table', 3),
	(35, '2025_01_20_000000_create_penilais_table', 4);

-- membuang struktur untuk table db_kepegunmul.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.model_has_permissions: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.model_has_roles: ~21 rows (lebih kurang)
REPLACE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(2, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(3, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(3, 'App\\Models\\BackendUnivUsulan\\Pegawai', 9),
	(4, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(4, 'App\\Models\\BackendUnivUsulan\\Pegawai', 15),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 2),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 4),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 13),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 14),
	(6, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(6, 'App\\Models\\BackendUnivUsulan\\Pegawai', 2),
	(6, 'App\\Models\\BackendUnivUsulan\\Pegawai', 4),
	(6, 'App\\Models\\BackendUnivUsulan\\Pegawai', 11),
	(6, 'App\\Models\\BackendUnivUsulan\\Pegawai', 12),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 2),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 4),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 5),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 6),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 7),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 8),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 9),
	(7, 'App\\Models\\BackendUnivUsulan\\Pegawai', 10);

-- membuang struktur untuk table db_kepegunmul.pangkats
DROP TABLE IF EXISTS `pangkats`;
CREATE TABLE IF NOT EXISTS `pangkats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pangkat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hierarchy_level` int DEFAULT NULL,
  `status_pangkat` enum('PNS','PPPK','Non-ASN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PNS' COMMENT 'Status pangkat: PNS, PPPK, atau Non-ASN',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pangkats_pangkat_unique` (`pangkat`),
  KEY `pangkats_hierarchy_level_index` (`hierarchy_level`),
  KEY `idx_pangkat_hierarchy` (`hierarchy_level`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.pangkats: ~24 rows (lebih kurang)
REPLACE INTO `pangkats` (`id`, `pangkat`, `hierarchy_level`, `status_pangkat`, `created_at`, `updated_at`) VALUES
	(1, 'Juru Muda (I/a)', 1, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(2, 'Juru Muda Tingkat I (I/b)', 2, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(3, 'Juru (I/c)', 3, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(4, 'Juru Tingkat I (I/d)', 4, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(5, 'Pengatur Muda (II/a)', 5, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(6, 'Pengatur Muda Tingkat I (II/b)', 6, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(7, 'Pengatur (II/c)', 7, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(8, 'Pengatur Tingkat I (II/d)', 8, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(9, 'Penata Muda (III/a)', 9, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(10, 'Penata Muda Tingkat I (III/b)', 10, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(11, 'Penata (III/c)', 11, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(12, 'Penata Tingkat I (III/d)', 12, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(13, 'Pembina (IV/a)', 13, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(14, 'Pembina Tingkat I (IV/b)', 14, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(15, 'Pembina Utama Muda (IV/c)', 15, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(16, 'Pembina Utama Madya (IV/d)', 16, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(17, 'Pembina Utama (IV/e)', 17, 'PNS', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(18, 'PPPK Ahli Pertama (III/a)', 18, 'PPPK', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(19, 'PPPK Ahli Muda (III/b)', 19, 'PPPK', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(20, 'PPPK Ahli Madya (III/c)', 20, 'PPPK', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(21, 'PPPK Ahli Utama (III/d)', 21, 'PPPK', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(22, 'Non-PNS', NULL, 'Non-ASN', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(23, 'Kontrak', NULL, 'Non-ASN', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(24, 'Honorer', NULL, 'Non-ASN', '2025-08-17 23:17:11', '2025-08-17 23:17:11');

-- membuang struktur untuk table db_kepegunmul.pegawais
DROP TABLE IF EXISTS `pegawais`;
CREATE TABLE IF NOT EXISTS `pegawais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned DEFAULT NULL,
  `pangkat_terakhir_id` bigint unsigned NOT NULL,
  `jabatan_terakhir_id` bigint unsigned NOT NULL,
  `unit_kerja_terakhir_id` bigint unsigned NOT NULL,
  `jenis_pegawai` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jenis jabatan sesuai konfigurasi jabatan',
  `status_kepegawaian` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nuptk` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_depan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_lengkap` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gelar_belakang` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_kartu_pegawai` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_handphone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `sk_cpns` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pns` date DEFAULT NULL,
  `sk_pns` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pangkat` date DEFAULT NULL,
  `sk_pangkat_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_jabatan` date DEFAULT NULL,
  `sk_jabatan_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_universitas_sekolah` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_prodi_jurusan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ijazah_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transkrip_nilai_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_penyetaraan_ijazah` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disertasi_thesis_terakhir` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_kuliah_diampu` text COLLATE utf8mb4_unicode_ci,
  `ranting_ilmu_kepakaran` text COLLATE utf8mb4_unicode_ci,
  `url_profil_sinta` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_pertama` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_pertama` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_kedua` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_kedua` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_konversi` double DEFAULT NULL,
  `pak_konversi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawais_nip_unique` (`nip`),
  UNIQUE KEY `pegawais_email_unique` (`email`),
  UNIQUE KEY `pegawais_username_unique` (`username`),
  KEY `pegawais_unit_kerja_id_foreign` (`unit_kerja_id`),
  KEY `idx_pegawais_jenis_status` (`jenis_pegawai`,`status_kepegawaian`),
  KEY `idx_pegawais_nama_nip` (`nama_lengkap`,`nip`),
  KEY `idx_pegawais_unit_kerja` (`unit_kerja_terakhir_id`,`unit_kerja_id`),
  KEY `idx_pegawais_nip` (`nip`),
  KEY `idx_pegawais_id` (`id`),
  KEY `idx_pegawais_unit_kerja_terakhir` (`unit_kerja_terakhir_id`),
  KEY `idx_pegawais_pangkat_terakhir` (`pangkat_terakhir_id`),
  KEY `idx_pegawais_jabatan_terakhir` (`jabatan_terakhir_id`),
  CONSTRAINT `pegawais_jabatan_terakhir_id_foreign` FOREIGN KEY (`jabatan_terakhir_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `pegawais_pangkat_terakhir_id_foreign` FOREIGN KEY (`pangkat_terakhir_id`) REFERENCES `pangkats` (`id`),
  CONSTRAINT `pegawais_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pegawais_unit_kerja_terakhir_id_foreign` FOREIGN KEY (`unit_kerja_terakhir_id`) REFERENCES `sub_sub_unit_kerjas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.pegawais: ~15 rows (lebih kurang)
REPLACE INTO `pegawais` (`id`, `unit_kerja_id`, `pangkat_terakhir_id`, `jabatan_terakhir_id`, `unit_kerja_terakhir_id`, `jenis_pegawai`, `jenis_jabatan`, `status_kepegawaian`, `nip`, `nuptk`, `gelar_depan`, `nama_lengkap`, `gelar_belakang`, `email`, `username`, `password`, `nomor_kartu_pegawai`, `foto`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `nomor_handphone`, `tmt_cpns`, `sk_cpns`, `tmt_pns`, `sk_pns`, `tmt_pangkat`, `sk_pangkat_terakhir`, `tmt_jabatan`, `sk_jabatan_terakhir`, `pendidikan_terakhir`, `nama_universitas_sekolah`, `nama_prodi_jurusan`, `ijazah_terakhir`, `transkrip_nilai_terakhir`, `sk_penyetaraan_ijazah`, `disertasi_thesis_terakhir`, `mata_kuliah_diampu`, `ranting_ilmu_kepakaran`, `url_profil_sinta`, `predikat_kinerja_tahun_pertama`, `skp_tahun_pertama`, `predikat_kinerja_tahun_kedua`, `skp_tahun_kedua`, `nilai_konversi`, `pak_konversi`, `created_at`, `updated_at`) VALUES
	(1, 1, 10, 4, 1, 'Dosen', 'Dosen Fungsional', 'Dosen PNS', '199405242024061001', '1234567890123456', '-', 'Muhammad Rivani Ibrahim', 'S.Kom., M.Kom.', 'admin.fakultas@kepegawaian.com', '199405242024061001', '$2y$12$RIZZC3Ubdzeu4YVVbtfLhu11vQKLT2HFO402eSEbQ0NCwALAEylM6', '1234567890123456', 'pegawai-files/foto/SEauQgzeU8dBS5adHfmttha13hrX8Vj0ReXK5OqK.jpg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567895', '2020-01-01', 'pegawai-files/sk_cpns/I7s7wK1X4B8AyXC5j7Si5Av5p9wao9tZuLAHvxJq.pdf', '2021-01-01', 'pegawai-files/sk_pns/T8LdFtsaQlLcB6ncETq7Uqy6WnCw4a8LDkwM194q.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/tJK8SzeObjJDlvgq3qtUN9ctpe2l4z8zCldFslmU.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/96hVSg4JpB65Lul3wG7Sely20Ye4tPui15OeIgvc.pdf', 'Magister (S2) / Sederajat', 'Universitas Mulawarman', 'Magister Sistem Informasi', 'pegawai-files/ijazah_terakhir/IljZEa4FYZ7uqV6YUw7hl8j8YHcQUJOV64pOKu0D.pdf', 'pegawai-files/transkrip_nilai_terakhir/dRfVV2beCjbkjLz0zRLZZLXBIIj9t5O9YsORg1AV.pdf', 'pegawai-files/sk_penyetaraan_ijazah/zkx2sakisXvzA18yKzu9xdmTZuC0FcVDTvKWgKcG.pdf', 'pegawai-files/disertasi_thesis_terakhir/3uNA8WMq43oGe1D7SHDFz4FHRyQhMGVMmbCJS9TK.pdf', 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/Kvb6VsisMKcf0CCMGGbp3KmWAgAxSbfZQzhFT322.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/lXwpChu7n5kvX5yyXqRSZ02kiMfG2gOJFV3tEWSl.pdf', 85.5, 'pegawai-files/pak_konversi/LszGzkcSvIB4GUR1vcIjf0dcN2er6fAa3PaskHQ3.pdf', '2025-08-17 23:17:12', '2025-08-18 10:21:50'),
	(2, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '199001012015011001', '1234567890123456', NULL, 'Budi Santoso', 'S.Kom., M.Kom.', 'budi.santoso@unmul.ac.id', '199001012015011001', '$2y$12$nPWOsQENWC.mIap7TD6cU.jQfOiw6O1OEmJp/TRvRlfXkhV5DnDzm', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:12', '2025-08-17 23:17:12'),
	(3, NULL, 1, 7, 1, 'Tenaga Kependidikan', NULL, 'Tenaga Kependidikan PNS', '199202022016022002', '1234567890123456', NULL, 'Citra Lestari', 'S.Kom., M.Kom.', 'citra.lestari@unmul.ac.id', '199202022016022002', '$2y$12$EM4dTKsl0cpoI6/956tPjOLnVbr9KS9SZGccephdXyWhPz5g3mdn.', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:12', '2025-08-17 23:17:12'),
	(4, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '199503032017033003', '1234567890123456', NULL, 'Ahmad Fauzi', 'S.Kom., M.Kom.', 'ahmad.fauzi@unmul.ac.id', '199503032017033003', '$2y$12$OuMb/JhbMWAT2UArbdEGxelcZDXT2cFgBlwIFT4DVM0t7lIgc0QJO', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:12', '2025-08-17 23:17:12'),
	(5, NULL, 1, 7, 1, 'Tenaga Kependidikan', NULL, 'Tenaga Kependidikan PNS', '199604042018044004', '1234567890123456', NULL, 'Siti Nurhaliza', 'S.Kom., M.Kom.', 'siti.nurhaliza@unmul.ac.id', '199604042018044004', '$2y$12$BCw/H98tET6TjcwHGHjfc.pQyXEix3HsQ9o7L/qg4fOeqn.AJFhdu', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:13', '2025-08-17 23:17:13'),
	(6, NULL, 18, 11, 1, 'Dosen', NULL, 'Dosen PPPK', '199705052019055005', '1234567890123456', NULL, 'Rizki Pratama', 'S.Kom., M.Kom.', 'rizki.pratama@unmul.ac.id', '199705052019055005', '$2y$12$1ep6lXEX/zvfTwvfnvm32OOsAaO9em2QlQDVljgjMADcC1oeVPBmK', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:13', '2025-08-17 23:17:13'),
	(7, NULL, 18, 7, 1, 'Tenaga Kependidikan', NULL, 'Tenaga Kependidikan PPPK', '199806062020066006', '1234567890123456', NULL, 'Dewi Sartika', 'S.Kom., M.Kom.', 'dewi.sartika@unmul.ac.id', '199806062020066006', '$2y$12$QlmTQcy9mLdoFF45ZwR20ecTQsgQdcPjHp9kfm9bTvaYPBa8kNp36', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:13', '2025-08-17 23:17:13'),
	(8, NULL, 22, 11, 1, 'Dosen', NULL, 'Dosen Non ASN', '199907072021077007', '1234567890123456', NULL, 'Hendra Wijaya', 'S.Kom., M.Kom.', 'hendra.wijaya@unmul.ac.id', '199907072021077007', '$2y$12$hCeaEX7wEh7zpdnbhy2PeevtVQUXtvMP915xRbFYp8/h/JUbikeUe', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:13', '2025-08-17 23:17:13'),
	(9, 4, 22, 7, 1, 'Tenaga Kependidikan', NULL, 'Tenaga Kependidikan Non ASN', '200008082022088008', '1234567890123456', NULL, 'Maya Indah', 'S.Kom., M.Kom.', 'maya.indah@unmul.ac.id', '200008082022088008', '$2y$12$r6MdRZELi8QF8XNXcrQ3TuulPEFtyzSj.mZIIi5ew756DcREcjTve', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:13', '2025-08-17 23:17:13'),
	(10, 1, 1, 3, 1, 'Dosen', 'Dosen Fungsional', 'Dosen PNS', '200109092023099009', '1234567890123456', NULL, 'Doni Kusuma', 'S.Kom., M.Kom.', 'doni.kusuma@unmul.ac.id', '200109092023099009', '$2y$12$1iNpvX/5KEmMboe58uMdkeOoQRbrmgUftfTV1Hh5KojvtB21kfOau', '1234567890123456', 'pegawai-files/foto/SEauQgzeU8dBS5adHfmttha13hrX8Vj0ReXK5OqK.jpg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Sarjana (S1) / Diploma IV / Sederajat', 'Universitas Mulawarman', 'Magister Sistem Informasi', 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:14', '2025-08-18 09:05:34'),
	(11, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '198505152010011001', '1234567890123456', NULL, 'Prof. Dr. Bambang Setiawan', 'S.Kom., M.Kom.', 'bambang.setiawan@unmul.ac.id', '198505152010011001', '$2y$12$nr5UC4IJKeiDpnYoW8S/wurfCf6LWWW2qXT0qqHSB4srYBdeLshNi', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:14', '2025-08-17 23:17:14'),
	(12, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '198606162011022002', '1234567890123456', NULL, 'Prof. Dr. Sri Wahyuni', 'S.Kom., M.Kom.', 'sri.wahyuni@unmul.ac.id', '198606162011022002', '$2y$12$DBn6ZkaNKhwBdqAi/V/v..B16MyV7dDfYk/34hjP0S1gKWHNUP0iC', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:14', '2025-08-17 23:17:14'),
	(13, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '197707172008011003', '1234567890123456', NULL, 'Prof. Dr. Agus Setiawan', 'S.Kom., M.Kom.', 'agus.setiawan@unmul.ac.id', '197707172008011003', '$2y$12$CqHKoMJP7a962pLbOwtXreYYMdx3y72UrWUSzM8Lwg0VKIThIDk6G', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:14', '2025-08-17 23:17:14'),
	(14, NULL, 1, 11, 1, 'Dosen', NULL, 'Dosen PNS', '197808182009022004', '1234567890123456', NULL, 'Prof. Dr. Endang Sulistyowati', 'S.Kom., M.Kom.', 'endang.sulistyowati@unmul.ac.id', '197808182009022004', '$2y$12$VPuzAzGJiAggCv/7YzOtgOQw7Gev4G1Te7sIRjFudrQRNCtewhvSy', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, 'Pemrograman Web, Basis Data, Algoritma', 'Teknologi Informasi', 'https://sinta.kemdikbud.go.id/authors/profile/123456', 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:14', '2025-08-17 23:17:14'),
	(15, NULL, 1, 7, 1, 'Tenaga Kependidikan', NULL, 'Tenaga Kependidikan PNS', '198909192012033005', '1234567890123456', NULL, 'Sri Mulyani', 'S.Kom., M.Kom.', 'sri.mulyani@unmul.ac.id', '198909192012033005', '$2y$12$EIq8jkCBEBrKiQufmEXHYuhET9CZb9TKeWvDrnHGRi/atfx3RFHuy', '1234567890123456', 'pegawai-files/foto/dummy-avatar.svg', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2020-01-01', 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf', '2021-01-01', 'pegawai-files/sk_pns/dummy-sk-pns.pdf', '2022-01-01', 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf', '2023-01-01', 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf', 'Magister (S2) / Sederajat', NULL, NULL, 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf', 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf', 'Sangat Baik', 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf', 85.5, 'pegawai-files/pak_konversi/dummy-pak.pdf', '2025-08-17 23:17:15', '2025-08-17 23:17:15');

-- membuang struktur untuk table db_kepegunmul.penilais
DROP TABLE IF EXISTS `penilais`;
CREATE TABLE IF NOT EXISTS `penilais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bidang_keahlian` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penilais_nip_unique` (`nip`),
  UNIQUE KEY `penilais_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.penilais: ~5 rows (lebih kurang)
REPLACE INTO `penilais` (`id`, `nama_lengkap`, `nip`, `email`, `bidang_keahlian`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Dr. Ahmad Hidayat, M.Si.', '198501012010011001', 'ahmad.hidayat@unmul.ac.id', 'Manajemen, Ekonomi, Administrasi Publik', 'aktif', '2025-08-20 02:11:16', '2025-08-20 02:11:16'),
	(2, 'Prof. Dr. Siti Nurhaliza, M.Pd.', '197503152005012001', 'siti.nurhaliza@unmul.ac.id', 'Pendidikan, Psikologi, Pengembangan SDM', 'aktif', '2025-08-20 02:11:16', '2025-08-20 02:11:16'),
	(3, 'Dr. Bambang Sutrisno, S.E., M.M.', '198012102008011001', 'bambang.sutrisno@unmul.ac.id', 'Manajemen, Bisnis, Keuangan', 'aktif', '2025-08-20 02:11:16', '2025-08-20 02:11:16'),
	(4, 'Dr. Rina Marlina, S.Pd., M.Ed.', '198604152010012001', 'rina.marlina@unmul.ac.id', 'Pendidikan, Kurikulum, Evaluasi Pembelajaran', 'aktif', '2025-08-20 02:11:16', '2025-08-20 02:11:16'),
	(5, 'Prof. Dr. Muhammad Rizki, S.T., M.T.', '197208102003121001', 'muhammad.rizki@unmul.ac.id', 'Teknik, Teknologi, Inovasi', 'aktif', '2025-08-20 02:11:16', '2025-08-20 02:11:16');

-- membuang struktur untuk table db_kepegunmul.periode_usulans
DROP TABLE IF EXISTS `periode_usulans`;
CREATE TABLE IF NOT EXISTS `periode_usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_usulan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kepegawaian` json DEFAULT NULL COMMENT 'Status kepegawaian yang diizinkan untuk mengakses periode ini',
  `tahun_periode` year NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `senat_min_setuju` int unsigned NOT NULL DEFAULT '1',
  `tanggal_mulai_perbaikan` date DEFAULT NULL,
  `tanggal_selesai_perbaikan` date DEFAULT NULL,
  `status` enum('Buka','Tutup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tutup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_periode_status_dates` (`status`,`tanggal_mulai`,`tanggal_selesai`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.periode_usulans: ~0 rows (lebih kurang)
REPLACE INTO `periode_usulans` (`id`, `nama_periode`, `jenis_usulan`, `status_kepegawaian`, `tahun_periode`, `tanggal_mulai`, `tanggal_selesai`, `senat_min_setuju`, `tanggal_mulai_perbaikan`, `tanggal_selesai_perbaikan`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Gelombang 1', 'Usulan Jabatan', '["Dosen PNS"]', '2025', '2025-08-01', '2025-08-25', 1, NULL, NULL, 'Buka', '2025-08-18 05:46:32', '2025-08-18 05:46:32');

-- membuang struktur untuk table db_kepegunmul.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.permissions: ~17 rows (lebih kurang)
REPLACE INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'view_all_pegawai_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(2, 'view_fakultas_pegawai_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(3, 'view_own_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(4, 'view_assessment_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(5, 'view_financial_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(6, 'view_senate_documents', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(7, 'manage_pegawai', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(8, 'manage_jabatan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(9, 'manage_pangkat', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(10, 'manage_unit_kerja', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(11, 'manage_roles', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(12, 'view_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(13, 'create_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(14, 'edit_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(15, 'delete_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(16, 'approve_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(17, 'reject_usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10');

-- membuang struktur untuk table db_kepegunmul.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.roles: ~7 rows (lebih kurang)
REPLACE INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Admin Universitas Usulan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(2, 'Admin Universitas', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(3, 'Admin Fakultas', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(4, 'Admin Keuangan', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(5, 'Tim Senat', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(6, 'Penilai Universitas', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(7, 'Pegawai Unmul', 'pegawai', '2025-08-17 23:17:10', '2025-08-17 23:17:10');

-- membuang struktur untuk table db_kepegunmul.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.role_has_permissions: ~32 rows (lebih kurang)
REPLACE INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 3),
	(3, 7),
	(4, 6),
	(5, 4),
	(6, 5),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(12, 3),
	(12, 4),
	(12, 5),
	(12, 6),
	(12, 7),
	(13, 1),
	(13, 3),
	(13, 7),
	(14, 1),
	(14, 3),
	(14, 7),
	(15, 1),
	(16, 1),
	(16, 3),
	(16, 5),
	(16, 6),
	(17, 1),
	(17, 3),
	(17, 5),
	(17, 6);

-- membuang struktur untuk table db_kepegunmul.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.sessions: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.sub_sub_unit_kerjas
DROP TABLE IF EXISTS `sub_sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` (`sub_unit_kerja_id`),
  CONSTRAINT `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` FOREIGN KEY (`sub_unit_kerja_id`) REFERENCES `sub_unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.sub_sub_unit_kerjas: ~57 rows (lebih kurang)
REPLACE INTO `sub_sub_unit_kerjas` (`id`, `sub_unit_kerja_id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Prodi Rekayasa Perangkat Lunak', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(2, 1, 'Prodi Jaringan Komputer', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(3, 1, 'Prodi Sistem Informasi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(4, 2, 'Prodi Struktur Bangunan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(5, 2, 'Prodi Manajemen Konstruksi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(6, 3, 'Prodi Konversi Energi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(7, 3, 'Prodi Manufaktur', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(8, 4, 'Prodi Teknik Tenaga Listrik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(9, 4, 'Prodi Teknik Telekomunikasi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(10, 5, 'Prodi Manajemen Keuangan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(11, 5, 'Prodi Manajemen Pemasaran', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(12, 6, 'Prodi Akuntansi Keuangan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(13, 6, 'Prodi Akuntansi Manajemen', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(14, 7, 'Prodi Ekonomi Regional', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(15, 7, 'Prodi Ekonomi Moneter', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(16, 8, 'Prodi Pendidikan Dokter Umum', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(17, 9, 'Prodi Keperawatan Medikal Bedah', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(18, 9, 'Prodi Keperawatan Maternitas', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(19, 10, 'Prodi Hukum Pidana', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(20, 10, 'Prodi Hukum Perdata', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(21, 10, 'Prodi Hukum Tata Negara', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(22, 11, 'Prodi Budidaya Tanaman', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(23, 11, 'Prodi Perlindungan Tanaman', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(24, 12, 'Prodi Agribisnis Hortikultura', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(25, 12, 'Prodi Agribisnis Perikanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(26, 13, 'Prodi Manajemen Hutan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(27, 13, 'Prodi Teknologi Hasil Hutan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(28, 14, 'Prodi Budidaya Perairan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(29, 14, 'Prodi Teknologi Hasil Perikanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(30, 15, 'Prodi Oseanografi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(31, 15, 'Prodi Manajemen Sumberdaya Pesisir', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(32, 16, 'Prodi Matematika Murni', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(33, 16, 'Prodi Matematika Terapan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(34, 17, 'Prodi Fisika Material', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(35, 17, 'Prodi Fisika Instrumentasi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(36, 18, 'Prodi Kimia Analitik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(37, 18, 'Prodi Kimia Organik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(38, 19, 'Prodi Biologi Molekuler', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(39, 19, 'Prodi Biologi Lingkungan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(40, 20, 'Prodi Sosiologi Pembangunan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(41, 20, 'Prodi Sosiologi Lingkungan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(42, 21, 'Prodi Politik Lokal', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(43, 21, 'Prodi Hubungan Internasional', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(44, 22, 'Prodi Jurnalistik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(45, 22, 'Prodi Public Relations', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(46, 23, 'Prodi Pendidikan Matematika SD', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(47, 23, 'Prodi Pendidikan Matematika SMP/SMA', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(48, 24, 'Prodi Pendidikan Biologi SMP/SMA', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(49, 25, 'Prodi Pendidikan Bahasa Indonesia SD', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(50, 25, 'Prodi Pendidikan Bahasa Indonesia SMP/SMA', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(51, 26, 'Lembaga Penelitian dan Pengabdian Masyarakat', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(52, 26, 'Lembaga Pengembangan Pendidikan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(53, 27, 'Biro Akademik dan Kemahasiswaan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(54, 27, 'Biro Umum dan Keuangan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(55, 27, 'Biro Perencanaan dan Kerjasama', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(56, 28, 'Direktorat Sistem Informasi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(57, 28, 'Direktorat Pengembangan Usaha', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(58, 29, 'Program Studi Teknik Informatika', '2025-08-19 04:24:27', '2025-08-19 04:24:27');

-- membuang struktur untuk table db_kepegunmul.sub_unit_kerjas
DROP TABLE IF EXISTS `sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sub_unit_kerjas_unit` (`unit_kerja_id`),
  CONSTRAINT `sub_unit_kerjas_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.sub_unit_kerjas: ~28 rows (lebih kurang)
REPLACE INTO `sub_unit_kerjas` (`id`, `unit_kerja_id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 1, 'S1 Informatika', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(2, 1, 'S1 Teknik Sipil', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(3, 1, 'S1 Teknik Mesin', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(4, 1, 'S1 Teknik Elektro', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(5, 2, 'S1 Manajemen', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(6, 2, 'S1 Akuntansi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(7, 2, 'S1 Ekonomi Pembangunan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(8, 3, 'S1 Pendidikan Dokter', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(9, 3, 'S1 Keperawatan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(10, 4, 'S1 Ilmu Hukum', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(11, 5, 'S1 Agroteknologi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(12, 5, 'S1 Agribisnis', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(13, 6, 'S1 Kehutanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(14, 7, 'S1 Perikanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(15, 7, 'S1 Ilmu Kelautan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(16, 8, 'S1 Matematika', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(17, 8, 'S1 Fisika', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(18, 8, 'S1 Kimia', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(19, 8, 'S1 Biologi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(20, 9, 'S1 Sosiologi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(21, 9, 'S1 Ilmu Politik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(22, 9, 'S1 Ilmu Komunikasi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(23, 10, 'S1 Pendidikan Matematika', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(24, 10, 'S1 Pendidikan Biologi', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(25, 10, 'S1 Pendidikan Bahasa Indonesia', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(26, 11, 'Lembaga', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(27, 11, 'Biro', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(28, 11, 'Direktorat', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(29, 1, 'Jurusan Teknik Informatika', '2025-08-19 04:24:27', '2025-08-19 04:24:27');

-- membuang struktur untuk table db_kepegunmul.unit_kerjas
DROP TABLE IF EXISTS `unit_kerjas`;
CREATE TABLE IF NOT EXISTS `unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_kerjas_nama_unique` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.unit_kerjas: ~11 rows (lebih kurang)
REPLACE INTO `unit_kerjas` (`id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 'Fakultas Teknik', '2025-08-17 23:17:10', '2025-08-17 23:17:10'),
	(2, 'Fakultas Ekonomi dan Bisnis', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(3, 'Fakultas Kedokteran', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(4, 'Fakultas Hukum', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(5, 'Fakultas Pertanian', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(6, 'Fakultas Kehutanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(7, 'Fakultas Kelautan dan Perikanan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(8, 'Fakultas Matematika dan Ilmu Pengetahuan Alam', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(9, 'Fakultas Ilmu Sosial dan Ilmu Politik', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(10, 'Fakultas Keguruan dan Ilmu Pendidikan', '2025-08-17 23:17:11', '2025-08-17 23:17:11'),
	(11, 'Unit Kerja Non-Fakultas', '2025-08-17 23:17:11', '2025-08-17 23:17:11');

-- membuang struktur untuk table db_kepegunmul.usulans
DROP TABLE IF EXISTS `usulans`;
CREATE TABLE IF NOT EXISTS `usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status_kepegawaian` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pegawai_id` bigint unsigned NOT NULL,
  `periode_usulan_id` bigint unsigned NOT NULL,
  `jenis_usulan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_lama_id` bigint unsigned DEFAULT NULL,
  `jabatan_tujuan_id` bigint unsigned DEFAULT NULL,
  `status_usulan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `data_usulan` json DEFAULT NULL,
  `validasi_data` json DEFAULT NULL,
  `catatan_verifikator` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulans: ~1 rows (lebih kurang)
REPLACE INTO `usulans` (`id`, `status_kepegawaian`, `pegawai_id`, `periode_usulan_id`, `jenis_usulan`, `jabatan_lama_id`, `jabatan_tujuan_id`, `status_usulan`, `data_usulan`, `validasi_data`, `catatan_verifikator`, `created_at`, `updated_at`) VALUES
	(15, 'Dosen PNS', 1, 1, 'Usulan Jabatan', 4, 5, 'Sedang Direview', '{"metadata": {"version": "1.0", "updated_by": 1, "jenjang_type": "lektor-kepala-to-guru-besar", "last_updated": "2025-08-20T03:13:54.427892Z", "submission_type": "Diajukan", "created_at_snapshot": "2025-08-20T02:25:23.319757Z"}, "karya_ilmiah": {"links": {"wos": "https://claude.ai/chat/", "sinta": "https://claude.ai/chat/4ae1a460-cddd-4a5c-801e-ebaecc9ad901", "scopus": "https://claude.ai/chat", "artikel": "https://claude.ai/chat/4ae1a460-cddd-4a5c-801e-ebaecc9ad901", "scimago": "https://claude.ai/chat/4ae1a460-cddd-4a5c-801e-ebaecc9ad901"}, "updated_at": "2025-08-20T03:13:54.427765Z", "jenis_karya": "Jurnal Internasional Bereputasi", "nama_jurnal": "Bayam Rumah", "edisi_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda", "judul_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda", "nomor_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda", "volume_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda", "halaman_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda", "penerbit_artikel": "Mengapa Kualitas Udara Mempengaruhi Kesehatan Mental Anda"}, "syarat_khusus": {"updated_at": "2025-08-20T03:13:54.427843Z", "deskripsi_syarat": "Pernah mendapatkan hibah penelitian", "syarat_guru_besar": "hibah"}, "dokumen_usulan": {"turnitin": {"path": "usulan-dokumen/1/2025/08/turnitin_1755656723_68a5321333be5.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.223377Z", "uploaded_by": 1, "original_name": "test.pdf"}, "upload_artikel": {"path": "usulan-dokumen/1/2025/08/upload_artikel_1755656723_68a5321336c39.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.235134Z", "uploaded_by": 1, "original_name": "test.pdf"}, "pakta_integritas": {"path": "usulan-dokumen/1/2025/08/pakta_integritas_1755656723_68a532130e43f.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.198111Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bkd_genap_2022_2023": {"path": "usulan-dokumen/1/2025/08/bkd_genap_2022_2023_1755656723_68a532134b0e1.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.318861Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bkd_genap_2023_2024": {"path": "usulan-dokumen/1/2025/08/bkd_genap_2023_2024_1755656723_68a5321344ee5.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.294486Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bukti_korespondensi": {"path": "usulan-dokumen/1/2025/08/bukti_korespondensi_1755656723_68a5321330903.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.211056Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bkd_ganjil_2023_2024": {"path": "usulan-dokumen/1/2025/08/bkd_ganjil_2023_2024_1755656723_68a5321348191.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.306490Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bkd_ganjil_2024_2025": {"path": "usulan-dokumen/1/2025/08/bkd_ganjil_2024_2025_1755656723_68a532133ca46.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.281449Z", "uploaded_by": 1, "original_name": "test.pdf"}, "bukti_syarat_guru_besar": {"path": "usulan-dokumen/1/2025/08/bukti_syarat_guru_besar_1755656723_68a5321339a0c.pdf", "file_size": 594270, "mime_type": "application/pdf", "uploaded_at": "2025-08-20T02:25:23.247542Z", "uploaded_by": 1, "original_name": "test.pdf"}}, "catatan_pengusul": null, "pegawai_snapshot": {"nip": "199405242024061001", "email": "admin.fakultas@kepegawaian.com", "nuptk": "1234567890123456", "sk_pns": "pegawai-files/sk_pns/T8LdFtsaQlLcB6ncETq7Uqy6WnCw4a8LDkwM194q.pdf", "sk_cpns": "pegawai-files/sk_cpns/I7s7wK1X4B8AyXC5j7Si5Av5p9wao9tZuLAHvxJq.pdf", "tmt_pns": "2021-01-01", "tmt_cpns": "2020-01-01", "jabatan_id": 4, "pangkat_id": 10, "gelar_depan": "-", "tmt_jabatan": "2023-01-01", "tmt_pangkat": "2022-01-01", "nama_lengkap": "Muhammad Rivani Ibrahim", "pak_konversi": "pegawai-files/pak_konversi/LszGzkcSvIB4GUR1vcIjf0dcN2er6fAa3PaskHQ3.pdf", "tempat_lahir": "Samarinda", "jenis_kelamin": "Laki-Laki", "jenis_pegawai": "Dosen", "tanggal_lahir": "1990-01-01", "unit_kerja_id": 1, "gelar_belakang": "S.Kom., M.Kom.", "nilai_konversi": 85.5, "ijazah_terakhir": "pegawai-files/ijazah_terakhir/IljZEa4FYZ7uqV6YUw7hl8j8YHcQUJOV64pOKu0D.pdf", "nomor_handphone": "081234567895", "skp_tahun_kedua": "pegawai-files/skp_tahun_kedua/lXwpChu7n5kvX5yyXqRSZ02kiMfG2gOJFV3tEWSl.pdf", "url_profil_sinta": "https://sinta.kemdikbud.go.id/authors/profile/123456", "jabatan_saat_usul": "Lektor Kepala", "pangkat_saat_usul": "Penata Muda Tingkat I (III/b)", "skp_tahun_pertama": "pegawai-files/skp_tahun_pertama/Kvb6VsisMKcf0CCMGGbp3KmWAgAxSbfZQzhFT322.pdf", "mata_kuliah_diampu": "Pemrograman Web, Basis Data, Algoritma", "status_kepegawaian": "Dosen PNS", "pendidikan_terakhir": "Magister (S2) / Sederajat", "sk_jabatan_terakhir": "pegawai-files/sk_jabatan_terakhir/96hVSg4JpB65Lul3wG7Sely20Ye4tPui15OeIgvc.pdf", "sk_pangkat_terakhir": "pegawai-files/sk_pangkat_terakhir/tJK8SzeObjJDlvgq3qtUN9ctpe2l4z8zCldFslmU.pdf", "unit_kerja_saat_usul": "Prodi Rekayasa Perangkat Lunak", "sk_penyetaraan_ijazah": "pegawai-files/sk_penyetaraan_ijazah/zkx2sakisXvzA18yKzu9xdmTZuC0FcVDTvKWgKcG.pdf", "ranting_ilmu_kepakaran": "Teknologi Informasi", "transkrip_nilai_terakhir": "pegawai-files/transkrip_nilai_terakhir/dRfVV2beCjbkjLz0zRLZZLXBIIj9t5O9YsORg1AV.pdf", "disertasi_thesis_terakhir": "pegawai-files/disertasi_thesis_terakhir/3uNA8WMq43oGe1D7SHDFz4FHRyQhMGVMmbCJS9TK.pdf", "predikat_kinerja_tahun_kedua": "Sangat Baik", "predikat_kinerja_tahun_pertama": "Baik"}}', '{"admin_fakultas": {"validation": {"bkd": {"bkd_genap_2022_2023": {"status": "sesuai", "keterangan": null}, "bkd_genap_2023_2024": {"status": "sesuai", "keterangan": null}, "bkd_ganjil_2023_2024": {"status": "sesuai", "keterangan": null}, "bkd_ganjil_2024_2025": {"status": "sesuai", "keterangan": null}}, "data_kinerja": {"nilai_konversi": {"status": "sesuai", "keterangan": "Kondisi ketika di edit dan reload hasilnya"}, "predikat_kinerja_tahun_kedua": {"status": "sesuai", "keterangan": null}, "predikat_kinerja_tahun_pertama": {"status": "sesuai", "keterangan": null}}, "data_pribadi": {"nip": {"status": "sesuai", "keterangan": null}, "email": {"status": "sesuai", "keterangan": null}, "nuptk": {"status": "sesuai", "keterangan": null}, "gelar_depan": {"status": "sesuai", "keterangan": null}, "nama_lengkap": {"status": "sesuai", "keterangan": null}, "tempat_lahir": {"status": "sesuai", "keterangan": null}, "jenis_kelamin": {"status": "sesuai", "keterangan": null}, "jenis_pegawai": {"status": "sesuai", "keterangan": null}, "tanggal_lahir": {"status": "sesuai", "keterangan": null}, "gelar_belakang": {"status": "sesuai", "keterangan": null}, "nomor_handphone": {"status": "sesuai", "keterangan": null}, "status_kepegawaian": {"status": "sesuai", "keterangan": null}}, "karya_ilmiah": {"link_wos": {"status": "sesuai", "keterangan": null}, "link_sinta": {"status": "sesuai", "keterangan": null}, "jenis_karya": {"status": "sesuai", "keterangan": null}, "link_scopus": {"status": "sesuai", "keterangan": null}, "nama_jurnal": {"status": "sesuai", "keterangan": null}, "link_artikel": {"status": "sesuai", "keterangan": null}, "link_scimago": {"status": "sesuai", "keterangan": null}, "edisi_artikel": {"status": "sesuai", "keterangan": null}, "judul_artikel": {"status": "sesuai", "keterangan": null}, "nomor_artikel": {"status": "sesuai", "keterangan": null}, "volume_artikel": {"status": "sesuai", "keterangan": null}, "halaman_artikel": {"status": "sesuai", "keterangan": null}, "penerbit_artikel": {"status": "sesuai", "keterangan": null}}, "dokumen_profil": {"sk_pns": {"status": "sesuai", "keterangan": null}, "sk_cpns": {"status": "sesuai", "keterangan": null}, "pak_konversi": {"status": "sesuai", "keterangan": null}, "ijazah_terakhir": {"status": "sesuai", "keterangan": "Kondisi ketika di edit dan reload hasilnya"}, "skp_tahun_kedua": {"status": "sesuai", "keterangan": null}, "skp_tahun_pertama": {"status": "sesuai", "keterangan": null}, "sk_jabatan_terakhir": {"status": "sesuai", "keterangan": null}, "sk_pangkat_terakhir": {"status": "sesuai", "keterangan": null}, "sk_penyetaraan_ijazah": {"status": "sesuai", "keterangan": null}, "transkrip_nilai_terakhir": {"status": "sesuai", "keterangan": null}, "disertasi_thesis_terakhir": {"status": "sesuai", "keterangan": null}}, "dokumen_usulan": {"turnitin": {"status": "sesuai", "keterangan": null}, "upload_artikel": {"status": "sesuai", "keterangan": null}, "pakta_integritas": {"status": "sesuai", "keterangan": null}, "bukti_korespondensi": {"status": "sesuai", "keterangan": null}}, "data_pendidikan": {"url_profil_sinta": {"status": "sesuai", "keterangan": null}, "mata_kuliah_diampu": {"status": "sesuai", "keterangan": null}, "nama_prodi_jurusan": {"status": "sesuai", "keterangan": "perbaiki dokumen dan naskah ini"}, "pendidikan_terakhir": {"status": "sesuai", "keterangan": null}, "ranting_ilmu_kepakaran": {"status": "sesuai", "keterangan": null}, "nama_universitas_sekolah": {"status": "sesuai", "keterangan": null}}, "data_kepegawaian": {"tmt_pns": {"status": "sesuai", "keterangan": "perbaiki dokumen dan naskah ini"}, "tmt_cpns": {"status": "sesuai", "keterangan": "perbaiki dokumen dan naskah ini"}, "tmt_jabatan": {"status": "sesuai", "keterangan": null}, "tmt_pangkat": {"status": "sesuai", "keterangan": null}, "jabatan_saat_usul": {"status": "sesuai", "keterangan": null}, "pangkat_saat_usul": {"status": "sesuai", "keterangan": null}, "unit_kerja_saat_usul": {"status": "sesuai", "keterangan": null}}, "syarat_guru_besar": {"syarat_guru_besar": {"status": "sesuai", "keterangan": null}, "bukti_syarat_guru_besar": {"status": "sesuai", "keterangan": null}}}, "validated_at": "2025-08-20T13:15:40.917620Z", "validated_by": 1, "dokumen_pendukung": {"nomor_berita_senat": "121/UN17/KP/2025", "nomor_surat_usulan": "121/UN17/KP/2025", "file_berita_senat_path": "dokumen-fakultas/berita-senat/SK PMK Rivani_2025-08-20_13-15-41_FYBxfLNo.pdf", "file_surat_usulan_path": "dokumen-fakultas/surat-usulan/SK PMK Rivani_2025-08-20_13-15-41_Bthe5WOC.pdf"}}, "admin_universitas": {"validation": {"bkd": {"bkd_genap_2022_2023": {"status": "sesuai", "keterangan": null}, "bkd_genap_2023_2024": {"status": "sesuai", "keterangan": null}, "bkd_ganjil_2023_2024": {"status": "sesuai", "keterangan": null}, "bkd_ganjil_2024_2025": {"status": "sesuai", "keterangan": null}}, "data_kinerja": {"nilai_konversi": {"status": "sesuai", "keterangan": null}, "predikat_kinerja_tahun_kedua": {"status": "sesuai", "keterangan": null}, "predikat_kinerja_tahun_pertama": {"status": "sesuai", "keterangan": null}}, "data_pribadi": {"nip": {"status": "sesuai", "keterangan": null}, "email": {"status": "sesuai", "keterangan": null}, "nuptk": {"status": "sesuai", "keterangan": null}, "gelar_depan": {"status": "sesuai", "keterangan": null}, "nama_lengkap": {"status": "sesuai", "keterangan": null}, "tempat_lahir": {"status": "sesuai", "keterangan": null}, "jenis_kelamin": {"status": "sesuai", "keterangan": null}, "jenis_pegawai": {"status": "sesuai", "keterangan": null}, "tanggal_lahir": {"status": "sesuai", "keterangan": null}, "gelar_belakang": {"status": "sesuai", "keterangan": null}, "nomor_handphone": {"status": "sesuai", "keterangan": null}, "status_kepegawaian": {"status": "sesuai", "keterangan": null}}, "karya_ilmiah": {"link_wos": {"status": "sesuai", "keterangan": null}, "link_sinta": {"status": "sesuai", "keterangan": null}, "jenis_karya": {"status": "sesuai", "keterangan": null}, "link_scopus": {"status": "sesuai", "keterangan": null}, "nama_jurnal": {"status": "sesuai", "keterangan": null}, "link_artikel": {"status": "sesuai", "keterangan": null}, "link_scimago": {"status": "sesuai", "keterangan": null}, "edisi_artikel": {"status": "sesuai", "keterangan": null}, "judul_artikel": {"status": "sesuai", "keterangan": null}, "nomor_artikel": {"status": "sesuai", "keterangan": null}, "volume_artikel": {"status": "sesuai", "keterangan": null}, "halaman_artikel": {"status": "sesuai", "keterangan": null}, "penerbit_artikel": {"status": "sesuai", "keterangan": null}}, "dokumen_profil": {"sk_pns": {"status": "sesuai", "keterangan": null}, "sk_cpns": {"status": "sesuai", "keterangan": null}, "pak_konversi": {"status": "sesuai", "keterangan": null}, "ijazah_terakhir": {"status": "sesuai", "keterangan": null}, "skp_tahun_kedua": {"status": "sesuai", "keterangan": null}, "skp_tahun_pertama": {"status": "sesuai", "keterangan": null}, "sk_jabatan_terakhir": {"status": "sesuai", "keterangan": null}, "sk_pangkat_terakhir": {"status": "sesuai", "keterangan": null}, "sk_penyetaraan_ijazah": {"status": "sesuai", "keterangan": null}, "transkrip_nilai_terakhir": {"status": "sesuai", "keterangan": null}, "disertasi_thesis_terakhir": {"status": "sesuai", "keterangan": null}}, "dokumen_usulan": {"turnitin": {"status": "sesuai", "keterangan": null}, "upload_artikel": {"status": "sesuai", "keterangan": null}, "pakta_integritas": {"status": "sesuai", "keterangan": null}, "bukti_korespondensi": {"status": "sesuai", "keterangan": null}}, "data_pendidikan": {"url_profil_sinta": {"status": "sesuai", "keterangan": null}, "mata_kuliah_diampu": {"status": "sesuai", "keterangan": null}, "nama_prodi_jurusan": {"status": "sesuai", "keterangan": null}, "pendidikan_terakhir": {"status": "sesuai", "keterangan": null}, "ranting_ilmu_kepakaran": {"status": "sesuai", "keterangan": null}, "nama_universitas_sekolah": {"status": "sesuai", "keterangan": null}}, "data_kepegawaian": {"tmt_pns": {"status": "sesuai", "keterangan": null}, "tmt_cpns": {"status": "sesuai", "keterangan": null}, "tmt_jabatan": {"status": "sesuai", "keterangan": null}, "tmt_pangkat": {"status": "sesuai", "keterangan": null}, "jabatan_saat_usul": {"status": "sesuai", "keterangan": null}, "pangkat_saat_usul": {"status": "sesuai", "keterangan": null}, "unit_kerja_saat_usul": {"status": "sesuai", "keterangan": null}}, "syarat_guru_besar": {"syarat_guru_besar": {"status": "sesuai", "keterangan": null}, "bukti_syarat_guru_besar": {"status": "sesuai", "keterangan": null}}, "dokumen_admin_fakultas": {"file_berita_senat": {"status": "sesuai", "keterangan": null}, "file_surat_usulan": {"status": "sesuai", "keterangan": null}, "nomor_berita_senat": {"status": "sesuai", "keterangan": null}, "nomor_surat_usulan": {"status": "sesuai", "keterangan": null}}}, "validated_at": "2025-08-20T22:11:45.327094Z", "validated_by": 1, "forward_to_penilai": {"catatan": null, "admin_id": 1, "tanggal_forward": "2025-08-20 22:11:45", "selected_penilais": ["4", "1"]}}}', 'dfgsdfgdfsgsdfgsdfgsdfgsdfgdfg', '2025-08-20 02:25:23', '2025-08-20 22:11:45');

-- membuang struktur untuk table db_kepegunmul.usulan_dokumens
DROP TABLE IF EXISTS `usulan_dokumens`;
CREATE TABLE IF NOT EXISTS `usulan_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `diupload_oleh_id` bigint unsigned NOT NULL,
  `nama_dokumen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_dokumens_usulan_id_created_at_index` (`usulan_id`,`created_at`),
  KEY `usulan_dokumens_diupload_oleh_id_index` (`diupload_oleh_id`),
  CONSTRAINT `usulan_dokumens_diupload_oleh_id_foreign` FOREIGN KEY (`diupload_oleh_id`) REFERENCES `pegawais` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `usulan_dokumens_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulan_dokumens: ~9 rows (lebih kurang)
REPLACE INTO `usulan_dokumens` (`id`, `usulan_id`, `diupload_oleh_id`, `nama_dokumen`, `path`, `created_at`, `updated_at`) VALUES
	(16, 15, 1, 'pakta_integritas', 'usulan-dokumen/1/2025/08/pakta_integritas_1755656723_68a532130e43f.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(17, 15, 1, 'bukti_korespondensi', 'usulan-dokumen/1/2025/08/bukti_korespondensi_1755656723_68a5321330903.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(18, 15, 1, 'turnitin', 'usulan-dokumen/1/2025/08/turnitin_1755656723_68a5321333be5.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(19, 15, 1, 'upload_artikel', 'usulan-dokumen/1/2025/08/upload_artikel_1755656723_68a5321336c39.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(20, 15, 1, 'bukti_syarat_guru_besar', 'usulan-dokumen/1/2025/08/bukti_syarat_guru_besar_1755656723_68a5321339a0c.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(21, 15, 1, 'bkd_ganjil_2024_2025', 'usulan-dokumen/1/2025/08/bkd_ganjil_2024_2025_1755656723_68a532133ca46.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(22, 15, 1, 'bkd_genap_2023_2024', 'usulan-dokumen/1/2025/08/bkd_genap_2023_2024_1755656723_68a5321344ee5.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(23, 15, 1, 'bkd_ganjil_2023_2024', 'usulan-dokumen/1/2025/08/bkd_ganjil_2023_2024_1755656723_68a5321348191.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(24, 15, 1, 'bkd_genap_2022_2023', 'usulan-dokumen/1/2025/08/bkd_genap_2022_2023_1755656723_68a532134b0e1.pdf', '2025-08-20 02:25:23', '2025-08-20 02:25:23');

-- membuang struktur untuk table db_kepegunmul.usulan_jabatan_senat
DROP TABLE IF EXISTS `usulan_jabatan_senat`;
CREATE TABLE IF NOT EXISTS `usulan_jabatan_senat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `anggota_senat_id` bigint unsigned NOT NULL,
  `keputusan` enum('direkomendasikan','belum_direkomendasikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `diputuskan_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usulan_jabatan_senat_usulan_id_anggota_senat_id_unique` (`usulan_id`,`anggota_senat_id`),
  KEY `usulan_jabatan_senat_anggota_senat_id_foreign` (`anggota_senat_id`),
  CONSTRAINT `usulan_jabatan_senat_anggota_senat_id_foreign` FOREIGN KEY (`anggota_senat_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_jabatan_senat_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulan_jabatan_senat: ~0 rows (lebih kurang)

-- membuang struktur untuk table db_kepegunmul.usulan_logs
DROP TABLE IF EXISTS `usulan_logs`;
CREATE TABLE IF NOT EXISTS `usulan_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `status_sebelumnya` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dilakukan_oleh_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usulan_logs_usulan_created` (`usulan_id`,`created_at`),
  KEY `idx_usulan_logs_dilakukan_oleh` (`dilakukan_oleh_id`),
  CONSTRAINT `usulan_logs_dilakukan_oleh_id_foreign` FOREIGN KEY (`dilakukan_oleh_id`) REFERENCES `pegawais` (`id`),
  CONSTRAINT `usulan_logs_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulan_logs: ~10 rows (lebih kurang)
REPLACE INTO `usulan_logs` (`id`, `usulan_id`, `status_sebelumnya`, `status_baru`, `catatan`, `dilakukan_oleh_id`, `created_at`, `updated_at`) VALUES
	(30, 15, NULL, 'Draft', 'Usulan dibuat dengan status Draft', 1, '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(31, 15, NULL, 'Draft', 'Usulan disimpan sebagai draft oleh pegawai', 1, '2025-08-20 02:25:23', '2025-08-20 02:25:23'),
	(32, 15, 'Draft', 'Diajukan', 'Usulan diajukan oleh pegawai untuk review', 1, '2025-08-20 02:25:51', '2025-08-20 02:25:51'),
	(33, 15, 'Diajukan', 'Perbaikan Usulan', 'Usulan dikembalikan ke Pegawai untuk perbaikan.', 1, '2025-08-20 02:54:47', '2025-08-20 02:54:47'),
	(34, 15, 'Perbaikan Usulan', 'Draft', 'Usulan diperbarui sebagai draft', 1, '2025-08-20 03:13:39', '2025-08-20 03:13:39'),
	(35, 15, 'Draft', 'Diajukan', 'Usulan diajukan oleh pegawai untuk review', 1, '2025-08-20 03:13:54', '2025-08-20 03:13:54'),
	(36, 15, 'Diajukan', 'Diusulkan ke Universitas', 'Usulan divalidasi dan diteruskan ke Universitas.', 1, '2025-08-20 04:41:35', '2025-08-20 04:41:35'),
	(37, 15, 'Perbaikan Usulan', 'Diusulkan ke Universitas', 'Usulan berhasil diperbaiki dan dikirim kembali ke Universitas.', 1, '2025-08-20 13:15:41', '2025-08-20 13:15:41');

-- membuang struktur untuk table db_kepegunmul.usulan_penilai
DROP TABLE IF EXISTS `usulan_penilai`;
CREATE TABLE IF NOT EXISTS `usulan_penilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `penilai_id` bigint unsigned NOT NULL,
  `status_penilaian` enum('Belum Dinilai','Sesuai','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dinilai',
  `catatan_penilaian` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_penilai_penilai_id_foreign` (`penilai_id`),
  KEY `idx_usulan_penilai` (`usulan_id`,`penilai_id`),
  KEY `idx_usulan_status_penilaian` (`usulan_id`,`status_penilaian`),
  CONSTRAINT `usulan_penilai_penilai_id_foreign` FOREIGN KEY (`penilai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_penilai_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulan_penilai: ~0 rows (lebih kurang)
REPLACE INTO `usulan_penilai` (`id`, `usulan_id`, `penilai_id`, `status_penilaian`, `catatan_penilaian`, `created_at`, `updated_at`) VALUES
	(1, 15, 4, 'Belum Dinilai', NULL, '2025-08-20 22:11:45', '2025-08-20 22:11:45'),
	(2, 15, 1, 'Belum Dinilai', NULL, '2025-08-20 22:11:45', '2025-08-20 22:11:45');

-- membuang struktur untuk table db_kepegunmul.usulan_validasi
DROP TABLE IF EXISTS `usulan_validasi`;
CREATE TABLE IF NOT EXISTS `usulan_validasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usulan_validasi_usulan_id_role_unique` (`usulan_id`,`role`),
  CONSTRAINT `usulan_validasi_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel db_kepegunmul.usulan_validasi: ~0 rows (lebih kurang)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

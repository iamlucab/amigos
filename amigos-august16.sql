-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 17, 2025 at 04:31 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amigos`
--

-- --------------------------------------------------------

--
-- Table structure for table `benefits`
--

CREATE TABLE `benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `benefit_member`
--

CREATE TABLE `benefit_member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `benefit_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Given',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashback_logs`
--

CREATE TABLE `cashback_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `level` tinyint(3) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_in_requests`
--

CREATE TABLE `cash_in_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `proof_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Food', 'categories/6UMDbNbZ5NK6tvjyue2Q8GCBkfR0CMaXoIGpkMUQ.png', NULL, '2025-07-29 02:39:30'),
(2, 'Drinks', 'categories/MGvDiCXqpgas087eEG8N2AzZwhLnQ5CB6OXOvyrS.png', NULL, '2025-07-29 02:39:40'),
(3, 'Household', 'categories/o30CgFBUreWEotg15QYpKQtD0uzMccR2YwTj2nv5.png', NULL, '2025-07-29 02:39:49'),
(4, 'Apparels', 'categories/9ODgsUt34mSlRMRw6DrKT8Tna4raGoEGOdmYcvr3.png', NULL, '2025-07-29 02:39:58'),
(5, 'Health & Beauty', 'categories/bWlVRjqIFrQXSIfStvejXlzjW0knPDpwRPR7rhB5.png', NULL, '2025-07-29 02:40:22'),
(6, 'Electronics', 'categories/5fexRjogus2ROMF6CRw8x58aMtZQBtYVZ6WeDrSo.png', NULL, '2025-07-29 02:40:36'),
(7, 'Sports & Outdoors', 'categories/6g6UUtJAFSp5LNCQnlCCIoPOYCKlWgKQu8vppA3i.png', NULL, '2025-07-29 02:40:46'),
(8, 'Toys & Games', 'categories/gxXY0IOvA6jpnPEG6lL9h1Pb9q7T8Z9DrXzdgZgg.png', NULL, '2025-07-29 02:40:56'),
(9, 'Books & Stationery', 'categories/yHF5St6apCTIjJHqD0V26eG99znPCeFlt151RMTc.png', NULL, '2025-07-29 02:41:06'),
(10, 'Automotive', 'categories/ozinXurk91zigybUpBwXYaD9Kma2SbTjmSq8kEix.png', NULL, '2025-07-29 02:41:29'),
(11, 'Pets', 'categories/T5P5ETwt43jAYdGdv18UG9aIGjPigDbEv9Oz5wje.png', NULL, '2025-07-29 02:41:38'),
(12, 'Gardening', 'categories/VXBMub3idn5WHzLFUmou7znOO5Ph39ruuONNcYh0.png', NULL, '2025-07-29 02:41:47'),
(13, 'Office Supplies', 'categories/onFaqETb7wipnghzvshdtCAxrkqbXV4uM3HRzB5W.png', NULL, '2025-07-29 02:41:57'),
(14, 'Jewelry & Accessories', 'categories/310JAQK6lNoxR04BjyO11LDIUEudTWQ1CHpz4K1Z.png', NULL, '2025-07-29 02:42:07'),
(15, 'Music & Movies', 'categories/uuephG4mwAXlUir30C9D81iTiVgAkoIyZgdV2XW6.png', NULL, '2025-07-29 02:42:14'),
(16, 'Skills Directory', 'categories/kUnWkZj2mtBRFA2KqzD0kN101NdxGqGDF5hMZ6uq.png', '2025-07-22 05:15:08', '2025-07-29 02:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE `device_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `device_token` varchar(500) NOT NULL,
  `device_type` varchar(255) NOT NULL DEFAULT 'mobile',
  `platform` varchar(255) DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `app_version` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `monthly_payment` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Staff','Member') NOT NULL DEFAULT 'Member',
  `sponsor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `loan_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `first_name`, `middle_name`, `last_name`, `birthday`, `mobile_number`, `occupation`, `address`, `photo`, `role`, `sponsor_id`, `voter_id`, `created_at`, `updated_at`, `loan_eligible`, `status`) VALUES
(1, 'System', 'Coder', 'Administrator', '1990-01-01', '09171234567', 'Administrator', 'Admin office', NULL, 'Admin', NULL, NULL, '2025-08-10 06:11:43', '2025-08-11 04:43:17', 1, 'Approved'),
(2, 'Staff', NULL, 'One', '1990-01-01', '09171234568', 'Staff', 'Staff office', NULL, 'Staff', 1, NULL, '2025-08-10 06:11:43', '2025-08-12 09:12:31', 0, 'Approved'),
(3, 'Staff', NULL, 'Two', '1990-01-01', '09171234569', 'Staff', 'Staff office', NULL, 'Staff', 1, NULL, '2025-08-10 06:11:43', '2025-08-12 09:12:08', 0, 'Approved'),
(4, 'Chief', NULL, 'Executive officer', '1980-01-25', '09177260180', 'Lawyer', 'Door c alpha 11 building, rizal extension street, davao city', NULL, 'Member', 1, NULL, '2025-08-10 06:11:43', '2025-08-11 05:29:36', 1, 'Approved'),
(5, 'Chief', NULL, 'Finance officer', '1982-06-13', '09191111111', 'Accountant', 'Rizal extension street, davao city', NULL, 'Member', 1, NULL, '2025-08-10 06:11:43', '2025-08-11 05:30:21', 1, 'Approved'),
(6, 'Chief', NULL, 'Operations officer', '2017-06-07', '09192222222', 'Prosecutor', 'Blk 8 lot 27 evissa subdivision, davao city', NULL, 'Member', 1, NULL, '2025-08-10 06:11:43', '2025-08-11 05:30:53', 1, 'Approved'),
(7, 'Luis', NULL, 'Cabandez', '1980-01-25', '09193333333', NULL, NULL, NULL, 'Member', 4, NULL, '2025-08-11 05:32:55', '2025-08-12 00:32:12', 1, 'Approved'),
(8, 'Susan', 'Tan', 'Lacia', '1963-05-30', '09774462817', NULL, NULL, NULL, 'Member', 7, NULL, '2025-08-11 23:32:59', '2025-08-12 00:33:24', 1, 'Approved'),
(9, 'Bernie', 'Paraguya', 'Baldesco', '1980-04-04', '09465935416', 'Businessman', NULL, NULL, 'Member', 7, NULL, '2025-08-12 01:34:01', '2025-08-12 01:34:01', 0, 'Approved'),
(10, 'Cindy', 'Polison', 'Bandao', '1998-02-23', '09914528619', 'Saleswoman', NULL, NULL, 'Member', 9, NULL, '2025-08-12 01:35:12', '2025-08-12 01:35:12', 0, 'Approved'),
(11, 'Nor', 'U', 'Umpar', '1982-04-04', '09099200018', 'Lawyer', NULL, NULL, 'Member', 7, NULL, '2025-08-12 01:36:46', '2025-08-12 01:36:46', 0, 'Approved'),
(12, 'Ariel', 'Besmar', 'Capili', '1967-10-19', '09171852313', NULL, NULL, NULL, 'Member', 11, NULL, '2025-08-12 01:37:30', '2025-08-12 01:37:30', 0, 'Approved'),
(13, 'Mary ann', 'Pagas', 'Olbez', '1982-10-25', '09264663844', 'Business', NULL, NULL, 'Member', 7, NULL, '2025-08-12 01:38:11', '2025-08-12 01:38:11', 0, 'Approved'),
(14, 'Renz', 'Lim', 'Licarte', '1988-05-11', '09763632594', 'Engineer', NULL, NULL, 'Member', 7, NULL, '2025-08-12 01:38:49', '2025-08-12 01:38:49', 0, 'Approved'),
(15, 'Margie', 'Navea', 'Palacio', '1993-07-12', '09670891993', 'Business owner', NULL, NULL, 'Member', 7, NULL, '2025-08-12 01:39:25', '2025-08-12 01:39:25', 0, 'Approved'),
(16, 'Leah', 'Maldepeña', 'Perez', '1989-01-21', '09198649321', 'Supervisor', NULL, NULL, 'Member', 15, NULL, '2025-08-12 01:40:13', '2025-08-12 01:40:13', 0, 'Approved'),
(17, 'Melanie', 'Moran', 'Guiday', '1988-01-12', '09165210706', 'Real estate salesperson', NULL, NULL, 'Member', 11, NULL, '2025-08-12 01:41:09', '2025-08-12 01:41:09', 0, 'Approved'),
(18, 'Abel', 'Avila', 'Moya', '1968-12-26', '09177732303', NULL, NULL, NULL, 'Member', 12, NULL, '2025-08-14 00:29:41', '2025-08-14 00:58:42', 1, 'Approved'),
(19, 'John crisostomo', 'Abaday', 'Perez', '1984-10-26', '09608511401', NULL, NULL, NULL, 'Member', 7, NULL, '2025-08-16 18:31:35', '2025-08-16 18:40:20', 1, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `membership_codes`
--

CREATE TABLE `membership_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(8) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `used_by` bigint(20) UNSIGNED DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `reserved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reserved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reserved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_codes`
--

INSERT INTO `membership_codes` (`id`, `code`, `used`, `used_by`, `used_at`, `reserved`, `created_at`, `updated_at`, `reserved_by`, `reserved_at`) VALUES
(1, 'BRJX5JGX', 1, 1, '2025-08-11 04:43:17', 0, '2025-08-11 04:42:30', '2025-08-11 04:43:17', NULL, NULL),
(2, '8ZOHNEZK', 1, 4, '2025-08-11 04:44:29', 0, '2025-08-11 04:42:30', '2025-08-11 04:44:29', NULL, NULL),
(3, 'TERNIIQV', 1, 5, '2025-08-11 04:46:01', 0, '2025-08-11 04:42:30', '2025-08-11 04:46:01', NULL, NULL),
(4, '7T3K8AHJ', 1, 6, '2025-08-11 04:47:22', 0, '2025-08-11 04:42:30', '2025-08-11 04:47:22', NULL, NULL),
(5, 'ZPO4ZWBT', 1, 7, '2025-08-12 00:32:12', 0, '2025-08-11 04:42:30', '2025-08-12 00:32:12', NULL, NULL),
(6, 'AD792CEI', 1, 8, '2025-08-12 00:33:24', 0, '2025-08-11 04:42:30', '2025-08-12 00:33:24', NULL, NULL),
(7, 'LXSCTOPG', 1, 9, '2025-08-12 01:34:01', 0, '2025-08-11 04:42:30', '2025-08-12 01:34:01', NULL, NULL),
(8, 'KPE7EKA7', 1, 10, '2025-08-12 01:35:12', 0, '2025-08-11 04:42:30', '2025-08-12 01:35:12', NULL, NULL),
(9, 'HPJ1CEUV', 1, 11, '2025-08-12 01:36:46', 0, '2025-08-11 04:42:30', '2025-08-12 01:36:46', NULL, NULL),
(10, 'COMTL3DH', 1, 12, '2025-08-12 01:37:30', 0, '2025-08-11 04:42:30', '2025-08-12 01:37:30', NULL, NULL),
(11, 'SSRP0NIJ', 1, 13, '2025-08-12 01:38:11', 0, '2025-08-12 01:35:51', '2025-08-12 01:38:11', NULL, NULL),
(12, 'P5RPB4SQ', 1, 14, '2025-08-12 01:38:49', 0, '2025-08-12 01:35:51', '2025-08-12 01:38:49', NULL, NULL),
(13, 'BPGCHLIY', 1, 15, '2025-08-12 01:39:25', 0, '2025-08-12 01:35:51', '2025-08-12 01:39:25', NULL, NULL),
(14, 'LQYMZMMY', 1, 17, '2025-08-12 01:41:09', 0, '2025-08-12 01:35:51', '2025-08-12 01:41:09', NULL, NULL),
(15, 'RH1DG8JP', 1, 16, '2025-08-12 01:40:14', 0, '2025-08-12 01:35:51', '2025-08-12 01:40:14', NULL, NULL),
(16, 'IVZYWUAC', 1, 3, '2025-08-12 09:12:09', 0, '2025-08-12 01:35:51', '2025-08-12 09:12:09', NULL, NULL),
(17, 'JAP1CUR5', 1, 18, '2025-08-14 00:37:37', 0, '2025-08-12 01:35:51', '2025-08-14 00:37:37', NULL, NULL),
(18, 'DEPSYLWX', 1, 2, '2025-08-12 09:12:32', 0, '2025-08-12 01:35:51', '2025-08-12 09:12:32', NULL, NULL),
(19, '8C2T7E8Y', 1, 19, '2025-08-16 18:40:20', 0, '2025-08-12 01:35:51', '2025-08-16 18:40:20', NULL, NULL),
(20, 'OPEAGPPI', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(21, 'CCEESXBW', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(22, 'I9VKHNHF', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(23, 'JO1BOQVA', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(24, 'BL1AMZB1', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(25, 'CUPX3FVH', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(26, 'QTTIJJ0C', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(27, 'GATADZKL', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(28, 'PFHUKDC1', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(29, 'LFAF8ZWK', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL),
(30, '6JWZA7MH', 0, NULL, NULL, 0, '2025-08-12 01:35:51', '2025-08-12 01:35:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `membership_code_requests`
--

CREATE TABLE `membership_code_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `reserved_codes_count` int(11) NOT NULL DEFAULT 0,
  `amount_per_code` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `proof_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_code_request_items`
--

CREATE TABLE `membership_code_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `membership_code_request_id` bigint(20) UNSIGNED NOT NULL,
  `membership_code_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(88, '2025_06_18_000000_create_members_table', 1),
(89, '2025_06_18_000001_create_users_table', 1),
(90, '2025_06_18_110030_create_voters_table', 1),
(91, '2025_06_18_110255_create_benefits_table', 1),
(92, '2025_06_18_110337_create_benefit_member_table', 1),
(93, '2025_06_18_110359_create_loans_table', 1),
(94, '2025_06_18_115815_create_wallets_table', 1),
(95, '2025_06_19_005439_add_wallet_id_to_wallets_table', 1),
(96, '2025_06_19_020350_create_cash_in_requests_table', 1),
(97, '2025_06_19_034014_alter_status_column_in_loans_table', 1),
(98, '2025_06_19_035728_add_term_months_to_loans_table', 1),
(99, '2025_06_19_040132_create_loan_payments_table', 1),
(100, '2025_06_19_040133_add_payment_fields_to_loan_payments_table', 1),
(101, '2025_06_19_040134_add_payment_fields_to_loan_payments_table_fixed', 1),
(102, '2025_06_19_040135_add_approval_fields_to_loans_table', 1),
(103, '2025_06_19_090214_add_purpose_to_loans_table', 1),
(104, '2025_06_29_013134_add_user_id_to_wallets_table', 1),
(105, '2025_06_29_013643_add_user_id_to_wallets_table', 1),
(106, '2025_06_29_014103_make_member_id_nullable_on_wallets_table', 1),
(107, '2025_06_29_115442_create_membership_codes_table', 1),
(108, '2025_06_29_125724_add_address_to_members_table', 1),
(109, '2025_06_30_150111_create_reward_programs_table', 1),
(110, '2025_06_30_150225_create_reward_winners_table', 1),
(111, '2025_06_30_151341_add_winner_id_to_reward_programs_table', 1),
(112, '2025_06_30_154307_add_foreign_key_to_reward_winners_table', 1),
(113, '2025_06_30_161911_add_seen_to_reward_winners_table', 1),
(114, '2025_06_30_170336_add_status_to_reward_winners_table', 1),
(115, '2025_06_30_172643_create_tickets_table', 1),
(116, '2025_06_30_174814_create_ticket_replies_table', 1),
(117, '2025_06_30_183242_add_member_id_to_ticket_replies_table', 1),
(118, '2025_06_30_194012_add_user_id_to_ticket_replies_table', 1),
(119, '2025_06_30_200510_add_wallet_id_to_wallets_table', 1),
(120, '2025_07_01_013959_add_loan_eligible_to_members_table', 1),
(121, '2025_07_01_034004_add_note_to_loan_payments_table', 1),
(122, '2025_07_02_031340_add_status_to_members_and_users', 1),
(123, '2025_07_02_064335_create_mobile_password_resets_table', 1),
(124, '2025_07_05_003513_add_proof_path_to_cash_in_requests_table', 1),
(125, '2025_07_05_102128_add_payment_method_to_cash_in_requests_table', 1),
(126, '2025_07_10_170926_create_referral_bonus_logs_table', 1),
(127, '2025_07_11_192351_create_products_table', 1),
(128, '2025_07_11_192501_create_orders_table', 1),
(129, '2025_07_11_192505_create_order_items_table', 1),
(130, '2025_07_11_192507_create_cashback_logs_table', 1),
(131, '2025_07_11_220911_create_categories_table', 1),
(132, '2025_07_11_220916_create_units_table', 1),
(133, '2025_07_11_220959_add_fields_to_products_table', 1),
(134, '2025_07_11_220960_add_created_by_to_products_table', 1),
(135, '2025_07_11_222707_add_stock_quantity_to_products_table', 1),
(136, '2025_07_12_105759_create_wallet_transactions_table', 1),
(137, '2025_07_12_111511_add_wallet_id_to_wallet_transactions_table', 1),
(138, '2025_07_13_061230_add_checkout_fields_to_orders_table', 1),
(139, '2025_07_13_064546_alter_orders_add_default_to_total_cashback', 1),
(140, '2025_07_13_115111_create_settings_table', 1),
(141, '2025_07_13_150011_add_discounts_to_products_table', 1),
(142, '2025_07_14_053443_add_type_to_wallets_table', 1),
(143, '2025_07_14_092645_add_source_to_cashback_logs_table', 1),
(144, '2025_07_14_093642_alter_wallet_transactions_add_cashback_type', 1),
(145, '2025_07_14_093953_add_cashback_given_to_orders_table', 1),
(146, '2025_07_15_180036_add_source_to_wallet_transactions_table', 1),
(147, '2025_07_15_200002_add_member_id_to_wallet_transactions_table', 1),
(148, '2025_07_17_232934_add_cashback_amount_to_order_items_table', 1),
(149, '2025_07_25_123112_create_referral_configurations_table', 1),
(150, '2025_07_25_134817_add_cashback_configuration_to_products_table', 1),
(151, '2025_07_25_135543_add_level_and_product_id_to_cashback_logs_table', 1),
(152, '2025_07_25_160111_add_status_to_order_items_table', 1),
(153, '2025_07_25_162730_add_promo_fields_to_orders_table', 1),
(154, '2025_07_27_091821_add_image_to_categories_table', 1),
(155, '2025_07_30_180255_create_device_tokens_table', 1),
(156, '2025_07_30_182526_create_sms_logs_table', 1),
(157, '2025_07_31_000000_migrate_existing_members_assign_codes', 1),
(158, '2025_08_06_141552_create_membership_code_requests_table', 1),
(159, '2025_08_06_142342_add_reservation_fields_to_membership_codes_table', 1),
(160, '2025_08_06_152422_create_notifications_table', 1),
(161, '2025_08_06_160000_add_reserved_codes_count_to_membership_code_requests_table', 1),
(162, '2025_08_06_161000_create_membership_code_request_items_table', 1),
(163, '2025_08_11_232849_create_aliexpress_tokens_table', 1),
(164, '2025_08_12_010112_create_sessions_table', 1),
(165, '2025_08_16_120608_create_sessions_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `mobile_password_resets`
--

CREATE TABLE `mobile_password_resets` (
  `mobile_number` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `notifiable_type` varchar(255) DEFAULT NULL,
  `notifiable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_cashback` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `cashback_given` tinyint(1) NOT NULL DEFAULT 0,
  `promo_code` varchar(255) DEFAULT NULL,
  `promo_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `delivery_type` varchar(255) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `gcash_note` varchar(255) DEFAULT NULL,
  `bank_note` varchar(255) DEFAULT NULL,
  `amount_sent` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `cashback_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cashback` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('mrcabandez@gmail.com', '$2y$10$jsDu0VnheN/5.RzuQZOGKeJcahCxq7Nae.M.muK9J5PKMux17x7Zi', '2025-08-11 05:23:50');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cashback_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cashback_max_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `cashback_level_bonuses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cashback_level_bonuses`)),
  `discount_value` decimal(8,2) DEFAULT NULL,
  `discount_type` enum('flat','percent') DEFAULT NULL,
  `promo_code` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `cashback_amount`, `cashback_max_level`, `cashback_level_bonuses`, `discount_value`, `discount_type`, `promo_code`, `created_by`, `stock_quantity`, `image`, `active`, `created_at`, `updated_at`, `thumbnail`, `gallery`, `category_id`, `unit_id`, `attributes`) VALUES
(1, 'UGREEN 19*12cm Pouch Bag for Power Bank TWS Bluetooth Earbuds Flannel Bag Mobile Phone Accessories Portable Waterproof Drawstring Protection Bag', 'UGREEN 19*12cm Pouch Bag for Power Bank TWS Bluetooth Earbuds Flannel Bag Mobile Phone Accessories Portable Waterproof Drawstring Protection Bag', 85.00, 1.00, 3, NULL, 10.00, 'flat', 'PROMO10', 1, 3, NULL, 1, '2025-07-16 05:16:35', '2025-07-18 19:12:41', 'products/thumbnails/E7FpFtypLVdUsY6oiNLYoPKSfj53ed9gX5PqRTjD.jpg', '[\"products\\/gallery\\/WmOGnOKHu8nbRX4DYnFKIMxqknqHYHImPMPKVlT5.jpg\",\"products\\/gallery\\/tlTL7MlI1QzxwmQeSrMl9CVGrfwEAsRDDblZxlTu.jpg\",\"products\\/gallery\\/4p8NfRU81TjawoP7HC3DvFB0dqF1fgFg0BEdIR1e.jpg\",\"products\\/gallery\\/W05Lp2jV84aPXTqoZF5eMJpKAIiwiIbaZYBdydyJ.jpg\",\"products\\/gallery\\/avIQwHFVPgoLWRA1oHclbUu2VD0eJTRVFPdBdLJX.jpg\"]', 8, 1, NULL),
(2, 'Turbo F08 USB Rechargeable Mini Handy Super 100 Wind Speed Function with LED Digital Display Fan', 'Turbo F08 USB Rechargeable Mini Handy Super 100 Wind Speed Function with LED Digital Display Fan', 100.00, 20.00, 3, '[]', 10.00, 'flat', 'PROMO10', 1, 2, NULL, 1, '2025-07-16 05:17:37', '2025-07-31 00:30:05', 'products/thumbnails/nKsl8gHOCEGAi3l1Z7ewcMacdTIEiNcSPr93v1Ve.jpg', '[\"products\\/gallery\\/P0RzduoshLgqJg0lh6xYzkUPJHvEYhKy3pQg45DB.jpg\",\"products\\/gallery\\/TGP5CWIQQCV0vxdr8bpmeBLjQyzLS7Sl7tBTejPN.jpg\",\"products\\/gallery\\/FKPfeOIVPAd17Td6tBuipUryqTKwBfyoGm7gNE9h.jpg\"]', 7, 10, NULL),
(3, 'Mini Fan USB Rechargeable Handheld Portable Fan 100 Speed Adjustment led Display battery level turbo Fan', 'Mini Fan USB Rechargeable Handheld Portable Fan 100 Speed Adjustment led Display battery level turbo Fan', 80.00, 10.00, 3, NULL, 10.00, 'flat', 'PROMO10', 1, 7, NULL, 1, '2025-07-17 02:12:40', '2025-07-22 01:26:01', 'products/thumbnails/TeM9teKlcqxjJvYXUqec3AiyVr2cZ81aqXbjpCgu.jpg', '[\"products\\/gallery\\/lIoqfFF9arNONAV3w8ehRbuhRyjBAdoAT9jpuOJZ.jpg\",\"products\\/gallery\\/my7ddCjSu9sPknD7ELallq0WfBNV6Odq8bXWFsws.jpg\",\"products\\/gallery\\/MtowV8d3uM44JXifZSbseFcuIm8kdHno0n0YJlN1.jpg\"]', 5, 1, NULL),
(4, 'Women Slim Strap Watch Square Dial Analog Quartz Wrist Watch Gift', 'Women Slim Strap Watch Square Dial Analog Quartz Wrist Watch Gift', 150.00, 10.00, 3, NULL, 10.00, 'flat', 'PROMO10', 1, 5, NULL, 1, '2025-07-17 02:14:42', '2025-07-17 20:44:57', 'products/thumbnails/tJq5irGJXFWgkXRIfk6tf4vWoIgGZQx9BUSjkuio.jpg', '[\"products\\/gallery\\/GG7HdbZ3BVaOs6CoNINqgEZf3GPqEXljiZiwkjin.jpg\",\"products\\/gallery\\/eVbFNhN7uIhB9kyw8NlFPfDCrSAEXoxuDi1AZFB9.jpg\",\"products\\/gallery\\/aqWQPCG1Bu2zpCSfIiIlWEbVesymF1HvBnNYuRx2.jpg\"]', 5, 1, NULL),
(5, 'Deep Cleansing Solid Green Tea Mask Stick Removal Oil Blackhead Moisturizing Facial Skin Care', 'Deep Cleansing Solid Green Tea Mask Stick Removal Oil Blackhead Moisturizing Facial Skin Care', 200.00, 10.00, 3, NULL, 10.00, 'flat', 'PROMO10', 1, 6, NULL, 1, '2025-07-17 02:16:44', '2025-07-19 15:11:03', 'products/thumbnails/ilYghDqfNdvMEFKnltFtthJ61ePwWJv3eCjmSd5x.jpg', '[\"products\\/gallery\\/gkY1LtZkhPjF9z68eMUACOY29OCuTqMxTPIrXI0N.jpg\",\"products\\/gallery\\/pkfwe75quiqIo7reLs4u2na4jRxfD1EPdZKFd91p.jpg\"]', 12, 2, NULL),
(6, 'Plain Summer Waffle Loose Casual Shorts For Men', 'Plain Summer Waffle Loose Casual Shorts For Men', 200.00, 20.00, 3, NULL, 10.00, 'flat', 'NEW25', 1, 5, NULL, 1, '2025-07-17 02:18:41', '2025-07-17 17:39:49', 'products/thumbnails/4lz3Naio9aK2GVHzwoOF8V5NFFph0fRYazmDXkYH.jpg', '[\"products\\/gallery\\/H28iTIqkJvwV5tcM0U4lCwXujbVJAoBwfTLjFFeN.jpg\",\"products\\/gallery\\/J00ueY14Ko0wLJ5oaUNLvOG4T5IxbPFAKt1lSK1P.jpg\",\"products\\/gallery\\/yHRu6o1wvIRoKG1I3aQ5jq0RHg3NfD00KjZKbHnE.jpg\"]', 4, 1, NULL),
(7, 'Original good quality Power Bank 10000mAh Mini Powerbank Built in Cables Portable fast charging', 'Original good quality Power Bank 10000mAh Mini Powerbank Built in Cables Portable fast charging', 150.00, 15.00, 3, NULL, 10.00, 'flat', 'PROMO10', 1, 8, NULL, 1, '2025-07-18 15:03:03', '2025-07-18 17:50:05', 'products/thumbnails/fxF2w4hWGijJ398CR51MrpydIc0AmaVT4NhFZKEt.jpg', '[\"products\\/gallery\\/mNFtHOQUf9qHQ39dYpx0dlXAq9JbF3JLJ9af31N9.jpg\",\"products\\/gallery\\/8XEHyEFgWzXhYVLXZiDqfxvANWv0BrzlStcqEzPY.jpg\",\"products\\/gallery\\/Ay04cGEeyz5eH0imcgRj9kEC1kfRqURRA27CdYl4.jpg\"]', 14, 1, NULL),
(8, 'Diamond Premium Long Grain Jasmine Rice (20kg/bag)', 'Jasmine Rice 20kg per sack\r\nDirect Import from Vietnam \r\n100% Organic', 850.00, 20.00, 3, NULL, NULL, NULL, 'NEW25', 1, 9, NULL, 1, '2025-07-22 05:38:33', '2025-07-28 23:15:49', 'products/thumbnails/1A2LN7WbWtJFlpfikLBWMx5Lrm3Z0cTZsX4b1dDs.jpg', '[\"products\\/gallery\\/4EJqe0XHRVGZY3QoK3xEKpDZLz4UjRXoNsZVwp3T.jpg\",\"products\\/gallery\\/HtybZsBENHqQ0Juk796CKgYI7f8ewPYRFKLQWc2f.jpg\",\"products\\/gallery\\/pJB2Z4uL1bZHN1MlxZIzYVJ4MvkzTFYafFTidyzl.jpg\"]', 3, 1, NULL),
(9, 'Canon Pixma MG2570S Printer (PG745/CL746)', 'Canon Pixma MG2570S Printer (PG745/CL746)\r\nCompact All-In-One for Low-Cost Printing\r\nAffordable All-In-One printer with basic printing, copying and scanning functions.\r\n\r\nPrint, Scan, Copy\r\nPrint Speed (A4): up to 8.0 / 4.0 ipm (mono/colour)\r\nUSB 2.0\r\nRecommended Monthly Print Volume: 10 - 80 pages\r\n\r\n\r\nNumber of Nozzles\r\n\r\nTotal 1,280 nozzles\r\n\r\nInk Cartridges (Type/Colours)\r\n\r\nPG-745S (Pigment Ink/Black), CL-746S (Dye-Based Ink/Colour)\r\n[Optional: PG-745, CL-746 / PG-745XL, CL-746XL]\r\n\r\nMaximum Print Resolution\r\n\r\n4,800 (horizontal)*1 x 600 (vertical) dpi\r\n\r\nPrint Speed*2 (Approx.)\r\n\r\nBased on ISO/IEC 24734\r\nClick here for summary report\r\nClick here for Document Print and Copy Speed Measurement Conditions\r\n\r\nDocument (ESAT/Simplex)\r\n\r\n8 / 4 ipm (mono/colour)\r\n\r\nPrint Width\r\n\r\nUp to 203.2 mm (8\")\r\n\r\nRecommended Printing Area\r\n\r\nTop margin: 31.6 mm\r\nBottom margin: 29.2 mm', 2998.00, 10.00, 3, NULL, NULL, NULL, NULL, 1, 10, NULL, 1, '2025-07-22 05:45:07', '2025-07-22 05:45:07', 'products/thumbnails/py7nxEfM22XqM5DdNYGgnzjUrLV73OHgc2WU0m70.jpg', '[\"products\\/gallery\\/qWOJLjVI5WHnHeHdgAN6aZy3sBw9knzD8u2E5KVV.jpg\",\"products\\/gallery\\/XhvGo7alJI4QmH4hvH18GX9UZDzcc6F51xpoZy6x.jpg\",\"products\\/gallery\\/tDrJQBDOGK7pfa63gI4OrWpKqnW0vgfIGBbC3fCt.jpg\",\"products\\/gallery\\/S5SILNahFy3zYYqTt9knluYKfAa8mLOsnqVAYSJj.jpg\"]', 13, 1, NULL),
(10, 'Brandnew 50\" NVISION Smart TV', 'Full HD Resolution: The Nvision N600-T43MA TV boasts a Full HD (1080p) resolution, providing crisp and detailed images with vibrant colors and sharp clarity. \r\nLED Backlighting: Equipped with LED backlighting technology, this TV delivers enhanced brightness and contrast levels while consuming less power compared to traditional LCD TVs. LED backlighting ensures energy efficiency and a more vibrant picture quality.\r\nMultiple Connectivity Options: The N600-T43MA TV features multiple connectivity options, including HDMI, USB, VGA, AV input, and RF input, allowing users to connect various devices such as gaming consoles, Blu-ray players, streaming devices, and more, expanding entertainment possibilities.\r\nBuilt-in Tuner: With its built-in tuner, this TV enables users to access over-the-air broadcast channels without the need for an external set-top box. Users can enjoy watching their favorite local channels with clear reception and minimal hassle.\r\nSlim Design: The Nvision N600-T43MA TV boasts a sleek and modern design that complements any living space.', 13000.00, 50.00, 3, NULL, NULL, NULL, NULL, 1, 10, NULL, 1, '2025-07-22 05:54:28', '2025-07-22 05:54:28', 'products/thumbnails/ZOYChy4AAJrZovOkmyxAtwfYmDS0R0paaVCqbmMg.jpg', '[\"products\\/gallery\\/wz4ZwOfEfZqpei1Y0Y4JLlADTFjWXIJbU6VnreqQ.jpg\",\"products\\/gallery\\/ar0HAxoDI7h0yhN44CO2QMCzLskLV3FEZWBsxW5n.jpg\",\"products\\/gallery\\/YGBVhK2ZKYMT8mBJJb8mlonfpxDnRKNBlgTIewWh.jpg\",\"products\\/gallery\\/UBWeruMWF6us9XzMFSreYFPNLQvTygUPFRQXipof.jpg\",\"products\\/gallery\\/COh9ctjbS2rgdfecJOpFy2MTDMk0U5ZUV6oGFS0s.jpg\"]', 3, 1, NULL),
(11, 'NVISION 55\" 4K UHD SMART ANDROID LED TV', 'Model: S800-55S1D\r\nDisplay Size: 55” LED\r\nResolution: 3840 x 2160\r\nWall-mount: 400mm x 300mm\r\nTV System: PAL, NTSC, SECAM\r\nSound System: I, D/K, B/G, M\r\nMusic Support: mp3, wma, m4a, aac\r\nPicture Support: jpg, jpeg, bmp, png, txt\r\nVideo Support: avi, mp4, ts/trp, mkv, mov, mpg, dat, vob, rm/rmvb\r\nInput Source: (1)RJ45, (1)VGA, (3)HDMI, (2)USB, AV in, RF in, Coaxial, MINI AV, MINI (YPbPr), Earphone in\r\nSmart System: Android 11.0, 1.5G + 8G\r\nPower Input: 100-240V ~ 50/60Hz\r\nConsumption: 70W\r\nGross Weight: 14.3Kg\r\nBox Size:1350mm x 150mm x 815mm', 21000.00, 100.00, 3, NULL, NULL, NULL, NULL, 1, 10, NULL, 1, '2025-07-22 05:59:04', '2025-07-22 05:59:04', 'products/thumbnails/G8QCyUvf7vqiUzTMxpZRq9ha58Cp9GfLdF6nBeze.jpg', '[\"products\\/gallery\\/4f1ktsamuSONtWSGH4LM8RrYDXqqcXpMxh8NyIQL.webp\",\"products\\/gallery\\/bylekX38ttNYPFtYaKeCYopXWMBCTm7mMOHPZDhy.jpg\",\"products\\/gallery\\/run4D7hp0gZrxRnLPRGlKtU0Bkf6qKVmfAr4LcCQ.jpg\"]', 3, 1, NULL),
(12, 'Pan/Tilt Home Security Wi-Fi Camera', 'High-Definition Video: The Tapo C200 features 1080p high-definition video, providing users with clear and detailed footage.\r\nPan and Tilt: The device offers 360° horizontal and 114° vertical range, enabling complete coverage of the area.\r\nNight Vision: With advanced night vision up to 30 feet, the Tapo C200 allows users to monitor their homes around the clock.\r\nMotion Detection and Alerts: The device uses smart motion detection technology to send instant notifications to your phone whenever movement is detected.\r\nTwo-Way Audio: The Tapo C200 comes equipped with a built-in microphone and speaker, allowing users to communicate with family, pets, or warn off intruders.\r\nLocal Storage: The device supports microSD cards up to 512GB for local storage, providing a secure and cost-effective way to store footage.\r\nPrivacy Mode: Users can enable Privacy Mode to stop recording and control when the camera is monitoring and when it\'s not.\r\nEasy Setup and Management: With the Tapo app, users can easily set up and manage their Tapo C200, and access live streaming and other controls.\r\nVoice Control: The Tapo C200 is compatible with Google Assistant and Amazon Alexa, offering hands-free control for users.\r\nSecure Encryption: The device uses advanced encryption and wireless protocols to ensure data privacy and secure communication between your phone and the device', 1450.00, 100.00, 3, NULL, NULL, NULL, NULL, 1, 10, NULL, 1, '2025-07-22 06:02:17', '2025-07-22 06:04:04', 'products/thumbnails/AKhZVCXR6JUS1QJ856ocud3JfYGIaS42x9Ny7IMX.jpg', '[\"products\\/gallery\\/fftkEyTaCcY3dXmKNGVEaegCPa4XPkZIvaSvgdtv.jpg\",\"products\\/gallery\\/fbbVr1byMQtgQy0844UJOVLtr25WO2zc7UCuFskD.jpg\",\"products\\/gallery\\/DiXquPu65SWNDbTF9kHX9Br1oWDNevGPE3MeLRJ5.jpg\",\"products\\/gallery\\/OdugxfRXRololc4Jc4aN1KPiqwhaz8MXp7kWEUgl.jpg\",\"products\\/gallery\\/LS1drhPmy0bCN4L7Iqkc97o4s7GrnAk3x9PQ3B7U.png\"]', 6, 1, NULL),
(13, 'Pan/Tilt AI Home Security Wi-Fi Camera', 'Seamless Privacy Control - Use the button on the product shell or Tapo app to easily open or close the privacy shield, giving you complete control over your private moments.\r\n\r\n2K QHD - When it comes to home security, details matter. With 2K QHD resolution, the Tapo C225 transcends beyond traditional FullHD 1080p quality to display finer details and incredibly clear videos.\r\n\r\nApple Homekit Supported - Along with Amazon Alexa and Google Assistant compatibility,Tapo C225 can also fully integrate into your Apple Home ecosystem for convenient hands-free operation.\r\n\r\nSmart Motion Tracking - With pan/tilt functionality and smart motion tracking technology with up to 120°/s rotating speed, precisely track and follow subjects, continuously keeping them within the camera’s field of view.\r\n\r\nColor Night Vision - The highly sensitive starlight sensor captures higher-quality images even in low-light conditions up to 30 ft.\r\n\r\nInvisible Infrared Mode - If the red IR LEDs prove to be a distraction while monitoring at night, switch to invisible IR mode to continue monitoring in low-light conditions without the disrupting red light, making it ideal for sleeping children and pets.\r\n\r\nLocal and Cloud Storage - Save recorded videos on a microSD Card (up to 512 GB, purchased separately) or use Tapo Care cloud storage services (subscribe separately).\r\n\r\nSharing Capabilities - Seamlessly forward videos you want to share to your social platforms.', 2950.00, 100.00, 3, NULL, NULL, NULL, NULL, 1, 10, NULL, 1, '2025-07-22 06:06:41', '2025-07-22 06:06:41', 'products/thumbnails/mRukDqYmAXzQ128TXbMOqQzCyN9DChT88lWcCjyj.jpg', '[\"products\\/gallery\\/aEHmPknyZtsa6BNRG33qjbdGOXDZZKSG5RHERJe0.jpg\",\"products\\/gallery\\/2TmFHqwCSIFP0y0Vg98g77i21NUbrgNsxYEAtunS.jpg\",\"products\\/gallery\\/6MZw8DfNOoHEl0s7MT6zLWAfgZilELpYc2nOOijC.jpg\"]', 6, 1, NULL),
(21, 'Women heart embroider wallet PU leather coin purse female short wallet for woman', 'BRAND NEW\r\n\r\nGood Quality\r\n\r\nDesigned for your convenience\r\n\r\nPlease allow 1-3cm differences in product measurement\r\n\r\nDue to different computer monitors/calibrations colors may vary slightly from the picture\r\n\r\n\r\nDELIVERY PERIOD: \r\n\r\n3-7 Days Manila area, 7-14 Days Provincial', 120.00, 20.00, 4, '[]', 20.00, 'flat', 'HELLO', 1, 10, NULL, 1, '2025-08-12 09:51:25', '2025-08-18 02:22:29', 'products/thumbnails/hCbzfpXDXnbzMjsG3RokwHA7NjgRlR4ZTbFooHlw.jpg', '[\"products\\/gallery\\/IUTNQoeoHfFaozguTn9qCQuUIm7sqytrIpKoKjxN.jpg\",\"products\\/gallery\\/MsNnVeoMn0y2dUZwo6q9wAln20zIWfS6KPNTbk1L.jpg\"]', 14, 1, '\"Red\"'),
(22, 'Korean Wallet for SALE Women\'s Short Wallet Korean Fashion Multi-function Buckle Multi-card Purse Ladies Wallet', 'Korean Wallet for SALE Women\'s Short Wallet Korean Fashion Multi-function Buckle Multi-card Purse Ladies Wallet #C219-1\r\nMaterial: PU leather\r\nSize: 14cm * 10cm * 2.5cm\r\nColor: Green, Blue, Black, Beige, Pink, Brown\r\nInterior: Note Compartment*1,\r\nInterior: Card Holder*8,\r\nInterior: Photo holder*2,\r\nInterior: Slot Pocket*3,\r\nInterior: Coin Pocket*2\r\nWallet closure: Hasp and Zipper', 160.00, 10.00, 2, '{\"1\":2}', NULL, 'flat', NULL, 1, 10, NULL, 1, '2025-08-12 09:57:17', '2025-08-18 02:23:47', 'products/thumbnails/UtdheBNkQtcMJ8pgsn1XXqQLPyZaxaLJo7nR0Cww.jpg', '[\"products\\/gallery\\/DtXRot25Ty4WEPNJmkNTuUk8CfYyqQeW0z6CZwkp.jpg\",\"products\\/gallery\\/APweizWTbSlL1LSlUczF92kFlLcTRYJsGcvLe3lh.jpg\",\"products\\/gallery\\/b1vZMA7XY9XA6Ze3d8f7YRcrU68bx3HuCK1w7o0e.jpg\",\"products\\/gallery\\/m4MnXtKoEGzmdoQNjlpKcEc0x7IIeqIZ1BADwItc.jpg\"]', 14, 1, NULL),
(23, 'BAVIN PC516 2.4A Mini Wall Charger + 1m Cable Smart 5.0 Chip Universal USB Adapter for iPh Micro Type-C Phones & Tablet', 'BAVIN PC516 2.4A Mini Wall Charger + 1m Cable Smart 5.0 Chip Universal USB Adapter for iPh Micro Type-C Phones & Tablet\r\n\r\n100% Authentic · Change of Mind · 30 Days Free Return · 1 Year Local Supplier Warranty', 185.00, 5.00, 3, '[]', NULL, NULL, NULL, 2, 10, NULL, 1, '2025-08-15 21:36:56', '2025-08-18 02:25:42', 'products/thumbnails/qLbpKx5tVoGWyqsgMPw3mD1BKFscAhM8lvxFziyf.jpg', '[\"products\\/gallery\\/GzHopYEf2MIQn9JJut1LySvwjXjb2iXtBWz8p0zN.jpg\",\"products\\/gallery\\/wgikR7eGKxfVjOqvaZ3bfUTLbYsqVco5JV6WRJMX.jpg\",\"products\\/gallery\\/vXxapxR8AjXqztM2OgqtsAalNQ51BY6zdPLBkmQM.jpg\",\"products\\/gallery\\/DpTDQStQtB64Nmdwk4cnUIZDf8OMjPX4HUjcDrOd.jpg\"]', 6, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `referral_bonus_logs`
--

CREATE TABLE `referral_bonus_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `referred_member_id` bigint(20) UNSIGNED NOT NULL,
  `level` tinyint(4) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_bonus_logs`
--

INSERT INTO `referral_bonus_logs` (`id`, `member_id`, `referred_member_id`, `level`, `amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 4, 7, 1, 25.00, 'Direct referral bonus from Luis Cabandez', '2025-08-12 00:32:12', '2025-08-12 00:32:12'),
(2, 1, 7, 2, 15.00, '2nd level referral bonus from Luis Cabandez', '2025-08-12 00:32:12', '2025-08-12 00:32:12'),
(3, 7, 8, 1, 25.00, 'Direct referral bonus from Susan Lacia', '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(4, 4, 8, 2, 15.00, '2nd level referral bonus from Susan Lacia', '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(5, 1, 8, 3, 10.00, '3rd level referral bonus from Susan Lacia', '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(6, 7, 9, 1, 25.00, 'Direct referral bonus from Bernie Baldesco', '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(7, 4, 9, 2, 15.00, '2nd level referral bonus from Bernie Baldesco', '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(8, 1, 9, 3, 10.00, '3rd level referral bonus from Bernie Baldesco', '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(9, 9, 10, 1, 25.00, 'Direct referral bonus from Cindy Bandao', '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(10, 7, 10, 2, 15.00, '2nd level referral bonus from Cindy Bandao', '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(11, 4, 10, 3, 10.00, '3rd level referral bonus from Cindy Bandao', '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(12, 7, 11, 1, 25.00, 'Direct referral bonus from Nor Umpar', '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(13, 4, 11, 2, 15.00, '2nd level referral bonus from Nor Umpar', '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(14, 1, 11, 3, 10.00, '3rd level referral bonus from Nor Umpar', '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(15, 11, 12, 1, 25.00, 'Direct referral bonus from Ariel Capili', '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(16, 7, 12, 2, 15.00, '2nd level referral bonus from Ariel Capili', '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(17, 4, 12, 3, 10.00, '3rd level referral bonus from Ariel Capili', '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(18, 7, 13, 1, 25.00, 'Direct referral bonus from Mary ann Olbez', '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(19, 4, 13, 2, 15.00, '2nd level referral bonus from Mary ann Olbez', '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(20, 1, 13, 3, 10.00, '3rd level referral bonus from Mary ann Olbez', '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(21, 7, 14, 1, 25.00, 'Direct referral bonus from Renz Licarte', '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(22, 4, 14, 2, 15.00, '2nd level referral bonus from Renz Licarte', '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(23, 1, 14, 3, 10.00, '3rd level referral bonus from Renz Licarte', '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(24, 7, 15, 1, 25.00, 'Direct referral bonus from Margie Palacio', '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(25, 4, 15, 2, 15.00, '2nd level referral bonus from Margie Palacio', '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(26, 1, 15, 3, 10.00, '3rd level referral bonus from Margie Palacio', '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(27, 15, 16, 1, 25.00, 'Direct referral bonus from Leah Perez', '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(28, 7, 16, 2, 15.00, '2nd level referral bonus from Leah Perez', '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(29, 4, 16, 3, 10.00, '3rd level referral bonus from Leah Perez', '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(30, 11, 17, 1, 25.00, 'Direct referral bonus from Melanie Guiday', '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(31, 7, 17, 2, 15.00, '2nd level referral bonus from Melanie Guiday', '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(32, 4, 17, 3, 10.00, '3rd level referral bonus from Melanie Guiday', '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(33, 7, 19, 1, 25.00, 'Direct referral bonus from John crisostomo Perez', '2025-08-16 18:40:20', '2025-08-16 18:40:20'),
(34, 4, 19, 2, 15.00, '2nd level referral bonus from John crisostomo Perez', '2025-08-16 18:40:20', '2025-08-16 18:40:20'),
(35, 1, 19, 3, 10.00, '3rd level referral bonus from John crisostomo Perez', '2025-08-16 18:40:20', '2025-08-16 18:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `referral_configurations`
--

CREATE TABLE `referral_configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_allocation` decimal(10,2) NOT NULL,
  `max_level` tinyint(3) UNSIGNED NOT NULL,
  `level_bonuses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`level_bonuses`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward_programs`
--

CREATE TABLE `reward_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `draw_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `winner_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward_winners`
--

CREATE TABLE `reward_winners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reward_program_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `drawn_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `excluded_until` date NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('unclaimed','redeemed','expired') NOT NULL DEFAULT 'unclaimed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7PbajZlAvrujJ1qhxCUCqZVGA7JMLW4AQfMwOEJE', 9171234567, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUEx4SkV1V1pHR0NzdFM4VDVtWk1nMjNDcEgyTXAzYkx4UjFFUUFGaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czoxMToiMDkxNzEyMzQ1NjciO30=', 1755318302);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sent_by` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_type` varchar(255) NOT NULL DEFAULT 'single',
  `recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`recipients`)),
  `message` text NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `total_recipients` int(11) NOT NULL DEFAULT 1,
  `successful_sends` int(11) NOT NULL DEFAULT 0,
  `failed_sends` int(11) NOT NULL DEFAULT 0,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `semaphore_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`semaphore_response`)),
  `message_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`message_ids`)),
  `campaign_name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','in_process','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `replied_by` varchar(255) NOT NULL,
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Piece', NULL, NULL),
(2, 'Kilogram', NULL, NULL),
(3, 'Liter', NULL, NULL),
(4, 'Box', NULL, NULL),
(5, 'Pack', NULL, NULL),
(6, 'Dozen', NULL, NULL),
(7, 'Set', NULL, NULL),
(8, 'Meter', NULL, NULL),
(9, 'Gram', NULL, NULL),
(10, 'Milliliter', NULL, NULL),
(11, 'Yard', NULL, NULL),
(12, 'Foot', NULL, NULL),
(13, 'Inch', NULL, NULL),
(14, 'Pound', NULL, NULL),
(15, 'Ounce', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Staff','Member') NOT NULL DEFAULT 'Member',
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `mobile_number`, `email`, `role`, `member_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `status`) VALUES
(1, 'System Administrator', '09171234567', 'mrcabandez@gmail.com', 'Admin', 1, '2025-08-10 06:11:43', '$2y$10$02cF4RCDC3YCrWxNvBVZcuKJzVTkFuwnVotHK7XADLumu68EIjfqe', NULL, '2025-08-10 06:11:43', '2025-08-11 05:28:39', 'Approved'),
(2, 'Staff One', '09171234568', 'staff1@example.com', 'Staff', 2, '2025-08-10 06:11:43', '$2y$10$o5dIY8z/LMHwJYuHU71Dk./yDusGz2wAim2xdOkRFcwPUGKoGrREa', NULL, '2025-08-10 06:11:43', '2025-08-12 09:12:32', 'Approved'),
(3, 'Staff Two', '09171234569', 'staff2@example.com', 'Staff', 3, '2025-08-10 06:11:43', '$2y$10$oQjehfOyK8oreRBKNHD67usUr3aezEZmPDQQYlgj106kXQ.zE53ci', NULL, '2025-08-10 06:11:43', '2025-08-12 09:12:09', 'Approved'),
(4, 'Chief Executive officer', '09177260180', 'ceo@amigostayo.com', 'Member', 4, '2025-08-10 06:11:43', '$2y$10$Co24zh4bnT5Dpcn.H9gPkOUWwSQhg5A5RuBMbeiJHk/kioy1nPS9K', NULL, '2025-08-10 06:11:43', '2025-08-11 05:29:36', 'Approved'),
(5, 'Chief Finance officer', '09191111111', 'ruth@amigostayo.com', 'Member', 5, '2025-08-10 06:11:43', '$2y$10$TGqpEIT3LndDEWI37qhUIeFaYJDgWMx9KmkANKT2a3PRCimCYWXS.', NULL, '2025-08-10 06:11:43', '2025-08-11 05:30:06', 'Approved'),
(6, 'Chief Operations officer', '09192222222', 'blissy@amigostayo.com', 'Member', 6, '2025-08-10 06:11:43', '$2y$10$yft53bCWelNFjsfZx4gqN.uahBW64iuev1N1NGTA3RIQP3N0II9Bu', NULL, '2025-08-10 06:11:43', '2025-08-11 05:30:53', 'Approved'),
(7, 'Luis Cabandez', '09193333333', '09193333333@amigostayo.com', 'Member', 7, NULL, '$2y$10$i3s/LwdN0jviz5aIL2cu0.O0mbPUJDPVr7M2ZsdSMEtZh6VAUWjia', NULL, '2025-08-11 05:32:55', '2025-08-12 00:32:12', 'Approved'),
(8, 'Susan Lacia', '09774462817', '09774462817@amigostayo.com', 'Member', 8, NULL, '$2y$10$rqvG14m.EWvIUX9.5a7XYOnFEFcf.ZkVeQtu34llSjeQm6WDXvTXu', NULL, '2025-08-11 23:32:59', '2025-08-12 00:33:24', 'Approved'),
(9, 'Bernie Baldesco', '09465935416', '09465935416@amigostayo.com', 'Member', 9, NULL, '$2y$10$CRo.4zp.C2Z7UVM68.Qf1.2NExlThrXHWL7bBkkfLY2T5eXbIUKy6', NULL, '2025-08-12 01:34:01', '2025-08-12 01:34:01', 'Approved'),
(10, 'Cindy Bandao', '09914528619', '09914528619@amigostayo.com', 'Member', 10, NULL, '$2y$10$cxF0MLyoVgsG9BLc20KOMOZzVvvrQXa9QvsiRVjvLDnbbcD1tCWbe', NULL, '2025-08-12 01:35:12', '2025-08-12 01:35:12', 'Approved'),
(11, 'Nor Umpar', '09099200018', '09099200018@amigostayo.com', 'Member', 11, NULL, '$2y$10$G/ekTmKjScSczCaC4fAGg.JBOk2lDe28QqMD.s7sbMduYr0SRYFpq', NULL, '2025-08-12 01:36:46', '2025-08-12 01:36:46', 'Approved'),
(12, 'Ariel Capili', '09171852313', '09171852313@amigostayo.com', 'Member', 12, NULL, '$2y$10$NFjCVYQSAiaYixmAYVGOxeY.kCJMms8/rkkqULPSyQmTbEftL3vrq', NULL, '2025-08-12 01:37:30', '2025-08-12 01:37:30', 'Approved'),
(13, 'Mary ann Olbez', '09264663844', '09264663844@amigostayo.com', 'Member', 13, NULL, '$2y$10$BKzEY1zmCJ2WWuxm8vm.8OomnnV8ktg6R0HjevV8C4yzfLhreeCVO', NULL, '2025-08-12 01:38:11', '2025-08-12 01:38:11', 'Approved'),
(14, 'Renz Licarte', '09763632594', '09763632594@amigostayo.com', 'Member', 14, NULL, '$2y$10$XAJVYKmWo8iZ1fb2iDO/guvrIwt6yZNergZaowCmFhmZ3E.feU6ly', NULL, '2025-08-12 01:38:49', '2025-08-12 01:38:49', 'Approved'),
(15, 'Margie Palacio', '09670891993', '09670891993@amigostayo.com', 'Member', 15, NULL, '$2y$10$DqGVGvSZMR9BnffWtJyiOeatHaGHDpNT4wdCNBB1C25Z5l5/Wolw2', NULL, '2025-08-12 01:39:25', '2025-08-12 01:39:25', 'Approved'),
(16, 'Leah Perez', '09198649321', '09198649321@amigostayo.com', 'Member', 16, NULL, '$2y$10$C.FkBOqO5JfchvetwTHV3OyKKe8tj4/XoK4dr9lGnoJEqbVGXfkVW', NULL, '2025-08-12 01:40:14', '2025-08-12 01:40:14', 'Approved'),
(17, 'Melanie Guiday', '09165210706', '09165210706@amigostayo.com', 'Member', 17, NULL, '$2y$10$5i.vfkuXLxRWVH9aouB2nuiNIQ0TkkMBRVsCVNzrMakIo2zeB8Af.', NULL, '2025-08-12 01:41:09', '2025-08-12 01:41:09', 'Approved'),
(18, 'Abel Moya', '09177732303', '09177732303@amigostayo.com', 'Member', 18, NULL, '$2y$10$gQU0M8k7EUKPLXC.C6yuC.L5V9.BikjRPr5aeXov1aKOkEW2b4Sr.', NULL, '2025-08-14 00:29:41', '2025-08-14 00:37:37', 'Approved'),
(19, 'John crisostomo Perez', '09608511401', '09608511401@amigostayo.com', 'Member', 19, NULL, '$2y$10$RDkczqh/vwEUWm721AelTeEHJUVOkvsCF6hRhB/h0xzoenKS4ABjq', NULL, '2025-08-16 18:31:35', '2025-08-16 18:40:20', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `First_name` varchar(255) NOT NULL,
  `Last_name` varchar(255) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Precinct` varchar(255) DEFAULT NULL,
  `Class` varchar(255) DEFAULT NULL,
  `Region` varchar(255) DEFAULT NULL,
  `Province` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `Barangay` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'main',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `wallet_id`, `type`, `user_id`, `member_id`, `balance`, `created_at`, `updated_at`) VALUES
(1, 'WALLET-6898381F41683', 'main', NULL, 1, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(2, 'WALLET-6898381F41687', 'cashback', NULL, 1, 85.00, '2025-08-10 06:11:43', '2025-08-16 18:40:20'),
(3, 'WALLET-6898381F52CFA', 'main', NULL, 2, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(4, 'WALLET-6898381F52CFE', 'cashback', NULL, 2, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(5, 'WALLET-6898381F635F1', 'main', NULL, 3, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(6, 'WALLET-6898381F635F3', 'cashback', NULL, 3, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(7, 'WALLET-6898381F73A9D', 'main', NULL, 4, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(8, 'WALLET-6898381F73A9F', 'cashback', NULL, 4, 170.00, '2025-08-10 06:11:43', '2025-08-16 18:40:20'),
(9, 'WALLET-6898381F831C8', 'main', NULL, 5, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(10, 'WALLET-6898381F831CB', 'cashback', NULL, 5, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(11, 'WALLET-6898381F93F0D', 'main', NULL, 6, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(12, 'WALLET-6898381F93F0F', 'cashback', NULL, 6, 0.00, '2025-08-10 06:11:43', '2025-08-10 06:11:43'),
(13, 'WALLET-6898AD971FF91', 'main', NULL, 7, 0.00, '2025-08-11 05:32:55', '2025-08-11 05:32:55'),
(14, 'WALLET-6898AD971FF93', 'cashback', NULL, 7, 235.00, '2025-08-11 05:32:55', '2025-08-16 18:40:20'),
(15, 'WALLET-6899AABB912D0', 'main', NULL, 8, 0.00, '2025-08-11 23:32:59', '2025-08-11 23:32:59'),
(16, 'WALLET-6899AABB912D4', 'cashback', NULL, 8, 0.00, '2025-08-11 23:32:59', '2025-08-11 23:32:59'),
(17, 'WALLET-6899C719DB90B', 'main', NULL, 9, 0.00, '2025-08-12 01:34:01', '2025-08-12 01:34:01'),
(18, 'WALLET-6899C719DB910', 'cashback', NULL, 9, 25.00, '2025-08-12 01:34:01', '2025-08-12 01:35:12'),
(19, 'WALLET-6899C76007FCD', 'main', NULL, 10, 0.00, '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(20, 'WALLET-6899C76007FD0', 'cashback', NULL, 10, 0.00, '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(21, 'WALLET-6899C7BED9B69', 'main', NULL, 11, 0.00, '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(22, 'WALLET-6899C7BED9B6B', 'cashback', NULL, 11, 50.00, '2025-08-12 01:36:46', '2025-08-12 01:41:09'),
(23, 'WALLET-6899C7EACD456', 'main', NULL, 12, 0.00, '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(24, 'WALLET-6899C7EACD45C', 'cashback', NULL, 12, 0.00, '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(25, 'WALLET-6899C81367248', 'main', NULL, 13, 0.00, '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(26, 'WALLET-6899C81367250', 'cashback', NULL, 13, 0.00, '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(27, 'WALLET-6899C839D5D32', 'main', NULL, 14, 0.00, '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(28, 'WALLET-6899C839D5D36', 'cashback', NULL, 14, 0.00, '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(29, 'WALLET-6899C85DC271B', 'main', NULL, 15, 0.00, '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(30, 'WALLET-6899C85DC2724', 'cashback', NULL, 15, 25.00, '2025-08-12 01:39:25', '2025-08-12 01:40:14'),
(31, 'WALLET-6899C88DE8D01', 'main', NULL, 16, 0.00, '2025-08-12 01:40:13', '2025-08-12 01:40:13'),
(32, 'WALLET-6899C88DE8D05', 'cashback', NULL, 16, 0.00, '2025-08-12 01:40:13', '2025-08-12 01:40:13'),
(33, 'WALLET-6899C8C50D267', 'main', NULL, 17, 0.00, '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(34, 'WALLET-6899C8C50D26B', 'cashback', NULL, 17, 0.00, '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(35, 'WALLET-689C5B0539413', 'main', NULL, 18, 0.00, '2025-08-14 00:29:41', '2025-08-14 00:29:41'),
(36, 'WALLET-689C5B0539416', 'cashback', NULL, 18, 0.00, '2025-08-14 00:29:41', '2025-08-14 00:29:41'),
(37, 'WALLET-689FFB979960F', 'main', NULL, 19, 0.00, '2025-08-16 18:31:35', '2025-08-16 18:31:35'),
(38, 'WALLET-689FFB9799613', 'cashback', NULL, 19, 0.00, '2025-08-16 18:31:35', '2025-08-16 18:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('credit','debit','transfer','payment','cashback') DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `related_member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `wallet_id`, `member_id`, `type`, `amount`, `source`, `description`, `notes`, `related_member_id`, `created_at`, `updated_at`) VALUES
(1, 8, 4, 'credit', 25.00, NULL, 'Direct referral bonus from Luis Cabandez', NULL, NULL, '2025-08-12 00:32:12', '2025-08-12 00:32:12'),
(2, 2, 1, 'credit', 15.00, NULL, '2nd level referral bonus from Luis Cabandez', NULL, NULL, '2025-08-12 00:32:12', '2025-08-12 00:32:12'),
(3, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Susan Lacia', NULL, NULL, '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(4, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Susan Lacia', NULL, NULL, '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(5, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Susan Lacia', NULL, NULL, '2025-08-12 00:33:24', '2025-08-12 00:33:24'),
(6, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Bernie Baldesco', NULL, NULL, '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(7, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Bernie Baldesco', NULL, NULL, '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(8, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Bernie Baldesco', NULL, NULL, '2025-08-12 01:34:02', '2025-08-12 01:34:02'),
(9, 18, 9, 'credit', 25.00, NULL, 'Direct referral bonus from Cindy Bandao', NULL, NULL, '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(10, 14, 7, 'credit', 15.00, NULL, '2nd level referral bonus from Cindy Bandao', NULL, NULL, '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(11, 8, 4, 'credit', 10.00, NULL, '3rd level referral bonus from Cindy Bandao', NULL, NULL, '2025-08-12 01:35:12', '2025-08-12 01:35:12'),
(12, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Nor Umpar', NULL, NULL, '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(13, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Nor Umpar', NULL, NULL, '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(14, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Nor Umpar', NULL, NULL, '2025-08-12 01:36:46', '2025-08-12 01:36:46'),
(15, 22, 11, 'credit', 25.00, NULL, 'Direct referral bonus from Ariel Capili', NULL, NULL, '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(16, 14, 7, 'credit', 15.00, NULL, '2nd level referral bonus from Ariel Capili', NULL, NULL, '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(17, 8, 4, 'credit', 10.00, NULL, '3rd level referral bonus from Ariel Capili', NULL, NULL, '2025-08-12 01:37:30', '2025-08-12 01:37:30'),
(18, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Mary ann Olbez', NULL, NULL, '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(19, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Mary ann Olbez', NULL, NULL, '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(20, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Mary ann Olbez', NULL, NULL, '2025-08-12 01:38:11', '2025-08-12 01:38:11'),
(21, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Renz Licarte', NULL, NULL, '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(22, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Renz Licarte', NULL, NULL, '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(23, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Renz Licarte', NULL, NULL, '2025-08-12 01:38:49', '2025-08-12 01:38:49'),
(24, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from Margie Palacio', NULL, NULL, '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(25, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from Margie Palacio', NULL, NULL, '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(26, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from Margie Palacio', NULL, NULL, '2025-08-12 01:39:25', '2025-08-12 01:39:25'),
(27, 30, 15, 'credit', 25.00, NULL, 'Direct referral bonus from Leah Perez', NULL, NULL, '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(28, 14, 7, 'credit', 15.00, NULL, '2nd level referral bonus from Leah Perez', NULL, NULL, '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(29, 8, 4, 'credit', 10.00, NULL, '3rd level referral bonus from Leah Perez', NULL, NULL, '2025-08-12 01:40:14', '2025-08-12 01:40:14'),
(30, 22, 11, 'credit', 25.00, NULL, 'Direct referral bonus from Melanie Guiday', NULL, NULL, '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(31, 14, 7, 'credit', 15.00, NULL, '2nd level referral bonus from Melanie Guiday', NULL, NULL, '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(32, 8, 4, 'credit', 10.00, NULL, '3rd level referral bonus from Melanie Guiday', NULL, NULL, '2025-08-12 01:41:09', '2025-08-12 01:41:09'),
(33, 14, 7, 'credit', 25.00, NULL, 'Direct referral bonus from John crisostomo Perez', NULL, NULL, '2025-08-16 18:40:20', '2025-08-16 18:40:20'),
(34, 8, 4, 'credit', 15.00, NULL, '2nd level referral bonus from John crisostomo Perez', NULL, NULL, '2025-08-16 18:40:20', '2025-08-16 18:40:20'),
(35, 2, 1, 'credit', 10.00, NULL, '3rd level referral bonus from John crisostomo Perez', NULL, NULL, '2025-08-16 18:40:20', '2025-08-16 18:40:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `benefits`
--
ALTER TABLE `benefits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `benefit_member`
--
ALTER TABLE `benefit_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benefit_member_benefit_id_foreign` (`benefit_id`),
  ADD KEY `benefit_member_member_id_foreign` (`member_id`);

--
-- Indexes for table `cashback_logs`
--
ALTER TABLE `cashback_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cashback_logs_member_id_foreign` (`member_id`),
  ADD KEY `cashback_logs_order_id_foreign` (`order_id`),
  ADD KEY `cashback_logs_product_id_foreign` (`product_id`);

--
-- Indexes for table `cash_in_requests`
--
ALTER TABLE `cash_in_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_in_requests_member_id_foreign` (`member_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_tokens_user_id_device_token_unique` (`user_id`,`device_token`),
  ADD KEY `device_tokens_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `device_tokens_device_type_index` (`device_type`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_member_id_foreign` (`member_id`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_payments_loan_id_foreign` (`loan_id`),
  ADD KEY `loan_payments_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_mobile_number_unique` (`mobile_number`),
  ADD KEY `members_sponsor_id_index` (`sponsor_id`),
  ADD KEY `members_voter_id_index` (`voter_id`);

--
-- Indexes for table `membership_codes`
--
ALTER TABLE `membership_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_codes_code_unique` (`code`),
  ADD KEY `membership_codes_used_by_foreign` (`used_by`),
  ADD KEY `membership_codes_reserved_by_foreign` (`reserved_by`);

--
-- Indexes for table `membership_code_requests`
--
ALTER TABLE `membership_code_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `membership_code_requests_member_id_foreign` (`member_id`);

--
-- Indexes for table `membership_code_request_items`
--
ALTER TABLE `membership_code_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `membership_code_request_items_membership_code_request_id_foreign` (`membership_code_request_id`),
  ADD KEY `membership_code_request_items_membership_code_id_foreign` (`membership_code_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobile_password_resets`
--
ALTER TABLE `mobile_password_resets`
  ADD KEY `mobile_password_resets_mobile_number_index` (`mobile_number`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_member_id_foreign` (`member_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_unit_id_foreign` (`unit_id`),
  ADD KEY `products_created_by_foreign` (`created_by`);

--
-- Indexes for table `referral_bonus_logs`
--
ALTER TABLE `referral_bonus_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_bonus_logs_member_id_foreign` (`member_id`),
  ADD KEY `referral_bonus_logs_referred_member_id_foreign` (`referred_member_id`);

--
-- Indexes for table `referral_configurations`
--
ALTER TABLE `referral_configurations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reward_programs`
--
ALTER TABLE `reward_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reward_programs_winner_id_foreign` (`winner_id`);

--
-- Indexes for table `reward_winners`
--
ALTER TABLE `reward_winners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reward_winners_member_id_foreign` (`member_id`),
  ADD KEY `fk_rwinners_program` (`reward_program_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sms_logs_recipient_type_status_index` (`recipient_type`,`status`),
  ADD KEY `sms_logs_sent_at_index` (`sent_at`),
  ADD KEY `sms_logs_sent_by_index` (`sent_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_member_id_foreign` (`member_id`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_replies_ticket_id_foreign` (`ticket_id`),
  ADD KEY `ticket_replies_member_id_foreign` (`member_id`),
  ADD KEY `ticket_replies_user_id_foreign` (`user_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_name_unique` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_mobile_number_unique` (`mobile_number`),
  ADD KEY `users_member_id_index` (`member_id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_wallet_id_unique` (`wallet_id`),
  ADD KEY `wallets_member_id_foreign` (`member_id`),
  ADD KEY `wallets_user_id_foreign` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_transactions_member_id_foreign` (`member_id`),
  ADD KEY `wallet_transactions_related_member_id_foreign` (`related_member_id`),
  ADD KEY `wallet_transactions_wallet_id_foreign` (`wallet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `benefit_member`
--
ALTER TABLE `benefit_member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashback_logs`
--
ALTER TABLE `cashback_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_in_requests`
--
ALTER TABLE `cash_in_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `membership_codes`
--
ALTER TABLE `membership_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `membership_code_requests`
--
ALTER TABLE `membership_code_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership_code_request_items`
--
ALTER TABLE `membership_code_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `referral_bonus_logs`
--
ALTER TABLE `referral_bonus_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `referral_configurations`
--
ALTER TABLE `referral_configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward_programs`
--
ALTER TABLE `reward_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward_winners`
--
ALTER TABLE `reward_winners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `benefit_member`
--
ALTER TABLE `benefit_member`
  ADD CONSTRAINT `benefit_member_benefit_id_foreign` FOREIGN KEY (`benefit_id`) REFERENCES `benefits` (`id`),
  ADD CONSTRAINT `benefit_member_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `cashback_logs`
--
ALTER TABLE `cashback_logs`
  ADD CONSTRAINT `cashback_logs_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cashback_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cashback_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `cash_in_requests`
--
ALTER TABLE `cash_in_requests`
  ADD CONSTRAINT `cash_in_requests_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD CONSTRAINT `device_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD CONSTRAINT `loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `membership_codes`
--
ALTER TABLE `membership_codes`
  ADD CONSTRAINT `membership_codes_reserved_by_foreign` FOREIGN KEY (`reserved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `membership_codes_used_by_foreign` FOREIGN KEY (`used_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `membership_code_requests`
--
ALTER TABLE `membership_code_requests`
  ADD CONSTRAINT `membership_code_requests_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `membership_code_request_items`
--
ALTER TABLE `membership_code_request_items`
  ADD CONSTRAINT `membership_code_request_items_membership_code_id_foreign` FOREIGN KEY (`membership_code_id`) REFERENCES `membership_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_code_request_items_membership_code_request_id_foreign` FOREIGN KEY (`membership_code_request_id`) REFERENCES `membership_code_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

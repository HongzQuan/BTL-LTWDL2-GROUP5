-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 03, 2026 lúc 07:19 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ql_nhahang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `table_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `guests` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `restaurant_id`, `table_id`, `booking_date`, `booking_time`, `guests`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-05-26', '18:30:00', 2, NULL, 'pending', '2026-05-26 00:12:34', '2026-05-26 00:12:34'),
(2, 1, 1, 2, '2026-05-26', '18:30:00', 2, NULL, 'cancelled', '2026-05-26 00:19:37', '2026-05-26 00:26:23'),
(3, 1, 21, 37, '2026-05-26', '18:30:00', 2, NULL, 'pending', '2026-05-26 00:32:57', '2026-05-26 00:32:57'),
(8, 4, 21, 37, '2026-05-28', '18:30:00', 2, NULL, 'cancelled', '2026-05-28 09:47:34', '2026-05-28 09:48:21'),
(9, 4, 16, 32, '2026-05-30', '18:30:00', 2, NULL, 'pending', '2026-05-28 09:49:15', '2026-05-28 09:49:15'),
(10, 4, 16, 31, '2026-05-28', '23:30:00', 2, NULL, 'pending', '2026-05-28 09:51:45', '2026-05-28 09:51:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('login.127.0.0.1', 'i:1;', 1779729269),
('login.127.0.0.1:timer', 'i:1779729269;', 1779729269);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Cơm & Cháo', 'com-chao', 'Quán cơm bình dân, cơm văn phòng, cháo, xôi', NULL, '2026-05-23 23:30:09', '2026-05-23 23:30:09'),
(2, 'Phở & Bún & Mì', 'pho-bun-mi', 'Các món nước: phở, bún bò, bún chả, mì quảng', NULL, '2026-05-23 23:30:24', '2026-05-23 23:30:24'),
(3, 'Hải sản', 'hai-san', 'Nhà hàng hải sản tươi sống, seafood cao cấp', NULL, '2026-05-23 23:30:36', '2026-05-23 23:30:36'),
(4, 'Lẩu & Nướng BBQ', 'lau-nuong-bbq', 'Lẩu các loại, nướng than hoa, BBQ Hàn Quốc', NULL, '2026-05-23 23:30:49', '2026-05-23 23:30:49'),
(5, 'Nhà hàng Âu – Á', 'nha-hang-au-a', 'Fine dining, Fusion, món Tây, Nhật, Hàn, Thái', NULL, '2026-05-23 23:30:58', '2026-05-23 23:30:58'),
(6, 'Chay & Healthy', 'chay-healthy', 'Ăn chay, thuần chay, salad, món ăn lành mạnh', NULL, '2026-05-23 23:31:08', '2026-05-23 23:31:08'),
(7, 'Quán nhậu & Bia', 'quan-nhau-bia', 'Nhậu nhẹt, bia hơi, đồ nhắm, ăn vặt buổi tối', NULL, '2026-05-23 23:31:18', '2026-05-23 23:31:18'),
(8, 'Buffet', 'buffet', 'Buffet lẩu, buffet nướng, buffet hải sản', NULL, '2026-05-23 23:31:36', '2026-05-25 22:59:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
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
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('khai_vi','mon_chinh','trang_mieng','do_uong','buffet') NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `image`, `type`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bún chả đặc biệt', NULL, 65000.00, 'uploads/menus/heMWbSEW6rmUuNY8r3Gtd41fB9jeTuPqznxhW68Q.jpg', 'mon_chinh', 1, '2026-05-23 17:24:17', '2026-05-23 17:24:17'),
(2, 1, 'Bún chả thường', NULL, 50000.00, 'uploads/menus/7D7wXyEhMPWNvKE8qvdBfHErw9eJzqqbjlI39yOk.jpg', 'mon_chinh', 1, '2026-05-24 03:21:56', '2026-05-24 03:21:56'),
(3, 1, 'Nem rán', NULL, 30000.00, 'uploads/menus/qhltD9ZWEtnUJkLwBxKw3Z16UkfsEMIsFSbzqwJE.jpg', 'khai_vi', 1, '2026-05-24 03:55:17', '2026-05-24 03:55:17'),
(4, 1, 'Bún chả cá', NULL, 55000.00, 'uploads/menus/mvT1rwl70jCOLIoSVBFSIf2af1dmAhPnAfUOGHnL.jpg', 'mon_chinh', 1, '2026-05-24 03:57:40', '2026-05-24 03:57:40'),
(5, 1, 'Nước chanh tươi', NULL, 20000.00, 'uploads/menus/nALP8G2fTSpBgewzt7i60aFSqFnfRW6av8jGzXAT.jpg', 'do_uong', 1, '2026-05-24 04:01:45', '2026-05-24 04:01:45'),
(6, 2, 'Chả cá đặc biệt (1 người)', NULL, 180000.00, 'uploads/menus/cyr8bFQ2sAfzUlPDmVS0xfisE0ldHvi2z3GJzTQT.jpg', 'mon_chinh', 1, '2026-05-24 17:16:49', '2026-05-24 17:16:49'),
(7, 2, 'Chả cá đặc biệt (2 người)', NULL, 340000.00, 'uploads/menus/FZg2B7YuiMlSFUKbLFHI8wMjKBAG9ldZWxIe5xwn.jpg', 'mon_chinh', 1, '2026-05-24 17:18:19', '2026-05-24 17:18:19'),
(8, 2, 'Bún ăn kèm', NULL, 15000.00, 'uploads/menus/lIW93Zg2vZeJj8fuYR0LWmMaAOF1DkBpzVnXAZ3l.jpg', 'khai_vi', 1, '2026-05-24 17:20:43', '2026-05-24 17:20:43'),
(9, 2, 'Mắm tôm', NULL, 10000.00, 'uploads/menus/uSGX98tMsOU5MTCS3biB22tCVHJWJRw0HbDs4gpe.jpg', 'khai_vi', 1, '2026-05-24 17:22:30', '2026-05-24 17:22:30'),
(10, 2, 'Bia Hà Nội', NULL, 25000.00, 'uploads/menus/qdv8GqFuVdYryj4sm6kMLOlSHXMwIvDwF4p1jVwF.jpg', 'do_uong', 1, '2026-05-24 17:24:08', '2026-05-24 17:24:08'),
(11, 5, 'Tôm hùm nướng bơ tỏi', NULL, 580000.00, 'uploads/menus/aFw5F53YQJamJjlsJIrsnY2BwVYGK9zUfE52M8k3.jpg', 'mon_chinh', 1, '2026-05-24 17:26:10', '2026-05-24 17:26:10'),
(12, 5, 'Cua rang muối', NULL, 320000.00, 'uploads/menus/kMrqepUb8btVxMSLn4VhoUjaQYrewMAVc4WmuUml.jpg', 'mon_chinh', 1, '2026-05-24 17:29:32', '2026-05-24 17:29:32'),
(13, 5, 'Gỏi tôm bưởi', NULL, 120000.00, 'uploads/menus/9baVpb8A5fKIrW4jgNhZDI6RKuvlCtbsGrqnRsJ7.jpg', 'khai_vi', 1, '2026-05-24 17:32:36', '2026-05-24 17:32:36'),
(14, 5, 'Sò điệp nướng phô mai', NULL, 150000.00, 'uploads/menus/6wdvk7Nkzy26jI12wFOW1Fx4AWP4v1nxauPhkRsF.jpg', 'khai_vi', 1, '2026-05-24 17:34:47', '2026-05-24 17:34:47'),
(15, 5, 'Kem dừa tươi', NULL, 65000.00, 'uploads/menus/At6sSnbTDWMWnX1lKvmw7xB8G3LrFttumbTj3tqu.jpg', 'trang_mieng', 1, '2026-05-24 17:36:13', '2026-05-24 17:36:13'),
(16, 4, 'Foie gras áp chảo', NULL, 280000.00, 'uploads/menus/m64n1hKRUseK8JvC9MsjBy6rJRVDIBpF2hH4yOwq.jpg', 'khai_vi', 1, '2026-05-24 17:38:35', '2026-05-24 17:38:35'),
(17, 4, 'Bò bít tết sốt nấm', NULL, 450000.00, 'uploads/menus/RypDnHxulZDv2yl2HI3gcXfnpqByRdJjhFOrxjn8.jpg', 'mon_chinh', 1, '2026-05-24 17:41:21', '2026-05-24 17:41:21'),
(18, 4, 'Cá hồi nướng thảo mộc', NULL, 380000.00, 'uploads/menus/hCqP26bB6Y5pCqs19FT05MlEXi97H8AKeuSi1ANA.jpg', 'mon_chinh', 1, '2026-05-24 17:44:07', '2026-05-24 17:44:07'),
(19, 4, 'Bánh soufflé sô-cô-la', NULL, 150000.00, 'uploads/menus/QL4B1Oas1v247IGakTYCjW8O41J7d4leEWf78xz2.jpg', 'trang_mieng', 1, '2026-05-24 17:46:52', '2026-05-24 17:46:52'),
(20, 4, 'Rượu vang đỏ (ly)', NULL, 220000.00, 'uploads/menus/yAnsXzmmiVwbTyFCCIuAykoSsZdcrDwSPaArEuh6.jpg', 'do_uong', 1, '2026-05-24 17:49:24', '2026-05-24 17:49:24'),
(21, 6, 'Mực nướng sa tế', NULL, 220000.00, 'uploads/menus/OCgJFR1gvsz9ZstIM2j1Cr9CKmkaHQCyuRijw2sb.png', 'mon_chinh', 1, '2026-05-24 18:52:22', '2026-05-24 18:52:22'),
(22, 6, 'Tôm sú hấp bia', NULL, 280000.00, 'uploads/menus/34N55koAlDN0WiS0zyy1spVDaAawGySNXqJQxpvh.jpg', 'mon_chinh', 1, '2026-05-24 18:53:20', '2026-05-24 18:53:20'),
(23, 6, 'Ghẹ rang me', NULL, 350000.00, 'uploads/menus/b8NuE6UmPj9df0ExW0gtnp129RsDoOaW0L8oF8Cc.jpg', 'mon_chinh', 1, '2026-05-24 18:56:06', '2026-05-24 18:56:06'),
(24, 6, 'Salad hải sản', NULL, 110000.00, 'uploads/menus/UwjgcDDJoN3092nuickWWHhhEYHTkdR3KW3DqsnK.jpg', 'khai_vi', 1, '2026-05-24 18:59:17', '2026-05-24 18:59:17'),
(25, 6, 'Nước dừa tươi', NULL, 35000.00, 'uploads/menus/wD9R3kZzLHQnvjlysyjzMMrVGg6PRbYJZLG5j0lw.jpg', 'do_uong', 1, '2026-05-24 19:00:22', '2026-05-24 19:00:22'),
(26, 3, 'Cơm tấm sườn bì chả', NULL, 75000.00, 'uploads/menus/RxNNalhcSzPREKLuH2c5ArZywMj16KlgFryOICGn.jpg', 'mon_chinh', 1, '2026-05-24 19:02:27', '2026-05-24 19:02:27'),
(27, 3, 'Bánh cuốn nhân thịt', NULL, 45000.00, 'uploads/menus/FoYx60OaKFhVuvYsyzL4P4JMaUkhKG92BZNRwDL9.jpg', 'khai_vi', 1, '2026-05-24 19:04:07', '2026-05-24 19:04:07'),
(28, 3, 'Bánh xèo miền Bắc', NULL, 55000.00, 'uploads/menus/WzBFmOOchGfmeSO62SDu55vLbenv0ujoDEqKmiIj.jpg', 'khai_vi', 1, '2026-05-24 19:05:22', '2026-05-24 19:05:22'),
(29, 3, 'Chè thập cẩm', NULL, 35000.00, 'uploads/menus/Si0DCJO6xoEGSQYLAvOGoQdGlfBUANdLAlBYwKF9.jpg', 'trang_mieng', 1, '2026-05-24 19:06:24', '2026-05-24 19:06:24'),
(30, 3, 'Trà đá', NULL, 10000.00, 'uploads/menus/F386F8aW63SkDPFB0LYEzmid7w1Cvx0ryprybSyP.jpg', 'do_uong', 1, '2026-05-24 19:07:53', '2026-05-24 19:07:53'),
(31, 10, 'Australian Wagyu Steak', NULL, 890000.00, 'uploads/menus/too5RaSIVZNgx4udmFMkgtLMbrAUkPWgsT94OszH.jpg', 'mon_chinh', 1, '2026-05-25 13:52:13', '2026-05-25 13:52:13'),
(32, 10, 'Grilled Salmon', NULL, 420000.00, 'uploads/menus/HJeGMEeDPGx4m63DXumfGrtYeNWafFX1Py1XBrvA.jpg', 'mon_chinh', 1, '2026-05-25 14:22:08', '2026-05-25 14:22:08'),
(33, 10, 'Truffle Fries', NULL, 180000.00, 'uploads/menus/Xds2sukICSKVY63ZnwLU88PyE7ImLCRZOmuzf9Kx.jpg', 'khai_vi', 1, '2026-05-25 14:23:00', '2026-05-25 14:23:00'),
(34, 10, 'Chill Signature Cocktail', NULL, 280000.00, 'uploads/menus/ER692uTMdvFir3TlDqIMD0DToa2rkJAjgZQSy4jn.jpg', 'do_uong', 1, '2026-05-25 14:25:02', '2026-05-25 14:25:02'),
(35, 10, 'Mojito', NULL, 220000.00, 'uploads/menus/ykI4rDrnwURjNbkwt8ZE8Koit5bokRtOgXpkIbcb.jpg', 'do_uong', 1, '2026-05-25 14:26:34', '2026-05-25 14:26:34'),
(36, 10, 'Chocolate Lava Cake', NULL, 160000.00, 'uploads/menus/IUFXXSy9TvXQ3ZGqWKEVX5d31TdT5uTvchmImUIW.jpg', 'trang_mieng', 1, '2026-05-25 14:27:19', '2026-05-25 14:27:19'),
(37, 9, 'Cơm sườn cốt lết nướng', NULL, 49000.00, 'uploads/menus/oBNsdLXdcre3zTs8ddlZrLJunFJoQIY3DbH4CXU5.jpg', 'mon_chinh', 1, '2026-05-25 14:28:42', '2026-05-25 14:28:42'),
(38, 9, 'Cơm sườn bì chả đặc biệt', NULL, 84000.00, 'uploads/menus/zo1VFi1d6ZVZx0WwFOlNbusKoGNYdfA66m0GKc4c.jpg', 'mon_chinh', 1, '2026-05-25 14:29:39', '2026-05-25 14:29:39'),
(39, 9, 'Chả trứng bách thảo', NULL, 15000.00, 'uploads/menus/ToosyM1lp6dzXoJKfDgM0IzcnGAKvEyDBUg6scgt.jpg', 'khai_vi', 1, '2026-05-25 14:30:37', '2026-05-25 14:30:37'),
(40, 9, 'Canh rong biển thịt bằm', NULL, 15000.00, 'uploads/menus/1Cya4sDVeVBP4lFrlfozO1h2CdyIceOB36JiBhof.jpg', 'khai_vi', 1, '2026-05-25 14:31:33', '2026-05-25 14:31:33'),
(41, 9, 'Trà sữa Sà Bì Chưởng', NULL, 29000.00, 'uploads/menus/WNL1LCQARJfAixReYVl0Ll1joxRNXeqyqmwDVMnm.jpg', 'do_uong', 1, '2026-05-25 14:33:24', '2026-05-25 14:33:24'),
(42, 8, 'Cơm đất nung cá kho tộ', NULL, 120000.00, 'uploads/menus/z0ygBJHPaekrHP1oEtbVRqiMBtoIaedrpZ68sAKn.jpg', 'mon_chinh', 1, '2026-05-25 14:34:57', '2026-05-25 14:34:57'),
(43, 8, 'Canh khổ qua nhồi thịt', NULL, 65000.00, 'uploads/menus/SfG4bNKkED2Cz2B6RXf8zAQvk5GRYCdgR2eQhbl6.jpg', 'khai_vi', 1, '2026-05-25 14:36:06', '2026-05-25 14:36:06'),
(44, 8, 'Rau muống xào tỏi', NULL, 45000.00, 'uploads/menus/hL8mtiap1YHi0vVL23LqxBD2nbV0i7qmzc4PLaix.jpg', 'khai_vi', 1, '2026-05-25 14:37:01', '2026-05-25 14:37:01'),
(45, 8, 'Chè đậu đen nước dừa', NULL, 45000.00, 'uploads/menus/8iMcjnMmRdZaFid87dtI0B0XLl2m6KddY91gYvRC.jpg', 'trang_mieng', 1, '2026-05-25 14:38:48', '2026-05-25 14:38:48'),
(46, 8, 'Nước sâm bí đao', NULL, 30000.00, 'uploads/menus/T2hQwFWlZquBXZNefoULf6kncoJpaDXRJnGvCobJ.jpg', 'do_uong', 1, '2026-05-25 14:41:45', '2026-05-25 14:41:45'),
(47, 13, 'Mì Quảng tôm thịt', NULL, 75000.00, 'uploads/menus/Gp0Au4n1X82zpwHWn7Xy9ND51dp61jLoqX8EONw9.jpg', 'mon_chinh', 1, '2026-05-25 14:42:50', '2026-05-25 14:42:50'),
(48, 13, 'Bún bò Huế đặc biệt', NULL, 80000.00, 'uploads/menus/3RW3riS4Qf3F9lrjrC2BE2rdJeiqUStngXPbrmxk.jpg', 'mon_chinh', 1, '2026-05-25 14:44:07', '2026-05-25 14:44:07'),
(49, 13, 'Bánh mì thịt nướng', NULL, 35000.00, 'uploads/menus/KtY5MEXAQNIdEMtwlZ6RsjWC0d4W0bVyy8EhcqeG.jpg', 'khai_vi', 1, '2026-05-25 14:45:00', '2026-05-25 14:45:00'),
(50, 13, 'Cao lầu Hội An', NULL, 70000.00, 'uploads/menus/LRgzz6bMItErwuYXcyZ53jy4SHrP5Hip0aGC8bW4.jpg', 'mon_chinh', 1, '2026-05-25 14:46:13', '2026-05-25 14:46:13'),
(51, 13, 'Sinh tố dâu tươi', NULL, 40000.00, 'uploads/menus/GX35BWV0NnZxtZZDn0XfnNvR2hHQu1dpJL6mFK1h.jpg', 'do_uong', 1, '2026-05-25 14:47:09', '2026-05-25 14:47:09'),
(52, 20, 'Cua Hội An rang me', NULL, 280000.00, 'uploads/menus/CAqnhUH55685ZpcQvTfPvOQBuHjJpBXXhWZm2rh0.jpg', 'mon_chinh', 1, '2026-05-25 14:48:53', '2026-05-25 14:48:53'),
(53, 20, 'Tôm rảo nướng than', NULL, 175000.00, 'uploads/menus/u0x6RA99175mmVNOpDJrgOlEpo6b5ZH01LaYI95G.jpg', 'mon_chinh', 1, '2026-05-25 14:50:07', '2026-05-25 14:50:07'),
(54, 20, 'Súp hải sản chua cay', NULL, 95000.00, 'uploads/menus/DuZVqDPVztmu1c77J26Q0eP726SwSKq1JcTVFH0d.jpg', 'khai_vi', 1, '2026-05-25 14:51:52', '2026-05-25 14:51:52'),
(55, 20, 'Nước ép xoài mango', NULL, 45000.00, 'uploads/menus/Qgj7YhgZJboD4IolSyLQ5JByK0nb3axwFV4uk5Wo.jpg', 'do_uong', 1, '2026-05-25 14:52:37', '2026-05-25 14:52:37'),
(56, 20, 'Bánh flan caramel', NULL, 50000.00, 'uploads/menus/xfsQT9oYOvez8GVxIRxWGRywd1fMBqpNrSiDzoyT.jpg', 'trang_mieng', 1, '2026-05-25 14:54:29', '2026-05-25 14:54:29'),
(57, 14, 'Tôm hùm nướng bơ tỏi', NULL, 450000.00, 'uploads/menus/VSxyDWZmotKjQjrAmCOziTIOXzp8lhr80VfCOjUJ.jpg', 'mon_chinh', 1, '2026-05-25 14:55:55', '2026-05-25 14:55:55'),
(58, 14, 'Ghẹ hấp sả', NULL, 320000.00, 'uploads/menus/L3zhykbClAcPaZKDJU2aQylYv3aBVzuOWHn3PCL0.jpg', 'mon_chinh', 1, '2026-05-25 14:56:40', '2026-05-25 14:56:40'),
(59, 14, 'Hàu nướng phô mai', NULL, 120000.00, 'uploads/menus/MXt18DsHr4wC0EFl2W6VDPZkxW2GscwOD1BhCRC1.jpg', 'khai_vi', 1, '2026-05-25 14:57:47', '2026-05-25 14:57:47'),
(60, 14, 'Mực nướng sa tế', NULL, 180000.00, 'uploads/menus/kRaTW9NP8al0uUYnVjbv5ZUS4z6xRaPzPY8tRIPS.jpg', 'mon_chinh', 1, '2026-05-25 14:58:53', '2026-05-25 14:58:53'),
(61, 14, 'Nước ép chanh dây', NULL, 35000.00, 'uploads/menus/sySYZhzAQISYNfd4h6YeqTrVznl7Zt539OJe2GmN.jpg', 'do_uong', 1, '2026-05-25 15:00:21', '2026-05-25 15:00:21'),
(62, 14, 'Rau câu dừa', NULL, 30000.00, 'uploads/menus/A0hUKAMMScOX9LCTTI1O9BgiQugPRY35YoQ4Hzzl.jpg', 'trang_mieng', 1, '2026-05-25 15:02:33', '2026-05-25 15:02:33'),
(63, 19, 'Cao lầu đặc biệt', NULL, 75000.00, 'uploads/menus/MooiVxaQSfFmQpawSyE8k3UiivptTAxCLD1CU43M.jpg', 'mon_chinh', 1, '2026-05-25 15:06:21', '2026-05-25 15:06:21'),
(64, 19, 'Mì Quảng gà', NULL, 65000.00, 'uploads/menus/4WUwoX2IcH8gxTv0EUMzzYVcltifBmUSK3aUTHh8.jpg', 'mon_chinh', 1, '2026-05-25 16:46:42', '2026-05-25 16:46:42'),
(65, 19, 'Hoành thánh chiên', NULL, 55000.00, 'uploads/menus/vgbKvoRgqQYqSZfd8zK5kQprmElmsN8CTJXLUcvs.jpg', 'khai_vi', 1, '2026-05-25 16:47:59', '2026-05-25 16:47:59'),
(66, 19, 'Bánh bao hấp nhân tôm', NULL, 45000.00, 'uploads/menus/Wj7ZysfFQV6OPOb2w2mfGMgDbRJPZOCinMrP7ulc.jpg', 'khai_vi', 1, '2026-05-25 16:49:14', '2026-05-25 16:49:14'),
(67, 19, 'Sinh tố xoài', NULL, 45000.00, 'uploads/menus/DQJHHH9Q6Y8qEBS0QAplDEc4rfS17AfgyEEynlTq.jpg', 'do_uong', 1, '2026-05-25 16:50:30', '2026-05-25 16:50:30'),
(68, 11, 'Sườn bò nướng rosemary', NULL, 380000.00, 'uploads/menus/7ajfNAt3ULqCtC5wV4TktmkMCE3Xh31BycY6oAgm.jpg', 'mon_chinh', 1, '2026-05-25 16:54:15', '2026-05-25 16:54:15'),
(69, 11, 'Cá hồi sốt chanh butter', NULL, 320000.00, 'uploads/menus/K2hBhwFpXrl9YFdsu43Db9FJeu54RhNsGv51kPB2.jpg', 'mon_chinh', 1, '2026-05-25 16:55:47', '2026-05-25 16:55:47'),
(70, 11, 'Bruschetta cà chua', NULL, 120000.00, 'uploads/menus/2Y8r4mr5bL6aeeXpWnajk0VcvgvPaR7z81gRHXtS.jpg', 'khai_vi', 1, '2026-05-25 16:57:27', '2026-05-25 16:57:27'),
(71, 11, 'Lava cake sô-cô-la', NULL, 130000.00, 'uploads/menus/wtzK00j8pwdbE6XTz9wSHLqXYOUJIbzMBY82HFLJ.jpg', 'trang_mieng', 1, '2026-05-25 16:59:40', '2026-05-25 16:59:40'),
(72, 11, 'Cocktail Đà Nẵng Sunset', NULL, 180000.00, 'uploads/menus/ez6rp89zMYrmwpW9dsM5ti83nxKJ3DGzBUqOcDA5.jpg', 'do_uong', 1, '2026-05-25 17:01:05', '2026-05-25 17:01:05'),
(73, 16, 'Lẩu hải sản', NULL, 350000.00, 'uploads/menus/UsXEu3r2s2Rwagvs6NtexCCDDYLBJ3pemrgdzTxh.jpg', 'mon_chinh', 1, '2026-05-25 17:03:03', '2026-05-25 17:03:03'),
(74, 16, 'Tôm sú nướng muối ớt', NULL, 280000.00, 'uploads/menus/al9CQS3W0LvAcmR2CO6ZtlM69w9m7FsIo8uPrlHx.jpg', 'mon_chinh', 1, '2026-05-25 17:04:10', '2026-05-25 17:04:10'),
(75, 16, 'Mực hấp gừng', NULL, 220000.00, 'uploads/menus/FMJRgAWBhz6905mpZ1o3PHSM74HkVsPnEO9Wepk1.jpg', 'mon_chinh', 1, '2026-05-25 17:05:05', '2026-05-25 17:05:05'),
(76, 16, 'Gỏi cá mai', NULL, 150000.00, 'uploads/menus/1y7usngtmR57IB5Tg2TCyQVSX6ES0dyL7jfjQnIc.jpg', 'khai_vi', 1, '2026-05-25 17:06:03', '2026-05-25 17:06:03'),
(77, 16, 'Nước ép cam tươi', NULL, 40000.00, 'uploads/menus/kdLMLNl4iXmx3zBxtIzH8b8bY81I161umZbz9UiV.jpg', 'do_uong', 1, '2026-05-25 17:07:02', '2026-05-25 17:07:02'),
(78, 16, 'Chè khúc bạch', NULL, 45000.00, 'uploads/menus/2k8Eyi4T0HfslpfY94Hw8C8o71fOjeY6HcptrgWr.jpg', 'trang_mieng', 1, '2026-05-25 17:08:04', '2026-05-25 17:08:04'),
(79, 12, 'Cơm chay thập cẩm', NULL, 65000.00, 'uploads/menus/Du2s1PpQvHNfDyXORl5s8Z2UGLJ5fBWcb3BHqsKK.jpg', 'mon_chinh', 1, '2026-05-25 17:09:53', '2026-05-25 17:09:53'),
(80, 12, 'Bún chay Huế', NULL, 55000.00, 'uploads/menus/QVXDolf3OxdhR4VZnrcGMNIi9WPpzQ86EJ9LACnZ.jpg', 'mon_chinh', 1, '2026-05-25 17:10:47', '2026-05-25 17:10:47'),
(81, 12, 'Chả giò chay', NULL, 40000.00, 'uploads/menus/luHSd2ptSkLbR2pwbOKMuw2vwVnmnv7NlOmvdIIB.jpg', 'khai_vi', 1, '2026-05-25 17:12:23', '2026-05-25 17:12:23'),
(83, 12, 'Nước ép dưa hấu', NULL, 30000.00, 'uploads/menus/9hdwsCORJE4wpUlzT5Sm7uP8hUDGnwPRwiq4RaK5.jpg', 'do_uong', 1, '2026-05-25 17:14:34', '2026-05-25 17:14:34'),
(84, 12, 'Bánh flan chay', NULL, 30000.00, 'uploads/menus/D8EVUMMj4hDyQzBu5nXxcLNlWLFyJe636vXYn9s5.jpg', 'trang_mieng', 1, '2026-05-25 17:16:00', '2026-05-25 17:16:00'),
(85, 17, 'Cao lầu Hội An', NULL, 95000.00, 'uploads/menus/CJsbuXz4H3cQzyNFrGUJpyrsyBZDxFMAXML9LJQU.jpg', 'mon_chinh', 1, '2026-05-25 17:18:14', '2026-05-25 17:18:14'),
(86, 17, 'Bò lúc lắc sốt tiêu', NULL, 220000.00, 'uploads/menus/VTKvtGT3mmdIt4sFuZwUuZTI5zel9EfUwLkxom88.jpg', 'mon_chinh', 1, '2026-05-25 17:20:18', '2026-05-25 17:20:18'),
(87, 17, 'Gỏi xoài tôm nướng', NULL, 120000.00, 'uploads/menus/BrcDx0N5V0yayTtN4NQBY5eoHHlIeJcY1QOmY3QV.jpg', 'khai_vi', 1, '2026-05-25 17:21:38', '2026-05-25 17:21:38'),
(88, 17, 'Pizza hải sản', NULL, 260000.00, 'uploads/menus/RJ3OVbbK9Qq4AyE9SomDrKpPQWDpBWJaCNmlssB7.jpg', 'mon_chinh', 1, '2026-05-25 17:23:08', '2026-05-25 17:23:08'),
(89, 17, 'Cocktail Tropical Paddy', NULL, 90000.00, 'uploads/menus/mNVWtvIl0t1cmLP3KWlhqgehxM0xEBoC2TVuFXFg.jpg', 'do_uong', 1, '2026-05-25 17:25:03', '2026-05-25 17:25:03'),
(90, 17, 'Bánh chuối nướng kem vani', NULL, 75000.00, 'uploads/menus/gRQzxFJiPxTdP5mG9gy1fiR816sMOY8fThpVE21g.jpg', 'trang_mieng', 1, '2026-05-25 17:26:07', '2026-05-25 17:26:07'),
(91, 7, 'Pizza Margherita', NULL, 195000.00, 'uploads/menus/GbDkB9xalJjzqqrlHrlTdiYLoswMu8iH7tbf99NJ.jpg', 'mon_chinh', 1, '2026-05-25 17:27:36', '2026-05-25 17:27:36'),
(92, 7, 'Pizza Burrata & Prosciutto', NULL, 295000.00, 'uploads/menus/CjjB0CvyJw27zZ0o8ASfTU1NNZ8f3Wbemr1VPVw5.jpg', 'mon_chinh', 1, '2026-05-25 17:28:33', '2026-05-25 17:28:33'),
(93, 7, 'Pasta Carbonara', NULL, 185000.00, 'uploads/menus/pkIZis2U1D9X4tCXm4de8AUOZM0rAMHzs4qyz8pf.jpg', 'mon_chinh', 1, '2026-05-25 17:29:51', '2026-05-25 17:29:51'),
(94, 7, 'Tiramisu', NULL, 125000.00, 'uploads/menus/JtJiGQq4KhG0yTj20TKuVlftqOEEjJYNsHdh0Uk5.jpg', 'trang_mieng', 1, '2026-05-25 17:30:49', '2026-05-25 17:30:49'),
(95, 7, 'Nước ép cam tươi', NULL, 75000.00, 'uploads/menus/rj2zBYU3e9zbXTh1Jh3yCg4r1ya5CPvmxep0HUY9.jpg', 'do_uong', 1, '2026-05-25 17:31:51', '2026-05-25 17:31:51'),
(96, 18, 'Bánh mì sandwich thịt nguội', NULL, 95000.00, 'uploads/menus/8qhMMrxdoeRX0Fu30DFIxlJQbO0hSt6kUdGevDdK.jpg', 'khai_vi', 1, '2026-05-25 17:33:45', '2026-05-25 17:33:45'),
(97, 18, 'Pasta hải sản Hội An', NULL, 195000.00, 'uploads/menus/gdoz0Hy85zLNfDymHVbfSYTcBKsFsUsKI6fwHRSy.jpg', 'mon_chinh', 1, '2026-05-25 17:35:50', '2026-05-25 17:35:50'),
(98, 18, 'Bánh crepe dứa', NULL, 85000.00, 'uploads/menus/Pw1xjwQpQXHXqDBsNFZwXQN7YpJTen9EuHADVLWo.jpg', 'trang_mieng', 1, '2026-05-25 17:36:54', '2026-05-25 17:36:54'),
(99, 18, 'Café sữa đá', NULL, 40000.00, 'uploads/menus/eipoGcN7GOcTDIjrjMWF5ynIiEGLXhIHrRe1V3bc.jpg', 'do_uong', 1, '2026-05-25 17:37:53', '2026-05-25 17:37:53'),
(100, 18, 'Kem dừa Hội An', NULL, 55000.00, 'uploads/menus/GYoQ7dVsuNMJsktjZceR7EgPhtgtqk4wMCzl2YCE.jpg', 'trang_mieng', 1, '2026-05-25 17:39:25', '2026-05-25 17:39:25'),
(101, 15, 'Cá kho tộ', NULL, 145000.00, 'uploads/menus/zBVFdGx3dchoSp0yeEmwxS6ILa0LE5f77T9ivuCt.jpg', 'mon_chinh', 1, '2026-05-25 17:41:19', '2026-05-25 17:41:19'),
(102, 15, 'Thịt kho tàu', NULL, 120000.00, 'uploads/menus/Jzmu6WMxPIbN4GfEHGTIXwcMYFCxQmqG4KUSzoGA.jpg', 'mon_chinh', 1, '2026-05-25 17:42:13', '2026-05-25 17:42:13'),
(103, 15, 'Gỏi cuốn tôm thịt', NULL, 75000.00, 'uploads/menus/d5OdYSTnIqSLerhjsfLnZ9YpvJAjsNkBBhjwkW7c.jpg', 'khai_vi', 1, '2026-05-25 17:43:09', '2026-05-25 17:43:09'),
(104, 15, 'Canh chua cá', NULL, 135000.00, 'uploads/menus/lfdQsnOzPNDE5OUJNiGgNM35BygqQuX1Dj7gjYzh.jpg', 'mon_chinh', 1, '2026-05-25 17:44:14', '2026-05-25 17:44:14'),
(105, 15, 'Trà sen vàng', NULL, 35000.00, 'uploads/menus/AEtTrPY9NbpQUjVdccJaeb9r7ikA9afNleQhuMHH.jpg', 'do_uong', 1, '2026-05-25 17:46:08', '2026-05-25 17:46:08'),
(106, 15, 'Chè đậu xanh', NULL, 30000.00, 'uploads/menus/Anxnt1h9sWY6A2otY31g8sGamvBenPMOxY9R8ki3.jpg', 'trang_mieng', 1, '2026-05-25 17:46:56', '2026-05-25 17:46:56'),
(107, 21, 'Buffet hải sản cao cấp', NULL, 699000.00, 'uploads/menus/ijY9LVihjH68XRBIzXJz0Q57v6aujdQQFL4kLoAi.jpg', 'buffet', 1, '2026-05-25 20:57:50', '2026-06-03 17:00:09'),
(108, 21, 'Buffet BBQ & Lẩu', NULL, 499000.00, 'uploads/menus/Igh21ILHpsfku0RVii5moMHWKc8zFR3ZJ1IO9kDi.jpg', 'buffet', 1, '2026-05-25 20:59:24', '2026-06-03 17:00:02'),
(109, 21, 'Tôm nướng phô mai', NULL, 180000.00, 'uploads/menus/NUSpNu4rgxxeIYVwj8Zo7vvQraTi18S6dA6CbggM.jpg', 'mon_chinh', 1, '2026-05-25 21:00:31', '2026-05-25 21:00:31'),
(110, 21, 'Hàu nướng mỡ hành', NULL, 120000.00, 'uploads/menus/XMEf5fHLCmO1Q96RsB5bYkwmNxgtmXCAagDYLgig.jpg', 'mon_chinh', 1, '2026-05-25 21:01:19', '2026-05-25 21:01:19'),
(111, 21, 'Sushi cá hồi', NULL, 150000.00, 'uploads/menus/qzTibKcJ2PMmZZ2joadbESGgZLWQehyrDp72mGbu.jpg', 'mon_chinh', 1, '2026-05-25 21:02:43', '2026-05-25 21:02:43'),
(112, 21, 'Nước ép cam tươi', NULL, 45000.00, 'uploads/menus/61MIF43FgHMs16x3IqQytU8K4nR0y6skIDItozBY.jpg', 'do_uong', 1, '2026-05-25 21:03:40', '2026-05-25 21:03:40'),
(113, 21, 'Bánh mousse chanh dây', NULL, 65000.00, 'uploads/menus/jOVOaAAGV2bFw8ZrbA5IVwka4lfgIOBFQhVJ6rQ1.jpg', 'trang_mieng', 1, '2026-05-25 21:04:33', '2026-05-25 21:04:33'),
(115, 22, 'Buffet nướng Nhật Bản', NULL, 299000.00, 'uploads/menus/1780503241_buffet-nhat-thumb.jpg', 'buffet', 1, '2026-06-03 16:14:01', '2026-06-03 16:22:05'),
(116, 22, 'Ba chỉ bò Mỹ nướng', NULL, 259000.00, 'uploads/menus/1780503781_ba-chi-bo-my-nuong_2.jpg', 'mon_chinh', 1, '2026-06-03 16:23:01', '2026-06-03 16:23:01'),
(117, 22, 'Lẩu kim chi hải sản', NULL, 329000.00, 'uploads/menus/1780503824_Lau-Thai-Hai-San.jpg', 'mon_chinh', 1, '2026-06-03 16:23:44', '2026-06-03 16:23:44'),
(118, 22, 'Salad rong biển', NULL, 89000.00, 'uploads/menus/1780503864_salad.png', 'khai_vi', 1, '2026-06-03 16:24:24', '2026-06-03 16:24:24'),
(119, 22, 'Trà đào cam sả', NULL, 45000.00, 'uploads/menus/1780503892_tra-dao-cam-sa-cong-thuc-pha-che-chuan-vi.png', 'do_uong', 1, '2026-06-03 16:24:52', '2026-06-03 16:24:52'),
(120, 23, 'Bò Mỹ tẩm ướp đặc biệt', NULL, 299000.00, 'uploads/menus/1780503978_ba-chi-bo-my-nuong_2.jpg', 'mon_chinh', 1, '2026-06-03 16:26:18', '2026-06-03 16:26:18'),
(121, 23, 'Lẩu bò Mỹ', NULL, 349000.00, 'uploads/menus/1780504079_Gioi-thieu-ve-mon-lau-bo-My.jpg', 'buffet', 1, '2026-06-03 16:27:59', '2026-06-03 16:27:59'),
(122, 23, 'Sườn nướng mật ong', NULL, 269000.00, 'uploads/menus/1780504159_cach-lam-suon-nuong-mat-ong-thumb.jpg', 'mon_chinh', 1, '2026-06-03 16:29:19', '2026-06-03 16:29:19'),
(123, 23, 'Khoai tây chiên lắc phô mai', NULL, 79000.00, 'uploads/menus/1780504205_khoai.jpg', 'khai_vi', 1, '2026-06-03 16:30:05', '2026-06-03 16:30:05'),
(124, 23, 'Nước ép cam tươi', NULL, 40000.00, 'uploads/menus/1780504264_nuoc cam.jpg', 'do_uong', 1, '2026-06-03 16:31:04', '2026-06-03 16:31:04'),
(125, 24, 'Mực nướng sa tế', NULL, 120000.00, 'uploads/menus/1780504321_muc.jpg', 'mon_chinh', 1, '2026-06-03 16:32:01', '2026-06-03 16:32:01'),
(126, 24, 'Set nhắm bia tổng hợp', NULL, 250000.00, 'uploads/menus/1780504490_cac-mon-nhau-voi-ruou-khac.jpg', 'mon_chinh', 1, '2026-06-03 16:34:50', '2026-06-03 16:34:50'),
(127, 24, 'Gỏi cá mai', NULL, 150000.00, 'uploads/menus/1780504582_goi-ca-mai-nha-trang-1.jpg', 'khai_vi', 1, '2026-06-03 16:36:22', '2026-06-03 16:36:22'),
(128, 24, 'Tôm sú nướng muối ớt', NULL, 230000.00, 'uploads/menus/1780504642_Thnhphm11-1699170028-3875-1699170031.jpg', 'mon_chinh', 1, '2026-06-03 16:37:22', '2026-06-03 16:37:22'),
(129, 24, 'Bia Heineken', NULL, 30000.00, 'uploads/menus/1780504696_bia-heineken-5-chai-250ml-phap-1.webp', 'do_uong', 1, '2026-06-03 16:38:16', '2026-06-03 16:38:16'),
(130, 25, 'Chân giò muối chiên giòn', NULL, 220000.00, 'uploads/menus/1780504747_cong-thuc-lam-mon-gio-heo-muoi-chien-gion-thom-ngon-dam-vi-202108061421121320.jpg', 'mon_chinh', 1, '2026-06-03 16:39:07', '2026-06-03 16:39:07'),
(131, 25, 'Cá lăng nướng riềng mẻ', NULL, 350000.00, 'uploads/menus/1780504831_Thnhphm11-1724660535-4248-1724660924.jpg', 'mon_chinh', 1, '2026-06-03 16:40:31', '2026-06-03 16:40:31'),
(132, 25, 'Lẩu riêu cua bắp bò', NULL, 500000.00, 'uploads/menus/1780504876_lau.jpg', 'mon_chinh', 1, '2026-06-03 16:41:16', '2026-06-03 16:41:16'),
(133, 25, 'Bê tái chanh', NULL, 180000.00, 'uploads/menus/1780504921_be.jpg', 'khai_vi', 1, '2026-06-03 16:42:01', '2026-06-03 16:42:01'),
(134, 25, 'Bia Sài Gòn Special', NULL, 25000.00, 'uploads/menus/1780504963_bia sg.webp', 'do_uong', 1, '2026-06-03 16:42:43', '2026-06-03 16:42:43'),
(135, 26, 'Ba chỉ bò nướng sốt Nhà Pá', NULL, 250000.00, 'uploads/menus/1780505035_Bo-Chi-Ba.jpg', 'mon_chinh', 1, '2026-06-03 16:43:55', '2026-06-03 16:43:55'),
(136, 26, 'Sườn heo nướng than hoa', NULL, 350000.00, 'uploads/menus/1780505081_suon.webp', 'mon_chinh', 1, '2026-06-03 16:44:41', '2026-06-03 16:44:41'),
(137, 26, 'Lẩu bò Nhà Pá', NULL, 700000.00, 'uploads/menus/1780505154_lau bo.jpg', 'mon_chinh', 1, '2026-06-03 16:45:54', '2026-06-03 16:45:54'),
(138, 26, 'Kim chi Hàn Quốc', NULL, 90000.00, 'uploads/menus/1780505197_kim.jpg', 'khai_vi', 1, '2026-06-03 16:46:37', '2026-06-03 16:46:37'),
(139, 26, 'Trà tắc mật ong', NULL, 35000.00, 'uploads/menus/1780505256_TRA-TAC-MAT-ONG-scaled.jpg', 'do_uong', 1, '2026-06-03 16:47:36', '2026-06-03 16:47:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_20_092021_create_categories_table', 1),
(5, '2026_05_20_092025_create_restaurants_table', 1),
(6, '2026_05_20_092029_create_tables_table', 1),
(7, '2026_05_20_092033_create_menu_items_table', 1),
(8, '2026_05_20_092037_create_bookings_table', 1),
(9, '2026_05_20_092042_create_reviews_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `district` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price_range` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_min` int(11) DEFAULT NULL,
  `price_max` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `slug`, `category_id`, `address`, `city`, `district`, `description`, `price_range`, `phone`, `open_time`, `close_time`, `image`, `status`, `created_at`, `updated_at`, `price_min`, `price_max`) VALUES
(1, 'Bún Chả Hương Liên', 'bun-cha-huong-lien', 2, '24 Lê Văn Hưu', 'Hà Nội', '', 'Bún chả Hương Liên là địa chỉ ẩm thực nức tiếng tại thủ đô, từng hân hạnh đón tiếp cựu Tổng thống Mỹ Barack Obama. Không gian quán mang đậm nét văn hóa Hà Nội xưa, kết hợp cùng hương vị chả nướng than hoa truyền thống...', NULL, '02439434106', '10:00:00', '22:00:00', 'uploads/restaurants/d7fdS0YoVEqLc0LnPmWa8xv1pMP8miNXowlJ7rgm.jpg', 1, '2026-05-23 23:44:12', '2026-05-26 11:09:00', 80000, 200000),
(2, 'Chả Cá Lã Vọng', 'cha-ca-la-vong', 3, '14 Chả Cá, Hoàn Kiếm', 'Hà Nội', '', 'Nhà hàng nổi tiếng với món chả cá truyền thống Hà Nội, không gian đậm nét cổ xưa và hương vị đặc trưng lâu đời.', NULL, '02438253929', '07:00:00', '22:00:00', 'uploads/restaurants/92hwoxrUioxjZqrw9AxlAZPXnkdcX1E5G8xIIxB3.jpg', 1, '2026-05-23 23:46:20', '2026-05-26 11:11:50', 150000, 350000),
(3, 'Nhà Hàng Quán Ăn Ngon', 'nha-hang-quan-an-ngon', 1, '18 Phan Bội Châu, Hoàn Kiếm', 'Hà Nội', '', 'Điểm đến quen thuộc dành cho thực khách muốn thưởng thức đa dạng món ăn Việt Nam trong không gian mang phong cách phố cổ.', NULL, '02439428162', '07:00:00', '22:00:00', 'uploads/restaurants/4NwdjxeL3lTETjG7DSVjzg7OR4JTITRlCtxgaUad.jpg', 1, '2026-05-23 23:48:59', '2026-05-26 11:12:50', 100000, 300000),
(4, 'La Badiane', 'la-badiane', 5, '10 Nam Ngư, Hoàn Kiếm', 'Hà Nội', '', 'Nhà hàng fine dining kết hợp ẩm thực Âu – Á tinh tế, nổi bật với không gian sang trọng và thực đơn cao cấp.', NULL, '02439424509', '00:00:00', '22:30:00', 'uploads/restaurants/1780497070_z7897487390029_b9d7b5a695890592cd2534b29dc5dd1f.jpg', 1, '2026-05-23 23:52:21', '2026-06-03 14:31:10', 300000, 800000),
(5, 'Cousins Restaurant', 'cousins-restaurant', 3, '5 Xuân Diệu, Tây Hồ', 'Hà Nội', '', 'Nhà hàng phong cách hiện đại chuyên hải sản và món Âu, phù hợp cho các buổi gặp gỡ gia đình hoặc bạn bè.', NULL, '02437180769', '11:00:00', '23:00:00', 'uploads/restaurants/1780497105_z7897487361460_41ae35d19a1ec89d157d48cd30dd550f.jpg', 1, '2026-05-23 23:54:08', '2026-06-03 14:31:45', 200000, 600000),
(6, 'Nhà Hàng Ngọc Sương', 'nha-hang-ngoc-suong', 3, '36–38 Lê Anh Xuân, Q.1', 'TP.HCM', '', 'Nhà hàng hải sản lâu năm nổi tiếng với thực đơn phong phú, không gian sang trọng và phục vụ chuyên nghiệp.', NULL, '02838297741', '10:30:00', '22:30:00', 'uploads/restaurants/1N3e602H2aSudrcO0ck5nuEKBQlUGvtHYXVQGyf2.jpg', 1, '2026-05-23 23:55:42', '2026-05-26 11:14:23', 250000, 700000),
(7, 'Pizza 4P\'s Lê Thánh Tôn', 'pizza-4ps-le-thanh-ton', 5, '8 Lê Thánh Tôn, Q.1', 'TP.HCM', '', 'Thương hiệu pizza nổi tiếng với phong cách Nhật – Ý, đặc biệt hấp dẫn nhờ phô mai tươi tự làm và không gian hiện đại.', NULL, '02836220500', '11:00:00', '23:00:00', 'uploads/restaurants/1780497123_z7897486649555_5d82b835cbcd6fd1b0804b2f14cd15f3.jpg', 1, '2026-05-25 01:34:46', '2026-06-03 14:32:03', 200000, 600000),
(8, 'Cục Gạch Quán', 'cuc-gach-quan', 1, '10 Đặng Tất, Q.1', 'TP.HCM', '', 'Quán ăn mang phong cách hoài cổ Việt Nam với thực đơn cơm nhà dân dã, gần gũi và ấm cúng.', NULL, '02838480144', '11:00:00', '23:00:00', 'uploads/restaurants/1780497145_z7897508777053_1192ef45169877ef38126f27d0478dec.jpg', 1, '2026-05-25 02:39:29', '2026-06-03 14:32:25', 150000, 400000),
(9, 'Cơm tấm Sà Bì Chưởng', 'com-tam-sa-bi-chuong', 1, '179 Trần Bình Trọng, Q.5', 'TP.HCM', '', 'Quán cơm tấm nổi tiếng với sườn nướng đậm vị, phần ăn đầy đặn và phong cách trẻ trung, hiện đại.', NULL, '0971095261', '07:00:00', '21:00:00', 'uploads/restaurants/3pRyJGrDmLOBXtenvgh56JRiNaihlk6F3tepbrO1.jpg', 1, '2026-05-25 05:24:52', '2026-05-26 11:15:56', 100000, 200000),
(10, 'Chill Skybar & Dining', 'chill-skybar-dining', 5, 'AB Tower, 76A Lê Lai, Bến Thành', 'TP.HCM', '', 'Tổ hợp skybar và fine dining sang trọng, nổi bật với view thành phố về đêm và không gian giải trí sôi động.', NULL, '0938822838', '17:30:00', '23:30:00', 'uploads/restaurants/VWnfD0ehYwSjftrLmg2juqmmJgyZyRq17fJDsOZS.jpg', 1, '2026-05-25 10:58:35', '2026-05-26 11:16:33', 200000, 1000000),
(11, 'Nhà Hàng Skyview', 'nha-hang-skyview', 5, '36 Bạch Đằng, tầng 23', 'Đà Nẵng', '', 'Nhà hàng trên cao với tầm nhìn toàn cảnh sông Hàn, phục vụ các món Âu – Á trong không gian hiện đại.', NULL, '02363525369', '11:30:00', '23:00:00', 'uploads/restaurants/1780497192_z7897486516984_76c08e29113d1477365e66f8b2939dce.jpg', 1, '2026-05-25 02:54:16', '2026-06-03 14:33:12', 250000, 700000),
(12, 'Nhà Hàng Trúc Lâm Viên', 'nha-hang-truc-lam-vien', 6, '253 Nguyễn Văn Thoại', 'Đà Nẵng', '', 'Nhà hàng chay thanh đạm với thực đơn healthy đa dạng, không gian yên tĩnh và gần gũi thiên nhiên.', NULL, '02363810880', '09:00:00', '21:30:00', 'uploads/restaurants/ayp3GwEwMtZoQFzKiA0Xypp5oWIWxuhM3YhZ5uxq.jpg', 1, '2026-05-25 02:46:59', '2026-05-26 11:17:24', 80000, 200000),
(13, 'Madame Lân', 'madame-lan', 2, '4 Bạch Đằng, Hải Châu', 'Đà Nẵng', '', 'Nhà hàng nổi tiếng với các món đặc sản miền Trung, thiết kế đậm chất Việt và không gian rộng rãi ven sông.', NULL, '02363561936', '10:00:00', '22:00:00', 'uploads/restaurants/dUMFob25tqj4mH12RtEko1t5UR5EyqS3FwgBzQqt.jpg', 1, '2026-05-25 02:50:58', '2026-05-26 11:18:09', 120000, 400000),
(14, 'Mộc Seafood Restaurant', 'moc-seafood-restaurant', 3, '26 Tô Hiến Thành, An Hải', 'Đà Nẵng', '', 'Nhà hàng hải sản tươi sống được yêu thích nhờ nguyên liệu chất lượng và phong cách chế biến đậm vị biển miền Trung.', NULL, '0905665058', '10:30:00', '22:30:00', 'uploads/restaurants/1780497822_moc-seefood.jpg', 1, '2026-05-25 10:25:14', '2026-06-03 14:43:42', 100000, 500000),
(15, 'Thìa Gỗ Restaurant', 'thia-go-restaurant', 5, '53 Phan Thúc Duyện, Ngũ Hành Sơn,', 'Đà Nẵng', '', 'Nhà hàng chuyên ẩm thực Việt Nam truyền thống với không gian ấm cúng và thực đơn phù hợp khách du lịch.', NULL, '02363689005', '10:00:00', '22:00:00', 'uploads/restaurants/4wA38Fla8BvqbEsaCcnaKEsM3kvSxnlw5rkvJ2Nl.jpg', 1, '2026-05-25 10:32:33', '2026-05-26 11:19:24', 80000, 350000),
(16, 'Nhà hàng Sông Thu', 'nha-hang-song-thu', 7, '55 Trần Quang Khải', 'Hội An', '', 'Nhà hàng ven sông mang phong cách Hội An cổ kính, phục vụ hải sản và món Việt trong không gian thư giãn.', NULL, '02352241848', '09:00:00', '22:00:00', 'uploads/restaurants/pyfvJVdL3klzgHbtXNguC7VxWdaZcod9m40YCKpA.jpg', 1, '2026-05-25 10:42:56', '2026-05-26 11:20:10', 120000, 500000),
(17, 'Paddy Field Restaurant', 'paddy-field-restaurant', 2, '215 Lê Thánh Tông', 'Hội An', '', 'Nhà hàng kết hợp ẩm thực Việt và fusion, nổi bật với khung cảnh đồng quê yên bình và thiết kế gần gũi thiên nhiên.', NULL, '0865582846', '07:00:00', '22:00:00', 'uploads/restaurants/wUmbUwTlpiBeEoQDZEFUNXCcpGLAwebujnzFw1F0.jpg', 1, '2026-05-25 10:48:22', '2026-05-26 11:20:39', 120000, 450000),
(18, 'The Cargo Club', 'the-cargo-club', 5, '107–109 Nguyễn Thái Học', 'Hội An', '', 'Nhà hàng và café nổi tiếng tại phố cổ Hội An, phục vụ món Âu – Á cùng nhiều loại bánh ngọt hấp dẫn.', NULL, '02353910489', '09:00:00', '23:00:00', 'uploads/restaurants/7sKaNu4kXiBgDElpKr6iqcbE0jy7TBOImJ5JuepX.jpg', 1, '2026-05-25 03:08:26', '2026-05-26 11:21:10', 150000, 500000),
(19, 'Morning Glory Restaurant', 'morning-glory-restaurant', 2, '106 Nguyễn Thái Học', 'Hội An', '', 'Địa chỉ nổi tiếng để thưởng thức đặc sản Hội An và món ăn Việt Nam truyền thống trong không gian sang trọng.', NULL, '02352241555', '11:00:00', '22:00:00', 'uploads/restaurants/oBvXblxmbRFuiL3nG2G0TfxA0PiYVZOCPuvVdNfP.jpg', 1, '2026-05-25 03:11:29', '2026-05-26 11:21:47', 100000, 350000),
(20, 'Mango Mango', 'mango-mango', 3, '45 Trần Hưng Đạo', 'Hội An', '', 'Nhà hàng mang phong cách nhiệt đới sáng tạo, nổi bật với các món hải sản fusion và view đẹp gần sông Hoài.', NULL, '02353861839', '10:00:00', '22:30:00', 'uploads/restaurants/XGmoMmuWQimDX710RYmhyq7o1BpmWsr8FRmMeYwz.jpg', 1, '2026-05-25 03:15:41', '2026-05-26 11:22:13', 120000, 400000),
(21, 'Silk Village Restaurants Hoi An', 'silk-village-restaurants-hoi-an', 8, '28 Nguyễn Tất Thành', 'Hội An', '', 'Nhà hàng buffet đa dạng món Á – Âu trong khuôn viên làng lụa Hội An, thích hợp cho đoàn khách và gia đình.', NULL, '0918921144', '17:30:00', '22:00:00', 'uploads/restaurants/fpXBqBLbgzUMFxNo0kOKJeAtVj1n6yNLWSORUKxL.jpg', 1, '2026-05-25 11:41:46', '2026-05-26 11:23:37', 299000, 699000),
(22, 'Tokyo BBQ Town', 'tokyo-bbq-town', 4, '4 Phạm Văn Đồng, Sơn Trà', 'Đà Nẵng', '', 'Tokyo BBQ Town (Phố Nướng Tokyo) là chuỗi nhà hàng nổi tiếng với phong cách nướng BBQ và bia Nhật Bản. Không gian nhà hàng mang đậm dấu ấn xứ hoa anh đào cùng hệ thống nướng không khói hiện đại.', NULL, '0931544399', '11:00:00', '23:00:00', 'uploads/restaurants/1780498477_z7897423734276_294ee3a2eab1a1e71b91f3396ec85144.jpg', 1, '2026-06-03 14:54:37', '2026-06-03 16:01:30', 200000, 600000),
(23, 'CHÚ BI quán nướng', 'chu-bi-quan-nuong', 4, '48 Nguyễn Chí Thanh, Hải Châu', 'Đà Nẵng', '', 'CHÚ BI quán nướng là địa điểm buffet nướng lẩu nổi tiếng tại số 48 Nguyễn Chí Thanh, Hải Châu, Đà Nẵng. Quán thu hút thực khách nhờ thực đơn đa dạng từ thịt heo quê, bò Mỹ thượng hạng đến hải sản tươi sống trên quầy line tự chọn không giới hạn.', NULL, '0777399369', '17:00:00', '23:00:00', 'uploads/restaurants/1780499582_z7897423779481_5772785e9ba44a458632439d4a059516.jpg', 1, '2026-06-03 14:59:26', '2026-06-03 16:02:19', 200000, 300000),
(24, 'Rực Rỡ Beer', 'ruc-ro-beer', 7, '301 Chương Dương, Mỹ An', 'Đà Nẵng', '', 'Nhà hàng thu hút giới trẻ nhờ không gian rộng rãi, thoáng mát bên bờ sông và phong cách trẻ trung. Thực đơn tại đây rất đa dạng từ hải sản tươi sống đến các món nhậu bình dân với mức giá hợp lý.', NULL, '0935301301', '11:00:00', '12:00:00', 'uploads/restaurants/1780498998_z7897423734590_f769513f7a3a7926d56a293d324782cf.jpg', 1, '2026-06-03 15:03:18', '2026-06-03 16:02:56', 100000, 200000),
(25, 'Quán Nhậu Tự Do', 'quan-nhau-tu-do', 7, '8 Lê Đại Hành, Hai Bà Trưng', 'Hà Nội', '', 'Quán Nhậu Tự Do là hệ thống quán nhậu cực kỳ nổi tiếng tại Hà Nội. Nổi bật với không gian mở hiện đại, trẻ trung, nơi đây phục vụ thực đơn hơn 300 món nhậu đặc sắc và nhiều loại bia hấp dẫn. Đây là tụ điểm lý tưởng để tụ tập, liên hoan và ăn uống cùng bạn bè.', NULL, '02432021386', '09:00:00', '23:00:00', 'uploads/restaurants/1780499373_tu do.jpg', 1, '2026-06-03 15:09:33', '2026-06-03 16:03:49', 200000, 500000),
(26, 'Nhà Pá Lẩu & Nướng', 'nha-pa-lau-nuong', 4, 'Cuối hẻm 1041/62 đường Trần Xuân Soạn, Q.7', 'TP.HCM', '', 'Quán gây ấn tượng mạnh nhờ không gian sân vườn rộng rãi, view sông thoáng mát hiếm có và phong cách thiết kế mộc mạc mang đậm âm hưởng văn hóa đại ngàn Tây Bắc. Thực đơn tại đây xoay quanh các món nướng than hồng đậm vị và lẩu đặc sản (nổi bật là lẩu cá tầm măng chua). Nơi đây không chỉ chú trọng vào món ăn tươi ngon mà còn hướng tới sự quây quần, kết nối của các gia đình và nhóm bạn bên bếp lửa ấm áp.', NULL, '0906828824', '08:00:00', '22:00:00', 'uploads/restaurants/1780499533_pa.jpg', 1, '2026-06-03 15:12:13', '2026-06-03 16:04:10', 200000, 700000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `restaurant_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'Quán rộng rãi, sạch sẽ. Bún chả nướng rất thơm, nước chấm vừa miệng!', '2026-05-26 00:11:21', '2026-05-26 00:11:21'),
(2, 1, 1, 5, 'Đỉnh cao ẩm thực Hà Nội. Mình ăn ở đây từ hồi sinh viên đến giờ hương vị vẫn không đổi.', '2026-05-26 00:11:21', '2026-05-26 00:11:21'),
(3, 1, 1, 5, 'Nhân viên phục vụ nhiệt tình dù quán rất đông. Sẽ quay lại ủng hộ thường xuyên.', '2026-05-26 00:11:21', '2026-05-26 00:11:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('available','occupied','maintenance') NOT NULL DEFAULT 'available',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tables`
--

INSERT INTO `tables` (`id`, `restaurant_id`, `name`, `capacity`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 'VIP 1', 8, 'available', NULL, '2026-05-25 01:24:40', '2026-05-25 11:50:58'),
(2, 1, 'Bàn 01', 4, 'available', NULL, '2026-05-25 03:24:33', '2026-05-25 03:24:33'),
(3, 2, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:50:19', '2026-05-25 11:50:19'),
(4, 2, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:50:43', '2026-05-25 11:50:43'),
(5, 3, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:51:30', '2026-05-25 11:51:30'),
(6, 3, 'VIP 1', 8, 'available', NULL, '2026-05-25 11:51:47', '2026-05-25 11:51:47'),
(7, 4, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:52:00', '2026-05-25 11:52:00'),
(8, 4, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:52:16', '2026-05-25 11:52:16'),
(9, 5, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:52:29', '2026-05-25 11:52:29'),
(10, 5, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:52:39', '2026-05-25 11:52:39'),
(11, 6, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:52:53', '2026-05-25 11:52:53'),
(12, 6, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:53:05', '2026-05-25 11:53:05'),
(13, 7, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:53:16', '2026-05-25 11:53:16'),
(14, 7, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:53:26', '2026-05-25 11:53:26'),
(15, 8, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:53:38', '2026-05-25 11:53:38'),
(16, 8, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:53:52', '2026-05-25 11:53:52'),
(17, 9, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:54:45', '2026-05-25 11:54:45'),
(18, 9, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:55:00', '2026-05-25 11:55:00'),
(19, 10, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:55:13', '2026-05-25 11:55:13'),
(20, 10, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:55:24', '2026-05-25 11:55:24'),
(21, 11, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:55:47', '2026-05-25 11:55:47'),
(22, 11, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:56:00', '2026-05-25 11:56:00'),
(23, 12, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:56:15', '2026-05-25 11:56:15'),
(24, 12, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:56:23', '2026-05-25 11:56:23'),
(25, 13, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:56:31', '2026-05-25 11:56:31'),
(26, 13, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:56:39', '2026-05-25 11:56:39'),
(27, 14, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:56:57', '2026-05-25 11:56:57'),
(28, 14, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:57:04', '2026-05-25 11:57:04'),
(29, 15, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:57:16', '2026-05-25 11:57:16'),
(30, 15, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:57:31', '2026-05-25 11:57:31'),
(31, 16, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:57:40', '2026-05-25 11:57:40'),
(32, 16, 'VIP 1', 8, 'available', NULL, '2026-05-25 11:57:52', '2026-05-25 11:57:52'),
(33, 17, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:58:12', '2026-05-25 11:58:12'),
(34, 18, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:58:30', '2026-05-25 11:58:30'),
(35, 19, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:58:40', '2026-05-25 11:58:40'),
(36, 20, 'VIP 1', 4, 'available', NULL, '2026-05-25 11:58:49', '2026-05-25 11:58:49'),
(37, 21, 'Bàn 01', 4, 'available', NULL, '2026-05-25 11:58:56', '2026-05-25 11:58:56'),
(39, 17, 'VIP 1', 4, 'available', NULL, '2026-06-02 17:13:10', '2026-06-02 17:13:10'),
(40, 18, 'Bàn 01', 4, 'available', NULL, '2026-06-02 17:14:05', '2026-06-02 17:14:05'),
(41, 19, 'VIP 1', 4, 'available', NULL, '2026-06-02 17:14:21', '2026-06-02 17:14:21'),
(42, 20, 'Bàn 01', 4, 'available', NULL, '2026-06-02 17:14:43', '2026-06-02 17:14:43'),
(43, 21, 'VIP 1', 4, 'available', NULL, '2026-06-02 17:14:57', '2026-06-02 17:14:57'),
(44, 22, 'Bàn 01', 4, 'available', NULL, '2026-06-03 16:50:41', '2026-06-03 16:50:41'),
(45, 22, 'VIP 1', 4, 'available', NULL, '2026-06-03 16:50:52', '2026-06-03 16:50:52'),
(46, 24, 'Bàn 01', 4, 'available', NULL, '2026-06-03 16:50:59', '2026-06-03 16:50:59'),
(47, 24, 'VIP 1', 4, 'available', NULL, '2026-06-03 16:51:08', '2026-06-03 16:51:08'),
(48, 25, 'Bàn 01', 4, 'available', NULL, '2026-06-03 16:51:16', '2026-06-03 16:51:16'),
(49, 25, 'VIP 1', 4, 'available', NULL, '2026-06-03 16:51:23', '2026-06-03 16:51:23'),
(50, 26, 'Bàn 01', 4, 'available', NULL, '2026-06-03 16:51:31', '2026-06-03 16:51:31'),
(51, 26, 'VIP 1', 4, 'available', NULL, '2026-06-03 16:51:38', '2026-06-03 16:51:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `phone` varchar(255) DEFAULT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `is_banned`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$12$VBvGpYT1vw1aWcuVa5xgeuXokj1LcKehnZ5mhvocrSUrke.vqO0.W', 'admin', '0123456789', 0, NULL, '2026-05-23 23:29:25', '2026-05-23 23:29:25'),
(3, 'Hồng Quân', 'qhong3541@gmail.com', '$2y$12$nf7aNkn0dRzK77pw5QenY.PtwBLSu4aVSiKzVCH1GoaRtnWgoxFya', 'user', '0342505297', 0, NULL, '2026-05-26 08:51:19', '2026-05-26 08:51:19'),
(4, 'Nt Huyền', 'huyen66@gmail.com', '$2y$12$GnYHP9dqUjxIMGmZoYR13erq3a9tfI86bCBNh6arMBbSzF8DiBDmi', 'admin', '0392694486', 0, NULL, '2026-05-28 09:47:16', '2026-05-28 09:47:16');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `bookings_table_id_foreign` (`table_id`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_restaurant_id_foreign` (`restaurant_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_restaurant_id_foreign` (`restaurant_id`);

--
-- Chỉ mục cho bảng `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tables_restaurant_id_foreign` (`restaurant_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

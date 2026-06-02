-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th6 02, 2026 lúc 07:54 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `wedding`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bao_cao_ads`
--

CREATE TABLE `bao_cao_ads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ngay` varchar(255) DEFAULT NULL,
  `ads_tiktok` varchar(255) DEFAULT NULL,
  `ads_fb` varchar(255) DEFAULT NULL,
  `khach_moi` varchar(255) DEFAULT NULL,
  `lich_hen` varchar(255) DEFAULT NULL,
  `cpl` varchar(255) DEFAULT NULL,
  `roas` varchar(255) DEFAULT NULL,
  `ty_le_hen_tren_khach` varchar(255) DEFAULT NULL,
  `khach_den_cua_hang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bao_cao_ads`
--

INSERT INTO `bao_cao_ads` (`id`, `ngay`, `ads_tiktok`, `ads_fb`, `khach_moi`, `lich_hen`, `cpl`, `roas`, `ty_le_hen_tren_khach`, `khach_den_cua_hang`, `created_at`, `updated_at`) VALUES
(1, '20/11/2024', '20/11/2024a', '123', '20/11/2024', '20/11/2024', '20/11/2024', '20/11/2024', '20/11/2024', '20/11/2024', '2026-05-26 02:48:45', '2026-05-26 02:48:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Cấu trúc bảng cho bảng `cham_cong`
--

CREATE TABLE `cham_cong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `diem_danh_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_diem_danh` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `concept`
--

CREATE TABLE `concept` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_concept` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `trang_thai` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dang_ky_tu_van`
--

CREATE TABLE `dang_ky_tu_van` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_co_dau` varchar(150) NOT NULL,
  `ten_chu_re` varchar(150) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `ngay_cuoi_du_kien` date DEFAULT NULL,
  `phim_truong_quan_tam` varchar(100) DEFAULT NULL,
  `goi_dich_vu_quan_tam` varchar(100) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dang_ky_tu_van`
--

INSERT INTO `dang_ky_tu_van` (`id`, `ten_co_dau`, `ten_chu_re`, `so_dien_thoai`, `ngay_cuoi_du_kien`, `phim_truong_quan_tam`, `goi_dich_vu_quan_tam`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(1, 'Tên cô dâu', 'Tên chú rể', '0988777888', '2026-12-12', 'Biệt Thự Sora (Colonial Villa)', 'Gói Cảm xúc (11.9 triệu)', 'Ghi chú ý tưởng', '2026-05-14 07:45:36', '2026-05-14 07:45:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dich_vu_le`
--

CREATE TABLE `dich_vu_le` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_dich_vu` varchar(255) NOT NULL,
  `ma_dich_vu` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(3) UNSIGNED DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `gia_dich_vu` decimal(15,2) DEFAULT NULL,
  `don_vi` varchar(255) DEFAULT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phong_ban_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dich_vu_le`
--

INSERT INTO `dich_vu_le` (`id`, `ten_dich_vu`, `ma_dich_vu`, `slug`, `mo_ta`, `trang_thai`, `ghi_chu`, `gia_dich_vu`, `don_vi`, `nguoi_tao_id`, `phong_ban_id`, `created_at`, `updated_at`) VALUES
(1, 'Thuê váy chụp', 'DVC1', 'thue-vay-chup', NULL, 1, NULL, 2000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(2, 'Thuê vest chú rể', 'DVC2', 'thue-vest-chu-re', NULL, 1, NULL, 1500000.00, 'bộ', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(3, 'Thuê cặp áo dài chụp / cổ phục', 'DVC3', 'thue-cap-ao-dai-chup-co-phuc', NULL, 1, NULL, 2000000.00, 'cặp', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(4, 'Váy cưới mang đi chụp', 'DVC4', 'vay-cuoi-mang-di-chup', NULL, 1, NULL, 5000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(5, 'Thêm điểm chụp thứ 3 nội thành HN', 'DVC5', 'them-diem-chup-thu-3-noi-thanh-hn', NULL, 1, NULL, 3000000.00, 'điểm', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(6, 'Vé vào Santo', 'DVC6', 've-vao-santo', NULL, 1, NULL, 500000.00, 'ekip', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(7, 'Vé vào 5garden', 'DVC7', 've-vao-5garden', NULL, 1, NULL, 450000.00, '1 tiếng', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(8, 'Vé vào đại học dược', 'DVC8', 've-vao-dai-hoc-duoc', NULL, 1, NULL, 200000.00, 'ekip', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(9, 'Vé vào bảo tàng nghệ thuật', 'DVC9', 've-vao-bao-tang-nghe-thuat', NULL, 1, NULL, 1000000.00, 'ekip', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(10, 'Vé vào lung linh', 'DVC10', 've-vao-lung-linh', NULL, 1, NULL, 750000.00, '1 tiếng', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(11, 'Vé vào Văn Miếu', 'DVC11', 've-vao-van-mieu', NULL, 1, NULL, 30000.00, '1 người', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(12, 'Hoa tươi theo khóm', 'DVC12', 'hoa-tuoi-theo-khom', NULL, 1, NULL, 600000.00, '1 khóm', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(13, 'Hoa hồng tung ( hoa thật )', 'DVC13', 'hoa-hong-tung-hoa-that', NULL, 1, NULL, 25000.00, 'cành', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(14, 'Phao vàng tung', 'DVC14', 'phao-vang-tung', NULL, 1, NULL, 200000.00, 'túi', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(15, 'Pháo điện', 'DVC15', 'phao-dien', NULL, 1, NULL, 100000.00, 'ống', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(16, 'Pháo ống', 'DVC16', 'phao-ong', NULL, 1, NULL, 100000.00, 'ống', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(17, 'Pháo que', 'DVC17', 'phao-que', NULL, 1, NULL, 200000.00, 'túi', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(18, 'Bảng tên CDCR fomex cứng', 'DVC18', 'bang-ten-cdcr-fomex-cung', NULL, 1, NULL, 500000.00, '1 bảng', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(19, 'Poster OUR WEDDING', 'DVC19', 'poster-our-wedding', NULL, 1, NULL, 300000.00, '1 tấm', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(20, 'Thuê kẹp tóc giả', 'DVC20', 'thue-kep-toc-gia', NULL, 1, NULL, 500000.00, '1 lần', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(21, 'Make test', 'DVC21', 'make-test', NULL, 1, NULL, 1000000.00, '1 lần', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(22, 'Laminate -> Mica cạnh', 'SP1', 'laminate-mica-canh', NULL, 1, NULL, 400000.00, '1 ảnh', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(23, 'Laminate -> Mica HD', 'SP2', 'laminate-mica-hd', NULL, 1, NULL, 600000.00, '1 ảnh', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(24, 'Laminate - > Mica HD khung trắng họa tiết', 'SP3', 'laminate-mica-hd-khung-trang-hoa-tiet', NULL, 1, NULL, 1000000.00, '1 ảnh', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(25, 'Chỉnh thêm ảnh', 'SP4', 'chinh-them-anh', NULL, 1, NULL, 50000.00, '1 ảnh', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(26, 'In thêm trang album', 'SP5', 'in-them-trang-album', NULL, 1, NULL, 200000.00, '1 trang', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(27, 'Bộ khung 9 ảnh Hàn Quốc', 'SP6', 'bo-khung-9-anh-han-quoc', NULL, 1, NULL, 1200000.00, 'bộ', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(28, 'Album 25x25 ( 20 trang )', 'SP7', 'album-25x25-20-trang', NULL, 1, NULL, 1200000.00, 'quyển', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(29, 'Album 25x30 ( 20 trang )', 'SP8', 'album-25x30-20-trang', NULL, 1, NULL, 1500000.00, 'quyển', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(30, 'Album 30x30 ( 20 trang )', 'SP9', 'album-30x30-20-trang', NULL, 1, NULL, 1700000.00, 'quyển', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(31, 'Album 25x35 ( 20 trang )', 'SP10', 'album-25x35-20-trang', NULL, 1, NULL, 1900000.00, 'quyển', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(32, 'Ảnh in 13x18', 'SP11', 'anh-in-13x18', NULL, 1, NULL, 18000.00, 'ảnh', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(33, 'Thuê thêm vest chú rể', 'DVT1', 'thue-them-vest-chu-re', NULL, 1, NULL, 1500000.00, 'bộ', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(34, 'Thuê váy đi bàn', 'DVT2', 'thue-vay-di-ban', NULL, 1, NULL, 2000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(35, 'Thuê lẻ váy Lux cũ', 'DVT3', 'thue-le-vay-lux-cu', NULL, 1, NULL, 5000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(36, 'Thuê lẻ váy Lux mới', 'DVT4', 'thue-le-vay-lux-moi', NULL, 1, NULL, 7000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(37, 'Thuê lẻ váy Sig', 'DVT5', 'thue-le-vay-sig', NULL, 1, NULL, 9000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(38, 'Thuê lẻ váy Limited', 'DVT6', 'thue-le-vay-limited', NULL, 1, NULL, 12000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(39, 'Up váy từ Lux cũ - Lux nước đầu', 'DVT7', 'up-vay-tu-lux-cu-lux-nuoc-dau', NULL, 1, NULL, 2000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(40, 'Up váy từ Lux - Sig', 'DVT8', 'up-vay-tu-lux-sig', NULL, 1, NULL, 6000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(41, 'Up váy từ Lux- Limited', 'DVT9', 'up-vay-tu-lux-limited', NULL, 1, NULL, 9000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(42, 'Up váy Sig - Limited', 'DVT10', 'up-vay-sig-limited', NULL, 1, NULL, 4000000.00, 'váy', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(43, 'Thuê áo dài dâu/ rể', 'DVT11', 'thue-ao-dai-dau-re', NULL, 1, NULL, 2000000.00, 'cặp', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(44, 'Thuê nơ - cavat', 'DVT12', 'thue-no-cavat', NULL, 1, NULL, 1000000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(45, 'Thuê voan đỏ lỡ', 'DVT13', 'thue-voan-do-lo', NULL, 1, NULL, 1000000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(46, 'Thuê tráp', 'DVT14', 'thue-trap', NULL, 1, NULL, 150000.00, 'áo', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(47, 'Make cd', 'DVT15', 'make-cd', NULL, 1, NULL, 2000000.00, 'lần', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(48, 'Make mẹ', 'DVT16', 'make-me', NULL, 1, NULL, 1000000.00, 'lần', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(49, 'Make người nhà', 'DVT17', 'make-nguoi-nha', NULL, 1, NULL, 600000.00, 'lần', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(50, 'Thiệp Onl', 'DVT18', 'thiep-onl', NULL, 1, NULL, 250000.00, 'thiệp', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(51, 'Slideshow', 'DVT19', 'slideshow', NULL, 1, NULL, 0.00, NULL, NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(52, 'Ảnh bàn 15x12 gỗ', 'DVT20', 'anh-ban-15x12-go', NULL, 1, NULL, 120000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(53, 'Ảnh bàn 15x21 mica cạnh', 'DVT21', 'anh-ban-15x21-mica-canh', NULL, 1, NULL, 150000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(54, 'Ảnh bàn 15x21 mica HD', 'DVT22', 'anh-ban-15x21-mica-hd', NULL, 1, NULL, NULL, NULL, NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(55, 'Ảnh bàn 20x30 gỗ', 'DVT23', 'anh-ban-20x30-go', NULL, 1, NULL, 150000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(56, 'Ảnh bàn 20x30 mica cạnh', 'DVT24', 'anh-ban-20x30-mica-canh', NULL, 1, NULL, 200000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(57, 'Ảnh bàn 20x30 mica hd', 'DVT25', 'anh-ban-20x30-mica-hd', NULL, 1, NULL, 850000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(58, 'Ảnh phóng 60x90 gỗ', 'DVT26', 'anh-phong-60x90-go', NULL, 1, NULL, 1100000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(59, 'Ảnh phóng 60x90 mica cạnh', 'DVT27', 'anh-phong-60x90-mica-canh', NULL, 1, NULL, 650000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(60, 'Ảnh phóng 50x75 gỗ', 'DVT28', 'anh-phong-50x75-go', NULL, 1, NULL, 850000.00, 'cái', NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL),
(61, 'Ảnh phóng 50x75mica cạnh', 'DVT29', 'anh-phong-50x75-mica-canh', NULL, 1, NULL, NULL, NULL, NULL, 'LTS01, KTS01, DPS01, CSKS01', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dich_vu_trong_hop_dong`
--

CREATE TABLE `dich_vu_trong_hop_dong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_hop_dong` bigint(20) UNSIGNED NOT NULL,
  `id_dich_vu` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `gia_goc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `gia_thuc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `thanh_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `diem_danh`
--

CREATE TABLE `diem_danh` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `gio_vao` datetime DEFAULT NULL,
  `gio_ra` datetime DEFAULT NULL,
  `di_muon` tinyint(1) NOT NULL DEFAULT 0,
  `hop_le` tinyint(1) NOT NULL DEFAULT 0,
  `ly_do` varchar(255) DEFAULT NULL,
  `nghi_phep` tinyint(1) NOT NULL DEFAULT 0,
  `loai_phep` varchar(255) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `gio_lam_co_ban` decimal(8,2) NOT NULL DEFAULT 0.00,
  `gio_lam_tang_ca` decimal(8,2) NOT NULL DEFAULT 0.00,
  `luong_co_ban` decimal(15,2) NOT NULL DEFAULT 0.00,
  `luong_tang_ca` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Cấu trúc bảng cho bảng `hop_dong`
--

CREATE TABLE `hop_dong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_hop_dong` varchar(255) DEFAULT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tho_chup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tho_make_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tho_edit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dia_diem` varchar(255) DEFAULT NULL,
  `ngay_chup` datetime DEFAULT NULL,
  `trang_phuc` text DEFAULT NULL,
  `concept` text DEFAULT NULL,
  `ghi_chu_chup` text DEFAULT NULL,
  `trang_thai_chup` varchar(50) DEFAULT NULL,
  `tong_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `nguoi_gioi_thieu` varchar(255) DEFAULT NULL,
  `so_tien_giam_gia` decimal(14,2) DEFAULT NULL,
  `thanh_toan_lan_1` decimal(15,2) DEFAULT NULL,
  `anh_thanh_toan_1` varchar(255) DEFAULT NULL,
  `thanh_toan_lan_2` decimal(15,2) DEFAULT NULL,
  `anh_thanh_toan_2` varchar(255) DEFAULT NULL,
  `thanh_toan_lan_3` decimal(15,2) DEFAULT NULL,
  `anh_thanh_toan_3` varchar(255) DEFAULT NULL,
  `trang_thai_hop_dong` varchar(50) DEFAULT NULL,
  `trang_thai_edit` varchar(50) DEFAULT NULL,
  `link_file_demo` varchar(255) DEFAULT NULL,
  `link_file_in` varchar(255) DEFAULT NULL,
  `ngay_tra_link_in` date DEFAULT NULL,
  `ngay_hen_tra_hang` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cho_thue_trang_phuc`
--

CREATE TABLE `hop_dong_cho_thue_trang_phuc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_khach_hang` varchar(255) NOT NULL,
  `sdt_khach_hang` varchar(20) NOT NULL,
  `ngay_thue` date NOT NULL,
  `ngay_tra_du_kien` date NOT NULL,
  `ngay_tra_chinh_thuc` date DEFAULT NULL,
  `so_ngay_thue` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `tong_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tien_coc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0,
  `ghi_chu` text DEFAULT NULL,
  `nguoi_cho_thue` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cuoi`
--

CREATE TABLE `hop_dong_cuoi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_hop_dong` varchar(30) NOT NULL,
  `loai_hop_dong` varchar(100) DEFAULT NULL,
  `ten_co_dau` varchar(150) NOT NULL,
  `ten_chu_re` varchar(150) NOT NULL,
  `email_sdt_co_dau` text DEFAULT NULL,
  `email_sdt_chu_re` text DEFAULT NULL,
  `ngay_chup_du_kien` date DEFAULT NULL,
  `ngay_chup_thuc_te` date DEFAULT NULL,
  `gio_chup` time DEFAULT NULL,
  `buoi_chup` enum('sang','chieu','ca_ngay') DEFAULT NULL,
  `ngay_cuoi_du_kien` date DEFAULT NULL,
  `ngay_cuoi_chinh_thuc` date DEFAULT NULL,
  `dia_diem_chup` text DEFAULT NULL,
  `concept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_dich_vu` enum('combo_tron_goi','ghep_dich_vu_le','combo_va_nang_cap') DEFAULT NULL,
  `nhom_dich_vu_id` bigint(20) NOT NULL DEFAULT -1,
  `kenh_tiep_can` varchar(100) DEFAULT NULL,
  `yeu_cau_dac_biet` text DEFAULT NULL,
  `tong_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `chiet_khau` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tien_coc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `trang_thai_hop_dong` enum('nhap','da_huy','dang_thuc_hien','tre_chup','tre_edit') NOT NULL DEFAULT 'nhap',
  `link_demo` varchar(500) DEFAULT NULL,
  `ngay_tra_link_demo_du_kien` date DEFAULT NULL,
  `ngay_tra_link_demo_chinh_thuc` date DEFAULT NULL,
  `ngay_up_link_demo_gan_nhat` datetime DEFAULT NULL,
  `nguoi_up_link_demo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `link_in` varchar(500) DEFAULT NULL,
  `ngay_tra_link_in_du_kien` date DEFAULT NULL,
  `ngay_tra_link_in_chinh_thuc` date DEFAULT NULL,
  `ngay_up_link_in_gan_nhat` datetime DEFAULT NULL,
  `nguoi_up_link_in_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu_sale` text DEFAULT NULL,
  `tho_chup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tho_make_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tho_edit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_ky_hop_dong` date DEFAULT NULL,
  `han_thanh_toan_lan2` date DEFAULT NULL,
  `han_thanh_toan_lan3` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hop_dong_cuoi`
--

INSERT INTO `hop_dong_cuoi` (`id`, `ma_hop_dong`, `loai_hop_dong`, `ten_co_dau`, `ten_chu_re`, `email_sdt_co_dau`, `email_sdt_chu_re`, `ngay_chup_du_kien`, `ngay_chup_thuc_te`, `gio_chup`, `buoi_chup`, `ngay_cuoi_du_kien`, `ngay_cuoi_chinh_thuc`, `dia_diem_chup`, `concept_id`, `loai_dich_vu`, `nhom_dich_vu_id`, `kenh_tiep_can`, `yeu_cau_dac_biet`, `tong_tien`, `chiet_khau`, `tien_coc`, `trang_thai_hop_dong`, `link_demo`, `ngay_tra_link_demo_du_kien`, `ngay_tra_link_demo_chinh_thuc`, `ngay_up_link_demo_gan_nhat`, `nguoi_up_link_demo_id`, `link_in`, `ngay_tra_link_in_du_kien`, `ngay_tra_link_in_chinh_thuc`, `ngay_up_link_in_gan_nhat`, `nguoi_up_link_in_id`, `ghi_chu_sale`, `tho_chup_id`, `tho_make_id`, `tho_edit_id`, `ngay_ky_hop_dong`, `han_thanh_toan_lan2`, `han_thanh_toan_lan3`, `created_at`, `updated_at`, `created_by`) VALUES
(1, '2805261', NULL, 'b', 'a', NULL, NULL, '2026-05-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'combo_tron_goi', 1, NULL, NULL, 8600000.00, 0.00, 0.00, 'dang_thuc_hien', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-28', NULL, NULL, '2026-05-28 21:52:25', '2026-05-28 21:53:04', 25),
(2, '2805262', NULL, 'cô dâu', 'Chú rể', NULL, NULL, NULL, '2026-05-29', '16:00:00', NULL, NULL, NULL, NULL, NULL, 'combo_va_nang_cap', 1, NULL, NULL, 21400000.00, 0.00, 0.00, 'dang_thuc_hien', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, 3, NULL, '2026-05-28', NULL, NULL, '2026-05-28 22:38:44', '2026-05-29 07:42:25', 25),
(3, '2905263', NULL, 'cô dâu', 'a', NULL, NULL, NULL, '2026-06-03', NULL, NULL, NULL, NULL, NULL, NULL, 'ghep_dich_vu_le', -1, NULL, NULL, 1200000.00, 0.00, 0.00, 'dang_thuc_hien', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-29 07:49:42', '2026-06-02 12:20:34', 25),
(4, '0206264', NULL, 'a', '', NULL, NULL, NULL, '2026-06-04', '15:00:00', NULL, NULL, '2026-06-19', 'Công viên Thủ Lệ', NULL, NULL, -1, NULL, NULL, 0.00, 0.00, 0.00, 'dang_thuc_hien', NULL, NULL, '2026-06-18', NULL, NULL, NULL, NULL, '2026-06-19', NULL, NULL, 'ghi chú sale', 2, 4, 3, NULL, NULL, NULL, '2026-06-02 11:24:10', '2026-06-02 12:11:26', 1),
(5, '0206265', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, -1, NULL, NULL, 0.00, 0.00, 0.00, 'nhap', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 12:20:17', '2026-06-02 12:20:17', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cuoi_dich_vu_le`
--

CREATE TABLE `hop_dong_cuoi_dich_vu_le` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dich_vu_le_id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_cuoi_id` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hop_dong_cuoi_dich_vu_le`
--

INSERT INTO `hop_dong_cuoi_dich_vu_le` (`id`, `dich_vu_le_id`, `hop_dong_cuoi_id`, `so_luong`, `created_at`, `updated_at`) VALUES
(487, 28, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(488, 31, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(489, 41, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(490, 8, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(491, 6, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(493, 28, 3, 1, '2026-06-02 05:26:51', '2026-06-02 05:26:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cuoi_nhom_dich_vu`
--

CREATE TABLE `hop_dong_cuoi_nhom_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dich_vu_le_id` bigint(20) UNSIGNED NOT NULL,
  `nhom_dich_vu_id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_cuoi_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai_su_dung` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hop_dong_cuoi_nhom_dich_vu`
--

INSERT INTO `hop_dong_cuoi_nhom_dich_vu` (`id`, `dich_vu_le_id`, `nhom_dich_vu_id`, `hop_dong_cuoi_id`, `trang_thai_su_dung`, `created_at`, `updated_at`) VALUES
(302, 12, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(303, 13, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(304, 24, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(305, 25, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(306, 26, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(307, 27, 1, 1, 1, '2026-05-28 14:52:32', '2026-05-28 14:52:32'),
(308, 12, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(309, 13, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(310, 24, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(311, 25, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(312, 26, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36'),
(313, 27, 1, 2, 1, '2026-05-28 15:39:36', '2026-05-28 15:39:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cuoi_thanh_vien_sale`
--

CREATE TABLE `hop_dong_cuoi_thanh_vien_sale` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_id` bigint(20) UNSIGNED NOT NULL,
  `nhan_vien_id` bigint(20) UNSIGNED NOT NULL,
  `vai_tro` enum('nguoi_tao','thanh_vien') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hop_dong_cuoi_thanh_vien_sale`
--

INSERT INTO `hop_dong_cuoi_thanh_vien_sale` (`id`, `hop_dong_id`, `nhan_vien_id`, `vai_tro`, `created_at`, `updated_at`) VALUES
(441, 1, 25, 'nguoi_tao', '2026-05-28 14:52:25', '2026-05-28 14:52:25'),
(442, 2, 25, 'nguoi_tao', '2026-05-28 15:38:44', '2026-05-28 15:38:44'),
(443, 3, 25, 'nguoi_tao', '2026-05-29 00:49:42', '2026-05-29 00:49:42'),
(444, 4, 1, 'nguoi_tao', '2026-06-02 04:24:10', '2026-06-02 04:24:10'),
(445, 5, 1, 'nguoi_tao', '2026-06-02 05:20:17', '2026-06-02 05:20:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_cuoi_trang_phuc`
--

CREATE TABLE `hop_dong_cuoi_trang_phuc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_cuoi_id` bigint(20) UNSIGNED NOT NULL,
  `trang_phuc_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_dich_vu_le`
--

CREATE TABLE `hop_dong_dich_vu_le` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_id` bigint(20) UNSIGNED NOT NULL,
  `dich_vu_le_id` bigint(20) UNSIGNED NOT NULL,
  `gia_goc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `gia_thuc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_thanh_toan`
--

CREATE TABLE `hop_dong_thanh_toan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_id` bigint(20) UNSIGNED NOT NULL,
  `lan_thanh_toan` tinyint(3) UNSIGNED NOT NULL,
  `so_tien` decimal(15,2) NOT NULL,
  `ngay_thanh_toan` date NOT NULL,
  `hinh_thuc_thanh_toan` varchar(50) DEFAULT NULL,
  `proof_urls` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`proof_urls`)),
  `ghi_chu` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
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
(4, '2026_06_02_000001_create_nhan_su_va_he_thong_tables', 1),
(5, '2026_06_02_000002_create_dich_vu_tables', 1),
(6, '2026_06_02_000003_create_trang_phuc_tables', 1),
(7, '2026_06_02_000004_create_hop_dong_tables', 1),
(8, '2026_06_02_000005_create_hop_dong_cuoi_tables', 1),
(9, '2026_06_02_000006_create_tai_chinh_tables', 1),
(10, '2026_06_02_000007_create_marketing_tables', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ngan_hang_thanh_toan`
--

CREATE TABLE `ngan_hang_thanh_toan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hinh_anh_logo` varchar(500) DEFAULT NULL,
  `ten_ngan_hang` varchar(150) NOT NULL,
  `ten_chi_tiet` varchar(255) DEFAULT NULL,
  `so_tai_khoan` varchar(50) NOT NULL,
  `chu_tai_khoan` varchar(150) NOT NULL,
  `chi_nhanh` varchar(255) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ngan_hang_thanh_toan`
--

INSERT INTO `ngan_hang_thanh_toan` (`id`, `hinh_anh_logo`, `ten_ngan_hang`, `ten_chi_tiet`, `so_tai_khoan`, `chu_tai_khoan`, `chi_nhanh`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'https://news.mbbank.com.vn/file-service/uploads/v1/images/c21788de-1a22-48e0-a4ca-7bda44d5b2b4-logo-bidv-20220426071253.jpg?width=947&height=366', 'BIDV', 'Ngân hàng Thương mại cổ phần Đầu tư và Phát triển Việt Nam', '11223344', 'Nguyễn Văn Anh', 'Tây Hồ', 1, '2026-04-10 07:54:47', '2026-04-10 07:54:47'),
(2, 'https://inkythuatso.com/uploads/images/2021/09/logo-techcombank-inkythuatso-10-15-11-46.jpg', 'techcombank', 'Ngân hàng kỹ thương việt nam', '1122334455', 'Phùng Xuân Ngọc', 'Hà Nam', 1, '2026-04-10 08:31:24', '2026-04-10 08:33:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phong_ban` varchar(255) DEFAULT NULL,
  `ngan_hang` varchar(255) DEFAULT NULL,
  `chi_nhanh` varchar(255) DEFAULT NULL,
  `so_tai_khoan` varchar(255) DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `vi_tri_lam_viec` varchar(255) DEFAULT NULL,
  `ngay_vao_cong_ty` date DEFAULT NULL,
  `ngay_ky_hop_dong` date DEFAULT NULL,
  `luong_co_ban` bigint(20) UNSIGNED DEFAULT NULL,
  `luong_tang_ca` bigint(20) UNSIGNED DEFAULT NULL,
  `ds_menu` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ds_menu`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien`
--

INSERT INTO `nhan_vien` (`id`, `hinh_anh`, `user_id`, `phong_ban`, `ngan_hang`, `chi_nhanh`, `so_tai_khoan`, `gioi_tinh`, `ngay_sinh`, `cccd`, `vi_tri_lam_viec`, `ngay_vao_cong_ty`, `ngay_ky_hop_dong`, `luong_co_ban`, `luong_tang_ca`, `ds_menu`, `created_at`, `updated_at`) VALUES
(1, 'nhan-vien/dhrUS3EZefNaMKobOq07Z1DAmQgiIe0C88PPqMvG.png', 1, NULL, NULL, NULL, NULL, 'nam', '1996-02-27', '123456789', 'Kỹ thuật viên', '2020-11-12', '2020-12-12', 50000, 80000, '[\"admin.nhan-su.lich-lam-viec.data\",\"admin.nhan-su.lich-lam-viec.chi-tiet-ngay\",\"admin.diem-danh.dieu-phoi-cong-viec\",\"admin.he-thong.ngan-hang-thanh-toan\",\"admin.he-thong.phong-ban\",\"admin.nhan-su.lich-lam-viec\",\"admin.index\",\"admin.khach-hang.danh-sach-hop-dong-cuoi\",\"admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay\",\"admin.khach-hang.chinh-sua-hop-dong-cuoi\",\"admin.khach-hang.tao-hop-dong-canh-bao\",\"admin.khach-hang.tao-hop-dong\",\"admin.lich-lam-viec\",\"admin.trang-phuc.san-pham\",\"admin.trang-phuc.hop-dong\",\"admin.tu-van.danh-sach\",\"admin.diem-danh.diem-danh\",\"admin.diem-danh.cham-cong\",\"admin.diem-danh.check-in\",\"admin.diem-danh.check-out\",\"admin.nhan-su.danh-sach\",\"admin.nhan-su.phan-quyen\",\"admin.nhan-su.cong-viec-cua-toi\",\"admin.concept.concept\",\"admin.dich-vu.dich-vu-le\",\"admin.dich-vu.nhom-dich-vu\",\"admin.tai-chinh.cong-no\",\"admin.tai-chinh.phieu-thu-chi\",\"admin.tai-chinh.tinh-luong\",\"admin.hen-lich\",\"admin.note-khach-moi\",\"admin.bao-cao-ads\",\"admin.thong-tin-ca-nhan\",\"admin.he-thong.tai-lieu\"]', '2026-03-04 12:18:04', '2026-05-24 19:27:43'),
(2, NULL, 2, 'LTS01', NULL, NULL, NULL, 'Nữ', '2004-04-16', NULL, 'Chuyên viên tư vấn', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:02', '2026-05-27 01:47:02'),
(3, NULL, 3, 'LTS01', NULL, NULL, NULL, 'Nữ', '2004-02-28', NULL, 'Chuyên viên tư vấn', '2025-10-11', NULL, NULL, NULL, NULL, '2026-05-27 01:47:02', '2026-05-27 01:47:02'),
(4, NULL, 4, 'LTS01', NULL, NULL, NULL, 'Nữ', '2005-05-13', NULL, 'Chuyên viên tư vấn', '2025-10-10', NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(5, NULL, 5, 'LTS01', NULL, NULL, NULL, 'Nữ', '2005-10-10', '352391708', 'Chuyên viên tư vấn', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(6, NULL, 6, 'LTS01', NULL, NULL, NULL, 'Nữ', '2000-10-16', NULL, 'Leader Sale', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(7, NULL, 7, 'LTS01', NULL, NULL, NULL, 'Nữ', '2008-07-10', NULL, 'Leader Trang phục', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(8, NULL, 8, 'LTS01', NULL, NULL, NULL, 'Nữ', '2004-05-29', NULL, 'Chuyên viên tư vấn', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(9, NULL, 9, 'LTS01', NULL, NULL, NULL, 'Nữ', '2005-04-09', NULL, 'Lễ tân phòng váy', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(10, NULL, 10, 'LTS01', NULL, NULL, NULL, 'Nữ', '2002-06-28', '38302013255', 'Lễ tân phòng váy', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(11, NULL, 11, 'PS01', NULL, NULL, NULL, 'Nam', '1998-10-25', NULL, 'Hậu kì', '2024-06-21', NULL, NULL, NULL, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(12, NULL, 12, 'PS01', NULL, NULL, NULL, 'Nam', NULL, NULL, 'Hậu kì', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(13, NULL, 13, 'PS01', NULL, NULL, NULL, 'Nam', NULL, NULL, 'Hậu kì', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(14, NULL, 14, 'PS01', NULL, NULL, NULL, 'Nam', NULL, NULL, 'Hậu kì', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(15, NULL, 15, 'PS01', NULL, NULL, NULL, 'Nam', NULL, NULL, 'Hậu kì', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(16, NULL, 16, 'MKS01', NULL, NULL, NULL, 'Nam', '2003-04-14', NULL, 'Leader Performance', '2024-11-20', NULL, NULL, NULL, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(17, NULL, 17, 'MKS01', NULL, NULL, NULL, 'Nam', '2003-05-06', NULL, 'Leader Media', '2024-04-30', NULL, NULL, NULL, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(18, NULL, 18, 'MKS01', NULL, NULL, NULL, 'Nữ', '2003-05-05', NULL, 'Content', '2025-06-16', NULL, NULL, NULL, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(19, NULL, 19, 'MKS01', NULL, NULL, NULL, 'Nữ', NULL, NULL, 'VJ Content', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(20, NULL, 20, 'MS01', NULL, NULL, NULL, 'Nữ', '2002-09-20', NULL, 'Makeup', '2022-09-20', NULL, NULL, NULL, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(21, NULL, 21, 'KTS01', NULL, NULL, NULL, 'Nữ', '2004-10-28', '30304001372', 'Kế toán - HCNS', '2025-10-13', NULL, NULL, NULL, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(22, NULL, 22, 'DPS01', NULL, NULL, NULL, 'Nữ', '1997-07-10', NULL, 'Điều phối', '2025-12-16', NULL, NULL, NULL, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(23, NULL, 23, 'CSKS01', NULL, NULL, NULL, 'Nữ', '2003-02-25', NULL, 'Chăm sóc khách hàng', NULL, NULL, NULL, NULL, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(24, NULL, 24, 'RDS01', NULL, NULL, NULL, 'Nam', '1999-10-30', NULL, 'Lead Sản Phẩm', '2023-03-24', NULL, NULL, NULL, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(25, NULL, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Quản lý chung', NULL, NULL, NULL, NULL, '[\"admin.nhan-su.lich-lam-viec.data\",\"admin.nhan-su.lich-lam-viec.chi-tiet-ngay\",\"admin.diem-danh.dieu-phoi-cong-viec\",\"admin.he-thong.ngan-hang-thanh-toan\",\"admin.he-thong.phong-ban\",\"admin.nhan-su.lich-lam-viec\",\"admin.index\",\"admin.khach-hang.danh-sach-hop-dong-cuoi\",\"admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay\",\"admin.khach-hang.chinh-sua-hop-dong-cuoi\",\"admin.khach-hang.tao-hop-dong-canh-bao\",\"admin.khach-hang.tao-hop-dong\",\"admin.lich-lam-viec\",\"admin.trang-phuc.san-pham\",\"admin.trang-phuc.hop-dong\",\"admin.tu-van.danh-sach\",\"admin.diem-danh.diem-danh\",\"admin.diem-danh.cham-cong\",\"admin.diem-danh.check-in\",\"admin.diem-danh.check-out\",\"admin.nhan-su.danh-sach\",\"admin.nhan-su.phan-quyen\",\"admin.nhan-su.cong-viec-cua-toi\",\"admin.concept.concept\",\"admin.dich-vu.dich-vu-le\",\"admin.dich-vu.nhom-dich-vu\",\"admin.tai-chinh.cong-no\",\"admin.tai-chinh.phieu-thu-chi\",\"admin.tai-chinh.tinh-luong\",\"admin.hen-lich\",\"admin.note-khach-moi\",\"admin.bao-cao-ads\",\"admin.thong-tin-ca-nhan\",\"admin.he-thong.tai-lieu\"]', '2026-05-27 01:47:07', '2026-05-27 01:47:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien_phong_ban`
--

CREATE TABLE `nhan_vien_phong_ban` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nhan_vien_id` bigint(20) UNSIGNED NOT NULL,
  `phong_ban_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien_phong_ban`
--

INSERT INTO `nhan_vien_phong_ban` (`id`, `nhan_vien_id`, `phong_ban_id`, `created_at`, `updated_at`) VALUES
(110, 2, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(111, 3, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(112, 4, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(113, 5, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(114, 6, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(115, 7, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(116, 8, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(117, 9, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(118, 10, 1, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(119, 11, 4, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(120, 12, 4, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(121, 13, 4, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(122, 14, 4, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(123, 15, 4, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(124, 16, 5, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(125, 17, 5, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(126, 18, 5, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(127, 19, 5, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(128, 20, 3, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(129, 21, 8, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(130, 22, 9, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(131, 23, 10, '2026-05-27 02:21:44', '2026-05-27 02:21:44'),
(132, 24, 7, '2026-05-27 02:21:44', '2026-05-27 02:21:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhom_dich_vu`
--

CREATE TABLE `nhom_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_nhom` varchar(255) NOT NULL,
  `ma_nhom` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `gia_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `gia_goc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `the` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhom_dich_vu`
--

INSERT INTO `nhom_dich_vu` (`id`, `ten_nhom`, `ma_nhom`, `slug`, `gia_tien`, `gia_goc`, `the`, `ghi_chu`, `mo_ta`, `trang_thai`, `nguoi_tao_id`, `created_at`, `updated_at`) VALUES
(1, 'LUXURY 2', 'Prewedding', 'luxury-2', 8600000.00, 10780000.00, 'LUXURY 2', NULL, NULL, 1, 25, '2026-05-28 14:49:28', '2026-05-30 07:42:21'),
(3, 'STUDIO 1', 'STU1', 'studio-1', 4600000.00, 3250000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 07:43:52', '2026-05-30 09:45:51'),
(4, 'STUDIO 2', 'STU2', 'studio-2', 6300000.00, 4450000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 07:45:00', '2026-05-30 07:45:00'),
(5, 'STUDIO3', 'STU', 'studio3', 7600000.00, 7880000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 07:53:15', '2026-05-30 07:56:42'),
(6, 'LUXURY 1', 'LUX1', 'luxury-1', 7900000.00, 5650000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 07:54:30', '2026-05-30 07:56:18'),
(7, 'LUXURY3', 'LUX3', 'luxury3', 11900000.00, 18680000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(8, 'SIGNATURE 1', 'SIG1', 'signature-1', 12600000.00, 22680000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 09:37:10', '2026-05-30 09:39:01'),
(9, 'SIGNATURE 2', 'SIG2', 'signature-2', 17900000.00, 29980000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(10, 'LIMITED1', 'LI1', 'limited1', 21900000.00, 36805000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(11, 'LIMITED 2', 'LI2', 'limited-2', 28900000.00, 39105000.00, NULL, NULL, NULL, 0, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhom_dich_vu_dich_vu_le`
--

CREATE TABLE `nhom_dich_vu_dich_vu_le` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nhom_dich_vu_id` bigint(20) UNSIGNED NOT NULL,
  `dich_vu_le_id` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhom_dich_vu_dich_vu_le`
--

INSERT INTO `nhom_dich_vu_dich_vu_le` (`id`, `nhom_dich_vu_id`, `dich_vu_le_id`, `so_luong`, `created_at`, `updated_at`) VALUES
(67, 1, 27, 1, '2026-05-28 14:49:28', '2026-05-28 14:49:28'),
(75, 1, 55, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(76, 1, 58, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(77, 1, 51, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(78, 1, 50, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(79, 1, 35, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(80, 1, 46, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(81, 1, 7, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(82, 1, 9, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(83, 1, 8, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(84, 1, 10, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(85, 1, 6, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(86, 1, 11, 1, '2026-05-30 07:42:21', '2026-05-30 07:42:21'),
(87, 3, 55, 1, '2026-05-30 07:43:52', '2026-05-30 07:43:52'),
(88, 3, 58, 1, '2026-05-30 07:43:52', '2026-05-30 07:43:52'),
(89, 4, 28, 1, '2026-05-30 07:45:01', '2026-05-30 07:45:01'),
(90, 4, 55, 1, '2026-05-30 07:45:01', '2026-05-30 07:45:01'),
(91, 4, 58, 1, '2026-05-30 07:45:01', '2026-05-30 07:45:01'),
(92, 4, 1, 1, '2026-05-30 07:45:01', '2026-05-30 07:45:01'),
(93, 3, 1, 1, '2026-05-30 07:45:26', '2026-05-30 07:45:26'),
(94, 5, 28, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(95, 5, 55, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(96, 5, 58, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(97, 5, 27, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(98, 5, 43, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(99, 5, 9, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(100, 5, 8, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(101, 5, 10, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(102, 5, 11, 1, '2026-05-30 07:53:15', '2026-05-30 07:53:15'),
(103, 6, 58, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(104, 6, 27, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(105, 6, 43, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(106, 6, 46, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(107, 6, 7, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(108, 6, 6, 1, '2026-05-30 07:54:30', '2026-05-30 07:54:30'),
(109, 7, 31, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(110, 7, 55, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(111, 7, 58, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(112, 7, 27, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(113, 7, 51, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(114, 7, 50, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(115, 7, 43, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(116, 7, 3, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(117, 7, 36, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(118, 7, 46, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(119, 7, 7, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(120, 7, 9, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(121, 7, 8, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(122, 7, 10, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(123, 7, 6, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(124, 7, 11, 1, '2026-05-30 07:55:59', '2026-05-30 07:55:59'),
(125, 6, 51, 1, '2026-05-30 07:56:18', '2026-05-30 07:56:18'),
(126, 6, 50, 1, '2026-05-30 07:56:18', '2026-05-30 07:56:18'),
(127, 5, 51, 1, '2026-05-30 07:56:42', '2026-05-30 07:56:42'),
(128, 5, 50, 1, '2026-05-30 07:56:42', '2026-05-30 07:56:42'),
(129, 8, 31, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(130, 8, 55, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(131, 8, 58, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(132, 8, 27, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(133, 8, 47, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(134, 8, 51, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(135, 8, 50, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(136, 8, 43, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(137, 8, 37, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(138, 8, 46, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(139, 8, 1, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(140, 8, 7, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(141, 8, 9, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(142, 8, 8, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(143, 8, 10, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(144, 8, 6, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(145, 8, 11, 1, '2026-05-30 09:39:01', '2026-05-30 09:39:01'),
(146, 9, 30, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(147, 9, 57, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(148, 9, 27, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(149, 9, 47, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(150, 9, 15, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(151, 9, 16, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(152, 9, 17, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(153, 9, 14, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(154, 9, 19, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(155, 9, 51, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(156, 9, 50, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(157, 9, 43, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(158, 9, 3, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(159, 9, 37, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(160, 9, 33, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(161, 9, 46, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(162, 9, 1, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(163, 9, 34, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(164, 9, 2, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(165, 9, 7, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(166, 9, 9, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(167, 9, 8, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(168, 9, 10, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(169, 9, 6, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(170, 9, 11, 1, '2026-05-30 09:45:34', '2026-05-30 09:45:34'),
(171, 10, 31, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(172, 10, 57, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(173, 10, 27, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(174, 10, 13, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(175, 10, 12, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(176, 10, 47, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(177, 10, 15, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(178, 10, 16, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(179, 10, 17, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(180, 10, 14, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(181, 10, 19, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(182, 10, 51, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(183, 10, 50, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(184, 10, 43, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(185, 10, 3, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(186, 10, 38, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(187, 10, 33, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(188, 10, 46, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(189, 10, 34, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(190, 10, 2, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(191, 10, 4, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(192, 10, 7, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(193, 10, 9, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(194, 10, 8, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(195, 10, 10, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(196, 10, 6, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(197, 10, 11, 1, '2026-05-30 09:49:29', '2026-05-30 09:49:29'),
(198, 11, 30, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(199, 11, 57, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(200, 11, 18, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(201, 11, 27, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(202, 11, 13, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(203, 11, 12, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(204, 11, 47, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(205, 11, 15, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(206, 11, 16, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(207, 11, 17, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(208, 11, 14, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(209, 11, 19, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(210, 11, 51, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(211, 11, 50, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(212, 11, 43, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(213, 11, 3, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(214, 11, 38, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(215, 11, 33, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(216, 11, 46, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(217, 11, 1, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(218, 11, 34, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(219, 11, 2, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(220, 11, 4, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(221, 11, 7, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(222, 11, 9, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(223, 11, 8, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(224, 11, 10, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(225, 11, 6, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53'),
(226, 11, 11, 1, '2026-05-30 09:50:53', '2026-05-30 09:50:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_khach_moi`
--

CREATE TABLE `note_khach_moi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_khach` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `phu_trach_sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_hen_lich` date DEFAULT NULL,
  `ngay_den_thuc_te` date DEFAULT NULL,
  `nguon_khach` varchar(255) DEFAULT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trang_thai` varchar(50) DEFAULT NULL,
  `ly_do_khong_chot` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `note_khach_moi`
--

INSERT INTO `note_khach_moi` (`id`, `ten_khach`, `so_dien_thoai`, `phu_trach_sale_id`, `ngay_hen_lich`, `ngay_den_thuc_te`, `nguon_khach`, `nguoi_tao_id`, `trang_thai`, `ly_do_khong_chot`, `created_at`, `updated_at`) VALUES
(1, '4 - 4', '82000032', 1, NULL, NULL, 'Instagram', 1, 'da_chot', NULL, '2026-05-25 21:00:50', '2026-05-25 21:14:54'),
(2, '4 - 4', '91000032', 1, NULL, NULL, 'Google / tìm kiếm', 1, 'dang_tu_van', NULL, '2026-05-25 21:03:54', '2026-05-25 21:04:06'),
(3, '4 - 4', '82000032', 1, NULL, NULL, 'Khác', 1, 'khong_chot', 'hihi', '2026-05-25 21:09:22', '2026-05-25 21:09:22'),
(4, '4 - 4', '82000032', 1, '2026-04-30', '2026-05-31', 'Khác', 1, 'khong_chot', 'Khách bận', '2026-05-25 21:10:10', '2026-05-25 21:15:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `note_khach_moi_phu_trach_sale`
--

CREATE TABLE `note_khach_moi_phu_trach_sale` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `note_khach_moi_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `note_khach_moi_phu_trach_sale`
--

INSERT INTO `note_khach_moi_phu_trach_sale` (`id`, `note_khach_moi_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-25 21:00:50', '2026-05-25 21:00:50'),
(2, 2, 1, '2026-05-25 21:03:54', '2026-05-25 21:03:54'),
(3, 3, 1, '2026-05-25 21:09:22', '2026-05-25 21:09:22'),
(4, 4, 1, '2026-05-25 21:10:10', '2026-05-25 21:10:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-client', '86bf0c7e671780b3bb26e18df6fb1a90ecf7f6b3f0a854fc734df5c9a97e85ac', '[\"*\"]', NULL, NULL, '2026-05-20 03:41:27', '2026-05-20 03:41:27'),
(2, 'App\\Models\\User', 1, 'api-client', '422f38de168cee93d4220570e033960dbfdb93e1f02bdfd0d9891b8af8b461d8', '[\"*\"]', '2026-05-21 04:28:45', NULL, '2026-05-20 03:48:03', '2026-05-21 04:28:45'),
(3, 'App\\Models\\User', 1, 'api-client', '8794ca9b757e10c77f46f429280e0d475bdf4a4b7aa8cd730e4ccdb586dc4bb2', '[\"*\"]', NULL, NULL, '2026-05-20 04:12:19', '2026-05-20 04:12:19'),
(4, 'App\\Models\\User', 1, 'api-client', 'ab152ad37e90457ddc5d3bb3edd7da4ce8c8feebe6876340549b9f3df93f8b23', '[\"*\"]', NULL, NULL, '2026-05-20 04:12:33', '2026-05-20 04:12:33'),
(5, 'App\\Models\\User', 1, 'api-docs', '836ab5be29d75f64e6412bf575b39f80ed689f29082d5bfc421abf2418192d8f', '[\"*\"]', '2026-05-20 06:47:25', NULL, '2026-05-20 05:00:22', '2026-05-20 06:47:25'),
(6, 'App\\Models\\User', 1, 'api-client', '74e606a7da96f52a9f685bfcb7f2a99a435957e031c52096424b0fc86ef0a52f', '[\"*\"]', NULL, NULL, '2026-05-20 14:40:47', '2026-05-20 14:40:47'),
(7, 'App\\Models\\User', 1, 'api-client', '99697a8ad77624619b7a36eefbfa8079645336957e1178b053a0cffd5f5d494b', '[\"*\"]', NULL, NULL, '2026-05-20 14:41:07', '2026-05-20 14:41:07'),
(8, 'App\\Models\\User', 1, 'api-client', '7a3b54ec0061b34692d54f026c4ea06d8b9b51125b8a90523bb0db1674312fa9', '[\"*\"]', NULL, NULL, '2026-05-20 14:43:26', '2026-05-20 14:43:26'),
(9, 'App\\Models\\User', 1, 'api-client', '3cb9ce1a74843daab9580dac0363e235c1ceea0a3aafe679122cbcbd77a7e128', '[\"*\"]', NULL, NULL, '2026-05-20 14:53:50', '2026-05-20 14:53:50'),
(10, 'App\\Models\\User', 1, 'api-client', '24a76344e79578727c0f10975cecffe88dbe9c1a3f7a248d35ff74cdb0d93ef9', '[\"*\"]', NULL, NULL, '2026-05-20 14:55:36', '2026-05-20 14:55:36'),
(11, 'App\\Models\\User', 1, 'api-client', '1be357c1fe47089e8dc1689a32bbf502da34efbd07f293bb25ffbb43e23782f8', '[\"*\"]', NULL, NULL, '2026-05-20 15:05:49', '2026-05-20 15:05:49'),
(12, 'App\\Models\\User', 1, 'api-client', 'e5ed8a343e0b988e3cb44eb247b1f6be677521ca4b812dea603e1b5aa3579f96', '[\"*\"]', NULL, NULL, '2026-05-20 15:08:46', '2026-05-20 15:08:46'),
(13, 'App\\Models\\User', 1, 'api-client', 'f4e647e76ddbd918a650909e1670345f2acaad1148292910e621418e5fd257f7', '[\"*\"]', NULL, NULL, '2026-05-20 15:11:32', '2026-05-20 15:11:32'),
(14, 'App\\Models\\User', 1, 'api-client', 'cb21fa6b28206a1718409abb4654dc0a2fbe96c0fb3daab886d216c8693c7c63', '[\"*\"]', NULL, NULL, '2026-05-20 15:22:28', '2026-05-20 15:22:28'),
(15, 'App\\Models\\User', 1, 'api-client', 'fc27f430f4cadd95afbbdac40368e8adb4c834dea1a56cc04f11cedb6f270d75', '[\"*\"]', NULL, NULL, '2026-05-21 01:27:52', '2026-05-21 01:27:52'),
(16, 'App\\Models\\User', 1, 'api-client', 'd08ac70719354d6daf0949a6d1f2c7f3803d9686843588be7d417203b03c82f4', '[\"*\"]', NULL, NULL, '2026-05-21 01:30:11', '2026-05-21 01:30:11'),
(17, 'App\\Models\\User', 1, 'api-client', 'f1c10305ae50ced6a5513f2ca7e4b5faf51e724e4505b3296ddd6691409165b6', '[\"*\"]', NULL, NULL, '2026-05-21 01:30:35', '2026-05-21 01:30:35'),
(18, 'App\\Models\\User', 1, 'api-client', 'f24fb32ae06d26affb7dfcbebef8fda28ab62f1e140c4f69d1043d4c0a338ae1', '[\"*\"]', NULL, NULL, '2026-05-21 01:31:02', '2026-05-21 01:31:02'),
(19, 'App\\Models\\User', 1, 'api-client', '9db26bcdd91f0064598ff163a5362a9b3a50b4e0d5897bd7fc5e69dfabc8761c', '[\"*\"]', NULL, NULL, '2026-05-21 01:37:50', '2026-05-21 01:37:50'),
(20, 'App\\Models\\User', 1, 'api-client', '89dab0761acb864bff3a5d3f939e6c2d57c47072dfa35532c62e4bea09a3e6dc', '[\"*\"]', NULL, NULL, '2026-05-21 01:39:57', '2026-05-21 01:39:57'),
(21, 'App\\Models\\User', 1, 'api-client', '66efcd2ddb41453447d079ae49a1e41443f3e6504ad5893f390d213d173e7504', '[\"*\"]', NULL, NULL, '2026-05-21 01:50:10', '2026-05-21 01:50:10'),
(22, 'App\\Models\\User', 1, 'api-client', 'b33ae45744de2290d4488805f59ea5a17fa809f4cf6a7ca2980b76738f5b0118', '[\"*\"]', NULL, NULL, '2026-05-21 01:55:17', '2026-05-21 01:55:17'),
(23, 'App\\Models\\User', 1, 'api-client', '442899b5eafa8f4516b2f5a137d6ab7296b67bd75237b36bac0233324f9bb993', '[\"*\"]', NULL, NULL, '2026-05-21 01:58:21', '2026-05-21 01:58:21'),
(24, 'App\\Models\\User', 1, 'api-client', 'd4f651e80c41f9757a96f6017a12df88145c67ea5647d36c175a8554967f6d4d', '[\"*\"]', NULL, NULL, '2026-05-21 02:24:11', '2026-05-21 02:24:11'),
(25, 'App\\Models\\User', 1, 'api-client', 'dbf3e6e4485e24f50d26bb3e883179d8526e5281ec1c71cb5101489f047e930f', '[\"*\"]', NULL, NULL, '2026-05-21 06:48:55', '2026-05-21 06:48:55'),
(26, 'App\\Models\\User', 1, 'api-client', 'df964d7d0e69461809ee3b7c3f14a414e217638d69afb81a4ea340cf759919ce', '[\"*\"]', NULL, NULL, '2026-05-25 12:37:16', '2026-05-25 12:37:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_thu_chi`
--

CREATE TABLE `phieu_thu_chi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_duyet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_phieu` tinyint(4) NOT NULL,
  `so_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ly_do` varchar(255) NOT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0,
  `ghi_chu` text DEFAULT NULL,
  `ngay_duyet` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong_ban`
--

CREATE TABLE `phong_ban` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_phong_ban` varchar(255) NOT NULL,
  `ma_phong_ban` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phong_ban`
--

INSERT INTO `phong_ban` (`id`, `ten_phong_ban`, `ma_phong_ban`, `mo_ta`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(1, 'Lễ tân', 'LTS01', NULL, NULL, NULL, NULL),
(2, 'Chụp', 'CS01', NULL, NULL, NULL, NULL),
(3, 'Makeup', 'MS01', NULL, NULL, NULL, NULL),
(4, 'Photoshop', 'PS01', NULL, NULL, NULL, NULL),
(5, 'Marketing', 'MKS01', NULL, NULL, NULL, NULL),
(6, 'Trang phục', 'TPS01', NULL, NULL, NULL, NULL),
(7, 'R&D', 'RDS01', NULL, NULL, NULL, NULL),
(8, 'Kế toán', 'KTS01', NULL, NULL, NULL, NULL),
(9, 'Điều phối', 'DPS01', NULL, NULL, NULL, NULL),
(10, 'Chăm sóc khách hàng', 'CSKS01', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham_cho_thue`
--

CREATE TABLE `san_pham_cho_thue` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hop_dong_id` bigint(20) UNSIGNED NOT NULL,
  `san_pham_id` bigint(20) UNSIGNED NOT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tai_lieu`
--

CREATE TABLE `tai_lieu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_tai_lieu` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `duong_dan` varchar(500) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tai_lieu`
--

INSERT INTO `tai_lieu` (`id`, `ten_tai_lieu`, `mo_ta`, `file`, `duong_dan`, `created_at`, `updated_at`) VALUES
(1, 'Tên', 'Mô tả', 'Sora_Bridal_UGC_Policy.html', 'taiLieu/da3tt2FqP6ywKE25InGtBO0W8iKO5y2RpJF03fCQ.html', '2026-05-14 01:12:06', '2026-05-14 01:12:06'),
(3, 'a', NULL, 'pandio (1).html', 'taiLieu/omrFY8emGXsQrc4f5UOigpsdrwls4lTh5S3dP1Hq.html', '2026-05-28 01:37:56', '2026-05-28 01:37:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trang_phuc`
--

CREATE TABLE `trang_phuc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `ma_san_pham` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` varchar(50) NOT NULL DEFAULT 'active',
  `gia_tri` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `trang_phuc`
--

INSERT INTO `trang_phuc` (`id`, `ten_san_pham`, `ma_san_pham`, `slug`, `hinh_anh`, `mo_ta`, `ghi_chu`, `trang_thai`, `gia_tri`, `created_at`, `updated_at`) VALUES
(5, 'Trang phục cưới mẫu 1', 'DEMO-TP-001', 'trang-phuc-cuoi-mau-1-demo-1', NULL, 'Mô tả trang phục demo 1.', NULL, '1', 550000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(6, 'Trang phục cưới mẫu 2', 'DEMO-TP-002', 'trang-phuc-cuoi-mau-2-demo-2', NULL, 'Mô tả trang phục demo 2.', NULL, '1', 600000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(7, 'Trang phục cưới mẫu 3', 'DEMO-TP-003', 'trang-phuc-cuoi-mau-3-demo-3', NULL, 'Mô tả trang phục demo 3.', NULL, '1', 650000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(8, 'Trang phục cưới mẫu 4', 'DEMO-TP-004', 'trang-phuc-cuoi-mau-4-demo-4', NULL, 'Mô tả trang phục demo 4.', NULL, '1', 700000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(9, 'Trang phục cưới mẫu 5', 'DEMO-TP-005', 'trang-phuc-cuoi-mau-5-demo-5', NULL, 'Mô tả trang phục demo 5.', NULL, '1', 750000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(10, 'Trang phục cưới mẫu 6', 'DEMO-TP-006', 'trang-phuc-cuoi-mau-6-demo-6', NULL, 'Mô tả trang phục demo 6.', NULL, '1', 800000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(11, 'Trang phục cưới mẫu 7', 'DEMO-TP-007', 'trang-phuc-cuoi-mau-7-demo-7', NULL, 'Mô tả trang phục demo 7.', NULL, '1', 850000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(12, 'Trang phục cưới mẫu 8', 'DEMO-TP-008', 'trang-phuc-cuoi-mau-8-demo-8', NULL, 'Mô tả trang phục demo 8.', NULL, '1', 900000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(13, 'Trang phục cưới mẫu 9', 'DEMO-TP-009', 'trang-phuc-cuoi-mau-9-demo-9', NULL, 'Mô tả trang phục demo 9.', NULL, '1', 950000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(14, 'Trang phục cưới mẫu 10', 'DEMO-TP-010', 'trang-phuc-cuoi-mau-10-demo-10', NULL, 'Mô tả trang phục demo 10.', NULL, '1', 1000000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(15, 'Trang phục cưới mẫu 11', 'DEMO-TP-011', 'trang-phuc-cuoi-mau-11-demo-11', NULL, 'Mô tả trang phục demo 11.', NULL, '1', 1050000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(16, 'Trang phục cưới mẫu 12', 'DEMO-TP-012', 'trang-phuc-cuoi-mau-12-demo-12', NULL, 'Mô tả trang phục demo 12.', NULL, '1', 1100000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(17, 'Trang phục cưới mẫu 13', 'DEMO-TP-013', 'trang-phuc-cuoi-mau-13-demo-13', NULL, 'Mô tả trang phục demo 13.', NULL, '1', 1150000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(18, 'Trang phục cưới mẫu 14', 'DEMO-TP-014', 'trang-phuc-cuoi-mau-14-demo-14', NULL, 'Mô tả trang phục demo 14.', NULL, '1', 1200000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(19, 'Trang phục cưới mẫu 15', 'DEMO-TP-015', 'trang-phuc-cuoi-mau-15-demo-15', NULL, 'Mô tả trang phục demo 15.', NULL, '1', 1250000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(20, 'Trang phục cưới mẫu 16', 'DEMO-TP-016', 'trang-phuc-cuoi-mau-16-demo-16', NULL, 'Mô tả trang phục demo 16.', NULL, '1', 1300000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(21, 'Trang phục cưới mẫu 17', 'DEMO-TP-017', 'trang-phuc-cuoi-mau-17-demo-17', NULL, 'Mô tả trang phục demo 17.', NULL, '1', 1350000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(22, 'Trang phục cưới mẫu 18', 'DEMO-TP-018', 'trang-phuc-cuoi-mau-18-demo-18', NULL, 'Mô tả trang phục demo 18.', NULL, '1', 1400000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(23, 'Trang phục cưới mẫu 19', 'DEMO-TP-019', 'trang-phuc-cuoi-mau-19-demo-19', NULL, 'Mô tả trang phục demo 19.', NULL, '1', 1450000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(24, 'Trang phục cưới mẫu 20', 'DEMO-TP-020', 'trang-phuc-cuoi-mau-20-demo-20', NULL, 'Mô tả trang phục demo 20.', NULL, '1', 1500000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(25, 'Trang phục cưới mẫu 21', 'DEMO-TP-021', 'trang-phuc-cuoi-mau-21-demo-21', NULL, 'Mô tả trang phục demo 21.', NULL, '1', 1550000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(26, 'Trang phục cưới mẫu 22', 'DEMO-TP-022', 'trang-phuc-cuoi-mau-22-demo-22', NULL, 'Mô tả trang phục demo 22.', NULL, '1', 1600000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(27, 'Trang phục cưới mẫu 23', 'DEMO-TP-023', 'trang-phuc-cuoi-mau-23-demo-23', NULL, 'Mô tả trang phục demo 23.', NULL, '1', 1650000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(28, 'Trang phục cưới mẫu 24', 'DEMO-TP-024', 'trang-phuc-cuoi-mau-24-demo-24', NULL, 'Mô tả trang phục demo 24.', NULL, '1', 1700000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(29, 'Trang phục cưới mẫu 25', 'DEMO-TP-025', 'trang-phuc-cuoi-mau-25-demo-25', NULL, 'Mô tả trang phục demo 25.', NULL, '1', 1750000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(30, 'Trang phục cưới mẫu 26', 'DEMO-TP-026', 'trang-phuc-cuoi-mau-26-demo-26', NULL, 'Mô tả trang phục demo 26.', NULL, '1', 1800000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(31, 'Trang phục cưới mẫu 27', 'DEMO-TP-027', 'trang-phuc-cuoi-mau-27-demo-27', NULL, 'Mô tả trang phục demo 27.', NULL, '1', 1850000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(32, 'Trang phục cưới mẫu 28', 'DEMO-TP-028', 'trang-phuc-cuoi-mau-28-demo-28', NULL, 'Mô tả trang phục demo 28.', NULL, '1', 1900000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(33, 'Trang phục cưới mẫu 29', 'DEMO-TP-029', 'trang-phuc-cuoi-mau-29-demo-29', NULL, 'Mô tả trang phục demo 29.', NULL, '1', 1950000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(34, 'Trang phục cưới mẫu 30', 'DEMO-TP-030', 'trang-phuc-cuoi-mau-30-demo-30', NULL, 'Mô tả trang phục demo 30.', NULL, '1', 2000000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(35, 'Trang phục cưới mẫu 31', 'DEMO-TP-031', 'trang-phuc-cuoi-mau-31-demo-31', NULL, 'Mô tả trang phục demo 31.', NULL, '1', 2050000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(36, 'Trang phục cưới mẫu 32', 'DEMO-TP-032', 'trang-phuc-cuoi-mau-32-demo-32', NULL, 'Mô tả trang phục demo 32.', NULL, '1', 2100000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(37, 'Trang phục cưới mẫu 33', 'DEMO-TP-033', 'trang-phuc-cuoi-mau-33-demo-33', NULL, 'Mô tả trang phục demo 33.', NULL, '1', 2150000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(38, 'Trang phục cưới mẫu 34', 'DEMO-TP-034', 'trang-phuc-cuoi-mau-34-demo-34', NULL, 'Mô tả trang phục demo 34.', NULL, '1', 2200000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(39, 'Trang phục cưới mẫu 35', 'DEMO-TP-035', 'trang-phuc-cuoi-mau-35-demo-35', NULL, 'Mô tả trang phục demo 35.', NULL, '1', 2250000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(40, 'Trang phục cưới mẫu 36', 'DEMO-TP-036', 'trang-phuc-cuoi-mau-36-demo-36', NULL, 'Mô tả trang phục demo 36.', NULL, '1', 2300000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(41, 'Trang phục cưới mẫu 37', 'DEMO-TP-037', 'trang-phuc-cuoi-mau-37-demo-37', NULL, 'Mô tả trang phục demo 37.', NULL, '1', 2350000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(42, 'Trang phục cưới mẫu 38', 'DEMO-TP-038', 'trang-phuc-cuoi-mau-38-demo-38', NULL, 'Mô tả trang phục demo 38.', NULL, '1', 2400000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(43, 'Trang phục cưới mẫu 39', 'DEMO-TP-039', 'trang-phuc-cuoi-mau-39-demo-39', NULL, 'Mô tả trang phục demo 39.', NULL, '1', 2450000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(44, 'Trang phục cưới mẫu 40', 'DEMO-TP-040', 'trang-phuc-cuoi-mau-40-demo-40', NULL, 'Mô tả trang phục demo 40.', NULL, '1', 2500000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(45, 'Trang phục cưới mẫu 41', 'DEMO-TP-041', 'trang-phuc-cuoi-mau-41-demo-41', NULL, 'Mô tả trang phục demo 41.', NULL, '1', 2550000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(46, 'Trang phục cưới mẫu 42', 'DEMO-TP-042', 'trang-phuc-cuoi-mau-42-demo-42', NULL, 'Mô tả trang phục demo 42.', NULL, '1', 2600000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(47, 'Trang phục cưới mẫu 43', 'DEMO-TP-043', 'trang-phuc-cuoi-mau-43-demo-43', NULL, 'Mô tả trang phục demo 43.', NULL, '1', 2650000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(48, 'Trang phục cưới mẫu 44', 'DEMO-TP-044', 'trang-phuc-cuoi-mau-44-demo-44', NULL, 'Mô tả trang phục demo 44.', NULL, '1', 2700000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(49, 'Trang phục cưới mẫu 45', 'DEMO-TP-045', 'trang-phuc-cuoi-mau-45-demo-45', NULL, 'Mô tả trang phục demo 45.', NULL, '1', 2750000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(50, 'Trang phục cưới mẫu 46', 'DEMO-TP-046', 'trang-phuc-cuoi-mau-46-demo-46', NULL, 'Mô tả trang phục demo 46.', NULL, '1', 2800000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(51, 'Trang phục cưới mẫu 47', 'DEMO-TP-047', 'trang-phuc-cuoi-mau-47-demo-47', NULL, 'Mô tả trang phục demo 47.', NULL, '1', 2850000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(52, 'Trang phục cưới mẫu 48', 'DEMO-TP-048', 'trang-phuc-cuoi-mau-48-demo-48', NULL, 'Mô tả trang phục demo 48.', NULL, '1', 2900000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(53, 'Trang phục cưới mẫu 49', 'DEMO-TP-049', 'trang-phuc-cuoi-mau-49-demo-49', NULL, 'Mô tả trang phục demo 49.', NULL, '1', 2950000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04'),
(54, 'Trang phục cưới mẫu 50', 'DEMO-TP-050', 'trang-phuc-cuoi-mau-50-demo-50', NULL, 'Mô tả trang phục demo 50.', NULL, '1', 3000000.00, '2026-05-04 20:05:04', '2026-05-04 20:05:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Phùng Xuân Ngọc', 'Xuanngoc.dev@gmail.com', '988505055', NULL, '$2y$12$tlfymxAv1o.GxswMaiSNiuC.9jJ3lgvxuUiNb/RFVgykaMDrJ/Ss.', 1, 0, NULL, '2026-03-04 12:18:04', '2026-03-24 21:36:54'),
(2, 'Nguyễn Thị Thuỳ Linh', NULL, '352391708', NULL, '$2y$12$pTqOkfWx8EUQPDtxUJ/BiuSouy9NIyI7V.0kvH0FsnoUMiTIdNTlG', 3, 1, NULL, '2026-05-27 01:47:02', '2026-05-27 01:47:02'),
(3, 'Đỗ Thị Thanh Thuý', 'dothithanhthuy3737@gmail.com', '702021843', NULL, '$2y$12$lrRZWUCOrjcI3ebNJ4.VZOl9pstB/N5/Am0ptwTX6sEexvt01eBYO', 3, 1, NULL, '2026-05-27 01:47:02', '2026-05-27 01:47:02'),
(4, 'Lưu Ngọc Anh', 'anha0926@gmail.com', '383361304', NULL, '$2y$12$sCYabjAshqwYOL98iczek.E.QKxnTKcOoZUeBb6.KO9jXOzpcumm.', 3, 1, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(5, 'Lê Thị Hoài Linh', NULL, '382085196', NULL, '$2y$12$Z.OZ8SeMRFci1mmmgrC.iO4hirdbcOoNp8DQORVlTdscWxeMOC7j2', 3, 2, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(6, 'Lê Thu Hiền', NULL, NULL, NULL, '$2y$12$0g3vm8lX.G6AUCANG42l0OUkqb5XMiydlxf4dfDYyevIdCVL115V6', 2, 1, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(7, 'Phùng Nhật Phương', NULL, NULL, NULL, '$2y$12$1cM3me5M7Fs3ygLccw7FhuUeypQDGgIKfuIhcmh0z8WlkjeA9mNIG', 2, 1, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(8, 'Nguyễn Thị Diệu Trang', NULL, NULL, NULL, '$2y$12$XzAhSNLbCdC1rSm3YhTxTerGjF8NQ34LU6p8pSWdOtKLY4UyOcY66', 3, 1, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(9, 'Trương Ngọc Ánh', NULL, NULL, NULL, '$2y$12$FABvk8eNnNmeQIDIFmTVGeGpomlDrRwXqgwdSIsog1weZiV9orWna', 3, 1, NULL, '2026-05-27 01:47:03', '2026-05-27 01:47:03'),
(10, 'Vũ Thị Ngọc', NULL, NULL, NULL, '$2y$12$ivTvcoPjlRomGnebp3MUYe6DwhoqbvsWG6xN2HYX.eJFvehJrXckG', 3, 2, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(11, 'Hoàng Tiến Anh', 'hoangtienanh25101998@gmail.com', '355608800', NULL, '$2y$12$jQaLIeGc4wnsLTurDgZNqeNgPhEsdvXp4bMe8XpuTcbphPsAJ2s2i', 3, 1, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(12, 'Trần Văn Bình', NULL, NULL, NULL, '$2y$12$7HVX6fW48QkKc72dkCMtReOHQpFDTUSvnuXmNg4ng2zmb7z/jVKnK', 3, 1, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(13, 'Trần Minh Hiếu', NULL, NULL, NULL, '$2y$12$q7xLEXYpkdVaV8AChAjq9.xQcmGzMeMMVGYTNVajeFYjJBF/JA5Vm', 3, 1, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(14, 'Lê Quốc Anh', NULL, NULL, NULL, '$2y$12$LRSXeObZxvmy2ZZUEF13OesQIk7//TKiTXjQGD6hCXRe7HXvN6cIK', 3, 1, NULL, '2026-05-27 01:47:04', '2026-05-27 01:47:04'),
(15, 'Trần Trọng Đại', NULL, NULL, NULL, '$2y$12$rDh8HQJjWOqxZZaN47JvAeOfEoUkGSSaaqNWJr1EvgKHz8FI.QUqG', 3, 1, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(16, 'Nguyễn Đức Hải', 'duchai14042003@gmail.com', '395942258', NULL, '$2y$12$pmYwphbxD05sIV/0wfErUOW3dsGrQ/s9f04FmNhe7MUfT8GZN44iC', 2, 1, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(17, 'Nguyễn Đình Hưng', 'hunghunh65@gmail.com', '988982633', NULL, '$2y$12$YnYw81oOWT3FHdjzxN/P3.Lco7JpHKXoA/2AWgJ28hnm13oHEV5jC', 2, 1, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(18, 'Bùi Thị Linh', 'buithilinh5503@gmail.com', '979632378', NULL, '$2y$12$cJBrtRGttVHVGtKzMGkoqOWt2e58zTO5MbuH0osfY8WTQ2gMXkuAa', 3, 1, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(19, 'Quỳnh', NULL, NULL, NULL, '$2y$12$7K1FZIzxiDpykmafSmRDTOYEEHb33E7GeKsYxfh3nssk3/YmSblH.', 3, 1, NULL, '2026-05-27 01:47:05', '2026-05-27 01:47:05'),
(20, 'Đỗ Ngọc Diệp', 'diepdo2604@gmail.com', '869689101', NULL, '$2y$12$1oqO4gGkQGC66UBys4fBYuxZg16fx2wXPfD..GqDA9ClAfLheEPDS', 3, 1, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(21, 'Nguyễn Thị Kim Ngân', 'kimnganhd1060@gmail.com', '911667510', NULL, '$2y$12$P/qwDDNC6ceo8WC1SpwXNuOzFtZUJ1YskO4AFoofEb0H5FkgEN5Xi', 3, 1, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(22, 'Đặng Thị Lan', 'dangthilan0797@gmail.com', '967877250', NULL, '$2y$12$ogFju.x6jdA17L2IOJjzwOYB3b3T5fGd0AR6xjLiVdv2iXHvtAyE.', 3, 1, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(23, 'Nguyễn Diệu Linh', NULL, NULL, NULL, '$2y$12$cxBNcB7WZ0tGlofIIgd.jOWuuxhEo/VclTyz0ZIGbczktz2jUXOEK', 3, 1, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(24, 'Đinh Trọng Tín', 'trongtin030199@gmail.com', '966884801', NULL, '$2y$12$jwbbpFrTj0RbmaxADt7KZOOzo9qyNZa9MBot.bbFtGfqUR9u8BveO', 2, 1, NULL, '2026-05-27 01:47:06', '2026-05-27 01:47:06'),
(25, 'Admin Sora', 'cskh@sorabridal.com', NULL, NULL, '$2y$12$PKzpknYpvZ0gJqYf7c.wUOufFNLI.d76wPTguwXEgPfimNFRUqPsS', 1, 1, NULL, '2026-05-27 01:47:07', '2026-05-27 01:47:07');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bao_cao_ads`
--
ALTER TABLE `bao_cao_ads`
  ADD PRIMARY KEY (`id`);

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
-- Chỉ mục cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cham_cong_user_id_foreign` (`user_id`),
  ADD KEY `cham_cong_diem_danh_id_foreign` (`diem_danh_id`);

--
-- Chỉ mục cho bảng `concept`
--
ALTER TABLE `concept`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `dang_ky_tu_van`
--
ALTER TABLE `dang_ky_tu_van`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `dich_vu_le`
--
ALTER TABLE `dich_vu_le`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dich_vu_le_ma_dich_vu_unique` (`ma_dich_vu`),
  ADD UNIQUE KEY `dich_vu_le_slug_unique` (`slug`),
  ADD KEY `dich_vu_le_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `dich_vu_trong_hop_dong`
--
ALTER TABLE `dich_vu_trong_hop_dong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dich_vu_trong_hop_dong_id_hop_dong_id_dich_vu_unique` (`id_hop_dong`,`id_dich_vu`),
  ADD KEY `dich_vu_trong_hop_dong_id_dich_vu_foreign` (`id_dich_vu`);

--
-- Chỉ mục cho bảng `diem_danh`
--
ALTER TABLE `diem_danh`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diem_danh_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hop_dong_ma_hop_dong_unique` (`ma_hop_dong`),
  ADD KEY `hop_dong_nguoi_tao_id_foreign` (`nguoi_tao_id`),
  ADD KEY `hop_dong_tho_chup_id_foreign` (`tho_chup_id`),
  ADD KEY `hop_dong_tho_make_id_foreign` (`tho_make_id`),
  ADD KEY `hop_dong_tho_edit_id_foreign` (`tho_edit_id`);

--
-- Chỉ mục cho bảng `hop_dong_cho_thue_trang_phuc`
--
ALTER TABLE `hop_dong_cho_thue_trang_phuc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hop_dong_cho_thue_trang_phuc_nguoi_cho_thue_foreign` (`nguoi_cho_thue`);

--
-- Chỉ mục cho bảng `hop_dong_cuoi`
--
ALTER TABLE `hop_dong_cuoi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hop_dong_cuoi_ma_hop_dong_unique` (`ma_hop_dong`),
  ADD KEY `hop_dong_cuoi_concept_id_foreign` (`concept_id`),
  ADD KEY `hop_dong_cuoi_nguoi_up_link_demo_id_foreign` (`nguoi_up_link_demo_id`),
  ADD KEY `hop_dong_cuoi_nguoi_up_link_in_id_foreign` (`nguoi_up_link_in_id`),
  ADD KEY `hop_dong_cuoi_tho_chup_id_foreign` (`tho_chup_id`),
  ADD KEY `hop_dong_cuoi_tho_make_id_foreign` (`tho_make_id`),
  ADD KEY `hop_dong_cuoi_tho_edit_id_foreign` (`tho_edit_id`),
  ADD KEY `hop_dong_cuoi_created_by_foreign` (`created_by`);

--
-- Chỉ mục cho bảng `hop_dong_cuoi_dich_vu_le`
--
ALTER TABLE `hop_dong_cuoi_dich_vu_le`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_hdc_dvlc_pair` (`hop_dong_cuoi_id`,`dich_vu_le_id`),
  ADD KEY `hop_dong_cuoi_dich_vu_le_dich_vu_le_id_foreign` (`dich_vu_le_id`);

--
-- Chỉ mục cho bảng `hop_dong_cuoi_nhom_dich_vu`
--
ALTER TABLE `hop_dong_cuoi_nhom_dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ndvlc_hdc_ndv_dvl` (`hop_dong_cuoi_id`,`nhom_dich_vu_id`,`dich_vu_le_id`),
  ADD KEY `hop_dong_cuoi_nhom_dich_vu_dich_vu_le_id_foreign` (`dich_vu_le_id`),
  ADD KEY `hop_dong_cuoi_nhom_dich_vu_nhom_dich_vu_id_foreign` (`nhom_dich_vu_id`);

--
-- Chỉ mục cho bảng `hop_dong_cuoi_thanh_vien_sale`
--
ALTER TABLE `hop_dong_cuoi_thanh_vien_sale`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_hdc_tv_sale_hop_nv_role` (`hop_dong_id`,`nhan_vien_id`,`vai_tro`),
  ADD KEY `hop_dong_cuoi_thanh_vien_sale_nhan_vien_id_foreign` (`nhan_vien_id`);

--
-- Chỉ mục cho bảng `hop_dong_cuoi_trang_phuc`
--
ALTER TABLE `hop_dong_cuoi_trang_phuc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_hdc_tp_pair` (`hop_dong_cuoi_id`,`trang_phuc_id`),
  ADD KEY `hop_dong_cuoi_trang_phuc_trang_phuc_id_foreign` (`trang_phuc_id`);

--
-- Chỉ mục cho bảng `hop_dong_dich_vu_le`
--
ALTER TABLE `hop_dong_dich_vu_le`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hop_dong_dich_vu_le_hop_dong_id_dich_vu_le_id_unique` (`hop_dong_id`,`dich_vu_le_id`),
  ADD KEY `hop_dong_dich_vu_le_dich_vu_le_id_foreign` (`dich_vu_le_id`);

--
-- Chỉ mục cho bảng `hop_dong_thanh_toan`
--
ALTER TABLE `hop_dong_thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hop_dong_thanh_toan_hop_dong_id_foreign` (`hop_dong_id`),
  ADD KEY `hop_dong_thanh_toan_created_by_foreign` (`created_by`);

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
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ngan_hang_thanh_toan`
--
ALTER TABLE `ngan_hang_thanh_toan`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nhan_vien_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `nhan_vien_phong_ban`
--
ALTER TABLE `nhan_vien_phong_ban`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nhan_vien_phong_ban_nhan_vien_id_phong_ban_id_unique` (`nhan_vien_id`,`phong_ban_id`),
  ADD KEY `nhan_vien_phong_ban_phong_ban_id_foreign` (`phong_ban_id`);

--
-- Chỉ mục cho bảng `nhom_dich_vu`
--
ALTER TABLE `nhom_dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nhom_dich_vu_ma_nhom_unique` (`ma_nhom`),
  ADD UNIQUE KEY `nhom_dich_vu_slug_unique` (`slug`),
  ADD KEY `nhom_dich_vu_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `nhom_dich_vu_dich_vu_le`
--
ALTER TABLE `nhom_dich_vu_dich_vu_le`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ndv_dvl_pair` (`nhom_dich_vu_id`,`dich_vu_le_id`),
  ADD KEY `nhom_dich_vu_dich_vu_le_dich_vu_le_id_foreign` (`dich_vu_le_id`);

--
-- Chỉ mục cho bảng `note_khach_moi`
--
ALTER TABLE `note_khach_moi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_khach_moi_phu_trach_sale_id_foreign` (`phu_trach_sale_id`),
  ADD KEY `note_khach_moi_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `note_khach_moi_phu_trach_sale`
--
ALTER TABLE `note_khach_moi_phu_trach_sale`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `note_khach_moi_phu_trach_sale_note_khach_moi_id_user_id_unique` (`note_khach_moi_id`,`user_id`),
  ADD KEY `note_khach_moi_phu_trach_sale_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Chỉ mục cho bảng `phieu_thu_chi`
--
ALTER TABLE `phieu_thu_chi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phieu_thu_chi_nguoi_tao_id_foreign` (`nguoi_tao_id`),
  ADD KEY `phieu_thu_chi_nguoi_duyet_id_foreign` (`nguoi_duyet_id`);

--
-- Chỉ mục cho bảng `phong_ban`
--
ALTER TABLE `phong_ban`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phong_ban_ma_phong_ban_unique` (`ma_phong_ban`);

--
-- Chỉ mục cho bảng `san_pham_cho_thue`
--
ALTER TABLE `san_pham_cho_thue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_cho_thue_hop_dong_id_foreign` (`hop_dong_id`),
  ADD KEY `san_pham_cho_thue_san_pham_id_foreign` (`san_pham_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `tai_lieu`
--
ALTER TABLE `tai_lieu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `trang_phuc`
--
ALTER TABLE `trang_phuc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trang_phuc_ma_san_pham_unique` (`ma_san_pham`),
  ADD UNIQUE KEY `trang_phuc_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bao_cao_ads`
--
ALTER TABLE `bao_cao_ads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `concept`
--
ALTER TABLE `concept`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dang_ky_tu_van`
--
ALTER TABLE `dang_ky_tu_van`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `dich_vu_le`
--
ALTER TABLE `dich_vu_le`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT cho bảng `dich_vu_trong_hop_dong`
--
ALTER TABLE `dich_vu_trong_hop_dong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `diem_danh`
--
ALTER TABLE `diem_danh`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cho_thue_trang_phuc`
--
ALTER TABLE `hop_dong_cho_thue_trang_phuc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cuoi`
--
ALTER TABLE `hop_dong_cuoi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cuoi_dich_vu_le`
--
ALTER TABLE `hop_dong_cuoi_dich_vu_le`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=494;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cuoi_nhom_dich_vu`
--
ALTER TABLE `hop_dong_cuoi_nhom_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=314;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cuoi_thanh_vien_sale`
--
ALTER TABLE `hop_dong_cuoi_thanh_vien_sale`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;

--
-- AUTO_INCREMENT cho bảng `hop_dong_cuoi_trang_phuc`
--
ALTER TABLE `hop_dong_cuoi_trang_phuc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong_dich_vu_le`
--
ALTER TABLE `hop_dong_dich_vu_le`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong_thanh_toan`
--
ALTER TABLE `hop_dong_thanh_toan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `ngan_hang_thanh_toan`
--
ALTER TABLE `ngan_hang_thanh_toan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `nhan_vien_phong_ban`
--
ALTER TABLE `nhan_vien_phong_ban`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT cho bảng `nhom_dich_vu`
--
ALTER TABLE `nhom_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `nhom_dich_vu_dich_vu_le`
--
ALTER TABLE `nhom_dich_vu_dich_vu_le`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

--
-- AUTO_INCREMENT cho bảng `note_khach_moi`
--
ALTER TABLE `note_khach_moi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `note_khach_moi_phu_trach_sale`
--
ALTER TABLE `note_khach_moi_phu_trach_sale`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `phieu_thu_chi`
--
ALTER TABLE `phieu_thu_chi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phong_ban`
--
ALTER TABLE `phong_ban`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `san_pham_cho_thue`
--
ALTER TABLE `san_pham_cho_thue`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tai_lieu`
--
ALTER TABLE `tai_lieu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `trang_phuc`
--
ALTER TABLE `trang_phuc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD CONSTRAINT `cham_cong_diem_danh_id_foreign` FOREIGN KEY (`diem_danh_id`) REFERENCES `diem_danh` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cham_cong_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `dich_vu_le`
--
ALTER TABLE `dich_vu_le`
  ADD CONSTRAINT `dich_vu_le_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `dich_vu_trong_hop_dong`
--
ALTER TABLE `dich_vu_trong_hop_dong`
  ADD CONSTRAINT `dich_vu_trong_hop_dong_id_dich_vu_foreign` FOREIGN KEY (`id_dich_vu`) REFERENCES `dich_vu_le` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dich_vu_trong_hop_dong_id_hop_dong_foreign` FOREIGN KEY (`id_hop_dong`) REFERENCES `hop_dong` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `diem_danh`
--
ALTER TABLE `diem_danh`
  ADD CONSTRAINT `diem_danh_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD CONSTRAINT `hop_dong_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_tho_chup_id_foreign` FOREIGN KEY (`tho_chup_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_tho_edit_id_foreign` FOREIGN KEY (`tho_edit_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_tho_make_id_foreign` FOREIGN KEY (`tho_make_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hop_dong_cho_thue_trang_phuc`
--
ALTER TABLE `hop_dong_cho_thue_trang_phuc`
  ADD CONSTRAINT `hop_dong_cho_thue_trang_phuc_nguoi_cho_thue_foreign` FOREIGN KEY (`nguoi_cho_thue`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_cuoi`
--
ALTER TABLE `hop_dong_cuoi`
  ADD CONSTRAINT `hop_dong_cuoi_concept_id_foreign` FOREIGN KEY (`concept_id`) REFERENCES `concept` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_nguoi_up_link_demo_id_foreign` FOREIGN KEY (`nguoi_up_link_demo_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_nguoi_up_link_in_id_foreign` FOREIGN KEY (`nguoi_up_link_in_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_tho_chup_id_foreign` FOREIGN KEY (`tho_chup_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_tho_edit_id_foreign` FOREIGN KEY (`tho_edit_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_cuoi_tho_make_id_foreign` FOREIGN KEY (`tho_make_id`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hop_dong_cuoi_dich_vu_le`
--
ALTER TABLE `hop_dong_cuoi_dich_vu_le`
  ADD CONSTRAINT `hop_dong_cuoi_dich_vu_le_dich_vu_le_id_foreign` FOREIGN KEY (`dich_vu_le_id`) REFERENCES `dich_vu_le` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_cuoi_dich_vu_le_hop_dong_cuoi_id_foreign` FOREIGN KEY (`hop_dong_cuoi_id`) REFERENCES `hop_dong_cuoi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_cuoi_nhom_dich_vu`
--
ALTER TABLE `hop_dong_cuoi_nhom_dich_vu`
  ADD CONSTRAINT `hop_dong_cuoi_nhom_dich_vu_dich_vu_le_id_foreign` FOREIGN KEY (`dich_vu_le_id`) REFERENCES `dich_vu_le` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_cuoi_nhom_dich_vu_hop_dong_cuoi_id_foreign` FOREIGN KEY (`hop_dong_cuoi_id`) REFERENCES `hop_dong_cuoi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_cuoi_nhom_dich_vu_nhom_dich_vu_id_foreign` FOREIGN KEY (`nhom_dich_vu_id`) REFERENCES `nhom_dich_vu` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_cuoi_thanh_vien_sale`
--
ALTER TABLE `hop_dong_cuoi_thanh_vien_sale`
  ADD CONSTRAINT `hop_dong_cuoi_thanh_vien_sale_hop_dong_id_foreign` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong_cuoi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_cuoi_thanh_vien_sale_nhan_vien_id_foreign` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_vien` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_cuoi_trang_phuc`
--
ALTER TABLE `hop_dong_cuoi_trang_phuc`
  ADD CONSTRAINT `hop_dong_cuoi_trang_phuc_hop_dong_cuoi_id_foreign` FOREIGN KEY (`hop_dong_cuoi_id`) REFERENCES `hop_dong_cuoi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_cuoi_trang_phuc_trang_phuc_id_foreign` FOREIGN KEY (`trang_phuc_id`) REFERENCES `trang_phuc` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_dich_vu_le`
--
ALTER TABLE `hop_dong_dich_vu_le`
  ADD CONSTRAINT `hop_dong_dich_vu_le_dich_vu_le_id_foreign` FOREIGN KEY (`dich_vu_le_id`) REFERENCES `dich_vu_le` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_dich_vu_le_hop_dong_id_foreign` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hop_dong_thanh_toan`
--
ALTER TABLE `hop_dong_thanh_toan`
  ADD CONSTRAINT `hop_dong_thanh_toan_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `nhan_vien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_thanh_toan_hop_dong_id_foreign` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong_cuoi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD CONSTRAINT `nhan_vien_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhan_vien_phong_ban`
--
ALTER TABLE `nhan_vien_phong_ban`
  ADD CONSTRAINT `nhan_vien_phong_ban_nhan_vien_id_foreign` FOREIGN KEY (`nhan_vien_id`) REFERENCES `nhan_vien` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhan_vien_phong_ban_phong_ban_id_foreign` FOREIGN KEY (`phong_ban_id`) REFERENCES `phong_ban` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhom_dich_vu`
--
ALTER TABLE `nhom_dich_vu`
  ADD CONSTRAINT `nhom_dich_vu_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `nhom_dich_vu_dich_vu_le`
--
ALTER TABLE `nhom_dich_vu_dich_vu_le`
  ADD CONSTRAINT `nhom_dich_vu_dich_vu_le_dich_vu_le_id_foreign` FOREIGN KEY (`dich_vu_le_id`) REFERENCES `dich_vu_le` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhom_dich_vu_dich_vu_le_nhom_dich_vu_id_foreign` FOREIGN KEY (`nhom_dich_vu_id`) REFERENCES `nhom_dich_vu` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `note_khach_moi`
--
ALTER TABLE `note_khach_moi`
  ADD CONSTRAINT `note_khach_moi_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `note_khach_moi_phu_trach_sale_id_foreign` FOREIGN KEY (`phu_trach_sale_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `note_khach_moi_phu_trach_sale`
--
ALTER TABLE `note_khach_moi_phu_trach_sale`
  ADD CONSTRAINT `note_khach_moi_phu_trach_sale_note_khach_moi_id_foreign` FOREIGN KEY (`note_khach_moi_id`) REFERENCES `note_khach_moi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_khach_moi_phu_trach_sale_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phieu_thu_chi`
--
ALTER TABLE `phieu_thu_chi`
  ADD CONSTRAINT `phieu_thu_chi_nguoi_duyet_id_foreign` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `phieu_thu_chi_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham_cho_thue`
--
ALTER TABLE `san_pham_cho_thue`
  ADD CONSTRAINT `san_pham_cho_thue_hop_dong_id_foreign` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong_cho_thue_trang_phuc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `san_pham_cho_thue_san_pham_id_foreign` FOREIGN KEY (`san_pham_id`) REFERENCES `trang_phuc` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

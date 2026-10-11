-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2026 at 08:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tukang_emas_rezky`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$arLi7s75fw6bDd4/lsRkcOIqgomVrZkgXWjmoWvSyYtj/KnTeETB6');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `judul`, `deskripsi`, `gambar`, `tanggal_dibuat`) VALUES
(5, '', '', 'galeri_6ac8b232326c26.94470091.jpg', '2026-10-09 17:21:54'),
(6, '', '', 'galeri_6ac8b322c09153.45902287.jpg', '2026-10-09 17:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `harga_emas`
--

CREATE TABLE `harga_emas` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `harga_88` decimal(15,2) NOT NULL,
  `harga_90` decimal(15,2) NOT NULL,
  `sumber` varchar(255) NOT NULL,
  `waktu_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harga_emas`
--

INSERT INTO `harga_emas` (`id`, `tanggal`, `harga_88`, `harga_90`, `sumber`, `waktu_update`) VALUES
(1, '2026-10-06', 2102720.85, 2150509.96, 'GoldPrice.dev', '2026-10-06 18:47:29'),
(2, '2026-10-07', 2107148.26, 2155037.99, 'GoldPrice.dev', '2026-10-07 07:19:48'),
(3, '2026-10-09', 2119026.25, 2167185.93, 'GoldPrice.dev', '2026-10-09 17:53:01'),
(4, '2026-10-10', 2123546.72, 2171809.14, 'GoldPrice.dev', '2026-10-11 05:58:11'),
(5, '2026-10-11', 2123507.56, 2171769.09, 'GoldPrice.dev', '2026-10-11 06:01:34');

-- --------------------------------------------------------

--
-- Table structure for table `koleksi`
--

CREATE TABLE `koleksi` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `koleksi`
--

INSERT INTO `koleksi` (`id`, `nama`, `kategori`, `deskripsi`, `gambar`, `tanggal_dibuat`) VALUES
(2, 'Cincin Pernikahan Les', 'Cincin', 'Cincin untuk pasangan dengan desain elegan dan dapat disesuaikan.', 'cincin kawin les.jpeg', '2026-10-09 18:46:46'),
(3, 'Cincin Pernikahan Permata Les Tengah', 'Cincin', 'Cincin untuk pasangan dengan desain elegan dan dapat disesuaikan.', 'cincin kawin permata les tengah.jpeg', '2026-10-09 18:46:46'),
(4, 'Cincin Ketupat', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin  ketupat.jpeg', '2026-10-09 18:46:46'),
(5, 'Cincin 2 Hati', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin 2 hati.jpeg', '2026-10-09 18:46:46'),
(6, 'Cincin Kotak', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin kotak.jpeg', '2026-10-09 18:46:46'),
(7, 'Cincin S3', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin s3.jpeg', '2026-10-09 18:46:46'),
(8, 'Cincin Set', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin set.jpeg', '2026-10-09 18:46:46'),
(9, 'Cincin Soliter 6 Gigi', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin soliter 6 gigi.jpeg', '2026-10-09 18:46:46'),
(10, 'Cincin Soliter 4 Gigi', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin soliter.jpeg', '2026-10-09 18:46:46'),
(11, 'Cincin SS', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin ss.jpeg', '2026-10-09 18:46:46'),
(12, 'Cincin Tinju', 'Cincin', 'Cincin custom yang dibuat berdasarkan desain atau referensi dari pelanggan.', 'cincin tinju.jpeg', '2026-10-09 18:46:46'),
(13, 'Anting Emas Bayi', 'Anting', 'Anting emas dengan berbagai model yang dapat disesuaikan.', 'anting bayi.jpeg', '2026-10-09 18:46:46'),
(14, 'Kalung Emas', 'Kalung', 'Kalung emas dengan pilihan desain untuk berbagai kebutuhan.', 'kalung.jpeg', '2026-10-09 18:46:46'),
(15, 'Gelang Emas Bayi', 'Gelang', 'Gelang emas dengan ukuran dan desain yang dapat disesuaikan.', 'gelang bayi.jpeg', '2026-10-09 18:46:46'),
(16, 'Gelang Emas Geroncong', 'Gelang', 'Gelang emas dengan ukuran dan desain yang dapat disesuaikan.', 'gelang geroncong.jpeg', '2026-10-09 18:46:46'),
(17, 'Gelang Emas Tikar', 'Gelang', 'Gelang emas dengan ukuran dan desain yang dapat disesuaikan.', 'gelang tikar.jpeg', '2026-10-09 18:46:46'),
(18, 'Gelang Emas Tinju', 'Gelang', 'Gelang emas dengan ukuran dan desain yang dapat disesuaikan.', 'gelang tinju.jpeg', '2026-10-09 18:46:46'),
(19, 'Gelang Emas Tinju v2', 'Gelang', 'Gelang emas dengan ukuran dan desain yang dapat disesuaikan.', 'gelang tinju versi 2.jpeg', '2026-10-09 18:46:46'),
(20, 'Liontin Emas', 'Liontin', 'Liontin dengan berbagai bentuk dan desain.', 'liontin.jpeg', '2026-10-09 18:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_perhiasan` varchar(100) NOT NULL,
  `ukuran` varchar(50) NOT NULL,
  `kadar` varchar(10) NOT NULL,
  `berat` decimal(10,2) NOT NULL,
  `harga_emas` decimal(15,2) NOT NULL,
  `desain` text DEFAULT NULL,
  `ukiran` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `estimasi` decimal(15,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Menunggu',
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `nama`, `jenis_perhiasan`, `ukuran`, `kadar`, `berat`, `harga_emas`, `desain`, `ukiran`, `catatan`, `estimasi`, `status`, `tanggal`) VALUES
(2, 'Aya', 'Cincin', '18', '88%', 4.00, 2102720.85, '', 'Al', '', 8410883.40, 'Diproses', '2026-10-06 20:13:46'),
(3, 'Aya', 'Cincin', '10', '90%', 4.00, 2150509.96, 'cincin ketupat', '-', '', 8602039.84, 'Menunggu', '2026-10-06 20:27:05'),
(4, 'Rezky Awalya Rustam', 'Cincin', '9', '88%', 4.00, 2102720.85, 'cincin ketupat', '-', '-', 8410883.40, 'Menunggu', '2026-10-06 20:45:17'),
(5, 'Nurhikmah', 'Cincin', '12', '90%', 5.00, 2150509.96, 'cincin pernikahan', 'R-N', '-', 10752549.80, 'Menunggu', '2026-10-06 20:48:41'),
(6, 'Rustam', 'Cincin', '25', '90%', 5.00, 2150509.96, 'cincin pernikahan', 'N-R', 'Ukiran N di cincin laki-laki dan R di cincin perempuan', 10752549.80, 'Diproses', '2026-10-06 21:04:02'),
(10, 'opi', 'Anting', 'dewasa', '90%', 2.00, 2167185.93, '-', '-', '-', 4334371.86, 'Menunggu', '2026-10-09 19:19:21'),
(11, 'kjs', 'Cincin', '98', '90%', 3.64, 2171769.09, '-', '-', '-', 7905239.49, 'Menunggu', '2026-10-11 06:01:54'),
(12, 'REZKY AWALYA', 'Cincin', '10', '90%', 3.00, 2171769.09, 'cincin ketupat', '-', '-', 6515307.27, 'Diproses', '2026-10-11 06:06:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `harga_emas`
--
ALTER TABLE `harga_emas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_tanggal` (`tanggal`);

--
-- Indexes for table `koleksi`
--
ALTER TABLE `koleksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `harga_emas`
--
ALTER TABLE `harga_emas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `koleksi`
--
ALTER TABLE `koleksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 02:51 PM
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
-- Database: `maybank`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `npk` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_position` int(11) NOT NULL,
  `id_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `npk`, `nama_lengkap`, `email`, `password`, `id_position`, `id_level`) VALUES
(17, 412020031, 'Albert Ardiansyah', 'albertardiansyah06@gmail.com', '$2y$10$M5bDStw02aIJabTVIMLVIOmvQB73/TQ8ch2ULj5Rt1hXRZSyFQQ5.', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_level`
--

CREATE TABLE `admin_level` (
  `id` int(11) NOT NULL,
  `level` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_level`
--

INSERT INTO `admin_level` (`id`, `level`) VALUES
(1, 'Super Admin'),
(2, 'Administrator'),
(3, 'Admin Uploader');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `size` decimal(10,0) NOT NULL,
  `directory` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `date_uploaded` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`id`, `nama`, `size`, `directory`, `uploaded_by`, `date_uploaded`) VALUES
(1, 'Copy of FR-UBM-0.0.5.1 V0.R1 Daftar Hadir(1).xls', 200704, '../../assets/uploads/Copy of FR-UBM-0.0.5.1 V0.R1 Daftar Hadir(1).xls', 0, '2024-11-05 01:24:54');

-- --------------------------------------------------------

--
-- Table structure for table `log_download`
--

CREATE TABLE `log_download` (
  `id` int(11) NOT NULL,
  `id_file` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` decimal(10,0) NOT NULL,
  `id_user` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `npk_user` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `date_downloaded` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_download`
--

INSERT INTO `log_download` (`id`, `id_file`, `file_name`, `file_size`, `id_user`, `full_name`, `npk_user`, `position`, `date_downloaded`) VALUES
(1, 0, 'Copy of FR-UBM-0.0.5.1 V0.R1 Daftar Hadir(1).xls', 200704, 0, 'Felix Savero', 412020015, 'Audit Supervisor', '2024-11-05 01:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `npk_user` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry_time` datetime NOT NULL,
  `used` tinyint(4) NOT NULL,
  `date_used` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`id`, `position`) VALUES
(1, 'Chief Audit Officer'),
(2, 'Audit Supervisor'),
(3, 'Audit Staff'),
(4, 'Audit Internship');

-- --------------------------------------------------------

--
-- Table structure for table `shared_file`
--

CREATE TABLE `shared_file` (
  `id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `id_user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shared_file`
--

INSERT INTO `shared_file` (`id`, `nama_file`, `id_user`) VALUES
(1, 'Copy of FR-UBM-0.0.5.1 V0.R1 Daftar Hadir(1).xls', 'everyone');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `npk` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_position` int(11) NOT NULL,
  `date_joined` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `npk`, `nama_lengkap`, `email`, `password`, `id_position`, `date_joined`) VALUES
(1, 412020015, 'Felix Savero', 'felix@gmail.com', '$2y$10$/l/3fVwSpdLBTAd0MhANlu3YYjHe.tV70BZ9ONSp7wXCbI2FLNXFe', 2, '2024-11-05 01:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `npk_user` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `date_login` datetime NOT NULL,
  `date_logout` datetime NOT NULL,
  `logged_out` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`id`, `id_user`, `npk_user`, `full_name`, `date_login`, `date_logout`, `logged_out`) VALUES
(1, 0, 412020015, 'Felix Savero', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(2, 0, 412020015, 'Felix Savero', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_level`
--
ALTER TABLE `admin_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_download`
--
ALTER TABLE `log_download`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared_file`
--
ALTER TABLE `shared_file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `admin_level`
--
ALTER TABLE `admin_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log_download`
--
ALTER TABLE `log_download`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shared_file`
--
ALTER TABLE `shared_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

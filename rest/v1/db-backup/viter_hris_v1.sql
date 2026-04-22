-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 08:53 AM
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
-- Database: `viter_hris_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_aid` int(11) NOT NULL,
  `employee_is_active` tinyint(1) NOT NULL,
  `employee_first_name` varchar(128) NOT NULL,
  `employee_middle_name` varchar(128) NOT NULL,
  `employee_last_name` varchar(128) NOT NULL,
  `employee_email` varchar(255) NOT NULL,
  `employee_created` datetime NOT NULL,
  `employee_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_roles`
--

CREATE TABLE `settings_roles` (
  `role_aid` int(11) NOT NULL,
  `role_is_active` tinyint(1) NOT NULL,
  `role_name` varchar(128) NOT NULL,
  `role_description` text NOT NULL,
  `role_created` datetime NOT NULL,
  `role_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings_roles`
--

INSERT INTO `settings_roles` (`role_aid`, `role_is_active`, `role_name`, `role_description`, `role_created`, `role_updated`) VALUES
(17, 1, 'developer1', 'developer', '2026-04-16 10:04:33', '2026-04-16 10:04:56'),
(18, 1, 'admin', 'admin', '2026-04-16 12:04:19', '2026-04-16 12:04:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_aid`);

--
-- Indexes for table `settings_roles`
--
ALTER TABLE `settings_roles`
  ADD PRIMARY KEY (`role_aid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_aid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_roles`
--
ALTER TABLE `settings_roles`
  MODIFY `role_aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 04:49 PM
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
-- Database: `laundry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_regno` varchar(20) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('booked','in_use','completed','cancelled') DEFAULT 'booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_regno`, `machine_id`, `booking_date`, `booking_time`, `status`, `created_at`) VALUES
(4, '22b91a05a6', 1, '2025-04-23', '16:30:00', 'cancelled', '2025-04-08 19:01:59'),
(5, '22b91a05a6', 2, '2025-04-23', '16:30:00', 'cancelled', '2025-04-08 19:03:28'),
(6, '22b91a05a6', 2, '2025-04-22', '15:44:00', 'completed', '2025-04-08 19:11:55'),
(7, '22b91a05a6', 5, '2025-04-18', '18:50:00', 'completed', '2025-04-08 19:14:32'),
(8, '22b91a05a6', 3, '2025-04-11', '10:34:00', 'completed', '2025-04-09 01:04:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `created_at`) VALUES
(4, 'no washing day', 'no washing is allowed on monday', '2025-04-08 16:22:30'),
(5, 'change in timings', 'on saturday no washing is allowed after 7pm', '2025-04-08 16:26:31');

-- --------------------------------------------------------

--
-- Table structure for table `notification_reads`
--

CREATE TABLE `notification_reads` (
  `id` int(11) NOT NULL,
  `regno` varchar(20) DEFAULT NULL,
  `notification_id` int(11) DEFAULT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_reads`
--

INSERT INTO `notification_reads` (`id`, `regno`, `notification_id`, `read_at`) VALUES
(1, '22b91a05a6', 4, '2025-04-08 16:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `registration_number` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `year_of_study` enum('1','2','3','4') NOT NULL,
  `hostel_block` varchar(50) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`registration_number`, `username`, `email`, `password`, `year_of_study`, `hostel_block`, `room_number`, `created_at`, `role`, `profile_picture`) VALUES
('22b91a0522', 'dundi', 'dundisairam@gmail.com', '$2y$10$O6tbJAYwKQ77iLlSnGT6Se93/H8qolv5PDV7vYaoFcgeA9EnhOSgm', '1', '1', '221', '2025-03-26 01:30:06', 'user', NULL),
('22b91a05a6', 'bharavi', 'kankatalabharavi@gmail.com', '$2y$10$wZbWchh/5X5l8apz.HQm7.MVBb9phzVsAWGz7XA1tVyZ0TVt4kN7K', '3', '1', '222', '2025-04-08 13:23:26', 'user', NULL),
('22b91a05b1', 'himani', 'himanikatari@gmail.com', '$2y$10$nZ1f97Ntz3cdu8D9mKX0AeGu70RcC0kmSk2mKyunpgcr37R84HALK', '3', '1', '222', '2025-03-26 01:33:49', 'user', NULL),
('22b91a05b5', 'keerthi', 'keerthisrivelaga@gmail.com', '$2y$10$apsOAyeEaFcd6hlWHEBULeQ4wiFNtN2OHUeV75bA/CjtnLVSrTf2W', '1', '6', '403', '2025-04-01 07:34:55', 'user', NULL),
('22b91a05w4', 'violet', 'xadenriorson@gmail.com', '$2y$10$nncuMwGXCBvnTXFTGpw/1Oe31gSeToPoIWOt3K3jjlcNDyEzae1u6', '3', '5', '111', '2026-03-18 15:29:00', 'admin', NULL),
('22b91a0629', 'keerthi_v21', 'keerthi.v1355@gmail.com', '$2y$10$kNBX3m7tkwzRwmUN/UnceeyVP5tyx/1R0FCA1mT1QSFWDAQvID.9K', '3', '1', '403', '2025-04-08 04:24:38', 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `washing_machines`
--

CREATE TABLE `washing_machines` (
  `id` int(11) NOT NULL,
  `machine_name` varchar(50) NOT NULL,
  `status` enum('free','in_use','out_of_service') NOT NULL DEFAULT 'free',
  `last_maintenance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `washing_machines`
--

INSERT INTO `washing_machines` (`id`, `machine_name`, `status`, `last_maintenance`) VALUES
(1, 'machine1', 'free', NULL),
(2, 'Machine 2', 'free', NULL),
(3, 'Machine 3', 'free', NULL),
(4, 'Machine 4', 'free', NULL),
(5, 'machine 5', 'free', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_regno` (`user_regno`),
  ADD KEY `machine_id` (`machine_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`registration_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `washing_machines`
--
ALTER TABLE `washing_machines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notification_reads`
--
ALTER TABLE `notification_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_regno`) REFERENCES `users` (`registration_number`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`machine_id`) REFERENCES `washing_machines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 08:36 PM
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
-- Database: `my_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_num` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `timeslots` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_num`, `date`, `timeslots`) VALUES
(40, 8, 21, '2024-12-12', '08:0AM-09:0AM'),
(41, 8, 23, '2024-12-31', '15:0PM-16:0PM'),
(42, 8, 23, '2024-12-23', '16:0PM-17:0PM'),
(50, 8, 2005, '2024-12-18', '10:0AM-11:0AM'),
(52, 8, 1050, '2024-12-26', '15:0PM-16:0PM');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_num` int(11) NOT NULL,
  `department` varchar(2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `equipment` text DEFAULT NULL,
  `type` enum('lecture','seminar','lab','meeting') DEFAULT 'lecture',
  `available_from` time DEFAULT '08:00:00',
  `available_to` time DEFAULT '18:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_num`, `department`, `capacity`, `equipment`, `type`, `available_from`, `available_to`) VALUES
(21, 'IS', 40, 'Projector, Computers', 'meeting', '13:00:00', '18:00:00'),
(23, 'IS', 20, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(28, 'IS', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(29, 'IS', 30, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(30, 'IS', 50, 'Computers, Projector', 'lecture', '08:00:00', '18:00:00'),
(32, 'IS', 20, 'Whiteboard, Projector', 'lecture', '08:00:00', '18:00:00'),
(49, 'CS', 30, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(51, 'CS', 50, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(56, 'CS', 40, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(57, 'CS', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(58, 'CS', 30, 'Computers, Projector', 'lecture', '08:00:00', '18:00:00'),
(60, 'CS', 30, 'Whiteboard, Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(77, 'CE', 40, 'Projector, Whiteboard, Computers', 'lecture', '08:00:00', '18:00:00'),
(79, 'CE', 40, 'Projector, Whiteboard, Computers', 'meeting', '08:00:00', '18:00:00'),
(84, 'CE', 20, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(85, 'CE', 20, 'Whiteboard, Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(86, 'CE', 20, 'Computers, Projector', 'lecture', '08:00:00', '18:00:00'),
(88, 'CE', 20, 'Whiteboard, Projector', 'lecture', '08:00:00', '18:00:00'),
(1006, 'IS', 40, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(1008, 'IS', 20, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(1010, 'IS', 50, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(1011, 'IS', 20, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(1012, 'IS', 30, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(1014, 'IS', 20, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(1043, 'CS', 30, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(1045, 'CS', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(1047, 'CS', 30, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(1048, 'CS', 40, 'Projector, Whiteboard, Computers', 'lab', '08:00:00', '18:00:00'),
(1050, 'CS', 30, 'Projector, Whiteboard, Computers', 'seminar', '08:00:00', '18:00:00'),
(1052, 'CS', 50, 'Projector, Whiteboard, Computers', 'lecture', '08:00:00', '18:00:00'),
(1081, 'CE', 20, 'Projector, Whiteboard, Computers', 'lab', '08:00:00', '18:00:00'),
(1083, 'CE', 40, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(1085, 'CE', 20, 'Projector, Whiteboard, Computers', 'lecture', '08:00:00', '18:00:00'),
(1086, 'CE', 20, 'Projector, Whiteboard, Computers', 'meeting', '08:00:00', '18:00:00'),
(1087, 'CE', 40, 'Projector, Whiteboard, Computers', 'lab', '08:00:00', '18:00:00'),
(1089, 'CE', 40, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(2005, 'IS', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(2007, 'IS', 20, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(2008, 'IS', 50, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(2010, 'IS', 20, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(2011, 'IS', 30, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(2012, 'IS', 20, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(2013, 'IS', 20, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(2015, 'IS', 20, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(2043, 'CS', 30, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(2045, 'CS', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(2046, 'CS', 30, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(2048, 'CS', 20, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(2049, 'CS', 50, 'Projector, Computers', 'seminar', '08:00:00', '18:00:00'),
(2050, 'CS', 30, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(2051, 'CS', 50, 'Projector, Computers', 'meeting', '08:00:00', '18:00:00'),
(2053, 'CS', 30, 'Projector, Computers', 'lab', '08:00:00', '18:00:00'),
(2081, 'CE', 40, 'Projector, Whiteboard, Computers', 'lab', '08:00:00', '18:00:00'),
(2083, 'CE', 30, 'Projector, Whiteboard, Computers', 'seminar', '08:00:00', '18:00:00'),
(2084, 'CE', 40, 'Projector, Whiteboard, Computers', 'lecture', '08:00:00', '18:00:00'),
(2086, 'CE', 50, 'Projector, Whiteboard, Computers', 'meeting', '08:00:00', '18:00:00'),
(2087, 'CE', 40, 'Projector, Whiteboard, Computers', 'lab', '08:00:00', '18:00:00'),
(2088, 'CE', 20, 'Projector, Whiteboard, Computers', 'seminar', '08:00:00', '18:00:00'),
(2089, 'CE', 20, 'Projector, Computers', 'lecture', '08:00:00', '18:00:00'),
(2091, 'CE', 40, 'Projector, Whiteboard, Computers', 'meeting', '08:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `Fname` varchar(20) DEFAULT NULL,
  `Lname` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','pending') DEFAULT 'user',
  `profil_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `pass`, `Fname`, `Lname`, `role`, `profil_pic`) VALUES
(1, '202101500@stu.uob.edu.bh', '$2y$10$KoCp4GSJfF8cfBNkzZQg3OgGNaFkNw6tL6Md593XJWbjpDI0ryP9u', NULL, NULL, 'user', NULL),
(2, '202101600@stu.uob.edu.bh', '$2y$10$y29hR6ALOpWOaJ5oLoRNu.InkgNJ1miEidyGV0U06xGuhBfBSezPO', NULL, NULL, 'user', NULL),
(3, '202101700@stu.uob.edu.bh', '$2y$10$n.2iKyWQ4DFJ4Whl9wSHN.mBcJ2OA7jysvTRM5JjnveF3lxwmTCcS', NULL, NULL, 'user', NULL),
(4, '1111111111@stu.uob.edu.bh', '$2y$10$67wxVmv82t33RpN1n/eKCOaK3VzJNZw4Ik.syIgW7FkhY6z/oR7h6', NULL, NULL, 'user', NULL),
(5, '12121212@stu.uob.edu.bh', '$2y$10$E3NDd.xxmdL39aL/kMbf.u3ZQtx8rhkpYMQKlLMFrH6xwCZJs5AAS', NULL, NULL, 'user', NULL),
(6, '2222222222@stu.uob.edu.bh', '$2y$10$gtd6zUJqa8iL4gPOTugG3e6lv0XydszNVMB8HPp5FVyz5tPtsklOG', NULL, NULL, 'user', NULL),
(7, '11111111111@stu.uob.edu.bh', '$2y$10$Q.lqs0N7olgO/jqvlB1zQenmnOr2F7n6Bb02C29n8ZjaHle8/NbsS', 'taif', 'taher', 'pending', NULL),
(8, '12345@stu.uob.edu.bh', '$2y$10$QaqzKxYNQxy0cr8KjglO9OFlMMgK8sZiljv0AGDO9M.G1sbE37Y.y', 'sara', 'ahmed', 'pending', NULL),
(9, '202101300@uob.edu.bh', '$2y$10$AgeGr5yWahuo6171pK61SecOpq2vUNhIzG2aTPm69sp2Pt7phz2qO', NULL, NULL, 'user', NULL),
(10, '5555555555@gmail.com', '$2y$10$nllcTU74w0xWuQD8fBKtj.h3L5DjYJVFo4yL2138KioYJi.e7Wl1e', NULL, NULL, 'user', NULL),
(12, '000000@stu.uob.edu.bh', '$2y$10$L.MfHfpJ3ONH9al9Jl1iC.TECFHOxfh.YB9/1jBnC7B7vChh6OIES', NULL, NULL, 'admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_num`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_num`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_num`) REFERENCES `rooms` (`room_num`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

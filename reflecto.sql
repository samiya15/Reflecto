-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 12:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reflecto`
--
CREATE DATABASE IF NOT EXISTS `reflecto` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `reflecto`;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courseadmin`
--

DROP TABLE IF EXISTS `courseadmin`;
CREATE TABLE `courseadmin` (
  `course_admin_id` int(11) NOT NULL,
  `course_admin_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courseadmin`
--

INSERT INTO `courseadmin` (`course_admin_id`, `course_admin_name`, `email`, `faculty_name`, `password`, `faculty_id`) VALUES
(1, 'Course Admin', 'cadmin@gmail.com', 'SCES', '', NULL),
(2, 'sces admin', 'sces@gmail.com', 'SCES', '', 1),
(3, 'sbs admin', 'sbs@gmail.com', 'SBS', '', NULL),
(4, 'Esra Can', 'esra@gmail.com', NULL, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE `faculty` (
  `faculty_id` int(11) NOT NULL,
  `faculty_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`) VALUES
(1, 'SCES');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `sentiment` varchar(20) DEFAULT NULL,
  `has_profanity` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `course_taught` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `unit_taught` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `profile_completed` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `first_name`, `last_name`, `email`, `faculty_name`, `course_taught`, `password`, `faculty_id`, `profile_photo`, `unit_taught`, `verification_status`, `profile_completed`) VALUES
(1, 17, '', '', '', 'SCES', 'BBIT, ICS, BCOM', '', NULL, NULL, 'BBT2301, ICS3201, BCOM2105, BBT1101', 'approved', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_faculties`
--

DROP TABLE IF EXISTS `lecturer_faculties`;
CREATE TABLE `lecturer_faculties` (
  `lecturer_faculty_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_faculties`
--

INSERT INTO `lecturer_faculties` (`lecturer_faculty_id`, `lecturer_id`, `faculty_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `student_course` varchar(100) DEFAULT NULL,
  `year_of_study` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `first_name`, `last_name`, `email`, `faculty_name`, `student_course`, `year_of_study`, `password`, `faculty_id`, `profile_photo`, `status`) VALUES
(1, 19, '', '', '', NULL, NULL, 0, '', 1, NULL, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `student_updates`
--

DROP TABLE IF EXISTS `student_updates`;
CREATE TABLE `student_updates` (
  `update_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `student_course` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `systemadmin`
--

DROP TABLE IF EXISTS `systemadmin`;
CREATE TABLE `systemadmin` (
  `system_admin_id` int(11) NOT NULL,
  `system_admin_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstName`, `lastName`, `email`, `password`, `role`, `status`) VALUES
(1, 'Jane ', 'Doe', 'example@gmail.com', '$2y$10$xg5m3r8J9IQHGVprvp66Nu4E7UAq1/m8Vs.WBLcHBjoisUsILYwXO', 1, 'pending'),
(2, 'Amina', 'Hassan', 'example2@gmail.com', '$2y$10$V5kWz39CZB6AaN90r1vtaeKUpe5blEcu04PUN2sCXa5Y/jcqDYEPC', 2, 'approved'),
(3, 'Mellisa', 'James', 'mlissa@gmail.com', '$2y$10$wO71VcVcEpZA3SpTi3FoOuqgDfyzRn2EAlH1oAt0GtVjGVcgSNdLG', 1, 'pending'),
(4, 'example', 'three', 'example3@gmail.com', '$2y$10$0pn.0AwxfAwUeGX/0VRE6.eIELDkoWfZejhB.YkvlOiystKb4DhxS', 1, 'pending'),
(5, 'ejany', 'jane', 'ej@gmail.com', '$2y$10$ibwvSP0i2V5SWsOwMX7.tuIaD4CwJtU8XeL5zdz9LfwOmXchCPbUa', 3, 'rejected'),
(6, 'try', 'one', 'try@gmail.com', '$2y$10$G59pL/PJGgNBGuyk6SKnqeXfKKwaEIGz5RKuz7zwQaeZwGaBmFT0i', 1, 'pending'),
(7, 'Fatuma', 'Ahmed', 'fatma@gmail.com', '$2y$10$P/yrU.VtAx5nQQyFspVgGeuRHZmrhDMmwW4aRmRpTtvcWkz/DwJpW', 1, 'pending'),
(8, 'Otieno', 'Mwithiki', 'mwioti@gmail.com', '$2y$10$awKRXvwUxoKWaj/TJX6oZe3sPJb/YvdFdBtdQzilR9a5d6gEPi.da', 2, 'approved'),
(9, 'Jude', 'Mido', 'jmido@gmail.com', '$2y$10$0mQTuxiU.vCYq0VQGIBniOKbnDSUK7UtU6N8JP34nrTTx3eHRp4Y2', 2, 'approved'),
(10, 'cos', 'Admin', 'cadmin@gmail.com', '$2y$10$nChct.9ulYwgwikfRYrY0eosKFUgGRoewS4GRzBjMFxKPheKRxghK', 3, 'approved'),
(11, 'System', 'Admin', 'sysadmin@strathmore.edu', '$2y$10$eDygFx0/GcmQQu/k4GL2Quc68GxQmYulIR7PdWG/rTICjD2gKVPrO', 4, 'approved'),
(12, 'hassan', 'Ahmed', 'hahmed@gmail.com', '$2y$10$/oL8hPAaElDSkY6w3Y.zZO1fkWhbj9hGIeq8AdNjttjMCJar3uaBW', 1, 'pending'),
(13, 'Amanda', 'Awiti', 'aawiti@gmail.com', '$2y$10$yFHSSIINW5rTzDyr2Q9gReuJXYUyPRo96l/zZMwHWk6nfFS0OwbCS', 2, 'approved'),
(14, 'Hasna', 'Mugo', 'hmugo@strathmore.edu', '$2y$10$0mR7xkI5daohYe51VrAUQ.yNFYi9LWeEjfV78QSIudNADRsuyt11e', 2, 'approved'),
(15, 'sces', 'admin', 'sces@gmail.com', '$2y$10$9s5yJxzGIdCA6CmvPxuYFOvRIUMh1c.LZpWMfz4UiuVaWTtvsZahm', 3, 'approved'),
(16, 'sbs', 'admin', 'sbs@gmail.com', '$2y$10$WHhWau.AB6H9dk/NKsSpAuTzT2BKDfuPy/4bOUWU4taVxFnBi/rzi', 3, 'approved'),
(17, 'lec', 'one', 'lecone@gmail.com', '$2y$10$zbpM4JIdKZKWVorXPbB5.e0kCp6SYzUpyaX/otczNKd4jvLVwTnyS', 2, 'approved'),
(18, 'Esra', 'Can', 'esra@gmail.com', '$2y$10$9gc.WhHjrs7olo0UZcFJSOhqnj0knF6IrfT/fdUHi0M8QoVWwnIqS', 3, 'approved'),
(19, 'student', 'one', 'student@gmail.com', '$2y$10$wJxj5qOmE9o8r0R9HTRdcuZJBfsgE8VhahcpskZGgwP4ggXfM2AES', 1, 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `courseadmin`
--
ALTER TABLE `courseadmin`
  ADD PRIMARY KEY (`course_admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `courseadmin_ibfk_1` (`faculty_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`);

--
-- Indexes for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  ADD PRIMARY KEY (`lecturer_faculty_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `fk_faculty` (`faculty_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `fk_students_faculty` (`faculty_id`);

--
-- Indexes for table `student_updates`
--
ALTER TABLE `student_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `systemadmin`
--
ALTER TABLE `systemadmin`
  ADD PRIMARY KEY (`system_admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courseadmin`
--
ALTER TABLE `courseadmin`
  MODIFY `course_admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  MODIFY `lecturer_faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_updates`
--
ALTER TABLE `student_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `systemadmin`
--
ALTER TABLE `systemadmin`
  MODIFY `system_admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `courseadmin`
--
ALTER TABLE `courseadmin`
  ADD CONSTRAINT `courseadmin_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`);

--
-- Constraints for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  ADD CONSTRAINT `fk_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lecturer_faculties_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `student_updates`
--
ALTER TABLE `student_updates`
  ADD CONSTRAINT `student_updates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

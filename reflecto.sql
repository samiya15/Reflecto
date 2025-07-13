-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 05:03 PM
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
-- Database: `reflecto`
--
CREATE DATABASE IF NOT EXISTS `reflecto` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `reflecto`;

-- --------------------------------------------------------

--
-- Table structure for table `all_questions`
--

DROP TABLE IF EXISTS `all_questions`;
CREATE TABLE `all_questions` (
  `question_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('scale','text') NOT NULL,
  `created_by` enum('admin','lecturer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `all_questions`
--

INSERT INTO `all_questions` (`question_id`, `form_id`, `question_text`, `question_type`, `created_by`) VALUES
(1, 2, 'how do you like the course', 'scale', 'admin'),
(2, 2, 'does class start and end on time', 'scale', 'admin'),
(3, 2, 'Anything you want to change?', 'text', 'admin'),
(4, 3, 'how do you like the course', 'scale', 'admin'),
(5, 3, 'class start and end on time', 'scale', 'admin'),
(6, 3, 'im happy with the teaching method', 'scale', 'admin'),
(7, 3, 'Anything you want to change?', 'text', 'admin'),
(8, 1, 'is the material offered for the course helpful', 'scale', 'admin'),
(9, 2, 'point of improvement', 'text', 'admin'),
(10, 2, 'point of improvement', 'text', 'admin'),
(11, 3, 'is the material offered for the course helpful', 'scale', 'admin'),
(12, 3, 'any point of change you wish to suggest', 'text', 'admin'),
(13, 3, 'is the material offered for the course helpful', 'scale', 'admin'),
(14, 3, 'any point of change you wish to suggest', 'text', 'admin'),
(15, 3, 'is the material offered for the course helpful', 'scale', 'admin'),
(16, 3, 'any point of change you wish to suggest', 'text', 'admin');

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

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `faculty_id`, `course_name`) VALUES
(1, 2, 'BCOM'),
(2, 1, 'BBIT');

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
  `faculty_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courseadmin`
--

INSERT INTO `courseadmin` (`course_admin_id`, `course_admin_name`, `email`, `faculty_name`, `password`, `faculty_id`, `user_id`) VALUES
(1, 'Course Admin', 'cadmin@gmail.com', 'SCES', '', 2, 10),
(2, 'sces admin', 'sces@gmail.com', 'SCES', '', 1, 15),
(3, 'sbs admin', 'sbs@gmail.com', 'SBS', '', NULL, 16),
(4, 'Esra Can', 'esra@gmail.com', NULL, '', 1, 18),
(5, 'Karanja George', 'george@gmail.com', 'SCES', '', 3, 28),
(6, 'Olekera Oluol', 'oluol@gmail.com', NULL, '', 4, 25),
(7, 'Harry James', 'james@strathmore.edu', NULL, '', 5, 27),
(8, 'Maya Jama', 'jama@sgmail.com', NULL, '', 6, 26),
(9, 'hayat ali', 'hayat@gmail.com', NULL, '', 2, 32);

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
(1, 'SCES'),
(2, 'SBS'),
(3, 'SLS'),
(4, 'SIMS'),
(5, 'SHSS'),
(6, 'STH');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lecturer_id` int(11) NOT NULL,
  `original_text` text NOT NULL,
  `cleaned_text` text NOT NULL,
  `sentiment` enum('positive','neutral','negative') NOT NULL,
  `confidence_score` float DEFAULT NULL,
  `contains_profanity` tinyint(1) DEFAULT NULL,
  `is_anonymous` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `lecturer_id`, `original_text`, `cleaned_text`, `sentiment`, `confidence_score`, `contains_profanity`, `is_anonymous`, `created_at`, `responded`) VALUES
(16, 19, 6, 'thanks', 'thanks', 'positive', 0.44, 0, 0, '2025-07-07 20:08:46', 1),
(19, NULL, 6, 'i hate you', 'i hate you', 'negative', -0.57, 0, 1, '2025-07-07 21:09:04', 0),
(20, 30, 6, 'i hate you', 'i hate you', 'negative', -0.57, 0, 0, '2025-07-07 21:20:01', 0),
(21, NULL, 6, 'you are a great teacher', 'you are a great teacher', 'positive', 0.62, 0, 1, '2025-07-11 08:59:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_archive`
--

DROP TABLE IF EXISTS `feedback_archive`;
CREATE TABLE `feedback_archive` (
  `archive_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lecturer_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `original_text` text DEFAULT NULL,
  `cleaned_text` text DEFAULT NULL,
  `sentiment` varchar(20) DEFAULT NULL,
  `confidence_score` float DEFAULT NULL,
  `contains_profanity` tinyint(1) DEFAULT NULL,
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_text` text DEFAULT NULL,
  `is_anonymous` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_archive`
--

INSERT INTO `feedback_archive` (`archive_id`, `user_id`, `lecturer_id`, `feedback_id`, `original_text`, `cleaned_text`, `sentiment`, `confidence_score`, `contains_profanity`, `reviewed_at`, `response_text`, `is_anonymous`) VALUES
(9, 19, 6, 16, 'thanks', 'thanks', 'positive', 0.44, 0, '2025-07-07 21:17:59', NULL, 0),
(10, NULL, 6, 19, 'i hate you', 'i hate you', 'negative', -0.57, 0, '2025-07-07 21:18:11', NULL, 1),
(11, 30, 6, 20, 'i hate you', 'i hate you', 'negative', -0.57, 0, '2025-07-07 21:20:19', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_forms`
--

DROP TABLE IF EXISTS `feedback_forms`;
CREATE TABLE `feedback_forms` (
  `form_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_forms`
--

INSERT INTO `feedback_forms` (`form_id`, `title`, `faculty_id`, `created_by`, `created_at`) VALUES
(1, 'end of sem', 2, 10, '2025-07-12 16:35:22'),
(2, 'end of sem', 2, 10, '2025-07-12 16:38:13'),
(3, 'example', 1, 15, '2025-07-13 00:05:55'),
(4, 'END OF SEMESTER EVALUATION', 2, 10, '2025-07-13 14:57:42'),
(5, 'END OF SEMESTER EVALUATION', 1, 15, '2025-07-13 14:58:41'),
(6, 'testing', 2, 10, '2025-07-13 17:39:55'),
(7, 'testing profanity', 1, 15, '2025-07-13 17:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_questions`
--

DROP TABLE IF EXISTS `feedback_questions`;
CREATE TABLE `feedback_questions` (
  `question_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('scale','text') NOT NULL,
  `form_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_questions`
--

INSERT INTO `feedback_questions` (`question_id`, `template_id`, `question_text`, `question_type`, `form_id`) VALUES
(1, NULL, 'how do you like the course', 'scale', 2),
(2, NULL, 'does class start and end on time', 'scale', 2),
(3, NULL, 'Anything you want to change?', 'text', 2),
(4, NULL, 'how do you like the course', 'scale', 3),
(5, NULL, 'class start and end on time', 'scale', 3),
(6, NULL, 'im happy with the teaching method', 'scale', 3),
(7, NULL, 'Anything you want to change?', 'text', 3),
(8, NULL, 'how do you like the course', 'scale', 4),
(9, NULL, 'class start and end on time', 'scale', 4),
(10, NULL, 'im happy with the teaching method', 'scale', 4),
(11, NULL, 'Anything you want to change?', 'text', 4),
(12, NULL, 'the material for the course is helpful', 'scale', 5),
(13, NULL, 'class start and end on time', 'scale', 5),
(14, NULL, 'im happy with the teaching method', 'scale', 5),
(15, NULL, 'Anything you want to change?', 'text', 5),
(16, NULL, 'the material for the course is helpful', 'scale', 6),
(17, NULL, 'Anything you want to change?', 'text', 6),
(18, NULL, 'the material for the course is helpful', 'scale', 7),
(19, NULL, 'Anything you want to change?', 'text', 7);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_responses`
--

DROP TABLE IF EXISTS `feedback_responses`;
CREATE TABLE `feedback_responses` (
  `response_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `responded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `viewed_by_student` tinyint(1) DEFAULT 0,
  `is_anoymous` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_responses`
--

INSERT INTO `feedback_responses` (`response_id`, `feedback_id`, `lecturer_id`, `response_text`, `responded_at`, `viewed_by_student`, `is_anoymous`) VALUES
(6, 16, 6, 'your welcome', '2025-07-07 20:09:06', 0, 0),
(7, 16, 6, 'your welcome', '2025-07-07 20:10:38', 0, 0),
(8, 16, 6, 'THANKS', '2025-07-07 20:10:49', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_templates`
--

DROP TABLE IF EXISTS `feedback_templates`;
CREATE TABLE `feedback_templates` (
  `template_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_responses`
--

DROP TABLE IF EXISTS `form_responses`;
CREATE TABLE `form_responses` (
  `response_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `unit_id` int(11) NOT NULL,
  `sentiment` varchar(255) NOT NULL,
  `cleaned_text` varchar(255) NOT NULL,
  `confidence_score` float NOT NULL,
  `contains_profanity` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_responses`
--

INSERT INTO `form_responses` (`response_id`, `form_id`, `question_id`, `student_id`, `response_text`, `submitted_at`, `unit_id`, `sentiment`, `cleaned_text`, `confidence_score`, `contains_profanity`) VALUES
(1, 3, 4, 31, '4', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(2, 3, 5, 31, '5', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(3, 3, 6, 31, '4', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(4, 3, 7, 31, 'no', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(5, 3, 8, 31, '5', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(6, 3, 9, 31, 'none', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(7, 3, 10, 31, 'none', '2025-07-13 14:46:24', 3, '', '', 0, 0),
(8, 5, 11, 31, '5', '2025-07-13 15:17:44', 2, '', '', 0, 0),
(9, 3, 4, 31, '5', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(10, 3, 5, 31, '4', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(11, 3, 6, 31, '5', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(12, 3, 7, 31, 'no', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(13, 3, 8, 31, '4', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(14, 3, 9, 31, 'no', '2025-07-13 15:51:48', 2, '', '', 0, 0),
(15, 3, 10, 31, 'no', '2025-07-13 15:51:48', 2, '', '', 0, 0);

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
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `profile_completed` tinyint(4) NOT NULL DEFAULT 0,
  `unit_taught` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `first_name`, `last_name`, `email`, `faculty_name`, `course_taught`, `password`, `faculty_id`, `profile_photo`, `verification_status`, `profile_completed`, `unit_taught`) VALUES
(1, 17, '', '', '', 'SCES', 'BBIT, ICS, BCOM', '', NULL, NULL, 'approved', 1, ''),
(2, 24, '', '', '', '', 'BCOM, BFS, BBIT, ICS', '', NULL, NULL, 'approved', 1, ''),
(3, 23, '', '', '', '', 'SLS', '', NULL, NULL, 'approved', 1, ''),
(4, 22, '', '', '', '', 'BBSAS, BBSFS, BBIT', '', NULL, NULL, 'approved', 1, ''),
(5, 20, '', '', '', NULL, 'BCOM, BHM, BSc.SDS', '', NULL, NULL, 'approved', 1, ''),
(6, 30, '', '', '', '', 'BCOM, BBIT, BICS', '', NULL, NULL, 'approved', 1, ''),
(7, 29, '', '', '', '', 'BBIT, BCOM', '', NULL, NULL, 'approved', 1, ''),
(8, 33, '', '', '', NULL, NULL, '', NULL, NULL, 'approved', 1, ''),
(10, 35, '', '', '', '', '', '', NULL, NULL, 'approved', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_courses`
--

DROP TABLE IF EXISTS `lecturer_courses`;
CREATE TABLE `lecturer_courses` (
  `lecturer_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_courses`
--

INSERT INTO `lecturer_courses` (`lecturer_id`, `course_id`) VALUES
(8, 1),
(8, 2),
(10, 1),
(10, 2);

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
(1, 1, 1),
(2, 2, 1),
(3, 2, 2),
(4, 3, 3),
(5, 4, 1),
(6, 4, 4),
(7, 5, 2),
(8, 5, 5),
(9, 5, 6),
(10, 6, 1),
(11, 6, 2),
(12, 7, 1),
(13, 7, 2),
(14, 8, 1),
(15, 8, 2),
(16, 10, 1),
(17, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_feedback_forms`
--

DROP TABLE IF EXISTS `lecturer_feedback_forms`;
CREATE TABLE `lecturer_feedback_forms` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `assigned_course_id` int(11) NOT NULL,
  `assigned_unit_id` int(11) NOT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_feedback_forms`
--

INSERT INTO `lecturer_feedback_forms` (`id`, `form_id`, `lecturer_id`, `assigned_course_id`, `assigned_unit_id`, `is_published`, `created_at`) VALUES
(1, 1, 8, 2, 2, 0, '2025-07-12 17:54:33'),
(2, 2, 8, 2, 2, 0, '2025-07-12 18:06:12'),
(3, 2, 8, 2, 1, 0, '2025-07-12 18:41:11'),
(4, 3, 8, 2, 2, 0, '2025-07-13 00:13:28'),
(5, 3, 8, 2, 2, 0, '2025-07-13 00:17:39'),
(6, 3, 8, 2, 2, 1, '2025-07-13 00:21:27'),
(7, 3, 10, 2, 3, 1, '2025-07-13 13:56:53'),
(8, 3, 8, 2, 2, 1, '2025-07-13 14:07:07'),
(9, 3, 8, 2, 2, 1, '2025-07-13 14:17:59'),
(10, 5, 8, 2, 2, 1, '2025-07-13 15:00:33'),
(11, 3, 8, 2, 2, 1, '2025-07-13 15:51:13'),
(12, 7, 8, 2, 2, 1, '2025-07-13 17:41:11'),
(13, 7, 8, 2, 2, 1, '2025-07-13 17:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_form_questions`
--

DROP TABLE IF EXISTS `lecturer_form_questions`;
CREATE TABLE `lecturer_form_questions` (
  `question_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('scale','text') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_form_questions`
--

INSERT INTO `lecturer_form_questions` (`question_id`, `form_id`, `lecturer_id`, `question_text`, `question_type`, `created_at`) VALUES
(1, 1, 8, 'is the material offered for the course helpful', 'scale', '2025-07-12 17:54:33'),
(2, 2, 8, 'point of improvement', 'text', '2025-07-12 18:06:12'),
(3, 2, 8, 'point of improvement', 'text', '2025-07-12 18:41:11'),
(4, 3, 8, 'is the material offered for the course helpful', 'scale', '2025-07-13 00:13:27'),
(5, 3, 8, 'any point of change you wish to suggest', 'text', '2025-07-13 00:13:28'),
(6, 3, 8, 'is the material offered for the course helpful', 'scale', '2025-07-13 00:17:39'),
(7, 3, 8, 'any point of change you wish to suggest', 'text', '2025-07-13 00:17:39'),
(8, 3, 8, 'is the material offered for the course helpful', 'scale', '2025-07-13 00:21:27'),
(9, 3, 8, 'any point of change you wish to suggest', 'text', '2025-07-13 00:21:27'),
(10, 3, 8, 'are you happy', 'text', '2025-07-13 14:17:59'),
(11, 5, 8, 'the lecturer is nice', 'scale', '2025-07-13 15:00:33'),
(12, 7, 8, 'are you happy', 'text', '2025-07-13 17:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_units`
--

DROP TABLE IF EXISTS `lecturer_units`;
CREATE TABLE `lecturer_units` (
  `lecturer_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_units`
--

INSERT INTO `lecturer_units` (`lecturer_id`, `unit_id`) VALUES
(8, 1),
(8, 2),
(10, 1),
(10, 3);

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
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `unit_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `first_name`, `last_name`, `email`, `faculty_name`, `student_course`, `year_of_study`, `password`, `faculty_id`, `profile_photo`, `status`, `unit_id`, `course_id`) VALUES
(8, 31, '', '', '', NULL, 'BCOM', 2, '', 1, NULL, 'approved', NULL, 2),
(9, 4, '', '', '', NULL, '', 2, '', 1, NULL, 'pending', NULL, 2),
(10, 34, '', '', '', NULL, '', 1, '', 1, NULL, 'pending', NULL, 2),
(11, 6, '', '', '', NULL, '', 1, '', 1, NULL, 'pending', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

DROP TABLE IF EXISTS `student_courses`;
CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_courses`
--

INSERT INTO `student_courses` (`id`, `student_id`, `course_id`) VALUES
(1, 8, 1),
(2, 8, 1),
(3, 8, 2),
(4, 8, 2),
(5, 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_units`
--

DROP TABLE IF EXISTS `student_units`;
CREATE TABLE `student_units` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_units`
--

INSERT INTO `student_units` (`id`, `student_id`, `unit_id`) VALUES
(5, 8, 2),
(6, 8, 3);

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
  `year_of_study` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submitted_feedback`
--

DROP TABLE IF EXISTS `submitted_feedback`;
CREATE TABLE `submitted_feedback` (
  `id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submitted_feedback`
--

INSERT INTO `submitted_feedback` (`id`, `form_id`, `student_id`, `submitted_at`, `unit_id`) VALUES
(11, 3, 31, '2025-07-13 14:46:24', 3),
(12, 5, 31, '2025-07-13 15:17:44', 2),
(13, 3, 31, '2025-07-13 15:51:48', 2);

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
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `year_of_study` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `course_id`, `year_of_study`) VALUES
(1, 'Introduction to Accounting', 1, 1),
(2, 'AOPP', 2, 2),
(3, 'Web Development', 2, 2);

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
(1, 'Jane ', 'Doe', 'example@gmail.com', '$2y$10$xg5m3r8J9IQHGVprvp66Nu4E7UAq1/m8Vs.WBLcHBjoisUsILYwXO', 1, 'approved'),
(2, 'Amina', 'Hassan', 'example2@gmail.com', '$2y$10$V5kWz39CZB6AaN90r1vtaeKUpe5blEcu04PUN2sCXa5Y/jcqDYEPC', 2, 'approved'),
(3, 'Mellisa', 'James', 'mlissa@gmail.com', '$2y$10$wO71VcVcEpZA3SpTi3FoOuqgDfyzRn2EAlH1oAt0GtVjGVcgSNdLG', 1, 'approved'),
(4, 'example', 'three', 'example3@gmail.com', '$2y$10$0pn.0AwxfAwUeGX/0VRE6.eIELDkoWfZejhB.YkvlOiystKb4DhxS', 1, 'approved'),
(5, 'ejany', 'jane', 'ej@gmail.com', '$2y$10$ibwvSP0i2V5SWsOwMX7.tuIaD4CwJtU8XeL5zdz9LfwOmXchCPbUa', 3, 'rejected'),
(6, 'try', 'one', 'try@gmail.com', '$2y$10$G59pL/PJGgNBGuyk6SKnqeXfKKwaEIGz5RKuz7zwQaeZwGaBmFT0i', 1, 'approved'),
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
(19, 'student', 'one', 'student@gmail.com', '$2y$10$wJxj5qOmE9o8r0R9HTRdcuZJBfsgE8VhahcpskZGgwP4ggXfM2AES', 1, 'approved'),
(20, 'David ', 'Wamoyo', 'wamoyo@strathmore.edu', '$2y$10$yQ5YXONuxtuwSy9XQYQGKuU0zHNZ13hj31wD71RtE/nVAu.lIY5mS', 2, 'approved'),
(21, 'Janice', 'Seko', 'seko@gmail.com', '$2y$10$T9iuZIOHnB7GP4.GIvbZiupNKQluOVzAPWaqxQ5KZc3tzIXdR0YdO', 2, 'approved'),
(22, 'Albert', 'Ojwang', 'ojwang@gmail.com', '$2y$10$OWdp/OE.9vyXgsbWHlq4H.fWEr7t9lZJavo7yV1Oyuzb/3UxG0sFW', 2, 'approved'),
(23, 'Mike', 'Muchiri', 'muchiri@gmail.com', '$2y$10$nckuC90XQu39oc6jPsLv/uZ9nyWA2U.Jil1hYhBgsigbGTHHdQp/q', 2, 'approved'),
(24, 'Mitchelle', 'Mali', 'mali@gmail.com', '$2y$10$rEWyv9pSVrU1sNX0dTZZ0uEshrGXKCe.ukIFVzpRxLfHkN4pzExbS', 2, 'approved'),
(25, 'Olekera', 'Oluol', 'oluol@gmail.com', '$2y$10$IkpSXpgjoqKegH37KFvQV.AyBpmmVpIGSRRpLeZOYKkGpJ6W.RkK.', 3, 'approved'),
(26, 'Maya', 'Jama', 'jama@sgmail.com', '$2y$10$kcOYYcgiSjBV5fzFMJRmg.2QM2OLTfCBLiUgF17E22L1./1HRv/EG', 3, 'approved'),
(27, 'Harry', 'James', 'james@strathmore.edu', '$2y$10$FDnZiunO5frSmmdBUfw/gOMRd2fMq7y5TKfudfTzoQJ7RssbpQ30a', 3, 'approved'),
(28, 'Karanja', 'George', 'george@gmail.com', '$2y$10$4GZBch.4.MgVW8Cs6XnrbOvMYJyTDItXBiHPrWLUEgijVThoNb7.O', 3, 'approved'),
(29, 'Mukami', 'Muthoni', 'muthoni@gmail.com', '$2y$10$SW7aZVl/.qSr85AH6RtZzuDUnwI0tnSevCgPVknV35oELMIYC3Cwm', 2, 'approved'),
(30, 'Shanice', 'Yappa', 'yappa@gmail.com', '$2y$10$wyCasru/exhrxwdfOGOM4u5uY354KTnoEd7o.rerjcxrphIM9DY.u', 2, 'approved'),
(31, 'Samiya', 'Abdullahi', 'sami@gmail.com', '$2y$10$OArrShXLuczZu3yAgXeNQOxB2l3ebWI6e.hu/zwmFnTK9S2SiqSj2', 1, 'approved'),
(32, 'hayat', 'ali', 'hayat@gmail.com', '$2y$10$/YZeQHHRie72XCeG.UT27e0EpT5zCXNhrihXFQxTJvzeSajqU3aK.', 3, 'approved'),
(33, 'Saidi', 'Atole', 'atole@gmail.com', '$2y$10$TnRfOeY5VVuyjZuJ9OO6GO/wa72qLLu5cigwL9UbzabETIbFDe5ji', 2, 'approved'),
(34, 'Jok', 'Ajok', 'ajok@gmail.com', '$2y$10$Rqwf5AmUT7BhLV9GKcSY3epF5QZF7.0J3zwlA1ND2pZisM7Y.z/Oy', 1, 'approved'),
(35, 'lec', 'two', 'lec2@gmail.com', '$2y$10$GRUoPbIM7Abc5kEztxAqwOhnLzoYsmdTfYoJjLg0eKg545Y.8hEi6', 2, 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `all_questions`
--
ALTER TABLE `all_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `form_id` (`form_id`);

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
  ADD KEY `courseadmin_ibfk_1` (`faculty_id`),
  ADD KEY `fk_courseadministrator_user` (`user_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_feedback_user` (`user_id`),
  ADD KEY `fk_feedback_lecturer` (`lecturer_id`);

--
-- Indexes for table `feedback_archive`
--
ALTER TABLE `feedback_archive`
  ADD PRIMARY KEY (`archive_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `fk_feedback_id` (`feedback_id`);

--
-- Indexes for table `feedback_forms`
--
ALTER TABLE `feedback_forms`
  ADD PRIMARY KEY (`form_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `feedback_questions`
--
ALTER TABLE `feedback_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `FK_form_id` (`form_id`);

--
-- Indexes for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `fk_response_feedback` (`feedback_id`),
  ADD KEY `fk_response_lecturer` (`lecturer_id`);

--
-- Indexes for table `feedback_templates`
--
ALTER TABLE `feedback_templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_all_questions` (`question_id`),
  ADD KEY `fk_formresponses_unit` (`unit_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`),
  ADD KEY `fk_lecturers_faculty` (`faculty_id`);

--
-- Indexes for table `lecturer_courses`
--
ALTER TABLE `lecturer_courses`
  ADD PRIMARY KEY (`lecturer_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  ADD PRIMARY KEY (`lecturer_faculty_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `fk_faculty` (`faculty_id`);

--
-- Indexes for table `lecturer_feedback_forms`
--
ALTER TABLE `lecturer_feedback_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `assigned_course_id` (`assigned_course_id`),
  ADD KEY `assigned_unit_id` (`assigned_unit_id`);

--
-- Indexes for table `lecturer_form_questions`
--
ALTER TABLE `lecturer_form_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `lecturer_units`
--
ALTER TABLE `lecturer_units`
  ADD PRIMARY KEY (`lecturer_id`,`unit_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `fk_students_faculty` (`faculty_id`),
  ADD KEY `fk_unit_id` (`unit_id`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_course_id` (`course_id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `student_units`
--
ALTER TABLE `student_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `student_updates`
--
ALTER TABLE `student_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_student_updates_course` (`course_id`);

--
-- Indexes for table `submitted_feedback`
--
ALTER TABLE `submitted_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `systemadmin`
--
ALTER TABLE `systemadmin`
  ADD PRIMARY KEY (`system_admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `course_id` (`course_id`);

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
-- AUTO_INCREMENT for table `all_questions`
--
ALTER TABLE `all_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courseadmin`
--
ALTER TABLE `courseadmin`
  MODIFY `course_admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `feedback_archive`
--
ALTER TABLE `feedback_archive`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `feedback_forms`
--
ALTER TABLE `feedback_forms`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedback_questions`
--
ALTER TABLE `feedback_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `feedback_templates`
--
ALTER TABLE `feedback_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_responses`
--
ALTER TABLE `form_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  MODIFY `lecturer_faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lecturer_feedback_forms`
--
ALTER TABLE `lecturer_feedback_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lecturer_form_questions`
--
ALTER TABLE `lecturer_form_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_units`
--
ALTER TABLE `student_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_updates`
--
ALTER TABLE `student_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `submitted_feedback`
--
ALTER TABLE `submitted_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `systemadmin`
--
ALTER TABLE `systemadmin`
  MODIFY `system_admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `all_questions`
--
ALTER TABLE `all_questions`
  ADD CONSTRAINT `all_questions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `feedback_forms` (`form_id`) ON DELETE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `courseadmin`
--
ALTER TABLE `courseadmin`
  ADD CONSTRAINT `courseadmin_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `fk_courseadmin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_courseadministrator_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback_archive`
--
ALTER TABLE `feedback_archive`
  ADD CONSTRAINT `feedback_archive_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_id` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_forms`
--
ALTER TABLE `feedback_forms`
  ADD CONSTRAINT `feedback_forms_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `feedback_forms_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `feedback_questions`
--
ALTER TABLE `feedback_questions`
  ADD CONSTRAINT `FK_form_id` FOREIGN KEY (`form_id`) REFERENCES `feedback_forms` (`form_id`),
  ADD CONSTRAINT `feedback_questions_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `feedback_templates` (`template_id`);

--
-- Constraints for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  ADD CONSTRAINT `fk_response_feedback` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_response_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE;

--
-- Constraints for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD CONSTRAINT `fk_all_questions` FOREIGN KEY (`question_id`) REFERENCES `all_questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_formresponses_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`),
  ADD CONSTRAINT `form_responses_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `feedback_forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `form_responses_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `fk_lecturers_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lecturer_courses`
--
ALTER TABLE `lecturer_courses`
  ADD CONSTRAINT `lecturer_courses_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `lecturer_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `lecturer_faculties`
--
ALTER TABLE `lecturer_faculties`
  ADD CONSTRAINT `fk_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lecturer_faculties_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`);

--
-- Constraints for table `lecturer_feedback_forms`
--
ALTER TABLE `lecturer_feedback_forms`
  ADD CONSTRAINT `lecturer_feedback_forms_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `feedback_forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lecturer_feedback_forms_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lecturer_feedback_forms_ibfk_3` FOREIGN KEY (`assigned_course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lecturer_feedback_forms_ibfk_4` FOREIGN KEY (`assigned_unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_form_questions`
--
ALTER TABLE `lecturer_form_questions`
  ADD CONSTRAINT `lecturer_form_questions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `feedback_forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lecturer_form_questions_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_units`
--
ALTER TABLE `lecturer_units`
  ADD CONSTRAINT `lecturer_units_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `lecturer_units_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  ADD CONSTRAINT `fk_students_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`),
  ADD CONSTRAINT `fk_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_units`
--
ALTER TABLE `student_units`
  ADD CONSTRAINT `student_units_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_units_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_updates`
--
ALTER TABLE `student_updates`
  ADD CONSTRAINT `fk_student_updates_course` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  ADD CONSTRAINT `student_updates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `submitted_feedback`
--
ALTER TABLE `submitted_feedback`
  ADD CONSTRAINT `submitted_feedback_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `lecturer_feedback_forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submitted_feedback_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submitted_feedback_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

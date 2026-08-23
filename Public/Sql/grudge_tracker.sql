-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2026 at 01:05 PM
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
-- Database: `grudge_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(10) DEFAULT '?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `code`, `title`, `description`, `icon`) VALUES
(1, 'first_blood', 'First Blood', 'Logged your first grudge', '🏆'),
(2, 'day_in_court', 'Day in Court', 'Filed your first dispute', '⚖️'),
(3, 'on_a_streak', 'On a Streak', '3-day logging streak', '🔥'),
(4, 'let_it_go', 'Let It Go', 'Resolved 10 grudges', '🕊️'),
(5, 'petty_royalty', 'Petty Royalty', 'Reached Level 40', '👑'),
(6, 'trusted', 'Trusted', 'Reached 80 trust score', '🛡️');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `grudge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` int(11) NOT NULL,
  `grudge_id` int(11) NOT NULL,
  `filed_by` int(11) NOT NULL,
  `status` enum('In Session','Ruled','Dismissed') DEFAULT 'In Session',
  `verdict` enum('Guilty','Innocent','Pending') DEFAULT 'Pending',
  `verdict_reasoning` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disputes`
--

INSERT INTO `disputes` (`id`, `grudge_id`, `filed_by`, `status`, `verdict`, `verdict_reasoning`, `created_at`, `resolved_at`) VALUES
(1, 2, 1, 'In Session', 'Pending', NULL, '2026-08-20 10:44:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dispute_claims`
--

CREATE TABLE `dispute_claims` (
  `id` int(11) NOT NULL,
  `dispute_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `side` enum('Prosecution','Defense') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispute_claims`
--

INSERT INTO `dispute_claims` (`id`, `dispute_id`, `submitted_by`, `side`, `content`, `created_at`) VALUES
(1, 1, 1, 'Prosecution', 'i want an apology and it seems the only way im going to get it is if i take this to court', '2026-08-20 10:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `dispute_jurors`
--

CREATE TABLE `dispute_jurors` (
  `id` int(11) NOT NULL,
  `dispute_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invited_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispute_jurors`
--

INSERT INTO `dispute_jurors` (`id`, `dispute_id`, `user_id`, `invited_at`) VALUES
(1, 1, 2, '2026-08-20 10:44:30'),
(2, 1, 3, '2026-08-20 10:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `grudges`
--

CREATE TABLE `grudges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `person_involved` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `severity` enum('Low','Medium','High','Critical') NOT NULL,
  `status` enum('Active','In Progress','Resolved','Archived') DEFAULT 'Active',
  `emoji` varchar(10) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_occurred` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grudges`
--

INSERT INTO `grudges` (`id`, `user_id`, `title`, `person_involved`, `category`, `severity`, `status`, `emoji`, `notes`, `date_occurred`, `created_at`, `updated_at`) VALUES
(1, 1, 'Zoe said she would handle something important, then completely forgot about it. When I brought it up, she acted like it wasn\'t a big deal and somehow I ended up being the one overreacting.', 'Zoe', 'Partner', 'Medium', 'Active', '😡', 'This isn\'t necessarily friendship-ending, but it definitely needs to be addressed. The annoying part isn\'t just that she forgot — it\'s that she didn\'t seem to care afterward.', '2026-08-06', '2026-08-18 20:33:07', '2026-08-18 20:33:07'),
(2, 1, 'Maya told me she couldn\'t come to my birthday because she was “too tired” and had an early morning the next day.\r\n\r\nCool. Whatever.\r\n\r\nThen I opened Instagram later that night and saw she was out at a party with people she literally met two weeks ago.\r\n\r\n', 'Maya', 'Friend', 'High', 'Active', '😤', 'The issue isn\'t even that she went out.\r\n\r\nIt\'s that she lied, publicly rubbed salt in the wound, and then acted confused about why I was upset.', '2026-12-08', '2026-08-20 07:57:50', '2026-08-20 07:57:50'),
(3, 1, 'Liam asked me to help him with an assignment because he said he was completely stuck.\r\n\r\nI spent three hours helping him — explaining everything, fixing his mistakes, and even sending him examples.\r\n\r\nThe next day, I found out he had submitted the assignm', 'Liam', 'Friend', 'High', 'Active', '😤', 'I\'m not angry that I helped him.\r\n\r\nI\'m angry because he used my help, took the credit, and then acted like I was weird for expecting basic acknowledgment.\r\n\r\nThis isn\'t about the assignment anymore.\r\n\r\nIt\'s about the principle.', '2026-02-05', '2026-08-20 08:23:49', '2026-08-20 08:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `grudge_evidence`
--

CREATE TABLE `grudge_evidence` (
  `id` int(11) NOT NULL,
  `grudge_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grudge_evidence`
--

INSERT INTO `grudge_evidence` (`id`, `grudge_id`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/evidence/1787085187_ChatGPT Image Aug 11, 2026, 09_05_33 PM.png', '2026-08-18 20:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `jury_votes`
--

CREATE TABLE `jury_votes` (
  `id` int(11) NOT NULL,
  `dispute_id` int(11) NOT NULL,
  `juror_id` int(11) NOT NULL,
  `vote` enum('Guilty','Innocent') NOT NULL,
  `reasoning` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `grudge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `undo_log`
--

CREATE TABLE `undo_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` enum('delete_grudge','delete_comment','cast_vote','file_dispute') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `previous_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`previous_state`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `restored` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar_emoji` varchar(10) DEFAULT '?',
  `trust_score` int(11) DEFAULT 50,
  `xp` int(11) DEFAULT 0,
  `level` int(11) DEFAULT 1,
  `streak_count` int(11) DEFAULT 0,
  `last_active_date` date DEFAULT NULL,
  `undo_used` tinyint(1) DEFAULT 0,
  `undo_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `avatar_emoji`, `trust_score`, `xp`, `level`, `streak_count`, `last_active_date`, `undo_used`, `undo_used_at`, `created_at`) VALUES
(1, 'shedxntluv.nk', 'NKLovesMaps@gmail.com', '$2y$10$ZVtViKFAH39mAaRNbqRlP.qOVkEhfPbL.xCFyX64cn6dybrXiiJna', '👑', 50, 100, 1, 0, NULL, 0, NULL, '2026-08-18 20:00:59'),
(2, 'dat.boi_nk', 'boi.NK@gmail.com', '$2y$10$YzXoLcZVNV7lQxx9Hl8eC./L/wIXG0PX0qBgU3KUx.8E5ORONOeAS', '👑', 50, 0, 1, 0, NULL, 0, NULL, '2026-08-18 20:24:06'),
(3, 'EnzoSQL', 'enzo.csl@gmail.com', '$2y$10$FLOg80LwWotoirOZShjOGuqYCUQfvmslrCX1jNG4AGoXWsR.ZJ0BO', '👑', 50, 0, 1, 0, NULL, 0, NULL, '2026-08-20 08:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`id`, `user_id`, `achievement_id`, `unlocked_at`) VALUES
(1, 1, 1, '2026-08-20 08:23:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grudge_id` (`grudge_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grudge_id` (`grudge_id`),
  ADD KEY `filed_by` (`filed_by`);

--
-- Indexes for table `dispute_claims`
--
ALTER TABLE `dispute_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispute_id` (`dispute_id`),
  ADD KEY `submitted_by` (`submitted_by`);

--
-- Indexes for table `dispute_jurors`
--
ALTER TABLE `dispute_jurors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_invite` (`dispute_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grudges`
--
ALTER TABLE `grudges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grudge_evidence`
--
ALTER TABLE `grudge_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grudge_id` (`grudge_id`);

--
-- Indexes for table `jury_votes`
--
ALTER TABLE `jury_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`dispute_id`,`juror_id`),
  ADD KEY `juror_id` (`juror_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`grudge_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `undo_log`
--
ALTER TABLE `undo_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_achievement` (`user_id`,`achievement_id`),
  ADD KEY `achievement_id` (`achievement_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dispute_claims`
--
ALTER TABLE `dispute_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dispute_jurors`
--
ALTER TABLE `dispute_jurors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grudges`
--
ALTER TABLE `grudges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grudge_evidence`
--
ALTER TABLE `grudge_evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jury_votes`
--
ALTER TABLE `jury_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `undo_log`
--
ALTER TABLE `undo_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`grudge_id`) REFERENCES `grudges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_ibfk_1` FOREIGN KEY (`grudge_id`) REFERENCES `grudges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disputes_ibfk_2` FOREIGN KEY (`filed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dispute_claims`
--
ALTER TABLE `dispute_claims`
  ADD CONSTRAINT `dispute_claims_ibfk_1` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispute_claims_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dispute_jurors`
--
ALTER TABLE `dispute_jurors`
  ADD CONSTRAINT `dispute_jurors_ibfk_1` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispute_jurors_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grudges`
--
ALTER TABLE `grudges`
  ADD CONSTRAINT `grudges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grudge_evidence`
--
ALTER TABLE `grudge_evidence`
  ADD CONSTRAINT `grudge_evidence_ibfk_1` FOREIGN KEY (`grudge_id`) REFERENCES `grudges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jury_votes`
--
ALTER TABLE `jury_votes`
  ADD CONSTRAINT `jury_votes_ibfk_1` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_votes_ibfk_2` FOREIGN KEY (`juror_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`grudge_id`) REFERENCES `grudges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `undo_log`
--
ALTER TABLE `undo_log`
  ADD CONSTRAINT `undo_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_achievements_ibfk_2` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

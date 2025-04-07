-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2025 at 12:07 AM
-- Server version: 8.0.36
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_allocator_tap`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` varchar(10) NOT NULL,
  `project_title` varchar(100) NOT NULL,
  `project_description` text NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total_budget` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `documents_title` varchar(100) DEFAULT NULL,
  `supporting_documents` json DEFAULT NULL,
  `team_leader_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_title`, `project_description`, `customer_name`, `total_budget`, `start_date`, `end_date`, `documents_title`, `supporting_documents`, `team_leader_id`) VALUES
('PROJ-00001', 'Website Development', 'Developing a responsive e-commerce website for a client.', 'John Doe', 15000.00, '2025-01-15', '2025-02-28', 'Technical Specifications', '[\"file1.pdf\", \"file2.png\"]', NULL),
('PROJ-00002', 'Mobile App Development', 'Developing a mobile application for a food delivery service.', 'Jane Smith', 20000.00, '2025-03-01', '2025-05-30', 'App Specifications', '[\"app_design.pdf\", \"app_features.doc\"]', NULL),
('PROJ-00003', 'ERP System Implementation', 'Implementing an ERP system for a manufacturing company.', 'Robert Brown', 50000.00, '2025-04-01', '2025-09-01', 'ERP Documentation', '[\"erp_technical.pdf\", \"erp_budget.xlsx\"]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_files`
--

CREATE TABLE `project_files` (
  `file_id` int NOT NULL,
  `project_id` varchar(10) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project_files`
--

INSERT INTO `project_files` (`file_id`, `project_id`, `file_path`, `file_title`) VALUES
(1, 'PROJ-00001', 'uploads/file1.pdf', 'Specifications Document'),
(2, 'PROJ-00001', 'uploads/file2.png', 'Design Mockup');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int NOT NULL,
  `task_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `project_id` varchar(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `effort` int NOT NULL,
  `status` enum('Pending','In Progress','Completed') DEFAULT 'Pending',
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `progress_percentage` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `task_name`, `description`, `project_id`, `start_date`, `end_date`, `effort`, `status`, `priority`, `progress_percentage`) VALUES
(1, 'Task 1', 'Developing the frontend interface.', 'PROJ-00001', '2025-01-22', '2025-01-31', 100, 'In Progress', 'Medium', 0),
(2, 'Task 2', 'Backend API Development.', 'PROJ-00002', '2025-03-15', '2025-04-15', 80, 'Pending', 'High', 0),
(3, 'Task 3', 'Database Optimization.', 'PROJ-00003', '2025-04-10', '2025-05-10', 100, 'Pending', 'Medium', 0);

-- --------------------------------------------------------

--
-- Table structure for table `task_team_members`
--

CREATE TABLE `task_team_members` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `role` enum('Developer','Designer','Tester','Analyst','Support') NOT NULL,
  `contribution_percentage` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `task_team_members`
--

INSERT INTO `task_team_members` (`id`, `task_id`, `user_id`, `role`, `contribution_percentage`, `start_date`) VALUES
(1, 1, '4444523690', 'Developer', 50.00, '2025-01-22'),
(2, 1, '9999523690', 'Designer', 50.00, '2025-01-23'),
(3, 2, '0003334442', 'Developer', 60.00, '2025-03-15'),
(4, 2, '0004445553', 'Designer', 40.00, '2025-03-16'),
(5, 3, '0005556664', 'Tester', 50.00, '2025-04-10'),
(6, 3, '0006667775', 'Analyst', 50.00, '2025-04-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `dob` date NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `role` varchar(50) NOT NULL,
  `qualification` varchar(255) NOT NULL,
  `skills` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `photo` varchar(255) DEFAULT 'images.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `dob`, `id_number`, `email`, `telephone`, `role`, `qualification`, `skills`, `username`, `password`, `user_id`, `photo`) VALUES
(1, 'hanan naji', '123 Main St', '1990-01-01', '1234567890', 'hanannaji@gmail.com', '123456789', 'Manager', 'MBA', 'Leadership, Planning', 'hanannaji', '6699', '1234567890', 'images.jpg'),
(2, 'aliali', '124 Main St', '2005-01-01', '1274856785690', 'aliali@gmail.com', '710456789', 'Project Leader', 'MBA', 'Leadership, Planning', 'aliali', 'ali00000', '9999523690', 'images.jpg'),
(3, 'ihsan', '124 Main St', '2000-01-01', '4444856785690', 'ihsan@gmail.com', '444456789', 'Team Member', 'MBA', 'Leadership, Planning', 'ihsan', '0000', '4444523690', 'images.jpg'),
(4, 'eman naji', '124 Main St', '2004-01-01', '123456785690', 'emannaji@gmail.com', '852456789', 'Manager', 'MBA', 'Leadership, Planning', 'emannaji', '2233', '4448523690', 'images.jpg'),
(5, 'Omar Khalid', '123 West Ave', '1985-04-10', '1239876543', 'omar.khalid@gmail.com', '0569871234', 'Manager', 'MBA', 'Leadership, Planning', 'omarkhalid', 'omar2025', '0001112220', 'images.jpg'),
(6, 'Lina Yusuf', '456 Green Rd', '1990-08-22', '9876543219', 'lina.yusuf@gmail.com', '0543219876', 'Project Leader', 'MSc Project Management', 'Team Coordination, Risk Management', 'linayusuf', 'lina2025', '0002223331', 'images.jpg'),
(7, 'Ali Hassan', '789 Red Blvd', '1995-06-15', '1123581321', 'ali.hassan@gmail.com', '0537896541', 'Team Member', 'BSc Computer Science', 'Frontend Development', 'alihassan', 'ali2025', '0003334442', 'images.jpg'),
(8, 'Sara Ahmed', '159 Blue St', '1997-03-12', '2233445566', 'sara.ahmed@gmail.com', '0561122334', 'Team Member', 'BSc Graphic Design', 'UI/UX Design, Photoshop', 'saraahmed', 'sara2025', '0004445553', 'images.jpg'),
(9, 'Nour Ali', '753 Yellow Ave', '1999-11-05', '7788996655', 'nour.ali@gmail.com', '0523345567', 'Team Member', 'BSc Software Engineering', 'Testing, Debugging', 'nourali', 'nour2025', '0005556664', 'images.jpg'),
(10, 'Yasmeen Naji', '951 Purple Rd', '1992-02-18', '9988776655', 'yasmeen.naji@gmail.com', '0519988776', 'Team Member', 'MSc Data Analysis', 'Data Analysis, Reporting', 'yasmeennaji', 'yasmeen2025', '0006667775', 'images.jpg'),
(11, 'Khaled Sami', '321 Orange St', '1990-10-25', '6677889900', 'khaled.sami@gmail.com', '0506677889', 'Team Member', 'BSc IT Support', 'Technical Support, Networking', 'khaledsami', 'khaled2025', '0007778886', 'images.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_files`
--
ALTER TABLE `project_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `task_team_members`
--
ALTER TABLE `task_team_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_files`
--
ALTER TABLE `project_files`
  MODIFY `file_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `task_team_members`
--
ALTER TABLE `task_team_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_files`
--
ALTER TABLE `project_files`
  ADD CONSTRAINT `project_files_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `task_team_members`
--
ALTER TABLE `task_team_members`
  ADD CONSTRAINT `task_team_members_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

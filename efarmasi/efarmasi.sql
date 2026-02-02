-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 29, 2026 at 07:17 AM
-- Server version: 8.4.3
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmedice`
--

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `main_symptom` varchar(255) DEFAULT NULL,
  `additional_symptoms` text,
  `answers` json DEFAULT NULL,
  `recommendation` text,
  `status` enum('pending','completed') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_logs`
--

CREATE TABLE `consultation_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `symptoms_data` json DEFAULT NULL,
  `applied_rules` json DEFAULT NULL,
  `final_recommendation` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forward_chaining_rules`
--

CREATE TABLE `forward_chaining_rules` (
  `id` int NOT NULL,
  `rule_name` varchar(255) DEFAULT NULL,
  `conditions` json DEFAULT NULL,
  `conclusions` json DEFAULT NULL,
  `confidence_level` decimal(3,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forward_chaining_rules`
--

INSERT INTO `forward_chaining_rules` (`id`, `rule_name`, `conditions`, `conclusions`, `confidence_level`, `is_active`, `created_at`) VALUES
(1, 'Demam Ringan', '{\"duration\": \"<3\", \"symptoms\": [\"Demam\"], \"temperature\": \"<38.5\"}', '{\"dosage\": \"1 tablet setiap 4-6 jam\", \"warning\": \"Jika demam >3 hari, konsultasi dokter\", \"medicine\": \"Paracetamol 500mg\", \"max_daily\": \"4 tablet\"}', 0.85, 1, '2026-01-29 01:19:14'),
(2, 'Batuk Kering', '{\"type\": \"kering\", \"duration\": \"<7\", \"symptoms\": [\"Batuk\"], \"has_allergy\": false}', '{\"dosage\": \"1 tablet setiap 6-8 jam\", \"warning\": \"Jika batuk disertai demam tinggi, konsultasi dokter\", \"medicine\": \"Dextromethorphan 15mg\", \"max_daily\": \"4 tablet\"}', 0.80, 1, '2026-01-29 01:19:14'),
(3, 'Diare Ringan', '{\"duration\": \"<2\", \"symptoms\": [\"Diare\"], \"frequency\": \"<5\"}', '{\"dosage\": \"2 tablet awal, lalu 1 tablet setelah tiap BAB\", \"warning\": \"Minum banyak air, jika diare berlanjut konsultasi dokter\", \"medicine\": \"Loperamide 2mg\", \"max_daily\": \"8 tablet\"}', 0.75, 1, '2026-01-29 01:19:14'),
(4, 'Alergi Ringan', '{\"severity\": \"ringan\", \"symptoms\": [\"Gatal-gatal\"], \"has_fever\": false}', '{\"dosage\": \"1 tablet setiap 6 jam\", \"warning\": \"Hindari penyebab alergi, jika sesak napas segera ke UGD\", \"medicine\": \"CTM 4mg\", \"max_daily\": \"4 tablet\"}', 0.70, 1, '2026-01-29 01:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `indications` text,
  `contraindications` text,
  `side_effects` text,
  `is_prescription` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `category`, `dosage`, `indications`, `contraindications`, `side_effects`, `is_prescription`) VALUES
(1, 'Paracetamol 500mg', 'Analgesik', '500mg', 'Demam, sakit kepala, nyeri ringan', 'Hipersensitif terhadap paracetamol', 'Reaksi alergi, gangguan hati pada dosis tinggi', 0),
(2, 'Ibuprofen 400mg', 'Anti-inflamasi', '400mg', 'Nyeri, demam, inflamasi', 'Ulkus lambung, gangguan ginjal', 'Gangguan pencernaan, pusing', 0),
(3, 'CTM 4mg', 'Antihistamin', '4mg', 'Alergi, gatal-gatal', 'Glaukoma, hipertrofi prostat', 'Mengantuk, mulut kering', 0),
(4, 'Dextromethorphan 15mg', 'Antitusif', '15mg', 'Batuk kering', 'Batuk berdahak, asma', 'Mual, pusing', 0),
(5, 'Loperamide 2mg', 'Antidiare', '2mg', 'Diare akut', 'Diare dengan demam tinggi, kolitis ulseratif', 'Sembelit, sakit perut', 0);

-- --------------------------------------------------------

--
-- Table structure for table `medicine_recommendations`
--

CREATE TABLE `medicine_recommendations` (
  `id` int NOT NULL,
  `consultation_id` int NOT NULL,
  `medicine_id` int NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_profiles`
--

CREATE TABLE `patient_profiles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_type` enum('A','B','AB','O') DEFAULT NULL,
  `allergies` text,
  `chronic_diseases` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patient_profiles`
--

INSERT INTO `patient_profiles` (`id`, `user_id`, `weight`, `height`, `blood_type`, `allergies`, `chronic_diseases`, `created_at`) VALUES
(1, 3, NULL, NULL, NULL, NULL, NULL, '2026-01-29 01:49:07');

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`id`, `name`, `category`, `description`) VALUES
(1, 'Demam', 'Umum', 'Suhu tubuh di atas 37.5°C'),
(2, 'Batuk', 'Pernapasan', 'Batuk kering atau berdahak'),
(3, 'Pilek', 'Pernapasan', 'Hidung tersumbat atau berair'),
(4, 'Sakit Kepala', 'Neurologi', 'Nyeri di kepala'),
(5, 'Diare', 'Pencernaan', 'Buang air besar encer lebih dari 3 kali sehari'),
(6, 'Mual', 'Pencernaan', 'Perasaan ingin muntah'),
(7, 'Muntah', 'Pencernaan', 'Mengeluarkan isi lambung'),
(8, 'Nyeri Otot', 'Musculoskeletal', 'Nyeri pada otot'),
(9, 'Gatal-gatal', 'Kulit', 'Rasa gatal pada kulit'),
(10, 'Sesak Napas', 'Pernapasan', 'Kesulitan bernapas');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `phone`, `birth_date`, `gender`, `role`, `created_at`) VALUES
(1, 'admin@pharmedice.com', '$2y$10$LFv9MJiEzThl7.fxpgrZuuUqrhODuIosZ2xBXgtm8CtjK5JQzd4aW', 'Admin Pharmedice', '081221813436', '2006-10-22', 'female', 'admin', '2026-01-29 01:19:14'),
(3, 'mhdikhsan95@gmail.com', '$2y$10$NCEtUUL2XVCgvYFjKFjd7.PqIEmdOIGcaFKsKTjIPzyKoWkCPwFIS', 'muhammad ikhsan', '082285453480', '1995-01-14', 'male', 'user', '2026-01-29 01:49:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `consultation_logs`
--
ALTER TABLE `consultation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forward_chaining_rules`
--
ALTER TABLE `forward_chaining_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicine_recommendations`
--
ALTER TABLE `medicine_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultation_logs`
--
ALTER TABLE `consultation_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forward_chaining_rules`
--
ALTER TABLE `forward_chaining_rules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicine_recommendations`
--
ALTER TABLE `medicine_recommendations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultation_logs`
--
ALTER TABLE `consultation_logs`
  ADD CONSTRAINT `consultation_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_recommendations`
--
ALTER TABLE `medicine_recommendations`
  ADD CONSTRAINT `medicine_recommendations_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_recommendations_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_profiles`
--
ALTER TABLE `patient_profiles`
  ADD CONSTRAINT `patient_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

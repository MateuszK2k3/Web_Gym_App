-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 24, 2025 at 12:30 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logged_in_users`
--

CREATE TABLE `logged_in_users` (
  `sessionId` varchar(100) NOT NULL,
  `userId` int(11) NOT NULL,
  `lastUpdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `logged_in_users`
--

INSERT INTO `logged_in_users` (`sessionId`, `userId`, `lastUpdate`) VALUES
('9176qvse98lktv77u9p0v9evqm', 14, '2025-01-24 00:14:44');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_date` datetime DEFAULT current_timestamp(),
  `exercise` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`exercise`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `training_plans`
--

CREATE TABLE `training_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `days_count` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_plans`
--

INSERT INTO `training_plans` (`id`, `user_id`, `name`, `created_at`, `days_count`, `active`) VALUES
(117, 14, 'Plan treningowy A', '2025-01-10 08:00:00', 3, 1),
(118, 14, 'Plan treningowy B', '2025-01-15 09:00:00', 4, 0),
(119, 14, 'Plan treningowy C', '2025-01-18 10:30:00', 2, 0),
(120, 14, 'Plan treningowy D', '2025-01-20 12:00:00', 1, 0),
(121, 14, 'Plan treningowy E', '2025-01-22 14:00:00', 4, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `training_session`
--

CREATE TABLE `training_session` (
  `id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `exercises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_session`
--

INSERT INTO `training_session` (`id`, `workout_id`, `user_id`, `data`, `exercises`) VALUES
(72, 181, 14, '2025-01-01', '[{\"name\":\"bench press\",\"exercises\":[{\"reps\":12,\"weight\":25},{\"reps\":15,\"weight\":30}]}, {\"name\":\"wiosłowanie\",\"exercises\":[{\"reps\":20,\"weight\":45},{\"reps\":10,\"weight\":50}]}, {\"name\":\"OHP\",\"exercises\":[{\"reps\":10,\"weight\":40},{\"reps\":8,\"weight\":35}]}]'),
(73, 181, 14, '2025-01-02', '[{\"name\":\"bench press\",\"exercises\":[{\"reps\":14,\"weight\":28},{\"reps\":16,\"weight\":32}]}, {\"name\":\"wiosłowanie\",\"exercises\":[{\"reps\":22,\"weight\":48},{\"reps\":12,\"weight\":53}]}, {\"name\":\"OHP\",\"exercises\":[{\"reps\":11,\"weight\":42},{\"reps\":9,\"weight\":38}]}]'),
(74, 181, 14, '2025-01-03', '[{\"name\":\"bench press\",\"exercises\":[{\"reps\":15,\"weight\":30},{\"reps\":17,\"weight\":33}]}, {\"name\":\"wiosłowanie\",\"exercises\":[{\"reps\":23,\"weight\":50},{\"reps\":13,\"weight\":55}]}, {\"name\":\"OHP\",\"exercises\":[{\"reps\":12,\"weight\":45},{\"reps\":10,\"weight\":40}]}]'),
(75, 182, 14, '2025-01-04', '[{\"name\":\"push-up\",\"exercises\":[{\"reps\":20,\"weight\":0},{\"reps\":25,\"weight\":0}]}, {\"name\":\"pull-up\",\"exercises\":[{\"reps\":12,\"weight\":20},{\"reps\":15,\"weight\":25}]}, {\"name\":\"squat\",\"exercises\":[{\"reps\":20,\"weight\":50},{\"reps\":25,\"weight\":60}]}]'),
(76, 182, 14, '2025-01-05', '[{\"name\":\"push-up\",\"exercises\":[{\"reps\":22,\"weight\":0},{\"reps\":28,\"weight\":0}]}, {\"name\":\"pull-up\",\"exercises\":[{\"reps\":13,\"weight\":22},{\"reps\":16,\"weight\":27}]}, {\"name\":\"squat\",\"exercises\":[{\"reps\":22,\"weight\":55},{\"reps\":27,\"weight\":65}]}]'),
(77, 182, 14, '2025-01-06', '[{\"name\":\"push-up\",\"exercises\":[{\"reps\":24,\"weight\":0},{\"reps\":30,\"weight\":0}]}, {\"name\":\"pull-up\",\"exercises\":[{\"reps\":14,\"weight\":24},{\"reps\":17,\"weight\":28}]}, {\"name\":\"squat\",\"exercises\":[{\"reps\":24,\"weight\":60},{\"reps\":30,\"weight\":70}]}]'),
(78, 183, 14, '2025-01-07', '[{\"name\":\"deadlift\",\"exercises\":[{\"reps\":8,\"weight\":80},{\"reps\":6,\"weight\":85}]}, {\"name\":\"bench press\",\"exercises\":[{\"reps\":10,\"weight\":50},{\"reps\":8,\"weight\":55}]}, {\"name\":\"row\",\"exercises\":[{\"reps\":12,\"weight\":60},{\"reps\":10,\"weight\":65}]}]'),
(79, 183, 14, '2025-01-08', '[{\"name\":\"deadlift\",\"exercises\":[{\"reps\":9,\"weight\":85},{\"reps\":7,\"weight\":90}]}, {\"name\":\"bench press\",\"exercises\":[{\"reps\":12,\"weight\":55},{\"reps\":10,\"weight\":60}]}, {\"name\":\"row\",\"exercises\":[{\"reps\":13,\"weight\":65},{\"reps\":11,\"weight\":70}]}]'),
(80, 183, 14, '2025-01-09', '[{\"name\":\"deadlift\",\"exercises\":[{\"reps\":10,\"weight\":90},{\"reps\":8,\"weight\":95}]}, {\"name\":\"bench press\",\"exercises\":[{\"reps\":13,\"weight\":60},{\"reps\":11,\"weight\":65}]}, {\"name\":\"row\",\"exercises\":[{\"reps\":14,\"weight\":70},{\"reps\":12,\"weight\":75}]}]'),
(81, 181, 14, '2025-01-24', '[{\"name\":\"bench press\",\"exercises\":[{\"reps\":45,\"weight\":54},{\"reps\":54,\"weight\":45}]},{\"name\":\"wiosłowanie\",\"exercises\":[{\"reps\":45,\"weight\":45},{\"reps\":54,\"weight\":45},{\"reps\":45,\"weight\":5}]},{\"name\":\"OHP\",\"exercises\":[{\"reps\":54,\"weight\":45},{\"reps\":45,\"weight\":45},{\"reps\":54,\"weight\":45}]}]');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `created_at`) VALUES
(8, 'KozzisxD', '$2y$10$oHX97z3kov96jB824MYBIuwrQaIQ762/N.ALUq7cJuU.y9PgIjx8O', '2025-01-12 12:10:19'),
(14, 'login', '$2y$10$W8UF76uByvrdWNw2kk3VG.D9CtvjbjHT42Ti2ujns4h4NPYY8UlZ2', '2025-01-23 23:42:25');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL,
  `training_plan_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `exercises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `training_plan_id`, `day`, `exercises`) VALUES
(181, 117, 0, '{\"exercises\":[{\"name\":\"bench press\",\"sets\":2},{\"name\":\"wiosłowanie\",\"sets\":3},{\"name\":\"OHP\",\"sets\":3}]}'),
(182, 117, 1, '{\"exercises\":[{\"name\":\"push-up\",\"sets\":4},{\"name\":\"pull-up\",\"sets\":5},{\"name\":\"squat\",\"sets\":6}]}'),
(183, 117, 2, '{\"exercises\":[{\"name\":\"deadlift\",\"sets\":3},{\"name\":\"bench press\",\"sets\":4},{\"name\":\"row\",\"sets\":5}]}'),
(184, 118, 0, '{\"exercises\":[{\"name\":\"biceps curl\",\"sets\":3},{\"name\":\"shoulder press\",\"sets\":4},{\"name\":\"lunges\",\"sets\":5}]}'),
(185, 118, 1, '{\"exercises\":[{\"name\":\"triceps dip\",\"sets\":3},{\"name\":\"leg press\",\"sets\":4},{\"name\":\"lat pulldown\",\"sets\":5}]}'),
(186, 118, 2, '{\"exercises\":[{\"name\":\"squat\",\"sets\":3},{\"name\":\"chest fly\",\"sets\":4},{\"name\":\"deadlift\",\"sets\":5}]}'),
(187, 118, 3, '{\"exercises\":[{\"name\":\"leg curl\",\"sets\":3},{\"name\":\"ab crunches\",\"sets\":4},{\"name\":\"overhead press\",\"sets\":5}]}'),
(188, 119, 0, '{\"exercises\":[{\"name\":\"squats\",\"sets\":3},{\"name\":\"push-ups\",\"sets\":5},{\"name\":\"plank\",\"sets\":2}]}'),
(189, 119, 1, '{\"exercises\":[{\"name\":\"deadlift\",\"sets\":3},{\"name\":\"barbell row\",\"sets\":4},{\"name\":\"pull-up\",\"sets\":5}]}'),
(190, 120, 0, '{\"exercises\":[{\"name\":\"leg press\",\"sets\":3},{\"name\":\"overhead press\",\"sets\":4},{\"name\":\"squat\",\"sets\":5}]}'),
(191, 121, 0, '{\"exercises\":[{\"name\":\"biceps curl\",\"sets\":3},{\"name\":\"squat\",\"sets\":5},{\"name\":\"push-up\",\"sets\":4}]}'),
(192, 121, 1, '{\"exercises\":[{\"name\":\"bench press\",\"sets\":4},{\"name\":\"lunges\",\"sets\":5},{\"name\":\"plank\",\"sets\":3}]}'),
(193, 121, 2, '{\"exercises\":[{\"name\":\"lat pulldown\",\"sets\":4},{\"name\":\"row\",\"sets\":5},{\"name\":\"triceps dip\",\"sets\":3}]}'),
(194, 121, 3, '{\"exercises\":[{\"name\":\"overhead press\",\"sets\":3},{\"name\":\"deadlift\",\"sets\":5},{\"name\":\"sit-up\",\"sets\":3}]}');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `logged_in_users`
--
ALTER TABLE `logged_in_users`
  ADD PRIMARY KEY (`sessionId`);

--
-- Indeksy dla tabeli `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `training_plans`
--
ALTER TABLE `training_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `training_session`
--
ALTER TABLE `training_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_training_session_training` (`workout_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_plan_id` (`training_plan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_plans`
--
ALTER TABLE `training_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `training_session`
--
ALTER TABLE `training_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_plans`
--
ALTER TABLE `training_plans`
  ADD CONSTRAINT `training_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_session`
--
ALTER TABLE `training_session`
  ADD CONSTRAINT `fk_training_session_training` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`training_plan_id`) REFERENCES `training_plans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

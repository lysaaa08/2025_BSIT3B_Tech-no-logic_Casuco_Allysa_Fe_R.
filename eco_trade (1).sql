-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2025 at 11:42 AM
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
-- Database: `eco_trade`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `user_id`, `title`, `description`, `image`, `created_at`) VALUES
(3, 2, 'Tote bag', 'Pre-loved tote bag, used twice and has no defect', 'uploads/1747759290_bag.jpg', '2025-05-20 16:41:30'),
(4, 2, 'Glass Beads Bracelet', 'brand new, I just don\'t like the style that\'s why I am trading this', 'uploads/1747759650_bracelet.jpg', '2025-05-20 16:47:30'),
(5, 3, 'necklace', 'pandora necklace, used once and has no defect', 'uploads/1747760040_necklace.jpg', '2025-05-20 16:54:00'),
(6, 3, 'Necklace for men', 'Gold Silver Black Rectangle Pendant Necklace Men Boys Trendy Chain 60cm, used once.', 'uploads/1747760337_necklace2.jpg', '2025-05-20 16:58:57'),
(7, 3, 'bike', 'Avatar 2.0 Electric Bike (Step Over)', 'uploads/1747760572_bike2.jpg', '2025-05-20 17:02:52'),
(8, 4, 'Old Cellphone', 'NOKIA OLD VINTAGE USED CELL PHONE, too old but still working', 'uploads/1747763263_old phone.jpg', '2025-05-20 17:47:43'),
(9, 4, 'Shoes', 'Pre-loved shoes, used many times but still wearable', 'uploads/1747763648_shoes.jpg', '2025-05-20 17:54:08'),
(10, 5, 'Guitar', 'Best Taylor 214ce, slightly old but still in good condition. Just needed to put some new strings.', 'uploads/1747832372_guitar.jpg', '2025-05-21 12:59:32'),
(11, 1, 'Assorted Items', 'Used Items that has no use for me. They are all in good condition but slightly old.', 'uploads/1747841992_usedItems.jpg', '2025-05-21 15:39:52'),
(12, 2, 'iPhone 13', 'pre-owned', 'uploads/1747893875_iPhone 13.jfif', '2025-05-22 06:04:35'),
(13, 2, 'Gaming chair', '2 months used', 'uploads/1747894098_gaming chair.jfif', '2025-05-22 06:08:18'),
(14, 1, 'bag', 'used once', 'uploads/1747896693_bag1.jpg', '2025-05-22 06:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `seen`, `is_read`) VALUES
(1, 1, 4, 'I want to trade something with you shoes', '2025-05-21 02:11:52', 0, 1),
(2, 4, 1, 'What item you want to trade?', '2025-05-21 06:46:32', 0, 1),
(3, 1, 5, 'Hi!', '2025-05-21 13:01:57', 0, 1),
(4, 1, 5, 'I want to trade something for your guitar', '2025-05-21 13:02:17', 0, 1),
(5, 5, 1, 'what item would you trade for my guitar?', '2025-05-21 13:03:57', 0, 1),
(6, 1, 5, 'can I trade my violin to your guitar?', '2025-05-21 13:05:50', 0, 0),
(7, 2, 3, 'Hi!', '2025-05-21 15:10:04', 0, 1),
(8, 2, 3, 'I want to trade something to your necklace', '2025-05-21 15:10:22', 0, 1),
(9, 2, 1, 'Hi! I want to trade something to you glasses', '2025-05-21 15:12:05', 0, 1),
(10, 2, 4, 'hi!', '2025-05-21 15:12:26', 0, 0),
(11, 2, 4, 'I want to trade something with your cellphone', '2025-05-21 15:12:40', 0, 0),
(12, 2, 5, 'Hi!', '2025-05-21 15:13:06', 0, 0),
(13, 2, 5, 'I want to trade something with your guitar', '2025-05-21 15:13:22', 0, 0),
(14, 3, 2, 'what is it?', '2025-05-21 15:14:42', 0, 0),
(15, 3, 5, 'Hi! I want to trade something with your guitar', '2025-05-21 15:15:22', 0, 0),
(16, 3, 4, 'Hi!', '2025-05-21 15:15:42', 0, 0),
(17, 3, 4, 'I want to trade something with your shoes', '2025-05-21 15:15:59', 0, 0),
(18, 3, 1, 'Hi!', '2025-05-21 15:16:34', 0, 1),
(19, 3, 1, 'I want to trade something with your gasses', '2025-05-21 15:16:50', 0, 1),
(20, 1, 4, 'I want to trade it with bag', '2025-05-22 05:49:27', 0, 0),
(21, 1, 2, 'what item would you trade?', '2025-05-22 05:51:51', 0, 0),
(22, 2, 5, 'hi', '2025-05-22 05:59:35', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_pic`, `created_at`, `is_admin`) VALUES
(1, 'fatima', 'fatima@example.com', '$2y$10$xdfun0zoEy5e8OP1aTeD9eoHaY9WN15wsa6MR/VGI5ynmbmnC/HBa', 'uploads/avatar_1.jpg', '2025-05-20 15:23:33', 0),
(2, 'jasmin', 'jasmin@example.com', '$2y$10$c4GrqOZijG2E/0sjM7jhRuqw2DrNpKcNEBPilq69D8G6kauKvBmMG', 'uploads/avatar_2.webp', '2025-05-20 16:30:28', 0),
(3, 'erica', 'erica@example.com', '$2y$10$1Cw7nGO0aAR9jpGPoyVjPOu3TynmkTNjIcW2c3TpRX7nXVK9FCBWG', 'uploads/avatar_3.jpg', '2025-05-20 16:50:53', 0),
(4, 'rancel', 'rancel@example.com', '$2y$10$BfBHH/tb.B2iPkeDAwrTfO4QneYvN81EHhr6wjgaNqGgOx4cN.Xsq', 'uploads/avatar_4.jpg', '2025-05-20 17:04:01', 0),
(5, 'sandara', 'sandara@example.com', '$2y$10$PyTru9qdqwVkjFllYHXuI.dlPBAfcA3cn2qEE.TDnhL0jC2UOAMii', 'uploads/avatar_5.jfif', '2025-05-21 07:17:51', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

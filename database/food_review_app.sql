-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Oct 13, 2025 at 06:37 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_review_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int NOT NULL,
  `restaurant_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `restaurant_id`, `name`, `menu_img`, `price`, `description`) VALUES
(1, 1, 'Salmon Sushi', '', 120.00, 'ซูชิแซลมอนสด'),
(2, 1, 'Tuna Roll', '', 150.00, 'โรลปลาทูน่า'),
(3, 2, 'Somtum Thai', '', 60.00, 'ส้มตำไทยรสจัด'),
(4, 2, 'Grilled Chicken', '', 80.00, 'ไก่ย่างถ่าน');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `restaurant_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`restaurant_id`, `name`, `restaurant_img`, `category`, `location`, `description`, `created_at`) VALUES
(1, 'Sushi Master', '', 'ญี่ปุ่น', 'Bangkok', 'ร้านซูชิสดใหม่ทุกวัน', '2025-10-01 15:28:43'),
(2, 'Somtum House', '', 'ไทย', 'Chiang Mai', 'ส้มตำรสจัดจ้าน', '2025-10-01 15:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `restaurant_id` int DEFAULT NULL,
  `menu_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `restaurant_id`, `menu_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, NULL, 5, 'อร่อยมาก สดใหม่', '2025-10-01 15:28:43'),
(2, 2, 2, NULL, 4, 'รสชาติถึงเครื่อง เผ็ดกำลังดี', '2025-10-01 15:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
--

CREATE TABLE `review_likes` (
  `like_id` int NOT NULL,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_likes`
--

INSERT INTO `review_likes` (`like_id`, `review_id`, `user_id`, `created_at`) VALUES
(1, 1, 2, '2025-10-01 15:28:43'),
(2, 2, 1, '2025-10-01 15:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('member','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `avatar_img`, `created_at`) VALUES
(1, 'aliceฟฟ', 'alice@example.com', '123456', 'member', NULL, '2025-10-01 15:28:43'),
(2, 'bob', 'bob@example.com', '123456', 'member', 'user_imgs/OdrwXQzeX07940HtZ8VFGynJkFnrEvN0t8o6gBrp.jpg', '2025-10-01 15:28:43'),
(3, 'tgh1233', 'abc@gmail.com', '$2y$12$mmY71ysesV.INPm2DyVLIO1grgLFgJ.UAU.zt8HE3sNeb8dWXScde', 'member', 'user_imgs/ed8NVe7BSUIneQLnyff5j0aJ1msn5LQfA50ns75Q.jpg', '2025-10-06 12:30:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `review_id` (`review_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `restaurant_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_likes`
--
ALTER TABLE `review_likes`
  MODIFY `like_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`) ON DELETE CASCADE;

--
-- Constraints for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD CONSTRAINT `review_likes_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

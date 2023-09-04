CREATE DATABASE `forum` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rank` tinyint(1) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activation_code` varchar(255) NOT NULL,
  `code_to_change_password` varchar(255) NULL DEFAULT NULL,
  `number_of_posts` int NOT NULL,
  `status` text NULL DEFAULT NULL,
  `topics` int NOT NULL,
  `reputation` int NOT NULL,
  `profile_picture` text NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `poster_id` int(11) NOT NULL,
  `post_order` int(11) NOT NULL DEFAULT 1,
  `name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `reputation` int(11) NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_edited` timestamp NULL DEFAULT NULL,
  `date_new_reply` timestamp NOT NULL DEFAULT current_timestamp(),
  `replies` int(11) NOT NULL DEFAULT 0,
  `visibility` tinyint(1) NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
CREATE TABLE `status_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poster_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_edited` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `visibility` tinyint(1) NOT NULL,
  `is_locked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `topics` int(11) NOT NULL,
  `posts` int(11) NOT NULL,
  `visibility` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Adminer 4.17.1 MySQL 8.3.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `category` (`id`, `name`, `color`, `user_id`) VALUES
(1,	'Práce',	'#3498db',	1),
(2,	'práce2',	'#3428AB',	1),
(3,	'programování',	'#008000',	1),
(5,	'červená',	'rgb(205, 92, 92)',	1),
(11,	'Práce',	'#FF0000',	1),
(12,	'Studium',	'#00FF00',	1),
(13,	'Volnočasové aktivity',	'#0000FF',	2),
(14,	'Sport',	'#FFA500',	2),
(15,	'Práce',	'#FF0000',	1),
(16,	'Studium',	'#00FF00',	1),
(17,	'Volnočasové aktivity',	'#0000FF',	2),
(18,	'Sport',	'#FFA500',	2),
(19,	'Programování',	'#1c27c4',	16),
(20,	'Odpočinek',	'#8c7812',	16);

DROP TABLE IF EXISTS `hours`;
CREATE TABLE `hours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `from_time` time DEFAULT NULL,
  `to_time` time DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `note` text,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `hours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hours_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `hours` (`id`, `user_id`, `category_id`, `date`, `from_time`, `to_time`, `duration`, `note`, `description`, `created_at`) VALUES
(2,	1,	1,	'2025-01-15',	NULL,	NULL,	90,	'Druhý zápis v lednu',	NULL,	'2025-05-02 09:35:18'),
(3,	1,	1,	'2025-02-05',	NULL,	NULL,	60,	'Únorová práce',	NULL,	'2025-05-02 09:35:18'),
(4,	1,	3,	'2025-02-20',	NULL,	NULL,	30,	'Krátká schůzka',	NULL,	'2025-05-02 09:35:18'),
(5,	1,	1,	'2025-03-01',	NULL,	NULL,	45,	'Začátek března',	NULL,	'2025-05-02 09:35:18'),
(6,	1,	1,	'2025-03-12',	NULL,	NULL,	75,	'Delší práce',	NULL,	'2025-05-02 09:35:18'),
(8,	1,	1,	'2025-04-03',	NULL,	NULL,	50,	'Duben start',	NULL,	'2025-05-02 09:35:18'),
(10,	1,	1,	'2025-05-01',	NULL,	NULL,	60,	'První máj',	NULL,	'2025-05-02 09:35:18'),
(11,	1,	1,	'2025-05-02',	NULL,	NULL,	90,	'Druhý máj',	NULL,	'2025-05-02 09:35:18'),
(14,	1,	3,	'2025-05-14',	'08:00:00',	'20:00:00',	720,	'poznámka',	'popis',	'2025-05-06 21:51:43'),
(15,	1,	2,	'2025-04-10',	NULL,	NULL,	600,	'asdasdasd',	'asdasdasd',	'2025-05-06 22:24:29'),
(16,	1,	3,	'2025-05-16',	'00:00:00',	'06:30:00',	390,	'byla to těžká práce',	'popisuji to bylo to těžké',	'2025-05-07 11:01:37'),
(18,	1,	5,	'2025-05-29',	NULL,	NULL,	50,	'tohle je červená',	'toto je popis červené',	'2025-05-08 10:42:50'),
(22,	1,	NULL,	'2025-05-08',	NULL,	NULL,	20,	'asdasd',	'asdasd',	'2025-05-08 23:59:38'),
(35,	16,	19,	'2025-05-08',	NULL,	NULL,	60,	'programuji 60 minut',	'Aliquam justo eros, commodo ac magna ac, molestie volutpat tellus. Phasellus viverra nisi venenatis erat tristique posuere. Quisque mollis sapien eu mi tincidunt consequat. Aliquam ut elit ante. Vivamus sodales dignissim gravida. Maecenas luctus rhoncus mi, et iaculis sem imperdiet eu. Vestibulum rhoncus sem id ultricies pulvinar. Nulla faucibus ipsum at sapien varius, ac dignissim ipsum blandit. Curabitur fermentum tincidunt turpis in gravida. Integer rhoncus sed lectus ac porta. Vestibulum vel odio cursus, aliquet nunc eget, dictum nisi. Nunc at pulvinar mi. Ut imperdiet lorem sed ligula lacinia blandit. Sed libero lacus, convallis quis ex ac, mollis pharetra nibh. Curabitur volutpat, velit ut gravida finibus, est erat lobortis risus, sit amet viverra libero eros ut nulla. Fusce condimentum lorem sit amet nunc vehicula suscipit vel et urna.',	'2025-05-09 00:25:10'),
(36,	16,	20,	'2025-05-08',	'01:29:00',	'10:30:00',	541,	'těžký odpočinek',	'Aliquam justo eros, commodo ac magna ac, molestie volutpat tellus. Phasellus viverra nisi venenatis erat tristique posuere. Quisque mollis sapien eu mi tincidunt consequat. Aliquam ut elit ante. Vivamus sodales dignissim gravida. Maecenas luctus rhoncus mi, et iaculis sem imperdiet eu. Vestibulum rhoncus sem id ultricies pulvinar. Nulla faucibus ipsum at sapien varius, ac dignissim ipsum blandit. Curabitur fermentum tincidunt turpis in gravida. Integer rhoncus sed lectus ac porta. Vestibulum vel odio cursus, aliquet nunc eget, dictum nisi. Nunc at pulvinar mi. Ut imperdiet lorem sed ligula lacinia blandit. Sed libero lacus, convallis quis ex ac, mollis pharetra nibh. Curabitur volutpat, velit ut gravida finibus, est erat lobortis risus, sit amet viverra libero eros ut nulla. Fusce condimentum lorem sit amet nunc vehicula suscipit vel et urna.',	'2025-05-09 00:25:51');

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `news` (`id`, `created_at`, `title`, `content`) VALUES
(1,	'2025-05-08 17:02:24',	'Přidáváme funkční barvy!!',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc luctus ac ante vel imperdiet. Nunc ultrices bibendum risus lobortis fermentum. Curabitur a scelerisque leo. Vestibulum elementum est ut iaculis suscipit. In in sapien felis. Etiam faucibus elit arcu. Fusce nec nibh convallis, bibendum dolor ac, porta dolor. Sed quis sapien nisl. Morbi iaculis mauris enim, in ultrices nunc dignissim a. Sed sagittis elementum libero, et elementum quam pretium ac. Phasellus viverra elit quis arcu consequat interdum. Vestibulum non urna a lectus posuere placerat ut quis ex. Phasellus lacinia suscipit erat, sagittis dictum sem interdum posuere.\n\nQuisque quis fermentum augue, eget pretium urna. Morbi ultrices feugiat massa, in blandit quam convallis a. Suspendisse venenatis libero sed est aliquam, at consequat mi cursus. Vestibulum sed mauris imperdiet, tempus quam sit amet, bibendum nisl. Curabitur rhoncus rhoncus neque a interdum. Sed at eros mauris. Nunc a turpis et magna pellentesque porttitor. Donec semper dignissim ligula at mattis. Sed et neque porttitor, ultricies odio id, porta sem. In feugiat tempor dui quis convallis. Suspendisse bibendum gravida rutrum. Interdum et malesuada fames ac ante ipsum primis in faucibus.');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `createDate`) VALUES
(1,	'Filip Kasan',	'$2y$10$xZTyEijVVjYDDqC4l66.gOL8VIHfnNmfX4EVZ9HXf265PfmGI/2v.',	'filip.kasan@gmail.com',	'admin',	'2025-05-08 11:21:23'),
(15,	'user',	'$2y$10$No6x1ZoRk35cRCad/yFG0uueZizW0v82/wU70EpnIPaHHpC/CbWfO',	'testu@ossp.cz',	'user',	'2025-05-09 00:22:22'),
(16,	'admin',	'$2y$10$b6EP/T2XP6I6784wLiWizuiY7o9TrJ7uwSUFBNARKvRHll79NP14O',	'testa@ossp.cz',	'admin',	'2025-05-09 00:22:48');

-- 2025-05-08 22:27:35
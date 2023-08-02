-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for musicstream2
CREATE DATABASE IF NOT EXISTS `musicstream2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `musicstream2`;

-- Dumping structure for table musicstream2.album
CREATE TABLE IF NOT EXISTS `album` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_album` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` datetime NOT NULL,
  `user_id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_39986E43D17F50A6` (`uuid`),
  KEY `IDX_39986E43A76ED395` (`user_id`),
  CONSTRAINT `FK_39986E43A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.album: ~5 rows (approximately)
INSERT INTO `album` (`id`, `cover`, `name_album`, `release_date`, `user_id`, `uuid`) VALUES
	(1, 'I-used-to-know-her.jpg', 'I Used to Know HER', '2019-05-02 14:07:22', 2, '0a074143-0694-11ee-af90-80e82c978fe5'),
	(2, 'H.E.R.png', 'H.E.R', '2017-05-02 14:10:57', 2, '0a0810e9-0694-11ee-af90-80e82c978fe5'),
	(3, 'KissLand.png', 'Kiss Land', '2013-05-02 14:12:56', 3, '0a081629-0694-11ee-af90-80e82c978fe5'),
	(4, 'Screenshot-2023-05-10-211902-645bff2233837.png', 'Back of My Mind', '2023-05-10 20:31:29', 2, '0a08187a-0694-11ee-af90-80e82c978fe5');

-- Dumping structure for table musicstream2.comment
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_mess` datetime NOT NULL,
  `song_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9474526CD17F50A6` (`uuid`),
  KEY `IDX_9474526CA0BDB2F3` (`song_id`),
  KEY `IDX_9474526CA76ED395` (`user_id`),
  CONSTRAINT `FK_9474526CA0BDB2F3` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.comment: ~13 rows (approximately)
INSERT INTO `comment` (`id`, `text`, `date_mess`, `song_id`, `user_id`, `uuid`) VALUES
	(319, 'hello', '2023-05-29 00:27:51', 13, NULL, '0a214fda-0694-11ee-af90-80e82c978fe5'),
	(323, 'fghjk', '2023-06-01 07:21:52', 13, 30, '0a2161e5-0694-11ee-af90-80e82c978fe5'),
	(324, 'super', '2023-06-01 07:26:52', 14, 30, '0a216468-0694-11ee-af90-80e82c978fe5'),
	(325, 'super', '2023-06-01 09:13:03', 7, 30, '0a2166b1-0694-11ee-af90-80e82c978fe5'),
	(326, 'bvfgdv', '2023-06-01 13:59:41', 19, 30, '0a21692f-0694-11ee-af90-80e82c978fe5'),
	(327, 'gfd', '2023-06-01 14:16:39', 5, 30, '0a216b68-0694-11ee-af90-80e82c978fe5'),
	(328, 'super', '2023-06-02 19:34:13', 6, 30, '0a216d6c-0694-11ee-af90-80e82c978fe5'),
	(329, 'test', '2023-06-09 07:23:17', 2, 30, '962e1614-b6b3-4f15-a02a-6733dd722c91'),
	(333, 'hello', '2023-07-29 20:14:58', 7, 2, 'f011626f-ef6e-4ea0-9a08-7d243017d8f0'),
	(335, 'tesf', '2023-07-31 20:18:45', 11, 30, '689f2514-98e0-446e-bde8-41da05ad32fd'),
	(337, 'test', '2023-08-02 15:19:57', 5, 30, '52cda280-8760-4ac6-b7c6-a49874b5fee3'),
	(339, 'hii', '2023-08-02 17:31:55', 6, 2, '933c059a-4c08-4cf0-9b6d-326c127fef31');

-- Dumping structure for table musicstream2.doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Dumping data for table musicstream2.doctrine_migration_versions: ~9 rows (approximately)
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230502082355', '2023-05-02 08:24:26', 338),
	('DoctrineMigrations\\Version20230502093825', '2023-05-02 09:38:44', 298),
	('DoctrineMigrations\\Version20230502100825', '2023-05-02 10:08:35', 57),
	('DoctrineMigrations\\Version20230502113335', '2023-05-02 11:33:50', 199),
	('DoctrineMigrations\\Version20230608072713', '2023-06-09 07:05:38', 1023),
	('DoctrineMigrations\\Version20230608073243', '2023-06-09 07:05:39', 875),
	('DoctrineMigrations\\Version20230629200322', '2023-06-30 06:47:05', 34),
	('DoctrineMigrations\\Version20230629200646', '2023-06-30 06:47:05', 32),
	('DoctrineMigrations\\Version20230630064753', '2023-06-30 06:47:59', 21),
	('DoctrineMigrations\\Version20230802174550', '2023-08-02 17:46:04', 42);

-- Dumping structure for table musicstream2.genre
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `genre_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_835033F8D17F50A6` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.genre: ~14 rows (approximately)
INSERT INTO `genre` (`id`, `genre_name`, `uuid`) VALUES
	(1, 'Popular music', '0a2e9c26-0694-11ee-af90-80e82c978fe5'),
	(2, 'Electronic music', '0a2ecdee-0694-11ee-af90-80e82c978fe5'),
	(3, 'Rhythm and blues', '0a2ed0b8-0694-11ee-af90-80e82c978fe5'),
	(4, 'Jazz', '0a2ed284-0694-11ee-af90-80e82c978fe5'),
	(5, 'Pop music', '0a2ed457-0694-11ee-af90-80e82c978fe5'),
	(6, 'Electronic music', '0a2ed61f-0694-11ee-af90-80e82c978fe5'),
	(7, 'Blues', '0a2ed7e9-0694-11ee-af90-80e82c978fe5'),
	(8, 'Folk music', '0a2eda9b-0694-11ee-af90-80e82c978fe5'),
	(9, 'House music', '0a2edc61-0694-11ee-af90-80e82c978fe5'),
	(10, 'Soul music', '0a2ede2f-0694-11ee-af90-80e82c978fe5'),
	(11, 'Reggae', '0a2edfed-0694-11ee-af90-80e82c978fe5'),
	(12, 'Techno', '0a2ee1ae-0694-11ee-af90-80e82c978fe5'),
	(13, 'Rock and roll', '0a2ee414-0694-11ee-af90-80e82c978fe5'),
	(14, 'Latin music', '0a2ee65d-0694-11ee-af90-80e82c978fe5');

-- Dumping structure for table musicstream2.messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.messenger_messages: ~3 rows (approximately)
INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
	(1, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:39:\\"Symfony\\\\Bridge\\\\Twig\\\\Mime\\\\TemplatedEmail\\":4:{i:0;s:41:\\"registration/confirmation_email.html.twig\\";i:1;N;i:2;a:3:{s:9:\\"signedUrl\\";s:165:\\"http://127.0.0.1:8000/verify/email?expires=1683040575&signature=KmuPSNHs8QqBBNcsjbUVLxYloTZSYcR5nJgpQULSkAs%3D&token=C4oo7MH75Y0DLR3pKDg0ECH4AgaKKrJSKjQ1pmXR%2Bn0%3D\\";s:19:\\"expiresAtMessageKey\\";s:26:\\"%count% hour|%count% hours\\";s:20:\\"expiresAtMessageData\\";a:1:{s:7:\\"%count%\\";i:1;}}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:19:\\"steph6362@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:4:\\"x0-3\\";}}}}s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:21:\\"bedoti4679@saeoil.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:25:\\"Please Confirm your Email\\";}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-05-02 14:16:16', '2023-05-02 14:16:16', NULL),
	(2, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:39:\\"Symfony\\\\Bridge\\\\Twig\\\\Mime\\\\TemplatedEmail\\":4:{i:0;s:41:\\"registration/confirmation_email.html.twig\\";i:1;N;i:2;a:3:{s:9:\\"signedUrl\\";s:175:\\"http://127.0.0.1:8000/verify/email?expires=1683054377&signature=hT5%2Ft0YK3kZWdoAwW41%2Ba2N41Ls1vlojWP%2FZ177iQeQ%3D&token=AGJDjQ453vqtLVJd2U4XOzgWRn%2FXMxfOhC%2B%2FnEjXcWk%3D\\";s:19:\\"expiresAtMessageKey\\";s:26:\\"%count% hour|%count% hours\\";s:20:\\"expiresAtMessageData\\";a:1:{s:7:\\"%count%\\";i:1;}}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:19:\\"steph6362@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:4:\\"x0-3\\";}}}}s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:23:\\"ditipen509@in2reach.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:25:\\"Please Confirm your Email\\";}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-05-02 18:06:18', '2023-05-02 18:06:18', NULL),
	(3, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:39:\\"Symfony\\\\Bridge\\\\Twig\\\\Mime\\\\TemplatedEmail\\":4:{i:0;s:41:\\"registration/confirmation_email.html.twig\\";i:1;N;i:2;a:3:{s:9:\\"signedUrl\\";s:165:\\"http://127.0.0.1:8000/verify/email?expires=1683054600&signature=qNeRv5siVKHbAPElRyrAJHEGLYgS990QoWRGPtBRCtc%3D&token=UuvJinsqXVfm%2FBBBUfGuqurbldo6g3b7qyKaKp5KHpw%3D\\";s:19:\\"expiresAtMessageKey\\";s:26:\\"%count% hour|%count% hours\\";s:20:\\"expiresAtMessageData\\";a:1:{s:7:\\"%count%\\";i:1;}}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:19:\\"steph6362@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:4:\\"x0-3\\";}}}}s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:22:\\"paxefem331@larland.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:25:\\"Please Confirm your Email\\";}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-05-02 18:10:00', '2023-05-02 18:10:00', NULL);

-- Dumping structure for table musicstream2.playlist
CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playlist_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `user_id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D782112DD17F50A6` (`uuid`),
  KEY `IDX_D782112DA76ED395` (`user_id`),
  CONSTRAINT `FK_D782112DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.playlist: ~5 rows (approximately)
INSERT INTO `playlist` (`id`, `image`, `playlist_name`, `date_created`, `user_id`, `uuid`, `description`) VALUES
	(1, 'playlist1.jpg', 'playlist #1', '2023-05-02 17:10:11', 1, '0a44a7bb-0694-11ee-af90-80e82c978fe5', NULL),
	(2, 'playlist2.jpg', 'playlist#2', '2023-05-10 14:19:45', 1, '0a44bc1b-0694-11ee-af90-80e82c978fe5', NULL),
	(6, 'blocks-T3mKJXfdims-unsplash-1-645ba73531624.jpg', 'fght', '2023-05-10 14:25:00', 2, '0a44be42-0694-11ee-af90-80e82c978fe5', NULL),
	(14, 'market-place-647850b2dee9d.png', 'date test', '2023-06-01 08:02:57', 30, '0a44c021-0694-11ee-af90-80e82c978fe5', NULL),
	(17, NULL, 'test1', '2023-06-15 14:00:02', 30, '1ffc08ca-40ed-42ff-8cf4-ca605dd90813', 'Lorem ipsum');

-- Dumping structure for table musicstream2.playlist_song
CREATE TABLE IF NOT EXISTS `playlist_song` (
  `playlist_id` int NOT NULL,
  `song_id` int NOT NULL,
  PRIMARY KEY (`playlist_id`,`song_id`),
  KEY `IDX_93F4D9C36BBD148` (`playlist_id`),
  KEY `IDX_93F4D9C3A0BDB2F3` (`song_id`),
  CONSTRAINT `FK_93F4D9C36BBD148` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_93F4D9C3A0BDB2F3` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.playlist_song: ~9 rows (approximately)
INSERT INTO `playlist_song` (`playlist_id`, `song_id`) VALUES
	(1, 2),
	(1, 7),
	(2, 6),
	(2, 9),
	(2, 10),
	(14, 2),
	(14, 6),
	(14, 17),
	(17, 6);

-- Dumping structure for table musicstream2.playlist_user
CREATE TABLE IF NOT EXISTS `playlist_user` (
  `playlist_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`playlist_id`,`user_id`),
  KEY `IDX_2D8AE12B6BBD148` (`playlist_id`),
  KEY `IDX_2D8AE12BA76ED395` (`user_id`),
  CONSTRAINT `FK_2D8AE12B6BBD148` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2D8AE12BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.playlist_user: ~6 rows (approximately)
INSERT INTO `playlist_user` (`playlist_id`, `user_id`) VALUES
	(1, 30),
	(2, 2),
	(2, 3),
	(2, 16),
	(2, 30),
	(17, 30);

-- Dumping structure for table musicstream2.reset_password_request
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.reset_password_request: ~0 rows (approximately)

-- Dumping structure for table musicstream2.song
CREATE TABLE IF NOT EXISTS `song` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_song` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `album_id` int NOT NULL,
  `genre_id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_33EDEEA1D17F50A6` (`uuid`),
  KEY `IDX_33EDEEA1A76ED395` (`user_id`),
  KEY `IDX_33EDEEA11137ABCF` (`album_id`),
  KEY `IDX_33EDEEA14296D31F` (`genre_id`),
  CONSTRAINT `FK_33EDEEA11137ABCF` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_33EDEEA14296D31F` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`),
  CONSTRAINT `FK_33EDEEA1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.song: ~18 rows (approximately)
INSERT INTO `song` (`id`, `name_song`, `link`, `user_id`, `album_id`, `genre_id`, `uuid`) VALUES
	(1, 'As I Am', 'H.E.R. - As I Am.mp3', 2, 1, 3, '0a585480-0694-11ee-af90-80e82c978fe5'),
	(2, 'Carried Away', 'H.E.R. - Carried Away.mp3', 2, 1, 10, '0a586497-0694-11ee-af90-80e82c978fe5'),
	(3, 'Could_ve Been ft. Bryson Tiller', 'H.E.R. - Could_ve Been (Audio) ft. Bryson Tiller.mp3', 2, 1, 7, '0a586736-0694-11ee-af90-80e82c978fe5'),
	(4, 'Racks (Audio) ft. YBN Cordae', 'H.E.R. - Racks (Audio) ft. YBN Cordae.mp3', 2, 1, 3, '0a58696c-0694-11ee-af90-80e82c978fe5'),
	(5, 'Love In The Sky', 'Love In The Sky.mp3', 3, 3, 7, '0a586c43-0694-11ee-af90-80e82c978fe5'),
	(6, 'Belong To The World', 'The Weeknd - Belong To The World.mp3', 3, 3, 5, '0a586e61-0694-11ee-af90-80e82c978fe5'),
	(7, 'Adaptation', 'TheWeeknd - Adaptation.mp3', 3, 3, 3, '0a5871d4-0694-11ee-af90-80e82c978fe5'),
	(8, 'The Town', 'TheWeeknd - The Town.mp3', 3, 3, 7, '0a587407-0694-11ee-af90-80e82c978fe5'),
	(9, 'Every Kind Of Way', 'H.E.R. - Every Kind Of Way.mp3', 2, 2, 7, '0a58763c-0694-11ee-af90-80e82c978fe5'),
	(10, 'Let Me In', 'H.E.R. - Let Me In.mp3', 2, 2, 3, '0a58793e-0694-11ee-af90-80e82c978fe5'),
	(11, 'Jungle', 'Jungle.mp3', 2, 2, 3, '0a587b41-0694-11ee-af90-80e82c978fe5'),
	(13, 'Come Through', 'H-E-R-Come-Through-Visualizer-ft-Chris-Brown-645bc58e76a9d.mp3', 2, 4, 3, '0a587d3d-0694-11ee-af90-80e82c978fe5'),
	(14, 'Damage', 'H-E-R-Damage-645caba61be74.mp3', 2, 4, 5, '0a587f7d-0694-11ee-af90-80e82c978fe5'),
	(15, 'Process', 'H-E-R-Process-645cabfb46966.mp3', 2, 4, 1, '0a588180-0694-11ee-af90-80e82c978fe5'),
	(16, 'My Own', 'H-E-R-My-Own-645cac672c03f.mp3', 2, 4, 7, '0a588383-0694-11ee-af90-80e82c978fe5'),
	(17, 'Bloody Water', 'H-E-R-Bloody-Waters-Audio-ft-Thundercat-645cacc6a818b.mp3', 2, 4, 3, '0a58857d-0694-11ee-af90-80e82c978fe5'),
	(19, 'test', 'The Weeknd ft. Future - Double Fantasy.mp3', 3, 3, 5, '0a5887fb-0694-11ee-af90-80e82c978fe5');

-- Dumping structure for table musicstream2.subscribe
CREATE TABLE IF NOT EXISTS `subscribe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_follow` datetime NOT NULL,
  `user1_id` int NOT NULL,
  `user2_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_68B95F3E56AE248B` (`user1_id`),
  KEY `IDX_68B95F3E441B8B65` (`user2_id`),
  CONSTRAINT `FK_68B95F3E441B8B65` FOREIGN KEY (`user2_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_68B95F3E56AE248B` FOREIGN KEY (`user1_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.subscribe: ~6 rows (approximately)
INSERT INTO `subscribe` (`id`, `date_follow`, `user1_id`, `user2_id`) VALUES
	(127, '2023-05-20 14:20:16', 1, 3),
	(132, '2023-05-20 14:25:53', 1, 2),
	(133, '2023-05-21 18:07:16', 3, 2),
	(143, '2023-07-27 07:30:57', 30, 3),
	(144, '2023-07-29 20:10:49', 30, 2),
	(145, '2023-07-29 20:13:56', 2, 3);

-- Dumping structure for table musicstream2.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `is_banned` tinyint(1) NOT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `poster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `twitter_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649D17F50A6` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.user: ~6 rows (approximately)
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `avatar`, `username`, `is_verified`, `is_banned`, `google_id`, `uuid`, `poster`, `bio`, `twitter_id`) VALUES
	(1, 'bedoti4679@saeoil.com', '{"1": "ROLE_USER"}', '$2y$13$Z2MkJWmewEX5gBzaN3R7VeJ2oAVLhWVbfcFp2UvjgcwNE3nEOZx.S', NULL, 'you', 1, 0, NULL, '0a779431-0694-11ee-af90-80e82c978fe5', NULL, NULL, NULL),
	(2, 'ditipen509@in2reach.com', '["ROLE_ARTIST"]', '$2y$13$JKJXoW24rQdZJ2dPi4O4ReeSiUFlgCG6rz7.E7SpoQ/.y2KXEwJUa', 'her.jpg', 'H.E.R', 1, 0, NULL, '0a783d57-0694-11ee-af90-80e82c978fe5', 'download-648e194e6481c.jpg', 'Global pop superstar Dua Lipa released Future Nostalgia, her #1 UK sophomore album, this year to worldwide acclaim. It is one of the best reviewed albums of 2020 and debuted in the top 5 of the Billboard 200 Album Chart. Upon release, Future Nostalgia was the most streamed album in a day by a British female artist globally in Spotify history and has over 4.5 billion streams to date. Dua is the biggest female artist in the world on Spotify and is currently the third biggest artist overall with nearly 60 million monthly listeners. The album’s certified platinum lead single “Don’t Start Now” is a worldwide hit with one billion streams on Spotify alone, and a #2 spot on the Billboard Hot 100, a career high for the pop star. The track also broke her personal best record of weeks at #1 at US Top 40 radio. Dua followed the success of “Don’t Start Now” by releasing smash UK single “Physical,” and her US Top 40 #1 “Break My Heart.” Most recently, Future Nostalgia was shortlisted for UK’s prestigious Mercury Prize. Future Nostalgia is the follow up to Dua’s eponymous 2017 debut, which is certified platinum and spawned 6 platinum tracks. She made BRIT Award history in 2018 by becoming the first female artist to pick up five nominations, with two wins for British Breakthrough Act and British Female Solo Artist, and received two Grammy awards for Best ', NULL),
	(3, 'paxefem331@larland.com', '["ROLE_ARTIST"]', '$2y$13$S2b11CeUO1ftho4QRDkEE.AV6YIUVJS/bTJOMO95Q5RkVtHmi3poG', 'th-648e108e1ef73.jpg', 'TW', 1, 0, NULL, '0a78439a-0694-11ee-af90-80e82c978fe5', 'images-648e12646ac97.jpg', 'Lorem Ipsum', NULL),
	(16, 'bannedUser@test.fr', '[]', '$2y$13$4ntQERaKzuKPOqzswrSxHOpZ82fP7JuLHtQmlZXyE5VI8EBSHRLdG', NULL, 'bannedUser', 1, 0, NULL, '0a784798-0694-11ee-af90-80e82c978fe5', NULL, NULL, NULL),
	(30, 'admin@admin.com', '["ROLE_ADMIN"]', '$2y$13$kCGYQs5RZe1fPY3BjwtPUuHHMd.llmJfzcc/wsFzH8rtTyuavZ9F.', '', 'admin', 1, 0, NULL, '0a784f6f-0694-11ee-af90-80e82c978fe5', '', 'vffdcvdcvgf', 'x03826155085695'),
	(36, 'steph6362@gmail.com', '[]', NULL, 'https://lh3.googleusercontent.com/a/AAcHTtcJZ1bqUFRAm1ZzIoUhS-q13tT-sv92WtMgTVBv=s96-c', 'X0 -3', 0, 0, '111905502707698564668', 'a1120be2-e9e2-472e-b075-788ac42ec54c', NULL, NULL, NULL);

-- Dumping structure for table musicstream2.user_song
CREATE TABLE IF NOT EXISTS `user_song` (
  `user_id` int NOT NULL,
  `song_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`song_id`),
  KEY `IDX_496CA268A76ED395` (`user_id`),
  KEY `IDX_496CA268A0BDB2F3` (`song_id`),
  CONSTRAINT `FK_496CA268A0BDB2F3` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_496CA268A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table musicstream2.user_song: ~22 rows (approximately)
INSERT INTO `user_song` (`user_id`, `song_id`) VALUES
	(1, 6),
	(1, 7),
	(1, 9),
	(1, 11),
	(1, 13),
	(1, 14),
	(1, 16),
	(2, 6),
	(2, 7),
	(3, 2),
	(3, 7),
	(3, 9),
	(3, 13),
	(30, 2),
	(30, 5),
	(30, 6),
	(30, 11),
	(30, 13),
	(30, 14),
	(30, 15),
	(30, 17),
	(30, 19);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

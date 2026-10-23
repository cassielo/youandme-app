-- YouAndMe database: tables and demo data (passwords are stored as bcrypt hashes)

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE TABLE IF NOT EXISTS `chat` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `userid` int(8) unsigned NOT NULL,
    `chatroomid` int(8) unsigned NOT NULL,
    `content` varchar(255) DEFAULT NULL,
    `created_at` datetime NOT NULL COMMENT 'When the message was sent, in UTC',
    PRIMARY KEY (`id`),
    KEY `chatroomid` (`chatroomid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `chatroom` (
    `chatid` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `managerid` int(8) unsigned NOT NULL,
    `chatname` varchar(255) NOT NULL,
    `intro` varchar(255) NOT NULL,
    PRIMARY KEY (`chatid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `list` (
    `listID` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `userID` int(8) NOT NULL,
    `chatroomID` int(8) unsigned NOT NULL,
    PRIMARY KEY (`listID`),
    UNIQUE KEY `member` (`userID`, `chatroomID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `user` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(255) NOT NULL DEFAULT '0',
    `password` varchar(255) NOT NULL DEFAULT '0',
    `mail` varchar(255) NOT NULL DEFAULT '0',
    `name` varchar(255) NOT NULL DEFAULT '0',
    `pic` int(8) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Demo data (times in UTC); to try the chat, register and join a room
INSERT INTO `user` (`id`, `username`, `password`, `mail`, `name`, `pic`) VALUES
    (1, 'emma', '$2y$10$qCgAupAXhhE9ZMhj83aw2unBOYOp9434hz0U4ug5UptsnA8cyVXYm', 'emma@example.com', 'Emma Wilson', 1),
    (2, 'liam', '$2y$10$cozNNK0SANeM000vY7kqs.n/QTn7xevPnYOgDnGlPVpivbDNLbRVm', 'liam@example.com', 'Liam Chen', 4),
    (3, 'sofia', '$2y$10$3PU6FnnybJnXGIPxcgAGk.wPjQS8MCHboJMfCGmqP1yaLKGJnSc9W', 'sofia@example.com', 'Sofia Garcia', 2),
    (4, 'noah', '$2y$10$geQF.cMmolNDBcaF/SgTNursZCsWXcRCRDBmTgAPhjas596ovnACy', 'noah@example.com', 'Noah Kim', 5),
    (5, 'mei', '$2y$10$7f0LcnomTDLpjlobbiVBq.jyRSz.gDup7KCQdZkAxzQGHSDl39VnS', 'mei@example.com', 'Mei Lin', 3),
    (6, 'demo', '$2y$10$X1zuaq3s0sNGS6WllK57Sek9CgyzhyVqM/YSdPVrlrLRgJl1vt8V2', 'demo@example.com', 'Demo User', 6);
INSERT INTO `chatroom` (`chatid`, `managerid`, `chatname`, `intro`) VALUES
    (1, 1, 'Movie Talk', 'Movies we''ve watched, want to watch, and can''t stop talking about.'),
    (2, 3, 'Music Lounge', 'Share what you''re listening to.'),
    (3, 2, 'Food Hunters', 'Good food nearby, tested by us.');
INSERT INTO `list` (`listID`, `userID`, `chatroomID`) VALUES
    (1, 1, 1),
    (2, 2, 1),
    (3, 4, 1),
    (4, 5, 1),
    (5, 3, 2),
    (6, 4, 2),
    (7, 1, 2),
    (8, 2, 3),
    (9, 5, 3),
    (10, 3, 3),
    (11, 4, 3),
    (12, 6, 1),
    (13, 6, 3);
INSERT INTO `chat` (`id`, `userid`, `chatroomid`, `content`, `created_at`) VALUES
    (1, 1, 1, 'Just watched Spirited Away again on the big screen. Still magical ✨', '2026-10-05 11:32:00'),
    (2, 2, 1, 'The re-release? I missed it last weekend!', '2026-10-05 11:35:00'),
    (3, 4, 1, 'Same here. Is it still showing this week?', '2026-10-05 11:36:00'),
    (4, 1, 1, 'Until Thursday, I think. The 7:30 show wasn''t full.', '2026-10-05 11:40:00'),
    (5, 5, 1, 'Shall we go together on Wednesday? I can book the tickets.', '2026-10-05 11:52:00'),
    (6, 2, 1, 'I''m in 🙌', '2026-10-05 11:53:00'),
    (7, 4, 1, 'Count me in too. Popcorn is on me 🍿', '2026-10-05 12:01:00'),
    (8, 5, 1, 'Booked 4 seats, row F. See you there!', '2026-10-05 12:20:00'),
    (9, 3, 2, 'What''s everyone listening to while studying these days?', '2026-10-07 13:05:00'),
    (10, 4, 2, 'Lo-fi jazz, mostly. No lyrics, so I can actually focus.', '2026-10-07 13:09:00'),
    (11, 1, 2, 'Film soundtracks for me. Joe Hisaishi is perfect for late nights 🎹', '2026-10-07 13:12:00'),
    (12, 3, 2, 'Ooh, I love his music. Any playlist you''d recommend?', '2026-10-07 13:15:00'),
    (13, 1, 2, 'I''ll share mine later. It''s about two hours long.', '2026-10-07 13:18:00'),
    (14, 4, 2, 'Speaking of music, the campus band is playing on Friday night.', '2026-10-07 13:40:00'),
    (15, 3, 2, 'Outdoor stage? I''ll bring snacks!', '2026-10-07 13:42:00'),
    (16, 2, 3, 'Found a beef noodle place near the station. The broth is amazing 🍜', '2026-10-09 04:10:00'),
    (17, 5, 3, 'Spicy or clear broth?', '2026-10-09 04:13:00'),
    (18, 2, 3, 'Spicy. They also have a half-size bowl if you''re not that hungry.', '2026-10-09 04:15:00'),
    (19, 3, 3, 'How long was the line?', '2026-10-09 04:21:00'),
    (20, 2, 3, 'About 15 minutes at noon. Go before 11:30 if you can.', '2026-10-09 04:24:00'),
    (21, 5, 3, 'Trying it tomorrow. Any side dishes worth ordering?', '2026-10-09 04:30:00'),
    (22, 2, 3, 'The cucumber salad and the braised tofu. Both great.', '2026-10-09 04:33:00'),
    (23, 4, 3, 'Saving this. Report back, Mei!', '2026-10-09 04:40:00'),
    (24, 6, 3, 'Went there today. The braised tofu was as good as you said!', '2026-10-10 05:15:00');

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

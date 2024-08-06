CREATE DATABASE tickets;

USE tickets;

CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `username` varchar(20) NOT NULL,
                         `userpass` varchar(50) DEFAULT NULL,
                         `email` varchar(45) NOT NULL,
                         `name` varchar(45) DEFAULT NULL,
                         `auth_token` char(64) DEFAULT NULL,
                         `user_level` tinyint(4) NOT NULL DEFAULT 1,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES (null, 'admin', md5('admin'), 'admin@tickets.ro', 'Administrator', null, 3);

CREATE TABLE `user_levels` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `name` char(10) NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reset_password` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `user_id` varchar(45) NOT NULL,
                                  `reset_token` varchar(64) NOT NULL,
                                  `expire` datetime NOT NULL,
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `events` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `title` varchar(75) NOT NULL DEFAULT '',
                          `event_date` datetime NOT NULL,
                          `logo` varchar(250) DEFAULT '',
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_tickets` (
                                `user_id` int(11) NOT NULL,
                                `event_id` int(11) NOT NULL,
                                `ticket_count` smallint(6) NOT NULL,
                                KEY `user_id` (`user_id`),
                                KEY `event_id` (`event_id`),
                                CONSTRAINT `user_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                                CONSTRAINT `user_tickets_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


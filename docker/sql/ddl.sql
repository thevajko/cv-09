SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `login`       varchar(100) NOT NULL,
    `last_action` datetime     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `recipient` varchar(100) DEFAULT NULL,
    `author`    varchar(100) NOT NULL,
    `created`   datetime     DEFAULT NULL,
    `message`   text         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_slovak_ci;

INSERT INTO `messages` (`id`, `recipient`, `author`, `created`, `message`)
VALUES (1, 'patrik', 'peter', NULL, 'Ahoj, ako sa máš?'),
       (2, 'peter', 'patrik', NULL, 'Pozdravujem ťa, mám sa dobre.');
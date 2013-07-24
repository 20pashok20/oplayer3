
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- cache
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(64) NOT NULL,
    `value` LONGTEXT NOT NULL,
    `expiredAt` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `key` (`key`),
    INDEX `key_expiredAt` (`key`, `expiredAt`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- playlist
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `playlist`;

CREATE TABLE `playlist`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `userId` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `position` INTEGER,
    `cnt` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `userId` (`userId`),
    CONSTRAINT `playlist_ibfk_1`
        FOREIGN KEY (`userId`)
        REFERENCES `user` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- playlist_track
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `playlist_track`;

CREATE TABLE `playlist_track`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `playlistId` INTEGER NOT NULL,
    `track` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `playlistId` (`playlistId`),
    CONSTRAINT `playlist_track_ibfk_1`
        FOREIGN KEY (`playlistId`)
        REFERENCES `playlist` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `isadmin` INTEGER,
    `name` VARCHAR(255),
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `confirmationtoken` VARCHAR(255),
    `salt` VARCHAR(255),
    `lastvisit` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `confirmed` TINYINT(1),
    PRIMARY KEY (`id`),
    INDEX `email` (`email`),
    INDEX `email_password` (`email`, `password`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_session
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_session`;

CREATE TABLE `user_session`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `userId` INTEGER NOT NULL,
    `sesskey` VARCHAR(64),
    `expiredAt` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `userId` (`userId`),
    INDEX `sess_expiredAt` (`sesskey`, `expiredAt`),
    CONSTRAINT `user_session_ibfk_2`
        FOREIGN KEY (`userId`)
        REFERENCES `user` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

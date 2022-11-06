USE `todo_list`;

DROP TABLE IF EXISTS `task`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
	`id`            INT PRIMARY KEY AUTO_INCREMENT,
	`email`         VARCHAR(512)  NOT NULL UNIQUE,
	`password_hash` VARCHAR(1024) NOT NULL,
	`name`          VARCHAR(255)  NOT NULL
);

CREATE TABLE `task` (
	`id`          INT PRIMARY KEY AUTO_INCREMENT,
	`user_id`     INT                      NOT NULL,
	`description` TEXT                     NOT NULL,
	`status`      ENUM ('Pending', 'Done') NOT NULL DEFAULT 'Pending',
	FOREIGN KEY (`user_id`)
		REFERENCES `user` (`id`)
);

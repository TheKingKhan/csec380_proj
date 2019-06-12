DROP DATABASE IF EXISTS Skitter;
CREATE DATABASE Skitter;
USE Skitter;

CREATE TABLE Users (userid INT PRIMARY KEY AUTO_INCREMENT NOT NULL, \
	password VARCHAR(75) NOT NULL, \
	username VARCHAR(30) NOT NULL, \
	email VARCHAR(100) NOT NULL, \
	profile_pic VARCHAR(100) DEFAULT "img/default.png" NOT NULL, \
	following VARCHAR(10000) NOT NULL
);
CREATE TABLE `videos` (
	`userid` INT(11),
	`file_name` VARCHAR(100) CHARACTER SET ascii COLLATE ascii_general_ci,
	`display_name` VARCHAR(100) CHARACTER SET ascii COLLATE ascii_general_ci,
	PRIMARY KEY (`file_name`)
);


INSERT INTO Users VALUES (1, "password", "gmiller", "gem1086@g.rit.edu", "img/default.png", "1,2,3,4");
INSERT INTO Users VALUES (2, "password", "mgoldman", "somename@somesite.com", "img/default.png", "1,3");
INSERT INTO Users VALUES (3, "password", "markmark", "markiemark@mark.com", "img/default.png", "1");
INSERT INTO Users VALUES (4, "password", "taffealex", "alex.taffe@taffenetwork.com", "img/default.png", "1");

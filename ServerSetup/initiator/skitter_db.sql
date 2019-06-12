DROP DATABASE IF EXISTS Skitter;
CREATE DATABASE Skitter;
USE Skitter;

CREATE TABLE Users (userid INT PRIMARY KEY AUTO_INCREMENT NOT NULL, \
	password VARCHAR(75) NOT NULL, \
	username VARCHAR(200) NOT NULL, \
	email VARCHAR(100) NOT NULL, \
	profile_pic VARCHAR(100) DEFAULT "img/default.png" NOT NULL, \
	following VARCHAR(10000) NOT NULL, \
	sessID VARCHAR(3) DEFAULT NULL
);
CREATE TABLE `videos` (
	`userid` INT(11),
	`file_name` VARCHAR(100) CHARACTER SET ascii COLLATE ascii_general_ci,
	`display_name` VARCHAR(100) CHARACTER SET ascii COLLATE ascii_general_ci,
	PRIMARY KEY (`file_name`)
);


INSERT INTO Users VALUES (1, "password", "Nifty Fella", "test@g.rit.edu", "img/default.png", "1,2,3,4", NULL);
INSERT INTO Users VALUES (2, "password", "Cool Cohort", "somename@somesite.com", "img/default.png", "1,3", NULL);
INSERT INTO Users VALUES (3, "password", "markiemarkie", "markiemark@mark.com", "img/default.png", "1", NULL);
INSERT INTO Users VALUES (4, "password", "crock", "party@rit.edu", "img/default.png", "1", NULL);

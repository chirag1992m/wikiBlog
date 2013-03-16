				/* MYSQL commands for creating tables */
mysql> CREATE TABLE `user` (
  `userID` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `emailID` varchar(100) NOT NULL,
  `password` varchar(165) NOT NULL,
  `profilePicPath` varchar(100) DEFAULT NULL,
  `aboutMe` text,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username_uk` (`username`),
  UNIQUE KEY `email_uk` (`emailID`),
  KEY `username_ind` (`username`) USING BTREE
);
Query OK, 0 rows affected (0.12 sec)

mysql> CREATE TABLE `interests` (
  `userID` int(5) NOT NULL,
  `interest` varchar(50) NOT NULL,
  PRIMARY KEY (`userID`,`interest`),
  CONSTRAINT `interests_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.11 sec)

mysql> CREATE TABLE `follows` (
  `follower` int(5) NOT NULL,
  `followee` int(5) NOT NULL,
  PRIMARY KEY (`follower`,`followee`),
  KEY `followee` (`followee`),
  CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followee`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.16 sec)

mysql> CREATE TABLE `article` (
  `articleID` int(5) NOT NULL AUTO_INCREMENT,
  `articleFilePath` varchar(100) NOT NULL,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`articleID`)
);
Query OK, 0 rows affected (0.13 sec)

mysql> CREATE TABLE `writesArticle` (
  `userID` int(5) NOT NULL,
  `articleId` int(5) NOT NULL,
  `version` int(3) NOT NULL,
  `articleFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`articleId`,`version`),
  KEY `articleId` (`articleId`),
  CONSTRAINT `writesArticle_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `writesArticle_ibfk_2` FOREIGN KEY (`articleId`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.14 sec)

mysql> CREATE TABLE `activeUsers` (
  `userID` int(5) NOT NULL,
  `randomString` char(10) NOT NULL,
  `ip` char(15) DEFAULT NULL,
  `loggedInAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`randomString`),
  CONSTRAINT `activeUsers_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.11 sec)

mysql> CREATE TABLE `blog` (
  `blogID` int(5) NOT NULL AUTO_INCREMENT,
  `userID` int(5) NOT NULL,
  `blogFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blogID`),
  KEY `user_ind` (`userID`) USING BTREE,
  CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.10 sec)

mysql> CREATE TABLE `comment` (
  `commentID` int(3) NOT NULL,
  `blogID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `comment` text NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commentID`,`blogID`),
  KEY `user_ind` (`userID`) USING BTREE,
  KEY `blogID` (`blogID`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.15 sec)

mysql> CREATE TABLE `reply` (
  `replyID` int(2) NOT NULL,
  `commentID` int(3) NOT NULL,
  `blogID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `reply` text NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`replyID`,`commentID`,`blogID`),
  KEY `user_ind` (`userID`) USING BTREE,
  KEY `commentID` (`commentID`,`blogID`),
  CONSTRAINT `reply_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reply_ibfk_2` FOREIGN KEY (`commentID`, `blogID`) REFERENCES `comment` (`commentID`, `blogID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.17 sec)

mysql> CREATE TABLE `likes_dislikes` (
  `userID` int(5) NOT NULL,
  `blogID` int(5) NOT NULL,
  `choice` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`blogID`),
  KEY `blogID` (`blogID`),
  CONSTRAINT `likes_dislikes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_dislikes_ibfk_2` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.16 sec)

mysql> CREATE TABLE `articleTag` (
  `articleID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`articleID`,`tag`),
  CONSTRAINT `articleTag_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.13 sec)

mysql> CREATE TABLE `blogTag` (
  `blogID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`blogID`,`tag`),
  CONSTRAINT `blogTag_ibfk_1` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.10 sec)

mysql> CREATE TABLE `articleKeyword` (
  `articleID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`articleID`,`keyword`),
  CONSTRAINT `articleKeyword_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.12 sec)

mysql> CREATE TABLE `blogKeyword` (
  `blogID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`blogID`,`keyword`),
  CONSTRAINT `blogKeyword_ibfk_1` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
);
Query OK, 0 rows affected (0.12 sec)

mysql> show tables;
+----------------------------+
| Tables_in_mchirag_wikiblog |
+----------------------------+
| activeUsers                |
| article                    |
| articleKeyword             |
| articleTag                 |
| blog                       |
| blogKeyword                |
| blogTag                    |
| comment                    |
| follows                    |
| interests                  |
| likes_dislikes             |
| reply                      |
| user                       |
| writesArticle              |
+----------------------------+
14 rows in set (0.00 sec)
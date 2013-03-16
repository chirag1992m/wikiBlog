mysql> create table user(
    -> userID int(5) not null,
    -> username varchar(30) not null,
    -> firstName varchar(30) not null,
    -> lastName varchar(30) not null,
    -> emailID varchar(100) not null,
    -> password varchar(165) not null,
    -> profilePicPath varchar(100),
    -> aboutMe text,
    -> primary key(userID),
    -> constraint username_uk unique(username),
    -> constraint email_uk unique(emailID),
    -> index username_ind using btree(username));
Query OK, 0 rows affected (0.12 sec)

mysql> create table interests (
    -> userID int(5) not null,
    -> interest varchar(50) not null,
    -> primary key (userID, interest),
    -> constraint user_fk foreign key userID references user on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'references user on delete cascade on update cascade))' at line 5
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), constraint user_fk foreign key userID references user(userID) on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'references user(userID) on delete cascade on update cascade))' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), constraint user_fk foreign key (userID) references user(userID) on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ')' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key userID references user on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'references user on delete cascade on update cascade))' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key (userID) references user on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ')' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key (userID) references user(userID) on delete cascade on update cascade));
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ')' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key (userID) references user on delete cascade on update cascade);
ERROR 1005 (HY000): Can't create table 'mchirag_wikiblog.interests' (errno: 150)
mysql> drop table interests;
ERROR 1051 (42S02): Unknown table 'interests'
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.11 sec)

mysql> create table follows (
    -> follower int(5) not null,
    -> followee int(5) not null,
    -> primary key (follower, followeee),
    -> foreign key (follower) references user(userID) on delete cascade on update cascade,
    -> foreign key (follower) references user(userID) on delete cascade on update cascade);
ERROR 1072 (42000): Key column 'followeee' doesn't exist in table
mysql> create table follows ( follower int(5) not null, followee int(5) not null, primary key (follower, followee), foreign key (follower) references user(userID) on delete cascade on update cascade, foreign key (followee) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.16 sec)

mysql> create table article(
    -> articleID int(5) not null,
    -> articleFilePath varchar(100) not null,
    -> lastModified timestamp not null,
    -> primary key (articleID));
Query OK, 0 rows affected (0.13 sec)

mysql> create table writesArticle(
    -> userID int(5) not null,
    -> articleID \c
mysql> create table writesArticle(
    -> userID int(5) not null,
    -> articleId int(5) not null,
    -> version int(3) not null,
    -> articleFilePath varchar(100) not null,
    -> writtenAt timestamp not null,
    -> primary key(userID, articleID, version),
    -> foreign key (userID) references user(userID) on delete cascade on update cascade,
    -> foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.14 sec)

mysql> create table activeUsers(
    -> userID int(5) not null,
    -> randomString char(10) not null,
    -> ip char(15),
    -> loggedInAt timestamp,
    -> primary key (userID, randomString),
    -> foreign key (userID) referenced user(userID) on delete cascade on delete cascade);
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'referenced user(userID) on delete cascade on delete cascade)' at line 7
mysql> create table activeUsers( userID int(5) not null, randomString char(10) not null, ip char(15), loggedInAt timestamp, primary key (userID, randomString), foreign key (userID) references user(userID) on delete cascade on delete cascade);
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'delete cascade)' at line 1
mysql> create table activeUsers( userID int(5) not null, randomString char(10) not null, ip char(15), loggedInAt timestamp, primary key (userID, randomString), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.16 sec)

mysql> create table blog (
    -> blogID int(5) not null,
    -> \c
mysql> 
mysql> 
mysql> 
mysql> 
mysql> 
mysql> 
mysql> 
mysql> 
mysql> show tables;
+----------------------------+
| Tables_in_mchirag_wikiblog |
+----------------------------+
| activeUsers                |
| article                    |
| follows                    |
| interests                  |
| user                       |
| writesArticle              |
+----------------------------+
6 rows in set (0.00 sec)

mysql> drop table activeUsers;
Query OK, 0 rows affected (0.06 sec)

mysql> drop table article;
ERROR 1217 (23000): Cannot delete or update a parent row: a foreign key constraint fails
mysql> drop table user;
ERROR 1217 (23000): Cannot delete or update a parent row: a foreign key constraint fails
mysql> drop table follows;
Query OK, 0 rows affected (0.07 sec)

mysql> drop table interests;
Query OK, 0 rows affected (0.03 sec)

mysql> drop table writesArticles;
ERROR 1051 (42S02): Unknown table 'writesArticles'
mysql> drop table writesArticle;
Query OK, 0 rows affected (0.05 sec)

mysql> drop table article;
Query OK, 0 rows affected (0.06 sec)

mysql> drop table user;
Query OK, 0 rows affected (0.06 sec)

mysql> create table user( userID int(5) not null, username varchar(30) not null, firstName varchar(30) not null, lastName varchar(30) not null, emailID varchar(100) not null, password varchar(165) not null, profilePicPath varchar(100), aboutMe text, primary key(userID), constraint username_uk unique(username), constraint email_uk unique(emailID), index username_ind using btree(username));
Query OK, 0 rows affected (0.16 sec)

mysql> drop table user;
Query OK, 0 rows affected (0.07 sec)

mysql> create table user( userID int(5) not null auto_increment, username varchar(30) not null, firstName varchar(30) not null, lastName varchar(30) not null, emailID varchar(100) not null, password varchar(165) not null, profilePicPath varchar(100), aboutMe text, primary key(userID), constraint username_uk unique(username), constraint email_uk unique(emailID), index username_ind using btree(username));
Query OK, 0 rows affected (0.14 sec)

mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key (userID, interest), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.12 sec)

mysql> create table follows ( follower int(5) not null, followee int(5) not null, primary key (follower, followee), foreign key (follower) references user(userID) on delete cascade on update cascade, foreign key (followee) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.17 sec)

mysql> create table article( articleID int(5) not null auto_increment, articleFilePath varchar(100) not null, lastModified timestamp not null default CURRENT_TIMESTAMP, primary key (articleID));
Query OK, 0 rows affected (0.14 sec)

mysql> create table writesArticle( userID int(5) not null, articleId int(5) not null, version int(3) not null, articleFilePath varchar(100) not null, writtenAt timestamp not null, primary key(userID, articleID, version), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.20 sec)

mysql> create table activeUsers( userID int(5) not null, randomString char(10) not null, ip char(15), loggedInAt timestamp default CURRENT_TIMESTAMP, primary key (userID, randomString), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.11 sec)

mysql> create table blog (
    -> blogID int(5) not null auto_increment,
    -> userID int(5) not null,
    -> blogFilePath varchar(100) not null,
    -> writtenAt timestamp not null default CURRENT_TIMESTAMP,
    -> primary key(blogID),
    -> index user_ind using btree(userID),
    -> foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.10 sec)

mysql> create table comment (
    -> commentID int(3) not null,
    -> blogID int(5) not null,
    -> userID int(5) not null,
    -> comment text not null,
    -> writtenAt timestamp not null default CURRENT_TIMESTAMP,
    -> primary key(commentID, blogID),
    -> index user_ind using btree(userID),
    -> foreign key (userID) references user(userID) on delete cascade on update cascade,
    -> foreign key (blogID) references user(blogID) on delete cascade on update cascade);
ERROR 1005 (HY000): Can't create table 'mchirag_wikiblog.comment' (errno: 150)
mysql> create table comment ( commentID int(3) not null, blogID int(5) not null, userID int(5) not null, comment text not null, writtenAt timestamp not null default CURRENT_TIMESTAMP, primary key(commentID, blogID), index user_ind using btree(userID), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (blogID) references blog(blogID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.15 sec)

mysql> create table reply (
    -> replyID int(2) not null,
    -> commentID int(3) not null,
    -> blogID int(5) not null,
    -> userID int(5) not null,
    -> reply text not null,
    -> writtenAt timestamp not null default CURRENT_TIMESTAMP,
    -> primary key (replyID, commentID, blogID),
    -> index user_ind using btree (userID),
    -> foreign key (userID) references user(userID) on delete cascade on update cascade,
    -> foreign key (blogID) references blog(blogID) on delete cascade on update cascade,
    -> foreign key (userID) references comment(commentID) on delete cascade on update cascade\c
mysql> create table reply ( replyID int(2) not null, commentID int(3) not null, blogID int(5) not null, userID int(5) not null, reply text not null, writtenAt timestamp not null default CURRENT_TIMESTAMP, primary key (replyID, commentID, blogID), index user_ind using btree (userID), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (commentID, blogID) references comment(commentID, blogID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.17 sec)

mysql> create table likes_dislikes(
    -> userID int(5) not null,
    -> blogID int(5) not null,
    -> choice boolean not null,
    -> primary key(userID, blogID),
    -> foreign key (userID) references user(userID) on delete cascade on update cascade,
    -> foreign key (blogID) references blog(blogID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.16 sec)

mysql> create table articleTag (
    -> articleID int(5) not null,
    -> tag varchar(100) not null,
    -> primary key (articleID, tag),
    -> foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.13 sec)

mysql> create table blogTag ( blogID int(5) not null, tag varchar(100) not null, primary key (blogID, tag), foreign key (blogID) references blog(blogID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.10 sec)

mysql> create table articleKeyword ( articleID int(5) not null, keyword varchar(100) not null, occurences int(2) not null default 1, primary key (articleID, keyword), foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.12 sec)

mysql> create table blogKeyword ( blogID int(5) not null, keyword varchar(100) not null, occurences int(2) not null default 1, primary key (blogID, keyword), foreign key (blogID) references blog(blogID) on delete cascade on update cascade);
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

mysql> show create table user;
+-------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
+-------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| user  | CREATE TABLE `user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table interests;
+-----------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table     | Create Table                                                                                                                                                                                                                                                                                  |
+-----------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| interests | CREATE TABLE `interests` (
  `userID` int(5) NOT NULL,
  `interest` varchar(50) NOT NULL,
  PRIMARY KEY (`userID`,`interest`),
  CONSTRAINT `interests_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-----------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table follows;
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table   | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                       |
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| follows | CREATE TABLE `follows` (
  `follower` int(5) NOT NULL,
  `followee` int(5) NOT NULL,
  PRIMARY KEY (`follower`,`followee`),
  KEY `followee` (`followee`),
  CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followee`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table articlel
    -> ;
ERROR 1146 (42S02): Table 'mchirag_wikiblog.articlel' doesn't exist
mysql> show create table article;
+---------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table   | Create Table                                                                                                                                                                                                                                        |
+---------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| article | CREATE TABLE `article` (
  `articleID` int(5) NOT NULL AUTO_INCREMENT,
  `articleFilePath` varchar(100) NOT NULL,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`articleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+---------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table writesArticle;
+---------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table         | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
+---------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| writesArticle | CREATE TABLE `writesArticle` (
  `userID` int(5) NOT NULL,
  `articleId` int(5) NOT NULL,
  `version` int(3) NOT NULL,
  `articleFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`articleId`,`version`),
  KEY `articleId` (`articleId`),
  CONSTRAINT `writesArticle_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `writesArticle_ibfk_2` FOREIGN KEY (`articleId`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+---------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table activeUsers;
+-------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table       | Create Table                                                                                                                                                                                                                                                                                                                                                                                      |
+-------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| activeUsers | CREATE TABLE `activeUsers` (
  `userID` int(5) NOT NULL,
  `randomString` char(10) NOT NULL,
  `ip` char(15) DEFAULT NULL,
  `loggedInAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`randomString`),
  CONSTRAINT `activeUsers_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table blog;
+-------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                  |
+-------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| blog  | CREATE TABLE `blog` (
  `blogID` int(5) NOT NULL AUTO_INCREMENT,
  `userID` int(5) NOT NULL,
  `blogFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blogID`),
  KEY `user_ind` (`userID`) USING BTREE,
  CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table comment;
+---------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table   | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
+---------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| comment | CREATE TABLE `comment` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+---------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table reply;
+-------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
+-------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| reply | CREATE TABLE `reply` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table likes_dislikes;
+----------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table          | Create Table                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
+----------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| likes_dislikes | CREATE TABLE `likes_dislikes` (
  `userID` int(5) NOT NULL,
  `blogID` int(5) NOT NULL,
  `choice` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`blogID`),
  KEY `blogID` (`blogID`),
  CONSTRAINT `likes_dislikes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_dislikes_ibfk_2` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+----------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table articleTag;
+------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table      | Create Table                                                                                                                                                                                                                                                                                          |
+------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| articleTag | CREATE TABLE `articleTag` (
  `articleID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`articleID`,`tag`),
  CONSTRAINT `articleTag_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table blogTag;
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table   | Create Table                                                                                                                                                                                                                                                                     |
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| blogTag | CREATE TABLE `blogTag` (
  `blogID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`blogID`,`tag`),
  CONSTRAINT `blogTag_ibfk_1` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+---------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table articleKeyword;
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table          | Create Table                                                                                                                                                                                                                                                                                                                                                      |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| articleKeyword | CREATE TABLE `articleKeyword` (
  `articleID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`articleID`,`keyword`),
  CONSTRAINT `articleKeyword_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> show create table blogKeyword;
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table       | Create Table                                                                                                                                                                                                                                                                                                                                 |
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| blogKeyword | CREATE TABLE `blogKeyword` (
  `blogID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`blogID`,`keyword`),
  CONSTRAINT `blogKeyword_ibfk_1` FOREIGN KEY (`blogID`) REFERENCES `blog` (`blogID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

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

mysql> exit

--
-- Table structure for table `user`
--
CREATE TABLE `user` (
  `userID` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) DEFAULT NULL,
  `emailID` varchar(50) NOT NULL,
  `password` varchar(165) NOT NULL,
  `profilePicUrl` varchar(100) DEFAULT NULL,
  `aboutMe` text,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `emailID` (`emailID`),
  KEY `username_index` (`username`) USING BTREE
);

--
-- Table structure for table `follows`
--
CREATE TABLE `follows` (
  `follower` int(5) NOT NULL,
  `followee` int(5) NOT NULL,
  PRIMARY KEY (`follower`,`followee`),
  KEY `followee` (`followee`),
  CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followee`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `interests`
--
CREATE TABLE `interests` (
  `userID` int(5) NOT NULL,
  `interest` varchar(50) NOT NULL,
  PRIMARY KEY (`userID`,`interest`),
  CONSTRAINT `interests_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `activeUsers`
--
CREATE TABLE `activeUsers` (
  `randomString` char(10) NOT NULL,
  `userID` int(5) NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `loggedInAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`randomString`,`userID`),
  KEY `userID` (`userID`),
  CONSTRAINT `activeUsers_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `article`
--
CREATE TABLE `article` (
  `articleID` int(5) NOT NULL AUTO_INCREMENT,
  `articleFilePath` varchar(100) NOT NULL,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `articleName` varchar(100) NOT NULL DEFAULT 'new article',
  PRIMARY KEY (`articleID`)
);

--
-- Table structure for table `writesArticle`
--
CREATE TABLE `writesArticle` (
  `userID` int(5) NOT NULL,
  `articleID` int(5) NOT NULL,
  `version` int(3) NOT NULL,
  `articleFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`articleID`,`version`),
  KEY `userid_index` (`userID`) USING BTREE,
  CONSTRAINT `writesArticle_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `writesArticle_ibfk_2` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `articleKeyword`
--
CREATE TABLE `articleKeyword` (
  `articleID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`articleID`,`keyword`),
  CONSTRAINT `articleKeyword_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `articleRating`
--
CREATE TABLE `articleRating` (
  `userID` int(5) NOT NULL,
  `articleID` int(5) NOT NULL,
  `version` int(3) NOT NULL,
  `rating` int(1) NOT NULL,
  PRIMARY KEY (`userID`,`articleID`,`version`),
  KEY `articleID` (`articleID`,`version`),
  CONSTRAINT `articleRating_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `articleRating_ibfk_2` FOREIGN KEY (`articleID`, `version`) REFERENCES `writesArticle` (`articleID`, `version`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `articleTag`
--
CREATE TABLE `articleTag` (
  `articleID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`articleID`,`tag`),
  CONSTRAINT `articleTag_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `post`
--
CREATE TABLE `post` (
  `userID` int(5) NOT NULL,
  `postID` int(5) NOT NULL AUTO_INCREMENT,
  `postFilePath` varchar(100) NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `postName` varchar(100) NOT NULL DEFAULT 'new post',
  PRIMARY KEY (`postID`),
  KEY `userid_index` (`userID`) USING BTREE,
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `comment`
--
CREATE TABLE `comment` (
  `commentID` int(4) NOT NULL,
  `postID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `comment` text NOT NULL,
  `writtenAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commentID`,`postID`),
  KEY `userid_index` (`userID`) USING BTREE,
  KEY `postID` (`postID`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`postID`) REFERENCES `post` (`postID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `commentThread`
--
CREATE TABLE `commentThread` (
  `childID` int(4) NOT NULL,
  `parentID` int(4) NOT NULL,
  `postID` int(5) NOT NULL,
  PRIMARY KEY (`childID`,`parentID`,`postID`),
  KEY `childID` (`childID`,`postID`),
  KEY `parentID` (`parentID`,`postID`),
  CONSTRAINT `commentThread_ibfk_1` FOREIGN KEY (`childID`, `postID`) REFERENCES `comment` (`commentID`, `postID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commentThread_ibfk_2` FOREIGN KEY (`parentID`, `postID`) REFERENCES `comment` (`commentID`, `postID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `likesDislikes`
--
CREATE TABLE `likesDislikes` (
  `userID` int(5) NOT NULL,
  `postID` int(5) NOT NULL,
  `choice` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`postID`),
  KEY `postID` (`postID`),
  CONSTRAINT `likesDislikes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likesDislikes_ibfk_2` FOREIGN KEY (`postID`) REFERENCES `post` (`postID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `postKeyword`
--
CREATE TABLE `postKeyword` (
  `postID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`postID`,`keyword`),
  CONSTRAINT `postKeyword_ibfk_1` FOREIGN KEY (`postID`) REFERENCES `post` (`postID`) ON DELETE CASCADE ON UPDATE CASCADE
);

--
-- Table structure for table `postTag`
--
CREATE TABLE `postTag` (
  `postID` int(5) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`postID`,`tag`),
  CONSTRAINT `postTag_ibfk_1` FOREIGN KEY (`postID`) REFERENCES `post` (`postID`) ON DELETE CASCADE ON UPDATE CASCADE
);
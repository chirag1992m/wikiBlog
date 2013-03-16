/* check if a user already exists */
SELECT userID from user WHERE username = 'username';

/* Query to add a new user */
INSERT INTO user(username, firstName, lastName, emailID, password, profilePicPath, aboutMe) VALUES ('username', 'first', 'last', 'firstlast@gmail.com', 'passwordinSHAencoding', '/home/user/pic.png', 'hey I am first last');
SELECT userID from user WHERE username = 'username';
INSERT INTO interests VALUES (userID, 'interest'); /* will run in loop for all the interests given */

/* Query to autheticate a user */
SELECT * FROM user WHERE username = 'givenUsername';
/* match the password, if(valid) go ahead: else skip */
INSERT INTO activeUsers(userID, randomString, ip) VALUES (userID, 'randomString', 'ip');

/* query to add a follower */
INSERT INTO follows(follower, followee) VALUES (userID1, userID2);
/* get/count the number of followers */
SELECT follower from follows WHERE followee = userID;
SELECT count(*) from follows WHERE followee = userID;
/* get/count the number of following */
SELECT followee from follows WHERE follower = userID;
SELECT count(*) from follows WHERE follower = userID;

/* query to get the details for a user */
select * from user WHERE username = 'givenUsername';
select * from user WHERE userID = 'userID';

/* query to add a new article */
/* LOCK table for writing */
/* first add the article */
INSERT INTO article(articleFilePath) VALUES ('/where/the/article/fileis');
/* get its articleID generated */
SELECT max(articleID) from article;
/* UNLOCK table */
/* add a entry in the writesArticle */
INSERT INTO writesArticle(userID, articleID, version, articleFilePath, writtenAt) VALUES (userID, articleID, 1, 'path/to/file', );

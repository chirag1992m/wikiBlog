mysql> create table user (
    -> userID int(5) not null,
    -> username int\c
mysql> create table user ( userID int(5) not null auto_increment, username varchar(30) not null, firstName varchar(30) not null, lastName varchar(30), emailID varchar(50) not null, password varchar(165) not null, profilePicUrl varchar(100), aboutMe text, primary key(userID), unique(username), unique(emailID), index username_index using btree);
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ')' at line 1
mysql> create table user ( userID int(5) not null auto_increment, username varchar(30) not null, firstName varchar(30) not null, lastName varchar(30), emailID varchar(50) not null, password varchar(165) not null, profilePicUrl varchar(100), aboutMe text, primary key(userID), unique(username), unique(emailID), index username_index using btree(username));
Query OK, 0 rows affected (0.11 sec)

mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key(userID, interest), foreign key (userID) references user(userID) on delete on update cascade);
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'on update cascade)' at line 1
mysql> create table interests ( userID int(5) not null, interest varchar(50) not null, primary key(userID, interest), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.12 sec)

mysql> create table interests ( follower int(5) not null, followee int(5) not null, primary key(follower, followee), foreign key (follower) references user(userID) on delete cascade on update cascade, foreign key (followee) references user(userID) on delete cascade on update cascade);
ERROR 1050 (42S01): Table 'interests' already exists
mysql> create table follows ( follower int(5) not null, followee int(5) not null, primary key(follower, followee), foreign key (follower) references user(userID) on delete cascade on update cascade, foreign key (followee) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.17 sec)

mysql> create table article( articleID int(5) not null auto_increment, articleFilePath varchar(100) not null, lastModified timestamp not null default CURRENT_TIMESTAMP, primary key(articleID));
Query OK, 0 rows affected (0.14 sec)

mysql> create table writesArticle ( userID int(5) not null, articleID int(5) not null, version int(3) not null, articleFilePath varchar(100) not null, writtenAt timestamp not null, primary key(articleID, version), index userid_index using btree(userID), foreign key (userID) references user on delete cascade on update cascade, foreign key (articleID) references article on delete cascade on update cascade);
ERROR 1005 (HY000): Cant create table 'mchirag_wikiblog.writesArticle' (errno: 150)
mysql> create table writesArticle ( userID int(5) not null, articleID int(5) not null, version int(3) not null, articleFilePath varchar(100) not null, writtenAt timestamp not null, primary key(articleID, version), index userid_index using btree(userID), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.13 sec)

mysql> create table articleRating ( userID int(5) not null, articleID int(5) not null, version int(3) not null, rating int(1) not null, primary key(userID, articleID, version), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (articleID, version) references writesArticle(articleID, version) on delete cascade on update cascade);
Query OK, 0 rows affected (0.15 sec)

mysql> create table actieUSers( randomString char(10) not null, userID int(5) not null, ip varchar(15), loggedInAt timestamp default CURRENT_TIMESTAMP, primary key(randomString, userID), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.11 sec)

mysql> create table post (userID int(5) not null, postID int(5) not null auto_increment, postFilePath varchar(100) not null, writtenAt timestamp not null default CURRENT_TIMESTAMP, primary key(postID), index userid_index using btree(userID), foreign key (userID) references user(userID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.12 sec)

mysql> create table comment (commentID int(4) not null, postID int(5) not null, userID int(5) not null, comment text not null, writtenAt timestamp not null default CURRENT_TIMESTAMP, primary key(commentID, postID), index userid_index using btree(userID), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (postID) references post(postID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.18 sec)

mysql> create tabe commentThread (childID int(4) not null, parentID int(4) not null, postID int(5) not null, primary key (childID, parentID, postID), foreign key (childID, postID) references comment(commentID, postID) on delete cascade on update cascade, foreign key (parentID, postID) references comment(commentID, postID) on delete cascade on update cascade);
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'tabe commentThread (childID int(4) not null, parentID int(4) not null, postID in' at line 1
mysql> create table commentThread (childID int(4) not null, parentID int(4) not null, postID int(5) not null, primary key (childID, parentID, postID), foreign key (childID, postID) references comment(commentID, postID) on delete cascade on update cascade, foreign key (parentID, postID) references comment(commentID, postID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.16 sec)

mysql> create table likes_dislikes (userID int(5) not null, postID int(5) not null, choice boolean not null, primary key(userID, postID), foreign key (userID) references user(userID) on delete cascade on update cascade, foreign key (postID) references post(postID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.18 sec)

mysql> create table articleTag (articleID int(5) not null, tag varchar(100) not null, url varchar(200), primary key (articleID, tag), foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.23 sec)

mysql> create table postTag (postID int(5) not null, tag varchar(100) not null, primary key (postID, tag), foreign key (postID) references post(postID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.11 sec)

mysql> create table articleKeyword (articleID int(5) not null, keyword varchar(100) not null, occurences int(2) not null default 1, primary key (articleID, keyword), foreign key (articleID) references article(articleID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.13 sec)

mysql> create table postKeyword (postID int(5) not null, keyword varchar(100) not null, occurences int(2) not null default 1, primary key (postID, keyword), foreign key (postID) references post(postID) on delete cascade on update cascade);
Query OK, 0 rows affected (0.10 sec)

mysql> show tables;
+----------------------------+
| Tables_in_mchirag_wikiblog |
+----------------------------+
| actieUSers                 |
| article                    |
| articleKeyword             |
| articleRating              |
| articleTag                 |
| comment                    |
| commentThread              |
| follows                    |
| interests                  |
| likes_dislikes             |
| post                       |
| postKeyword                |
| postTag                    |
| user                       |
| writesArticle              |
+----------------------------+
15 rows in set (0.00 sec)

mysql> alter table actieUSers rename to activeUsers;
Query OK, 0 rows affected (0.05 sec)

mysql> alter table likes_dislikes rename to likesDislikes;
Query OK, 0 rows affected (0.08 sec)

mysql> exit

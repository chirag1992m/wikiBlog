-- To create a new profile/user
	INSERT INTO user(username, firstName, lastName, emailID, password, profilePicUrl, aboutMe) VALUES ('username', 'first', 'last', 'firstlast@gmail.com', 'passwordinSHAencoding', '/home/user/pic.png', 'hey I am first last');
	SELECT userID from user WHERE username = 'username';	-- here SCOPE_IDENTITY() or @@IDENTITY or LAST_INSERT_ID() or @@Scope_Identity can be used to retrieve the most recently inserted tables ID.
	
	-- To add the user interests, loop through all the interests and add them one by one.
	INSERT INTO interests VALUES (userID, 'interest');

-- To check if a username already exixts
	SELECT userID from user WHERE username = 'username';

-- Query to authenticate a user provided username and password
	SELECT * FROM user WHERE username = 'givenUsername';
	-- match the password, if(valid) create a session: else skip
	INSERT INTO activeUsers(userID, randomString, ip) VALUES (userID, 'randomString', 'ip');

-- Query to add a follower
	INSERT INTO follows(follower, followee) VALUES (userID1, userID2);

-- get/count the number of followers
	SELECT follower from follows WHERE followee = userID;
	SELECT count(*) from follows WHERE followee = userID;
-- get/count the number of following
	SELECT followee from follows WHERE follower = userID;
	SELECT count(*) from follows WHERE follower = userID;

-- query to get the details for a user
	select * from user WHERE username = 'givenUsername'; -- given a username
	select * from user WHERE userID = 'userID'; -- given a userID

-- Add a new article
	-- LOCK TABLE
	INSERT INTO article(articleName, articleFilePath) VALUES ('newArticle', '/where/the/article/fileis'); -- add the article
	-- get its articleID generated
	SELECT max(articleID) from article;	-- here SCOPE_IDENTITY() or @@IDENTITY or LAST_INSERT_ID() or @@Scope_Identity can be used to retrieve the most recently inserted tables ID.
	-- UNLOCK table
	
	-- add a entry in the writesArticle
	select lastModified from article where articleID = articleID;
	INSERT INTO writesArticle(userID, articleID, version, articleFilePath, writtenAt) VALUES (userID, articleID, 1, 'path/to/file', lastModified);
	
	-- add its tags/references
	insert into articleTag values (articleID, 'tag', null); -- tag
	insert into articleTag values (articleID, 'tag', 'url'); -- references
	
	-- add its keywords
		-- loop through all the keywords count their occurences
	insert into articleKeyword values (articleID, 'keyword', occurences);
	
-- edit an existing article (we can procedure for this)
	-- create a new version
		-- LOCK TABLE
	select max(version) as maxVersion from writesArticle where articleID = articleID; -- check if there is auto_increment on a secondary column in a multiple-column index
	insert into writesArticle(userID, articleID, version, articleFilePath) values (userID, articleID, maxVersion+1, 'path/to/new/file');
	select writtenAt from writesArticle where articleID = articleID and version = maxVersion+1;
		-- UNLOCK TABLE
	
	-- update the article table
	update article set articleFilePath = 'path/to/new/file', lastModified = writtenAt where articleID = articleID;
	
	-- update the tags/references
		-- remove any tag/reference which has been removed
		delete from articleTag where articleID = articleID and tag = 'tag';
		-- add the new tags/references
		insert into articleTag values (articleID, 'tag', null); -- tag
		insert into articleTag values (articleID, 'tag', 'url'); -- references
		
	-- update the article keywords
		-- for every keyword, check if already in the table
		select * from articleKeyword where articleID = articleID and keyword = 'keyword';
		-- if the keyword exists, update the occurence
		update articleKeyword set occurences = newOccurence where articleID = articleID and keyword = 'keyword';
		-- else insert a new entry
		insert into articleKeyword values (articleID, 'keyword', occurences);
		
-- add a new post
		-- LOCK TABLE
	insert into post(userID, postFilePath, postName) values (userID, 'path/to/blog/post/file', 'new blog post');
	select max(postID) from post;	-- here SCOPE_IDENTITY() or @@IDENTITY or LAST_INSERT_ID() or @@Scope_Identity can be used to retrieve the most recently inserted tables ID.
		-- UNLOCK TABLE
	
	-- add the tags
		-- loop through all the tags and insert all the tags
	insert into postTag values (postId, 'tag');
	
	-- add the keywords
		-- loop through all the keywords and insert them
	INSERT into postKeyword values (postId, 'keyword', occurences);

-- add a new comment on a post
		-- LOCK TABLE
	select max(commentID) as maxComment from comment where postID = postId;
	insert into comment(commentID, postID, userID, comment) values (maxComment+1, postID, userID, comment);
		-- UNLOCK TABLE

-- add a reply to a comment
	-- add the reply as a comment in the comment table
	-- add a parent child relationship in commentThread
	insert into commentThread(childID, parentID, postID) values (newCommentID, parentCommentID, postID);

-- when a user like/unlikes a post
	insert into likesDislikes values (userID, postID, 1) -- 1 stands for like
	insert into likesDislikes values (userID, postID, 0) -- 0 stands for dislike

-- get the number of likes/dislikes on a post
	-- like
	select count(userID) from likesDislikes where postID = postId and choice = 1;
	-- dislike
	select count(userID) from likesDislikes where postID = postId and choice = 0;

-- check/get a choice of a user on a post
	select choice from likesDislikes where postID = postId and userID = userID;

-- generate the whole comment thread for a post
	-- first select all the root comments (which has no parent in the commentThread table)
	select userID, comment, writtenAt, commentID from comment C where postID = postId and not exists (select * from commentThread CT where CT.childID = C.commentID);
	-- for every comment get its children in recursion
	select userID, comment, writtenAt, commentID from comment C where commentID in (select childID from commentThread where parentID = commentID and postID = postId);

-- get all the post for a user
	select * from post where userID = userID;

-- get all the articles written/edited by a user (only the latest one if present)
	select * from writesArticle where userID = userID group by articleID having version = (select max(version) from writesArticle where articleID = articleID);

-- get all the articles written/edited by a user (old or new)
	select * from writesArticle where userID = userID;

-- QUERIES DURING SEARCHING OF A Query
	-- first tokenize the query
	-- for every keyword extracted from the query, do the following:
	----------
	-- for article
		-- search in tags
		select articleID from articleTag where tag LIKE '%keywordGiven%';
		-- search in keywords
		select articleID from articleTag where keyword LIKE '%keywordGiven%' order by occurences;

	-- for posts
		-- search in tags
		select postID from postTag where tag LIKE '%keywordGiven%';
		-- search in keywords
		select postID from postTag where keyword LIKE '%keywordGiven%' order by occurences;

	-- for users
		select * from user where username LIKE '%keywordGiven%';
		select userID from interests where interest LIKE '%keywordGiven%';
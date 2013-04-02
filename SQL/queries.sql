-- QUERIES ON ARTICLE
	-- count the number of articles without versioning taking into account
		SELECT count(DISTINCT(articleID)) FROM writesArticle WHERE userID = ?
	-- count the number of articles
		SELECT count(articleID) FROM writesArticle WHERE userID = ?
	-- get the timing of an article using its article-ID
		SELECT lastModified FROM article WHERE articleID = ?
	-- get the user information for an article with a fixed version
		SELECT userID FROM writesArticle WHERE articleID = ? AND version = ?
	-- insert a new article
		INSERT INTO article(articleName, articleText) VALUES (?, ?)
	-- insert a new version of article in the writesArticle
		INSERT INTO writesArticle(userID, articleID, version, articleText, writtenAt) VALUES (?, ?, ?, ?, ?)
	-- insert a new article tag
		INSERT INTO articleTag(articleID, tag, url) VALUES (?, ?, ?)
	-- insert a new keyword in an article with its number of occurences
		INSERT INTO articleKeyword(articleID, keyword, occurences) VALUES (?, ?, ?)
	-- insert a new rating to an article
		INSERT INTO articleRating(userID, articleID, version, rating) VALUES (?, ?, ?, ?)
	-- get the latest article given an article-ID
		SELECT articleText, lastModified, articleName FROM article WHERE articleID = ?
	-- get all the articles written by a particular user taking into account the versioning.
		SELECT articleID, version, articleText, writtenAt FROM writesArticle WHERE userID = ? ORDER BY writtenAt DESC
	-- get all the tags and references for an article.
		SELECT tag, url FROM articleTag WHERE articleID = ?
	-- get the information for an article for a particular version
		SELECT userID, articleText, writtenAt FROM writesArticle WHERE articleID = ? AND version = ?
	-- get the last written/edited version for an article
		SELECT max(version) FROM writesArticle WHERE articleID = ?
	-- get all the information for an article including its version ordered on the basis of version
		SELECT userID, version, writtenAt FROM writesArticle WHERE articleID = ? ORDER BY version
	-- get the average rating for an article
		SELECT avg(rating) FROM articleRating WHERE articleID = ? AND version = ?
	-- get the rating of article for an article by a particular user.
		SELECT avg(rating) FROM articleRating WHERE userID = ? AND articleID = ? AND version = ?
	-- update the details for an article when a new version is written
		UPDATE article SET articleText = ?, lastModified = ? WHERE articleID = ?
	-- this is a part of the search query.
		-- get the all the articles with the given keyword matching its tags.
			SELECT articleID FROM articleTag WHERE tag LIKE ?
		-- get all the articles containing the keyword between its body ordered by the number of occurences for basic ranking.
			SELECT articleID FROM articleKeyword WHERE keyword LIKE ? ORDER BY occurences DESC
	-- get the latest written articles limited by a number
		SELECT articleID, articleText, articleName, lastModified FROM article ORDER BY lastModified DESC LIMIT ?;

-- Queries related to a user
	-- add a new user when a new registration happens
		INSERT INTO user(username, firstName, lastName, emailID, password, profilePicUrl, aboutMe) VALUES (?, ?, ?, ?, ?, ?, ?)
	-- insert interests for the user
		INSERT INTO interests(userID, interest) VALUES (?, ?)
	-- insert an user login details, i.e., when it becomes an active user
		INSERT INTO activeUsers(randomString, userID, ip) VALUES (?, ?, ?)
	-- get the userid with a particular username
		SELECT userID from user WHERE username = ?
	-- get an userid with a particular email address
		SELECT userID from user WHERE emailID = ?
	-- check for a logged in user comparing its unique random string and userid combination
		SELECT loggedInAt from activeUsers WHERE userID = ? AND randomString = ?
	-- get the password hash for a given username
		SELECT userID, password FROM user WHERE username = ?
	-- get all the details for an user using its userid
		SELECT username, firstname, lastname, emailID, profilePicUrl, aboutme from user WHERE userID = ?
	-- get all the interests for an user
		SELECT interest from interests WHERE userID = ?
	-- get all the details for an user using its username
		SELECT userID, firstname, lastname, emailID, profilePicUrl, aboutme from user WHERE username = ?
	-- update an user details when the user edits its profile
		UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ? WHERE userID = ?
	-- update an user details with its password
		UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, password = ? WHERE userID = ?
	-- update an user details withs its password and email address
		UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, password = ?, emailID = ? WHERE userID = ?
	-- update an user details with its email address
		UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, emailID = ? WHERE userID = ?
	-- delete all interests for an user
		DELETE from interests WHERE userID = ?
	-- part of the search query
		-- find all the users with keyword as a substring in its username
			SELECT userID FROM user where username LIKE ?
		-- find all the users with keyword as a substring iin one of its interests
			SELECT userID from interests where interest LIKE ?

-- queries related to a post
	-- get the post count for an user
		SELECT count(*) FROM post WHERE userID = ?
	-- add a new post
		INSERT INTO post(userID, postText, postName) VALUES (?, ?, ?)
	-- add tags for a blog-post
		INSERT INTO postTag(postID, tag) VALUES (?, ?)
	-- add keywords with its occurences for a post
		INSERT INTO postKeyword(postID, keyword, occurences) VALUES (?, ?, ?)
	-- get all the details for a post
		SELECT userID, postText, writtenAt, postName FROM post WHERE postID = ?
	-- get all the posts written by a particular user
		SELECT postID, postText, writtenAt, postName FROM post WHERE userID = ? ORDER BY writtenAt DESC
	-- get all the tags for a post
		SELECT tag FROM postTag WHERE postID = ?
	-- part of the search query
		-- find all the post containing the query as a substring in one of its tags
			SELECT postID FROM postTag WHERE tag LIKE ?
		-- find all the post containing the keyword as a part of its body
			SELECT postID FROM postKeyword WHERE keyword LIKE ? ORDER BY occurences DESC
	-- get all the latest post limited by a number
		SELECT postID, postText, postName, writtenAt FROM post ORDER BY writtenAt DESC LIMIT ?

-- queries related to followers
	-- count the number of followers
		SELECT count(*) from follows WHERE followee = ?
	-- count the number of followees
		SELECT count(*) from follows WHERE follower = ?
	-- check for a particular following
		SELECT follower, followee from follows WHERE follower = ? AND followee = ?
	-- add a new following
		INSERT INTO follows(follower, followee) VALUES (?, ?)
	-- delete a following
		DELETE FROM follows WHERE follower = ? AND followee = ?
	-- get all the followers
		SELECT follower from follows WHERE followee = ?
	-- get all the followees
		SELECT followee from follows WHERE follower = ?

-- queries related to comments
	-- count the number of comments
		SELECT count(*) FROM comment WHERE postID = ?
	-- get all the root comments for a particular post
		SELECT userID, comment, writtenAt, commentID FROM comment C WHERE postID = ? AND NOT EXISTS (SELECT * FROM commentThread CT WHERE CT.childID = C.commentID) ORDER BY writtenAt
	-- get all the children for a comment (should be used in recursion to get all the articles)
		SELECT userID, comment, writtenAt, commentID FROM comment C WHERE commentID IN (SELECT childID FROM commentThread WHERE parentID = ? AND postID = ?)  ORDER BY writtenAt
	-- get the last entered commentID
		SELECT max(commentID) FROM comment WHERE postID = ?
	-- add a new comment
		INSERT INTO comment(commentID, postID, userID, comment) VALUES (?, ?, ?, ?)
	-- add a new parent child relationship for replies on comments
		INSERT INTO commentThread(childID, parentID, postID) VALUES (?, ?, ?)

-- queries related to likes
	-- get the number of likes
		SELECT count(*) FROM likesDislikes WHERE postID = ? AND choice = 1
	-- get the number of dislikes
		SELECT count(*) FROM likesDislikes WHERE postID = ? AND choice = 0
	-- get the users who like a post
		SELECT userID FROM likesDislikes WHERE postID = ? AND choice = 1
	-- get the users who dislike a post
		SELECT userID FROM likesDislikes WHERE postID = ? AND choice = 0
	-- get the choice of an user on a post
		SELECT choice FROM likesDislikes WHERE postID = ? AND userID = ?
	-- add a new liking/disliking
		INSERT INTO likesDislikes(userID, postID, choice) VALUES (?, ?, ?)
	-- update the old liking/disliking
		UPDATE likesDislikes SET choice = ? WHERE userID = ? AND postID = ?
	-- remove any liking/disliking for a post by an user
		DELETE FROM likesDislikes WHERE userID = ? AND postID = ?
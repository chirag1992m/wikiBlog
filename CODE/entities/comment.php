<?php
/*
	constains the class Comment which handles the functions and
	related to Comments on a post.
*/
?>
<?php
chdir(dirname(__FILE__));
	include_once('user.php');
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class Comment {
	var $comment;
	var $userid;
	var $time;
	var $id;
	var $children;

	function __construct($c, $u, $t, $i) {
		$children = null;

		$this->comment = $c;
		$this->userid = $u;
		$this->time = $t;
		$this->id = $i;
	}

	function addChildren($child) {
		$this->children = $child;
	}
};

class Comments {
	var $dbclient;

	const QUERY_COMMENT_COUNT = "SELECT count(*) FROM comment WHERE postID = ?";

	const QUERY_GET_ROOT_COMMENTS = "SELECT userID, comment, writtenAt, commentID FROM comment C WHERE postID = ? AND NOT EXISTS (SELECT * FROM commentThread CT WHERE CT.childID = C.commentID) ORDER BY writtenAt";
	const QUERY_GET_CHILDREN = "SELECT userID, comment, writtenAt, commentID FROM comment C WHERE commentID IN (SELECT childID FROM commentThread WHERE parentID = ? AND postID = ?)  ORDER BY writtenAt";
	const QUERY_GET_MAXID = "SELECT max(commentID) FROM comment WHERE postID = ?";

	const QUERY_ADD_COMMENT = "INSERT INTO comment(commentID, postID, userID, comment) VALUES (?, ?, ?, ?)";
	const QUERY_ADD_THREADING = "INSERT INTO commentThread(childID, parentID, postID) VALUES (?, ?, ?)";

	function __construct($client) {
		if(is_null($client)) {
			global $Database_const;
			try {
				$this->dbclient = new DB_Class($Database_const->getHost(), $Database_const->getUser(), $Database_const->getPass(), $Database_const->getDatabase());
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
		} else {
			$this->dbclient = $client;
		}
	}

	function getCommentCount($postid) {
		if(!$this->dbclient->prepare(self::QUERY_COMMENT_COUNT)) {
			die("Error in QUERY_COMMENT_COUNT, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return 0;
			}
		}
	}

	function getChildren($commentid, $postid) {
		$child_comments = array();

		if(!$this->dbclient->prepare(self::QUERY_GET_CHILDREN)) {
			die("Error in QUERY_GET_CHILDREN, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $commentid, $postid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($userid, $text, $time, $commentid);
				while($return = $this->dbclient->current_stmt->fetch()) {
					$comm = new Comment($text, $userid, $time, $commentid);

					array_push($child_comments, $comm);
				}
			}
		}

		foreach ($child_comments as $key => $value) {
			$child = $this->getChildren($value->id, $postid);

			$child_comments[$key]->addChildren($child);
		}

		if(count($child_comments) == 0)
			return null;
		else
			return $child_comments;
	}

	function getCommentTree($postid) {
		/* get the root comments first */
		$root_comments = array();
		if(!$this->dbclient->prepare(self::QUERY_GET_ROOT_COMMENTS)) {
			die("Error in QUERY_GET_ROOT_COMMENTS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($userid, $text, $time, $commentid);
				while($return = $this->dbclient->current_stmt->fetch()) {
					$comm = new Comment($text, $userid, $time, $commentid);

					array_push($root_comments, $comm);
				}
			}
		}

		/* get children for every comment using recursion */
		foreach ($root_comments as $key => $value) {
			$child = $this->getChildren($value->id, $postid);

			$root_comments[$key]->addChildren($child);
		}

		return $root_comments;
	}

	function getMaxCommentid($postid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_MAXID)) {
			die("Error in QUERY_GET_MAXID, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return 0;
			}
		}
	}

	function addNewReply($postid, $comment, $userid, $parent) {
		$id = $this->addNewComment($postid, $comment, $userid);

		if($id) {
			if($this->dbclient->prepare(self::QUERY_ADD_THREADING)) {
				$this->dbclient->current_stmt->bind_param('iii', $id, $parent, $postid);
				if($this->dbclient->current_stmt->execute()) {
					return true;
				} else {
					return false;
				}
			} else {
				die("Error in QUERY_ADD_THREADING, ".$this->dbclient->getLastError());
			}
		} else {
			return false;
		}
	}

	function addNewComment($postid, $comment, $userid) {
		$commentid = $this->getMaxCommentid($postid) + 1;

		if($this->dbclient->prepare(self::QUERY_ADD_COMMENT)) {
			$this->dbclient->current_stmt->bind_param('iiis', $commentid, $postid, $userid, $comment);
			if($this->dbclient->current_stmt->execute()) {
				return $commentid;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_ADD_COMMENT, ".$this->dbclient->getLastError());
		}
	}
};
?>
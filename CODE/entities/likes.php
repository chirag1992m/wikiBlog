<?php
/*
	constains the class Likes which handles the functions and
	related to like on a post
*/
?>
<?php
chdir(dirname(__FILE__));
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class Likes {
	var $dbclient;

	const QUERY_LIKE_COUNT = "SELECT count(*) FROM likesDislikes WHERE postID = ? AND choice = 1";
	const QUERY_DISLIKE_COUNT = "SELECT count(*) FROM likesDislikes WHERE postID = ? AND choice = 0";

	const QUERY_LIKES = "SELECT userID FROM likesDislikes WHERE postID = ? AND choice = 1";
	const QUERY_DISLIKES = "SELECT userID FROM likesDislikes WHERE postID = ? AND choice = 0";

	const QUERY_GET_CHOICE = "SELECT choice FROM likesDislikes WHERE postID = ? AND userID = ?";

	const QUERY_ADD_NEW_CHOICE = "INSERT INTO likesDislikes(userID, postID, choice) VALUES (?, ?, ?)";
	const QUERY_UPDATE_CHOICE = "UPDATE likesDislikes SET choice = ? WHERE userID = ? AND postID = ?";
	const QUERY_REMOVE_CHOICE = "DELETE FROM likesDislikes WHERE userID = ? AND postID = ?";

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

	function getLikesDislikesCount($postid) {
		$data = array();

		$data['like'] = $this->getLikesCount($postid);
		$data['dislike'] = $this->getDislikesCount($postid);

		return $data;
	}

	function getLikesCount($postid) {
		if(!$this->dbclient->prepare(self::QUERY_LIKE_COUNT)) {
			die("Error in QUERY_LIKE_COUNT, ".$this->dbclient->getLastError());
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

	function getDislikesCount($postid) {
		if(!$this->dbclient->prepare(self::QUERY_DISLIKE_COUNT)) {
			die("Error in QUERY_DISLIKE_COUNT, ".$this->dbclient->getLastError());
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

	function getLikeUsers($postid) {
		if(!$this->dbclient->prepare(self::QUERY_LIKES)) {
			die("Error in QUERY_LIKES, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);
			$users = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				while($this->dbclient->current_stmt->fetch()) {
					array_push($users, $result);
				}
			}

			return $users;
		}
	}

	function getDislikeUsers($postid) {
		if(!$this->dbclient->prepare(self::QUERY_DISLIKES)) {
			die("Error in QUERY_DISLIKES, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);
			$users = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				while($this->dbclient->current_stmt->fetch()) {
					array_push($users, $result);
				}
			}

			return $users;
		}
	}

	function checkLikeExistence($postid, $userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_CHOICE)) {
			die("Error in QUERY_GET_CHOICE, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $postid, $userid);
			$choice = -1;
			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($this->dbclient->current_stmt->fetch()) {
					$choice = $result;
				}
			}
			return $choice;
		}
	}

	function removeChoice($postid, $userid) {
		if(!$this->dbclient->prepare(self::QUERY_REMOVE_CHOICE)) {
			die("Error in QUERY_REMOVE_CHOICE, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $userid, $postid);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else
				return false;
		}
	}

	function addChoice($postid, $userid, $choice) {
		if($this->checkLikeExistence($postid, $userid) == -1) {
			if(!$this->dbclient->prepare(self::QUERY_ADD_NEW_CHOICE)) {
				die("Error in QUERY_ADD_NEW_CHOICE, ".$this->dbclient->getLastError());
			} else {
				$this->dbclient->current_stmt->bind_param('iii', $userid, $postid, $choice);

				if($this->dbclient->current_stmt->execute()) {
					return true;
				} else
					return false;
			}
		} else {
			if(!$this->dbclient->prepare(self::QUERY_UPDATE_CHOICE)) {
				die("Error in QUERY_UPDATE_CHOICE, ".$this->dbclient->getLastError());
			} else {
				$this->dbclient->current_stmt->bind_param('iii', $choice, $userid, $postid);

				if($this->dbclient->current_stmt->execute()) {
					return true;
				} else
					return false;
			}
		}
	}
};
?>
<?php
/*
	constains the class Follows which handles the functions and
	related to a following and followers relation.
*/
?>
<?php
chdir(dirname(__FILE__));
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class Follows {
	var $dbclient;

	const QUERY_FOLLOWERS_COUNT = "SELECT count(*) from follows WHERE followee = ?";
	const QUERY_FOLLOWEES_COUNT = "SELECT count(*) from follows WHERE follower = ?";

	const QUERY_CHECK_FOLLOWS = "SELECT follower, followee from follows WHERE follower = ? AND followee = ?";

	const QUERY_ADD_FOLLOWS = "INSERT INTO follows(follower, followee) VALUES (?, ?)";

	const QUERY_REMOVE_FOLLOWS = "DELETE FROM follows WHERE follower = ? AND followee = ?";

	const QUERY_GET_FOLLOWERS = "SELECT follower from follows WHERE followee = ?";
	const QUERY_GET_FOLLOWEES = "SELECT followee from follows WHERE follower = ?";

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

	function getFollowersCount($userid) {
		if(!$this->dbclient->prepare(self::QUERY_FOLLOWERS_COUNT)) {
			die("Error in QUERY_FOLLOWERS_COUNT, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return 0;
			}
		}
	}

	function getFolloweesCount($userid) {
		if(!$this->dbclient->prepare(self::QUERY_FOLLOWEES_COUNT)) {
			die("Error in QUERY_FOLLOWEES_COUNT, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return 0;
			}
		}
	}

	function checkFollows($userid1, $userid2) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_FOLLOWS)) {
			die("Error in QUERY_FOLLOWEES_COUNT, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $userid1, $userid2);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($col1, $col2);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return true;
				} else
					return false;
			}
		}
	}

	function addNewFollower($userid1, $userid2) {
		if(!$this->dbclient->prepare(self::QUERY_ADD_FOLLOWS)) {
			die("Error in QUERY_ADD_FOLLOWS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $userid1, $userid2);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else
				return false;
		}
	}

	function removeFollows($userid1, $userid2) {
		if(!$this->dbclient->prepare(self::QUERY_REMOVE_FOLLOWS)) {
			die("Error in QUERY_REMOVE_FOLLOWS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $userid1, $userid2);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else
				return false;
		}
	}

	function getFollowers($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_FOLLOWERS)) {
			die("Error in QUERY_GET_FOLLOWERS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			$followers = array();
			
			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				
				while($return = $this->dbclient->current_stmt->fetch()) {
					array_push($followers, $result);
				}
			}

			return $followers;
		}
	}

	function getFollowees($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_FOLLOWEES)) {
			die("Error in QUERY_GET_FOLLOWEES, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			$followees = array();
			
			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				
				while($return = $this->dbclient->current_stmt->fetch()) {
					array_push($followees, $result);
				}
			}

			return $followees;
		}
	}
};
?>
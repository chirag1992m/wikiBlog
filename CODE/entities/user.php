<?php
/*
	Constains the class for entity USER
*/
?>
<?php
	chdir(dirname(__FILE__));
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class User {
	var $dbclient;
	var $userid;

	const QUERY_ADD_USER = "INSERT INTO user(username, firstName, lastName, emailID, password, profilePicUrl, aboutMe) VALUES (?, ?, ?, ?, ?, ?, ?)";
	const QUERY_ADD_INTEREST = "INSERT INTO interests(userID, interest) VALUES (?, ?)";
	const QUERY_ADD_ACTIVE_USER = "INSERT INTO activeUsers(randomString, userID, ip) VALUES (?, ?, ?)";

	const QUERY_CHECK_USERNAME = "SELECT userID from user WHERE username = ?";
	const QUERY_CHECK_EMAILID = "SELECT userID from user WHERE emailID = ?";
	const QUERY_CHECK_ACTIVE_USER = "SELECT loggedInAt from activeUsers WHERE userID = ? AND randomString = ?";
	
	const QUERY_GET_PASSWORD = "SELECT userID, password FROM user WHERE username = ?";
	const QUERY_GET_DETAILS_USERID = "SELECT username, firstname, lastname, emailID, profilePicUrl, aboutme from user WHERE userID = ?";
	const QUERY_GET_INTEREST_USERID = "SELECT interest from interests WHERE userID = ?";
	const QUERY_GET_DETAILS_USERNAME = "SELECT userID, firstname, lastname, emailID, profilePicUrl, aboutme from user WHERE username = ?";

	const QUERY_UPDATE_DETAILS = "UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ? WHERE userID = ?";
	const QUERY_UPDATE_DETAILS_PASS = "UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, password = ? WHERE userID = ?";
	const QUERY_UPDATE_DETAILS_EMAIL_PASS = "UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, password = ?, emailID = ? WHERE userID = ?";
	const QUERY_UPDATE_DETAILS_EMAIL = "UPDATE user set firstname = ?, lastname = ?, profilePicUrl = ?, aboutme = ?, emailID = ? WHERE userID = ?";

	const QUERY_DROP_INTERESTS = "DELETE from interests WHERE userID = ?";

	const QUERY_SEARCH_USERNAME = "SELECT userID FROM user where username LIKE ?";
	const QUERY_SEARCH_INTEREST = "SELECT userID from interests where interest LIKE ?";

	function __construct() {
		global $Database_const;
		try {
			$this->dbclient = new DB_Class($Database_const->getHost(), $Database_const->getUser(), $Database_const->getPass(), $Database_const->getDatabase());
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}

	function addNewUser($firstname, $lastname, $username, $password, $emailid, $aboutme, $interests, $photourl) {
		/* Adding A new User in the user table */
		if($this->dbclient->prepare(self::QUERY_ADD_USER)) {
			$this->dbclient->current_stmt->bind_param('sssssss', $username, $firstname, $lastname, $emailid, $password, $photourl, $aboutme);
			if($this->dbclient->current_stmt->execute()) {
				$this->userid = intval($this->dbclient->lastInsertId());

				$this->addInterests($this->userid, $interests);

				return $this->userid;
			} else {
				return -1;
			}
		} else {
			die("Error i QUERY_ADD_USER, ".$this->dbclient->getLastError());
		}
	}

	function addInterests($userid, $interests) {
		$interest = "";

		if(!$this->dbclient->prepare(self::QUERY_ADD_INTEREST)) {
			die("Error in QUERY_ADD_INTEREST, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('is', $userid, $interest);
		}

		foreach ($interests as $key => $value) {
			$interest = $value;
			if(!$this->dbclient->current_stmt->execute())
				continue;
		}
	}

	function checkUsernameExist($username) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_USERNAME)) {
			die("Error in QUERY_CHECK_USERNAME, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $username);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return -1;
			}
		}
	}

	function checkEmailIDExist($emailid) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_EMAILID)) {
			die("Error in QUERY_CHECK_EMAILID, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $emailid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $result;
				} else
					return -1;
			}
		}
	}

	function matchUsernamePassword($user, $pass) {
		if(!$this->dbclient->prepare(self::QUERY_GET_PASSWORD)) {
			die("Error in QUERY_GET_DETAILS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $user);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($userid, $password);
				if($return = $this->dbclient->current_stmt->fetch()) {
					if($password != $pass)
						return 0;
					else
						return $userid;
				} else
					return -1;
			}
		}
	}

	function addActiveUser($userid, $randomid, $ip) {
		/* Adding A new User in the user table */
		if($this->dbclient->prepare(self::QUERY_ADD_ACTIVE_USER)) {
			$this->dbclient->current_stmt->bind_param('sss', $randomid, $userid, $ip);
			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_ADD_ACTIVE_USER, ".$this->dbclient->getLastError());
		}
	}

	function checkActiveUser($userid, $randomid) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_ACTIVE_USER)) {
			die("Error in QUERY_CHECK_ACTIVE_USER, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('is', $userid, $randomid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return true;
				} else
					return false;
			} else {
				return false;
			}
		}
	}

	function getAllDetailsByUserid($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_DETAILS_USERID)) {
			die("Error in QUERY_GET_DETAILS_USERID, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);

			if($this->dbclient->current_stmt->execute()) {
				$userdata = array();
				$this->dbclient->current_stmt->bind_result($userdata['username'], $userdata['firstname'], $userdata['lastname'], $userdata['emailid'], $userdata['profilepic'], $userdata['aboutme']);

				if($return = $this->dbclient->current_stmt->fetch()) {
					$userdata['interests'] = $this->getAllInterests($userid);

					return $userdata;
				} else
					return null;
			} else {
				return null;
			}
		}
	}

	function getAllInterests($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_INTEREST_USERID)) {
			die("Error in QUERY_GET_INTEREST_USERID, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			$interests = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($interest);
				while ($this->dbclient->current_stmt->fetch()) {
					array_push($interests, $interest);
				}
			}
			return $interests;
		}
	}

	function getAllDetailsByUsername($username) {
		if(!$this->dbclient->prepare(self::QUERY_GET_DETAILS_USERNAME)) {
			die("Error in QUERY_GET_DETAILS_USERNAME, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $username);

			if($this->dbclient->current_stmt->execute()) {
				$userdata = array();
				$this->dbclient->current_stmt->bind_result($userdata['userid'], $userdata['firstname'], $userdata['lastname'], $userdata['emailid'], $userdata['profilepic'], $userdata['aboutme']);

				if($return = $this->dbclient->current_stmt->fetch()) {
					$userdata['interests'] = $this->getAllInterests($userdata['userid']);

					return $userdata;
				} else
					return null;
			} else {
				return null;
			}
		}
	}

	function dropInterests($userid) {
		if($this->dbclient->prepare(self::QUERY_DROP_INTERESTS)) {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			
			$this->dbclient->current_stmt->execute();
		} else {
			die("Error in QUERY_DROP_INTERESTS, ".$this->dbclient->getLastError());
		}
	}

	function updateDetails($firstname, $lastname, $aboutme, $interests, $photourl, $userid) {
		if($this->dbclient->prepare(self::QUERY_UPDATE_DETAILS)) {
			$this->dbclient->current_stmt->bind_param('ssssi', $firstname, $lastname, $photourl, $aboutme, $userid);
			if($this->dbclient->current_stmt->execute()) {

				$this->dropInterests($userid);
				$this->addInterests($userid, $interests);

				return true;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_UPDATE_DETAILS, ".$this->dbclient->getLastError());
		}
	}

	function updateDetailsPass($firstname, $lastname, $aboutme, $interests, $photourl, $newpass, $userid) {
		if($this->dbclient->prepare(self::QUERY_UPDATE_DETAILS_PASS)) {
			$this->dbclient->current_stmt->bind_param('sssssi', $firstname, $lastname, $photourl, $aboutme, $newpass, $userid);
			if($this->dbclient->current_stmt->execute()) {

				$this->dropInterests($userid);
				$this->addInterests($userid, $interests);
				
				return true;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_UPDATE_DETAILS_PASS, ".$this->dbclient->getLastError());
		}
	}

	function updateDetailsEmail($firstname, $lastname, $aboutme, $interests, $photourl, $emailid, $userid) {
		if($this->dbclient->prepare(self::QUERY_UPDATE_DETAILS_EMAIL)) {
			$this->dbclient->current_stmt->bind_param('sssssi', $firstname, $lastname, $photourl, $aboutme, $emailid, $userid);
			if($this->dbclient->current_stmt->execute()) {

				$this->dropInterests($userid);
				$this->addInterests($userid, $interests);
				
				return true;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_UPDATE_DETAILS_EMAIL, ".$this->dbclient->getLastError());
		}
	}

	function updateDetailsEmailPass($firstname, $lastname, $aboutme, $interests, $photourl, $emailid, $newpass, $userid) {
		if($this->dbclient->prepare(self::QUERY_UPDATE_DETAILS_EMAIL_PASS)) {
			$this->dbclient->current_stmt->bind_param('ssssssi', $firstname, $lastname, $photourl, $aboutme, $newpass, $emailid, $userid);
			if($this->dbclient->current_stmt->execute()) {

				$this->dropInterests($userid);
				$this->addInterests($userid, $interests);
				
				return true;
			} else {
				return false;
			}
		} else {
			die("Error in QUERY_UPDATE_DETAILS_EMAIL_PASS, ".$this->dbclient->getLastError());
		}
	}

	function searchUsers($keyword_count) {
		$keyword = "";
		$ids = array();

		/* searching the tags */
		if(!$this->dbclient->prepare(self::QUERY_SEARCH_USERNAME)) {
			die("Error in QUERY_SEARCH_TAGS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $keyword);
		}


		foreach ($keyword_count as $key => $value) {
			$keyword = "%".$key."%";
			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($id);

				while($this->dbclient->current_stmt->fetch()) {
					if(!in_array($id, $ids))
						array_push($ids, $id);
				}
			}
		}

		/* searching the keyword */
		if(!$this->dbclient->prepare(self::QUERY_SEARCH_INTEREST)) {
			die("Error in QUERY_SEARCH_KEYWORDS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('s', $keyword);
		}


		foreach ($keyword_count as $key => $value) {
			$keyword = "%".$key."%";
			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($id);

				while($this->dbclient->current_stmt->fetch()) {
					if(!in_array($id, $ids))
						array_push($ids, $id);
				}
			}
		}

		return $ids;
	}

	function getDatabaseClient() {
		return $this->dbclient;
	}
};
$user = new USER;
?>
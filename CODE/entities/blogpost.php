<?php
/*
	constains the class BlogPost which handles the functions and
	related to a blogpost.
*/
?>
<?php
chdir(dirname(__FILE__));
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class BlogPost {
	var $dbclient;
	var $postid;

	const QUERY_GET_POST_COUNT = "SELECT count(*) FROM post WHERE userID = ?";

	const QUERY_ADD_POST = "INSERT INTO post(userID, postText, postName) VALUES (?, ?, ?)";
	const QUERY_ADD_TAG = "INSERT INTO postTag(postID, tag) VALUES (?, ?)";
	const QUERY_ADD_KEYWORD = "INSERT INTO postKeyword(postID, keyword, occurences) VALUES (?, ?, ?)";

	const QUERY_GET_POST = "SELECT userID, postText, writtenAt, postName FROM post WHERE postID = ?";
	const QUERY_GET_POST_USER = "SELECT postID, postText, writtenAt, postName FROM post WHERE userID = ? ORDER BY writtenAt DESC";
	const QUERY_GET_POST_TAG = "SELECT tag FROM postTag WHERE postID = ?";

	const QUERY_SEARCH_TAGS = "SELECT postID FROM postTag WHERE tag LIKE ?";
	const QUERY_SEARCH_KEYWORDS = "SELECT postID FROM postKeyword WHERE keyword LIKE ? ORDER BY occurences DESC";

	const QUERY_GET_LATEST_POSTS = "SELECT postID, postText, postName, writtenAt FROM post ORDER BY writtenAt DESC LIMIT ?";

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

	function getPostCount($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_POST_COUNT)) {
			die("Error in QUERY_GET_POST_COUNT, ".$this->dbclient->getLastError());
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

	function addTags($pid, $tags) {
		$tag = "";

		if(!$this->dbclient->prepare(self::QUERY_ADD_TAG)) {
			die("Error in QUERY_ADD_TAG, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('is', $pid, $tag);
		}

		foreach ($tags as $key => $value) {
			$tag = $value;

			if(!$this->dbclient->current_stmt->execute())
				continue;
		}
	}

	function addKeywords($pid, $keywords) {
		$keyword = "";
		$occurence = 0;

		if(!$this->dbclient->prepare(self::QUERY_ADD_KEYWORD)) {
			die("Error in QUERY_ADD_KEYWORD, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('isi', $pid, $keyword, $occurence);
		}

		foreach ($keywords as $key => $value) {
			$keyword = $key;
			$occurence = $value;

			$this->dbclient->current_stmt->execute();
		}
	}

	function addNewPost($userid, $postname, $tags, $post_text, $keyword_count) {
		if(!$this->dbclient->prepare(self::QUERY_ADD_POST)) {
			die("Error in QUERY_ADD_POST, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iss', $userid, $post_text, $postname);

			if($this->dbclient->current_stmt->execute()) {
				$this->postid = intval($this->dbclient->lastInsertId());

				$this->addTags($this->postid, $tags);

				$this->addKeywords($this->postid, $keyword_count);

				return true;
			} else {
				return false;
			}
		}
	}

	function getAllTags($postid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_POST_TAG)) {
			die("Error in QUERY_GET_POST_TAG, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);
			$tags = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($tag);
				while ($this->dbclient->current_stmt->fetch()) {
					array_push($tags, $tag);
				}
			}
			return $tags;
		}
	}

	function getPostsGivenUser($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_POST_USER)) {
			die("Error in QUERY_GET_POST_USER, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			if($this->dbclient->current_stmt->execute()) {
				$data = array();
				$this->dbclient->current_stmt->bind_result($postid, $text, $time, $name);

				while($this->dbclient->current_stmt->fetch()) {
					array_push($data, array('postid' => $postid, 'text' => $text, 'time' => $time, 'name' => $name));
				}

				foreach ($data as $key => $value) {
					$data[$key]['tags'] = $this->getAllTags($value['postid']);
				}

				return $data;
			} else {
				return null;
			}
		}
	}

	function getPostGivenPostid($postid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_POST)) {
			die("Error in QUERY_GET_POST, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $postid);
			if($this->dbclient->current_stmt->execute()) {
				$data = array();
				$this->dbclient->current_stmt->bind_result($userid, $postText, $writtenAt, $postName);

				if($return = $this->dbclient->current_stmt->fetch()) {
					$data['uid'] = $userid;
					$data['text'] = $postText;
					$data['time'] = $writtenAt;
					$data['name'] = $postName;
					$data['tags'] = $this->getAllTags($postid);

					return $data;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
	}

	function searchPost($keyword_count) {
		$keyword = "";
		$ids = array();

		/* searching the tags */
		if(!$this->dbclient->prepare(self::QUERY_SEARCH_TAGS)) {
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
		if(!$this->dbclient->prepare(self::QUERY_SEARCH_KEYWORDS)) {
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

	function getLatestPosts($limit) {
		if(!$this->dbclient->prepare(self::QUERY_GET_LATEST_POSTS)) {
			die("Error in QUERY_GET_LATEST_POSTS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $limit);
			if($this->dbclient->current_stmt->execute()) {
				$data = array();
				$this->dbclient->current_stmt->bind_result($id, $text, $name, $time);

				while($this->dbclient->current_stmt->fetch()) {
					array_push($data, array('id' => $id, 'text'=>$text, 'name'=>$name, 'time'=>$time));
				}

				return $data;
			} else {
				return null;
			}
		}
	}
};
?>
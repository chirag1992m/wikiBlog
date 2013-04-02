<?php
/*
	constains the class Article which handles the functions and
	related to a Article.
*/
?>
<?php
chdir(dirname(__FILE__));
	include_once('../includes/constants.php');
	include_once('../includes/dbClient.php');
?>
<?php
class Article {
	var $dbclient;
	var $articleid;

	const QUERY_GET_DISTINCT_ARTICLE_COUNT = "SELECT count(DISTINCT(articleID)) FROM writesArticle WHERE userID = ?";
	const QUERY_GET_ARTICLE_COUNT = "SELECT count(articleID) FROM writesArticle WHERE userID = ?";

	const QUERY_CHECK_ARTICLE_EXISTENCE = "SELECT lastModified FROM article WHERE articleID = ?";
	const QUERY_CHECK_ARTICLE_EXISTENCE_VERSION = "SELECT userID FROM writesArticle WHERE articleID = ? AND version = ?";

	const QUERY_ADD_NEW_ARTICLE_ARTICLE = "INSERT INTO article(articleName, articleText) VALUES (?, ?)";
	const QUERY_ADD_NEW_ARTICLE_WRITES = "INSERT INTO writesArticle(userID, articleID, version, articleText, writtenAt) VALUES (?, ?, ?, ?, ?)";
	const QUERY_ADD_TAG = "INSERT INTO articleTag(articleID, tag, url) VALUES (?, ?, ?)";
	const QUERY_ADD_KEYWORD = "INSERT INTO articleKeyword(articleID, keyword, occurences) VALUES (?, ?, ?)";
	const QUERY_ADD_RATING = "INSERT INTO articleRating(userID, articleID, version, rating) VALUES (?, ?, ?, ?)";

	const QUERY_GET_ARTICLE_LATEST = "SELECT articleText, lastModified, articleName FROM article WHERE articleID = ?";
	const QUERY_GET_ARTICLE_USER = "SELECT articleID, version, articleText, writtenAt FROM writesArticle WHERE userID = ? ORDER BY writtenAt DESC";
	const QUERY_GET_ARTICLE_TAGS = "SELECT tag, url FROM articleTag WHERE articleID = ?";
	const QUERY_GET_ARTICLE_VERSION = "SELECT userID, articleText, writtenAt FROM writesArticle WHERE articleID = ? AND version = ?";
	const QUERY_GET_LATEST_VERSION_NUMBER = "SELECT max(version) FROM writesArticle WHERE articleID = ?";
	const QUERY_GET_VERSIONS = "SELECT userID, version, writtenAt FROM writesArticle WHERE articleID = ? ORDER BY version";

	const QUERY_GET_AVERAGE_RATING = "SELECT avg(rating) FROM articleRating WHERE articleID = ? AND version = ?";
	const QUERY_GET_RATING = "SELECT avg(rating) FROM articleRating WHERE userID = ? AND articleID = ? AND version = ?";

	const QUERY_UPDATE_ARTICLE = "UPDATE article SET articleText = ?, lastModified = ? WHERE articleID = ?";

	const QUERY_SEARCH_TAGS = "SELECT articleID FROM articleTag WHERE tag LIKE ?";
	const QUERY_SEARCH_KEYWORDS = "SELECT articleID FROM articleKeyword WHERE keyword LIKE ? ORDER BY occurences DESC";

	const QUERY_GET_LATEST_ARTICLES = "SELECT articleID, articleText, articleName, lastModified FROM article ORDER BY lastModified DESC LIMIT ?";

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

	function getDistinctArticleCount($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_DISTINCT_ARTICLE_COUNT)) {
			die("Error in QUERY_GET_DISTINCT_ARTICLE_COUNT, ".$this->dbclient->getLastError());
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

	function getAllArticleCount($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_ARTICLE_COUNT)) {
			die("Error in QUERY_GET_ARTICLE_COUNT, ".$this->dbclient->getLastError());
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

	function checkArticleExistence($articleid) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_ARTICLE_EXISTENCE)) {
			die("Error in QUERY_CHECK_ARTICLE_EXISTENCE, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $articleid);

			if($this->dbclient->current_stmt->execute()) {
				if($this->dbclient->current_stmt->fetch()) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	function checkArticleExistenceVersion($articleid, $version) {
		if(!$this->dbclient->prepare(self::QUERY_CHECK_ARTICLE_EXISTENCE_VERSION)) {
			die("Error in QUERY_CHECK_ARTICLE_EXISTENCE_VERSION, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $articleid, $version);

			if($this->dbclient->current_stmt->execute()) {
				if($this->dbclient->current_stmt->num_rows > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	function getLatestArticleDataByID($articleid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_ARTICLE_LATEST)) {
			die("Error in QUERY_GET_ARTICLE_LATEST, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $articleid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($text, $modified, $articleName);
				$data = array();
				if($return = $this->dbclient->current_stmt->fetch()) {
					$data['articleText'] = $text;
					$data['lastModified'] = $modified;
					$data['articleName'] = $articleName;

					return $data;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
	}

	function getLatestVersionNumber($articleid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_LATEST_VERSION_NUMBER)) {
			die("Error in QUERY_GET_LATEST_VERSION_NUMBER, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $articleid);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($version);
				if($return = $this->dbclient->current_stmt->fetch()) {
					return $version;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
	}

	function getArticleDataVersion($articleid, $version) {
		if(!$this->dbclient->prepare(self::QUERY_GET_ARTICLE_VERSION)) {
			die("Error in QUERY_GET_ARTICLE_VERSION, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $articleid, $version);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($userid, $text, $time);
				$data = array();
				if($return = $this->dbclient->current_stmt->fetch()) {
					$data['uid'] = $userid;
					$data['text'] = $text;
					$data['time'] = $time;

					return $data;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
	}

	function getAllTags($articleid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_ARTICLE_TAGS)) {
			die("Error in QUERY_GET_ARTICLE_TAGS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $articleid);
			$tags = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($tag, $url);
				while ($this->dbclient->current_stmt->fetch()) {
					array_push($tags, array($tag, $url));
				}
			}
			return $tags;
		}
	}

	function getArticleName($articleid) {
		$name = $this->getLatestArticleDataByID($articleid);
		if(!is_null($name)) {
			return $name['articleName'];
		} else {
			return "";
		}
	}

	function getAverageRating($articleid, $version) {
		if(!$this->dbclient->prepare(self::QUERY_GET_AVERAGE_RATING)) {
			die("Error in QUERY_GET_AVERAGE_RATING, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ii', $articleid, $version);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					if(!is_null($result))
						return $result;
					else
						return 0;
				} else
					return 0;
			}
		}
	}

	function getUserRating($userid, $articleid, $version) {
		if(!$this->dbclient->prepare(self::QUERY_GET_RATING)) {
			die("Error in QUERY_GET_RATING, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iii', $userid, $articleid, $version);

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($result);
				if($return = $this->dbclient->current_stmt->fetch()) {
					if(!is_null($result))
						return $result;
					else
						return null;
				} else
					return null;
			}
		}
	}

	function getArticleListByUser($userid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_ARTICLE_USER)) {
			die("Error in QUERY_GET_ARTICLE_USER, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $userid);
			if($this->dbclient->current_stmt->execute()) {
				$data = array();
				$this->dbclient->current_stmt->bind_result($postid, $version, $text, $time);

				while($this->dbclient->current_stmt->fetch()) {
					array_push($data, array('articleid' => $postid, 'version' => $version,'text' => $text, 'time' => $time));
				}

				foreach ($data as $key => $value) {
					$data[$key]['tags'] = $this->getAllTags($value['articleid']);
					$data[$key]['name'] = $this->getArticleName($value['articleid']);
					$data[$key]['avgrating'] = $this->getAverageRating($value['articleid'], $value['version']);
					$data[$key]['rating'] = $this->getUserRating($userid, $value['articleid'], $value['version']);
				}

				return $data;
			} else {
				return null;
			}
		}
	}

	function addWritesArticle($userid, $articleid, $version, $text, $writtenAt) {
		if(!$this->dbclient->prepare(self::QUERY_ADD_NEW_ARTICLE_WRITES)) {
			die("Error in QUERY_ADD_NEW_ARTICLE_WRITES, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iiiss', $userid, $articleid, $version, $text, $writtenAt);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
	}

	function addTags($aid, $tags) {
		$tag = "";
		$url = null;

		if(!$this->dbclient->prepare(self::QUERY_ADD_TAG)) {
			die("Error in QUERY_ADD_TAG, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iss', $aid, $tag, $url);
		}

		foreach ($tags as $key => $value) {
			$tag = $value;

			if(!$this->dbclient->current_stmt->execute())
				continue;
		}
	}

	function addReferences($aid, $references) {
		$tag = "";
		$url = "";

		if(!$this->dbclient->prepare(self::QUERY_ADD_TAG)) {
			die("Error in QUERY_ADD_TAG, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iss', $aid, $tag, $url);
		}

		foreach ($references as $key => $value) {
			$tag = $value[0];
			$url = $value[1];

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

	function addNewArticle($userid, $name, $tags, $references, $text, $keyword_count) {
		if(!$this->dbclient->prepare(self::QUERY_ADD_NEW_ARTICLE_ARTICLE)) {
			die("Error in QUERY_ADD_NEW_ARTICLE_ARTICLE, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ss', $name, $text);

			if($this->dbclient->current_stmt->execute()) {
				$this->articleid = intval($this->dbclient->lastInsertId());

				$data = $this->getLatestArticleDataByID($this->articleid);
				$this->addWritesArticle($userid, $this->articleid, 1, $text, $data['lastModified']);
				$this->addTags($this->articleid, $tags);
				$this->addReferences($this->articleid, $references);
				$this->addKeywords($this->articleid, $keyword_count);

				return true;
			} else {
				return false;
			}
		}
	}

	function getAllVersions($articleid) {
		if(!$this->dbclient->prepare(self::QUERY_GET_VERSIONS)) {
			die("Error in QUERY_GET_VERSIONS, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $articleid);

			$data = array();

			if($this->dbclient->current_stmt->execute()) {
				$this->dbclient->current_stmt->bind_result($userid, $version, $time);

				while($this->dbclient->current_stmt->fetch()) {
					array_push($data, array('uid' => $userid, 'version' => $version, 'time' => $time));
				}
			}

			return $data;
		}
	}

	function addNewRating($userid, $articleid, $version, $rating) {
		if(!$this->dbclient->prepare(self::QUERY_ADD_RATING)) {
			die("Error in QUERY_ADD_RATING, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('iiii', $userid, $articleid, $version, $rating);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
	}

	function editExistingArticle($userid, $articleid, $name, $tags, $references, $text, $keyword_count) {
		$new_version = $this->getLatestVersionNumber($articleid) + 1;
		$this->addWritesArticle($userid, $articleid, $new_version, $text, null);
		$new_data = $this->getArticleDataVersion($articleid, $new_version);
		$this->addTags($articleid, $tags);
		$this->addReferences($articleid, $references);
		$this->addKeywords($articleid, $keyword_count);

		if(!$this->dbclient->prepare(self::QUERY_UPDATE_ARTICLE)) {
			die("Error in QUERY_UPDATE_ARTICLE, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('ssi', $text, $new_data['time'], $articleid);

			if($this->dbclient->current_stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
	}

	function searchArticles($keyword_count) {
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

	function getLatestArticles($limit) {
		if(!$this->dbclient->prepare(self::QUERY_GET_LATEST_ARTICLES)) {
			die("Error in QUERY_GET_LATEST_ARTICLES, ".$this->dbclient->getLastError());
		} else {
			$this->dbclient->current_stmt->bind_param('i', $limit);
			if($this->dbclient->current_stmt->execute()) {
				$data = array();
				$this->dbclient->current_stmt->bind_result($articleid, $article, $name, $time);

				while($this->dbclient->current_stmt->fetch()) {
					array_push($data, array('id' => $articleid, 'text'=>$article, 'name'=>$name, 'time'=>$time));
				}

				return $data;
			} else {
				return null;
			}
		}
	}
};
?>
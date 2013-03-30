<?php
ob_start();
	include_once('includes/session.php');
	if(!$session->loggedin) {
		header("location: index.php");
		ob_flush();
	}
chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');
	include_once('entities/article.php');
?>
<?php
if(isset($_POST['type'])) {
	$type = intval($_POST['type']);
	if($type < 1 || $type > 3 ) {
		header("location: write");
		ob_flush();
	} else {
		/* Get all the form values */
		$name = strtolower(trim($_POST['name']));
		$temp_tags = strtolower(trim($_POST['tags']));
		if($type != 1) {
			$temp_references = trim($_POST['references']);
		} else {
			$temp_references = null;
		}
		if($type == 3) {
			if(isset($_POST['articleid']))
				$articleid = intval($_POST['articleid']);
			else {
				$form->SetError("form", "Article does not exist already.");
				$articleid = null;
			}
		} else {
			$articleid = null;
		}

		/* Check the common values and return if any errors have occured */
		if(!$name || strlen($name) == 0) {
			$form->SetError("name", "* name not entered");
		} else if(strlen($name) > 99) {
			$form->SetError("name", "* name too long");
		}

		/* explode the tags and references around the commas as given in the form */
		$tags = array();
		if($temp_tags && (strlen($temp_tags) > 0)) {
			$temp_tags = strip_tags($temp_tags);
			$temp_tags = explode(",", $temp_tags);

			foreach ($temp_tags as $key => $value) {
				$temp_tags[$key] = $value = trim($value);
				if(strlen($value) == 0)
					unset($temp_tags[$key]);
				else {
					if(!in_array($value, $tags))
						array_push($tags, $value);
				}
			}
		} else {
			$tags = null;
		}

		if($type != 1) {
			$references = array();
			if($temp_references && (strlen($temp_references) > 0)) {
				$temp_references = strip_tags($temp_references);
				$temp_references = explode(",", $temp_references);

				foreach ($temp_references as $key => $value) {
					$temp_references[$key] = $value = trim($value);
					if($pos = strpos($value, "=")) {
						$tag = trim(substr($value, 0, $pos));
						$url = trim(substr($value, $pos+1));
						if($commonfunctions->checkURL($url)) {
							if(!in_array(array(0 => $tag, 1 => $url), $references))
								array_push($references, array(0 => $tag, 1 => $url));
						} else {
							continue;
						}
					} else {
						continue;
					}
				}
			} else {
				$references = null;
			}
		} else {
			$references = null;
		}
	}

	/* if type = 3, check article existence, then only editing is possible */
	$article = null;
	if($type == 3) {
		$article = new Article($user->getDatabaseClient());
		if(!$article->checkArticleExistence($articleid)) {
			$form->SetError("form", "Article does not exist already.");
		}
	}

	/* Now getting the text and testing it */
	$text = str_replace("<div", " <div", $_POST['text']);
	if(!$text || strlen(strip_tags($text)) == 0) {
		$form->SetError("text", "* Text not entered");
	}

	if($form->num_errors > 0) {
		$_SESSION['write_success'] = false;
		$_SESSION['write_message'] = $name." couldnt be added, try again!";
		$_SESSION['value_array'] = $_POST;
		$_SESSION['error_array'] = $form->GetErrorArray();
		header("location: write?type=".($type == 1 ? 'post' : 'article').($type == 3 ? '&articleid='.$articleid : ''));
		ob_flush();
	} else {
		$final_text = $text." ".$name;
		$keyword_count = $commonfunctions->keyword_extraction($final_text);

		$userid = $session->userid;
		if($type == 1) {
			$post = new BlogPost($user->getDatabaseClient());
			$retValue = $post->addNewPost($userid, $name, $tags, $text, $keyword_count);
		} else if($type == 2) {
			$article = new Article($user->getDatabaseClient());
			$retValue = $article->addNewArticle($userid, $name, $tags, $references, $text, $keyword_count);
		} else if ($type == 3) {
			$retValue = $article->editExistingArticle($userid, $articleid, $name, $tags, $references, $text, $keyword_count);
		} else {
			$retValue = false;
		}

		if($retValue) {
			$_SESSION['write_success'] = true;
			$_SESSION['write_message'] = $name." has been added. :)";
		} else {
			$_SESSION['write_success'] = false;
			$_SESSION['write_message'] = $name." couldnt be added/edited, try again!";
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->GetErrorArray();
		}
		header("location: write?type=".($type == 1 ? 'post' : 'article').($type == 3 ? '&articleid='.$articleid : ''));
		ob_flush();
	}
}
?>
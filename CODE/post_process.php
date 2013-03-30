<?php
/*ob_start();
	include_once('includes/session.php');
	if(!$session->loggedin) {
		header("location: index.php");
		ob_flush();
	}
chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');*/
?>
<?php
/*if(!(isset($_POST['postname']) && isset($_POST['tags']) && isset($_POST['post_text']))) {
	header("location: write_post.php");
	ob_flush();
}
	$postname = $_POST['postname'];
	$postname = strtolower(trim($postname));
	if(!$postname || strlen($postname) == 0) {
		$form->SetError("postname", "* post name not entered");
	}
	
	$temp_tags = $_POST['tags'];
	$temp_tags = trim($temp_tags);
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
		$temp_tags = NULL;
	}

	$post_text = str_replace("<div", " <div", $_POST['post_text']);
	if(!$post_text || strlen(strip_tags($post_text)) == 0) {
		$form->SetError("post_text", "* post not entered");
	}

	if($form->num_errors > 0) {
		$_SESSION['post_success'] = false;
		$_SESSION['post_message'] = $postname." couldnt be added, try again!";
		$_SESSION['value_array'] = $_POST;
		$_SESSION['error_array'] = $form->GetErrorArray();
		header("location: write_post.php");
		ob_flush();
	} else {
		
		// the keywords consists of words from post text and post name
		$stripped_post = html_entity_decode(strip_tags($post_text), ENT_NOQUOTES, "UTF-8")." ".html_entity_decode(strip_tags($postname), ENT_NOQUOTES, "UTF-8");
		$stripped_post = $commonfunctions->strip_punctuation($stripped_post);
		$stripped_post = strtolower($stripped_post);

		$keywords = explode(' ', $stripped_post);
		// now getting all the occurences of words (making values as the and counting) and removing "faltu" words
		$keyword_count = array();

		foreach ($keywords as $key => $value) {
			//removing unnecessary words
			$value = preg_replace('/&(?:[a-z\d]+|#\d+|#x[a-f\d]+);/i', '', $value);	// remove html entities
			$value = preg_replace('/[[:punct:]]/i', ' ', $value);	// remove puntucations
			// check for simple words
			$value = $commonfunctions->strip_simple_words($value);
			$value = trim($value);

			if($value == '' || strlen($value) == 0) {
				unset($keywords[$key]);
			} else {
				if(isset($keyword_count[$value])) {
					$keyword_count[$value]++;
				} else {
					$keyword_count[$value] = 1;
				}
			}
		}
		
		$userid = $session->userid;

		$post = new BlogPost($user->getDatabaseClient());
		if($post->addNewPost($userid, $postname, $tags, $post_text, $keyword_count)) {
			$_SESSION['post_success'] = true;
			$_SESSION['post_message'] = $postname." has been added. :)";
		} else {
			$_SESSION['post_success'] = false;
			$_SESSION['post_message'] = $postname." couldnt be added, try again!";
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->GetErrorArray();
		}

		header("location: write_post.php");
		ob_flush();
	}*/
?>
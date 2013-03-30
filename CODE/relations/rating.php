<?php
ob_start();

include_once('../includes/session.php');
if(!$session->loggedin) {
	header("location: ../index.php");
} else {
	chdir(dirname(__FILE__));
	include_once('../entities/article.php');

	if(isset($_POST['article']) && isset($_POST['version']) && isset($_POST['rating'])) {
		$articleid = intval($_POST['article']);
		$version = intval($_POST['version']);
		$rating = intval($_POST['rating']);

		if($rating > 0 && $rating < 6) {
			$article = new Article($user->getDatabaseClient());

			$article->addNewRating($session->userid, $articleid, $version, $rating);
		}
		header("location: ../article?id=".$articleid."&version=".$version);
	} else {
		header("location: ../index.php");
	}
}

ob_flush();
?>
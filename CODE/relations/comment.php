<?php
ob_start();

include_once('../includes/session.php');
if(!$session->loggedin) {
	header("location: ../index.php");
} else {
	chdir(dirname(__FILE__));
	include_once('../entities/comment.php');

	if(isset($_GET['postid']) && isset($_POST['submit']) && isset($_POST['comment'])) {
		$postid = intval($_GET['postid']);
		$comment = strip_tags($_POST['comment']);
		$type = $_POST['submit'];
		$userid = $session->userid;

		if($type == "Comment") {
			$comments = new Comments($user->getDatabaseClient());
			$comments->addNewComment($postid, $comment, $userid);
			header("location: ../post?id=".$postid);
		} else if($type == "Reply") {
			if(isset($_POST['commentid'])) {
				$comments = new Comments($user->getDatabaseClient());
				$parent = intval($_POST['commentid']);
				$comments->addNewReply($postid, $comment, $userid, $parent);
			}
			header("location: ../post?id=".$postid);
		} else {
			header("location: ../post?id=".$postid);
		}
	} else {
		header("location: ../index.php");
	}
}

ob_flush();
?>
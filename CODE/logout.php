<?php
ob_start();
	include_once('includes/session.php');
	$session->logout();
	header("location: index.php");
ob_flush();
?>
<?php
/*
	Processes the login and registration form
*/
?>
<?php
ob_start();
	include_once('includes/session.php');
?>
<?php
class Process {
	
	function __construct() {
		if(isset($_POST['type'])) {
			if($_POST['type'] == "1") {
				$this->registerUser();
			} else if($_POST['type'] == "2") {
				$this->login();
			} else if($_POST['type'] == "3"){
				$this->editAccount();
			} else {
				header("location: ".$_SERVER['HTTP_REFERRER']);
			}
		} else {
			header("location: index.php");
		}
		ob_flush();
	}

	function registerUser() {
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$emailid = $_POST['emailid'];
		$aboutme = $_POST['aboutme'];
		$interests = $_POST['interests'];
		if(isset($_FILES['profilepic'])) {
			$photo = $_FILES['profilepic'];
		}
		else {
			$photo = null;
		}

		global $session, $form;

		$result = $session->registerNewUser($firstname, $lastname, $username, $password, $emailid, $aboutme, $interests, $photo);
		if($result) {
			$_SESSION['success_registration'] = true;
			$_SESSION['success_registration_message'] = $username." successfully registered. :)";
			header("location: index.php");
		} else {
			$_SESSION['success_registration'] = false;
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->GetErrorArray();
			header("location: registration.php");
		}
		ob_flush();
	}

	function login() {
		global $session, $form;
		$username = $_POST['username'];
		$pass = $_POST['pass'];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$result = $session->login($username, $pass, $ip);

		if($result) {
			header("location: profile.php?username=".$username);
		} else {
			$_SESSION['success_login'] = false;
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->GetErrorArray();
			header("location: index.php");
		}
		ob_flush();
	}

	function editAccount() {
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$username = $_POST['username'];
		$oldpass = $_POST['oldpass'];
		$newpass = $_POST['newpass'];
		$emailid = $_POST['emailid'];
		$aboutme = $_POST['aboutme'];
		$interests = $_POST['interests'];
		if(isset($_FILES['profilepic'])) {
			$photo = $_FILES['profilepic'];
		}
		else {
			$photo = null;
		}

		global $session, $form;
		$result = $session->editAccount($firstname, $lastname, $username, $oldpass, $newpass, $emailid, $aboutme, $interests, $photo);

		if($result) {
			header("location: profile.php?username=".$username);
		} else {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->GetErrorArray();
			header("location: edit_profile.php");
		}
		ob_flush();
	}
};

$process = new Process;
?>
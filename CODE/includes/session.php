<?php
/*
	Contains the information for maintaining session
	and user information
*/
?>
<?php
	chdir(dirname(__FILE__));
	include_once('form.php');
	include_once('commonFunctions.php');
	include_once('../entities/user.php');
?>
<?php
class Session {

	var $username;
	var $userid;
	var $randomid;
	var $loggedin;
	var $time;
	var $referrer;

	function __construct() {
		$this->time = time();
		$this->startSession();
	}

	function startSession() {
		session_start();
		$this->loggedin = $this->checkLogin();

		if(!$this->loggedin) {
			$this->username = "Guest";
			$this->userid = -1;
		}

		$this->referrer = isset($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] : "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}

	function checkLogin() {
		if(isset($_SESSION['authenticated'])) {
			if(isset($_SESSION['userid']) && isset($_SESSION['rand_str']) && isset($_SESSION['username'])) {
				global $user;
				$retValue = $user->checkActiveUser($_SESSION['userid'], $_SESSION['rand_str']);

				if($retValue) {
					$this->userid = $_SESSION['userid'];
					$this->username = $_SESSION['username'];
					$this->randomid = $_SESSION['rand_str'];

					return true;
				}
				else {
					unset($_SESSION['userid']);
					unset($_SESSION['rand_str']);
					unset($_SESSION['username']);
					unset($_SESSION['authenticated']);
					return false;
				}
			} else {
				if(isset($_SESSION['userid']))
					unset($_SESSION['userid']);
				if(isset($_SESSION['rand_str']))
					unset($_SESSION['rand_str']);
				if(isset($_SESSION['username']))
					unset($_SESSION['username']);
				unset($_SESSION['authenticated']);
				return false;
			}
		} else {
			return false;
		}
	}

	function login($username, $password, $ip) {
		global $user, $form;

		/* error checking with username */
		$field = "username";
		$username = trim($username);
		if(!$username || (strlen($username)==0)) {
			$form->SetError($field, "* user name not entered");
		} else if(strlen($username) < 5 || strlen($username) > 30 || !preg_match("/^([0-9a-zA-Z])+$/", $username)) {
			$form->SetError($field, "* user name doest not exist");
		}

		/* Error checking with the password */
		$field = "password";
		if(!$password || (strlen($password) == 0)) {
			$form->SetError($field, "* password not entered");
		}
		else {
			$field = "login";
			$password = stripslashes($password);
			if(strlen($password) < 5) {
				$form->SetError($field, "username password do not match");
			}
			else {
				$password = trim($password);
				if(!(preg_match("/^([0-9a-zA-Z])+$/",$password))) {
					$form->SetError($field, "username password do not match");
				}
			}
		}

		if($form->num_errors > 0)
			return false;

		$password = sha1($password);

		//checking the username and password in database
		$retValue = $user->matchUsernamePassword($username, $password);
		
		if($retValue == -1) {
			$form->SetError("username", "* user name doest not exist");
			return false;
		} else if($retValue == 0) {
			$form->SetError("login", "username password do not match");
			return false;
		} else {
			$result = $this->createSession($retValue, $username, $ip);
			if($result) {
				return true;
			} else {
				$form->SetError("login", "There was some error in the error, try again later.");
				return false;
			}
		}
	}

	function logout() {
		if(isset($_SESSION['userid']))
			unset($_SESSION['userid']);
		if(isset($_SESSION['rand_str']))
			unset($_SESSION['rand_str']);
		if(isset($_SESSION['username']))
			unset($_SESSION['username']);
		if(isset($_SESSION['authenticated']))
			unset($_SESSION['authenticated']);
	}

	function createSession($userid, $username, $ip) {
		global $commonfunctions, $user;

		// set authenticated to true
		$_SESSION['authenticated'] = true;

		// generate a random string and save it in the session with userid, username.
		$this->userid = $_SESSION['userid'] = $userid;
		$this->randomid = $_SESSION['rand_str'] = $commonfunctions->generateRandStr(10);
		$this->username = $_SESSION['username'] = $username;

		$retValue = $user->addActiveUser($this->userid, $this->randomid, $ip);
		if($retValue) {
			//echo "creating session true!";
			return true;
		}
		else {
			//echo "cant create session";
			unset($_SESSION['authenticated']);
			unset($_SESSION['userid']);
			unset($_SESSION['rand_str']);
			unset($_SESSION['username']);
			return false;
		}
	}

	function registerNewUser($firstname, $lastname, $username, $password, $emailid, $aboutme, $interests, $photo) {
		global $form, $user;

		/* sanitizing firstname */
		$field = "firstname";
		$firstname = trim($firstname);
		if(!$firstname || (strlen($firstname)==0)) {
			$form->SetError($field, "* first name not entered");
		} else if(strlen($firstname) > 30) {
			$form->SetError($field, "* first name too long");
		} else if(!preg_match("/^([a-zA-Z])+$/", $firstname)) {
			$form->SetError($field, "* first name is invalid");
		}

		/* error checking with the last name */
		$field = "lastname";
		$lastname = trim($lastname);
		if($lastname && (strlen($lastname)!=0)) {
			if(strlen($lastname) > 30) {
				$form->SetError($field, "* last name too long");
			} else if(!preg_match("/^([a-zA-Z])+$/", $lastname)) {
				$form->SetError($field, "* last name is invalid");
			}
		}

		/* error checking with username */
		$field = "username";
		$username = trim($username);
		if(!$username || (strlen($username)==0)) {
			$form->SetError($field, "* user name not entered");
		} else if(strlen($username) < 5) {
			$form->SetError($field, "* user name too short");
		} else if(strlen($username) > 30) {
			$form->SetError($field, "* user name too long");
		} else if(!preg_match("/^([0-9a-zA-Z])+$/", $username)) {
			$form->SetError($field, "* user name should be alphanumeric");
		} else if($username == "Guest" || $username == "Admin" || $user->checkUsernameExist($username) != -1) {
			$form->SetError($field, "* user name already in use");
		}

		/* Error checking with the password */
		$field = "password";
		if(!$password || (strlen($password) == 0)) {
			$form->SetError($field, "* password not entered");
		}
		else {
			$password = stripslashes($password);
			if(strlen($password) < 5) {
				$form->SetError($field, "* password too short");
			}
			else {
				$password = trim($password);
				if(!(preg_match("/^([0-9a-zA-Z])+$/",$password))) {
					$form->SetError($field, "* password should be alphanumeric");
				}
			}
		}

		/* Error checking with email-ID */
		global $commonfunctions;
		$field = "emailid";
		$emailid = trim($emailid);
		$emailid = stripslashes($emailid);
		if(!$emailid || (strlen($emailid) == 0)) {
			$form->SetError($field, "* email-ID not entered");
		} else if(!$commonfunctions->checkEmail($emailid)) {
			$form->SetError($field, "* email-ID invalid");
		} else if($user->checkEmailIDExist($emailid) != -1) {
			$form->SetError($field, "* email-ID already exists.");
		}

		/* Error checking and filtering of about me */
		$field = "aboutme";
		$aboutme = trim($aboutme);
		if($aboutme && (strlen($aboutme) > 0)) {
			$aboutme = strip_tags($aboutme);
		} else {
			$aboutme = "";
		}

		/* Error checking the interests and exploding into different interests */
		$field = "interests";
		$interests = trim($interests);
		if($interests && (strlen($interests) > 0)) {
			$interests = strip_tags($interests);
			$interests = explode(",", $interests);

			foreach ($interests as $key => $value) {
				$interests[$key] = $value = trim($value);
				if(strlen($value) == 0)
					unset($interests[$key]);
			}
		} else {
			$interests = NULL;
		}

		/* Error checking with photo and storing it in the folder */
		if($form->num_errors > 0) {
			return false;		// As if there is any error in the form, no use of storing the photograph
		}

		/* As the username does not already exist till here, the username is new */
		// create a directory with name as username in userdata.
		/*chdir(USERDATA);
		if(is_dir(USERDATA.$username)) {
			rmdir(USERDATA.$username);
		}
		mkdir(USERDATA.$username);*/

		$field = "profilepic";
		if(!is_null($photo)) {
			if($photo['size'] == 0)
				$photourl = "";
			else {
				$allowed_extensions = array("jpeg", "jpg", "png");
				$allowed_types = array("image/jpeg", "image/jpg", "image/png");
				$extension = strtolower(end(explode(".", $photo['name'])));

				if($photo['error'] != 0) {
					$photo_error = "";
					foreach ($photo['error'] as $key => $value) {
						$photo_error .= ", ".$value;
					}
					$form->SetError($field, $photo_error);
				} else if(!(in_array($photo["type"], $allowed_types) && in_array($extension, $allowed_extensions))) {
					$form->SetError($field, "* File type is wrong.");
				} else if($photo['size'] > 1048576) {
					$form->SetError($field, "* file is too large.");
				}

				if($form->num_errors > 0) {
					return false;
				} else {
					// save the file in the user directory.
					$oldpath = $photo['tmp_name'];
					$newpath = USERDATA."$username.".$extension;
					chdir("..");
					if(file_exists($newpath)) {
						unlink($newpath);
					}
					move_uploaded_file($oldpath, $newpath);
					$photourl = realpath($newpath);
				}
			}
		} else {
			$photourl = "";
		}

		$password = sha1($password);
		$retValue = $user->addNewUSer($firstname, $lastname, $username, $password, $emailid, $aboutme, $interests, $photourl);

		if($retValue != -1) {
			return true;		
		} else {
			return false;
		}
	}

	function editAccount($firstname, $lastname, $username, $oldpass, $newpass, $emailid, $aboutme, $interests, $photo) {
		global $form, $user;
		$old_details = $user->getAllDetailsByUserid($this->userid);
		$userid = $this->userid;

		// var_dump($old_details);
		/* sanitizing firstname */
		$field = "firstname";
		$firstname = trim($firstname);
		if(!$firstname || (strlen($firstname)==0)) {
			$form->SetError($field, "* first name not entered");
		} else if(strlen($firstname) > 30) {
			$form->SetError($field, "* first name too long");
		} else if(!preg_match("/^([a-zA-Z])+$/", $firstname)) {
			$form->SetError($field, "* first name is invalid");
		}

		/* error checking with the last name */
		$field = "lastname";
		$lastname = trim($lastname);
		if($lastname && (strlen($lastname)!=0)) {
			if(strlen($lastname) > 30) {
				$form->SetError($field, "* last name too long");
			} else if(!preg_match("/^([a-zA-Z])+$/", $lastname)) {
				$form->SetError($field, "* last name is invalid");
			}
		}

		/* Error checking with the password */
		$no_password_change = false;
		$field = "newpass";
		if(!$newpass || (strlen($newpass)==0)) {
			$no_password_change = true;
		} else {
			$newpass = stripslashes($newpass);
			if(strlen($newpass) < 5) {
				$form->SetError($field, "* new password too short");
			}
			else {
				$newpass = trim($newpass);
				if(!(preg_match("/^([0-9a-zA-Z])+$/",$newpass))) {
					$form->SetError($field, "* new password should be alphanumeric");
				}
			}
		}
		$newpass = sha1($newpass);

		if(!$no_password_change) {
			$field = "oldpass";
			if(!$oldpass || (strlen($oldpass) == 0)) {
				$form->SetError($field, "* old password not entered");
			}
			else {
				$oldpass = stripslashes($oldpass);
				if(strlen($oldpass) < 5) {
					$form->SetError($field, "* old password incorrect");
				}
				else {
					$oldpass = trim($oldpass);
					if(!(preg_match("/^([0-9a-zA-Z])+$/",$oldpass))) {
						$form->SetError($field, "* old password incorrect");
					} else if($user->matchUsernamePassword($username, sha1($oldpass)) < 1) {
						$form->SetError($field, "* old password incorrect");
					}
				}
			}
		}

		/* Error checking with email-ID */
		global $commonfunctions;
		$field = "emailid";
		$emailid = trim($emailid);
		$emailid = stripslashes($emailid);
		$no_email_change = false;
		if(!$emailid || (strlen($emailid) == 0)) {
			$form->SetError($field, "* email-ID not entered");
		} else if(!$commonfunctions->checkEmail($emailid)) {
			$form->SetError($field, "* email-ID invalid");
		} else if($emailid == $old_details["emailid"]) {
			$no_email_change = true;
		}
		else if($user->checkEmailIDExist($emailid) != -1) {
			$form->SetError($field, "* email-ID already exists.");
		}

		/* Error checking and filtering of about me */
		$field = "aboutme";
		$aboutme = trim($aboutme);
		if($aboutme && (strlen($aboutme) > 0)) {
			$aboutme = strip_tags($aboutme);
		} else {
			$aboutme = "";
		}

		/* Error checking the interests and exploding into different interests */
		$field = "interests";
		$interests = trim($interests);
		if($interests && (strlen($interests) > 0)) {
			$interests = strip_tags($interests);
			$interests = explode(",", $interests);

			foreach ($interests as $key => $value) {
				$interests[$key] = $value = trim($value);
				if(strlen($value) == 0)
					unset($interests[$key]);
			}
		} else {
			$interests = NULL;
		}

		/* Error checking with photo and storing it in the folder */
		if($form->num_errors > 0) {
			return false;		// As if there is any error in the form, no use of storing the photograph
		}

		/* Photo */
		$field = "profilepic";
		if($photo['size'] == 0) {
			$photourl = $old_details['profilepic'];
		} else {
			$allowed_extensions = array("jpeg", "jpg", "png");
			$allowed_types = array("image/jpeg", "image/jpg", "image/png");
			$extension = strtolower(end(explode(".", $photo['name'])));

			if($photo['error'] != 0) {
				$photo_error = "";
				foreach ($photo['error'] as $key => $value) {
					$photo_error .= ", ".$value;
				}
				$form->SetError($field, $photo_error);
			} else if(!(in_array($photo["type"], $allowed_types) && in_array($extension, $allowed_extensions))) {
				$form->SetError($field, "* File type is wrong.");
			} else if($photo['size'] > 1048576) {
				$form->SetError($field, "* file is too large.");
			}

			if($form->num_errors > 0) {
				return false;
			} else {
				// save the file in the user directory.
				$oldpath = $photo['tmp_name'];
				$newpath = USERDATA.$username.".".$extension;
				$i = 1;
				chdir("..");
				while(file_exists($newpath)) {
					$newpath = USERDATA.$username."_".$i.".".$extension;
					$i++;
				}
				move_uploaded_file($oldpath, $newpath);
				$photourl = realpath($newpath);
			}
		}


		if($no_email_change) {
			if($no_password_change) {
				$retValue = $user->updateDetails($firstname, $lastname, $aboutme, $interests, $photourl, $userid);
			} else {
				$retValue = $user->updateDetailsPass($firstname, $lastname, $aboutme, $interests, $photourl, $newpass, $userid);
			}
		} else {
			if($no_password_change) {
				$retValue = $user->updateDetailsEmail($firstname, $lastname, $aboutme, $interests, $photourl, $emailid, $userid);
			} else {
				$retValue = $user->updateDetailsEmailPass($firstname, $lastname, $aboutme, $interests, $photourl, $emailid, $newpass, $userid);
			}
		}

		return $retValue;
	}
};

$session = new Session;
$form = new FormVals;
?>
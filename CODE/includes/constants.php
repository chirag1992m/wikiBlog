<?php
/*
	Contains all the required constants in one file
*/
?>

<?php
/*
	Database Connection
*/
class DATABASE_CONST {
	const USER = "mchirag";
	static $DATABASE = "mchirag_wikiblog";
	static $HOST = "localhost";
	static $PASS = "";
	
	public function getUser() {
		return self::USER;
	}
	
	public function getDatabase() {
		return self::$DATABASE;
	}
	
	public function getHost() {
		return self::$HOST;
	}

	public function getPass() {
		return self::$PASS;
	}
};
$Database_const = new DATABASE_CONST;
?>

<?php
	define("USERDATA", "");
	define("PROFILE_PATH", "");
?>

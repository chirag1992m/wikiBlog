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
	static $PASS = "asauca";
	
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
	define("USERDATA", "/var/www/others/wikiblog_code/userdata/");
	define("PROFILE_PATH", "http://10.11.0.41/others/wikiblog_code/userdata/");
?>
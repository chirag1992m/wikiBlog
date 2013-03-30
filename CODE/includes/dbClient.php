<?php
/*
	a general database connection class.
*/
?>
<?php
class DBQueryException extends Exception {};
class DBConnectionException extends Exception {};

class DB_Class {
	var $host;
	var $user;
	var $pas;
	var $database; 
	var $connection;
	var $result;
	var $current_stmt;
	
	function __construct($host, $user, $pas, $database) {
		$this->host = $host;
		$this->user = $user;
		$this->pas = $pas;
		$this->database = $database;

		$this->connect();
	}
	
	function connect() {
		$this->connection = new mysqli($this->host, $this->user, $this->pas, $this->database);
		if($this->connection->connect_errno)
			throw new DBConnectionException("couldn't connect to the database : ".$this->connection->connect_errno." - ".$this->connection->connect_error);
		else
			return true;
	}
	
	function prepare($query) {
		if($this->current_stmt) {
			$this->current_stmt->close();
		}
		if($this->current_stmt = $this->connection->prepare($query))
			return true;
		else
			return false;
	}

	function getLastError() {
		return $this->connection->error;
	}

	function lastInsertId() {
		return $this->connection->insert_id;
	}
	
	function __destruct() {
		$this->connection->close();
	}
};
?>
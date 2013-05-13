<?php

function db_connect($db_hostname = "localhost", $db_username = "symphony_user", $db_password = "d00h", $db_database = "symphony")
{
	$conn = null;

	$hostname = getenv("DB_HOST") ? getenv("DB_HOST") : $db_hostname;
	$username = getenv("DB_USERNAME") ? getenv("DB_USERNAME") : $db_username;
	$password = getenv("DB_PASSWORD") ? getenv("DB_PASSWORD") : $db_password;
	$database = getenv("DB_DATABASE") ? getenv("DB_DATABASE") : $db_database;

	if ($db_password != null) {
		$conn = mysql_connect($hostname, $username, $password) or die(mysql_error());
	} else {
		$conn = mysql_connect($hostname, $username) or die(mysql_error());
	}
	mysql_select_db($database, $conn);
	return $conn;
}

class Database {

	public static $db_hostname = null;
	public static $db_username = null;
	public static $db_password = null;
	public static $db_database = null;

	/**
	 * @static
	 * @return mysqli|null
	 */
	public static function mysqli($host = null, $user = null, $password = null, $database = null) {

		$myHostname = $host ? $host : self::$db_hostname;
		$myUsername = $user ? $user : self::$db_username;
		$myPassword = $password ? $password : self::$db_password;
		$myDatabase = $database ? $database : self::$db_database;

		$mysqli = new mysqli($myHostname, $myUsername, $myPassword, $myDatabase);
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: " . $mysqli->connect_error;
			return null;
		}
		return $mysqli;
	}

	public static function sqlite($dbfile = '/dat/local/db/player.db'){
		if($db = new SQLite3($dbfile)){
			return $db;	
		} else {
			echo "Failed to connect to SQLite database: " . $dbfile;	
		}
	}

}


?>

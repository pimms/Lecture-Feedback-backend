<?php

/**
 * @class Database
 * A static wrapper class around a PDO instance.
 */
class Database {
	private static $dbHandle;

	/**
	 * Open the database. 
	 *
	 * @return 
	 * true on success, false on failure
	 */
	public static function open() {
		try {
			self::$dbHandle = new PDO(
				"mysql:
				 hort=localhost;
				 dbname=lecture_feedback;", 
			    "lecture-feedback", "Cps7yHL");
			self::$dbHandle->setAttribute(
				PDO::ATTR_ERRMODE, 
				PDO::ERRMODE_EXCEPTION
			);
		} catch (PDOException $e) {
			echo "MySQL connection failed: " . $e->getMessage();
			return false;
		}

        return true;
	}

	/**
	 * Query the static PDO object.
	 *
	 * @param query
	 * The MySQL query to be performed
	 *
	 * @return
	 * A PDOStatement object on success, false on failure.
	 */
	public static function query($query) {
		return self::$dbHandle->query($query);
	}

	/**
	 * Execute an INSERT or UPDATE query
	 * on the database.
	 *
	 * @param query
	 * The MySQL query to be performed
	 *
	 * @return
	 * The number of affected rows
  	 */
	public static function exec($query) {
		return self::$dbHandle->exec($query);
	}

	/**
	 * Close the static PDO object
	 */
	public static function close() {
		self::$dbHandle = NULL;
	}
}

?>
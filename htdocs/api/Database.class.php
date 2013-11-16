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
	 * Close the static PDO object
	 */
	public static function close() {
		self::$dbHandle = NULL;
	}
}

?>
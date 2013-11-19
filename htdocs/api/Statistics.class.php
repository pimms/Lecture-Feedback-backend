<?php

require_once("Database.class.php");
require_once("Const.php");


/**
 * @class Statistics
 * Model class for retrieving and calculating statistics
 * about the reviews. 
 */
class Statistics {
	/**
	 * Retrieve the total amount of positive / negative 
	 * votes per course. As the database is "stringly typed",
	 * some string-hacking must be done. NO REGRETS.
	 *
	 * @param course
	 * The HiG code of the course.
	 *
	 * @return
	 * Associative array on the form:
	 * {"positive":N,"negative":M}
	 */
	public static function getTotalVotesForCourse($course) {
		// We get the number of positive votes by removing
		// all the "1"'s from the stringified ratings, and
		// we get the amount of total votes by multiplying 
		// the times we replace the ones with the number of
		// attributes.
		$count = NUM_ATTRIBUTES;
		$query = "SELECT SUM(len) AS positive, "
				." 		 COUNT(*) * {$count} as total "
				."FROM ( "
				."	SELECT "
				."		LENGTH(ratings) - "
				." 		LENGTH(REPLACE(ratings,'1','')) "
				."		AS len "
				."	FROM ReviewItem "
				." WHERE courseCode='{$course}' "
				.")T";
		
		Database::open();
		$stmt = Database::query($query);

		$pos = 0;
		$neg = 0;

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pos = (int)$row["positive"];
			$neg = (int)$row["total"] - $pos;	
		} 

		$arr = Array();
		$arr["positive"] = $pos;
		$arr["negative"] = $neg;
		return $arr;
	}
}

?>
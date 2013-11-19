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
		$where = "WHERE course='{$course}'";
		return self::getTotalVotes($course);
	}


	/**
	 * Get the total number of votes from a lecture.
	 *
	 * @param hash
	 * The lecture hash.
	 *
	 * @return
	 * {"positive":M, "negative",N}
	 */
	public static function getTotalVotesForLecture($hash) {
		$where = "WHERE hash='{$hash}'";
		return self::getTotalVotes($where);
	}



	/**
	 * Return the number of positive / negative votes for 
	 * a set of lectures.
	 * 
	 * @param whereClause
	 * The FULL WHERE-clause of the query, or null for 
	 * unconditional querying. Example: "WHERE 1=1"
	 *
	 * @return
	 * {"positive":M, "negative":N}
	 */
	private static function getTotalVotes($whereClause) {
		$query = self::getVoteCountQuery($whereClause);
		
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


	/**
	 * Returns the query that will retrieve the number of votes
	 * for a set of lectures. Use the whereClause parameter to
	 * define how the set is generated.
	 *
	 * @param whereClause
	 * The FULL WHERE-clause of the query, or null for 
	 * unconditional querying. Example: "WHERE 1=1"
	 *
	 * @param return
	 * Queryyyyy
	 */
	private static function getVoteCountQuery($whereClause) {
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
				." {$whereClause} "
				.")T";
		return $query;
	}
}

?>
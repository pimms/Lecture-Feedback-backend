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
	 * CSV string of the HiG course codes to be included
	 * in the result set.
	 *
	 * @return
	 * See ../index.html
	 */
	public static function getTotalVotesForCourse($courses) {
		$courses = explode(",", $courses);
		$query = self::getCourseVotesQuery($courses);

		Database::open();
		$stmt = Database::query($query);

		$json = Array(	"item_count" => $stmt->rowCount(),
						"items" => Array()  );

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = Array();
			$item["course_code"] 	= $row["courseCode"];
			$item["positive"] 		= $row["positive"];
			$item["negative"] 		= $row["total"] - $row["positive"];

			$json["items"][] = $item;
		}

		return $json;
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
	 * Get the total number of votes on one or more lecturers
	 *
	 * @param courses
	 * The courses to filter the lecturers by, or NULL.
	 *
	 * @return
	 * {"num_items":N, "items":{ "lecturer":"..", "positive":P, "negative":N } }
	 */
	public static function getTotalVotesForLecturer(array $courses) {
		$query = self::getLecturerVoteCountQuery($courses);

		Database::open();

		$stmt = Database::query($query);

		$allItems = array(	"item_count" => $stmt->rowCount()
							"items" => Array() );

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = array();

			$item["lecturer"] = $row["lecturer"];
			$item["positive"] = $row["positive"];
			$item["negative"] = $row["total"] - $item["positive"];

			$allItems["items"][] = $item;
		}

		return $allItems;
	}

	/** 
	 * Returns "count" lectures in a course, starting at "first".
	 * Maps directly to getStats?action=lecture_votes_all.
	 *
	 * @param course
	 * The HiG course code for the course.
	 *
	 * @param first
	 * The first item to be returned in the result set.
	 *
	 * @param count
	 * The maximum number of items to be returned in the result set.
	 *
	 * @return
	 * See ../index.html.
	 */
	public static function getAllLectures($course, $first, $count) {
		$query = self::getAllLecturesQuery($course, $first, $count);
		Database::open();
		$stmt = Database::query($query);

		$json = Array(	"first" => $first, 
						"count" => $count,
						"item_count" => $stmt->rowCount(),
						"items" => Array() );
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = Array();

			// Calculate the time and format it on the form "HH:mm - HH:mm".
			$start = new DateTime();
			$start->setTimeStamp($row["startTime"]);
			$end = new DateTime();
			$end->setTimeStamp($row["endTime"]);
			$time = $start->format("H:i") ." - ". $end->format("H:i");
			$date = $start->format("Y-m-d");

			$item["course_name"]	= $row["courseName"];
			$item["course_code"] 	= $row["courseCode"];
			$item["lecturer"] 		= $row["lecturer"];
			$item["time"] 			= $time;
			$item["date"] 			= $date;
			$item["room"] 			= $row["room"];-
			$item["positive"] 		= $row["positive"];
			$item["negative"] 		= $row["total"] - $row["positive"];

			$json["items"][] = $item;
		}

		return $json;
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

	/**
	 * Returns a query that will return the number of votes per
	 * course.
	 *
	 * @param courses
	 * Array containing the HiG codes
	 *
	 * @return
	 * Query that will return the course code and votes for each
	 * course.
	 */
	private static function getCourseVotesQuery(array $courses) {
		foreach ($courses as & $course) { $course = "'{$course}'"; }
		$csv = implode(", ", $courses);

		$count = NUM_ATTRIBUTES;
		$query = "SELECT SUM(len) AS positive, "
				." 		 COUNT(*) * {$count} as total, "
				."		 courseCode "
				."FROM ( "
				."	SELECT "
				."		LENGTH(ratings) - "
				." 		LENGTH(REPLACE(ratings,'1','')) "
				."		AS len, courseCode "
				."	FROM ReviewItem "
				."  WHERE courseCode IN ( {$csv} ) "
				.")T "
				."GROUP BY courseCode ";
		return $query;
	}

	private static function getAllLecturesQuery($course, $first, $count) {
		$count = NUM_ATTRIBUTES;
		$query = "SELECT "
				."	SUM(len) AS positive,"
				."	COUNT(*)*{$count} as total, "
				."	hash, courseName, courseCode, "
				."	startTime, endTime, lecturer, room "
				."FROM ( "
				."	SELECT LENGTH(ratings) - LENGTH(REPLACE(ratings,'1','')) AS len, "
				."			hash, courseName, courseCode, "
				." 			startTime, endTime, lecturer, room "
				."	FROM ReviewItem "
				."	WHERE courseCode='{$course}' "
				.")T "
				."GROUP BY hash "
				."ORDER BY startTime DESC, endTime ASC, positive DESC "
				."LIMIT {$first}, {$count}";

		return $query;
	}


	/**
	 * Returns a query that will give the total number and number of positive
	 * votes for all lecturers. If courses contains any courses, only lecturers
	 * that have tought those courses will be returned.
	 *
	 * @param courses
	 * Array of HiG course codes, or NULL.
	 *
	 * @return
	 * SQL Query string
	 */
	private static function getLecturerVoteCountQuery(array $courses) {
		$lectureWhere = "";
		if ($courses != NULL) {
			foreach ($courses as & $course) { $course = "'{$course}'"};
			$csv = implode(", " $courses);
			$lectureWhere = "WHERE courseCode IN ( {$csv} ) "
		}

		$query = "	SELECT SUM(len) AS positive, "
       			."	COUNT(*) * 5 as total, lecturer "
				."	FROM ( "
				." 		SELECT "
				." 		LENGTH(ratings) - "
    			."		LENGTH(REPLACE(ratings,'1','')) "
				."		AS len, lecturer "
				."		FROM ReviewItem "
    			." 		WHERE lecturer IN ( "
    			."			SELECT DISTINCT lecturer "
    			. 			$lectureWhere 
    			."		) "
				."	)t " 
				." 	GROUP BY lecturer "
				." 	HAVING total >= 5 ";

		return $query;
	}
}

?>
<?php

require_once("Review.class.php");
require_once("Database.class.php");

/**
 * @class ReviewFeed
 * Retrieves feeds. Who would have thought?
 * Given a list of courses (HiG notation (IMTxxxx)), relevant
 * reviews are returned.
 */
class ReviewFeed {
	/**
	 * Get an array of feed items.
	 *
	 * @param filter
	 * Array of string containing HiG course codes
	 *
	 * @param first
	 * The first item in the total list to be returned 
	 *
	 * @param count
	 * The maximum number of items to be retrieved.
	 *
	 * @return
	 * Array of Review-objects, or false if an error was
	 * occured.
	 */
	public function getFeed($filter, $first, $count) {
		if (!Database::open()) {
			return false;
		}

		$result = array();	
		$query = $this->getQuery($filter, $first, $count);
		$stmt = Database::query($query);

		if ($stmt == false) {
			echo "failed to read :(";
			return false;
		}
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ratings = explode(".", $row["ratings"]);

	        $obj = new Review();
	        $obj->setAllValues( 
	        	$row["id"], 
	        	$row["courseName"], 
	        	$row["courseCode"], 
	        	$row["lecturer"], 
	        	$row["startTime"], 
	        	$row["endTime"], 
	        	$row["room"], 
	        	$ratings,
	        	$row["comment"], 
	        	$row["reviewTime"]);
	        $result[] = $obj;
	    }

		return $result;
	}


	private function getQuery($filter, $first, $count) {
		$query = "	SELECT * FROM ReviewItem ";

		$query .= $this->getQueryWhere($filter);

		$query .= " ORDER BY reviewTime DESC ";
		$query .= " LIMIT $first, $count";

		return $query;
	}

	private function getQueryWhere($filter) {
		$where = "WHERE courseCode IN (";
		$where .= "\"{$filter[0]}\"";

		for ($i = 1; $i < count($filter); $i++) {
			$where .= ", \"{$filter[$i]}\" ";
		}

		$where .= " ) ";

		return $where;
	}
}
?>
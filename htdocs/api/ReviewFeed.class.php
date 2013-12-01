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
	 * Get an array of feed items. Note that either FILTER or HASH
	 * must be defined.
	 *
	 * @param filter
	 * Array of string containing HiG course codes
	 * 
	 * @param hash
	 * The hash of a single lecture.
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
	public function getFeed($filter, $hash, $first, $count) {
		if (isset($filter) == isset($hash)) {
			die(json_encode(Array(	"status"=>"bad", 
									"reason"=>"Both filter and hash is defined")));
		}

		if (!Database::open()) {
			return false;
		}

		$result = array();	
		$query = $this->getQuery($filter, $hash, $first, $count);
		$stmt = Database::query($query);

		if ($stmt == false) {
			echo "failed to read :(";
			return false;
		}
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			/* Convert the "1" and "0"s to true and false. */
			$ratings = explode(".", $row["ratings"]);
			foreach ($ratings as & $r) {
				$r = ($r == "1") ? true : false;
			}


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
	        	strtotime($row["reviewTime"]),
	        	$row["num_clones"]);
	        $result[] = $obj;
	    }

		return $result;
	}


	private function getQuery($filter, $hash, $first, $count) {
		$query = "	SELECT * FROM ReviewItem ";

		$query .= $this->getQueryWhere($filter, $hash);

		$query .= " ORDER BY reviewTime DESC ";
		$query .= " LIMIT $first, $count";

		return $query;
	}

	private function getQueryWhere($filter, $hash) {
		$where = "";

		if ($filter != null) {
			$where = " WHERE courseCode IN (";
			$where .= "\"{$filter[0]}\"";

			for ($i = 1; $i < count($filter); $i++) {
				$where .= ", \"{$filter[$i]}\" ";
			}

			$where .= " ) ";
		}

		if ($hash != null) {
			$where = " WHERE hash='{$hash}' ";
		}

		return $where;
	}
}
?>
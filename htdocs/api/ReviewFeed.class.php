<?php

require_once("Review.class.php");

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
	 * Array of string containing HiG course IDs ("IMTxxxx")
	 *
	 * @param first
	 * The first item in the total list to be returned 
	 *
	 * @param count
	 * The maximum number of items to be retrieved.
	 *
	 * @return
	 * Array of Review-objects.
	 */
	public function getFeed($filter, $first, $count) {
		$result = array();

		/* Return some dummy values */
		$obj = new Review("Fag A", "Professor Robertsen", "10:00 - 13:00", (new DateTime())->format('Ymd'), 
			              "K105", array(false, false, false, true, true), "HEISANN MAMMA");
		$result[] = $obj;

		$obj = new Review("Fag B", "Professor Robertsen", "12:00 - 15:00", (new DateTime())->format('Ymd'), 
			              "K105", array(false, false, false, true, true), "digger faget, yolo");
		$result[] = $obj;

		return $result;
	}
}
?>
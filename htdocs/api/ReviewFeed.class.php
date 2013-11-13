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
		
	}
}
?>
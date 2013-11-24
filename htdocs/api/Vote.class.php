<?php

require_once("Database.class.php");

/**
 * @class Vote
 * Static class for voting up (cloning) or down (flagging).
 */
class Vote {
	/**
	 * Add a positive vote to the review with the 
	 * specified ID.
	 *
	 * @param reviewId
	 * The unique ID of the review to be voted up.
	 *
	 * @return
	 * true if success, otherwise false.
	 */
	public function cloneReview($reviewId) {
		$query = $this->getCloneQuery($reviewId);
		return $this->performUpdate($query)
	}

	/**
	 * Flag the review with the specified ID as
	 * profane or inappropriate.
	 *
	 * @param reviewId
	 * The unique ID of the review to be flagged.
	 *
	 * @return
	 * true if success, otherwise false.
	 */
	public function flagProfanity($reviewId) {
		$query = $this->getProfanityQuery($reviewId);
		return $this->performUpdate($query);
	}


	private function performUpdate($updateQuery) {
		if (!Database::open()) {
			return false;
		}

		$query = $this->getCloneQuery($reviewId);
		$rows = Database::exec($query);

		return ($rows != 0);
	}


	private function getCloneQuery($reviewId) {
		$query = "UPDATE ReviewItem "
				."SET num_clones = num_clones + 1 "
				."WHERE id = {$reviewId}";
		return $query;
	}

	private function getProfanityQuery($reviewId) {
		$query = "UPDATE ReviewItem "
				."SET negative_flags = negative_flags + 1 "
				."WHERE id = {$reviewId}";
		return $query;
	}
}

?>
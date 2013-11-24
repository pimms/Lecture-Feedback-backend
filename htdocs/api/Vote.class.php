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
	public function voteUp($reviewId) {
		$query = $this->getUpVoteQuery($reviewId);
		return $this->performUpdate($query);
	}

	/**
	 * Add a negative vote to the review with the
	 * specified ID.
	 *
	 * @param reviewId
	 * The unique ID of the review to be voted down.
	 *
	 * @return
	 * true if success, otherwise false.
	 */
	public function voteDown($reviewId) {
		$query = $this->getDownVoteQuery($reviewId);
		return $this->performUpdate($query);
	}


	private function performUpdate($updateQuery) {
		if (!Database::open()) {
			return false;
		}

		$rows = Database::exec($updateQuery);

		return ($rows != 0);
	}


	private function getUpVoteQuery($reviewId) {
		$query = "UPDATE ReviewItem "
				."SET num_clones = num_clones + 1 "
				."WHERE id = {$reviewId}";
		return $query;
	}

	private function getDownVoteQuery($reviewId) {
		$query = "UPDATE ReviewItem "
				."SET negative_flags = negative_flags + 1 "
				."WHERE id = {$reviewId}";
		return $query;
	}
}

?>
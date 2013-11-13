<?php

/**
 * @class Review
 * Contains a review about a single lecture
 */
class Review {
	/**
	 * INT containing the unique ID of this review
	 */
	private $id;

	/**
	 * STRING containing the name of the course.
	 */
	private $courseName;

	/**
	 * STRING containing the name of the lecturer(s).
	 */
	private $lecturer;

	/**
	 * STRING containing the time on the form "hh:mm - hh:mm"
	 */
	private $time;

	/**
	 * DATE containing the date of the lecture
	 */
	private $date;

	/**
	 * STRING containing the room in which the lecture was held.
	 */
	private $room;

	/**
	 * BOOL ARRAY containing the ratings of the various attributes.
	 */
	private $ratings;

	/**
	 * STRING containing the comment.
	 */
	private $comment;


	public function __construct($id, $courseName, $lecturer, $time, 
								$date, $room, $ratings, $comment) {
		$this->id = $id;
		$this->courseName = $courseName;
		$this->lecturer = $lecturer;
		$this->time = $time;

		$this->date = $date;
		$this->room = $room;
		$this->ratings = $ratings;
		$this->comment = $comment;
	}


	public function getAssocArray() {
		$arr = array();

		$arr["id"] 		 = $this->getId();
		$arr["course"] 	 = $this->getCourseName();
		$arr["lecturer"] = $this->getLecturer();
		$arr["time"] 	 = $this->getTime();
		$arr["date"] 	 = $this->getDate();
		$arr["room"] 	 = $this->getRoom();
		$arr["ratings"]  = $this->getRatings();
		$arr["comment"]  = $this->getComment();

		return $arr;
	}


	public function getId() {
		return $this->id;
	}

	public function getCourseName() {
		return $this->courseName;
	}

	public function getLecturer() {
		return $this->lecturer;
	}

	public function getTime() {
		return $this->time;
	}

	public function getRoom() {
		return $this->room;
	}

	public function getDate() {
		return $this->date;
	}

	public function getRatings() {
		return $this->ratings;
	}

	public function getComment() {
		return $this->comment;
	}
}

?>
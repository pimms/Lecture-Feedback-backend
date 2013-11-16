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
	 * STRING containing the 20-char SHA-1 hash
	 * identifying the posting user.
	 */
	private $clientHash;

	/**
	 * STRING containing the name of the course.
	 * Example: "Mobile Development Project"
	 */
	private $courseName;

	/**
	 * STRING containing the code of the course.
	 * Example: "IMT3672"
	 */
	private $courseCode;

	/**
	 * STRING containing the name of the lecturer(s).
	 */
	private $lecturer;

	/**
	 * STRING containing the time on the form "hh:mm - hh:mm"
	 */
	private $time;

	/**
	 * STRING containing the date of the lecture on the form "yyyy-MM-dd"
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

	/**
	 * UNIX TIMESTAMP of the review time.
	 */
	private $reviewTime;

	/**
	 * @param id 
	 * -1 if unkown, the unique ID of the review otherwise.
	 *
	 * @param courseName
	 * The name of the course. Example: "Mobile Development Project".
	 *
	 * @param courseCode 
	 * The HiG code for this course. Example: "IMT3672".
	 *
	 * @param lecturer
	 * The name of the lecturer as it appears on TimeEdit.
	 *
	 * @param startTime
	 * UNIX timestamp for the starting time of the lecture. 
	 * STRING or INT.
	 *
	 * @param endTime
	 * UNIX timestamp for the ending time of the lecture.
	 * STRING or INT.
	 *
	 * @param room
	 * The room in which the lecture is held, exactly as it
	 * is displayed on TimeEdit.
	 *
	 * @param ratings 
	 * Boolean array containing the ratings of the attributes.
	 * Example: [0, 0, 1, 1, 0]
	 *
	 * @param comment
	 * The comment of the lecture, or NULL if none was given.
	 *
	 * @param reviewTime
	 * The unix timestamp of the review if this object was 
	 * read from a database, undefined otherwise.
	 */
	public function __construct($id, 
								$courseName, 
								$courseCode,
								$lecturer, 
								$startTime, 
								$endTime, 
								$room, 
								array $ratings, 
								$comment, 
								$reviewTime) {
		$this->id = $id;
		$this->courseName = $courseName;
		$this->courseCode = $courseCode;
		$this->lecturer = $lecturer;
		
		$this->room = $room;
		$this->ratings = $ratings;
		$this->comment = $comment;

		// Set "time" and "date" 
		$this->setTimeFieldsFromUnix($startTime, $endTime);

		// Set the review time
		if (isset($reviewTime)) {
			$this->reviewTime = (int)$reviewTime;
		} else {
			$date = new DateTime();
			$this->reviewTime = $date->getTimestamp();
		}
	}


	/**
	 * Populate the object with values from 
	 * POST-parameters.
	 */
	public function createFromPostParameters() {
		/* Get all absolute values */
		$this->clientHash 	= $_POST["client_hash"];
		$this->courseName 	= $_POST["course_name"];
		$this->courseCode 	= $_POST["course_code"];
		$this->lecturer 	= $_POST["lecturer"];
		$this->room 		= $_POST["room"];
		$this->comment 		= $_POST["comment"];

		/* Derive date values */
		$unixStart = $_POST["start_time"];
		$unixEnd = $_POST["end_time"];
		$this->setTimeFieldsFromUnix($unixStart, $unixEnd);

		/* Create the attribute array */
		$attrs = explode(".", $_POST["attributes"]);
		$this->ratings = Array();
		foreach ($attrs as $a) {
			$this->ratings[] = boolean($a);
		}
	}


	public function getAssocArray() {
		$arr = array();

		$reviewTime = new DateTime();
		$reviewTime->setTimestamp($this->reviewTime);

		$arr["id"] 		 	= $this->getId();
		$arr["course"] 	 	= $this->getCourseName();
		$arr["lecturer"] 	= $this->getLecturer();
		$arr["time"] 	 	= $this->getTime();
		$arr["date"] 	 	= $this->getDate();
		$arr["room"] 	 	= $this->getRoom();
		$arr["ratings"]  	= $this->getRatings();
		$arr["comment"]  	= $this->getComment();
		$arr["review_time"] = $reviewTime->format("Y-m-d H:m");

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


	/**
	 * Set the member attributes "time" and "date" from
	 * start and end UNIX timestamps.
	 *
	 * @param unixStart
	 * The unix timestamp of the lecture starting time.
	 *
	 * @param unixEnd
	 * The unix timestamp of the lecture ending time. Must be 
	 * the same DAY as unixStart.
	 */
	public function setTimeFieldsFromUnix($unixStart, $unixEnd) {
		$date = new DateTime();

		/* Set the date */
		$date->setTimestamp((int)$unixStart);
		$this->date = $date->format("Y-m-d");

		// Get the starting time
		$startTime = $date->format("H:m");

		// Set the end-time and receive the string value.
		$date->setTimestamp((int)$unixEnd);
		$endTime = $date->format("H:m");

		// Concatenate the strings into the final value
		$this->time = $startTime . " - " . $endTime;
	}
}

?>
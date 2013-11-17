<?php

require_once("Utils.php");
require_once("Database.class.php");

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
	 * UNIX TIMESTAMP of the start time of the lecture
	 */
	private $startTime;

	/**
	 * UNIX TIMESTAMP of the end time of the lecture
	 */
	private $endTime;

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


	public function __construct() {
		/* Do jack diddly squat yo */
	}

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
	public function setAllValues($id, 
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
			$this->reviewTime = $reviewTime;
		} else {
			$date = new DateTime();
			$this->reviewTime = $date->getTimestamp();
		}
	}


	/**
	 * Populate the object with values from 
	 * POST-parameters.
	 *
	 * @return 	
	 * true if validation succeeded, false otherwise.
	 */
	public function createFromAssoc($assoc) {
		if (!$this->validateCreationAssoc($assoc)) {
			return false;
		}

		/* Get all absolute values */
		$this->clientHash 	= $assoc["client_hash"];
		$this->courseName 	= $assoc["course_name"];
		$this->courseCode 	= $assoc["course_code"];
		$this->lecturer 	= $assoc["lecturer"];
		$this->room 		= $assoc["room"];

		if (isset($assoc["comment"])) {
			$this->comment 		= $assoc["comment"];
		}

		/* Derive date values */
		$unixStart = $assoc["start_time"];
		$unixEnd = $assoc["end_time"];
		$this->setTimeFieldsFromUnix($unixStart, $unixEnd);

		/* Set the current time as the review time */
		$this->reviewTime = (new DateTime())->getTimestamp();

		/* Create the attribute array */
		$attrs = explode(".", $assoc["attributes"]);
		$this->ratings = Array();
		foreach ($attrs as $a) {
			$this->ratings[] = $a ? "1" : "0";
		}

		return true;
	}

	/**
	 * Validate an associative array.
	 *
	 * @param assoc
	 * The array to be validatet. Usually _GET or _POST.
	 *
	 * @return
	 * TRUE of the array can be used for creation, FALSE otherwise.
	 */
	private function validateCreationAssoc($assoc) {
		if (!getFromAssoc($assoc, "client_hash", NULL)) return false;
		if (!getFromAssoc($assoc, "course_name", NULL)) return false;
		if (!getFromAssoc($assoc, "course_code", NULL)) return false;
		if (!getFromAssoc($assoc, "start_time", NULL)) return false;
		if (!getFromAssoc($assoc, "end_time", NULL)) return false;
		if (!getFromAssoc($assoc, "lecturer", NULL)) return false;
		if (!getFromAssoc($assoc, "room", NULL)) return false;
		if (!(getFromAssoc($assoc, "attribute_version_set", NULL) == 1)) return false;
		if (!getFromAssoc($assoc, "attributes", NULL)) return false;

		return true;
	}


	public function getAssocArray() {
		$arr = array();

		$reviewTime = new DateTime();
		$reviewTime->setTimestamp($this->reviewTime);

		$arr["id"] 		 	= $this->getId();
		$arr["course_name"]	= $this->getCourseName();
		$arr["course_code"] = $this->getCourseCode();
		$arr["lecturer"] 	= $this->getLecturer();
		$arr["time"] 	 	= $this->getTime();
		$arr["date"] 	 	= $this->getDate();
		$arr["room"] 	 	= $this->getRoom();
		$arr["ratings"]  	= $this->getRatings();
		$arr["comment"]  	= $this->getComment();
		$arr["review_time"] = $reviewTime->format("Y-m-d H:i");

		return $arr;
	}


	/**
	 * Write the contents of this review to
	 * database.
	 *
	 * @return
	 * TRUE if write succeeded, FALSE otherwise.
	 */
	public function writeToDatabase() {
		if (!Database::open()) {
			return false;
		}

		/* Create a dot-separated string of the rating values */
		$ratingDSV = implode(".", $this->ratings);

		/* Don't insert the comment value if the value
		 * doesn't exist. 
		 */
		$commentValue = "";
		$commentColumn = "";
		if ($this->comment && strlen($this->comment) > 0) {
			$commentColumn = ", comment ";
			$commentValue = ", \"" . $this->comment . "\"";
		}

		$query = "INSERT INTO ReviewItem(
					courseName, courseCode, 
					lecturer, 	startTime, 
					endTime, 	room,
					ratings   {$commentColumn}
			 		) 
					VALUES (
					\"{$this->courseName}\",
					\"{$this->courseCode}\",
					\"{$this->lecturer}\", 	
					{$this->startTime},
					{$this->endTime}, 	
					\"{$this->room}\", 
					\"{$ratingDSV}\"
					{$commentValue}
					)";

		$result = Database::query($query);
		Database::close();

		return $result;
	}


	public function getId() {
		return $this->id;
	}

	public function getCourseName() {
		return $this->courseName;
	}

	public function getCourseCode() {
		return $this->courseCode;
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
		$this->startTime = $unixStart;
		$this->endTime = $unixEnd;

		$date = new DateTime();

		/* Set the date */
		$date->setTimestamp((int)$unixStart);
		$this->date = $date->format("Y-m-d");

		// Get the starting time
		$startTime = $date->format("H:i");

		// Set the end-time and receive the string value.
		$date->setTimestamp((int)$unixEnd);
		$endTime = $date->format("H:i");

		// Concatenate the strings into the final value
		$this->time = $startTime . " - " . $endTime;
	}
}

?>
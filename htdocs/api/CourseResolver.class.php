<?php

/**
 * @class CourseResolver
 * Class that resolved HiG classes into courses.
 */
class CourseResolver {
	private $filter;
	private $startDate;
	private $endDate;

	/**
	 * @param filter
	 * The filter for TimeEdit. Must be a CSV of the 
	 * TimeEdit IDs to be included in the final result.
	 */
	public function __construct($filter) {
		// Add "OR" separators (-1) between the filter items
		$filter =  join(",-1,", explode(",", $filter));
		$this->filter = $filter;
	}

	/**
	 * @param startDate
	 * The first date to be included in the search, of type
	 * STRING on the form "yyyyMMdd" (Ymd)
	 *
	 * @param endDate
	 * The last date to be included in the search, of type
	 * STRING on the form "yyyyMMdd" (Ymd)
	 */
	public function setDates($startDate, $endDate) {
		$this->startDate = $startDate;
		$this->endDate = $endDate;
	}


	/** 
	 * Get the ICal file from TimeEdit
	 *
	 * @return
	 * The contents of the ICal file.
	 */
	public function getICal() {
		$url = $this->getICalURL();
		return file_get_contents($url);
	}

	/**
	 * Get the URL for the ICal feed.
	 */
	public function getICalURL() {
		$url = "https://web.timeedit.se/hig_no/db1/open/r.ics?sid=3&p=";
		$url .= $this->startDate . ".x%2C";
		$url .= $this->endDate . ".x&objects=";
		$url .= $this->filter . "&ox=0&types=0&fe=0&l=en&pp=f";
		return $url;
	}


	/**
	 * Set default dates - 2 weeks back, 2 weeks forward.
	 */
	private function setDefaultDates() {
		$date = new DateTime();

		$date->modify('-2 week');
		$start = $date->format('Ymd');

		$date->modify('+4 week');
		$end = $date->format('Ymd');

		$this->setDates($start, $end);
	}

	/**
	 * TimeEdit classes are defined by a ".182" postfix, and
	 * must be resolved into courses (".183" postfix).
	 *
	 * @return
	 * True if any filter item is a class, false if all
	 * items in the filter are courses.
	 */
	public function doesFilterContainClasses() {
		foreach (explode(", ", $this->filter) as $item) {
			if (explode(".", $item)[1] === "182") {
				return true;
			}
		}

		return false;
	}


	/**
	 * Retrieve all related course names from the passed filter.
	 *
	 * @param filter
	 * Array of TimeEdit IDs. TimeEdit class and course IDs will
	 * be resolved into HiG course IDs (e.g., IMTxxxx).
	 *
	 * @return
	 * Array of HiG course IDs
	 */ 
	public function resolveCourses() {
		$ical = $this->getICal();
		$courses = array();

		$lines = explode("\r\n", $ical);
		foreach ($lines as $line) {
			if (substr($line, 0, 8) == "SUMMARY:") {
				$course = substr($line, 8);
				if (gettype($course) === "string" && !in_array($course, $courses)) {
					$courses[] = $course;
				}
			}
		}

		return $courses;
	}
}
?>
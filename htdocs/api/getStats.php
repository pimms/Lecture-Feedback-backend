<?php

require_once("Statistics.class.php");
require_once("Utils.php");


function dieBad() {
	echo json_encode(Array("status" => "bad"));
	die;
}



$json = Array("status" => "ok");
$action = getFromAssoc($_GET, "action", false) or dieBad();

if ($action == "course_votes") {
	$course = getFromAssoc($_GET, "courses", false) or dieBad();
	$json = array_merge($json, Statistics::getTotalVotesForCourse($course));
} else if ($action == "lecture_votes") {
	$hash = getFromAssoc($_GET, "hash", false) or dieBad();
	$json = array_merge($json, Statistics::getTotalVotesForLecture($hash));
} else if ($action == "lecture_votes_all") {
	$course = getFromAssoc($_GET, "course", false) or dieBad();
	$first = getFromAssoc($_GET, "first", 0);
	$count = getFromAssoc($_GET, "count", 25);
	$json = array_merge($json, Statistics::getAllLectures($course, $first, $count));
} else {
	dieBad();
}

echo json_encode($json);

?>
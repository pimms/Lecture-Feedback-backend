<?php

require_once("CourseRetriever.class.php");


$json = array();


if (isset($_GET["filter"])) {
	$courseRetriever = new CourseRetriever($_GET["filter"]);
	$filter = explode(",", $_GET["filter"]);
	$courses = $courseRetriever->resolveCourses();
	$json["courses"] = $courses;
	$json["status"] = "ok";
} else {
	$json["status"] = "bad";
}


echo json_encode($json) . "\n";

?>
<?php

require_once("CourseResolver.class.php");


$json = array();


if (isset($_GET["filter"])) {
	$courseResolver = new CourseResolver($_GET["filter"]);
	$courses = $courseResolver->resolveCourses();
	

} else {
	$json["status"] = "bad";
}


echo json_encode($json) . "\n";

?>
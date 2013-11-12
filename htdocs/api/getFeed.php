<?php

require_once("TimeEditICS.class.php");


$json = array();


if (isset($_GET["filter"])) {
	$timeEdit = new TimeEditICS($_GET["filter"]);
	$ical = $timeEdit->getICal();
	echo str_replace("\n", "<br/>", $ical);
} else {
	$json["status"] = "bad";
}


echo json_encode($json) . "\n";

?>
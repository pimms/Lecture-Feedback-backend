<?php

require_once("Utils.php");
require_once("Vote.class.php");

function dieBad($reason) {
	echo json_encode( 
		Array(	"status"=>"bad",
				"reason"=>$reason
		) 
	);
	die();
}


/* Fetch the required parameters or die horribly */
$reviewId = (int)getFromAssoc($_GET, "review_id", null) 
			or dieBad("review_id is not defined");
$voteType = getFromAssoc($_GET, "type", null)
			or dieBad("type is not defined");

/* Ensure voteType has a valid value */
if ($voteType != "up" && $voteType != "down") {
	dieBad("Invalid type value");
}



$json = Array("status" => "bad");

$vote = new Vote();
$success = false;

if ($voteType == "up") {
	$success = $vote->voteUp($reviewId);
} else if ($voteType = "down") {
	$success = $vote->voteDown($reviewId);
}

if ($success) {
	$json["status"] = "ok";
}

echo json_encode($json);
?>
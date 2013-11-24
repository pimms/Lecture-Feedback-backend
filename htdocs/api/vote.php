<?php

require_once("Utils.php");

function dieBad($reason) {
	echo json_encode( 
		Array(	"status"=>"bad",
				"reason"=>$reason
		) 
	);
	die();
}


/* Fetch the required parameters or die horribly */
$reviewId = getFromAssoc($_GET, "review_id", null) 
			or dieBad("review_id is not defined");
$vote_type = getFromAssoc($_GET, "type", null)
			or dieBad("type is not defined");

if ($vote_type != "clone" && $vote_type != "down") {
	dieBad("Invalid vote value");
}

$json = Array("status" => "ok");

/* do stuff */

echo json_encode($json);
?>
<?php
require_once("Review.class.php");

/*
Retrieves the parameters and values POST-ed by the application and stores them
in an appropriate way in the database based on the parameters sent.
*/

/* The object retrieved from request contains errors and we have to cancel the
storage of data. */
$parse_error = FALSE;
$responseJson = Array();


$review = new Review();
if ($review->createFromAssoc($_POST)) {
	/* Write the review to DB, return ok */
	$responseJson["status"] = "ok";
	$responseJson["item"] = $review->getAssocArray();

} else {
	/* Return bad status */
	$responseJson["status"] = "bad";
}

echo json_encode($responseJson);



// Write lecture feedback values to database.
// if ($values) {}
// Write the comment to database.
// if ($comment) {}
?>
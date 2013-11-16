<?php
require_once("Review.class.php");

/*
Retrieves the parameters and values POST-ed by the application and stores them
in an appropriate way in the database based on the parameters sent.

Example URL:
http://localhost/lfb/api/postReview.php?client_hash=dustehash&course_name=mobil%20YO&course_code=IMTeplefjes&start_time=34835913&end_time=453252&lecturer=prof&room=K102&attribute_version_set=1&attributes=1.0.0.1.0
*/

/* The object retrieved from request contains errors and we have to cancel the
storage of data. */
$parse_error = FALSE;
$responseJson = Array();


$review = new Review();
if ($review->createFromAssoc($_GET)) {
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
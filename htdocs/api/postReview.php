<?php

/*
Retrieves the parameters and values POST-ed by the application and stores them
in an appropriate way in the database based on the parameters sent.
TODO: Document usage of POST parameters.
*/

// Initialize database object for for use in storage iterator below.
$student_hash = $Room = $time = $student_comment = '';

// Array to contain feedback name-value pairs for a single object.
$values = [];

/* The object retrieved from request contains errors and we have to cancel the
storage of data. */
$parse_error = FALSE;

// The element number (1 and up) we're currently trying to store.
$element_no = 0;

try {

/* The user used POST in their request and wishes to store data. Iterate through
each element sent by the user, and retrieve the objects the user wishes to
store. */
foreach ($_POST as $column => $value) {

	// Increment element number.
	$element_no++;
	
	// Retrieve element.
	switch ($column) {
		case 'student_hash':	$student_hash	= $value;		break;
		case 'Room':			$room			= $value;		break;
		case 'time':			$time			= $value;		break;
		case 'feedback_name':	$feedback_name	= $value;		break;
		case 'feedback_value':	$feedback_value = $value;
			if(!in_array($feedback_name)
				$values[$feedback_name] = $value;
			else throw new Exception
				('Tried overwriting incomplete values object'); break;
		// End of values object.
		case 'student_comment':	$teacher =		$value;			break;
		// End of comment object.
	}
}

// An element triggered an exception. Abort database operations and list exceptions.
} catch (Exception $e) {
	$parse_error = TRUE;
	echo 'Unexpected data: ' . $e . ' in element no. ' . $element_no . '\n';
} finally { if($parse_error) die("\nUnexpected data received. If you're the 
	application	developer, please review your API usage and correct any errors. 
	Submit any remaining errors as an issue to our issue tracker, describing
	the data and intended usage: http://bit.ly/lfb-issues");
}

// Write lecture feedback values to database.
// if ($values) {}
// Write the comment to database.
// if ($comment) {}
?>
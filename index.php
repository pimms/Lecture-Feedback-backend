<?php

// Initializing object.
$room = $time = $duration = $subject = $teacher = $student_hash
	= $student_comment = $comment_flags = $feedback_name = $active
	= $feedback_value = '';

/* The user used POST in their request and wishes to store data. Iterate through
each element sent by the user, and retrieve the objects the user wishes to
store. */
foreach ($_POST as $method => $data) {
	
	// Store element.
	switch ($method) {
		case 'Room':		$room = $data; break;
		case 'time':		$time = $data; break;
		case 'duration':	$duration = $data;
		case 'Subject':		$subject = $data;
		case 'Teacher':		$teacher = $data;
		case 'student_hash':$student_hash = 
	}
}

?>
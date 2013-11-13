<?php

require_once("CourseResolver.class.php");
require_once("ReviewFeed.class.php");


function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "<br/>".str_repeat( "&nbsp;", 4*$new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}



$json = array();


if (isset($_GET["filter"])) {
	$courseResolver = new CourseResolver($_GET["filter"]);
	$courses = $courseResolver->resolveCourses();
	
	$feed = new ReviewFeed();
	$reviews = $feed->getFeed($courses, 0, 10);

	$json["item_count"] = count($reviews);
	$json["items"] = array();
	
	foreach ($reviews as $review) {
		$arr = array();
		$arr["course"] 	 = $review->getCourseName();
		$arr["lecturer"] = $review->getLecturer();
		$arr["time"] 	 = $review->getTime();
		$arr["date"] 	 = $review->getDate();
		$arr["room"] 	 = $review->getRoom();
		$arr["ratings"]  = $review->getRatings();
		$arr["comment"]  = $review->getComment();
		$arr["id"] 		 = 15012;
		$json["items"][] = $arr;	
	}
} else {
	$json["status"] = "bad";
}


$jsonString = json_encode($json);
echo prettyPrint($jsonString);
?>
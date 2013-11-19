<?php

require_once("ReviewFeed.class.php");
require_once("Utils.php");


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



$json = array("status" => "bad");


if (isset($_GET["filter"])) {
	// Set FIRST and COUNT from default / GET
	$first = getFromAssoc($_GET, "first", 0);
	$count = getFromAssoc($_GET, "count", 25);
	
    $filter = explode(",", $_GET["filter"]);
    $hash = getFromAssoc($_GET, "hash", null);

	// Retrieve the feed of items
	$feed = new ReviewFeed();
	$reviews = $feed->getFeed($filter, $first, $count, $hash);

    if ($reviews !== false) {
    	// Prepare the JSON array
    	$json["status"] = "ok";
    	$json["item_count"] = count($reviews);
    	$json["items"] = array();

    	foreach ($reviews as $review) {
    		$json["items"][] = $review->getAssocArray();	
    	}
    }
} 

$jsonString = json_encode($json);
//$jsonString = prettyPrint($jsonString);
echo $jsonString;
?>
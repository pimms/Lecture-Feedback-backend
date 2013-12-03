<?php

require_once("ReviewFeed.class.php");
require_once("Utils.php");


$json = array("status" => "bad");


if (isset($_GET["filter"]) || isset($_GET["hash"])) {
	// Set FIRST and COUNT from default / GET
	$first  = getFromAssoc($_GET, "first", 0);
    $lastId = getFromAssoc($_GET, "lastid", 0);
	$count  = getFromAssoc($_GET, "count", 25);
    $hash   = getFromAssoc($_GET, "hash", null);
    $filter = getFromAssoc($_GET, "filter", null);

    if ($filter) {
        $filter = explode(",", $_GET["filter"]);
    }

	// Retrieve the feed of items
	$feed = new ReviewFeed();
	$reviews = $feed->getFeed($filter, $hash, $first, $count);

    if ($reviews !== false) {
    	// Prepare the JSON array
    	$json["status"] = "ok";
        $json["first"] = $first;
        $json["count"] = $count;
    	$json["item_count"] = count($reviews);
    	$json["items"] = array();

    	foreach ($reviews as $review) {
    		$json["items"][] = $review->getAssocArray();	
    	}
    }
} 

$jsonString = json_encode($json);

if (isset($_GET["pretty"])) {
    $jsonString = prettyPrint($jsonString);
}

echo $jsonString;
?>
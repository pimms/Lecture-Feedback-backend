<?php

/**
 * @script Utils.php
 * Script containing utility functions used frequently.
 */



/**
 * @param assoc
 * The associative array that may contain "key".
 *
 * @param key
 * The desired key of the value
 *
 * @param default
 * The default value, if $assoc[$key] is unset.
 *
 * @return
 * $assoc[$key] if set, $default otherwise.
 */
function getFromAssoc($assoc, $key, $default) {
	return isset($assoc[$key]) ? $assoc[$key] : $default;
}

?>
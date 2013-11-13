<?php

/**
 * @script Utils.php
 * Script containing utility functions used frequently.
 */



/**
 * @param var
 * The variable to be evaluated and possibly returned.
 *
 * @param default
 * The default value, if "var" is unset.
 *
 * @return
 * $var if set, $default otherwise.
 */
function getIfSet($var, $default) {
	return isset($var) ? $var : $default;
}

?>
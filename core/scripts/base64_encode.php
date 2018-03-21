<?php
/**
 * Base64 encodes a string.
 * 
 * Works as a wrapper for PHP's base64_encode function.
 * 
 * Usage: jamp base64_encode <input>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

if (empty($argv[1])) {
	passthru('jamp usage base64_encode');
	exit;
}

jampEcho(base64_encode($argv[1]));

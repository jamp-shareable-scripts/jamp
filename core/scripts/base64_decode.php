<?php
/**
 * Decodes a base64 encoded string.
 * 
 * Works as a wrapper for PHP's base64_decode function.
 * 
 * Usage: jamp base64_decode <input>
 *   -d,--detect-encoding Detect multibyte encoding.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

$opts = getopt('d', ['detect-encoding'], $lastArg);
$detectEncoding = isset($opts['d']) || isset($opts['detect-encoding']);

if (empty($argv[$lastArg])) {
	passthru('jamp usage base64_decode');
	exit;
}

$raw = base64_decode($argv[$lastArg]);
if (!$detectEncoding) {
	return jampEcho($raw);
}

echo iconv(mb_detect_encoding($raw, mb_detect_order(), true), "UTF-8", $raw);


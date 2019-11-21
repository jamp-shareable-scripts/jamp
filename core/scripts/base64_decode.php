<?php
/**
 * Decodes a base64 encoded string.
 * 
 * Works as a wrapper for PHP's base64_decode function.
 * 
 * Usage: jamp base64_decode <input>
 *        <input> | jamp base64_decode
 *   -d,--detect-encoding Detect multibyte encoding.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampArgOrStdIn', 'jampEcho']);

$opts = getopt('d', ['detect-encoding'], $lastArg);
$detectEncoding = isset($opts['d']) || isset($opts['detect-encoding']);
$input = trim(jampArgOrStdIn($lastArg));
$raw = base64_decode($input);
if (!$detectEncoding) {
	return jampEcho($raw);
}

echo iconv(mb_detect_encoding($raw, mb_detect_order(), true), "UTF-8", $raw);


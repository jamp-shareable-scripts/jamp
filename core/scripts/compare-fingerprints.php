<?php
/**
 * Compares two fingerprint strings returning true if they are the same and
 * false otherwise. Colons are stripped and both strings converted to lowercase
 * before the comparison is made.
 * 
 * Usage: jamp compare-fingerprints str1 str2
 * 
 */

jampUse(['jampEcho', 'jampArgOrStdIn']);

if (!isset($argv[1])) {
	passthru('jamp usage compare-fingerprints');
	exit;
}

$str1 = strtolower(str_replace([':', ' '], '', $argv[1]));
$str2 = strtolower(str_replace([':', ' '], '', trim(jampArgOrStdIn(2))));

if (strcmp($str1, $str2) === 0) {
    jampEcho('true');
    exit;
}

jampEcho('false');

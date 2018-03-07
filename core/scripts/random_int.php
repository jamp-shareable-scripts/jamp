<?php
/**
 * Generate a random number using random_int.
 * 
 * Usage: jamp random_int <minimum> <maximum>
 * 
 *   minimum The smallest integer to return. Defaults to 0.
 *   maximum The largest integer to return. Defaults to PHP_INT_MAX.
 */

jampUse('jampEcho');

$min = 0;
$max = PHP_INT_MAX;
if (isset($argv[1]) && is_numeric($argv[1])) {
	$min = (int)$argv[1];
}

if (isset($argv[2]) && is_numeric($argv[2])) {
	$max = (int)$argv[2];
}

if ($max <= $min) {
	passthru('jamp usage random_int');
	exit;
}

jampEcho(random_int($min, $max));

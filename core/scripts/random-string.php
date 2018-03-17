<?php

/**
 * Generates a random string.
 *
 * Usage: jamp random-string [options]
 * 
 *   --numbers  Use numbers 0-9 in the string
 *   --letters  Use letters a-zA-Z in the string
 *   --length=n Generate a string of length n, default 64
 * 
 * @todo Fix off by one error in length of output.
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampEcho']);

$opts = getopt('', ['letters', 'numbers', 'length:']);
$charPool = '';
$letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$numbers = '0123456789';

// If requested, include letters in the string.
if (isset($opts['letters'])) {
	$charPool .= $letters;
}

// If requested, include numbers in the string.
if (isset($opts['numbers'])) {
	$charPool .= $numbers;
}

// If no character types are provided as options, default to numbers and
// letters.
if (empty($charPool)) {
	$charPool .= $numbers . $letters;
}

// Use the length given in the options; if none is provided, use a length of 64.
$length = isset($opts['length']) && is_numeric($opts['length']) 
? (int)$opts['length']
: 64;

// The max index of the charPool.
$max = strlen($charPool);

// Randomly add a character to the string, one at a time.
$output = '';
for ($i = 0; $i < $length; $i++) {
	$output .= substr($charPool, random_int(0, $max), 1);
}

jampEcho($output);

<?php

/**
 * Generates a random string of printable ASCII characters.
 *
 * Usage: jamp random-string [options]
 *    jamp random-string
 *    jamp random-string --all
 *    jamp random-string --letters --numbers --symbols
 *    jamp random-string --length n
 *    jamp random-string -A --not 
 *    jamp random-string --mysqlpw
 *
 *   -A,--all          Use all printable ASCII characters.
 *   -L,--letters      Use letters a-zA-Z in the string.
 *   -N,--numbers      Use numbers 0-9 in the string.
 *   -l,--length=n     Generate a string of length n, default 64.
 *   --not <hex codes> List hex codes of chars to remove, e.g.\x40\x60\x2f
 *   --mysqlpw         Follow mysql password rules (ASCII printable chars
 *                     excluding characters @, /, and '.
 * 
 * When no options are specified, the jamp random-string command will generate
 * a string of random letters and numbers with a length of 64 characters.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

// Get the arguments passed to the script.
$opts = getopt('ALNSl:', ['all', 'letters', 'numbers', 'symbols',
'length:', 'not:', 'mysqlpw']);

// Set up the possible character ranges to use.
$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
$numbers = '0123456789';
$symbols = ' !"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';

// Determine which type of characters are actually being used.
$doMysqlPw = isset($opts['mysqlpw']);
$useAll = isset($opts['A']) || isset($opts['all']) || $doMysqlPw;
$useLetters = isset($opts['L']) || isset($opts['letters']) || $useAll;
$useNumbers = isset($opts['N']) || isset($opts['numbers']) || $useAll;
$useSymbols = isset($opts['S']) || isset($opts['symbols']) || $useAll;
$avoidCharCodes = empty($opts['not']) ? null : $opts['not'];
$defaultLength = $doMysqlPw ? 16 : 64;

// Build a string containing the characters that may be included in the output.
$characterRange = '';
if ($useLetters) {
	$characterRange .= $letters;
}
if ($useNumbers) {
	$characterRange .= $numbers;
}
if ($useSymbols) {
	$characterRange .= $symbols;
}

if (empty($characterRange)) {
	$characterRange .= $letters . $numbers;
}

$hexPattern = "/\\\\x([0-9a-z]{2})/";
if ($avoidCharCodes && preg_match_all($hexPattern, $avoidCharCodes, $matches)) {
	$avoidChars = '';
	foreach ($matches[1] as $charHexCode) {
		$avoidChars .= chr(hexdec($charHexCode));
	}
	$charsArg = preg_quote($avoidChars, '/');
	$pattern = "/[$charsArg]/";
	$characterRange = preg_replace($pattern, '', $characterRange);
}
if ($doMysqlPw) {
	$characterRange = preg_replace("/[@'\/]/", '', $characterRange);
}

if (empty($characterRange)) {
	throw new Error('Invalid character range');
}

// Determine the length of the output.
$lengthRaw = empty($opts['length'])
? (empty($opts['l']) ? $defaultLength : $opts['l'])
: $opts['length'];
$length = is_numeric($lengthRaw) ? (int)$lengthRaw : 64;

// The max index of the character range.
$max = strlen($characterRange) - 1;

// Randomly add a character to the string, one at a time.
$output = '';
for ($i = 0; $i < $length; $i++) {
	$output .= substr($characterRange, random_int(0, $max), 1);
}

jampEcho($output);

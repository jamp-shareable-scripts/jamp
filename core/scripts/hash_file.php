<?php

/**
 * Calculates the hash of a file by wrapping PHP's hash_file function.
 * 
 * Usage: jamp hash_file [-c <checksum>|--checksum <checksum>] <algo> <filename>
 * 
 *   -c,--checksum A checksum to compare the hash to.
 *
 * <algo> is the algorithm used to hash the file, e.g. sha256.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

// Get options given to the script.
$opts = getopt('c:', ['checksum:'], $lastArg);

if (empty($argv[$lastArg]) || empty($argv[$lastArg + 1])) {
	passthru('jamp usage hash_file');
	exit;
}

$algo = $argv[$lastArg];
if (!in_array($algo, hash_algos())) {
	showAlgoHelp();
	exit;
}

$file = $argv[$lastArg + 1];
if (!is_file($file)) {
	throw new Error("File does not exist: $file");
}

/**
 * A checksum to compare the hash against.
 * @var string
 */
$checksum = empty($opts['checksum'])
? (empty($opts['c']) ? null : $opts['c'])
: $opts['checksum'];

// Get the hash.
$output = hash_file($algo, $file);

// Compare against the checksum if we have one.
if ($checksum) {
	$output .= $checksum === $output
	? (PHP_EOL . 'Checksum matches :)')
	: (PHP_EOL . 'Checksum does not match :(');
}
jampEcho($output);

/**
 * Lets the user know which algo functions are available.
 */
function showAlgoHelp() {
	$availableAlgos = hash_algos();
	echo 'The algorithm argument must be one of the following:' . PHP_EOL;
	$lineSize = 0;
	$lineMax = 80;
	$maxAlgo = 0;
	foreach($availableAlgos as $algo) {
		if (strlen($algo) > $maxAlgo) {
			$maxAlgo = strlen($algo);
		}
	}
	$colSize = $maxAlgo + 2;
	foreach($availableAlgos as $algo) {
		if (($lineSize + strlen($algo)) > $lineMax) {
			echo PHP_EOL;
			$lineSize = 0;
		}
		$output = str_pad($algo, $colSize, ' ');
		$lineSize += strlen($output);
		echo $output;
	}
	echo PHP_EOL;
}

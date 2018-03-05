<?php

/**
 * Displays the usage for the requested jamp script.
 * 
 * Usage: jamp usage <script name>
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse(['jampEcho', 'jampGetScriptPath']);

if (empty($argv[1])) {
	passthru('jamp usage usage');
	exit;
}

$scriptPath = jampGetScriptPath($argv[1]);
if (!$scriptPath) {
	throw new Error('Could not find requested script ' . $argv[1]);
}

$handle = fopen($scriptPath, 'rb');
$line = fgets($handle);
$doc = '';
$complete = $isReading = false;
while ($line !== false && !feof($handle) && !$complete) {
	if (strpos($line, '*/')) {
		$doc .= PHP_EOL;
		$complete = true;
	}
	elseif ($isReading) {
		$trimmed = trim($line);
		if (!empty($trimmed) && empty($doc)) {
			$doc .= PHP_EOL; // Ensure the doc starts with blank line.
		}
		if (!empty($trimmed) && $trimmed !== '/**') {
			$startChar = strpos('*', $trimmed) + 2;
			$doc .= substr($trimmed, $startChar) . PHP_EOL;
		}
	}
	elseif (strpos($line, '/**') !== false) {
		$isReading = true;
	}
	$line = fgets($handle);
}
fclose($handle);

jampEcho($doc);

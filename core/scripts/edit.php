<?php

/**
 * Open a jamp script for editing.
 * 
 * Usage: jamp edit [-p|--print-path] <script name>
 *
 *   -p,--print-path Display the path to the script instead of opening the file.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampEcho', 'jampTextEditor']);

$options = getopt('p', ['print-path'], $lastId);
$printPath = isset($options['p']) || isset($options['print-path']);
$name = empty($argv[$lastId]) ? null : $argv[$lastId];

if (!$name) {
	passthru('jamp usage edit');
}

$scriptName = substr_compare($name, '.php', -4) === 0
? $name
: ($name . '.php');

// Try to open a local version of the script.
$assumedLocalPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'local'
. DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $scriptName;
if (is_file($assumedLocalPath)) {
	openFile($assumedLocalPath, $printPath);
	exit();
}

// Try to open the core version of the script.
$assumedCorePath = __DIR__ . DIRECTORY_SEPARATOR . $scriptName;
if (is_file($assumedCorePath)) {
	openFile($assumedCorePath, $printPath);
	exit();
}

throw new Error("Could not find $scriptName. Searched:" . PHP_EOL
	. "  $assumedLocalPath" . PHP_EOL . "  $assumedCorePath" . PHP_EOL);	

function openFile($path, $printPath) {
	if ($printPath) {
		jampEcho($path);
	}
	else {
		jampTextEditor($path);
	}
}

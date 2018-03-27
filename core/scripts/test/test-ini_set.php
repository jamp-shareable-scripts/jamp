<?php

/**
 * Test that the ini_set script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$testIniName = 'testini_set' . rand(10000,99999) . '.ini';
$testIniPath = JAMP_CORE_DATA . $testIniName;

if (file_exists($testIniPath)) {
	throw new Error("File unexpectedly exists: $testIniPath");
}

touch($testIniPath);

// Test the option is added if it doesn't exist in the ini file already.
$pathArg = escapeshellarg($testIniPath);
exec("jamp ini_set -f $pathArg anoption anoptionvalue");
$contents = file_get_contents($testIniPath);
$expected = PHP_EOL . '; Value added via jamp' . PHP_EOL . 'anoption = '
. 'anoptionvalue' . PHP_EOL;
test($expected === $contents, 'Option should be added.');

// Test that a backup file is created when an ini file is changed.
test(is_file("$testIniPath.bkp"), 'Backup file should be created.');

// Test that the backup file contains the old version of the file.
test(
	empty(file_get_contents("$testIniPath.bkp")),
	'Backup is old file version'
);

// Cleanup.
unlink("$testIniPath.bkp");
unlink($testIniPath);
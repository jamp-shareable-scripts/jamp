<?php

$testIniName = 'testini_set' . rand(10000,99999) . '.ini';
$testIniPath = JAMP_CORE_DATA . $testIniName;

if (file_exists($testIniPath)) {
	throw new Error("File unexpectedly exists: $testIniPath");
}

touch($testIniPath);

$pathArg = escapeshellarg($testIniPath);

exec("jamp ini_set -f $pathArg anoption anoptionvalue");

$contents = file_get_contents($testIniPath);

$expected = PHP_EOL . '; Value added via jamp' . PHP_EOL . 'anoption = '
. 'anoptionvalue' . PHP_EOL;

test($expected === $contents, 'Option should be added.');

test(is_file("$testIniPath.bkp"), 'Backup file should be created.');

test(
	empty(file_get_contents("$testIniPath.bkp")),
	'Backup is old file version'
);

unlink("$testIniPath.bkp");
unlink($testIniPath);
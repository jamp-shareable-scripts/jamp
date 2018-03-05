<?php

/**
 * Tests for the jampGetScriptPath helper function.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse('jampGetScriptPath');

$scriptName = 'testgetscriptpath' . rand(10000, 99999);
$scriptPath = getenv('JAMP_BASE') . 'local' . DIRECTORY_SEPARATOR . 'scripts'
. DIRECTORY_SEPARATOR . $scriptName . '.php';

if (file_exists($scriptPath)) {
	throw new Error("File unexpectedly already exists: $scriptPath");
}

$nonexistentPath1 = jampGetScriptPath($scriptName);
test($nonexistentPath1 === false, 'Nonexistent script should not be found');

$nonexistentPath2 = jampGetScriptPath($scriptName . '.php');
test(
	$nonexistentPath2 === false,
	'Nonexistent script with extension should not be found'
);

exec("jamp create -s $scriptName");
$validPath1 = jampGetScriptPath($scriptName);
test($validPath1 === $scriptPath, 'Valid script should be found');

$invalidPath = jampGetScriptPath($scriptName . '.php');
test($invalidPath === false, 'Should not handle extensions');

if (is_file($scriptPath)) {
	unlink($scriptPath);
}

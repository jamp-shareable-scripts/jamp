<?php

/**
 * Test that the edit script works as expected.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

$scriptName = 'testcreatescript' . rand(10000, 99999);
$scriptLocalPath = JAMP_LOCAL_SCRIPTS . $scriptName . '.php';
$scriptCorePath = JAMP_CORE_SCRIPTS . $scriptName . '.php';

if (file_exists($scriptLocalPath)) {
	throw new Error("File unexpectedly already exists: $scriptLocalPath");
}
if (file_exists($scriptCorePath)) {
	throw new Error("File unexpectedly already exists: $scriptCorePath");
}

// Test that the create command creates a new file locally by default.
exec("jamp create --silent $scriptName");
$localOutput = trim(exec("jamp edit --print-path $scriptName"));
test($localOutput === $scriptLocalPath, 'Should edit local files');
unlink($scriptLocalPath);

exec("jamp create --silent --global $scriptName");
$coreOuput = trim(exec("jamp edit --print-path $scriptName"));
test($coreOuput === $scriptCorePath, 'Should edit core files');

exec("jamp create --silent $scriptName");
$overrideOuput = trim(exec("jamp edit --print-path $scriptName"));
test($overrideOuput === $scriptLocalPath, 'Should respect local override');

unlink($scriptCorePath);
unlink($scriptLocalPath);


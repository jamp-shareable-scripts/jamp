<?php

/**
 * Test that the create script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
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
test(file_exists($scriptLocalPath), 'The script should be created in core');
unlink($scriptLocalPath);

exec("jamp create --silent --global $scriptName");
test(file_exists($scriptCorePath), 'The script should be created in local');
unlink($scriptCorePath);

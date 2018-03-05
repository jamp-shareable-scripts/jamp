<?php

/**
 * Runs tests to check jamp script functionality.
 * 
 * Usage: jamp test
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse('jampEcho');

$passed = $count = 0;

runTests();

/**
 * Tests the $expression described by the $message.
 * 
 * Wraps around PHP's assert function and increments test metrics.
 * 
 * @param boolean $expression
 * @param string $message
 */
function test($expression, $message) {
	global $passed, $count;
	$count++;
	if (assert($expression, $message)) {
		$passed++;
		echo "  Passed: $message" . PHP_EOL;
	}
	else {
		throw new Error("$message - failed");
	}
}

/**
 * Locate and run all test files.
 */
function runTests() {
	global $passed, $count;
	$helperTestDir = JAMP_HELPERS . 'test' . DIRECTORY_SEPARATOR;
	$helperTests = array_diff(scandir($helperTestDir), ['.','..']);
	foreach ($helperTests as $test) {
		include $helperTestDir . $test;
	}
	$coreTestsDir = JAMP_CORE_SCRIPTS . 'test' . DIRECTORY_SEPARATOR;
	$coreTests = array_diff(scandir($coreTestsDir), ['.','..']);
	foreach ($coreTests as $test) {
		include $coreTestsDir . $test;
	}
	jampEcho("Tests complete. $passed/$count successful.");
}


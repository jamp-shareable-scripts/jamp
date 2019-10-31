<?php
/**
 * Performs a binary-safe comparison on two strings using PHP's strcmp. Returns
 * 0 if the strings are the same, a number less than 0 if `stringA` is less than
 * `stringB`, or a number greater than 0 if `stringA` is greater than `stringB`
 *
 * Usage: jamp strcmp <stringA> <stringB>
 *
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

if (!isset($argv[1], $argv[2])) {
	passthru('jamp usage strcmp');
	exit;
}

jampEcho(strcmp($argv[1], $argv[2]));

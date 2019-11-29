<?php

/**
 * Copies items in source directory into target directory.
 * 
 * Usage: jamp copy <source directory> <target directory>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampResolvePath', 'jampEcho', 'jampCopyDirectory']);

if (empty($argv[1]) || empty($argv[2])) {
	passthru('jamp usage copy');
	exit;
}

$source = realpath($argv[1]);
if (!$source || !is_dir($source)) {
	throw new Error('Must be a directory: ' . $argv[1]);
}

$target = jampResolvePath($argv[2]);
if (!is_dir($target)) {
	mkdir($target);
}

jampCopyDirectory($source, $target);
jampEcho('Copy completed.');


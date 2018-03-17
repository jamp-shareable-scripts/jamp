<?php

/**
 * Resolves a relative path. Will return the resolved path even if it does not
 * exist.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

/**
 * Resolves the given path.
 * @param  string $path
 * @return string|boolean An absolute path.
 * @throws Error If the absolute path cannot be resolved.
 */
function jampResolvePath($path) {
	$real = realpath($path);
	if ($real) {
		return $real;
	}
	$parentDir = realpath(dirname($path));
	$basename = pathinfo($path)['basename'];
	if ($parentDir && $basename) {
		return $parentDir . DIRECTORY_SEPARATOR . $basename;
	}
	throw new Error("Could not resolve directory: $path");
}

<?php

/**
 * Copies items in source directory into target directory.
 * 
 * Usage: jamp copy <source directory> <target directory>
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse(['jampResolvePath', 'jampEcho']);

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

copyDirectory($source, $target);
jampEcho('Copy completed.');

/**
 * Copies all files and subdirectories in $from directory to $to directory.
 * @param string $from Source directory.
 * @param string $to Target directory.
 */
function copyDirectory($from, $to) {
	$iterator = new RecursiveDirectoryIterator(
		$from, FilesystemIterator::SKIP_DOTS
	);
	while($iterator->valid()) {
		$item = $iterator->current();
		$newPath = $to . DIRECTORY_SEPARATOR . $item->getFilename();
		if ($item->isDir()) {
			if (!file_exists($newPath)) {
				mkdir($newPath);
			}
			copyDirectory($item->getRealPath(), $newPath);
		}
		elseif ($item->isFile() && !file_exists($newPath)) {
			copy($item->getRealPath(), $newPath);
		}
		elseif ($item->isFile()) {
			echo "Skipping file because it already exists: $newPath" . PHP_EOL;
		}
		$iterator->next();
	}
}
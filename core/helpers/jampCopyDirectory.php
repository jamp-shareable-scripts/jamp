<?php

/**
 * Copies all files and subdirectories in $from directory to $to directory.
 * @param string $from Source directory.
 * @param string $to Target directory.
 */
function jampCopyDirectory($from, $to, $opts = []) {
	$overwrite = !empty($opts) && isset($opts['overwrite']) && $opts['overwrite'];
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
			jampCopyDirectory($item->getRealPath(), $newPath, $opts);
		}
		elseif ($item->isFile() && (!file_exists($newPath) || $overwrite)) {
			copy($item->getRealPath(), $newPath);
		}
		elseif ($item->isFile()) {
			echo "Skipping file because it already exists: $newPath" . PHP_EOL;
		}
		else {
			echo 'Warning, item not copied: ' . $item->getRealPath() . PHP_EOL;
		}
		$iterator->next();
	}
}

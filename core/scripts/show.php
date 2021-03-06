<?php
/**
 * Shows all available scripts runnable by jamp.
 *
 * Usage: jamp show
 *
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

$local = is_dir(JAMP_BASE . 'local') ? new FilesystemIterator(
	JAMP_BASE . 'local' . DIRECTORY_SEPARATOR . 'scripts',
	FilesystemIterator::SKIP_DOTS | FilesystemIterator::NEW_CURRENT_AND_KEY
) : false;

if ($local) {
	echo 'Local scripts' . PHP_EOL;
	foreach ($local as $file => $info) {
		if (strtolower($info->getExtension()) === 'php') {
			echo '  ' . substr($file, 0, -4) . PHP_EOL;
		}
	}
}

$core = new FilesystemIterator(
	JAMP_BASE . 'core' . DIRECTORY_SEPARATOR . 'scripts',
	FilesystemIterator::SKIP_DOTS | FilesystemIterator::NEW_CURRENT_AND_KEY
);
echo PHP_EOL . 'Core scripts' . PHP_EOL;
foreach ($core as $file => $info) {
	if (strtolower($info->getExtension()) === 'php') {
		echo '  ' . substr($file, 0, -4) . PHP_EOL;
	}
}

$installed = is_dir(JAMP_BASE . 'installed') ? new FilesystemIterator(
	JAMP_BASE . 'installed',
	FilesystemIterator::SKIP_DOTS
) : false;

if ($installed) {
	echo PHP_EOL . 'Installed scripts' . PHP_EOL;
	foreach ($installed as $subdirpath) {
		if (!is_dir($subdirpath)) {
			continue;
		}

		$subdir = new FilesystemIterator(
			$subdirpath . DIRECTORY_SEPARATOR . 'scripts',
			FilesystemIterator::SKIP_DOTS | FilesystemIterator::NEW_CURRENT_AND_KEY
		);
		foreach ($subdir as $file => $info) {
			if (strtolower($info->getExtension()) === 'php') {
				echo '  ' . substr($file, 0, -4) . PHP_EOL;
			}
		}
	}
}

jampEcho('');

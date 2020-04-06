<?php
/**
 * Uninstalls a jamp script library.
 * 
 * Usage: jamp uninstall <library>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampEcho']);

/**
 * Extracts the repo name from the given $repoUrl.
 * @param string $repoUrl
 */
function getRepoName($repoUrl) {
	$template = "/https:\/\/github\.com\/(.*?)\/(.*?)\/?$/";
	if (!preg_match($template, $repoUrl, $parts)) {
		throw new Error("Does not seem to be a valid github repo: $repoUrl"
		. PHP_EOL . 'Please let us know if you want to use repos from a new '
		. 'source, or if you could help implement that! :)');
	}
	return $parts[2];
}

function removeFromLookup($dir) {
	$lookupFile = JAMP_INSTALLED . 'lookup.json';
	$lookup = is_file($lookupFile)
	? json_decode(file_get_contents($lookupFile), true)
	: [];
	$scriptsDir = $dir . DIRECTORY_SEPARATOR . 'scripts';
	$iterator = new DirectoryIterator($scriptsDir);
	while ($iterator->valid()) {
		$item = $iterator->current();
		if ($item->isFile()) {
			$name = pathinfo($item->getBasename(), PATHINFO_FILENAME);
			if (isset($lookup[$name])) {
				unset($lookup[$name]);
			}
		}
		$iterator->next();
	}
	file_put_contents($lookupFile, json_encode($lookup, JSON_PRETTY_PRINT));
}

function deleteDirectory($dir) {
	$files = array_diff(scandir($dir), ['.','..']);
	$sep = DIRECTORY_SEPARATOR;
	foreach ($files as $file) {
		(is_dir("$dir$sep$file")) ?
			deleteDirectory("$dir$sep$file") : 
			unlink("$dir$sep$file");
	}
	return rmdir($dir);
}

/**
 * The name of the library to uninstall;
 * @var string
 */
$libraryName = $argv[1];

if (empty($libraryName)) {
	passthru('jamp usage uninstall');
	exit;
}

if (!is_dir(JAMP_INSTALLED)) {
	echo 'No installed scripts found';
	exit;
}

// Check the location of the script is known.
$locations = json_decode(file_get_contents(
	JAMP_CORE_DATA . 'other-script-locations.json'
), true);
if (empty($locations[$libraryName])) {
	throw new Error("Do not know the location of $libraryName");
}

// Check the script is not already installed.
$repoName = getRepoName($locations[$libraryName]);
$dir = JAMP_INSTALLED . $repoName;
if (!file_exists($dir)) {
	jampEcho("$libraryName is not installed. Checked location: $dir");
	exit;
}

removeFromLookup($dir);
deleteDirectory($dir);

jampEcho("$libraryName removed." . PHP_EOL);

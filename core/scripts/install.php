<?php

/**
 * Installs a jamp script.
 * 
 * Usage: jamp install <library>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampEcho', 'jampFetchJson', 'jampFetch', 'jampZipOpenErrorMessage']);

if (empty($argv[1])) {
	passthru('jamp usage install');
	exit;
}

// Ensure CURL is available.
if (!extension_loaded('curl')) {
	throw new Error('PHP\'s curl extension is required to install jamp '
	. 'scripts automatically, please enable it. ' . PHP_EOL
	. 'See: https://secure.php.net/manual/en/curl.installation.php');
}

/**
 * The name of the script to install.
 * @var string
 */
$scriptName = $argv[1];

// Ensure we have a directory to install scripts into.
if (!is_dir(JAMP_INSTALLED)) {
	mkdir(JAMP_INSTALLED);
}

/**
 * Randomise the temp file/folder names.
 */
$rand = random_int(10000,99999);

/**
 * The temprary location of the zip file.
 * @var string
 */
$tempZip = JAMP_CORE_DATA . $rand . 'install.zip';

/**
 * The temporary directory extracted from the zip file.
 * @var string
 */
$tempDir = JAMP_CORE_DATA . $rand . 'install';

if (file_exists($tempZip)) {
	throw new Error("Could not install, temp file already exists: $tempZip");
}
if (file_exists($tempDir)) {
	throw new Error("Directory already exists: $tempDir");
}

// Check the location of the script is known.
$locations = json_decode(file_get_contents(
	JAMP_CORE_DATA . 'other-script-locations.json'
), true);
if (empty($locations[$scriptName])) {
	throw new Error("Do not know the location of $scriptName");
}

// Check the script is not already installed.
$repoName = getRepoName($locations[$scriptName]);
$to = JAMP_INSTALLED . $repoName;
if (file_exists($to)) {
	jampEcho("$scriptName is already installed: $to");
	exit;
}

// Download the repository.
$zipURL = getZipURL($locations[$scriptName]);
$content = jampFetch($zipURL, 'application/json');
if (1 > file_put_contents($tempZip, $content)) {
	throw new Error("Failed to download: $zipURL");
}

// Extract the ZIP containing the code.
$zip = new ZipArchive;
$errorCode = $zip->open($tempZip);
if ($errorCode !== true) {
	throw new Error('Encountered "' . jampZipOpenErrorMessage($errorCode)
	. '" when attempting to open: ' . $tempZip);
}
$zip->extractTo($tempDir);
$zip->close();

// Move the extracted files into the jamp installed folder.
$from = $tempDir . DIRECTORY_SEPARATOR . array_diff(
	scandir($tempDir),['.','..']
)[2];

rename($from, $to);
rmdir($tempDir);
unlink($tempZip);
addToLookup($to);
jampEcho("$scriptName script added." . PHP_EOL);

/**
 * Saves the location of the scripts in the given directory.
 * @newDir string The directory the scripts are saved in.
 */
function addToLookup($newDir) {
	$lookupFile = JAMP_INSTALLED . 'lookup.json';
	$lookup = is_file($lookupFile)
	? json_decode(file_get_contents($lookupFile), true)
	: [];
	$scriptsDir = $newDir . DIRECTORY_SEPARATOR . 'scripts';
	$iterator = new DirectoryIterator($scriptsDir);
	while($iterator->valid()) {
		$item = $iterator->current();
		if ($item->isFile()) {
			$name = pathinfo($item->getBasename(), PATHINFO_FILENAME);
			$lookup[$name] = $item->getRealPath();
		}
		$iterator->next();
	}
	file_put_contents($lookupFile, json_encode($lookup));
}

/**
 * Returns the URL to a zip of the latest release of the given repo, or a URL
 * to a zip of master branch if no release information could be found.
 * @param string $repoUrl URL to a GitHub repository.
 */
function getZipURL($repoUrl) {
	$template = "/https:\/\/github\.com\/(.*?)\/(.*?)\/?$/";
	if (!preg_match($template, $repoUrl, $parts)) {
		throw new Error("Does not seem to be a valid github repo: $repoUrl"
		. PHP_EOL . 'Please let us know if you want to use repos from a new '
		. 'source, or if you could help implement that! :)');
	}
	$user = $parts[1];
	$repo = $parts[2];
	$releasesURL = "https://api.github.com/repos/$user/$repo/releases";
	$info = jampFetchJson($releasesURL, true);
	if (is_array($info) && count($info) > 0) {
		return $info[0]['zipball_url'];
	}
	return "https://github.com/$user/$repo/archive/master.zip";
}

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

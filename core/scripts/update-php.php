<?php

/**
 * Updates PHP installation. Currently only supports updating PHP patch versions
 * (e.g. 7.3.x -> 7.3.y) and only updating the PHP installation that is used by
 * jamp and only on Windows.
 * 
 * Usage: jamp update-php
 */
jampUse(['jampIsWindows', 'jampEcho']);

if (!jampIsWindows()) {
	throw new Exception('jamp update-php only works on Windows');
}

if (!is_dir(JAMP_BASE)) {
	throw new Exception('Unable to find JAMP_BASE directory.');
}

// Check if the latest version of PHP is already installed.
$downloadLink = getNewPatchDownloadLink();
$phpMinorVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
if (!preg_match('/php-(\d+\.\d+\.\d+)/', $downloadLink, $matches)) {
	throw new Exception('Could not extract PHP version from download link.');
}
if ($matches[1] === PHP_VERSION) {
	jampEcho('You already have the latest patch for PHP ' . $phpMinorVersion
		. ' (' . PHP_VERSION . ').');
	exit();
}
$newPhpVersion = $matches[1];

// Download the latest PHP version.
$jampTempDir = JAMP_BASE . 'temp';
if (!is_dir($jampTempDir)) {
	mkdir($jampTempDir);
}
$targetFile = $jampTempDir . DIRECTORY_SEPARATOR . basename($downloadLink);
echo('Planning to upgrade PHP from ' . PHP_VERSION . ' to ' . $newPhpVersion
	. ' from:' . PHP_EOL . $downloadLink . PHP_EOL . '(Downloading to: '
	. $targetFile . ')' . PHP_EOL);
$input = readline('Proceed? [y/n]: ');
if (($input !== 'y') && ($input !== 'Y')) {
	exit();
}
if (is_file($targetFile)) {
	echo ('Skipping download. File exists.' . PHP_EOL);
} else {
	echo ('Downloading...' . PHP_EOL);
	ini_set('user_agent', 'PHP ' . $phpMinorVersion . 'CLI');
	if (file_put_contents($targetFile, fopen($downloadLink, 'r')) === FALSE) {
		throw new Exception('Error downloading file: ' . $downloadLink);
	}
}

// Unzip the PHP archive file.
echo ('Unzipping...' . PHP_EOL);
$zip = new ZipArchive;
$zipOpened = $zip->open($targetFile);
if ($zipOpened !== TRUE) {
	throw new Exception('Could not open zip file: ' . $targetFile . '. Error '
		. 'code: ' . $zipOpened);
}
$unzipDir = $jampTempDir . DIRECTORY_SEPARATOR . basename($targetFile, '.zip');
if (is_dir($unzipDir)) {
	echo('Skipping extraction, directory already exists: ' . $unzipDir . PHP_EOL);
}
else {
	mkdir($unzipDir);
	$zip->extractTo($unzipDir);
}

$phpDir = dirname(PHP_BINARY);
$backupPhpDir = dirname($phpDir) . DIRECTORY_SEPARATOR . basename($phpDir) . '-'
. (new DateTime())->format('Y-m-d') . '-backup';
echo('Swapping out old PHP directory...');
rename($phpDir, $backupPhpDir);
rename($unzipDir, $phpDir);
$oldPhpIniPath = $backupPhpDir . DIRECTORY_SEPARATOR . 'php.ini';
$newPhpIniPath = $phpDir . DIRECTORY_SEPARATOR . 'php.ini';
if (is_file($newPhpIniPath)) {
	echo('Not copying php.ini from ' . $oldPhpIniPath . ' to ' . $newPhpIniPath .
		' because it already exists in the target location.' . PHP_EOL);
}
else {
	copy($oldPhpIniPath, $newPhpIniPath);
}

$oldCaPath = $backupPhpDir . DIRECTORY_SEPARATOR . 'cacert.pem';
$newCaPath = $phpDir . DIRECTORY_SEPARATOR . 'cacert.pem';

if (is_file($newCaPath)) {
	echo('Not copying cacert.pem from ' . $oldCaPath . ' to ' . $newCaPath .
		' because it already exists in the target location.' . PHP_EOL);
}
else {
	if (is_file($oldCaPath)) {
		copy($oldCaPath, $newCaPath);
	}
}

echo('PHP has been updated to ' . $newPhpVersion . '. Please compare the '
. 'following files to check for config updates:' . PHP_EOL
. dirname($newPhpIniPath) . DIRECTORY_SEPARATOR . 'php.ini-development'
. PHP_EOL . $newPhpIniPath . PHP_EOL);
$showFolderAnswer = readline('Show the downloaded file in Explorer? [y/n]: ');
if ($showFolderAnswer === 'y' || $showFolderAnswer === 'Y') {
	pclose(popen('start ' . $phpDir, 'r'));
}

/**
 * Retrieves the latest patch link for the current minor version of PHP from
 * https://windows.php.net/download.
 *
 * @return String Url to download latest PHP patch.
 */
function getNewPatchDownloadLink()
{
	$currentPHPMinorVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
	$downloadPageHtml = file_get_contents('https://windows.php.net/download');
	$dom = new DOMDocument();
	// Ignore warnings and notices when loading HTML (the PHP page had some
	// issues last check).
	set_error_handler(function ($errno) {
		if ($errno === E_WARNING) {
			return;
		}
		if ($errno === E_NOTICE) {
			return;
		}
		return false;
	});
	$dom->loadHTML($downloadPageHtml);
	restore_error_handler();
	$docXPath = new DOMXPath($dom);
	$threadSafeStatus = PHP_ZTS ? '' : '-nts';
	$architecture = getArchitecture();
	foreach ($docXPath->query("//div[@class='info entry']") as $minorVersionSection) {
		$headingNodes = $minorVersionSection->getElementsByTagName('h3');
		if ($headingNodes->length === 0) {
			continue;
		}
		if ($headingNodes->length > 1) {
			throw new Exception('More than one heading found.');
		}
		if (strpos($headingNodes[0]->nodeValue, $currentPHPMinorVersion) === FALSE) {
			continue;
		}
		foreach ($minorVersionSection->getElementsByTagName('a') as $a) {
			$url = $a->attributes['href']->value;
			if (substr($url, 0, 1) === '/') {
				$url = 'https://windows.php.net' . $url;
			}
			if (preg_match(
				'/https:\/\/windows\.php\.net\/downloads\/releases\/php-'
					. $currentPHPMinorVersion . '.\d+' . $threadSafeStatus
					. '-Win\d+-VC\d+-' . $architecture . '\.zip/',
				$url
			)) {
				return $url;
			}
		}
	}
	throw new Exception('Could not find download links for PHP version: '
		. $currentPHPMinorVersion);
}

/**
 * Return the architecture that PHP was built for.
 * 
 * @return String e.g. x86 or x64
 */
function getArchitecture()
{
	$handle = popen(PHP_BINARY . " -i", 'r');
	$infoOutput = stream_get_contents($handle);
	pclose($handle);
	preg_match('/\s*Architecture\s*=>\s*(\S*)/', $infoOutput, $matches);
	if (!$matches || empty($matches[1])) {
		throw new Exception('Unable to read architecture from phpinfo.');
	}
	return trim($matches[1]);
}

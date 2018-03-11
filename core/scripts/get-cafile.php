<?php

/**
 * Downloads the latest version of https://curl.haxx.se/ca/cacert.pem into the
 * PHP directory and sets the openssl.cafile option in php.ini to point to the
 * downloaded file.
 * 
 * Usage: jamp get-cafile
 * 
 * This command will overwrite a previous version of the same file. Information
 * about the source of the file: https://curl.haxx.se/docs/caextract.html
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse('jampEcho');

// Where we are getting the cert data from.
$certUrl = 'https://curl.haxx.se/ca/cacert.pem';

// Where we will save the cert to.
$targetFile = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'cacert.pem';

// Make a backup in case we have a cert file already.
if (file_exists($targetFile)) {
	copy($targetFile, $targetFile . '.bkp');
}

// Get the content of the cacert.pem file.
$certContent = file_get_contents($certUrl);

if (empty($certContent)) {
	throw new Error("Unable to download from: $certUrl");
}

// Save the content as the new cacert.pem file.
file_put_contents($targetFile, $certContent);

// Get path to the php.ini file.
$phpIni = php_ini_loaded_file();

// Get the current contents of the php.ini file.
$iniFileContent = file_get_contents($phpIni);

// No sense continuing if there's no ini file.
if (!$iniFileContent) {
	throw new Error("Could not read php.ini file: $phpIni");
}

// A variable to store a new version of the ini file with the cert path
// added.
$newIni = "";

// Matches for parts of the php.ini file.
$matches = [];

// Expected existing line
$assumedSetting = '/(\R)(openssl.cafile)\s?=\s?"(' . preg_quote($targetFile)
. ')"/';
$otherFilePattern = '/(\R)(openssl.cafile)\s?=\s?"?(.*)"?/';

// If the assumed setting is already present, we don't need to do anything more.
if (preg_match($assumedSetting, $iniFileContent, $matches)) {
	jampEcho('The php.ini file is already pointing openssl.cafile to the '
	. 'cacert.pem file.');
	exit();
}

if (preg_match('/(\R;)(openssl\.cafile)/', $iniFileContent)) {
	// Add the setting if it's currently commented out
	jampEcho('Uncommenting openssl.cafile setting and pointing to cert data file'
	. '...' . PHP_EOL . 'You may need to restart your server for changes to take'
	. ' effect.');
	$replacementLine = '$1$2="' . $targetFile . '"';
	$newIni = preg_replace(
		'/(\R);(openssl\.cafile).*/',
		$replacementLine,
		$iniFileContent
	);
}
elseif (preg_match($otherFilePattern, $iniFileContent, $matches)) {
	jampEcho('The openssl.cafile was already in use, point to: ' . $matches[3]
	. PHP_EOL . 'This is being updated to the path of the new cacert.pem file...'
	. PHP_EOL . 'You may need to restart your server for changes to take '
	. 'effect.');
	$replacementLine = '$1$2="' . $targetFile . '"';
	$newIni = preg_replace(
		'/(\R)(openssl.cafile)\s?=\s?"?(.*)"?/',
		$replacementLine,
		$iniFileContent
	);
}
else {
	throw new Error('Could not find openssl.cafile in php.ini file. Please check '
	. 'your php installation has openssl available and add the openssl setting '
	. 'openssl.cafile="' . $targetFile . '" to the php.ini file.');
}

file_put_contents($phpIni . ".bkp", $iniFileContent);
file_put_contents($phpIni, $newIni);

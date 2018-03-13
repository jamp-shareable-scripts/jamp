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
$certPath = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'cacert.pem';

// Make a backup in case we have a cert file already.
if (file_exists($certPath)) {
	copy($certPath, $certPath . '.bkp');
}

// Get the content of the cacert.pem file.
$certContent = file_get_contents($certUrl);

if (empty($certContent)) {
	throw new Error("Unable to download from: $certUrl");
}
echo "Downloaded $certUrl" . PHP_EOL;

// Save the content as the new cacert.pem file.
file_put_contents($certPath, $certContent);

$certArg = escapeshellarg($certPath);
passthru('jamp ini_set openssl.cafile ' . $certArg);

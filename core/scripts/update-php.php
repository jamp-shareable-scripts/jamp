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
$targetFile = dirname(dirname(PHP_BINARY)) . DIRECTORY_SEPARATOR
. basename($downloadLink);
echo('Planning to download the latest PHP ' . $phpMinorVersion . ' patch '
. 'from:' . PHP_EOL . $downloadLink . PHP_EOL . 'to:' . PHP_EOL
. $targetFile . PHP_EOL);
$input = readline('Proceed? [y/n]: ');
if ($input !== 'y') {
    exit();
}
ini_set('user_agent', 'PHP ' . $phpMinorVersion . 'CLI');
if (file_put_contents($targetFile, fopen($downloadLink, 'r')) === FALSE) {
    throw new Exception('There creating the file: ' . $downloadLink);
}
echo('Successfully downloaded patch. Next steps:' . PHP_EOL);
echo('  1. Unzip the downloaded file into a new directory. Unblock executables '
. 'if necessary.' . PHP_EOL);
echo('  2. Enter the extracted directory.' . PHP_EOL);
echo('  3. Copy all files in the directory into your existing PHP directory ('
. dirname(PHP_BINARY) . '). Replace existing files.' . PHP_EOL);
$input2 = readline('Show the downloaded file in Explorer? [y/n]: ');
if ($input2 === 'y') {
    pclose(popen('start ' . dirname($targetFile), 'r'));
}

/**
 * Retrieves the latest patch link for the current minor version of PHP from
 * https://windows.php.net/download.
 *
 * @return String Url to download latest PHP patch.
 */
function getNewPatchDownloadLink() {
    $currentPHPMinorVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    $downloadPageHtml = file_get_contents('https://windows.php.net/download');
    $dom = new DOMDocument();
    set_error_handler(function($data) {});
    $dom->loadHTML($downloadPageHtml);
    restore_error_handler();
    $docXPath = new DOMXPath($dom);
    $threadSafeStatus = PHP_ZTS ? '' : '-nts';
    $architecture = getArchitecture();
    $downloadPathRegex = str_replace(
        ['/', '.'], ['\/', '\.'], 'https://windows.php.net/downloads'
    );
    $urlPattern = '/' . $downloadPathRegex . '\/releases\/php-'
    . $currentPHPMinorVersion . '.\d+' . $threadSafeStatus
    . '-Win\d+-VC\d+-' . $architecture . '\.zip/';
    foreach($docXPath->query("//div[@class='info entry']") as $minorVersionSection) {
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
                $url,
                $matches)
            ) {
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
function getArchitecture() {
    $handle = popen(PHP_BINARY . " -i", 'r');
    $infoOutput = stream_get_contents($handle);
    pclose($handle);
    preg_match('/\s*Architecture\s*=>\s*(\S*)/', $infoOutput, $matches);
    if (!$matches || empty($matches[1])) {
        throw new Exception('Unable to read architecture from phpinfo.');
    }
    return trim($matches[1]);
}

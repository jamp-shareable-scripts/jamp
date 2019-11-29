<?php

/**
 * Updates PHP installation. Currently only supports updating PHP patch versions
 * (e.g. 7.3.x -> 7.3.y) and only updating the PHP installation that is used by
 * jamp and only on Windows.
 * 
 * Usage: jamp install-apache
 */
jampUse(['jampIsWindows', 'jampEcho', 'jampCopyDirectory']);

if (!jampIsWindows()) {
	throw new Exception('jamp install-apache only works on Windows');
}

if (!is_dir(JAMP_BASE)) {
	throw new Exception('Unable to find JAMP_BASE directory.');
}

$sep = DIRECTORY_SEPARATOR;

// Check if the latest version of PHP is already installed.
$downloadLinkMeta = getLatestApacheLinkMeta();
$downloadLink = $downloadLinkMeta['link'];
$jampTempDir = JAMP_BASE . 'temp';
$targetFile = $jampTempDir . $sep . basename($downloadLink);
echo('Downloading Apache from:' . PHP_EOL . $downloadLink . PHP_EOL
. '(Downloading to: ' . $targetFile . ')' . PHP_EOL);

if (is_file($targetFile)) {
	echo('Skipping download. File exists.' . PHP_EOL);
} else {
	echo('Downloading...' . PHP_EOL);
	ini_set('user_agent', 'PHP ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION
	. 'CLI');
	if (file_put_contents($targetFile, fopen($downloadLink, 'r')) === FALSE) {
		throw new Exception('Error downloading file: ' . $downloadLink);
	}
	if (
		strtolower(hash_file(
			$downloadLinkMeta['checksumInfo']['algorithm'],
			$targetFile,
		)) !== $downloadLinkMeta['checksumInfo']['checksum']
	) {
		throw new Exception('Downloaded file does not have correct checksum');
	}
}

$downloadLink = $downloadLinkMeta['link'];
if (!is_dir($jampTempDir)) {
	mkdir($jampTempDir);
}

$unzipDir = $jampTempDir . $sep . basename($targetFile, '.zip');
if (is_dir($unzipDir)) {
	echo('Skipping extraction, directory already exists: ' . $unzipDir . PHP_EOL);
}
else {
	echo ('Unzipping...' . PHP_EOL);
	$zip = new ZipArchive;
	$zipOpened = $zip->open($targetFile);
	if ($zipOpened !== TRUE) {
		throw new Exception('Could not open zip file: ' . $targetFile . '. Error '
			. 'code: ' . $zipOpened);
	}
	mkdir($unzipDir);
	$zip->extractTo($unzipDir);
	if (!is_dir($unzipDir . $sep . 'Apache24')) {
		throw new Exception('Expected to find Apache24 directory in extracted '
		. 'files');
	}
	if (!is_dir($unzipDir . $sep . 'Apache24' . $sep . 'bin')) {
		throw new Exception('Expected to find Apache24\\bin in extracted files');
	}
	if (!is_dir($unzipDir . $sep . 'Apache24' . $sep . 'conf')) {
		throw new Exception('Expected to find Apache24\\conf in extracted files');
	}
	$zip->close();
}

if (!is_file(JAMP_BASE . 'jamp.ini')) {
	touch(JAMP_BASE . 'jamp.ini');
}
$jampSettings = parse_ini_file(JAMP_BASE . 'jamp.ini');
if (
	!isset($jampSettings['apache_dir'])
) {
	$installDirAnswer = readline('Which directory would you like to install '
	. 'Apache into: ');
	file_put_contents(JAMP_BASE . 'jamp.ini', PHP_EOL . 'apache_dir = "'
	. $installDirAnswer . '"' . PHP_EOL, FILE_APPEND);
}
$installDir = parse_ini_file(JAMP_BASE . 'jamp.ini')['apache_dir'];
if (!is_dir($installDir)) {
	throw new Exception('Directory does not exist: ' . $installDir . PHP_EOL
	. 'Fix apache_dir setting in: ' . JAMP_BASE . 'jamp.ini');
}

$backupDir = dirname($installDir) . $sep . 'backup-'
. basename($installDir) . '-' . str_replace(
	[':', '+'],
	['-', '-'],
	(new DateTime())->format(DateTime::ISO8601)
);

echo('Backing up existing files to: ' . $backupDir . PHP_EOL);
set_error_handler(function ($errno, $errstr) {
	echo($errstr . PHP_EOL);
	throw new Exception('Possible unsuccessful backup. Aborting.');
});
rename($installDir, $backupDir);
restore_error_handler();
mkdir($installDir);
echo('Installing Apache files into: ' . $installDir . PHP_EOL);
jampCopyDirectory($unzipDir, $installDir, [
	'overwrite' => true
]);

$httpdConf = file_get_contents($installDir . $sep . 'Apache24' . $sep . 'conf' . $sep . 'httpd.conf');
$lineEnding = strpos($httpdConf, "\r\n") === FALSE ? "\n" : "\r\n";

// Work with the conf file backwards, so the custom changes are toward the
// bottom.
$phpDir = dirname(PHP_BINARY);
$httpdConfLines = array_reverse(explode($lineEnding, $httpdConf));
$newConfLines = ['', 'PHPIniDir "' . $phpDir . '"'];
$moduleSet = false;
$typeSet = false;
$handlerSet = false;
$rootSet = false;
foreach($httpdConfLines as $lineIndex => $line) {
	if (!$moduleSet && preg_match('/^LoadModule\b/', $line, $moduleMatches)) {
		array_push($newConfLines, 'LoadModule php7_module "' . $phpDir . $sep . 'php7apache2_4.dll' . '"');
		$moduleSet = true;
	}
	if (!$handlerSet && preg_match('/(\s*)#? ?\bAddHandler\b/', $line, $handlerMatches)) {
		array_push($newConfLines, $handlerMatches[1] . 'AddHandler application/x-httpd-php .php');
		$handlerSet = true;
	}
	if (!$typeSet && preg_match('/(\s*)#? ?\bAddType\b/', $line, $typeMatches)) {
		array_push($newConfLines, $typeMatches[1] . 'AddType application/x-httpd-php .php');
		$typeSet = true;
	}
	if (!$rootSet && preg_match('/^Define SRVROOT\b/', $line)) {
		array_push($newConfLines, 'Define SRVROOT "' . $installDir . $sep . 'Apache24' . '"');
		$rootSet = true;
		continue;
	}
	array_push($newConfLines, $line);
}
file_put_contents($installDir . $sep . 'Apache24' . $sep . 'conf' . $sep . 'httpd.conf', implode($lineEnding, array_reverse($newConfLines)));

jampEcho('Apache installed. Notes: ' . PHP_EOL 
. '- PHP has been enabled in the httpd.conf file.' . PHP_EOL
. '- To copy your config across, merge the backup Apache folder with the new '
. 'folder (both folders shown above).' . PHP_EOL
. '- To install Apache as a service, run the following as an administrator in '
. 'PowerShell:' . PHP_EOL . 
'cd "' . $installDir . $sep . 'Apache24' . $sep . 'bin"; .\httpd.exe -k install');

/**
 * Retrieves the latest download link from
 * https://www.apachehaus.com/cgi-bin/download.plx
 *
 * @return String Url to download Apache.
 */
function getLatestApacheLinkMeta()
{
	$overviewPage = file_get_contents(
		'https://www.apachehaus.com/cgi-bin/download.plx'
	);
	$overviewDom = new DOMDocument();
	// Ignore warnings and notices when loading HTML
	set_error_handler(function ($errno) {
		if ($errno === E_WARNING) {
			return;
		}
		if ($errno === E_NOTICE) {
			return;
		}
		return false;
	});
	$overviewDom->loadHTML($overviewPage);
	restore_error_handler();
	$overviewXPath = new DOMXPath($overviewDom);
	$architectureRaw = getArchitecture();
	$architecture = $architectureRaw === 'x64' ? 'x64' : '';
	$links = [];
	foreach ($overviewXPath->query("//table[@class='default']") as $infoTable) {
		$versionNameResult = $overviewXPath->query('tr[1]/td[1]', $infoTable);
		if ($versionNameResult->length < 1) {
			continue;
		}
		$versionName = trim($versionNameResult[0]->textContent);
		if (preg_match('/^Apache .*' . $architecture . '$/', $versionName)) {
			$versionInfo = $overviewXPath->query('tr[2]/td[1]', $infoTable);
			$versionLink = $overviewXPath->query('(tr[2]/td[4]/a)[2]', $infoTable);
			$versionCheckSum = $overviewXPath->query('tr[3]/td[1]', $infoTable);
			if (
				!preg_match(
					'/^(\S+) Checksum: (\S+)$/',
					trim($versionCheckSum[0]->textContent),
					$checksumInfoMatches
				)
			) {
				throw new Exception('Could not parse checksum info.');
			};
			$algorithm = strtolower($checksumInfoMatches[1]);
			$checksum = strtolower($checksumInfoMatches[2]);
			if (!in_array($algorithm, hash_algos())) {
				throw new Exception('Hash algorithm not available: ' . $algorithm);
			}
			array_push($links, [
				'name' => $versionName,
				'info' => trim($versionInfo[0]->textContent),
				'metaLink' => trim($versionLink[0]->attributes['href']->value),
				'checksumInfo' => [
					'algorithm' => $algorithm,
					'checksum' => $checksum,
				],
			]);
		}
	}
	if (empty($links)) {
		throw new Exception('No Apache server download links found');
	}
	echo('Which version of Apache would you like to install?' . PHP_EOL);
	foreach ($links as $key => $link) {
		echo(($key + 1) . '. ' . $link['name'] . ': ' . $link['info'] . PHP_EOL);
	}
	$choiceAnswer = readline('Choice [1 to ' . count($links) . ' or any other '
	. 'answer to cancel]: ');
	$choiceNumber = intval($choiceAnswer);
	if (!$choiceNumber || ($choiceNumber > count($links))) {
		echo 'Download cancelled.';
	}
	$targetLink = $links[$choiceNumber - 1];

	// Go to download page
	$downloadPageHtml = file_get_contents(
		'https://www.apachehaus.com' . $targetLink['metaLink']
	);
	if (!$downloadPageHtml) {
		throw new Error('Failed to download ' . 'https://www.apachehaus.com'
			. $targetLink['metaLink']);
	}
	$downloadDom = new DOMDocument($downloadPageHtml);
	set_error_handler(function ($errno) {
		if ($errno === E_WARNING) {
			return;
		}
		if ($errno === E_NOTICE) {
			return;
		}
		return false;
	});
	$downloadDom->loadHTML($downloadPageHtml);
	restore_error_handler();
	$downloadXPath = new DOMXPath($downloadDom);
	$directLinkResult = $downloadXPath->query(
		'//table[@class="contentpaneopen"]/tr/td/p[2]/a[1]'
	);
	if ($directLinkResult->length < 1) {
		throw new Error('Failed to find direct link to download.');
	}
	$zipLink = trim($directLinkResult[0]->attributes['href']->value);
	$targetLink['link'] = $zipLink;
	return $targetLink;
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

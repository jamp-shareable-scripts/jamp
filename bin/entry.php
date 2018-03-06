<?php

/**
 * Sets up the PHP environment and calls the target jamp script.
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

/**
 * Path to jamp base directory.
 */
define('JAMP_BASE', getenv('JAMP_BASE'));

/**
 * path to install directory. (Contains all scripts from other sources).
 */
define('JAMP_INSTALLED', JAMP_BASE . 'installed' . DIRECTORY_SEPARATOR);

/**
 * Path to jamp core data directory;
 */
define('JAMP_CORE_DATA', JAMP_BASE . 'core' . DIRECTORY_SEPARATOR . 'data'
. DIRECTORY_SEPARATOR);

/**
 * Path to the core scripts directory.
 */
define('JAMP_CORE_SCRIPTS', JAMP_BASE . 'core' . DIRECTORY_SEPARATOR . 'scripts'
. DIRECTORY_SEPARATOR);

/**
 * Path to the local scripts directory.
 */
define('JAMP_LOCAL_SCRIPTS', JAMP_BASE . 'local' . DIRECTORY_SEPARATOR
. 'scripts' . DIRECTORY_SEPARATOR);

/**
 * Path to the helpers directory.
 */
define('JAMP_HELPERS', JAMP_BASE . 'core' .DIRECTORY_SEPARATOR . 'helpers'
. DIRECTORY_SEPARATOR);

jampUse('jampGetScriptPath');
jampRunScript(getenv('JAMP_SCRIPT'));

/**
 * Includes, with require_once, a jamp helper of the given name.
 * @param string|array $helperNames
 */
function jampUse($helperNames) {
	$helpers = is_array($helperNames) ? $helperNames : [$helperNames];
	foreach($helpers as $helper) {
		if (substr_compare($helper, '.php', -4) === 0) {
			require_once JAMP_HELPERS . $helper;
		}
		else {
			require_once JAMP_HELPERS . $helper . '.php';
		}
	}
}

/**
 * Runs the jamp script of the given name.
 * @param string $scriptName
 */
function jampRunScript($scriptName) {

	/**
	 * Minimise the number of unnecessary variables in the actual jamp script by
	 * running it in the scope of this anonymous function.
	 * @var function
	 */
	$includeScript = function($jampScriptFilename) {
		global $argv; // Make arguments available to script.
		try {
			include $jampScriptFilename;
		} catch (Throwable $error) {
			jampDisplayError($error);
		}
	};

	$scriptPath = jampGetScriptPath($scriptName);
	if ($scriptPath) {
		return $includeScript($scriptPath);
	}
	else {
		// Check if we know where the script might be located.
		$locations = json_decode(file_get_contents(
			JAMP_CORE_DATA . 'other-script-locations.json'
		), true);
		if (isset($locations[$scriptName])) {
			offerInstall($scriptName, $locations[$scriptName], $includeScript);
			return;
		}
		
	}

	throw new Error("Unable to find $scriptName. See commands with 'jamp show'");
}

function offerInstall($name, $location, $runScript) {
	echo "$name is not installed. However, it is available from:" . PHP_EOL
	. $location . PHP_EOL . 'Only install scripts from locations you trust.'
	. PHP_EOL . "Would you like to install and run $name [y|n]? ";
	$input = strtolower(trim(fgets(STDIN)));
	if ($input !== 'y') {
		return;
	}
	if (!is_dir(JAMP_INSTALLED)) {
		mkdir(JAMP_INSTALLED);
	}
	$rand = rand(10000,99999);
	$tempZip = JAMP_CORE_DATA . $rand . 'install.zip';
	$tempDir = JAMP_CORE_DATA . $rand . 'install';
	if (file_exists($tempZip)) {
		throw new Error("Could not install, temp file already exists: $tempZip");
	}
	if (1 > file_put_contents($tempZip, file_get_contents($location))) {
		throw new Error("Failed to download: $location");
	}
	$zip = new ZipArchive;
	$zip->open($tempZip);
	$zip->extractTo($tempDir);
	$zip->close();
	preg_match('/github.com\\/.*?\\/(.*?)\\/archive\\/.*?\\.zip/',
	$location, $matches);
	$repoName = $matches[1];
	$from = $tempDir . DIRECTORY_SEPARATOR
	. array_diff(scandir($tempDir),['.','..'])[2];
	$to = JAMP_INSTALLED . $repoName;
	rename($from, $to);
	rmdir($tempDir);
	unlink($tempZip);
	addToLookup($to);
	echo "$name script added." . PHP_EOL;
	$scriptPath = jampGetScriptPath($name);
	$runScript($scriptPath);
}

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
 * Display an error message, formatted for terminal/command line display.
 * @param Throwable $error
 */
function jampDisplayError($error) {
	echo 'Error: ' . $error->getMessage() . PHP_EOL;
	echo $error->getFile() . ':' . $error->getLine() . PHP_EOL;
	foreach($error->getTrace() as $step) {
		extract($step);
		$argsCopy = isset($args) ? $args : null;
		if (!empty($argsCopy) && is_array($args)) {
			array_walk($argsCopy, function(&$item) {
				if (is_object($item)) {
					$item = get_class($item) . ' Object';
				}
			});
		}
		$argsString = is_array($argsCopy) ? implode(',', $argsCopy) : '';
		echo "$file:$line $function($argsString)" . PHP_EOL;
	}
}

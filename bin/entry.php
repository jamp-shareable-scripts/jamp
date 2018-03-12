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

/**
 * Ask the user if they would like to install a script, and start the install
 * if they do.
 * @param string $name The script name.
 * @param string $location The location of the script's repository.
 * @param string $runScript A function that will run the script.
 */
function offerInstall($name, $location, $runScript) {
	echo "$name is not installed. However, it is available from:" . PHP_EOL
	. $location . PHP_EOL . 'Only install scripts from locations you trust.'
	. PHP_EOL . "Would you like to install and run $name [y|n]? ";
	$input = strtolower(trim(fgets(STDIN)));
	if ($input !== 'y') {
		return;
	}
	passthru("jamp install $name");
	$scriptPath = jampGetScriptPath($name);
	$runScript($scriptPath);
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

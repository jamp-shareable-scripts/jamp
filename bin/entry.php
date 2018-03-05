<?php

/**
 * Sets up the PHP environment and calls the target jamp script.
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

/**
 * Path to the core scripts directory.
 */
define('JAMP_CORE_SCRIPTS', getenv('JAMP_BASE') . 'core' . DIRECTORY_SEPARATOR
. 'scripts' . DIRECTORY_SEPARATOR);

/**
 * Path to the local scripts directory.
 */
define('JAMP_LOCAL_SCRIPTS', getenv('JAMP_BASE') . 'local' . DIRECTORY_SEPARATOR
. 'scripts' . DIRECTORY_SEPARATOR);

/**
 * Path to the helpers directory.
 */
define('JAMP_HELPERS', getenv('JAMP_BASE') . 'core' .DIRECTORY_SEPARATOR
. 'helpers' . DIRECTORY_SEPARATOR);

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

	throw new Error("Unable to find $scriptName. See commands with 'jamp show'");
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

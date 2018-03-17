<?php

/**
 * Gets the path for the give script name if it exists or returns false if the
 * script file cannot be found.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

/**
 * Gets the path of the given script if it exists, or return false.
 * @param  string $scriptName
 * @return string|boolean The full path to the script or false.
 */
function jampGetScriptPath($scriptName) {
	$localName = JAMP_BASE . 'local' . DIRECTORY_SEPARATOR . 'scripts'
	. DIRECTORY_SEPARATOR . $scriptName . '.php';
	
	// Check first for a local script, so local scripts can override global ones.
	if (file_exists($localName)) {
		return $localName;
	}

	$coreName = JAMP_BASE . 'core' . DIRECTORY_SEPARATOR . 'scripts'
	. DIRECTORY_SEPARATOR . $scriptName . '.php';
	if (file_exists($coreName)) {
		return $coreName;
	}
	
	$lookupFile = JAMP_INSTALLED . 'lookup.json';
	if (is_file($lookupFile)) {
		$lookup = json_decode(file_get_contents($lookupFile), true);
		if (isset($lookup[$scriptName]) && is_file($lookup[$scriptName])) {
			return $lookup[$scriptName];
		}
	}

	return false;
}

<?php

/**
 * Gets the path for the give script name if it exists or returns false if the
 * script file cannot be found.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

/**
 * Gets the path of the given script if it exists, or return false.
 * @param  string $scriptName
 * @return string|boolean The full path to the script or false.
 */
function jampGetScriptPath($scriptName) {
	$base = getenv('JAMP_BASE');
	$localName = $base . 'local' . DIRECTORY_SEPARATOR . 'scripts'
	. DIRECTORY_SEPARATOR . $scriptName . '.php';
	
	// Check first for a local script, so local scripts can override global ones.
	if (file_exists($localName)) {
		return $localName;
	}

	$coreName = $base . 'core' . DIRECTORY_SEPARATOR . 'scripts'
	. DIRECTORY_SEPARATOR . $scriptName . '.php';
	if (file_exists($coreName)) {
		return $coreName;
	}

	return false;
}

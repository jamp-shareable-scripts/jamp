<?php

/**
 * Check if the script is running in Windows environment.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

/**
 * Returns true if the OS appears to be windows, false otherwise.
 * @return boolean
 */
function jampIsWindows() {
	return strtolower(substr(PHP_OS, 0, 3)) === "win";
}

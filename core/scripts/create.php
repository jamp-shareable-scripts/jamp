<?php

/**
 * Creates a new jamp script.
 * 
 * By default, the script is created in the jamp local folder and, therefore,
 * ignored by git. If the script could be globally useful, including the -g, or
 * --global, option will create the script in the jamp core folder, which is not
 * ignored by git. The -s, or --silent, argument will prevent the script from
 * attempting to automatically open the new script in a text editor.
 *
 * Usage: jamp create [-g|--global] [-s|--silent] <script name>
 * 
 *   -g,--global Create the new script in the core folder, included by git
 *   -s,--silent Prevent the new script automatically opening in a text editor
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

// Default name for new script.
define('DEFAULT_SCRIPT_NAME', 'newscript');

jampUse(['jampEcho', 'jampTextEditor']);

$options = getopt("gs", ['global', 'silent'], $lastId);
$isGlobal = isset($options['global']) || isset($options['g']);
$isSilent = isset($options['silent']) || isset($options['s']);
$name = empty($argv[$lastId]) ? DEFAULT_SCRIPT_NAME : $argv[$lastId];

// The file name for the new script, either the default above or a given
// argument if it exists.
$scriptName = filterScriptName($name);

// The directory for the new script.
$scriptDir = getScriptDir($isGlobal);

// The full path to the script.
$scriptPath = $scriptDir . DIRECTORY_SEPARATOR . $scriptName . ".php";

// Check the if the script already exists when we're not using the default
// name.
if ($scriptName !== DEFAULT_SCRIPT_NAME && file_exists($scriptPath)) {

	// Let the user know how to edit an existing script.
	throw new Error("Script already exists: $scriptPath" . PHP_EOL . "Try "
	. "editing the script instead: \"jamp edit $scriptName\" or pick a different "
	. "name for the script.");
}

// If we are making the first non-global script, create a local folder for
// it and future local scripts to live in.
if (!$isGlobal && !is_dir($scriptDir)) {
	if (!is_dir(dirname($scriptDir))) {
		// Create <jamp base path>/local directory.
		mkdir(dirname($scriptDir));
	}
	
	// Create <jamp base path>/local/scripts directory.
	mkdir($scriptDir);
}

// Get the new jamp template.
$template = getJampTemplate($scriptName);

// Save the script file.
file_put_contents($scriptPath, $template);

if (!$isSilent) {
	// Open the new jamp script in a text editor.
	jampTextEditor($scriptPath);
}

/**
 * Returns a valid script name. Basically, the file name of the script
 * without the .php extension.
 * @return string The name of the script.
 */
function filterScriptName($name) {
	return substr_compare($name, ".php", -4) === 0
	? substr($name, 0, -4)
	: $name;
}

/**
 * Gets the directory for the new script base on whether to make it global or
 * not.
 * @return string
 */
function getScriptDir($isGlobal) {
	if ($isGlobal) {
		// Put it in the same directory as this script.
		return __DIR__;
	}
	return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'local'
	. DIRECTORY_SEPARATOR . 'scripts';
}

/**
 * Returns a template for the new jamp script.
 * @return string jamp template.
 */
function getJampTemplate($scriptName) {
	$isDefaultName = $scriptName === DEFAULT_SCRIPT_NAME;
	$template = '<?php'
	. ($isDefaultName ? ' // Remember to change the name of the file when you '
	. 'first save it!' : '') . PHP_EOL
	. '/**' . PHP_EOL
	. ' * Usage: jamp ' . $scriptName . PHP_EOL
	. ' * ' . PHP_EOL
	. ' */' . PHP_EOL;
	return $template;
}

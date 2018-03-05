<?php

/**
 * Sets the editor jamp will use to open files.
 * 
 * Usage: jamp set-editor <editor program name>
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

if (empty($argv[1])) {
	passthru('jamp usage set-editor');
	exit;
}

jampUse('jampEcho');

$configFile = getenv('JAMP_BASE') . 'jamp.ini';

$editorSetting = 'editor = ' . $argv[1];
echo 'Writing line:' . PHP_EOL . $editorSetting . PHP_EOL . 'to jamp.ini file.'
. PHP_EOL . 'Proceed [y|n]? ';

$input = strtolower(trim(fgets(STDIN)));
if ($input !== 'y') {
	jampEcho('Exiting.');
	exit;
}

if (!file_exists($configFile)) {
	file_put_contents($configFile, '; jamp configuration' . PHP_EOL . PHP_EOL
	. $editorSetting . PHP_EOL);
	
}
else {
	$currentConfig = file_get_contents($configFile);
	$newConfig = preg_replace(
		'/\r?\n?editor\\s*=.*\r?\n?/',
		PHP_EOL . $editorSetting . PHP_EOL,
		$currentConfig
	);
	file_put_contents($configFile, $newConfig);
}

jampEcho("Default editor set in $configFile");

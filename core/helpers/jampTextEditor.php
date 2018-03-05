<?php

/**
 * Opens the given path in a text editor.
 * 
 * It will use the editor value set in <jamp base folder>/jamp.ini as the text
 * editor if it exists. If there is no config file, or the config editor setting
 * does not exist, then a message will be displayed showing the user the path so
 * they can open it manually and how to set a default editor for jamp.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

jampUse('jampEcho');

/**
 * Prompts the user to open the path in a text editor.
 * @param string $path The file to open.
 */
function jampTextEditor($path) {
	$configFile = getenv('JAMP_BASE') . 'jamp.ini';
	if (is_file($configFile)) {
		$config = parse_ini_file($configFile);
		if (!empty($config['editor'])) {
			passthru($config['editor'] . ' ' . $path);
			return;
		}
	}
	jampEcho("In an editor, open: $path" . PHP_EOL . 'Note: you may set a '
	. 'default editor using:' . PHP_EOL . 'jamp set-editor <text editor program '
	. 'path>' . PHP_EOL . 'On Windows, if you use "jamp set-editor start", the '
	. 'default program for .php files will be used. That is, "start <script '
	. 'path>" will be called to open a file.');
}

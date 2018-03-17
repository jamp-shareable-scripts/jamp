<?php
/**
 * Updates an option in the php.ini file with a new value.
 * 
 * Usage: jamp ini_set <option name> <option value>
 *    jamp ini_set -c|--comment <option name> <option value>
 *
 *   -c,--comment Turns a configuration line into a comment.
 * 
 * If updating an option without providing an option value, that option will be
 * set to an empty value.
 * 
 * WARNING: unlike PHP's ini_set function (which would not make much sense in
 * this context), this script will update the php.ini file itself, setting the
 * the configuration value permanently, not just for the execution of the
 * current script.
 * 
 * NOTE: When commenting out a configuration option, the value of that option is
 * also required. This is temporary solution to help avoid commenting out
 * configuration options that might have multiple values; for example, it
 * prevents one accidentally commenting out all the extension = ... values.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

$opts = getopt('c', ['--comment'], $lastArg);
$doComment = isset($opts['c']) || isset($opts['comment']);

if (empty($argv[$lastArg])) {
	passthru('jamp usage ini_set');
	exit;
}

/**
 * The name of the configuration option to update.
 * @var string
 */
$name = $argv[$lastArg];

/**
 * The intended value for the configuration option.
 * @var string
 */
$value = empty($argv[$lastArg+1]) ? '' : $argv[$lastArg+1];

/**
 * For user messages, to make it clear if the value is empty or not.
 * @var string
 */
$humanValue = empty($value) ? 'empty' : $value;

/**
 * Escaped option name for use in regex.
 * @var string
 */
$nameArg = preg_quote($name);

/**
 * Escaped option value for use in regex.
 * @var string
 */
$valueArg = preg_quote($value);

/**
 * Path to the php.ini file.
 * @var string
 */
$phpIni = php_ini_loaded_file();

/**
 * Current contents of the php.ini file.
 * @var string
 */
$iniFileContent = file_get_contents($phpIni);

// No sense continuing if there's no ini file.
if (!$iniFileContent) {
	throw new Error("Could not read php.ini file: $phpIni");
}

/**
 * New version of the ini file.
 * @var string
 */
$newIni = "";

/**
 * Search pattern for existing option/value pair.
 * @var string
 */
$sameValue = '/(\R)(' . $nameArg . ') ?= ?("?' . $valueArg . '"?)(\r|\n)/';

/**
 * Search pattern for existing option with a different target value.
 * @var string
 */
$diffValue = '/(\R)(' . $nameArg . ') ?= ?"?(.*?)"?(\r|\n)/';

/**
 * Search pattern for line where configuration option is commented out.
 * @var string
 */
$commValue = '/(\R);(' . $nameArg . ') ?= ?"?(.*?)"?(\r|\n)/';

// If the assumed setting is already present, we don't need to do anything more.
if (!$doComment && preg_match($sameValue, $iniFileContent)) {
	jampEcho("$name already has the value $value. No changes were made.");
	return;
}

// If we want to comment out an options that's already commented out, there's
// nothing more to do.
// TODO: technically, this is a bug because this searches for any commented line
// starting with the configuration option; whereas, we are more interested if
// the value is the same as well.
if ($doComment && preg_match($commValue, $iniFileContent)) {
	jampEcho("$name is already commented.");
	return;
}

// Check that we're matching a configuration option and its value when looking
// for configuration lines to comment out.
if ($doComment && !preg_match($sameValue, $iniFileContent)) {
	jampEcho("Could not find $name where value is $humanValue");
	return;
}

if (preg_match_all($diffValue, $iniFileContent) > 1) {
	throw new Error('Unable to support configuration options that can be set '
	. "multiple times and more than one occurence of '$name' was found.");
}

// Update an option where a different value is present.
if (!$doComment && preg_match($diffValue, $iniFileContent)) {
	$spacing = empty($value) ? '' : ' ';
	$valuePart = (empty($value) || ctype_alnum($value) || is_numeric($value))
	? $value : ('"' . $value . '"');
	$replacement = '$1$2 ='. $spacing . $valuePart . '$3';
	$newIni = preg_replace(
		'/(\R)(' . $nameArg . ') ?=.*?(\r|\n)/', $replacement, $iniFileContent
	);
}

// Update an option where it is currently commented out.
elseif (!$doComment && preg_match($commValue, $iniFileContent)) {
	echo "Note: uncommenting $name" . PHP_EOL;
	$spacing = empty($value) ? '' : ' ';
	$valuePart = (empty($value) || ctype_alnum($value) || is_numeric($value))
	? $value : ('"' . $value . '"');
	$replacement = '$1$2 =' . $spacing . $valuePart . '$3';
	$newIni = preg_replace(
		'/(\R);(' . $nameArg . ') ?=.*?(\r|\n)/', $replacement, $iniFileContent
	);
}

// Update an option where it does not exist yet.
elseif (!$doComment) {
	echo "Note: adding $name to ini file." . PHP_EOL;
	$newline = preg_match('/(\R)/', $iniFileContent, $matches)
	? $matches[1] : PHP_EOL;
	$spacing = empty($value) ? '' : ' ';
	$valuePart = (empty($value) || ctype_alnum($value) || is_numeric($value))
	? $value : ('"' . $value . '"');
	$newIni = $iniFileContent . "$newline; Value added via jamp$newline"
	. "$name =$spacing$valuePart$newline";
}

// Comment out an option.
elseif ($doComment && preg_match($sameValue, $iniFileContent)) {
	$spacing = empty($value) ? '' : ' ';
	$replacement = '$1;$2 =' . $spacing . '$3$4$5';
	$newIni = preg_replace(
		$sameValue, $replacement, $iniFileContent
	);
}

// Something seems wrong, abort.
if (empty($newIni)) {
	throw new Error("Invalid update.");
}

// Create a backup of the current version of the php.ini file.
file_put_contents($phpIni . ".bkp", $iniFileContent);

// Update the php.ini file.
file_put_contents($phpIni, $newIni);

// Let the user know what happened.
if ($doComment) {
	jampEcho("Commented out: $name: $humanValue");
}
else {
	jampEcho("$name set to: $humanValue");
}

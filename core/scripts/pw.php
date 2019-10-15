<?php

/**
 * Generates a random password.
 * 
 * Usage: jamp pw [-l <length>|--length <length>]
 * 
 *   -l,--length The length of the password to generate. Default 20.
 * 
 * Note that it can be helpful to pass the output straight to the clipboard. For
 * example, `jamp pw | clip` (using the clip program on Windows).
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$opts = getopt('l:', ['length:']);

$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789,./;\''
. '[]\`-=~!@#$%^&*()_';

$length = empty($opts['length']) ? 
((empty($opts['l'])) ? 20 : $opts['l']) : $opts['length'];

$password = '';
$index = 0;
$lastIndex = (strlen($chars) - 1);
while ($index++ < $length) {
    $password .= $chars[random_int(0, $lastIndex)];
}

echo $password;

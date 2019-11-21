<?php

/**
 * Encodes a string with PHP's rawurlencode function.
 * 
 * Usage: jamp rawurlencode <input string>
 *        ... | jamp rawurlencode
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampArgOrStdIn', 'jampEcho']);

$input = jampArgOrStdIn();
jampEcho(rawurlencode($input));

<?php

/**
 * Returns the value of the given $argv index; if it is not set, falls back to
 * reading `stdin`.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

/**
 * Returns the value of the given $argv index; if it is not set, falls back to
 * reading `stdin`.
 * @return String|NULL
 */
function jampArgOrStdIn($argIndex = 1) {
    global $argv;
    if (isset($argv[$argIndex])) {
        return $argv[$argIndex];
    }
    return stream_get_contents(STDIN);
}

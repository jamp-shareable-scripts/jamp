<?php

/**
 * Makes input lowercase.
 * 
 * Works as a wrapper for PHP's strtolower function.
 * 
 * Usage: jamp strtolower <input>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$input = isset($argv[1]) ? $argv[1] : fgets(STDIN);
echo strtolower($input);

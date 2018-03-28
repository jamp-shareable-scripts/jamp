<?php

/**
 * Gets the hex representation of the given integer.
 * 
 * Works as a wrapper for PHP's dechex function.
 * 
 * Usage: jamp dechex <character>
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

echo dechex((int) $argv[1]);

<?php

/**
 * Test that the ord script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$letter = 'A';
$charCode = ord($letter);
$result = trim(exec("jamp ord $letter"));
test($charCode === (int) $result, "Input converted to charcode");

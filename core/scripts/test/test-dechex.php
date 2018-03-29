<?php

/**
 * Test that the dechex script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$num = 451;
$hex = dechex(451);
$result = trim(exec("jamp dechex $num"));
test($hex === $result, "Input converted to hex");

<?php

/**
 * Test that the base64_encode script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$plain = "abcd";
$encoded = base64_encode($plain);
$encodedResult = trim(exec("jamp base64_encode \"$plain\""));
test($encodedResult === $encoded, "Input should be encoded");

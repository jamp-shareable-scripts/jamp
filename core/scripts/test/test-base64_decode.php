<?php

/**
 * Test that the base64_decode script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$raw = "abcd";
$encoded = base64_encode($raw);
$result = trim(exec("jamp base64_decode \"$encoded\""));
test($result === $raw, "Input should be decoded");

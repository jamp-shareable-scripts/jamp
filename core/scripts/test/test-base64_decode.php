<?php

$raw = "abcd";
$encoded = base64_encode($raw);
$result = trim(exec("jamp base64_decode \"$encoded\""));
test($result === $raw, "Input should be decoded");

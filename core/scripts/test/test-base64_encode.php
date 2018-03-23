<?php

$plain = "abcd";
$encoded = base64_encode($plain);
$encodedResult = trim(exec("jamp base64_encode \"$plain\""));
test($encodedResult === $encoded, "Input should be encoded");

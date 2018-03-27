<?php

/**
 * Test that the usage script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampIsWindows');

// Reconstruct the expected output so the line endings are correct.
$expected = 
PHP_EOL
. 'Displays the usage for the requested jamp script.' . PHP_EOL
. PHP_EOL
. 'Usage: jamp usage <script name>' . PHP_EOL
. PHP_EOL
. '@author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>'
. PHP_EOL
. '@license GPL-2.0' . PHP_EOL . PHP_EOL . (jampIsWindows() ? '' : PHP_EOL);
ob_start();
system('jamp usage usage');
$output = ob_get_clean();
test($output === $expected, 'The usage output should be displayed');

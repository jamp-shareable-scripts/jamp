<?php

/**
 * Uses 'echo' in a consistent way across operating systems.
 * 
 * That is, it ensures that a new line is present after the output is echoed.
 * It is most useful for the outputting the final line of the script as Windows
 * will always add a newline after it whereas other OSes will not add a new
 * line. It is helpful to follow the general rule: always use echo with a new
 * line for all output apart from the very last line, then, for the very last
 * line of output, use jampEcho;
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampIsWindows');

/**
 * Echoes $output in a uniform way across platforms.
 * @param string $output The string to echo.
 */
function jampEcho($output) {
	if (jampIsWindows()) {
		echo $output;
	}
	else {
		echo $output . PHP_EOL;
	}
}

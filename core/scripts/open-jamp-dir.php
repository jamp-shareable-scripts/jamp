<?php
/**
 * Opens the jamp directory.
 * 
 * Usage: jamp open-jamp-dir
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampIsWindows');

if (jampIsWindows()) {
	passthru('start ' . JAMP_BASE);
}
else {
	jampEcho(JAMP_BASE);
}

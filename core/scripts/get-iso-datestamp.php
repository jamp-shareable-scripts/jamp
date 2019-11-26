<?php

/**
 * Returns datestamp for the current date in the format Y-m-d (e.g. 2019-01-01).
 * 
 * Usage: jamp get-iso-datestamp
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse(['jampEcho']);
jampEcho((new DateTime())->format('Y-m-d'));

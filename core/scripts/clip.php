<?php

/**
 * Copies data to clipboard. Currently only supports Windows.
 *
 * Usage: <cmd generating output> | jamp clip
 * 
 * WARNING: performs a trim on the input piped into it.
 * WARNING: only intended for single-line input.
 */

jampUse('jampIsWindows');

if (!jampIsWindows()) {
    throw new Exception('jamp clip currently only works on Windows');
}

$contentForClipboard = str_replace(
    '\'', '\'\'', trim(stream_get_contents(STDIN))
);

$cmd = 'powershell -Command  "& {echo \'' . $contentForClipboard
    . '\' | Set-Clipboard -Value {$_.Trim()}}"';
pclose(popen('start "" /B ' . $cmd, 'r'));

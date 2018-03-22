<?php

/**
 * Test that the hash_file script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$fileName = 'testhash_filescript' . rand(10000, 99999);
$filePath = JAMP_CORE_DATA . $fileName . '.txt';
$content = 'File used to test hash_file function';
$algo = 'sha256';
$expectedHash = hash($algo, $content);

if (file_exists($filePath)) {
	throw new Error("File unexpectedly already exists: $filePath");
}
file_put_contents($filePath, $content);

// Check that hashes are calculated correctly.
$algoArg = escapeshellarg($algo);
$fileArg = escapeshellarg($filePath);
$checksumArg = escapeshellarg($expectedHash);
$hash = exec("jamp hash_file $algoArg $fileArg");
test($expectedHash === $hash, 'The hash of the file is shown.');

// Check that checksums are compared correctly.
$message1 = exec("jamp hash_file -c $checksumArg $algoArg $fileArg");
$expectMessage1 = 'Checksum matches :)';
test($expectMessage1 === $message1, 'Good match message is shown');

$message2 = exec("jamp hash_file -c \"wrong hash\" $algoArg $fileArg");
$expectMessage2 = 'Checksum does not match :(';
test($expectMessage2 === $message2, 'Non-match message is shown');

// Cleanup
unlink($filePath);

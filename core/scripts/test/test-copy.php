<?php

/**
 * Test that the copy script works as expected.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

$testDir = JAMP_CORE_DATA . DIRECTORY_SEPARATOR . 'testcopy'
. rand(10000, 99999) . DIRECTORY_SEPARATOR;
if (file_exists($testDir)) {
	throw new Error("Directory unexpectedly exists: $testDir");
}

// Make a test directory.
mkdir($testDir);

// Set all the test file names. Files will be moved from a directory to b 
// directory.
$aDir = $testDir . 'a' . DIRECTORY_SEPARATOR;
$bDir = $testDir . 'b' . DIRECTORY_SEPARATOR;
$aaDir = $aDir . 'a' . DIRECTORY_SEPARATOR;
$baDir = $bDir . 'a' . DIRECTORY_SEPARATOR;
$a1File = $aDir . 'a1.txt';
$b1File = $bDir . 'a1.txt';
$a2File = $aDir . 'a2.txt';
$b2File = $bDir . 'a2.txt';
$aa1File = $aaDir . 'aa1.txt';
$ba1File = $baDir . 'aa1.txt';
$aa2File = $aaDir . 'aa2.txt';
$ba2File = $baDir . 'aa2.txt';

// Create the initial files needed for the test.
mkdir($aDir);
mkdir($aaDir);
touch($a1File);
touch($a2File);
touch($aa1File);
touch($aa2File);

// Do the copy.
$aArg = escapeshellarg($aDir);
$bArg = escapeshellarg($bDir);
ob_start();
passthru("jamp copy $aArg $bArg");
ob_end_clean();

// Test that the files and directories were copied as expected.
test(is_dir($bDir), 'Subdirectory should exist');
test(is_dir($baDir), 'Subdirectory should exist');
test(is_file($b1File), 'File 1 in directory should exist');
test(is_file($b2File), 'File 2 in directory should exist');
test(is_file($ba1File), 'File 1 in subdirectory should exist');
test(is_file($ba2File), 'File 2 in subdirectory should exist');

// Clean up the target directory.
unlink($ba2File);
unlink($ba1File);
unlink($b2File);
unlink($b1File);
rmdir($baDir);
rmdir($bDir);

// Clean up the source directory.
unlink($aa2File);
unlink($aa1File);
unlink($a2File);
unlink($a1File);
rmdir($aaDir);
rmdir($aDir);

// Clean up the base test directory.
rmdir($testDir);

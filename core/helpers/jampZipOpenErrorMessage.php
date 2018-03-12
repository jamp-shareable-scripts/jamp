<?php

/**
 * Clarifies a ZipArchive::open error code.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

/**
 * Return a string representation of the ZipArchive::open error code.
 * @param  string $code The error code returned when trying to open a zip file.
 * @return string The error message.
 */
function jampZipOpenErrorMessage($code) {
	switch ($code) {
		case ZipArchive::ER_EXISTS: return 'File already exists';
		case ZipArchive::ER_INCONS: return 'Zip archive inconsistent';
		case ZipArchive::ER_INVAL: return 'Invalid argument';
		case ZipArchive::ER_MEMORY: return 'Malloc failure';
		case ZipArchive::ER_NOENT: return 'No such file';
		case ZipArchive::ER_NOZIP: return 'Not a zip archive';
		case ZipArchive::ER_OPEN: return 'Can\'t open file';
		case ZipArchive::ER_READ: return 'Read error';
		case ZipArchive::ER_SEEK: return 'Seek error';
		default: return "Unknown error code $code";
	}
}

<?php

/**
 * Performs a network request to download a resource.
 * 
 * @author  jampperson <https://github.com/jampperson>
 * @license GPL-2.0
 */

/**
 * Returns a resource from the given $url.
 * @param  string $url The URL of the resource.
 * @param  string $accept The resource type to expect.
 * @return string The response data.
 */
function jampFetch($url, $accept = 'text/html') {
	$options = [
		CURLOPT_URL => $url,
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 3,
		CURLOPT_HTTPHEADER => [
			'User-Agent: jamp client/0.0.1',
			'Accept: ' . $accept,
			'Accept-Language: en-US',
			'Upgrade-Insecure-Requests: 1'
		]
	];
	$handle = curl_init();
	curl_setopt_array($handle, $options);
	$raw = curl_exec($handle);
	$curlError = curl_error($handle);
	curl_close($handle);
	if ($raw === false) {
		throw new Error($curlError);
	}
	return $raw;
}

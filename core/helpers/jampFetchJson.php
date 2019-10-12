<?php

/**
 * Performs a network request to retrieve a JSON resource.
 * 
 * @author  jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
 * @license GPL-2.0
 */

jampUse('jampEcho');

/**
 * Returns a JSON resource from the given $url.
 * @param  string $url The URL of the JSON resource.
 * @param  boolean $assoc When true, returned objects will be converted into 
 *                        associative arrays.
 * @return object|array
 */
function jampFetchJson($url, $assoc = false) {
	$options = [
		CURLOPT_URL => $url,
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 3,
		CURLOPT_HTTPHEADER => [
			'User-Agent: jamp client/0.0.1',
			'Accept: application/json',
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
		if (strpos($curlError, "SSL certificate problem: unable to get local issuer certificate") !== FALSE) {
			jampEcho("It may be possible to resolve the following error by running:");
			jampEcho("jamp get-cafile");
			jampEcho("This will download the cacert.pem file from https://curl.haxx.se/ca/cacert.pem");
		}
		throw new Error($curlError);
	}
	$response = json_decode($raw, $assoc);
	$jsonError = json_last_error();
	if ($jsonError !== JSON_ERROR_NONE) {
		throw new Error(json_last_error_msg());
	}
	return $response;
}

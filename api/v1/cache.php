<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Number of seconds a page should remain cached for
$cache_expires = 10;

// Checks whether the page has been cached or not
function is_cached($file) {
	echo "checking if cached\n";
	global $cache_expires;
	$cachefile_created = (file_exists($file)) ? @filemtime($file) : 0;
	return ((time() - $cache_expires) < $cachefile_created);
}

// Reads from a cached file
function read_cache($file) {
	echo "reading cache\n";
	return file_get_contents($file);
}

// Writes to a cached file
function write_cache($file, $out) {
	echo "writing cache\n";
	file_put_contents($file, $out);
}

function getLiveData($url, $cache_file) {
	// Check if it has already been cached and not expired
	// If true then we output the cached file contents and finish
	if (is_cached($cache_file)) {
		return read_cache($cache_file);
	}
	$contents = file_get_contents($url);
	write_cache($cache_file, $contents);
	return $contents;
}
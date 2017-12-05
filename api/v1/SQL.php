<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//$host = "localhost";
//$host = "127.0.0.1";
$host = "localhost";
$db = "u800402696_busyb";
$user = "u800402696_group";
$pass = file_get_contents(__DIR__ . '/pw.txt');
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false,
];

try {
	$pdo = new PDO($dsn, $user, $pass, $opt);
} catch (PDOException $e) {
	echo "Could not connect to database\n";
	http_response_code(500);
	echo $e;
}

function getAllShapes() {
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM shapes");
	$stmt->execute();
	$results = $stmt->fetchAll();

	return encodeResults($results);
}

function getShapeByRoute($route) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT DISTINCT shape_id FROM trips WHERE route_id = ?");
	$stmt->execute([$route]);
	$results = $stmt->fetchAll();
	$shapeIDs = array();
	for ($i = 0; $i < count($results); $i++) {
		$shapeIDs[$i] = $results[$i]["shape_id"];
	}
	$in = str_repeat('?,', count($shapeIDs) - 1) . '?';
	$query = "SELECT * FROM shapes WHERE shape_id IN ($in)";
	$stmt = $pdo->prepare($query);
	$stmt->execute($shapeIDs);
	$results = $stmt->fetchAll();

	return encodeResults($results);
}

function getShapeByTripId($tripId) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT shape_id FROM trips WHERE trip_id = ?");
	$stmt->execute([$tripId]);
	$shapeIDs = $stmt->fetchAll();
	$shapeID = $shapeIDs[0]["shape_id"];
	$query = "SELECT sequence, latitude, longitude FROM shapes WHERE shape_id = ?";
	$stmt = $pdo->prepare($query);
	$stmt->execute([$shapeID]);
	$results = $stmt->fetchAll();

	return encodeResult($results);
}

function getShortName($longName) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT short_name FROM stop_names WHERE long_name = ?");
	$stmt->execute([$longName]);
	$shortName = $stmt->fetch();
	return $shortName["short_name"];
}

function getSchedule($shortName, $trips) {
	global $pdo;
	$in = str_repeat('?,', count($trips) - 1) . '?';
	array_unshift($trips, $shortName);
	$stmt = $pdo->prepare("SELECT trip_id, arrival_time, departure_time FROM stops WHERE stop_name_short = ? AND trip_id IN ($in)");
	$stmt->execute($trips);
	$results = $stmt->fetchAll();
	return $results;
}

function getRouteId($tripId) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT route_id FROM trips WHERE trip_id= ?");
	$stmt->execute([$tripId]);
	$shortName = $stmt->fetch();
	return $shortName["route_id"];
}

function encodeResult($results) {
	$out = array();
	for ($i = 0; $i < count($results); $i++) {
		$sequence = intval($results[$i]["sequence"]);
		$latitude = $results[$i]["latitude"];
		$longitude = $results[$i]["longitude"];
		$out[$sequence]["lat"] = $latitude;
		$out[$sequence]["lng"] = $longitude;
	}
	return $out;
}

function encodeResults($results) {
	$out = array();
	for ($i = 0; $i < count($results); $i++) {
		$shapeId = $results[$i]["shape_id"];
		$sequence = intval($results[$i]["sequence"]);
		$latitude = $results[$i]["latitude"];
		$longitude = $results[$i]["longitude"];
		$out[$shapeId][$sequence]["lat"] = $latitude;
		$out[$shapeId][$sequence]["lng"] = $longitude;
	}
	return $out;
}
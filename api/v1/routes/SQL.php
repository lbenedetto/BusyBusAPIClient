<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//$host = "localhost";
//$host = "127.0.0.1";
$host = "localhost";
$db = "u800402696_busyb";
$user = "u800402696_group";
$pass = file_get_contents( __DIR__ .'/pw.txt');
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
	$in  = str_repeat('?,', count($shapeIDs) - 1) . '?';
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
	$query = "SELECT * FROM shapes WHERE shape_id = ?";
	$stmt = $pdo->prepare($query);
	$stmt->execute([$shapeID]);
	$results = $stmt->fetchAll();

	return encodeResults($results);
}

function encodeResults($results){
	$out = array();
	for ($i = 0; $i < count($results); $i++) {
		$shapeId = $results[$i]["shape_id"];
		$sequence = $results[$i]["sequence"];
		$latitude = $results[$i]["latitude"];
		$longitude = $results[$i]["longitude"];
		$out[$shapeId][$sequence]["latitude"] = $latitude;
		$out[$shapeId][$sequence]["longitude"] = $longitude;
	}
	return $out;
}
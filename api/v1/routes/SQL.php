<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//$host = "localhost";
//$host = "127.0.0.1";
$host = "localhost";
$db = "u800402696_busyb";
$user = "u800402696_group";
$pass = "Group3";
//$pass = file_get_contents( __DIR__ .'/pw.txt');
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
}

function getShapeByTripId($tripId) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT DISTINCT shape_id FROM trips WHERE trip_id = ?");
	$stmt->execute([$tripId]);
	$shapeIDs = $stmt->fetchAll();

	//https://stackoverflow.com/questions/920353/can-i-bind-an-array-to-an-in-condition
	$params = array_combine(
		array_map(
		// construct param name according to array index
			function ($v) {return ":name_{$v}";},
			// get values of users
			array_keys($shapeIDs)
		),
		$shapeIDs
	);
	echo $params;
	echo implode(",", array_keys($params));
//	$query = "SELECT * FROM shapes WHERE shape_id IN ( " . implode(",", array_keys($params)) . " )";
//	$stmt = $pdo->prepare($query);
//	$stmt->execute($params);
//	$results = $stmt->fetchAll();

	return encodeResults($params);
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
<?php
include "../SQL.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	echo json_encode(getShapeByTripId($_REQUEST["tripId"]));
}
<?php
include "../../SQL.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	echo json_encode(getShapeByRoute($_REQUEST["id"]), JSON_NUMERIC_CHECK);
}
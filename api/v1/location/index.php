<?php
include '../sharedMethods.php';
require_once '../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
use transit_realtime\FeedMessage;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$feedMessage = getFeedMessage();
	$route = $_REQUEST["route"];
	$allData = getPositions($feedMessage, $route);
	echo json_encode($allData);
}

function getPositions($feedMessage, $route) {
	/** @var \transit_realtime\FeedMessage $feedMessage */
	/** @var \transit_realtime\FeedEntity $feedEntity */
	$allData = array();
	$i = 0;
	foreach ($feedMessage->getEntityList() as $feedEntity) {
		if ($feedEntity->hasVehicle()) {
			$data = getVehiclePositionData($feedEntity);
			if ($data["trip"]["routeId"] == $route)
				$allData[$i++] = $data;
		}
	}
	return $allData;
}

function getFeedMessage(){
	$data = file_get_contents("http://205.143.55.253:8250/Vehicle/VehiclePositions.pb");
	$feedMessage = new FeedMessage();
	$feedMessage->parse($data);
	return $feedMessage;
}
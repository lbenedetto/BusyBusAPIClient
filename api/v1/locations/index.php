<?php
include '../sharedMethods.php';
require_once '../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
use transit_realtime\FeedMessage;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$feedMessage = getFeedMessage();
	$routes = $_REQUEST["routes"];
	$allData = getPositions($feedMessage);
	echo json_encode($allData);
}

function getAllPositions($feedMessage) {
	/** @var \transit_realtime\FeedMessage $feedMessage */
	/** @var \transit_realtime\FeedEntity $feedEntity */
	$allData = array();
	$i = 0;
	foreach ($feedMessage->getEntityList() as $feedEntity) {
		if ($feedEntity->hasVehicle()) {
			$allData[$i++] = getVehiclePositionData($feedEntity);
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
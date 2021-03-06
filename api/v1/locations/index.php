<?php
include "../cache.php";
include '../sharedMethods.php';
require_once '../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
use transit_realtime\FeedMessage;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $feedMessage = getFeedMessage();
    $routes = $_REQUEST["routes"];
    $allData = getPositions($feedMessage, $routes);
    echo json_encode($allData);
}

function getPositions($feedMessage, $routes) {
    /** @var \transit_realtime\FeedMessage $feedMessage */
    /** @var \transit_realtime\FeedEntity $feedEntity */
    $allData = array();
    $i = 0;
    foreach ($feedMessage->getEntityList() as $feedEntity) {
        if ($feedEntity->hasVehicle()) {
            $data = getVehiclePositionData($feedEntity);
            if (in_array($data["trip"]["routeId"], $routes)) {
                $allData[$i++] = $data;
            }
        }
    }
    return $allData;
}

function getFeedMessage(){
	$data = getLiveData("http://205.143.55.253:8250/Vehicle/VehiclePositions.pb", "VehiclePositions.pb");
    $feedMessage = new FeedMessage();
    $feedMessage->parse($data);
    return $feedMessage;
}
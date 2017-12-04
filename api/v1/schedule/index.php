<?php
include "../SQL.php";
include "../sharedMethods.php";
require_once '../../vendor/autoload.php';

use transit_realtime\FeedMessage;

$trips = $_REQUEST["trips"];
$longName = $_REQUEST["name"];
$shortName = getShortName($longName);
$schedule = getSchedule($shortName, $trips);
$updates = getTripUpdates(getFeedMessage(), $trips);
$data = array();
foreach ($schedule as $scheduledStop) {
	$trip = $scheduledStop["trip_id"];
	if (in_array($trip, $trips)) {
		$data[$trip] = array();
		$data[$trip]["arrival"] = $scheduledStop["arrival_time"];
		$data[$trip]["departure"] = $scheduledStop["departure_time"];
		$data[$trip]["delay"] = 0;
		if (array_key_exists($trip, $updates)) {
			$data[$trip]["routeId"] = $updates[$trip]["trip"]["routeId"];
			if (array_key_exists("stopTimeUpdateList", $updates[$trip])) {
				$updateList = $updates[$trip]["stopTimeUpdateList"];
				foreach ($updateList as $update) {
					if ($update["stopId"] == $shortName) {
						if(array_key_exists("departure", $update)) {
							$data[$trip]["delay"] = $update["departure"]["delay"];
						}
					}
				}
			}
		}else{
			//TODO: Get route_id from database,
		}
	}
}
echo json_encode($data);

function getTripUpdates($feedMessage, $trips) {
	/** @var \transit_realtime\FeedMessage $feedMessage */
	/** @var \transit_realtime\FeedEntity $feedEntity */
	$allData = array();
	foreach ($feedMessage->getEntityList() as $feedEntity) {
		$id = $feedEntity->getId();
		if (in_array($id, $trips)) {
			$allData[$id] = getTripUpdateData($feedEntity);
		}
	}
	return $allData;
}

function getFeedMessage() {
	$data = file_get_contents("http://205.143.55.253:8250/TripUpdate/TripUpdates.pb");
	$feedMessage = new FeedMessage();
	$feedMessage->parse($data);
	return $feedMessage;
}

function getTripUpdateData($feedEntity) {
	/** @var \transit_realtime\FeedEntity $feedEntity */
	/** @var \transit_realtime\TripUpdate $tripUpdate */
	$tripUpdateData = array();
	if ($feedEntity->hasTripUpdate()) {
		$tripUpdate = $feedEntity->getTripUpdate();
		$tripUpdateData["timestamp"] = $tripUpdate->getTimestamp();
		$tripUpdateData["delay"] = $tripUpdate->getDelay();
		$tripUpdateData["trip"] = getTripDescriptorData($tripUpdate);
		$tripUpdateData["stopTimeUpdateList"] = getStopTimeUpdateListData($tripUpdate);
	}
	return $tripUpdateData;
}

function getStopTimeUpdateListData($tripUpdate) {
	/** @var \transit_realtime\TripUpdate $tripUpdate */
	/** @var \transit_realtime\TripUpdate\StopTimeUpdate $stopTimeUpdate */
	global $constants;
	$stopTimeUpdateListData = array();
	if ($tripUpdate->hasStopTimeUpdate()) {
		$stopTimeUpdateList = $tripUpdate->getStopTimeUpdateList();
		for ($i = 0; $i < sizeof($stopTimeUpdateList); $i++) {
			$stopTimeUpdate = $stopTimeUpdateList[$i];
			if ($stopTimeUpdate->hasStopSequence()) {
				$stopTimeUpdateListData[$i]["stopSeqeuence"] = $stopTimeUpdate->getStopSequence();
			}
			if ($stopTimeUpdate->hasStopId()) {
				$stopTimeUpdateListData[$i]["stopId"] = $stopTimeUpdate->getStopId();
			}
			if ($stopTimeUpdate->hasArrival()) {
				$stopTimeEvent = $stopTimeUpdate->getArrival();
				$stopTimeUpdateListData[$i]["arrival"] = getStopTimeEvent($stopTimeEvent);
			}
			if ($stopTimeUpdate->hasDeparture()) {
				$stopTimeEvent = $stopTimeUpdate->getDeparture();
				$stopTimeUpdateListData[$i]["departure"] = getStopTimeEvent($stopTimeEvent);
			}
			if ($stopTimeUpdate->hasScheduleRelationship()) {
				$rel = $constants["TripUpdateScheduleRelationship"][$stopTimeUpdate->getScheduleRelationship()];
				$stopTimeUpdateListData[$i]["scheduleRelationship"] = $rel;
			}
		}
	}
	return $stopTimeUpdateListData;
}

function getStopTimeEvent($stopTimeEvent) {
	/** @var \transit_realtime\TripUpdate\StopTimeEvent $stopTimeEvent */
	$stopTimeEventData = array();
	$stopTimeEventData["delay"] = $stopTimeEvent->getDelay();
	$stopTimeEventData["time"] = $stopTimeEvent->getTime();
	$stopTimeEventData["uncertainty"] = $stopTimeEvent->getUncertainty();
	return $stopTimeEventData;
}
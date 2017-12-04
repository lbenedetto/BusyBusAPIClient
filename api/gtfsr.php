<?php
//https://groups.google.com/forum/#!topic/gtfs-realtime/umglqNtuJ_c
//https://github.com/google/gtfs-realtime-bindings-php
//Documentation: https://hastebin.com/loyoxepaza.java
//Documentation2: https://hastebin.com/vosabugepi.java
require_once 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use transit_realtime\FeedMessage;

$constants = loadConstants();
$data = file_get_contents("http://205.143.55.253:8250/GTFS-RealTime/TrapezeRealTimeFeed.pb");
$feedMessage = new FeedMessage();
$feedMessage->parse($data);
$allData = getAllData($feedMessage);
echo json_encode($allData);

//The following code retrieves all data available from TrapezeRealTimeFeed.pb and encodes it in json
function getAllData($feedMessage) {
	/** @var \transit_realtime\FeedMessage $feedMessage */
	/** @var \transit_realtime\FeedEntity $feedEntity */
	$allData = array();
	$allData["header"] = getHeaderData($feedMessage);
	foreach ($feedMessage->getEntityList() as $feedEntity) {
		$id = $feedEntity->getId();
		$entityData["isDeleted"] = $feedEntity->getIsDeleted();
		$entityData["tripUpdate"] = getTripUpdateData($feedEntity);
		$entityData["vehiclePosition"] = getVehiclePositionData($feedEntity);

		$allData["entities"][$id] = $entityData;
	}
	return $allData;
}

function getHeaderData($feedMessage) {
	/** @var \transit_realtime\FeedHeader $header */
	/** @var \transit_realtime\FeedMessage $feedMessage */
	global $constants;
	$header = $feedMessage->getHeader();
	$headerData = array();
	$headerData["gtfsRealtimeVersion"] = $header->getGtfsRealtimeVersion();
	$headerData["incrementality"] = $constants["Incrementality"][$header->getIncrementality()];
	$headerData["timestamp"] = $header->getTimestamp();
	return $headerData;
}

//<editor-fold desc="TripUpdate Methods">
function getTripUpdateData($feedEntity) {
	/** @var \transit_realtime\FeedEntity $feedEntity */
	/** @var \transit_realtime\TripUpdate $tripUpdate */
	$tripUpdateData = array();
	if ($feedEntity->hasTripUpdate()) {
		$tripUpdate = $feedEntity->getTripUpdate();
		$tripUpdateData["timestamp"] = $tripUpdate->getTimestamp();
		$tripUpdateData["delay"] = $tripUpdate->getDelay();
		$tripUpdateData["trip"] = getTripDescriptorData($tripUpdate);
		$tripUpdateData["vehicle"] = getVehicleDescriptorData($tripUpdate);
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
			if($stopTimeUpdate->hasStopSequence()){
				$stopTimeUpdateListData[$i]["stopSeqeuence"] = $stopTimeUpdate->getStopSequence();
			}
			if($stopTimeUpdate->hasStopId()){
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

//</editor-fold>

//<editor-fold desc="VehiclePosition Methods">
function getVehiclePositionData($feedEntity) {
	/** @var $feedEntity        \transit_realtime\FeedEntity */
	/** @var $vehiclePosition   \transit_realtime\VehiclePosition */
	global $constants;
	$vehiclePositionData = array();
	if ($feedEntity->hasVehicle()) {
		$vehiclePosition = $feedEntity->getVehicle();
		$vehiclePositionData["currentStopSequence"] = $vehiclePosition->getCurrentStopSequence();
		$vehiclePositionData["stopId"] = $vehiclePosition->getStopId();
		$vehiclePositionData["currentStatus"] = $constants["VehicleStopStatus"][$vehiclePosition->getCurrentStatus()];
		$vehiclePositionData["timestamp"] = $vehiclePosition->getTimestamp();
		$vehiclePositionData["congestionLevel"] = $constants["CongestionLevel"][$vehiclePosition->getCongestionLevel()];
		$vehiclePositionData["occupancyStatus"] = $constants["OccupancyStatus"][$vehiclePosition->getOccupancyStatus()];
		$vehiclePositionData["position"] = getPositionData($vehiclePosition);
		$vehiclePositionData["trip"] = getTripDescriptorData($vehiclePosition);
		$vehiclePositionData["vehicle"] = getVehicleDescriptorData($vehiclePosition);
	}
	return $vehiclePositionData;
}

function getPositionData($vehiclePosition) {
	/** @var \transit_realtime\VehiclePosition $vehiclePosition */
	/** @var \transit_realtime\Position $position */
	$positionData = array();
	if ($vehiclePosition->hasPosition()) {
		$position = $vehiclePosition->getPosition();
		$positionData["latitude"] = $position->getLatitude();
		$positionData["longitude"] = $position->getLongitude();
		$positionData["bearing"] = $position->getBearing();
		$positionData["odometer"] = $position->getOdometer();
		$positionData["speed"] = $position->getSpeed();
	}
	return $positionData;
}

//Note that $vehiclePosition might actually be TripUpdate or EntitySelector
function getTripDescriptorData($vehiclePosition) {
	/** @var \transit_realtime\VehiclePosition $vehiclePosition */
	/** @var \transit_realtime\TripDescriptor $tripDescriptor */
	global $constants;
	$tripDescriptorData = array();
	if ($vehiclePosition->hasTrip()) {
		$tripDescriptor = $vehiclePosition->getTrip();
		$tripDescriptorData["tripId"] = $tripDescriptor->getTripId();
		$tripDescriptorData["routeId"] = $tripDescriptor->getRouteId();
		$tripDescriptorData["directionId"] = $tripDescriptor->getDirectionId();
		$tripDescriptorData["startTime"] = $tripDescriptor->getStartTime();
		$tripDescriptorData["startDate"] = $tripDescriptor->getStartDate();
		$tripDescriptorData["scheduleRelationship"] = $constants["TripDescriptorScheduleRelationship"][$tripDescriptor->getScheduleRelationship()];
	}
	return $tripDescriptorData;
}

//Note that $vehiclePosition might actually be TripUpdate
function getVehicleDescriptorData($vehiclePosition) {
	/** @var \transit_realtime\VehiclePosition $vehiclePosition */
	/** @var \transit_realtime\VehicleDescriptor $vehicleDescriptor */
	$vehicleDescriptorData = array();
	if ($vehiclePosition->hasVehicle()) {
		$vehicleDescriptor = $vehiclePosition->getVehicle();
		$vehicleDescriptorData["id"] = $vehicleDescriptor->getId();
		$vehicleDescriptorData["label"] = $vehicleDescriptor->getLabel();
		$vehicleDescriptorData["licensePlate"] = $vehicleDescriptor->getLicensePlate();
	}
	return $vehicleDescriptorData;
}

//</editor-fold>

function loadConstants() {
	$constants = array();
	$constants["Incrementality"][0] = "FULL_DATASET";
	$constants["Incrementality"][1] = "DIFFERENTIAL";
	$constants["Incrementality"][null] = "NOT_SPECIFIED";
	$constants["TripUpdateScheduleRelationship"][0] = "SCHEDULED";
	$constants["TripUpdateScheduleRelationship"][1] = "SKIPPED";
	$constants["TripUpdateScheduleRelationship"][2] = "NO_DATA";
	$constants["TripUpdateScheduleRelationship"][null] = "NOT_SPECIFIED";
	$constants["TripDescriptorScheduleRelationship"][0] = "SCHEDULED";
	$constants["TripDescriptorScheduleRelationship"][1] = "ADDED";
	$constants["TripDescriptorScheduleRelationship"][2] = "UNSCHEDULED";
	$constants["TripDescriptorScheduleRelationship"][3] = "CANCELED";
	$constants["TripDescriptorScheduleRelationship"][null] = "NOT_SPECIFIED";
	$constants["VehicleStopStatus"][0] = "INCOMING_AT";
	$constants["VehicleStopStatus"][1] = "STOPPED_AT";
	$constants["VehicleStopStatus"][2] = "IN_TRANSIT_TO";
	$constants["VehicleStopStatus"][null] = "NOT_SPECIFIED";
	$constants["CongestionLevel"][0] = "UNKNOWN_CONGESTION_LEVEL";
	$constants["CongestionLevel"][1] = "RUNNING_SMOOTHLY";
	$constants["CongestionLevel"][2] = "STOP_AND_GO";
	$constants["CongestionLevel"][3] = "CONGESTION";
	$constants["CongestionLevel"][4] = "SEVERE_CONGESTION";
	$constants["CongestionLevel"][null] = "NOT_SPECIFIED";
	$constants["OccupancyStatus"][0] = "EMPTY0";
	$constants["OccupancyStatus"][1] = "MANY_SEATS_AVAILABLE";
	$constants["OccupancyStatus"][2] = "FEW_SEATS_AVAILABLE";
	$constants["OccupancyStatus"][3] = "STANDING_ROOM_ONLY";
	$constants["OccupancyStatus"][4] = "CRUSHED_STANDING_ROOM_ONLY";
	$constants["OccupancyStatus"][5] = "FULL";
	$constants["OccupancyStatus"][6] = "NOT_ACCEPTING_PASSENGERS";
	$constants["OccupancyStatus"][null] = "NOT_SPECIFIED";
	$constants["Cause"][1] = "UNKNOWN_CAUSE";
	$constants["Cause"][2] = "OTHER_CAUSE";
	$constants["Cause"][3] = "TECHNICAL_PROBLEM";
	$constants["Cause"][4] = "STRIKE";
	$constants["Cause"][5] = "DEMONSTRATION";
	$constants["Cause"][6] = "ACCIDENT";
	$constants["Cause"][7] = "HOLIDAY";
	$constants["Cause"][8] = "WEATHER";
	$constants["Cause"][9] = "MAINTENANCE";
	$constants["Cause"][10] = "CONSTRUCTION";
	$constants["Cause"][11] = "POLICE_ACTIVITY";
	$constants["Cause"][12] = "MEDICAL_EMERGENCY";
	$constants["Cause"][null] = "NOT_SPECIFIED";
	$constants["Effect"][1] = "NO_SERVICE";
	$constants["Effect"][2] = "REDUCED_SERVICE";
	$constants["Effect"][3] = "SIGNIFICANT_DELAYS";
	$constants["Effect"][4] = "DETOUR";
	$constants["Effect"][5] = "ADDITIONAL_SERVICE";
	$constants["Effect"][6] = "MODIFIED_SERVICE";
	$constants["Effect"][7] = "OTHER_EFFECT";
	$constants["Effect"][8] = "UNKNOWN_EFFECT";
	$constants["Effect"][9] = "STOP_MOVED";
	$constants["Effect"][null] = "NOT_SPECIFIED";
	return $constants;
}

<?php
//https://groups.google.com/forum/#!topic/gtfs-realtime/umglqNtuJ_c
//https://github.com/google/gtfs-realtime-bindings-php
//Documentation: https://hastebin.com/umulojisow.php
require_once 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use transit_realtime\FeedMessage;

$constants = loadConstants();
$data = file_get_contents("http://205.143.55.253:8250/GTFS-RealTime/TrapezeRealTimeFeed.pb");
$feedMessage = new FeedMessage();
$feedMessage->parse($data);
$allData = array();
//The following code retrieves all data available from TrapezeRealTimeFeed.pb and encodes it in json

//FeedMessage > FeedHeader
/** @var \transit_realtime\FeedHeader $header */
$header = $feedMessage->getHeader();
$allData["header"]["gtfsRealtimeVersion"] = $header->getGtfsRealtimeVersion();
$allData["header"]["incrementality"] = $constants["Incrementality"][$header->getIncrementality()];
$allData["header"]["timestamp"] = $header->getTimestamp();

//FeedMessage > FeedEntity
foreach ($feedMessage->getEntityList() as $feedEntity) {
	/** @var \transit_realtime\FeedEntity $feedEntity */
	$id = $feedEntity->getId();
	$entityData["isDeleted"] = $feedEntity->getIsDeleted();
	if ($feedEntity->hasTripUpdate()) {
		/** @var \transit_realtime\TripUpdate $tripUpdate */
		$tripUpdate = $feedEntity->getTripUpdate();
		$tripUpdateData["timestamp"] = $tripUpdate->getTimestamp();
		$tripUpdateData["delay"] = $tripUpdate->getDelay();

		$entityData["tripUpdate"] = $tripUpdateData;
	}
	if ($feedEntity->hasVehicle()) {
		/** @var \transit_realtime\VehiclePosition $vehiclePosition */
		$vehiclePosition = $feedEntity->getVehicle();
		$vehiclePositionData["currentStopSequence"] = $vehiclePosition->getCurrentStopSequence();
		$vehiclePositionData["stopId"] = $vehiclePosition->getStopId();
		$vehiclePositionData["currentStatus"] = $constants["VehicleStopStatus"][$vehiclePosition->getCurrentStatus()];
		$vehiclePositionData["timestamp"] = $vehiclePosition->getTimestamp();
		$vehiclePositionData["congestionLevel"] = $vehiclePosition->getCongestionLevel();
		$vehiclePositionData["occupancyStatus"] = $vehiclePosition->getOccupancyStatus();

		if ($vehiclePosition->hasPosition()) {
			/** @var \transit_realtime\Position $position */
			$position = $vehiclePosition->getPosition();
			$positionData["latitude"] = $position->getLatitude();
			$positionData["longitude"] = $position->getLongitude();
			$positionData["bearing"] = $position->getBearing();
			$positionData["odometer"] = $position->getOdometer();
			$positionData["speed"] = $position->getSpeed();
			$vehiclePositionData["position"] = $positionData;
		}

//		if($vehiclePosition->hasTrip()){
//			echo "Trip :: ";
//		}
//		if($vehiclePosition->hasVehicle()){
//			echo "Vehicle :: ";
//		}
		$entityData["vehiclePosition"] = $vehiclePositionData;
	}
	$allData["entities"][$id] = $entityData;
}
echo json_encode($allData);
//

////		if($vehiclePosition->hasCurrentStopSequence()){
////			echo "CurrentStopSequence :: ";
////		}
////		if($vehiclePosition->hasStopId()){
////			echo "StopId :: ";
////		}
////		if($vehiclePosition->hasCurrentStatus()){
////			echo "CurrentStatus :: ";
////		}
////		if($vehiclePosition->hasTimeStamp()){
////			echo "TimeStamp :: ";
////		}
////		if($vehiclePosition->hasCongestionLevel()){
////			echo "CongestionLevel :: ";
////		}
////		if($vehiclePosition->hasOccupancyStatus()){
////			echo "OccupancyStatus :: ";
////		}
//
//	}
////	if($feedEntity->hasAlert()){
////		echo "Alert :: ";
////	}
//	echo json_encode();
//}

function loadConstants() {
	$constants = array();
	$constants["Incrementality"][0] = "FULL_DATASET";
	$constants["Incrementality"][1] = "DIFFERENTIAL";
	$constants["TripUpdateScheduleRelationship"][0] = "SCHEDULED";
	$constants["TripUpdateScheduleRelationship"][1] = "SKIPPED";
	$constants["TripUpdateScheduleRelationship"][2] = "NO_DATA";
	$constants["TripDescriptorScheduleRelationship"][0] = "SCHEDULED";
	$constants["TripDescriptorScheduleRelationship"][1] = "ADDED";
	$constants["TripDescriptorScheduleRelationship"][2] = "UNSCHEDULED";
	$constants["TripDescriptorScheduleRelationship"][3] = "CANCELED";
	$constants["VehicleStopStatus"][0] = "INCOMING_AT";
	$constants["VehicleStopStatus"][1] = "STOPPED_AT";
	$constants["VehicleStopStatus"][2] = "IN_TRANSIT_TO";
	$constants["CongestionLevel"][0] = "UNKNOWN_CONGESTION_LEVEL";
	$constants["CongestionLevel"][1] = "RUNNING_SMOOTHLY";
	$constants["CongestionLevel"][2] = "STOP_AND_GO";
	$constants["CongestionLevel"][3] = "CONGESTION";
	$constants["CongestionLevel"][4] = "SEVERE_CONGESTION";
	$constants["OccupancyStatus"][0] = "EMPTY0";
	$constants["OccupancyStatus"][1] = "MANY_SEATS_AVAILABLE";
	$constants["OccupancyStatus"][2] = "FEW_SEATS_AVAILABLE";
	$constants["OccupancyStatus"][3] = "STANDING_ROOM_ONLY";
	$constants["OccupancyStatus"][4] = "CRUSHED_STANDING_ROOM_ONLY";
	$constants["OccupancyStatus"][5] = "FULL";
	$constants["OccupancyStatus"][6] = "NOT_ACCEPTING_PASSENGERS";
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
	$constants["Effect"][1] = "NO_SERVICE";
	$constants["Effect"][2] = "REDUCED_SERVICE";
	$constants["Effect"][3] = "SIGNIFICANT_DELAYS";
	$constants["Effect"][4] = "DETOUR";
	$constants["Effect"][5] = "ADDITIONAL_SERVICE";
	$constants["Effect"][6] = "MODIFIED_SERVICE";
	$constants["Effect"][7] = "OTHER_EFFECT";
	$constants["Effect"][8] = "UNKNOWN_EFFECT";
	$constants["Effect"][9] = "STOP_MOVED";
	return $constants;
}

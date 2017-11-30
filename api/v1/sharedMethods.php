<?php

function getVehiclePositionData($feedEntity) {
	/** @var $feedEntity        \transit_realtime\FeedEntity */
	/** @var $vehiclePosition   \transit_realtime\VehiclePosition */
	$vehiclePositionData = array();
	$vehiclePosition = $feedEntity->getVehicle();
	$vehiclePositionData["position"] = getPositionData($vehiclePosition);
	$vehiclePositionData["trip"] = getTripDescriptorData($vehiclePosition);
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
	}
	return $positionData;
}

function getTripDescriptorData($vehiclePosition) {
	/** @var \transit_realtime\VehiclePosition $vehiclePosition */
	/** @var \transit_realtime\TripDescriptor $tripDescriptor */
	$tripDescriptorData = array();
	if ($vehiclePosition->hasTrip()) {
		$tripDescriptor = $vehiclePosition->getTrip();
		$tripDescriptorData["routeId"] = $tripDescriptor->getRouteId();
		$tripDescriptorData["tripId"] = $tripDescriptor->getTripId();
	}
	return $tripDescriptorData;
}
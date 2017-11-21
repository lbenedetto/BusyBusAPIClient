<?php
//https://github.com/google/gtfs-realtime-bindings-php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;

$data = file_get_contents("http://205.143.55.253:8250/GTFS-RealTime/TrapezeRealTimeFeed.pb");
$feed = new FeedMessage();
$feed->parse($data);
try {
	foreach ($feed->getEntityList() as $entity) {
		if ($entity->hasTripUpdate()) {
			error_log("trip: " . $entity->getId());
			echo "trip: " . $entity->getTripUpdate() . "\n";
		}
	}
}catch (Exception $e){
	echo $e;
}
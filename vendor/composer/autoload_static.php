<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite5b390e0341d50214cd432cb4dcecf83
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Symfony\\Component\\Console\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/console',
            ),
        ),
        'D' => 
        array (
            'DrSlump' => 
            array (
                0 => __DIR__ . '/..' . '/centraldesktop/protobuf-php/library',
            ),
        ),
    );

    public static $classMap = array (
        'transit_realtime\\Alert' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\Alert\\Cause' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\Alert\\Effect' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\EntitySelector' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\FeedEntity' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\FeedHeader' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\FeedHeader\\Incrementality' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\FeedMessage' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\Position' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TimeRange' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TranslatedString' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TranslatedString\\Translation' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripDescriptor' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripDescriptor\\ScheduleRelationship' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripUpdate' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripUpdate\\StopTimeEvent' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripUpdate\\StopTimeUpdate' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\TripUpdate\\StopTimeUpdate\\ScheduleRelationship' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\VehicleDescriptor' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\VehiclePosition' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\VehiclePosition\\CongestionLevel' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\VehiclePosition\\OccupancyStatus' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
        'transit_realtime\\VehiclePosition\\VehicleStopStatus' => __DIR__ . '/..' . '/google/gtfs-realtime-bindings/src/gtfs-realtime.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInite5b390e0341d50214cd432cb4dcecf83::$prefixesPsr0;
            $loader->classMap = ComposerStaticInite5b390e0341d50214cd432cb4dcecf83::$classMap;

        }, null, ClassLoader::class);
    }
}

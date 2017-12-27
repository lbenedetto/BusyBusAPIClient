<?php
namespace transit_realtime {
	class FeedMessage extends \DrSlump\Protobuf\Message {
		//@return transit_realtime\FeedHeader
		public function getHeader();
		//@return transit_realtime\FeedEntity[]
		public function getEntityList();
	}
}
namespace transit_realtime {
	class FeedHeader extends \DrSlump\Protobuf\Message {
		//@return string
		public function getGtfsRealtimeVersion();
		//@return int - transit_realtime\FeedHeader\Incrementality
		public function getIncrementality();
		//@return int
		public function getTimestamp();
	}
}
namespace transit_realtime {
	class FeedEntity extends \DrSlump\Protobuf\Message {
		//@return string
		public function getId();
		//@return boolean
		public function getIsDeleted();
		//@return transit_realtime\TripUpdate
		public function getTripUpdate();
		//@return transit_realtime\VehiclePosition
		public function getVehicle();
		//@return transit_realtime\Alert
		public function getAlert();
	}
}
namespace transit_realtime {
	class TripUpdate extends \DrSlump\Protobuf\Message {
		//@return transit_realtime\TripDescriptor
		public function getTrip();
		//@return transit_realtime\VehicleDescriptor
		public function getVehicle();
		//@return transit_realtime\TripUpdate\StopTimeUpdate[]
		public function getStopTimeUpdateList();
		//@return int
		public function getTimestamp();
		//@return int
		public function getDelay();
	}
}
namespace transit_realtime\TripUpdate {
	class StopTimeEvent extends \DrSlump\Protobuf\Message {
		//@return int
		public function getDelay();
		//@return int
		public function getTime();
		//@return int
		public function getUncertainty();
	}
}
namespace transit_realtime\TripUpdate {
	//@return int
	public function getStopSequence();
	//@return string
	public function getStopId();
	//@return transit_realtime\TripUpdate\StopTimeEvent
	public function getArrival();
	//@return transit_realtime\TripUpdate\StopTimeEvent
	public function getDeparture();
	//@return int - transit_realtime\TripUpdate\StopTimeUpdate\ScheduleRelationship
	public function getScheduleRelationship();
}
namespace transit_realtime {
	class VehiclePosition extends \DrSlump\Protobuf\Message {
		//@return transit_realtime\TripDescriptor
		public function getTrip();
		//@return transit_realtime\VehicleDescriptor
		public function getVehicle();
		//@return transit_realtime\Position
		public function getPosition();
		//@return int
		public function getCurrentStopSequence();
		//@return string
		public function getStopId();
		//@return int - transit_realtime\VehiclePosition\VehicleStopStatus
		public function getCurrentStatus();
		//@return int
		public function getTimestamp();
		//@return int - transit_realtime\VehiclePosition\CongestionLevel
		public function getCongestionLevel();
		//@return int - transit_realtime\VehiclePosition\OccupancyStatus
		public function getOccupancyStatus();
	}
}
namespace transit_realtime {
	class Alert extends \DrSlump\Protobuf\Message {
		//@return transit_realtime\TimeRange[]
		public function getActivePeriod($idx = null);
		//@return transit_realtime\TimeRange[]
		public function getActivePeriodList();
		//@return transit_realtime\EntitySelector[]
		public function getInformedEntity($idx = null);
		//@return transit_realtime\EntitySelector[]
		public function getInformedEntityList();
		//@return int - transit_realtime\Alert\Cause
		public function getCause();
		//@return int - transit_realtime\Alert\Effect
		public function getEffect();
		//@return transit_realtime\TranslatedString
		public function getUrl();
		//@return transit_realtime\TranslatedString
		public function getHeaderText();
		//@return transit_realtime\TranslatedString
		public function getDescriptionText();
	}
}
namespace transit_realtime {
	class TimeRange extends \DrSlump\Protobuf\Message {
		//@return int
		public function getStart();
		//@return int
		public function getEnd();
	}
}
namespace transit_realtime {
	class Position extends \DrSlump\Protobuf\Message {
		//@return float
		public function getLatitude();
		//@return float
		public function getLongitude();
		//@return float
		public function getBearing();
		//@return float
		public function getOdometer();
		//@return float
		public function getSpeed();
	}
}
namespace transit_realtime {
	class TripDescriptor extends \DrSlump\Protobuf\Message {
		//@return string
		public function getTripId();
		//@return string
		public function getRouteId();
		//@return int
		public function getDirectionId();
		//@return string
		public function getStartTime();
		//@return string
		public function getStartDate();
		//@return int - transit_realtime\TripDescriptor\ScheduleRelationship
		public function getScheduleRelationship();
	}
}
namespace transit_realtime {
	class VehicleDescriptor extends \DrSlump\Protobuf\Message {
		//@return string
		public function getId();
		//@return string
		public function getLabel();
		//@return string
		public function getLicensePlate();
	}
}
namespace transit_realtime\TripUpdate {
    class StopTimeUpdate extends \DrSlump\Protobuf\Message {
        // @return int 
        public function getStopSequence();
        // @return string 
        public function getStopId();
        // @return transit_realtime\TripUpdate\StopTimeEvent 
        public function getArrival();
        // @return transit_realtime\TripUpdate\StopTimeEvent 
        public function getDeparture();
        // @return int - transit_realtime\TripUpdate\StopTimeUpdate\ScheduleRelationship 
        public function getScheduleRelationship();
    }
}
namespace transit_realtime {
	class EntitySelector extends \DrSlump\Protobuf\Message {
		//@return string
		public function getAgencyId();
		//@return string
		public function getRouteId();
		//@return int
		public function getRouteType();
		//@return transit_realtime\TripDescriptor
		public function getTrip();
		//@return string
		public function getStopId();
	}
}
namespace transit_realtime {
	class TranslatedString extends \DrSlump\Protobuf\Message {
		//@return transit_realtime\TranslatedString\Translation[]
		public function getTranslation($idx = null)
		{;
			return $this->translation;
		}
		//@return transit_realtime\TranslatedString\Translation[]
		public function getTranslationList();
	}
}
namespace transit_realtime\TranslatedString {
	class Translation extends \DrSlump\Protobuf\Message {
		//@return string
		public function getText();
		//@return string
		public function getLanguage();
	}
}
namespace transit_realtime\FeedHeader {
	class Incrementality extends \DrSlump\Protobuf\Enum
	{
		const FULL_DATASET = 0;
		const DIFFERENTIAL = 1;
	}
}
namespace transit_realtime\TripUpdate\StopTimeUpdate {
	class ScheduleRelationship extends \DrSlump\Protobuf\Enum
	{
		const SCHEDULED = 0;
		const SKIPPED = 1;
		const NO_DATA = 2;
	}
}
namespace transit_realtime\VehiclePosition {
	class VehicleStopStatus extends \DrSlump\Protobuf\Enum
	{
		const INCOMING_AT = 0;
		const STOPPED_AT = 1;
		const IN_TRANSIT_TO = 2;
	}
}
namespace transit_realtime\VehiclePosition {
	class CongestionLevel extends \DrSlump\Protobuf\Enum
	{
		const UNKNOWN_CONGESTION_LEVEL = 0;
		const RUNNING_SMOOTHLY = 1;
		const STOP_AND_GO = 2;
		const CONGESTION = 3;
		const SEVERE_CONGESTION = 4;
	}
}
namespace transit_realtime\VehiclePosition {
	class OccupancyStatus extends \DrSlump\Protobuf\Enum
	{
		const EMPTY0 = 0;
		const MANY_SEATS_AVAILABLE = 1;
		const FEW_SEATS_AVAILABLE = 2;
		const STANDING_ROOM_ONLY = 3;
		const CRUSHED_STANDING_ROOM_ONLY = 4;
		const FULL = 5;
		const NOT_ACCEPTING_PASSENGERS = 6;
	}
}
namespace transit_realtime\Alert {
	class Cause extends \DrSlump\Protobuf\Enum
	{
		const UNKNOWN_CAUSE = 1;
		const OTHER_CAUSE = 2;
		const TECHNICAL_PROBLEM = 3;
		const STRIKE = 4;
		const DEMONSTRATION = 5;
		const ACCIDENT = 6;
		const HOLIDAY = 7;
		const WEATHER = 8;
		const MAINTENANCE = 9;
		const CONSTRUCTION = 10;
		const POLICE_ACTIVITY = 11;
		const MEDICAL_EMERGENCY = 12;
	}
}
namespace transit_realtime\Alert {
	class Effect extends \DrSlump\Protobuf\Enum
	{
		const NO_SERVICE = 1;
		const REDUCED_SERVICE = 2;
		const SIGNIFICANT_DELAYS = 3;
		const DETOUR = 4;
		const ADDITIONAL_SERVICE = 5;
		const MODIFIED_SERVICE = 6;
		const OTHER_EFFECT = 7;
		const UNKNOWN_EFFECT = 8;
		const STOP_MOVED = 9;
	}
}
namespace transit_realtime\TripDescriptor {
	class ScheduleRelationship extends \DrSlump\Protobuf\Enum
	{
		const SCHEDULED = 0;
		const ADDED = 1;
		const UNSCHEDULED = 2;
		const CANCELED = 3;
	}
}
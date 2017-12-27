<?php 
class FeedMessage {
    FeedHeader 
    FeedEntity
    FeedEntity[]
}
class FeedHeader {
    string #GtfsRealtimeVersion
    int - Incrementality 
    int #timestamp
}
class FeedEntity {
    string #id
    boolean #isDeleted
    TripUpdate 
    VehiclePosition 
    Alert 
}
class TripUpdate {
    TripDescriptor 
    VehicleDescriptor 
    StopTimeUpdate
    StopTimeUpdate[]
    int #timestamp
    int #delay
}
class VehiclePosition {
    TripDescriptor 
    VehicleDescriptor 
    Position 
    int #CurrentStopSequence
    string #StopId
    int - VehicleStopStatus 
    int #Timestamp
    int - CongestionLevel 
    int - OccupancyStatus 
}
class Alert {
    TimeRange
    TimeRange[]
    EntitySelector
    EntitySelector[]
    int - Cause 
    int - Effect 
    TranslatedString 
    TranslatedString 
    TranslatedString 
}
class TimeRange {
    int #Start
    int #End
}
class Position {
    float #Latitude
    float #Longitude
    float #Bearing
    float #Odometer
    float #Speed
}
class TripDescriptor {
    string #TripId
    string #RouteId
    int    #DirectionId
    string #StartTime
    string #StartDate
    int - TripDescriptorScheduleRelationship 
}
class VehicleDescriptor {
    string #Id
    string #Label
    string #LicensePlate
}
class EntitySelector {
    string #AgencyId
    string #RouteId
    int    #RouteType
    TripDescriptor 
    string #StopId
}
class TranslatedString {
    Translation
    Translation[]
}

class StopTimeEvent {
    int #Delay
    int #Time
    int #Uncertainty
}
class StopTimeUpdate {
    int #StopSequence
    string #StopId
    StopTimeEvent 
    StopTimeEvent 
    int - TripUpdateScheduleRelationship 
}

class Translation {
    string #Text
    string #Language
}

/** ENUMS **/
class Incrementality;
class TripUpdateScheduleRelationship;
class TripDescriptorScheduleRelationship;
class VehicleStopStatus;
class CongestionLevel;
class OccupancyStatus;
class Cause;
class Effect;
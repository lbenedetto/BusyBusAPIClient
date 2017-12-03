var map, latitude, longitude, markers = [], currentTripId, currentRouteId, locationMarker, locationError = false;

//TODO: Uncomment this before deploying
// if (location.protocol !== 'https:') {
// 	location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
// }
//Update bus locations every 10 seconds

var routeColors = {
	1:'#f3801f',
    2:'#e89612',
    20:'#daab09',
    21:'#c9bf03',
    22:'#b6d101',
    23:'#a1e102',
    24:'#8bed08',
    25:'#75f711',
    26:'#60fd1d',
    27:'#4bff2c',
    28:'#37fd3e',
    29:'#26f752',
    32:'#18ed68',
    33:'#0de17e',
    34:'#05d194',
    39:'#01bfa9',
    42:'#01abbd',
    43:'#0596d0',
    44:'#0c80e0',
    45:'#1769ed',
    60:'#2554f6',
    61:'#3640fc',
    62:'#492efe',
    66:'#5e1efd',
    68:'#7412f7',
    74:'#8a08ee',
    90:'#9f02e2',
    94:'#b401d3',
    95:'#c802c1',
    96:'#d908ad',
    97:'#e71297',
    98:'#f21e81',
    124:'#fa2e6b',
    165:'#fe4056',
    172:'#fe5442',
    173:'#fa692f'};


    var timeleft = 1;
    var downloadTimer = setInterval(function () {
        timeleft--;
        document.getElementById("Refresh").innerHTML = "Refresh in " + timeleft + " sec";
        if (timeleft <= 0) {
            timeleft = 10;
            getBuses();
            setInterval(downloadTimer);
        }
    }, 1000);
setInterval(showLocationMarker, 1500);

function getBuses() {
	console.log("update");
	var routes = getSelectedRoutes();
	$.get("./api/v1/locations/",
		{"routes[]": routes},
		function callback(response) {
			deleteMarkers();
			$(jQuery.parseJSON(response)).each(function showBus() {
				drawMarker(this["position"]["latitude"],
					this["position"]["longitude"],
					this["position"]["bearing"],
					this["trip"]["tripId"],
					this["trip"]["routeId"])
			});
		}
	);
}

function drawMarker(latitude, longitude, bearing, tripId, routeId) {
	var size = 30;
	var latLng = new google.maps.LatLng(latitude, longitude);
	var marker = new google.maps.Marker({
		position: latLng,
		icon: {
			url: "./icons/" + routeId + ".png",
            scaledSize: new google.maps.Size(size * 1.25, size)
		},
		map: map,
		tripId: tripId,
		routeId: routeId
	});
	markers.push(marker);
	google.maps.event.addListener(marker, 'click', markerClick);

	if (bearing !== null) {
		var bearingIcon = new google.maps.Marker({
			position: latLng,
			icon: {
				url: "./icons/direction" + bearing + ".png",
				scaledSize: new google.maps.Size(size * 1.25, size)
			},
			map: map,
			tripId: tripId,
			routeId: routeId
		});
		markers.push(bearingIcon);
		google.maps.event.addListener(bearingIcon, 'click', markerClick);

	}
}

function markerClick() {
	currentTripId = this.tripId;
	currentRouteId = this.routeId;
	$.get("./api/v1/routes/byTripId/?id=" + currentTripId, function callback(response) {
		var shapeJSON = JSON.parse(response);
		var shape = $.map(shapeJSON, function (el) {
			return el;
		});

		var flightPath = new google.maps.Polyline({
			path: shape,
			geodesic: true,
			strokeColor: routeColors[currentRouteId],
			strokeOpacity: 1.0,
			strokeWeight: 4
		});
		flightPath.setMap(map);
	});
}


function deleteMarkers() {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}
	markers = [];
}

function showLocationMarker() {
	getLocation(null);
	//if (locationMarker !== null) locationMarker.setMap(null);
	var latLng = new google.maps.LatLng(latitude, longitude);
	locationMarker = new google.maps.Marker({
		position: latLng,
		icon: {
			url: "./icons/bluedot.png",
			scaledSize: new google.maps.Size(20, 20)
		},
		map: map
	});
}

function getLocation(callback) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
			function setPosition(position) {
				latitude = position.coords.latitude;
				longitude = position.coords.longitude;
				if (position.coords.accuracy > 50) {
					document.getElementById("LowAcc").innerHTML = "GPS Accuracy: " + position.coords.accuracy + "m";
				}
				if(callback !== null) callback();
			},
			function error(msg) {
				if(!locationError){
					locationError=true;
					console.log("Could not get location");
					//alert("Could not get location");
				}
			},
			{
				maximumAge: 60,
				timeout: 5000,
				enableHighAccuracy: true
			});
	} else {
		//Spokane's coordinates
		latitude = 47.6588;
		longitude = -117.4260;
		if(callback !== null) callback();
	}

}
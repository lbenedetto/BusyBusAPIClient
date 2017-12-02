var map, latitude, longitude, markers = [], currentTripId;

// if (location.protocol !== 'https:') {
// 	location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
// }
//Update bus locations every 5 seconds
setInterval(getBuses, 10000);

function getBuses() {
	console.log("update");
	$.get("./api/v1/locations/", function callback(response) {
		deleteMarkers();
		$(jQuery.parseJSON(response)).each(function showBus() {
			drawMarker(this["position"]["latitude"],
				this["position"]["longitude"],
				'https://maps.google.com/mapfiles/kml/shapes/bus.png',
				this["trip"]["tripId"],
				this["trip"]["routeId"])
		});
	});
}
/*
Initially, all buses and routes are drawn
"" -> Backend -> "Every Shape"
Then, the user filters it down to only, say, Route 66
The backend can return a list of all the shapes to draw for that route
"?route=66" -> Backend -> "Couple of Shapes"
Then, a user should be able to click on a bus marker, and filter to only showing that tripID
"?tripID=23280" -> Backend -> "One Shape"
The backend can return a shape when given a tripID

A shape is
 */
function drawMarker(latitude, longitude, iconURL, tripId, routeId){
    var size = 20;
	var latLng = new google.maps.LatLng(latitude, longitude);
	var marker = new google.maps.Marker({
		position: latLng,
		icon: {
            url: iconURL,
            scaledSize: new google.maps.Size(size, size)
		},
		map: map,
		tripId: tripId,
		routeId: routeId
	});
	markers.push(marker);
	google.maps.event.addListener(marker, 'click', function(){
		currentTripId = this.tripId;
		$.get("./api/v1/routes/byTripId/?id="+currentTripId, function callback(response){
			var shapeJSON = JSON.parse(response);
			var shape = $.map(shapeJSON, function(el) { return el; });
			console.log(shape);
			var flightPath = new google.maps.Polyline({
				path: shape,
				geodesic: true,
				strokeColor: '#2d4ec6',
				strokeOpacity: 1.0,
				strokeWeight: 2
			});

			flightPath.setMap(map);
		});
	});
}

function getBus(route) {
	console.log("update");
	$.get("./api/v1/location/?route=" + route, function callback(response) {
		console.log(response);
		deleteMarkers();
		$(jQuery.parseJSON(response)).each(function showBus() {
			drawMarker(this["position"]["latitude"], this["position"]["longitude"], 'https://maps.google.com/mapfiles/kml/shapes/bus.png');
		});
	});
}


function deleteMarkers() {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}
	markers = [];
}

function getLocation() {
	getBuses();
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
			function setPosition(position) {
				latitude = position.coords.latitude;
				longitude = position.coords.longitude;
                if (position.coords.accuracy > 50) {
                    document.getElementById("LowAcc").innerHTML = "GPS Accuracy: " + position.coords.accuracy + "m";
				}
				showMap();
			},
			function error(msg) {
				alert('Please enable your GPS position future.');
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
		showMap();
	}
}
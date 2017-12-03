var map, latitude, longitude, markers = [], currentTripId;

//TODO: Uncomment this before deploying
// if (location.protocol !== 'https:') {
// 	location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
// }
//Update bus locations every 10 seconds
var timeleft = 10;
var downloadTimer = setInterval(function () {
	timeleft--;
	document.getElementById("Refresh").innerHTML = "Refresh in " + timeleft + " sec";
	if (timeleft <= 0) {
		timeleft = 10;
		getBuses();
		setInterval(downloadTimer);
	}
}, 1000);

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

function drawMarker(latitude, longitude, bearing, tripId, routeId){
    var size = 20;
	var latLng = new google.maps.LatLng(latitude, longitude);
	var marker = new google.maps.Marker({
		position: latLng,
		icon: {
            url: "./icons/" + routeId + ".png",
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
				strokeWeight: 3
			});
			flightPath.setMap(map);
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
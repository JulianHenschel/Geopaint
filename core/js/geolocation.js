//Source: http://www.bctx.info/wx

var geodata = new Object();
var geo_counter = 0;
var current_pos_marker;

function showPosition(position) {
	
	geodata["latitude"] = position.coords.latitude;
	geodata["longitude"] = position.coords.longitude;
	geodata["altitude"] = position.coords.altitude;
	geodata["accuracy"] = position.coords.accuracy;
	geodata["heading"] = position.coords.heading;
	geodata["speed"] = position.coords.speed;
	
	getCountryCityName(geodata["latitude"],geodata["longitude"]);
	
	if(geo_counter == 0) {
		jQT.goTo('#starting_options', 'slide'); 
	}

	geo_counter += 1;
}

function noPosition() {
	document.getElementById("nogeo").style.visibility = 'visible';
}

$(document).ready(function() {
	var geo;
	try {window.navgeo = !!(typeof navigator.geolocation != 'undefined');}catch(e){}
	
	if (window.navgeo) {
		geo = navigator.geolocation;		
	} else {
		try {window.gears = !!(typeof GearsFactory != 'undefined' || navigator.mimeTypes['application/x-googlegears'] || new ActiveXObject('Gears.Factory'));}catch(e){}
		if (window.gears) {
			geo = google.gears.factory.create('beta.geolocation');
		}
	}
	if (geo != "") {
		geo.watchPosition(showPosition, noPosition, {enableHighAccuracy:true,maximumAge:600000});
	} else {
		noPosition();
	}
});
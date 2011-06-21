var map, locationname, overlay;
var marker_list = [], polyline_list = [];
var bounds = new google.maps.LatLngBounds();
var geocoder = new google.maps.Geocoder();

/* 
------------------------------------------------------------------------------------------------------
load map
------------------------------------------------------------------------------------------------------
*/  
function loadMap() {

	setMapSize();
	
	var myLatlng = new google.maps.LatLng(geodata["latitude"],geodata["longitude"]);
	
	var map_options = {
		zoom: 2,
		center: myLatlng,
		mapTypeControl: false,
		//mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
		navigationControl: false,
		disableDoubleClickZoom: true,
		draggable: false,
		mapTypeId: google.maps.MapTypeId.HYBRID
	}
	
	// greyed out style for better visibility of polylines
	var styles = [{
		featureType: "all",
		elementType: "all",
		stylers: [
			{ saturation: -100 },
			{ lightness: 50 }
		]
	}];
	
	if(map) {
		map.setCenter(myLatlng);
		map.setZoom(2);
	
	}else {
		map = new google.maps.Map(document.getElementById("gmap"), map_options);
		
		// grey out map
		var styledMapOptions = {
			map: map,
			name: "Grey"
		}
		var greyMap =  new google.maps.StyledMapType(styles,styledMapOptions);
		map.mapTypes.set('grey', greyMap);
		map.setMapTypeId('grey');
		
		// ui controls
		var controls = document.createElement("div");
		controls.style.width = "100%";
		controls.innerHTML = '<ul id="controls" class="individual"><li><a class="slideup" href="#color">Color&nbsp;&nbsp;<span id="color_info" style="display:inline-block; background:#'+color+'; width:12px; height:12px;"></span></a></li><li><a class="slideup" href="#pencil">Size&nbsp;&nbsp;<span id="size_info1" style="display:inline-block; background:#fff; width:'+size+'px; height:'+size+'px; -moz-border-radius:'+(size/2)+'px; -webkit-border-radius:'+(size/2)+'px;"></span></a></li></ul>';
		
		map.controls[google.maps.ControlPosition.BOTTOM].push(controls);
		
		// swipe control
		var swipe_control = document.createElement("a");
		swipe_control.style.width = "100%";
		swipe_control.style.height = "85%";
		swipe_control.style.textAlign = "center";
		swipe_control.href = "#";
		swipe_control.innerHTML = '<img src="'+domain+'/images/slide_projectinfo.png" style="margin-top:100px;" border="0" id="sliding_image" width="150" height="105">';
		
		map.controls[google.maps.ControlPosition.TOP].push(swipe_control);
		
		window.setTimeout('$("#sliding_image").fadeOut("slow")', 8000);
	}
}
/* 
------------------------------------------------------------------------------------------------------
fit map div to device screen
------------------------------------------------------------------------------------------------------
*/ 
function setMapSize() {

	document.getElementById("gmap").style.width = window.innerWidth+"px";
	document.getElementById("gmap").style.height = window.innerHeight-45+"px"; //minus the toolbar
}
/* 
------------------------------------------------------------------------------------------------------
add marker to map
------------------------------------------------------------------------------------------------------
*/  
function setMarker(lat,lng,img) {
  
	if(!img) {
		var marker_icon = domain+'/images/google_maps/transparent.png';
	}
	
	var new_point = new google.maps.LatLng(lat,lng);
	
	var marker = new google.maps.Marker({
		position: new_point,
		map: map,
		flat: true,
		icon: marker_icon
	});
	
	bounds.extend(new_point);
	marker_list.push(marker);
	
	map.fitBounds(bounds);
}
/* 
------------------------------------------------------------------------------------------------------
remove marker
------------------------------------------------------------------------------------------------------
*/
function removeMarkers() {
	for(j=0; j<marker_list.length; j++){
		marker_list[j].setMap(null);
	}
	bounds = '';
	bounds = new google.maps.LatLngBounds();
}
/* 
------------------------------------------------------------------------------------------------------
add polylines
------------------------------------------------------------------------------------------------------
*/  
function setPolyline(mvcarray, weight, opacity, color) {
  
  polyline = new google.maps.Polyline({
      path: mvcarray,
      strokeColor: color,
      strokeOpacity: opacity,
      strokeWeight: weight
  });
  
  polyline_list.push(polyline);
  
  polyline.setMap(map);
}
/* 
------------------------------------------------------------------------------------------------------
remove polylines
------------------------------------------------------------------------------------------------------
*/
function removePolyline() {
	for(j=0; j<polyline_list.length; j++){
		polyline_list[j].setMap(null);
	}
}
/*
------------------------------------------------------------------------------------------------------
return city and country name
------------------------------------------------------------------------------------------------------
*/ 
function getCountryCityName(lat,lng) {

	var reverselatlng = new google.maps.LatLng(lat,lng);
	
	if (geocoder) {
		geocoder.geocode({"latLng": reverselatlng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {      
				if (results[4]) {      
					//locationname =  results[5].address_components[1]["long_name"];
					locationname =  results[4].formatted_address;
					//alert(dump(results[3].address_components));
					document.getElementById("current_location").innerHTML = locationname;
				}else {
					locationname = 'notset';
				}
			} else {
				locationname = 'status_failure';
			}
		});
	}else {
		locationname = 'null';
	}
}
